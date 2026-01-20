<?php

namespace App\Models\v2;
use Session;

use Illuminate\Database\Eloquent\Model;

class CallPushRecord extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'call_push_records';
    protected $fillable = [
        'account_id','user_ids','devSn','fcm_token','roomId','roomuuid','roomCode','building_no','buildingCode','eventType','eventtime','captureImage'
    ];

    public function getunit(){
        return $this->belongsTo('App\Models\v2\Unit','roomCode', 'code');
    }

    public function deviceDetail($sno){
        $info = Device::where('device_serial_no',$sno)->first();
        return $info;
      }
}
