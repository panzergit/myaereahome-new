<?php

namespace App\Models\v2;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class QrcodeOpenRecord extends Model
{
     protected $fillable = [
     'account_id', 'booking_id','visitor_id','devSn','dataType','message'
    ];

    public function BookingInfo(){
        return $this->belongsTo('App\Models\v2\VisitorBooking','booking_id');
    }

    public function VisitorInfo(){
        return $this->belongsTo('App\Models\v2\VisitorList','visitor_id');
    }

    public function deviceDetail($sno){
        $info = Device::where('device_serial_no',$sno)->first();
        return $info;
      }

   
}
