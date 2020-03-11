<?php

namespace DanTheCoder\SaaSCore\Subscription\Http\Controllers;

use Stripe;
use Carbon\Carbon;
use SaaSCoreHelper;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DanTheCoder\SaaSCore\Subscription\Models\Invoice;
use DanTheCoder\SaaSCore\Subscription\Models\InvoiceLine;
use DanTheCoder\SaaSCore\Subscription\Models\ProcessWebhook;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;
use DanTheCoder\SaaSCore\Subscription\Notifications\PaymentFailed;
use DanTheCoder\SaaSCore\Subscription\Notifications\PaymentSucceeded;
use DanTheCoder\SaaSCore\Subscription\Notifications\SubscriptionCanceled;

class WebhookStripeController extends Controller
{

    /**
     * Handle Stripe webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function handleWebhook(Request $request)
    {
    	$payload = json_decode($request->getContent(), true);


    	// If event is not valid return
		if ( ! $this->eventExistsOnStripe($payload['id']) )
            return;


       // Check if the webhook is a duplicate
        $webhookExist = ProcessWebhook::where(['event_id' => $payload['id'], 'gateway' => 'Stripe'])->first();
        if ($webhookExist)
            return;


        $method = 'handle'.studly_case(str_replace('.', '_', $payload['type']));
        
        if (method_exists($this, $method)) {
            return $this->{$method}($payload);
        } else {
            return $this->missingMethod();
        }
    }


    /**
     * Handle a successfully paid invoice.
     *
     * @param  array  $payload
     * @return Response
     */
    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        $user = $this->getUser( $payload['data']['object']['customer'] );
        
        if ($user) {

            // Get the customer card details
            $charge = Stripe::charges()->find( $payload['data']['object']['charge'] );


            // Get the user subscription details
            $userSubscription = PlanSubscription::where( 'subscribable_id', $user->id )->with('plan')->latest()->first();


            // Generate an invoice Number
            $lastInvoice = Invoice::latest()->first();
            
            if ( $lastInvoice )
                $invoiceId = $lastInvoice->id;
            else 
                $invoiceId = 0;

            $invoiceId++;
            $invoiceNumber = sprintf('%04d', $invoiceId);


            // Generate the paid system invoice
            $invoice = Invoice::create([
                'user_id'               => $user->id,
                'invoice_number'        => config('settings.invoice_prefix') . "-" . $invoiceNumber,
                'subtotal'              => number_format($payload['data']['object']['subtotal'] / 100, 2),
                'total'                 => number_format($payload['data']['object']['total'] / 100, 2),
                'currency'              => $payload['data']['object']['currency'],
                'subscription_id'       => $userSubscription->id,
                'payment_gateway'       => 'Stripe',
                'gateway_charge_id'     => $payload['data']['object']['charge'],
                'card_brand'            => $charge['source']['brand'],
                'card_last4'            => $charge['source']['last4'],
                'created_at'            => Carbon::createFromTimestamp( $payload['data']['object']['date'] )->toDateTimeString()
            ]);

            // Add invoice line items
            foreach ( $payload['data']['object']['lines']['data'] as $item ) {

                $invoiceLine = InvoiceLine::create([
                    'invoice_id'    => $invoice->id,
                    'description'   => $item['description'],
                    'amount'        => number_format($item['amount'] / 100, 2),
                    'period_start'  => Carbon::createFromTimestamp($item['period']['start'])->toDateTimeString(),
                    'period_end'    => Carbon::createFromTimestamp($item['period']['end'])->toDateTimeString()
                ]);

            }


            // Renew the user subscription if it's not the first payment
            if ( Invoice::where('user_id', $user->id)->count() >= 2 )
                $user->subscription('membership')->renew();


            // Send notification
            $subscriptionNotification = [
                'user'          => $user->name,
                'date'          => Carbon::createFromTimestamp( $payload['data']['object']['date'] )->timezone( $user->timezone )->format( config('settings.date_format') ),
                'amount'        => SaaSCoreHelper::guessCurrencySymbol($payload['data']['object']['currency']) .''. number_format($payload['data']['object']['total'] / 100, 2) .' '. strtoupper($payload['data']['object']['currency']),
                'action_url'    => config('app.url') . '/billing/invoice/' . $invoice->id
            ];

            $user->notify( new PaymentSucceeded($subscriptionNotification) );


            // Save the webhook to database as processed
            $this->saveProcessWebhook( $payload['id'] );

        }
        
        return response('Invoice.Payment.Succeeded Webhook Handled', 200);
    }


    /**
     * Handle a failed invoice payment
     *
     * @param  array  $payload
     * @return Response
     */
    protected function handleInvoicePaymentFailed(array $payload)
    {
        $user = $this->getUser($payload['data']['object']['customer']);
        
        if ($user) {

            $nextAttempt = null;
            if ( $payload['data']['object']['next_payment_attempt'] != null )
                $nextAttempt = Carbon::createFromTimestamp( $payload['data']['object']['next_payment_attempt'] )->timezone( $user->timezone )->format( config('settings.date_format') );

            // Notify the user
            $paymentNotification = [
                'user'          => $user->name,
                'next_attempt'  => $nextAttempt,
                'action_url'    => config('app.url') . '/billing'
            ];

            $user->notify( new PaymentFailed($paymentNotification) );


            // Save the webhook to database as processed
            $this->saveProcessWebhook( $payload['id'] );
        }
        
        return response('Invoice.Payment.Failed Webhook Handled', 200);
    }


    /**
     * Handle a canceled customer from a Stripe subscription.
     *
     * @param  array  $payload
     * @return Response
     */
    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        $user = $this->getUser($payload['data']['object']['customer']);
        
        if ($user) {

            // Cancel user subscription
            $user->subscription('membership')->cancel(true);

            // Send notification
            $user->notify( new SubscriptionCanceled() );
         

            // Save the webhook to database as processed
            $this->saveProcessWebhook( $payload['id'] );   
        }

        return response('Customer.Subscription.Deleted Webhook Handled', 200);
    }


    /**
     * Get the user by Stripe ID.
     *
     * @param  string  $stripeId
     * @return \App\Models\User
     */
    protected function getUser($stripeId)
    {
        return (new User)->where('stripe_id', $stripeId)->first();
    }


    /**
     * Verify with Stripe that the event is genuine.
     *
     * @param  string  $id
     * @return bool
     */
    protected function eventExistsOnStripe($eventId)
    {
        try {
        	return ! is_null( Stripe::events()->find( $eventId ) );
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * Save a webhook as processed
     */
    protected function saveProcessWebhook($webhookId) {
        $webhook = new ProcessWebhook;

        $webhook->event_id = $webhookId;
        $webhook->gateway = 'Stripe';
        $webhook->save();
    }


    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  array  $parameters
     * @return Response
     */
    protected function missingMethod($parameters = [])
    {
        return response('Ignored Webhook', 200);
    }

}