<?php

namespace DanTheCoder\SaaSCore\Admin\Http\Middleware;

use Closure;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     * and redirect users if they are not a staff
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if ( ! $request->user()->hasPermissionTo('access admin') ) {

            if( $request->headers->get('content-type') == 'application/json' )
                return response(['location' => '/dashboard'], 307);

            return redirect('dashboard');

        }

        return $next($request);
    }
}
