<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Link;
use App\Models\Domain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DomainResource;

class DomainController extends Controller
{

    public function index(Request $request)
    {
        try {  
            
            // Set order by
            $orderBy = ['created_at', 'desc'];

            if ( $request->order_by )
                $orderBy = explode('|', $request->order_by);


            $query = Domain::WhereUser()->orderBy($orderBy[0], $orderBy[1]);


            // If per_page is not set return all result
            $perPage = ( $request->per_page ? $request->per_page : $query->count() );
            
            return DomainResource::collection( $query->paginate($perPage) );

        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    public function store(Request $request)
    {

        // Check if the user has the ability to add more domains
        // Only check for non admin users
        if ( ! $request->user()->hasPermissionTo('access admin') ) {
        
            $canUse = $request->user()->subscription('membership')->ability()->canUse('domains');
            if ( ! $canUse )
                return response(['message' => 'You have maxed out the total number of Domains allowed on your current plan'], 422);

        }


        // Validate request server side
        $request->validate([
            'name'          => 'bail|required',
        ]);


        try {

            $cleanDomain = trim($request->name, '/');

            // If scheme not included, prepend it
            if (!preg_match('#^http(s)?://#', $cleanDomain)) {
                $cleanDomain = 'http://' . $cleanDomain;
            }

            $urlParts = parse_url($cleanDomain);

            // remove www
            $domain = preg_replace('/^www\./', '', $urlParts['host']);


            Domain::create([
                'user_id'   => $request->user()->id,
                'name'      => $domain
            ]);


            // Record feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->record('domains');

            
            return response(['message' => 'Your domain was successfully added'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to add this domain at the moment'], 500);
        }
    }


    public function update(Request $request, $id)
    {
        // Validate request server side
        $request->validate([
            'name'          => 'bail|required',
        ]);


        try {

            $cleanDomain = trim($request->name, '/');

            // If scheme not included, prepend it
            if (!preg_match('#^http(s)?://#', $cleanDomain)) {
                $cleanDomain = 'http://' . $cleanDomain;
            }

            $urlParts = parse_url($cleanDomain);

            // remove www
            $domain = preg_replace('/^www\./', '', $urlParts['host']);


            // Update the pixel data
            $userDomain = Domain::find($id);


            $userDomain->name = $domain;
            $userDomain->save();

            return response(['message' => 'Your domain name was successfully updated'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to update this domain name at the moment'], 500);
        }
    }


    public function destroy(Request $request, $id)
    {
        try {

            Domain::WhereUser()->where('id', $id)->delete();


            // Set all the links that use the domain name to null
            Link::where('domain_id', $id)
                ->where('user_id', auth()->user()->id)
                ->update(['domain_id' => null]);



            // Reduce feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->reduce('domains');
                

            return response(['message' => 'Your domain was successfully deleted'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to delete this domain at the moment'], 500);
        }
    }

}
