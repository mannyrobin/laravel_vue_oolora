<?php

namespace App\Http\Controllers\Api;

use Storage;
use Carbon\Carbon;
use App\Models\Link;
use Illuminate\Support\Str;
use App\Models\CallToAction;
use Illuminate\Http\Request;
use App\Models\StatisticLink;
use App\Models\StatisticOverall;
use App\Http\Controllers\Controller;
use App\Http\Resources\CallToAction as CallToActionResource;

class CallToActionController extends Controller
{

    /**
     * Return all call to action
     */
    public function index(Request $request)
    {
        try {  
            
            // Set order by
            $orderBy = ['created_at', 'desc'];

            if ( $request->order_by )
                $orderBy = explode('|', $request->order_by);


            $ctaQuery = CallToAction::WhereUser()->orderBy($orderBy[0], $orderBy[1])->withCount('links');


            // If per_page is not set return all result
            $perPage = ( $request->per_page ? $request->per_page : $ctaQuery->count() );
            
            return CallToActionResource::collection( $ctaQuery->paginate($perPage) );

        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Show a specific cta
     */
    public function show($id)
    {
        try {

            $cta = new CallToActionResource( CallToAction::WhereUser()->where('id', $id)->firstOrFail() );
            
            return response($cta, 200);

        } catch (Exception $e) {
            return response(['message' => 'Unable to locate this call to action'], 404);
        }
    }


    /**
     * Show a specific cta
     */
    public function previewCta($id)
    {
        try {

            $cta = new CallToActionResource( CallToAction::where('id', $id)->firstOrFail() );
            
            return response($cta, 200);

        } catch (Exception $e) {
            return response(['message' => 'Unable to locate this call to action'], 404);
        }
    }


    /**
     * Store or update a call to action
     */
    public function store(Request $request)
    {

        if ( ! $request->id) {
            // Check if the user has the ability to add more pixels
            // Only check for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') ) {
            
                $canUse = $request->user()->subscription('membership')->ability()->canUse('call_to_actions');
                if ( ! $canUse )
                    return response(['message' => 'You have maxed out the total number of Call to Actions allowed on your current plan'], 422);

            }
        }


        // Validate request server side
        $request->validate([
            'name'      => 'bail|required|string|max:40',
            'type'      => 'required'
        ]);


        try {

            if ( $request->id )
                $cta = CallToAction::WhereUser()->find($request->id);
            else
                $cta = new CallToAction;


            $cta->user_id = $request->user()->id;
            $cta->name = trim( $request->name );
            $cta->type = $request->type;
            $cta->meta = $request->meta;

            $cta->save();


            if ( ! $request->id) {
                // Record feature usage for non admin users
                if ( ! $request->user()->hasPermissionTo('access admin') )
                    $request->user()->subscriptionUsage('membership')->record('call_to_actions');
            }
            
            return response(['message' => 'Your call to action was successfully saved'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to save your call to action at the moment'], 500);
        }
    }


    /**
     * Delete a CTA
     */
    public function destroy(Request $request, $id)
    {
        try {

            CallToAction::WhereUser()->where('id', $id)->delete();


            // Reduce feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->reduce('call_to_actions');
                

            return response(['message' => 'Your call to action was successfully deleted'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to delete this call to action at the moment'], 500);
        }
    }


    /**
     * Change the CTA status from disabled or enabled
     */
    public function changeStatus(Request $request, $id)
    {
        try {

            $link = CallToAction::WhereUser()->where('id', $id)->first();

            $link->disabled = $request->disabled;
            $link->save();

            return response(['message' => 'Your call to action status was successfully updated'], 201);
        
        } 
        catch (Exception $e) {
            return response(['message' => 'Unable to update your call to action status at the moment'], 500);
        }
    }


    /**
     * Record conversion click from a CTA
     */
    public function recordConversion($id)
    {
        try {
            
            // Link total conversion
            $link = Link::find($id);
            $link->total_conversion++;
            $link->save();


            // Link daily conversion
            $linkStats = StatisticLink::where(['created_at' => Carbon::today()->toDateTimeString(), 'link_id' => $link->id])->first();
            $linkStats->conversion++;
            $linkStats->save();


            // Overall daily conversion
            $linkOverallStats = StatisticOverall::where(['created_at' => Carbon::today()->toDateTimeString(), 'user_id' => $link->user_id])->first();
            $linkOverallStats->conversion++;
            $linkOverallStats->save();

            return response([], 201);

        } catch (Exception $e) {
            return response($e, 500);
        }
    }


    /**
     * Upload CTA graphics
     */
    public function uploadImage(Request $request)
    {
        // Validation the file upload
        $request->validate([
            'file' => 'bail|required|file|image|mimes:jpeg,png|max:2048'
        ]);


        // Check if file exist
        if( ! $request->hasFile('file') )
            return response(['message' => 'No file was selected'], 422);


        // Is the file valid
        if( ! $request->file('file')->isValid() )
            return response(['message' => 'The file is not valid'], 422);


        try {

            $file = $request->file('file');


            // Delete the old file
            $oldFile = Str::replaceFirst( config('app.url').'/storage/', '', $request->current_file );

            if( $oldFile != 'cta-graphics/default.png')
                Storage::disk('public')->delete($oldFile);


            // Save the file to the storage
            $filePath = $request->file('file')->store('cta-graphics');
            // $filePath = $request->file('file')->storeAs('assets', $request->settings_key.'.'.$file->guessExtension());


            return response(['image_url' => Storage::url($filePath), 'message' => 'The file was successfully uploaded'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove an image that was uploaded
     */
    public function removeImage(Request $request)
    {
        try {
            $oldFile = Str::replaceFirst( config('app.url').'/storage/', '', $request->current_file );

            if( $oldFile != 'cta-graphics/default.png')
                Storage::disk('public')->delete($oldFile);


            return response(['message' => 'The image was successfully removed'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


}
