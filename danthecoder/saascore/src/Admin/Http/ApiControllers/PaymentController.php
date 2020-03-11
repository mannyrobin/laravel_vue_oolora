<?php

namespace DanTheCoder\SaaSCore\Admin\Http\ApiControllers;

use Stripe;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DanTheCoder\SaaSCore\Admin\Models\Refund;
use DanTheCoder\SaaSCore\Subscription\Models\Invoice;
use DanTheCoder\SaaSCore\Admin\Notifications\PaymentRefunded;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;
use DanTheCoder\SaaSCore\Subscription\Resources\Invoice as InvoiceResource;

class PaymentController extends Controller
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
	 * Return all payments
	 */
    public function index(Request $request)
    {
		try {

            // Set order by
            $orderBy = ['created_at', 'desc'];

            if ( $request->order_by )
                $orderBy = explode('|', $request->order_by);


            // Query for invoices based on the filter type
            switch ($request->filter_type) {
                case 'refunds':
                    $invoices = Invoice::orderBy($orderBy[0], $orderBy[1])->with(['user:id,name,email,avatar', 'refund.user:id,name,email,avatar'])->whereHas('refund')->paginate($request->per_page);
                    break;

                case 'payments':
                    $invoices = Invoice::orderBy($orderBy[0], $orderBy[1])->with('user:id,name,email,avatar')->doesntHave('refund')->paginate($request->per_page);
                    break;

                default:
                    $invoices = Invoice::orderBy($orderBy[0], $orderBy[1])->with(['user:id,name,email,avatar', 'refund.user:id,name,email,avatar'])->paginate($request->per_page);
                    break;
            }

	    	return InvoiceResource::collection( $invoices );
        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Return a specific payment by ID
     * @param  Invoice ID  $id
     */
    public function show($id)
    {
        try {
	    	$invoice = new InvoiceResource( Invoice::where('id', $id)->with('user')->with('lines')->firstOrFail() );
            
            return response($invoice, 200);
        
        } 
        catch (Exception $e) {
            return response(['message' => $e->getMessage()], 404);
        }
    }


    /**
     * Refund a payment
     */
    public function refundPayment(Request $request) 
    {
 
        // Validation the request
        $request->validate([
            'amount' => 'bail|required|regex:/^\d*(\.\d{1,2})?$/|lte:'.$request->invoice['total'].'',
        ]);


        try {
            
            $subscription = PlanSubscription::find($request->invoice['subscription_id']);
            $user = User::find($request->invoice['user']['id']);


            // Refund Stripe payment
            if ( $request->invoice['payment_gateway'] === 'Stripe' ) {
                
                $stripeRefund = Stripe::refunds()->create($request->invoice['gateway_charge_id'], $request->amount);


                // Refund fail
                if ( $stripeRefund['status'] != "succeeded" )
                    return response(['message' => 'Unable to process this refund at the moment'], 422);


                // Cancel subscription if stated
                if ( $request->cancel_subscription === true ) {
                    
                    // Cancel stripe subscription immediately
                    Stripe::subscriptions()->cancel( $user->stripe_id, $subscription->gateway_subscription_id );

                    // Remove customer card from stripe
                    Stripe::cards()->delete( $user->stripe_id, $user->stripe_card );

                    // Update Stripe info in the database
                    $user->update(['stripe_id' => null, 'stripe_card' => null]);

                    // Cancel application membership
                    $user->subscription('membership')->cancel(true);
                }

            }


            // Refund PayPal payment
            if ( $request->invoice['payment_gateway'] === 'PayPal' ) {

                // Set the refund amount and currency
                $refundAmount = new \PayPal\Api\Amount();
                $refundAmount->setCurrency($request->invoice['currency'])->setTotal($request->amount);


                // Create the refund request
                $refundRequest = new \PayPal\Api\RefundRequest();
                $refundRequest->setAmount($refundAmount);


                // Create a Sale object with the given sale transaction id.
                $paymentMade = new \PayPal\Api\Sale();
                $paymentMade->setId( $request->invoice['gateway_charge_id'] );


                // Do the refund
                $paypalRefund = $paymentMade->refundSale($refundRequest, $this->paypalApiContext);

           
                // Refund fail
                if ( $paypalRefund->state != "completed" )
                    return response(['message' => 'Unable to process this refund at the moment'], 422);


                // Cancel subscription if stated
                if ( $request->cancel_subscription === true ) {

                    // Cancel PayPal billing agreement
                    $agreementStateDescriptor = new \PayPal\Api\AgreementStateDescriptor();
                    $agreementStateDescriptor->setNote("Subscription canceled due to refund");

                    $agreement = \PayPal\Api\Agreement::get( $subscription->gateway_subscription_id, $this->paypalApiContext );
                    $agreement->cancel($agreementStateDescriptor, $this->paypalApiContext);


                    // Update PayPal info in the database
                    $user->update(['paypal_id' => null]);

                    // Cancel application membership
                    $user->subscription('membership')->cancel(true);
                }

            }


            // Send notification
            $user->notify( new PaymentRefunded([
                'refund_amount'         => $request->amount,
                'currency_symbol'       => $request->invoice['currency_symbol'],
                'cancel_subscription'   => $request->cancel_subscription
            ]) );
            

            // Add refund details to the database
            $refund = new Refund();

            $refund->invoice_id     = $request->invoice['id'];
            $refund->user_id        = $request->user()->id;
            $refund->amount         = $request->amount;
            $refund->reason         = $request->reason;
            $refund->save();

            return response(['message' => "A refund was successfully given to the user"], 200);
        
        } 
        catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
     
    }

}
