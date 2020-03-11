<?php

namespace DanTheCoder\SaaSCore\Subscription\Http\Controllers;

use Carbon\Carbon;
use SaaSCoreHelper;
use Illuminate\Http\Request;
use \PayPal\Api\WebhookEvent;
use App\Http\Controllers\Controller;
use \PayPal\Api\VerifyWebhookSignature;
use DanTheCoder\SaaSCore\Subscription\Models\Invoice;
use DanTheCoder\SaaSCore\Subscription\Models\InvoiceLine;
use DanTheCoder\SaaSCore\Subscription\Models\ProcessWebhook;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;
use DanTheCoder\SaaSCore\Subscription\Notifications\PaymentSucceeded;
use DanTheCoder\SaaSCore\Subscription\Notifications\SubscriptionCanceled;

class WebhookPaypalController extends Controller
{

    private $paypalApiContext;
    private $paypalClientId;
    private $paypalSecret;


    public function __construct()
    {
        // Detect if we are running in live mode or sandbox
        if ( config('services.paypal.settings.mode') === 'live' ) {
            $this->paypalClientId   = config('services.paypal.live_client_id');
            $this->paypalSecret     = config('services.paypal.live_secret');
        } else {
            $this->paypalClientId   = config('services.paypal.sandbox_client_id');
            $this->paypalSecret     = config('services.paypal.sandbox_secret');
        }
        
        // Set the Paypal API Context/Credentials
        $this->paypalApiContext = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->paypalClientId, $this->paypalSecret));
        $this->paypalApiContext->setConfig(config('services.paypal.settings'));
    }


    /**
     * Handle PayPal webhook payload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleWebhook(Request $request)
    {
        $payload = json_decode( $request->getContent(), true );
        $headers = getallheaders();


        // Return if the webhook is not valid
        if ( ! $this->verifyWebhook( $request->getContent(), $headers) )
            return;


        // Check if the webhook is a duplicate
        $webhookExist = ProcessWebhook::where(['event_id' => $payload['id'], 'gateway' => 'PayPal'])->first();
        if ($webhookExist)
            return;


        $method = 'handle'.studly_case(str_replace('.', '_', $payload['event_type']));
        
        if (method_exists($this, $method)) {
            return $this->{$method}($payload);
        } else {
            return $this->missingMethod();
        }
    }


    /**
     * Handle a successfully payment
     *
     * @param  array  $payload
     * @return Response
     */
    protected function handlePaymentSaleCompleted(array $payload)
    {
        // Get the user for the transaction
        $subscription = $this->getUserSubscription( $payload['resource']['billing_agreement_id'] );
        
        if ($subscription) {

            $user = $subscription->subscribable;
        
            // Generate an invoice Number
            $lastInvoice = Invoice::latest()->first();
            
            if ( $lastInvoice )
                $invoiceId = $lastInvoice->id;
            else 
                $invoiceId = 0;

            $invoiceId++;
            $invoiceNumber = sprintf('%04d', $invoiceId);


            // Get information on the payer
            $paypalAgreement = \PayPal\Api\Agreement::get( $subscription->gateway_subscription_id, $this->paypalApiContext );


            // Generate the paid system invoice
            $invoice = Invoice::create([
                'user_id'               => $user->id,
                'invoice_number'        => config('settings.invoice_prefix') . "-" . $invoiceNumber,
                'subtotal'              => $payload['resource']['amount']['total'],
                'total'                 => $payload['resource']['amount']['total'],
                'currency'              => $payload['resource']['amount']['currency'],
                'subscription_id'       => $subscription->id,
                'gateway_charge_id'     => $payload['resource']['id'],
                'payment_gateway'       => 'PayPal',
                'paypal_email'          => $paypalAgreement->payer->payer_info->email,
                'created_at'            => Carbon::createFromTimestamp( strtotime($payload['resource']['create_time']) )->toDateTimeString()
            ]);


            // Set the line item description
            $description = "1 × ". $subscription->plan->name ." (at ". SaaSCoreHelper::guessCurrencySymbol($payload['resource']['amount']['currency']) ."". $payload['resource']['amount']['total']." / ". $subscription->plan->interval .")";

            // Add invoice line items
            $invoiceLine = InvoiceLine::create([
                'invoice_id'    => $invoice->id,
                'description'   => $description,
                'amount'        => $payload['resource']['amount']['total'],
                'period_start'  => $subscription->starts_at,
                'period_end'    => $subscription->ends_at
            ]);


            // Renew the user subscription if it's not the first payment
            if ( Invoice::where('user_id', $user->id)->count() >= 2 )
                $user->subscription('membership')->renew();

            
            // Send notification
            $subscriptionNotification = [
                'user'          => $user->name,
                'date'          => Carbon::createFromTimestamp( strtotime($payload['resource']['create_time']) )->timezone( $user->timezone )->format( config('settings.date_format') ),
                'amount'        => SaaSCoreHelper::guessCurrencySymbol($payload['resource']['amount']['currency']) .''. $payload['resource']['amount']['total'] .' '. strtoupper($payload['resource']['amount']['currency']),
                'action_url'    => config('app.url') . '/billing/invoice/' . $invoice->id
            ];

            $user->notify( new PaymentSucceeded($subscriptionNotification) );

            // Save the webhook to database as processed
            $this->saveProcessWebhook( $payload['id'] );

        }

        return response('PAYMENT.SALE.COMPLETED Webhook Handled', 200);

    }


    /**
     * Handle suspended billing agreement if there was no payments
     *
     * @param  array  $payload
     * @return Response
     */
    protected function handleBillingSubscriptionSuspended(array $payload)
    {
        $subscription = $this->getUserSubscription( $payload['resource']['id'] );
        
        if ($subscription) {
            
            $user = $subscription->subscribable;
        
            // Check if the failed payment count is more than max attempt
            if ( $payload['resource']['agreement_details']['failed_payment_count'] >= $payload['resource']['plan']['merchant_preferences']['max_fail_attempts'] ) {

                // Cancel user subscription as payment failed
                $user->subscription('membership')->cancel(true);

                // Send notification
                $user->notify( new SubscriptionCanceled() );

            } else {
                // Pre-cancel user subscription
                // Cron job will handle the actually cancellation when the remaining time expired
                $user->subscription('membership')->cancel();
            }

            // Save the webhook to database as processed
            $this->saveProcessWebhook( $payload['id'] );

        }

        return response('BILLING.SUBSCRIPTION.SUSPENDED Webhook Handled', 200);
    }


    /**
     * Handle canceled billing agreement
     * @param  array  $payload
     * @return Response
     */
    protected function handleBillingSubscriptionCancelled(array $payload)
    {
        $subscription = $this->getUserSubscription( $payload['resource']['id'] );
        
        if ($subscription) {
            
            $user = $subscription->subscribable;
        
            // Check if the failed payment count is more than max attempt
            if ( $payload['resource']['agreement_details']['failed_payment_count'] >= $payload['resource']['plan']['merchant_preferences']['max_fail_attempts'] ) {
                
                // Cancel user subscription as payment failed
                $user->subscription('membership')->cancel(true);
                
                // Send notification
                $user->notify( new SubscriptionCanceled() );

            } else {

                // Pre-cancel user subscription
                // Cron job will handle the actually cancellation when the remaining time expired
                $user->subscription('membership')->cancel();

            }

            // Save the webhook to database as processed
            $this->saveProcessWebhook( $payload['id'] );
        }

        return response('BILLING.SUBSCRIPTION.CANCELLED Webhook Handled', 200);
    }


    /**
     * Get the user 
     *
     * @param  string  $transactionId
     * @return \App\Models\User
     */
    protected function getUserSubscription($transactionId)
    {
        $subscription = PlanSubscription::where( 'gateway_subscription_id', $transactionId )->with('plan')->with('subscribable')->first();

        return $subscription;
    }


    /**
     * Verify with PayPal that the event is genuine.
     */
    protected function verifyWebhook($requestBody, $headers) {
        $headers = $headers;
        $headers = array_change_key_case($headers, CASE_UPPER);

        $signatureVerification = new VerifyWebhookSignature();
        $signatureVerification->setAuthAlgo($headers['PAYPAL-AUTH-ALGO']);
        $signatureVerification->setTransmissionId($headers['PAYPAL-TRANSMISSION-ID']);
        $signatureVerification->setCertUrl($headers['PAYPAL-CERT-URL']);
        $signatureVerification->setWebhookId( config('services.paypal.webhook_id') );
        $signatureVerification->setTransmissionSig($headers['PAYPAL-TRANSMISSION-SIG']);
        $signatureVerification->setTransmissionTime($headers['PAYPAL-TRANSMISSION-TIME']);
        
        $signatureVerification->setRequestBody( $requestBody );
        $request = clone $signatureVerification;

        try {
            $response = $signatureVerification->post($this->paypalApiContext);

            if ( $response->verification_status === "SUCCESS")
                return true;
            else
                return false;

        } catch (Exception $ex) {
            return false;
        }
    }


    /**
     * Save a webhook as processed
     */
    protected function saveProcessWebhook($webhookId) {
        $webhook = new ProcessWebhook;

        $webhook->event_id = $webhookId;
        $webhook->gateway = 'PayPal';
        $webhook->save();
    }


    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function missingMethod($parameters = [])
    {
        return response('Ignored Webhook', 200);
    }

}