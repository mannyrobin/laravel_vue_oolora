<?php

namespace DanTheCoder\SaaSCore\Subscription\Resources;

use SaaSCoreHelper;
use Illuminate\Http\Resources\Json\JsonResource;
use DanTheCoder\SaaSCore\Account\Resources\User as UserResource;
use DanTheCoder\SaaSCore\Subscription\Resources\InvoiceLine as InvoiceLineResource;

class Invoice extends JsonResource
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
            'id'                    => $this->id,
            'invoice_number'        => $this->invoice_number,
            'billed_on'             => $this->created_at,
            'currency'              => $this->currency,
            'currency_symbol'       => SaaSCoreHelper::guessCurrencySymbol($this->currency),
            'subtotal'              => $this->subtotal,
            'total'                 => $this->total,
            'payment_gateway'       => $this->payment_gateway,
            'subscription_id'       => $this->subscription_id,
            'gateway_charge_id'     => $this->gateway_charge_id,
            'paypal_email'          => $this->paypal_email,
            'card_brand'            => $this->card_brand,
            'card_last4'            => $this->card_last4,
            'lines'                 => InvoiceLineResource::collection($this->whenLoaded('lines')),
            'user'                  => new UserResource($this->whenLoaded('user')),
            'refund'                => $this->whenLoaded('refund')
        ];
    }

}
