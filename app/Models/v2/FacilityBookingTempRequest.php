<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class FacilityBookingTempRequest extends Model
{
     protected $fillable = [
      'type_id','user_id','account_id','unit_no','booking_date','booking_time','status'
    ];

}
