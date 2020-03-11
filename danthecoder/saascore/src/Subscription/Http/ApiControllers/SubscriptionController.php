<?php

namespace DanTheCoder\SaaSCore\Subscription\Http\ApiControllers;

use Stripe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;
use DanTheCoder\SaaSCore\Subscription\Notifications\ReactivateSubscription;
use DanTheCoder\SaaSCore\Subscription\Notifications\SubscriptionCancelRequest;
use DanTheCoder\SaaSCore\Subscription\Resources\Subscription as SubscriptionResource;

class SubscriptionController extends Controller
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
     * Return information about the user subscription
     */
    public function index(Request $request)
    {
        try {

            // Return null if the user doesn't have any active subscription
            if ( ! $request->user()->subscribed('membership') )
                return response([], 200);
            

            // Get any active subscription from the database
            $userSubscription = PlanSubscription::where(['subscribable_id' => $request->user()->id])->with('plan')->latest()->first();

            
            $paymentMethod = null;

            // Get Stripe billing information
            if ( $request->user()->stripe_id && $request->user()->stripe_card )
                $paymentMethod = Stripe::cards()->find( $request->user()->stripe_id, $request->user()->stripe_card );


            // Get PayPal billing agreement details
            if ( $request->user()->paypal_id ) {
                $agreement = \PayPal\Api\Agreement::get( $userSubscription->gateway_subscription_id, $this->paypalApiContext );
                $paymentMethod = $agreement->payer->payer_info->toArray();
            }


            return new SubscriptionResource( 
                array_merge( $userSubscription->toArray(), ['payment_method' => $paymentMethod] )
            );

        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
    * Suspend/Cancel a user subscription
     */
    public function cancel(Request $request)
    {
        try {

            // Cancel PayPal billing agreement
            if ( $request->gateway === 'PayPal' ) {

                // Create an Agreement State Descriptor, explaining the reason to suspend.
                $agreementStateDescriptor = new \PayPal\Api\AgreementStateDescriptor();
                $agreementStateDescriptor->setNote("User request to have their membership subscription suspended");


                // Suspend the subscription
                $agreement = \PayPal\Api\Agreement::get( $request->gateway_subscription_id, $this->paypalApiContext );
                $agreement->suspend($agreementStateDescriptor, $this->paypalApiContext);

            }


            // Cancel Stripe subscription
            if ( $request->gateway === 'Stripe' ) {
                
                // Cancel Stripe at the end of the period
                Stripe::subscriptions()->cancel( $request->user()->stripe_id, $request->gateway_subscription_id, true );

                // Remove customer card from stripe
                Stripe::cards()->delete( $request->user()->stripe_id, $request->user()->stripe_card );

                // Update card info in database
                $request->user()->update(['stripe_card' => null]);
                
            }


            // Cancel membership subscription in the database
            // Cron job will handle the actually cancellation when the remaining time expired
            $request->user()->subscription('membership')->cancel();

            
            // Send notification
            $request->user()->notify( new SubscriptionCancelRequest([
                'ends_at' => $request->ends_at
            ]) );


            return response(['message' => 'Your membership subscription was canceled.'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
    * Reactivate a suspended or canceled subscription
     */
    public function reactivate(Request $request)
    {
        try {

            // Reactivate PayPal billing agreement
            if ( $request->gateway === 'PayPal' ) {

                // Create an Agreement State Descriptor, explaining the reason to suspend.
                $agreementStateDescriptor = new \PayPal\Api\AgreementStateDescriptor();
                $agreementStateDescriptor->setNote("User request to reactivating their subscription");

                // Reactivate the agreement
                $agreement = \PayPal\Api\Agreement::get( $request->gateway_subscription_id, $this->paypalApiContext );
                $agreement->reActivate($agreementStateDescriptor, $this->paypalApiContext);
            }


            // Reactivate Stripe subscription
            if ( $request->gateway === 'Stripe' ) {

                // Prevent users from reactivating without having a card
                if ( ! $request->user()->stripe_card )
                    return response(['message' => 'Please add a credit card to your account before reactivating your subscription.'], 422);


                Stripe::subscriptions()->reactivate( $request->user()->stripe_id, $request->gateway_subscription_id );
            }


            // Re-activate membership in the database
            PlanSubscription::where('id', $request->id)->update(['canceled_at' => null]);
            

            // Send notification
            $request->user()->notify( new ReactivateSubscription() );


            return response(['message' => 'Your subscription was successfully reactivated'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
    * Return the user subscription usage
     */
    public function usage(Request $request)
    {
        try {

            $value = $request->user()->subscription('membership')->ability()->value( $request->feature );
            $consumed = $request->user()->subscription('membership')->ability()->consumed( $request->feature );
            $canUse = $request->user()->subscription('membership')->ability()->canUse( $request->feature );

            $result = [
                'value'     => $value,
                'consumed'  => $consumed,
                'canuse'    => $canUse
            ];

            return response($result, 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }
  
}