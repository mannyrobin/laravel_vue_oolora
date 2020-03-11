<?php

namespace DanTheCoder\SaaSCore\Subscription\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceLine extends JsonResource
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
            'description'       => $this->description,
            'amount'            => $this->amount,
            'period_start'      => $this->period_start,
            'period_end'        => $this->period_end
        ];
    }

}
