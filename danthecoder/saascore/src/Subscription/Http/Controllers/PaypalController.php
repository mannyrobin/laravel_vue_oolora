<?php

namespace DanTheCoder\SaaSCore\Subscription\Http\Controllers;

use PayPal\Api\Payer;
use PayPal\Api\Agreement;
use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use PayPal\Api\Plan as PaypalPlan;
use App\Http\Controllers\Controller;
use PayPal\Auth\OAuthTokenCredential;
use DanTheCoder\SaaSCore\Subscription\Period;
use DanTheCoder\SaaSCore\Subscription\Models\Plan;

class PaypalController extends Controller
{
    private $paypalApiContext;
    private $paypalClientId;
    private $paypalSecret;


    public function __construct()
    {
        // Detect if we are running in live mode or sandbox
        if ( config('services.paypal.settings.mode') === 'live' ) {
            $this->paypalClientId   = config('services.paypal.live_client_id');
            $this->paypalSecret 	= config('services.paypal.live_secret');
        } else {
            $this->paypalClientId   = config('services.paypal.sandbox_client_id');
            $this->paypalSecret 	= config('services.paypal.sandbox_secret');
        }
        
        // Set the Paypal API Context/Credentials
        $this->paypalApiContext = new ApiContext( new OAuthTokenCredential($this->paypalClientId, $this->paypalSecret) );
        $this->paypalApiContext->setConfig( config('services.paypal.settings') );
    }   


    /**
     * Process a PayPal billing agreement request
     * then redirect to PayPal
     */
	public function subscribe(Request $request) 
	{
		$plan = Plan::findOrFail($request->plan);


        // Create new agreement
        $agreement = new Agreement();
        $agreement->setName('Billing Agreement')
			->setDescription( config('app.name') . ' Subscription Payment');
		

        // If the plan has trial period start the billing agreement now
        // else start it on the next bill date
        if ( $plan->trial_period_days ) {
            $agreement->setStartDate(\Carbon\Carbon::now()->addMinutes(5)->toIso8601String());
        } else {
            $period = new Period( $plan->interval, $plan->interval_count, '' );
            $agreement->setStartDate( $period->getEndDate()->toIso8601String() );
        }
        

        // Set the paypal plan id
        $paypalPlan = new PaypalPlan();
        $paypalPlan->setId($plan->paypal_plan_id);
        $agreement->setPlan($paypalPlan);


        // Add payer type
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $agreement->setPayer($payer);


        try {
			// Create the agreement
			$agreement = $agreement->create($this->paypalApiContext);

			// Extract approval URL to redirect user
			$approvalUrl = $agreement->getApprovalLink();

			// Put the plan details in the user session
			$request->session()->put('plan', $plan);

          	return redirect($approvalUrl);
        } 
        catch (PayPal\Exception\PayPalConnectionException $e) {
        	return redirect('/billing/plans')->with(['type' => 'danger', 'message' => $e->getMessage()]);
		}
    }


    /**
     * Handle the return request from PayPal
     */
    public function return(Request $request){

        $token 		= $request->token;
        $agreement 	= new Agreement();
        $plan       = $request->session()->get('plan');

        try {
            // Execute agreement
            $result = $agreement->execute( $token, $this->paypalApiContext );


            // If the billing agreement set up failed
            if ( $result->state === 'Cancelled' )
                return redirect('/billing/plans')->with(['type' => 'danger', 'message' => "We couldn't process your subscription. Please try again or contact support."]);


			// Record user PayPal ID
			$request->user()->fill([
	            'paypal_id'  => $result->payer->payer_info->payer_id
	        ])->save();

		        
	        // Subscribe the user to the membership plan
	        $request->user()->newSubscription('membership', $plan)->create([
                'gateway_subscription_id'	=> $result->id,
                'gateway'                   => 'PayPal'
            ]);


	        // Remove plan data from session
	        $request->session()->forget('plan');


            return redirect('/dashboard');

        } catch (\PayPal\Exception\PayPalConnectionException $e) {
        	return redirect('/billing/plans')->with(['type' => 'danger', 'message' => $e->getMessage()]);
        }
    }
}