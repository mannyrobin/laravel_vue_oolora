<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Cookie;
use Carbon\Carbon;
use App\Models\Link;
use App\Models\Domain;
use App\Helpers\Helper;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Models\StatisticLink;
use App\Models\StatisticOverall;
use App\Models\StatisticReferrer;


require_once(dirname(__FILE__)."/Parser-PHP/bootstrap.php");


class PreviewDomainController extends Controller
{

    public function __invoke($domain, $slug)
    {

        // Get the link details
        $link = Link::active()->where('slug', $slug)->with(['user', 'pixels', 'scripts', 'cta', 'domain'])->first();


        // If the domain does not belong to the system
        if ($domain != config('settings.link_shorten_domain') && $domain != 'orl.ink' ) {


          // Make sure the link domain match what is in the db
          if ( $link->domain['name'] != $domain )
            return redirect()->away( config('app.url') );

        }

         // Redirect to homepage if the link doesn't exist
        if ( ! $link )
            return redirect()->away( config('app.url') );


        // Check the feature usage ability
        // Only check for non admin users
        if ( ! $link->user->hasPermissionTo('access admin') ) {

            $canUse = $link->user->subscription('membership')->ability()->canUse('clicks');
            if ( ! $canUse )
                return redirect()->away( config('app.url') );

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




        /*
        VIEWS
        */

        $ip = getIP();
        if(strpos($ip, ",") !== false) { $ip = explode(",", $ip)[0]; }
        $agent = parseUA();

        $view = [
          "link" => $link->id,
          "user" => $link->user_id,
          "country" => getCountry($ip),
          "ip" => $ip,
          "browser" => $agent->browser->name,
          "os" => $agent->os->name,
          "type" => $agent->device->type,
          "user_agent" => $_SERVER["HTTP_USER_AGENT"],
          "created" => time(),
          "date_day" => date("j F Y"),
          "date_month" => date("F Y")
        ];
        if(array_key_exists("HTTP_REFERER", $_SERVER)) {
          $view["referrer"] = $_SERVER["HTTP_REFERER"];
          $view["referrer_domain"] = parse_url($_SERVER["HTTP_REFERER"])["host"];
        }

        DB::table("views")->insert($view);


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








/*
Retrieves the client's IP address.
*/
function getIP() {
  if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])) { return $_SERVER["HTTP_CF_CONNECTING_IP"]; }
  else if(isset($_SERVER["HTTP_CLIENT_IP"])) { return $_SERVER["HTTP_CLIENT_IP"]; }
  else if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) { return $_SERVER["HTTP_X_FORWARDED_FOR"]; }
  else if(isset($_SERVER["HTTP_X_FORWARDED"])) { return $_SERVER["HTTP_X_FORWARDED"]; }
  else if(isset($_SERVER["HTTP_FORWARDED_FOR"])) { return $_SERVER["HTTP_FORWARDED_FOR"]; }
  else if(isset($_SERVER["HTTP_FORWARDED"])) { return $_SERVER["HTTP_FORWARDED"]; }
  else if(isset($_SERVER["REMOTE_ADDR"])) { return $_SERVER["REMOTE_ADDR"]; }
}



/*
Gets the country of the visitor.
*/
function getCountry($ip = false) {
  if(!$ip) { $ip = getIP(); }

  if(isset($_SERVER["HTTP_CF_IPCOUNTRY"])) { return $_SERVER["HTTP_CF_IPCOUNTRY"]; }
  else if(filter_var($ip, FILTER_VALIDATE_IP)) {
    //Use a GEO IP service.
    try {
      $geo = json_decode(file_get_contents("http://ip-api.com/json/$ip"));
      return $geo->countryCode;
    } catch (\Exception $e) {}
  }
}
