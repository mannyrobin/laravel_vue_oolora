<?php

namespace DanTheCoder\SaaSCore\Admin\Models;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    // Guarded attributes
    protected $guarded = [];


    // Get the admin who made the refund
    public function user()
    {
        return $this->belongsTo( User::class );
    }


    // Format dates
	public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone( auth()->user()->timezone )->format( config('settings.date_format') );
    }
}
