<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CustomScript extends Model
{

    // Guarded attributes
    protected $guarded = [];


    // Casting
    protected $casts = [
        'disabled' => 'boolean',
    ];

    
    // Get the user who made the payment
    public function user()
    {
        return $this->belongsTo( User::class )->withTrashed();
    }


    // Links pivot relationship
    public function links()
    {
        return $this->belongsToMany( Link::class, 'link_custom_script' );
    }


    // Scope a query to only include data for the user
    public function scopeWhereUser($query) {
        return $query->where( 'user_id', auth()->user()->id );
    }


    // Format created at date
    public function getCreatedAtAttribute($value)
    {
       return Carbon::parse($value)->timezone( auth()->user()->timezone )->format( config('settings.date_format') );
    }
}
