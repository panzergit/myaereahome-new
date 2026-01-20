<?php

namespace App\Models\v7;
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
        return $this->belongsTo('App\Models\v7\Unit','roomCode', 'code');
    }

    public function deviceDetail($sno){
        $info = Device::where('device_serial_no',$sno)->first();
        return $info;
      }

      
    public function unitDetail($roomcode,$account_id){
        $roomCode = $roomcode;
		$unitcode = "0".$roomcode;
        $unitObj = Unit::where('account_id',$account_id)->get();
        $unit_id ='';
		if(isset($unitObj)){
			foreach($unitObj as $unit){
				//echo " ;".Crypt::decryptString($unit->code)." ".$roomCode;
				if(Crypt::decryptString($unit->code) == $roomCode || Crypt::decryptString($unit->code) ==$unitcode){
					$unit_id = $unit->id;
					break;
				}
			}
		}
        return $unit_id;
      }
}
