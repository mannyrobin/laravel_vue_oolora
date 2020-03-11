<?php

namespace DanTheCoder\SaaSCore\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use DanTheCoder\SaaSCore\Subscription\Traits\BelongsToPlan;
use DanTheCoder\SaaSCore\Subscription\Contracts\PlanFeatureInterface;

class PlanFeature extends Model implements PlanFeatureInterface
{
    use BelongsToPlan;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_id',
        'name',
        'code',
        'value',
        'sort_order',
        'value_selection'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];

    /**
     * Get feature usage.
     *
     * This will return all related
     * subscriptions usages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usage()
    {
        return $this->hasMany(PlanSubscriptionUsage::class);
    }
}
