<?php

namespace App\Models\v7;
use App\Models\v7\Device;

use Illuminate\Database\Eloquent\Model;

class NormalDoorOpenRecord extends Model
{
    protected $fillable = [
      'user_id','account_id','unit_no','empuuid','empname','empPhone','empCardNo','devId','devuuid','devname','devSn','building_no','buildingCode','eventType','eventtime','captureImageBase64','captureImageUrl','faceAge','faceGender','','faceMatchScore','bodyTemperature'
    ]; 

  public function user(){
      return $this->belongsTo('App\Models\v7\User','empuuid');
  }

  public function getunit(){
    return $this->belongsTo('App\Models\v7\Unit','unit_no');
  }

  



}
