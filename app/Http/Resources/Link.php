<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Link extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        if ( $this->domain === null ) {
            if ( ! config('settings.link_shorten_domain') ) {
                $domain = config('app.url') .'/l';
            } else {
                $domain = 'https://'.config('settings.link_shorten_domain');
            }
        } else {
            $domain = 'https://'.$this->domain['name'];
        }


        return [
            'id'                    => $this->id,
            'user_id'               => $this->user_id,
            'campaign_id'           => $this->campaign_id,
            'slug'                  => $this->slug,
            'url'                   => $this->url,
            'title'                 => $this->title,
            'favicon'               => $this->favicon,
            'campaign'              => $this->campaign,
            'domain'                => $domain,
            'total_clicks'          => ($this->total_clicks ? $this->total_clicks : 0),
            'total_unique_clicks'   => ($this->total_unique_clicks ? $this->total_unique_clicks : 0),
            'total_conversion'      => ($this->total_conversion ? $this->total_conversion : 0),
            'iframe_blocked'        => $this->iframe_blocked,
            'disabled'              => $this->disabled,
            'created_at'            => $this->created_at,
        ];
    }
}
