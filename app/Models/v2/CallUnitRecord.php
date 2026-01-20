<?php

namespace App\Models\v2;
use App\Models\v2\Device;

use Illuminate\Database\Eloquent\Model;

class CallUnitRecord extends Model
{
    protected $fillable = [
      'account_id','devSn','roomId','roomuuid','roomCode','buildingCode','eventType','eventtime','captureImage'
    ]; 

  public function user(){
      return $this->belongsTo('App\Models\v2\User','user_id');
  }

  



}
