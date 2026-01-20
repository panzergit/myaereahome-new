<?php

namespace App\Models\v2;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Econcierge extends Model
{
     protected $fillable = [
     'account_id', 'assigned_property','banner_title','banner_image','description','display_order','status'
    ];

}
