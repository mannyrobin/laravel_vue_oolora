<?php

namespace DanTheCoder\SaaSCore\Account\Resources;

use SaaSCoreHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class Notification extends JsonResource
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
            'title'         => $this->data['title'],
            'message'       => $this->data['message'],
            'action'        => $this->data['action'],
            'icon'          => SaaSCoreHelper::getNotificationIcon($this->type),
            'icon_color'    => SaaSCoreHelper::getNotificationIconColor($this->type),
            'date'          => $this->created_at->timezone( auth()->user()->timezone )->diffForHumans(),
            'read'          => ($this->read_at === null ? false : true)
        ];
    }

}