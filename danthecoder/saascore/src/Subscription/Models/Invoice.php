<?php

namespace DanTheCoder\SaaSCore\Subscription\Models;

use Carbon\Carbon;
use App\Models\User;
use DanTheCoder\SaaSCore\Admin\Models\Refund;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

    // Guarded attributes
    protected $guarded = [];


    // Get invoice line items
    public function lines()
    {
        return $this->hasMany( InvoiceLine::class );
    }


    // Get any refunds associated with the payment
    public function refund()
    {
        return $this->hasOne( Refund::class );
    }


    // Get the user who made the payment
    public function user()
    {
        return $this->belongsTo( User::class )->withTrashed();
    }


    /**
     * Scope a query to only include data for the user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereUser($query) {
        return $query->where( 'user_id', auth()->user()->id );
    }


    // Format dates
	public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone( auth()->user()->timezone )->format( config('settings.date_format') );
    }

}
