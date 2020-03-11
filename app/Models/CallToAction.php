<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CallToAction extends Model
{

    // Guarded attributes
    protected $guarded = [];


	// Casting
	protected $casts = [
		'disabled' 	=> 'boolean',
	    'meta' 	=> 'array',
	];


    // Links pivot relationship
    public function links()
    {
        return $this->belongsToMany( Link::class, 'link_call_to_action' );
    }


    // Scope a query to only include data for the user
    public function scopeWhereUser($query) {
        return $query->where( 'user_id', auth()->user()->id );
    }


    // Format created at date
    public function getCreatedAtAttribute($value)
    {
        if ( auth()->user() )
            return Carbon::parse($value)->timezone( auth()->user()->timezone )->format( config('settings.date_format') );
        else
            return $value;
    }

}
