<?php

namespace DanTheCoder\SaaSCore\Admin\Http\ApiControllers;

use Cache;
use Storage;
use Stripe;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use DanTheCoder\SaaSCore\Admin\Mail\TestMail;
use DanTheCoder\SaaSCore\Admin\Models\Setting;
use DanTheCoder\SaaSCore\Subscription\Models\Plan;

class SettingController extends Controller
{

    /**
     * Return the settings data from the database
     */
    public function index(Request $request)
    {
        try {
            $settings = array_pluck(Setting::all()->toArray(), 'value', 'key');
  
            return response($settings, 200);
        } 
        catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Update settings data
     */
    public function store(Request $request)
    {

        $paypalMood = ($request['services.paypal.settings.mode'] ? 'sandbox' : 'live');

        // Return if the user has PayPal enabled without the appropriate credentials
        if ( $request['services.paypal.enable'] ) {

            if (  $paypalMood === 'live') {

                if( empty( $request['services.paypal.live_client_id'] ) || empty( $request['services.paypal.live_secret'] ) )
                    return response(['message' => 'You have PayPal gateway in live mood and the appropriate API credentials are not set'], 422);

            } else {

                if( empty( $request['services.paypal.sandbox_client_id'] ) || empty( $request['services.paypal.sandbox_secret'] ) )
                    return response(['message' => 'You have PayPal gateway in sandbox mood and the appropriate API credentials are not set'], 422);

            }

        }


        // Return if Stripe is enable without the appropriate credentials
        if ( $request['services.stripe.enable'] ) {
            
            if( empty( $request['services.stripe.key'] ) || empty( $request['services.stripe.secret'] ) )
                return response(['message' => 'You have Stripe gateway enabled and the appropriate API credentials are not set'], 422);
        
        }


        try {

            // Store payment gateway current config to a var
            $oldStripeKey = config('services.stripe.key');
            $oldStripeSecret = config('services.stripe.secret');

            $oldPaypal = [
                'sandbox_client_id' => config('services.paypal.sandbox_client_id'),
                'sandbox_secret'    => config('services.paypal.sandbox_secret'),
                'live_client_id'    => config('services.paypal.live_client_id'),
                'live_secret'       => config('services.paypal.live_secret')
            ];


            // Clean dynamic domain
            $cleanDomain = trim($request['settings.link_shorten_domain'], '/');

            // If scheme not included, prepend it
            if (!preg_match('#^http(s)?://#', $cleanDomain)) {
                $cleanDomain = 'http://' . $cleanDomain;
            }

            $urlParts = parse_url($cleanDomain);

            // remove www
            $request['settings.link_shorten_domain'] = preg_replace('/^www\./', '', $urlParts['host']);




            // Save settings
            foreach ($request->all() as $key => $value ) {

                // Set PayPal mood to string from boolean
                if ( $key === 'services.paypal.settings.mode' )
                    $value = ($value ? 'sandbox' : 'live');


                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }


            // Flush settings cache
            Cache::flush('settings');


            // Get updated settings data and save them to the cache
            $settings = Cache::rememberForever('settings', function() {
                return array_pluck(Setting::all()->toArray(), 'value', 'key');
            });
            config($settings);



            // If Stripe credentials entered are new
            // Sync all plans to the new Stripe account
            if ( $request['services.stripe.key'] != $oldStripeKey || $request['services.stripe.secret'] != $oldStripeSecret )
                $this->syncPlansToStripe();


            

            // If we are in PayPal sandbox mood
            // and the credentials are new sync plans to new account
            if ( $paypalMood === 'sandbox' ) {
                
                if ( $request['services.paypal.sandbox_client_id'] != $oldPaypal['sandbox_client_id'] || $request['services.paypal.sandbox_secret'] != $oldPaypal['sandbox_secret'] )
                    $this->syncPlansToPayPal();

            }


            // If we are in PayPal live mood
            // and the credentials are new sync plans to new account
            if ( $paypalMood === 'live' ) {

                if ( $request['services.paypal.live_client_id'] != $oldPaypal['live_client_id'] || $request['services.paypal.live_secret'] != $oldPaypal['live_secret'] )
                    $this->syncPlansToPayPal();

            }


            return response(['message' => 'All changes were successfully updated'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Upload branding images
     */
    public function uploadImage(Request $request)
    {

        // Validation the file upload
        $request->validate([
            'file' => 'bail|required|file|image|mimes:jpeg,png|max:2048'
        ]);


        // Check if file exist
        if( ! $request->hasFile('file') )
            return response(['message' => 'No file was selected'], 422);


        // Is the file valid
        if( ! $request->file('file')->isValid() )
            return response(['message' => 'The file is not valid'], 422);


        try {

            $file = $request->file('file');


            // Delete the old file
            $oldFile = Str::replaceFirst( config('app.url').'/storage', '', config('settings.'.$request->settings_key) );
            Storage::disk('public')->delete($oldFile);


            // Save the file to the storage
            $filePath = $request->file('file')->store('assets');
            // $filePath = $request->file('file')->storeAs('assets', $request->settings_key.'.'.$file->guessExtension());


            // Save the file to the settings table
            Setting::updateOrCreate(
                ['key'      => 'settings.'.$request->settings_key],
                ['value'    => Storage::url($filePath)]
            );


            // Flush settings cache
            Cache::flush('settings');


            // Get updated settings data and save them to the cache
            $settings = Cache::rememberForever('settings', function() {
                return array_pluck(Setting::all()->toArray(), 'value', 'key');
            });
            config($settings);


            return response(['message' => 'The file was successfully uploaded'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Send a test email 
     * To check if the mail settings have been configured properly
     */
    public function sendTestMail(Request $request)
    {
        try {

            Mail::to( $request->user() )->send( new TestMail() );
            
            return response(['message' => 'The test email was sent successfully'], 200);

        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Use to set a plan to auto registration when the user register
     * without requested payment method
     */
    public function planOnboarding(Request $request, $id) 
    {
        try {
            // Reset any plans with auto assign to null
            Plan::where('auto_assign', 1)->update(['auto_assign' => null]);

            $plan = Plan::find($id);
            $plan->auto_assign = ($request->enable ? 1 : null);
            $plan->save();

            return response(['message' => 'Registration onbooarding was successfully updated'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Sync all created plans to Stripe
     */
    protected function syncPlansToStripe() {

        // Do nothing if the Stripe API keys are empty
        if ( empty(config('services.stripe.key')) || empty(config('services.stripe.secret')) )
            return;


        try {

            $plans = Plan::get();

            foreach ($plans as $plan) {
                Stripe::plans()->create([
                    'id'                   => $plan->id,
                    'name'                 => $plan->name,
                    'amount'               => $plan->price,
                    'currency'             => config('settings.currency_code'),
                    'interval'             => $plan->interval,
                    'interval_count'       => $plan->interval_count,
                    'trial_period_days'    => $plan->trial_period,
                    'statement_descriptor' => 'Subscription Payment',
                ]);
            }

            return true;

        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Sync all created plans to PayPal
     */
    protected function syncPlansToPayPal() {

        // Set up PayPal info here so it will use the updated config info that was just save
        if ( config('services.paypal.settings.mode') === 'live' ) {
            $paypalClientId   = config('services.paypal.live_client_id');
            $paypalSecret     = config('services.paypal.live_secret');
        } else {
            $paypalClientId   = config('services.paypal.sandbox_client_id');
            $paypalSecret     = config('services.paypal.sandbox_secret');
        }
        
        // Set the Paypal API Context/Credentials
        $paypalApiContext = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($paypalClientId, $paypalSecret));
        $paypalApiContext->setConfig(config('services.paypal.settings'));


        try {

            $plans = Plan::get();

            foreach ($plans as $plan) {

                // Create a new billing plan
                $paypalPlan = new \PayPal\Api\Plan();
                $paypalPlan->setName($plan->name)
                    ->setDescription($plan->description)
                    ->setType('INFINITE');


                // Regular payment definitions
                $paymentDefinition = new \PayPal\Api\PaymentDefinition();
                $paymentDefinition->setName('Regular Payments')
                    ->setType('REGULAR')
                    ->setFrequency($plan->interval)
                    ->setFrequencyInterval($plan->interval_count)
                    ->setCycles('0')
                    ->setAmount(new \PayPal\Api\Currency(
                        array(
                            'value'     => $plan->price, 
                            'currency'  => config('settings.currency_code') )
                        ));


                // Create trial definitions if the plan has trial
                if ( $plan->trial_period ) {
                    
                    $trialDefinition = new \PayPal\Api\PaymentDefinition();
                    
                    $trialDefinition->setName('Trial Payments')
                        ->setType('TRIAL')
                        ->setFrequency('Day')
                        ->setFrequencyInterval('1')
                        ->setCycles($plan->trial_period)
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
                $merchantPreferences->setSetupFee(new \PayPal\Api\Currency(
                    array(
                        'value'     => $plan->price, 
                        'currency'  => config('settings.currency_code') )
                    ));


                // Set payment definitions
                if ( $plan->trial_period )
                    $paypalPlan->setPaymentDefinitions( array($paymentDefinition, $trialDefinition) );
                else
                   $paypalPlan->setPaymentDefinitions( array($paymentDefinition) ); 

                $paypalPlan->setMerchantPreferences($merchantPreferences);


                // Create the plan
                $createdPlan = $paypalPlan->create($paypalApiContext);


                // Set the plan to active
                $patch = new \PayPal\Api\Patch();
                $value = new \PayPal\Common\PayPalModel('{"state":"ACTIVE"}');
                
                $patch->setOp('replace')
                    ->setPath('/')
                    ->setValue($value);
                
                $patchRequest = new \PayPal\Api\PatchRequest();
                $patchRequest->addPatch($patch);
                $createdPlan->update( $patchRequest, $paypalApiContext );
                

                // Get the created plan ID
                $paypalCreatedPlan = \PayPal\Api\Plan::get( $createdPlan->getId(), $paypalApiContext );

                
                // Update the DB plan to include the PayPal plan ID
                $plan->paypal_plan_id = $paypalCreatedPlan->getId();
                $plan->save();

            }

            return true;

        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

}