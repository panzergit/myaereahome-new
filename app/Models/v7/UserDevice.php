<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $fillable = [
      'user_id','building_id','unit_no','account_id','device_id','device_svn','status'
    ]; 

    public function deviceinfo(){
      return $this->belongsTo('App\Models\v7\Device','device_id');
  }
   

}
