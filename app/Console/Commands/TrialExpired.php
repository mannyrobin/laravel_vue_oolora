<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;
use DanTheCoder\SaaSCore\Subscription\Notifications\TrialExpired as TrialExpiredNotification;

class TrialExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:trial-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel any expired subscriptions that doesn`t have any payment method on file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        do {
            // Get the expired subscriptions
            $subscriptions = PlanSubscription::take(100)
                                ->where('canceled_immediately', null)
                                ->whereDate( 'trial_ends_at', '<', Carbon::now() )
                                ->with('subscribable')
                                ->get();


            // Loop through them and cancel
            foreach ($subscriptions as $subscription) {


                // check to ensure the user exist
                if ( $subscription->subscribable['id'] ) {

                    // Cancel user subscription
                    $subscription->subscribable->subscription('membership')->cancel(true);

                    // Send notification
                    $subscription->subscribable->notify( new TrialExpiredNotification() );

                }
                
            }

        } while ( count($subscriptions) > 0 );

    }
}
