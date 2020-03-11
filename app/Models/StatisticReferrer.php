<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticReferrer extends Model
{
	public $timestamps = false;

		
    // Guarded attributes
    protected $guarded = [];

 
    // Scope a query to only include data for the user
    public function scopeWhereUser($query) {
        return $query->where( 'user_id', auth()->user()->id );
    }

}
