<?php

namespace DanTheCoder\SaaSCore\Subscription\Http\Middleware;

use Closure;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;

class CheckMembership
{
    /**
     * Handle an incoming request.
     * and redirect users if they do not have active membership
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // If the user has "access admin" permission do not prevent them from accessing the website
        if ( $request->user()->hasPermissionTo('access admin') )
            return $next($request);


        $userSubscription = PlanSubscription::where( 'subscribable_id', $request->user()->id )->latest()->first();

        // No Active Membership - If the user does not have an active subscription
        if ( $userSubscription === null || $request->user()->subscription('membership')->isCanceledImmediately() ) {
            if( $request->headers->get('content-type') == 'application/json' )
                return response(['location' => '/billing/plans'], 307);

            return redirect('billing/plans');
        }


        // Free Trial Ended - If the user is trialling without payment method
        if ( $request->user()->subscription('membership')->onTrial() === false && $request->user()->stripe_card === null && $request->user()->paypal_id === null ) {
            if( $request->headers->get('content-type') == 'application/json' )
                return response(['location' => '/billing/plans'], 307);

            return redirect('billing/plans');
        }

        return $next($request);
    }
}
