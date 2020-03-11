<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Pixel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Pixel as PixelResource;

class PixelController extends Controller
{

    /**
     * Return all pixels
     */
    public function index(Request $request)
    {
        try {  
            
            // Set order by
            $orderBy = ['created_at', 'desc'];

            if ( $request->order_by )
                $orderBy = explode('|', $request->order_by);


            $pixelQuery = Pixel::WhereUser()->orderBy($orderBy[0], $orderBy[1])->withCount('links');


            // If per_page is not set return all result
            $perPage = ( $request->per_page ? $request->per_page : $pixelQuery->count() );
            
            return PixelResource::collection( $pixelQuery->paginate($perPage) );

        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Store a new created pixel
     */
    public function store(Request $request)
    {
        // Check if the user has the ability to add more pixels
        // Only check for non admin users
        if ( ! $request->user()->hasPermissionTo('access admin') ) {
        
            $canUse = $request->user()->subscription('membership')->ability()->canUse('pixels');
            if ( ! $canUse )
                return response(['message' => 'You have maxed out the total number of Pixels allowed on your current plan'], 422);

        }


        // Validate request server side
        $request->validate([
            'name'          => 'bail|required|string|max:40',
            'platform'      => 'required',
            'pixel_code'    => 'required'
        ]);


        try {

            Pixel::create([
                'user_id'   => $request->user()->id,
                'name'      => trim( $request->name ),
                'platform'  => $request->platform,
                'code'      => trim( $request->pixel_code ),
            ]);


            // Record feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->record('pixels');

            
            return response(['message' => 'Your pixel was successfully added'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to add this pixel at the moment'], 500);
        }
    }


    /**
     * Update a pixel
     */
    public function update(Request $request, $id)
    {
        // Validate request server side
        $request->validate([
            'name'          => 'bail|required|string|max:40',
            'platform'      => 'required',
            'code'          => 'required'
        ]);


        try {

            // Update the pixel data
            $pixel = Pixel::find($id);

            $pixel->name        = trim( $request->name );
            $pixel->platform    = $request->platform;
            $pixel->code        = trim( $request->code );
            $pixel->disabled    = ($request->disabled ? 1 : null);
            $pixel->save();

            return response(['message' => 'Your pixel was successfully updated'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to update this pixel at the moment'], 500);
        }
    }


    /**
     * Delete a pixel
     */
    public function destroy(Request $request, $id)
    {
        try {

            Pixel::WhereUser()->where('id', $id)->delete();


            // Reduce feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->reduce('pixels');
                

            return response(['message' => 'Your pixel was successfully deleted'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to delete this pixel at the moment'], 500);
        }
    }

}
