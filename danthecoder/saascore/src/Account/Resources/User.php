<?php

namespace DanTheCoder\SaaSCore\Account\Resources;

use Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'avatar'            => Storage::url($this->avatar),
            'email'             => $this->email,
            'email_verified'    => ($this->email_verified_at ? true : false),
            'created_at'        => $this->created_at,
            'timezone'          => $this->timezone,
            'deleted'           => ($this->deleted_at ? true : false),
            'roles'             => $this->whenLoaded('roles'),
            'subscribable'      => $this->whenLoaded('subscribable'),
        ];
    }

}
