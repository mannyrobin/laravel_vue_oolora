<?php

namespace DanTheCoder\SaaSCore\Admin\Http\ApiControllers;

use Stripe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DanTheCoder\SaaSCore\Subscription\Models\Plan;
use DanTheCoder\SaaSCore\Subscription\Models\PlanFeature;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;

class PlanController extends Controller
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
	 * Return all plans
	 */
    public function index(Request $request)
    {
        try {   
            return Plan::with('features')->orderBy('sort_order', 'asc')->get();
        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Create a plan
     */
    public function store(Request $request)
    {
        
        // Validate the request
        $request->validate([
            'name'          => 'bail|required|string',
            'price'         => 'bail|required|regex:/^\d*(\.\d{1,2})?$/',
            'description'   => 'bail|required|max:127',
        ]);
        

        try {

            // Create the plan
            $plan = Plan::create([
                'name'                  => $request->name,
                'description'           => $request->description,
                'price'                 => $request->price,
                'interval'              => $request->interval,
                'interval_count'        => $request->interval_count,
                'trial_period_days'     => $request->trial_period,
                'sort_order'            => $request->sort_order,
            ]);


            // Create plan features
            $features = [];
            foreach (config('settings.plan_features') as $key => $feature ) {
                $features[] =  new PlanFeature([
                        'code'              => $key, 
                        'name'              => $feature['name'],
                        'value'             => $feature['value'], 
                        'value_selection'   => $feature['selection'],
                        'sort_order'        => $feature['sort_order']
                    ]);
            }

            $plan->features()->saveMany($features);


            // Create the plan on Stripe
            if ( config('services.stripe.key') && config('services.stripe.secret') ) {

                Stripe::plans()->create([
                    'id'                   => $plan->id,
                    'name'                 => $request->name,
                    'amount'               => $request->price,
                    'currency'             => config('settings.currency_code'),
                    'interval'             => $request->interval,
                    'interval_count'       => $request->interval_count,
                    'trial_period_days'    => $request->trial_period,
                    'statement_descriptor' => 'Subscription Payment',
                ]);

            }


            // Create the plan on PayPal
            if ( config('services.paypal.enable') ) {
                
                // Create a new billing plan
                $paypalPlan = new \PayPal\Api\Plan();
                $paypalPlan->setName($request->name)
                    ->setDescription($request->description)
                    ->setType('INFINITE');


                // Regular payment definitions
                $paymentDefinition = new \PayPal\Api\PaymentDefinition();
                $paymentDefinition->setName('Regular Payments')
                    ->setType('REGULAR')
                    ->setFrequency($request->interval)
                    ->setFrequencyInterval($request->interval_count)
                    ->setCycles('0')
                    ->setAmount(new \PayPal\Api\Currency(
                        array(
                            'value'     => $request->price, 
                            'currency'  => config('settings.currency_code') )
                        ));


                // Create trial definitions if the plan has trial
                if ( $request->trial_period ) {
                    
                    $trialDefinition = new \PayPal\Api\PaymentDefinition();
                    
                    $trialDefinition->setName('Trial Payments')
                        ->setType('TRIAL')
                        ->setFrequency('Day')
                        ->setFrequencyInterval('1')
                        ->setCycles($request->trial_period)
                        ->setAmount(new \PayPal\Api\Currency(
                            array(
                                'value'     => 0.00, 
                                'currency'  => config('settings.currency_code') )
                            ));

                }


                // Set merchant preferences
                $merchantPreferences = new \PayPal\Api\MerchantPreferences();
                $merchantPreferences->setMaxFailAttempts('1')
                    ->setInitialFailAmountAction('CANCEL')
                    ->setReturnUrl( config('app.url') . '/paypal/return' )
                    ->setCancelUrl( config('app.url') . '/billing/plans' );


                // None trial plans only
                if ( ! $request->trial_period ) {

                    $merchantPreferences->setSetupFee(new \PayPal\Api\Currency(
                        array(
                            'value'     => $request->price, 
                            'currency'  => config('settings.currency_code') )
                        ));

                }


                // Set payment definitions
                if ( $request->trial_period ) {
                    $paypalPlan->setPaymentDefinitions( array($paymentDefinition, $trialDefinition) );
                }
                else {
                   $paypalPlan->setPaymentDefinitions( array($paymentDefinition) ); 
                }

                $paypalPlan->setMerchantPreferences($merchantPreferences);


                // Create the plan
                $createdPlan = $paypalPlan->create($this->paypalApiContext);


                // Set the plan to active
                $patch = new \PayPal\Api\Patch();
                $value = new \PayPal\Common\PayPalModel('{"state":"ACTIVE"}');
                
                $patch->setOp('replace')
                    ->setPath('/')
                    ->setValue($value);
                
                $patchRequest = new \PayPal\Api\PatchRequest();
                $patchRequest->addPatch($patch);
                $createdPlan->update( $patchRequest, $this->paypalApiContext );
                

                // Get the created plan ID
                $paypalCreatedPlan = \PayPal\Api\Plan::get( $createdPlan->getId(), $this->paypalApiContext );

                
                // Update the DB plan to include the paypal plan ID
                $plan->paypal_plan_id = $paypalCreatedPlan->getId();
                $plan->save();

            }


            return response(['message' => 'Subscription plan was successfully created'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Update a plan
     */
    public function update($id, Request $request)
    {
        // Validate the request
        $request->validate([
            'name'          => 'bail|required|string',
            'description'   => 'bail|required|max:127',
        ]);


        try {

            $plan = Plan::find($id);

            // Update the plan in the DB
            $plan->fill([
                'name'                  => $request->name,
                'description'           => $request->description,
                'sort_order'            => $request->sort_order,
            ])->save();


            return response(['message' => 'The plan was successfully updated'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Delete or disable a plan
     * 
     */
    public function destroy($id) 
    {
        try {

            // Get plan subscribers count
            $subscribers = PlanSubscription::where(['plan_id' => $id, 'canceled_immediately' => null, 'canceled_at' => null])->count();
            $plan = Plan::find($id);

            // Only delete if the plan has no subscribers
            if ( $subscribers >= 1 ) {
                
                $plan->active = 0;
                $plan->save();

                $message = 'This plan currently have active subscribers therefore it was only disabled';

            } 
            else {

                // Delete from Stripe
                if ( config('services.stripe.key') && config('services.stripe.secret') ) {
                    Stripe::plans()->delete($id);
                }


                // Delete From PayPal
                if ( config('services.paypal.enable') ) {

                    $paypalPlan = \PayPal\Api\Plan::get($plan->paypal_plan_id, $this->paypalApiContext);
                    $paypalPlan->delete($this->paypalApiContext);
                    
                }


                // Delete DB plan
                $plan->delete();


                $message = 'The plan was successfully deleted';

            }
            

            return response(['message' => $message], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Update a specific plan features
     */
    public function updateFeatures(Request $request)
    {
        try {

            // update the plan features
            foreach ($request->all() as $feature) {
                PlanFeature::where('id', $feature['id'])->update([
                    'name'  => $feature['name'],
                    'value' => $feature['value']
                ]);
            }

            return response(['message' => 'The plan features were successfully updated'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Enable a plan that was disable
     */
    public function enablePlan($id) 
    {
        try {

            $plan = Plan::find($id);
            $plan->active = 1;
            $plan->save();

            return response(['message' => 'The plan has been enabled'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

}
