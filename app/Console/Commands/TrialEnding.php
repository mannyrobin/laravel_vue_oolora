<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;
use DanTheCoder\SaaSCore\Subscription\Notifications\TrialEnding as TrialEndingNotification;

class TrialEnding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:trial-ending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify all users that are on free trial which is ending in 1 day time';

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
        // Chunk all subscription that has expiring trial in 1 day
        PlanSubscription::where('canceled_immediately', null)->whereDate( 'trial_ends_at', Carbon::now()->addDays(1)->toDateString() )->with('subscribable')->chunk(100, function ($subscriptions) {
            foreach ($subscriptions as $subscription) {
                
                // Send notification
                $trialExpiration = $subscription->trial_ends_at->timezone( $subscription->subscribable->timezone )->format( config('settings.date_format') );
                
                $subscription->subscribable->notify( new TrialEndingNotification(['trial_expiration' => $trialExpiration]) );

            }
        });
    }
}