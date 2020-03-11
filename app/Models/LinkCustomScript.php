<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LinkCustomScript extends Pivot
{
	public $timestamps = false;

	
    // Guarded attributes
    protected $guarded = [];
}