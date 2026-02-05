<?php

namespace App\Models\v7;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\v7\Building;
use App\Models\v7\Unit;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\Role;
use App\Traits\Syncable;

use Illuminate\Database\Eloquent\Model;

class UserMoreInfo extends Model
{
    use Syncable;
    
    protected $table = 'user_more_infos_new';

    protected $fillable = [
      'user_id','account_id','profile_picture','profile_picture_base64','receive_device_cal','face_picture','face_picture_base64','unit_no','first_name','last_name','card_no', 'phone','mailing_address','country','postal_code','company_name','status','deactivated_date','faceid_access_permission','faceid_access_code','encrypted','contract_file'
    ];

    public function getunit(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
    }
    public function usercountry(){
        return $this->belongsTo('App\Models\v7\Country','country');
    }

    public function employmentstaus(){
        return $this->belongsTo('App\Models\v7\EmploymentType','employement_status');
    }

    public function getproperty(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }

    public function getuser(){
        return $this->belongsTo('App\Models\v7\User','user_id');
    }
    public function moreinfofields($user,$property){
       
        $records = UserMoreInfo::where('user_id', $user)
        ->where('account_id', $property)
        ->orderby('id','desc')->first();
        return $records;
    }

    public function moreunitinfo($user,$property,$unit=''){
        $units =array();
        if($unit !=''){     
            $unitObj = Unit::select('id','unit')->where('account_id',$property)->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    if($unit !='' && Crypt::decryptString($unitid->unit) ===$unit)
                        $units[] = $unitid->id;
                }
            }
        }

        $records = UserPurchaserUnit::where(function ($query) use ($user,$property,$unit,$units) {
           $query->where('user_info_id', $user)->where('property_id', $property);
           if($unit !='')
            $query->whereIn('unit_id', $units);
        })->orderby('id','desc')->first();
        return $records; 
        //return $this->hasOne('App\Models\v7\UserPurchaserUnit','user_info_id')->where('account_id',$this->account_id);;

       
    }
    public function userunitinfo($user,$unit){
       
        $records = UserPurchaserUnit::where('user_info_id', $user)
        ->where('unit_id', $unit)
        ->orderby('id','desc')->first();
        return $records; 
        //return $this->hasOne('App\Models\v7\UserPurchaserUnit','user_info_id')->where('account_id',$this->account_id);;

       
    }
    public function buildinginfo($id){
       
        $records = Building::where('id',$id)
        ->first();
        return $records; 
        //return $this->hasOne('App\Models\v7\UserPurchaserUnit','user_info_id')->where('account_id',$this->account_id);;
    }
    public function unitInfo($id){
        $records = Unit::where('id',$id)
        ->first();
        return $records; 
        //return $this->hasOne('App\Models\v7\UserPurchaserUnit','user_info_id')->where('account_id',$this->account_id);;
    }

    public function roleInfo($id){

        $records = Role::where('id',$id)
        ->first();
        return $records; 
        //return $this->hasOne('App\Models\v7\UserPurchaserUnit','user_info_id')->where('account_id',$this->account_id);;

       
    }

    public function licenseplates(){
        return $this->hasMany('App\Models\v7\UserLicensePlate','user_info_id');
    }
    


}
