<?php

namespace App\Models\v2;
use Session;


use Illuminate\Database\Eloquent\Model;

class HolidaySetting extends Model
{
    protected $table = "holiday_settings";
    
    protected $fillable = ['account_id','public_holidays'];

}
