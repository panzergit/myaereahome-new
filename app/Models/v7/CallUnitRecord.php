<?php

namespace App\Models\v7;
use App\Models\v7\Device;

use Illuminate\Database\Eloquent\Model;

class CallUnitRecord extends Model
{
    protected $fillable = [
      'account_id','devSn','roomId','roomuuid','roomCode','buildingCode','eventType','eventtime','captureImage'
    ]; 

  public function user(){
      return $this->belongsTo('App\Models\v7\User','user_id');
  }

  



}
