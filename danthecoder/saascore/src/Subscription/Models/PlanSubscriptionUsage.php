<?php

namespace DanTheCoder\SaaSCore\Subscription\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use DanTheCoder\SaaSCore\Subscription\Contracts\PlanSubscriptionUsageInterface;

class PlanSubscriptionUsage extends Model implements PlanSubscriptionUsageInterface
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subscription_id',
        'code',
        'valid_until',
        'used'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'valid_until',
    ];

    /**
     * Get feature.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feature()
    {
        return $this->belongsTo(PlanFeature::class);
    }

    /**
     * Get subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo(PlanSubscription::class);
    }

    /**
     * Scope by feature code.
     *
     * @param \Illuminate\Database\Eloquent\Builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByFeatureCode($query, $feature_code)
    {
        return $query->whereCode($feature_code);
    }

    /**
     * Check whether usage has been expired or not.
     *
     * @return bool
     */
    public function isExpired()
    {
        if (is_null($this->valid_until)) {
            return false;
        }

        return Carbon::now()->gt($this->valid_until) or Carbon::now()->eq($this->valid_until);
    }
}
