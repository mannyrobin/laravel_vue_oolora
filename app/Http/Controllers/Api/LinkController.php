<?php

namespace App\Http\Controllers\Api;

use App\Models\Link;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Requests\LinkRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Link as LinkResource;

class LinkController extends Controller
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


            $linkQuery = Link::WhereUser()
                ->with(['campaign' => function($query) {
                    $query->select('name', 'id');
                }])
                ->with(['domain' => function($query) {
                    $query->select('name', 'id');
                }])
                ->orderBy($orderBy[0], $orderBy[1])
                ->when($request->campaign_where, function ($query, $campaigns) {
                    return $query->whereIn('campaign_id', $campaigns);
                });


            // If per_page is not set return all result
            $perPage = ( $request->per_page ? $request->per_page : $linkQuery->count() );

            return LinkResource::collection( $linkQuery->paginate($perPage) );

        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Show a specific link
     * use on the link creator page
     */
    public function show($id)
    {
        try {

            $link = Link::WhereUser()->where('id', $id)->firstOrFail();


            // Get the pixel Id's
            $pixels = [];
            foreach ($link->pixels as $pixel) {
               $pixels[] = $pixel->pivot->pixel_id;
            }


            // Get all the scripts ID's
            $scripts = [];
            foreach ($link->scripts as $script) {
               $scripts[] = $script->pivot->custom_script_id;
            }


            $linkResult = array_merge( $link->toArray(), ['pixels' => $pixels], ['scripts' => $scripts], ['cta' => ( isset($link->cta[0]->id) ? $link->cta[0]->id : null) ]);

            return response($linkResult, 200);

        } catch (Exception $e) {
            return response(['message' => 'Unable to locate this link'], 404);
        }
    }


    /**
     * Save a link to the DB
     */
    public function store(Request $request)
    {
        // Check if the user has the ability to add more pixels
        // Only check for non admin users
        if ( ! $request->user()->hasPermissionTo('access admin') ) {
        
            $canUse = $request->user()->subscription('membership')->ability()->canUse('links');
            if ( ! $canUse )
                return response(['message' => 'You have maxed out the total number of Smart Links allowed on your current plan'], 422);

        }


        // Validate request server side
        $request->validate([
            'url' => 'required|active_url',
        ]);


        try {

            $linkUrl = trim( $request->url );
            $urlData = $this->getUrlData($linkUrl);


            // Ensure that the slug is unique
            do {
                $slug = str_random(6);
            } while ( Link::where('slug', $slug)->exists() );


            $addLink = Link::create([
                'user_id'           => $request->user()->id,
                'slug'              => $slug,
                'iframe_blocked'    => $urlData['iframe_blocked'],
                'url'               => $linkUrl,
                'title'             => $urlData['link_title'],
                'favicon'           => $urlData['favicon']
            ]);


            // Record feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->record('links');


            return response($addLink, 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to add this link at the moment'], 500);
        }
    }


    /**
     * Update a link
     */
    public function update(LinkRequest $request, $id)
    {
        // Validate the request
        $request->validated();


        try {

            $link = Link::WhereUser()->where('id', $id)->first();

            $shortenUrlOnly = $request->shorten_only ? 1 : null;

            $link->title            = $request->title;
            $link->favicon          = $request->favicon;
            $link->iframe_blocked   = $request->iframe_blocked;
            $link->url              = trim( $request->url );
            $link->slug             = trim( $request->slug );
            $link->campaign_id      = $request->campaign_id;
            $link->domain_id      = $request->domain_id;
            $link->shorten_only     = $shortenUrlOnly;
            $link->save();


            // Add the pixels to the pivot table
            $link->pixels()->sync( $request->pixels );


            // Add the custom scripts to the pivot table
            $link->scripts()->sync( $request->scripts );


            // Add the call to action to the pivot table
            $link->cta()->sync( $request->cta );


            return response(['message' => 'Your link was successfully updated'], 201);
        
        } 
        catch (Exception $e) {
            return response(['message' => 'Unable to update your link at the moment'], 500);
        }
    }


    /**
     * Delete a link
     */
    public function destroy(Request $request, $id)
    {
        try {

            Link::WhereUser()->where('id', $id)->delete();


            // Reduce feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->reduce('links');
                

            return response(['message' => 'Your link was deleted successfully'], 201);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to delete your link at the moment'], 500);
        }
    }


    /**
     * Change the link status from disabled or enabled
     */
    public function changeStatus(Request $request, $id)
    {
        try {

            $link = Link::WhereUser()->where('id', $id)->first();

            $link->disabled = $request->disabled;
            $link->save();

            return response(['message' => 'Your link status was successfully updated'], 201);
        
        } 
        catch (Exception $e) {
            return response(['message' => 'Unable to update your link status at the moment'], 500);
        }
    }

    /**
     * Return instant data about a given link
     */
    public function linkCheck(Request $request)
    {
        // Validate request server side
        $request->validate([
            'url' => 'required|active_url'
        ]);

        try {

            $linkUrl = trim( $request->url );
            $urlData = $this->getUrlData($linkUrl);

            // Return the data
            $result = array(
                'url'               => $linkUrl,
                'title'             => $urlData['link_title'],
                'favicon'           => $urlData['favicon'],
                'iframe_blocked'    => $urlData['iframe_blocked']
            );


            return response($result, 200);
        
        } catch (Exception $e) {
            return response(['message' => 'Unable to process the URL you have entered'], 500);
        }
    }


    // Get data on a given URL
    protected function getUrlData($url)
    {
        $websiteContent = Helper::curl($url);

        $parseUrl = parse_url($url);
        $websiteTitle = $parseUrl['host'];
        $favicon = asset('assets/images/web.png');

        if ($websiteContent['body']) {
            
            $htmlDom = new \DOMDocument();
            @$htmlDom->loadHTML($websiteContent['body']);
            
            $websiteTitle = $htmlDom->getElementsByTagName('title')->item(0)->nodeValue;


            // Get the favicon
            $domLinks = $htmlDom->getElementsByTagName('link');

            // Loop through all the link tags
            for( $i = 0; $i < $domLinks->length; $i++ ) {
                
                $link = $domLinks->item($i);
                
                // Check for links that has these attributes
                if($link->getAttribute('rel') == 'icon' || $link->getAttribute('rel') == "Shortcut Icon" || $link->getAttribute('rel') == "shortcut icon") {
                    
                    $favicon = $link->getAttribute('href');

                    // check if absolute url or relative path
                    $faviconPath = parse_url($favicon);

                    if( ! isset($faviconPath['host']) )
                        $favicon = $parseUrl['scheme'] . '://' . $parseUrl['host'] . '/' . $favicon;

                }
            }

        }

        $iframeCheck = ( isset($websiteContent['headers']["x-frame-options"]) ? 1 : null);

        $result = array(
            'iframe_blocked'    => $iframeCheck,
            'link_title'        => $websiteTitle,
            'favicon'           => $favicon
        );

        return $result;
    }

}