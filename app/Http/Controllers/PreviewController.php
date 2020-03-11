<?php

namespace App\Http\Controllers;

use Cookie;
use Carbon\Carbon;
use App\Models\Link;
use App\Helpers\Helper;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Models\StatisticLink;
use App\Models\StatisticOverall;
use App\Models\StatisticReferrer;

class PreviewController extends Controller
{

    public function __invoke($slug)
    {

        // Get the link details
        $link = Link::active()->where('slug', $slug)->with(['user', 'pixels', 'scripts', 'cta'])->first();


        // Redirect to homepage if the link doesn't exist
        if ( ! $link  )
            return redirect('/');


        // Check the feature usage ability
        // Only check for non admin users
        if ( ! $link->user->hasPermissionTo('access admin') ) {
        
            $canUse = $link->user->subscription('membership')->ability()->canUse('clicks');
            if ( ! $canUse )
                return redirect('/');

        }

        
         // Record feature usage for non admin users
        if ( ! $link->user->hasPermissionTo('access admin') )
                $link->user->subscriptionUsage('membership')->record('clicks');


        // Set cookie to record unique clicks
        Cookie::queue('link_view_'.$link->id, true, 43800); // expire in a month
        $cookieSet = Cookie::get('link_view_'.$link->id);


        // Record Link daily Statistics
        $linkStats = StatisticLink::firstOrNew(['created_at' => Carbon::today()->toDateTimeString(), 'link_id' => $link->id]);
        $linkStats->user_id = $link->user_id;
        $linkStats->link_id = $link->id;
        $linkStats->clicks++;

        if ( ! $cookieSet )
            $linkStats->unique_clicks++;

        $linkStats->created_at = Carbon::today()->toDateTimeString();
        $linkStats->save();


        // Record Link total daily Statistics
        $linkOverallStats = StatisticOverall::firstOrNew(['created_at' => Carbon::today()->toDateTimeString(), 'user_id' => $link->user_id]);
        $linkOverallStats->user_id = $link->user_id;
        $linkOverallStats->clicks++;

        if ( ! $cookieSet )
            $linkOverallStats->unique_clicks++;

        $linkOverallStats->created_at = Carbon::today()->toDateTimeString();
        $linkOverallStats->save();


        // Update link total stats
        if ( ! $cookieSet )
            $linkStats->total_unique_clicks++;
        
        $link->total_clicks++;
        $link->save();


        // Record link daily referrers and count
        $referrer = ( request()->headers->get('referer') ? request()->headers->get('referer') : 'Direct');
        
        $referrerStats = StatisticReferrer::firstOrNew(['referrer_url' => $referrer, 'user_id' => $link->user_id, 'link_id' => $link->id]);
        $referrerStats->user_id = $link->user_id;
        $referrerStats->link_id = $link->id;
        $referrerStats->referrer_url = $referrer;
        $referrerStats->count++;
        $referrerStats->save();


        // Create the pixel script
        $pixelScript = []; 
        foreach ($link->pixels as $pixel) {
            if ( ! $pixel->disabled ) {
                $pixelScript[] = Helper::pixelSourceCode( $pixel['platform'], $pixel['code'] );
            }
        }


        return view('preview-link', compact('link', 'pixelScript') );
    }

}
