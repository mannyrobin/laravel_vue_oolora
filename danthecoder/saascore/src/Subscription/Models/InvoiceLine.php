<?php

namespace DanTheCoder\SaaSCore\Subscription\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{

    // Guarded attributes
    protected $guarded = [];


    // Format dates
	public function getPeriodStartAttribute($value)
    {
        return Carbon::parse($value)->timezone( auth()->user()->timezone )->format( config('settings.date_format') );
    }
    
    public function getPeriodEndAttribute($value)
    {
        return Carbon::parse($value)->timezone( auth()->user()->timezone )->format( config('settings.date_format') );
    }

}
