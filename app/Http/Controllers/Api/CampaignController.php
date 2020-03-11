<?php

namespace App\Http\Controllers\Api;

use App\Models\Link;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Campaign as CampaignResource;

class CampaignController extends Controller
{

    /**
     * Return all campaigns for a user
     */
    public function index(Request $request)
    {
        try {
            
            $campaignQuery = Campaign::WhereUser()
                ->with(['links' => function($query) {
                    $query->select('campaign_id', 'total_clicks', 'total_unique_clicks', 'total_conversion');
                }])
                ->orderBy('created_at', 'desc');


            // If per_page is not set return all result
            $perPage = ( $request->per_page ? $request->per_page : $campaignQuery->count() );

            return CampaignResource::collection( $campaignQuery->paginate($perPage) );
        } 
        catch (Exception $e) {
            return response(['message' => 'Unable to create this campaign at the moment'], 500);
        }        
    }


    /**
     * Store a created campaign
     */
    public function store(Request $request)
    {

        // Check if the user has the ability to add more pixels
        // Only check for non admin users
        if ( ! $request->user()->hasPermissionTo('access admin') ) {
        
            $canUse = $request->user()->subscription('membership')->ability()->canUse('campaigns');
            if ( ! $canUse )
                return response(['message' => 'You have maxed out the total number of Campaigns allowed on your current plan'], 422);

        }


        // Validate request server side
        $request->validate([
            'name' => 'bail|required|string|max:40'
        ]);


        try {

            Campaign::create( $request->all() + ['user_id' => $request->user()->id] );
        

            // Record feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->record('campaigns');


            return response(['message' => 'Your campaign was successfully created'], 201);
        
        } 
        catch (Exception $e) {
            return response(['message' => 'Unable to create this campaign at the moment'], 500);
        }
    }


    /**
     * Update a campaign
     */
    public function update(Request $request, $id)
    {
        // Validate request server side
        $request->validate([
            'name' => 'bail|required|string|max:40'
        ]);


        try {

            $campagin = Campaign::WhereUser()->where('id', $id)->first();
            
            $campagin->name = trim( $request->name );
            $campagin->save();

            return response(['message' => 'Your campaign was successfully updated'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to update this campaign at the moment'], 500);
        }
    }


    /**
     * Delete a campaign
     */
    public function destroy(Request $request, $id)
    {
        try {

            // Remove campaign
            Campaign::WhereUser()->where('id', $id)->delete();


            // Set all the links that are under this campaign to null
            Link::where('campaign_id', $id)
                ->where('user_id', auth()->user()->id)
                ->update(['campaign_id' => null]);


            // Reduce feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->reduce('campaigns');
                

            return response(['message' => 'Your campaign was successfully deleted'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to delete this campaign at the moment'], 500);
        }
    }

}
