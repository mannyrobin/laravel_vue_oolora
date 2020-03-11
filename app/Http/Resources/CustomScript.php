<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomScript extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'name'          => $this->name,
            'code'          => $this->code,
            'disabled'      => $this->disabled,
            'created_at'    => $this->created_at,
            'links_count'   => $this->links_count,
            'links'         => $this->whenLoaded('links'),
        ];
    }
}
