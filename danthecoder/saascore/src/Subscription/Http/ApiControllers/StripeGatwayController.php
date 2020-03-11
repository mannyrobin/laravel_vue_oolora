<?php

namespace DanTheCoder\SaaSCore\Subscription\Http\ApiControllers;

use Stripe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DanTheCoder\SaaSCore\Subscription\Models\Plan;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;

class StripeGatwayController extends Controller
{

    /**
     * Subscribe a user to a plan
    */
    public function subscribe(Request $request)
    {

        // Check for the token and plan ID
        if ( ! $request->additional_data['plan_id'] || ! $request->token )
            return response(['message' => 'Unable to process your payment at this time. Please try again or contact support.'], 422);


        try {

            // If the Stripe customer wasn't previously created, then create one
            if ( $request->user()->stripe_id === null ) {

                $customer = Stripe::customers()->create([
                    'email'         => $request->user()->email,
                    'description'   => 'Customer was created from ' . config('app.name') . ' (' . config('app.url') . ')'
                ]);

            }

            // Set the customer ID
            $customerId = ($request->user()->stripe_id === null) ? $customer['id'] : $request->user()->stripe_id;


            // Remove any default card that was added before
            if ( $request->user()->stripe_card )
                Stripe::cards()->delete( $customerId, $request->user()->stripe_card );


            // STRIPE - Add the new card to the customer
            $card = Stripe::cards()->create( $customerId, $request->token );


            // Update the customer info in the DB
            $request->user()
                ->update([
                    'stripe_id'     => $customerId,
                    'stripe_card'   => $card['id']
                ]);



            // If it's the user first subscription use the plan default trial date
            // Else override free trial for the user
            $plan = Plan::find( $request->additional_data['plan_id'] );
            $userSubscription = PlanSubscription::where( 'subscribable_id', $request->user()->id )->latest()->first();
            

            if ( $userSubscription === null ) {

                // STRIPE - Subscribe the customer to the plan
                $subscribe = Stripe::subscriptions()->create($customerId, [
                    'plan' => $plan->id
                ]);

                // Start the user membership
                $request->user()->newSubscription('membership', $plan)->create([
                    'gateway_subscription_id'   => $subscribe['id'],
                    'gateway'                   => 'Stripe'
                ]);

            } else {

                // STRIPE - Subscribe the customer to the plan
                $subscribe = Stripe::subscriptions()->create($customerId, [
                    'plan'      => $plan->id,
                    'trial_end' => 'now'
                ]);


                // Start the user membership
                $request->user()->newSubscription('membership', $plan)->create([
                    'trial_ends_at'             => null,
                    'gateway_subscription_id'   => $subscribe['id'],
                    'gateway'                   => 'Stripe'
                ]);

            }

            return response(['message' => 'Your payment was successfully processed.'], 201);
        
        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Upgrade or downgrade a user plan
     */
    public function updateSubscription(Request $request)
    {
        // Ensure that the plan does not match the user current plan
        $userSubscription = PlanSubscription::where( 'subscribable_id', $request->user()->id )->with('plan')->latest()->first();
        if ($userSubscription->plan['id'] == $request->plan_id )
            return response(['message' => 'The plan that you are changing to, is your current plan.'], 422);
    

        try {
            
            // Change Stripe plan and don't initiate any free trial
            Stripe::subscriptions()->update( $request->user()->stripe_id, $request->gateway_subscription_id, [
                'plan'      => $request->plan_id,
                'trial_end' => 'now'
            ]);

            
            // Update the user subscription in the Database
            $request->user()->subscription('membership')->changePlan( $request->plan_id )->save();


            return response(['message' => 'You have successfully changed your plan'], 200);
        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Update the user current credit card
     */
    public function updateCard(Request $request)
    {
        try {

            // Add the new card
            $addCard = Stripe::cards()->create( $request->user()->stripe_id, $request->token );

            // Remove old card
            if ( $request->user()->stripe_card )
                Stripe::cards()->delete( $request->user()->stripe_id, $request->user()->stripe_card );

            // Save new card to the DB
            $request->user()->update([ 'stripe_card' => $addCard['id'] ]);

            return response(['message' => 'Your credit card was successfully updated.'], 200);
        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

}