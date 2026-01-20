<?php

namespace App\Models\v2;
use App\Models\v2\Building;
use App\Models\v2\Unit;
use Illuminate\Database\Eloquent\Model;

class UserMoreInfo extends Model
{
    protected $fillable = [
      'user_id','account_id','profile_picture','profile_picture_base64','receive_device_cal','face_picture','face_picture_base64','unit_no','first_name','last_name','card_no', 'phone','mailing_address','country','postal_code','company_name','status','deactivated_date','faceid_access_permission','faceid_access_code'
    ];

    public function getunit(){
        return $this->belongsTo('App\Models\v2\Unit','unit_no');
    }
    public function usercountry(){
        return $this->belongsTo('App\Models\v2\Country','country');
    }

    public function employmentstaus(){
        return $this->belongsTo('App\Models\v2\EmploymentType','employement_status');
    }

    public function getuser(){
        return $this->belongsTo('App\Models\v2\User','user_id');
    }
    public function moreinfofields($user,$property){
       
        $records = UserMoreInfo::where('user_id', $user)
        ->where('account_id', $property)
        ->first();
        return $records;
    }

    public function moreunitinfo($user,$property,$unit=''){
        $units =array();
        if($unit !=''){     
            $unitObj = Unit::select('id')->where('account_id',$property)->where(function ($query) use ($unit) {
            if($unit !='')
                $query->Where('unit', $unit);
            })->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    $units[] = $unitid->id;
                }
            }
        }

        $records = UserPurchaserUnit::where(function ($query) use ($user,$property,$unit,$units) {
           $query->where('user_info_id', $user)->where('property_id', $property);
           if($unit !='')
            $query->whereIn('unit_id', $units);
        })->first();
        return $records; 
        //return $this->hasOne('App\Models\v2\UserPurchaserUnit','user_info_id')->where('account_id',$this->account_id);;

       
    }
    public function userunitinfo($user,$unit){
       
        $records = UserPurchaserUnit::where('user_info_id', $user)
        ->where('unit_id', $unit)
        ->first();
        return $records; 
        //return $this->hasOne('App\Models\v2\UserPurchaserUnit','user_info_id')->where('account_id',$this->account_id);;

       
    }
    public function buildinginfo($id){
       
        $records = Building::where('id',$id)
        ->first();
        return $records; 
        //return $this->hasOne('App\Models\v2\UserPurchaserUnit','user_info_id')->where('account_id',$this->account_id);;
    }
    public function unitInfo($id){
        $records = Unit::where('id',$id)
        ->first();
        return $records; 
        //return $this->hasOne('App\Models\v2\UserPurchaserUnit','user_info_id')->where('account_id',$this->account_id);;
    }

    public function roleInfo($id){

        $records = Role::where('id',$id)
        ->first();
        return $records; 
        //return $this->hasOne('App\Models\v2\UserPurchaserUnit','user_info_id')->where('account_id',$this->account_id);;

       
    }
    


}
