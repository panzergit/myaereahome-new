<?php

namespace App\Models\v7;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Econcierge extends Model
{
     protected $fillable = [
     'account_id', 'assigned_property','banner_title','banner_image','display_order','status'
    ];

}
