<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['serial_no', 'code', 'status', 'plan_id', 'period', 'user_id'];



}
