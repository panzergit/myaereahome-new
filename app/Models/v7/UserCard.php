<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{
    protected $fillable = [
      'user_id','user_info_id','property_id','building_id','unit_id','card_no','status'
    ];  

    public function role(){
      return $this->belongsTo('App\Models\v7\Role','role_id');
  }
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

    public function addubuildinginfo(){
      return $this->belongsTo('App\Models\v7\Building','building_id');
    }


}
