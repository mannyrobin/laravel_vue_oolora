<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;
use DanTheCoder\SaaSCore\Subscription\Notifications\SubscriptionCanceledConfirmation;

class ConfirmCancel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:confirm-cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Completely cancel all subscriptions that are passed the end date and is set to cancel';


    private $paypalApiContext;
    private $paypalClientId;
    private $paypalSecret;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();


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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // Create a PayPal Agreement State Descriptor, explaining the reason to suspend.
        $agreementStateDescriptor = new \PayPal\Api\AgreementStateDescriptor();
        $agreementStateDescriptor->setNote("User request to have their membership subscription canceled");


        do {
            // Get all canceled subscriptions
            $subscriptions = PlanSubscription::take(100)
                                ->where('canceled_immediately', null)
                                ->whereDate( 'canceled_at', '<', Carbon::now() )
                                ->whereDate( 'ends_at', '<', Carbon::now() )
                                ->with('subscribable')
                                ->get();


            // Loop through them and finalize the cancellation
            foreach ($subscriptions as $subscription) {

                // Finalize the cancellation of any suspended PayPal agreements
                if ( $subscription->gateway === 'PayPal' ) {
  
                    $agreement = \PayPal\Api\Agreement::get( $subscription->gateway_subscription_id, $this->paypalApiContext );
                    $agreement->cancel($agreementStateDescriptor, $this->paypalApiContext);

                }


                // Cancel user subscription
                $subscription->subscribable->subscription('membership')->cancel(true);

                // Send notification
                $subscription->subscribable->notify( new SubscriptionCanceledConfirmation() );
                
            }

        } while ( count($subscriptions) > 0 );
    }
}
