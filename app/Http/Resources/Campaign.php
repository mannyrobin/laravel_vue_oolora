<?php

namespace App\Http\Resources;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class Campaign extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $clicks = $this->links->sum('total_clicks');
        $unique_clicks = $this->links->sum('total_unique_clicks');
        $conversion = $this->links->sum('total_conversion');

        return [
            'id'                => $this->id,
            'user_id'           => $this->user_id,
            'name'              => $this->name,
            'clicks'            => $clicks,
            'unique_clicks'     => $unique_clicks,
            'conversion'        => $conversion,
            'conversion_rate'   => Helper::conversionRate($clicks, $conversion),
            'links'             => $this->links->count(),
            'created_at'        => $this->created_at,
        ];
    }
}
