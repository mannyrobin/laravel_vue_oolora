<?php

namespace DanTheCoder\SaaSCore\Subscription\Listeners;

use Illuminate\Auth\Events\Registered;
use DanTheCoder\SaaSCore\Subscription\Models\Plan;

class MembershipAutoAssign
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Auto subscribe a user to a membership plan upon registration
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        // Get the plan that is set to auto assign
        $plan = Plan::active()->autoAssign()->first();

        if ( $plan )
            $event->user->newSubscription('membership', $plan)->create();
    }
}
