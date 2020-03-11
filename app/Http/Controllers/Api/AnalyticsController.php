<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use App\Models\Link;
use App\Models\Pixel;
use App\Helpers\Helper;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Models\CallToAction;
use App\Models\StatisticLink;
use App\Models\StatisticOverall;
use App\Models\StatisticReferrer;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{

    /**
     * Return overall statistic on the link
     */
    public function linkTotal(Request $request)
    {
        try {

            $link = Link::where(['id' => $request->link_id, 'user_id' => $request->user()->id])->pluck('title')->first();

            $statistic = StatisticLink::WhereUser()->where('link_id', $request->link_id);

            $clicks =  $statistic->sum('clicks');
            $conversion = $statistic->sum('conversion');

            $result = array_merge(
                ['link_title'       => $link],
                ['clicks'           => $clicks],
                ['unique_clicks'    => $statistic->sum('unique_clicks')],
                ['conversion'       => $conversion],
                ['conversion_rate'  => Helper::conversionRate($clicks, $conversion)]
            );

            return response($result, 200);

        } catch (Exception $e) {
            return response(['message' => 'Unable to locate this link'], 404);
        }
    }

    /**
     * Return daily stats on a link over a period
     */
    public function linkDaily(Request $request)
    {
        try {

            // Query the DB
            $statistic = StatisticLink::WhereUser()
                ->where('link_id', $request->link_id)
                ->where('created_at', '>', Carbon::today()->subDays($request->period)->toDateTimeString() )
                ->get();


            // Convert all the stats to an array
            $clicks = [];
            $uniqueClicks = [];
            $conversion = [];
            $dates = [];
            foreach ($statistic as $item ) {
                $clicks[] = $item->clicks;
                $uniqueClicks[] = $item->unique_clicks;
                $conversion[] = $item->conversion;
                $dates[] = $item->created_at;
            }


            $result = array_merge(['dates' => $dates], ['clicks' => $clicks], ['unique_clicks' => $uniqueClicks], ['conversion' => $conversion]);

            return response($result, 200);

        } catch (Exception $e) {
            return response(['message' => 'Unable to locate this link'], 404);
        }
    }


    /*
     * Return the total stats for all the link belonging to a given user
     */
    public function overallStatistic(Request $request)
    {
        try {

            // Data for the overall chart
            $dailyStatistic = StatisticOverall::WhereUser()
                ->where('created_at', '>', Carbon::today()->subDays(7)->toDateTimeString() )
                ->get();


            // Convert all the stats to an array
            $clicks = [];
            $uniqueClicks = [];
            $conversion = [];
            $dates = [];
            foreach ($dailyStatistic as $item ) {
                $clicks[] = $item->clicks;
                $uniqueClicks[] = $item->unique_clicks;
                $conversion[] = $item->conversion;
                $dates[] = $item->created_at;
            }


            // Get the total stats count for the user links
            $totalStatistic = Link::select('total_clicks', 'total_unique_clicks', 'total_conversion')->WhereUser();


            $totalConversion = $totalStatistic->sum('total_conversion');
            $totalClicks = $totalStatistic->sum('total_clicks');

            // Merge the result in one
            $dailyOverall = array_merge(['labels' => $dates], ['clicks' => $clicks], ['unique_clicks' => $uniqueClicks], ['conversion' => $conversion]);
            $totalOverall = array_merge(['clicks' => $totalClicks], ['unique_clicks' => $totalStatistic->sum('total_unique_clicks')], ['conversion' => $totalConversion], ['conversion_rate' => Helper::conversionRate($totalClicks, $totalConversion)]);
            $overview = array_merge(['links' => Link::WhereUser()->count()], ['pixels' => Pixel::WhereUser()->count()], ['campaigns' => Campaign::WhereUser()->count()], ['cta' => CallToAction::WhereUser()->count()] );

            return response(['daily' => $dailyOverall, 'total' => $totalOverall, 'overview' => $overview], 200);

        }
        catch (Exception $e) {
            return response(['message' => 'Unable to get the statistic data'], 404);
        }
    }


    /**
     * Return information on the top links for a given user
     */
    public function topLinks(Request $request)
    {
        try {

            $links = Link::WhereUser()
                ->select('id', 'title', 'slug', 'favicon', 'total_clicks')
                ->where('total_clicks', '>', 0)
                ->take($request->amount)
                ->orderBy('total_clicks', 'desc')
                ->get();

            return response($links, 200);

        }
        catch (Exception $e) {
            return response(['message' => 'Unable to get the statistic data'], 404);
        }
    }


    /**
     * Return information on the top referrers for a given user
     */
    public function referrers(Request $request)
    {
        try {

            $referrersQuery = StatisticReferrer::WhereUser()->orderBy('count', 'desc');


            if ( $request->amount )
                $referrers = $referrersQuery->take($request->amount)->get();
            else
                $referrers = $referrersQuery->where('link_id', $request->link_id)->paginate($request->per_page);


            return response($referrers, 200);

        }
        catch (Exception $e) {
            return response(['message' => 'Unable to get the statistic data'], 404);
        }
    }





    /**
     * Return information on the views
     */
    public function views(Request $request)
    {
        try {

          $where = "AND user = '".htmlentities(auth()->user()->id)."'";
          if($request->link) { $where .= " AND link = '".htmlentities($request->link)."'"; }

          $countriesData = json_decode(json_encode(DB::select(DB::raw("SELECT country, COUNT(*) FROM views WHERE country != '' ".$where." GROUP BY country"))), true);
          $browsersData = json_decode(json_encode(DB::select(DB::raw("SELECT browser, COUNT(*) FROM views WHERE browser != '' ".$where." GROUP BY browser"))), true);
          $osData = json_decode(json_encode(DB::select(DB::raw("SELECT os, COUNT(*) FROM views WHERE os != '' ".$where." GROUP BY os"))), true);
          $typesData = json_decode(json_encode(DB::select(DB::raw("SELECT type, COUNT(*) FROM views WHERE type != '' ".$where." GROUP BY type"))), true);

          //Clean data.
          $countries = []; $browsers = []; $os = []; $types = []; $selector = 1;
          if(is_array($countriesData) && array_key_exists(0, $countriesData)) {
            if(array_key_exists("count", $countriesData[0])) { $selector = "count"; }
            else if(array_key_exists("COUNT(*)", $countriesData[0])) { $selector = "COUNT(*)"; }
          }

          if(array_key_exists(0, $countriesData) && array_key_exists($selector, $countriesData[0])) {
            foreach ($countriesData as $data) { $countries[$data["country"]] = $data[$selector]; }
          }
          if(array_key_exists(0, $browsersData) && array_key_exists($selector, $browsersData[0])) {
            foreach ($browsersData as $data) { $browsers[$data["browser"]] = $data[$selector]; }
          }
          if(array_key_exists(0, $osData) && array_key_exists($selector, $osData[0])) {
            foreach ($osData as $data) { $os[$data["os"]] = $data[$selector]; }
          }
          if(array_key_exists(0, $typesData) && array_key_exists($selector, $typesData[0])) {
            foreach ($typesData as $data) { $types[ucfirst($data["type"])] = $data[$selector]; }
          }

          arsort($countries); arsort($browsers); arsort($os); arsort($types);

          return response(["countries" => $countries, "browsers" => $browsers, "operating_systems" => $os, "devices" => $types], 200);
        }
        catch (Exception $e) {
            return response(['message' => 'Unable to get the statistic data'], 404);
        }
    }

}
