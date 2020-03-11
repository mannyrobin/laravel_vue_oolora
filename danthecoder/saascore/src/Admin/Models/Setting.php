<?php

namespace DanTheCoder\SaaSCore\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	
	public $timestamps = false;
	
	// Mass assignable attributes
	protected $fillable = [
		'key', 'value'
	];
	
}
