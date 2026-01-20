<?php

namespace App\Models\v7;
use App\Models\v7\Device;

use Illuminate\Database\Eloquent\Model;

class RemoteDoorOpen extends Model
{
    protected $fillable = [
      'user_id','account_id','unit_no','devMac','devType','eKey','devSn','devName','status','action_type','call_date_time'
    ]; 

  public function user(){
      return $this->belongsTo('App\Models\v7\User','user_id');
  }

  public function deviceDetail($sno){
    $info = Device::where('device_serial_no',$sno)->first();
    return $info;
  }

  public function getunit(){
    return $this->belongsTo('App\Models\v7\Unit','unit_no');
}


}
