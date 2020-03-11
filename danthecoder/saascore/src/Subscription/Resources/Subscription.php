<?php

namespace DanTheCoder\SaaSCore\Subscription\Resources;

use SaaSCoreHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class Subscription extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Check if the user free trial has ended
        $freeTrialEnded = false;
        if ( $request->user()->subscription('membership')->onTrial() === false && $request->user()->stripe_card === null && $request->user()->paypal_id === null )
            $freeTrialEnded = true;

        return [
            "id"                            => $this['id'],
            "gateway_subscription_id"       => $this['gateway_subscription_id'],
            "canceled_immediately"          => $this['canceled_immediately'],
            'free_trial_ended'              => $freeTrialEnded,
            "gateway"                       => $this['gateway'],
            "trial_ends_at"                 => is_null( $this['trial_ends_at']) ? null : SaaSCoreHelper::formatDate($this['trial_ends_at']),
            "starts_at"                     => is_null( $this['starts_at']) ? null : SaaSCoreHelper::formatDate($this['starts_at']),
            "ends_at"                       => is_null( $this['ends_at']) ? null : SaaSCoreHelper::formatDate($this['ends_at']),
            "canceled_at"                   => is_null( $this['canceled_at']) ? null : SaaSCoreHelper::formatDate($this['canceled_at']),
            "plan"                          => $this['plan'],
            "payment_method"                => $this['payment_method']
        ];
    }

}
