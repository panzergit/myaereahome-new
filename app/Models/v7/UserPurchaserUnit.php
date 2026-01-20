<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class UserPurchaserUnit extends Model
{
    protected $fillable = [
      'user_id','user_info_id','property_id','building_id','unit_id','card_no','role_id','primary_contact','status','receive_call'
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

    public function activeunit($id){
      $purchaserObj = UserPurchaserUnit::find($id);
      $userObj = User::where('id',$purchaserObj->user_id)->where('unit_no',$purchaserObj->unit_id)->first();
      if(isset($userObj)){
        return 1;
      }else{
        return 0;
      }

    }

}
