<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class UserLicensePlate extends Model
{
    protected $fillable = [
      'user_id','user_info_id','property_id','building_id','unit_id','license_plate'
    ];  
   
    public function userinfo(){
      return $this->belongsTo('App\Models\v7\User','user_id');
    }

    public function usermoreinfo(){
      return $this->belongsTo('App\Models\v7\UserMoreInfo','user_info_id');
    }

    public function addpropinfo(){
      return $this->belongsTo('App\Models\v7\Property','property_id');
    }

    public function addunitinfo(){
      return $this->belongsTo('App\Models\v7\Unit','unit_id');
    }

    public function buildinginfo(){
      return $this->belongsTo('App\Models\v7\Building','building_id');
    }

    




}
