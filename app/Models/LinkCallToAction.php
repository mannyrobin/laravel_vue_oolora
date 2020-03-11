<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LinkCallToAction extends Pivot
{
	public $timestamps = false;


    // Guarded attributes
    protected $guarded = [];
}
