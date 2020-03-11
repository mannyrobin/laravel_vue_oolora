<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\CustomScript;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomScript as CustomScriptResource;

class CustomScriptController extends Controller
{

    /**
     * Return all Custom Scripts
     */
    public function index(Request $request)
    {
        try {  
            
            // Set order by
            $orderBy = ['created_at', 'desc'];

            if ( $request->order_by )
                $orderBy = explode('|', $request->order_by);


            $scriptQuery = CustomScript::WhereUser()
                ->orderBy($orderBy[0], $orderBy[1])
                ->withCount('links');


            // If per_page is not set return all result
            $perPage = ( $request->per_page ? $request->per_page : $scriptQuery->count() );

            return CustomScriptResource::collection( $scriptQuery->paginate($perPage) );

        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Store a new created Custom Script
     */
    public function store(Request $request)
    {

        // Check if the user has the ability to add more custom scripts
        // Only check for non admin users
        if ( ! $request->user()->hasPermissionTo('access admin') ) {
        
            $canUse = $request->user()->subscription('membership')->ability()->canUse('custom_scripts');
            if ( ! $canUse )
                return response(['message' => 'You have maxed out the total number of Custom Scripts allowed on your current plan'], 422);

        }


        // Validate request server side
        $request->validate([
            'name'      => 'bail|required|string|max:40',
            'code'      => 'required',
        ]);


        try {

            CustomScript::create([
                'user_id'   => $request->user()->id,
                'name'      => trim( $request->name ),
                'code'      => $request->code,
            ]);


            // Record feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->record('custom_scripts');


            return response(['message' => 'Your custom script was successfully added'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to add your custom script at the moment'], 500);
        }
    }


    /**
     * Update a Custom Script
     */
    public function update(Request $request, $id)
    {
        // Validate request server side
        $request->validate([
            'name'    => 'bail|required|string|max:40',
            'code'    => 'required'
        ]);


        try {

            $customScript = CustomScript::find($id);

            $customScript->name         = trim( $request->name );
            $customScript->code         = $request->code;
            $customScript->disabled     = ($request->disabled ? 1 : null);
            $customScript->save();

            return response(['message' => 'Your custom script was successfully updated'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to update your custom script at the moment'], 500);
        }
    }


    /**
     * Delete a Custom Script
     */
    public function destroy(Request $request, $id)
    {
        try {

            CustomScript::WhereUser()->where('id', $id)->delete();


            // Reduce feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->reduce('custom_scripts');


            return response(['message' => 'Your custom script was successfully deleted'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to delete your custom script at the moment'], 500);
        }
    }

}
