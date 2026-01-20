<?php

namespace App\Models\v7;
use Session;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
class Unit extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'unites_new';
    protected $fillable = [
        'account_id','building_id','unit','code','unit_name','unit_code','size','share_amount','encrypted','test_field'
    ];

    public function takeover_appt(){
        return $this->hasOne('App\Models\v7\UnittakeoverAppointment','unit_no','id')->where('status','=', 1)->orwhere('status','=', 2);
    }

    public function propertyinfo(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }

    public function buildinginfo(){
        return $this->belongsTo('App\Models\v7\Building','building_id');
    }

    public function unit_info($user,$unit,$building ='',$account_id='',$userinfo_id=''){
        $unit = Crypt::encryptString($unit);
        $unit_lists = Unit::select('id')->where(function ($query) use ($building,$unit) {
            if($building !='')
                $query->where('building_id',$building);
            if($unit !='')
                $query->Where('unit', $unit);
        })->get();
        $units= array();
        if(isset($unit_lists)){
            foreach($unit_lists as $unitid){
                $units[] = $unitid->id;
            }
        }
        $record = UserPurchaserUnit::where('user_id',$user)->where('user_info_id',$userinfo_id)->where(function ($subquery) use ($units,$building,$account_id) {
            if($building !='')
                $subquery->where('building_id', $building);
            if($account_id !='')
                $subquery->where('property_id', $account_id);
            if(count($units)>0)
                $subquery->WhereIn('unit_id', $units);
        })->where('status',1)->orderby('id','desc')->first();
        return $record;
    }

    public function unit_check_record($token,$unit) {

        $url = env('THINMOO_API_URL')."sqRoom/extapi/get";
    
        //The data you want to send via POST
        $fields = [
            'accessToken'       => $token,
            'extCommunityUuid'  => $unit->account_id,
            'uuid'              => $unit->id
        ];
    
    
        $fields_string = http_build_query($fields);
    
        $ch = curl_init();
    
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
    
        
        $result = curl_exec($ch);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        return $json;
    
    }

    public function unit_add_api($token,$unit) {

        $url = env('THINMOO_API_URL')."sqRoom/extapi/add";

        //print_r($unit);
        //The data you want to send via POST
        $unit_code = Crypt::decryptString($unit->code);
        $unit_code = (int)$unit_code;
        $fields = [
            'accessToken'           =>  $token,
            'extCommunityUuid'      =>  $unit->account_id,
            'buildingUuid'          =>  $unit->building_id,
            'name'                  =>  Crypt::decryptString($unit->unit),
            'uuid'                  =>  $unit->id,
            'code'                  =>  $unit_code,

        ];

        //print_r( $fields);


        $fields_string = http_build_query($fields);

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

        
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        //echo $result;

}


    public function unit_update_api($token,$unit) {

        $url = env('THINMOO_API_URL')."sqRoom/extapi/update";
        $unit_code = Crypt::decryptString($unit->code);
        $unit_code = (int)$unit_code;
        $fields = [
            'accessToken'           =>  $token,
            'extCommunityUuid'      =>  $unit->account_id, 
            'buildingUuid'          =>  $unit->building_id,
            'name'                  =>  Crypt::decryptString($unit->unit),
            'code'                  =>  $unit_code,
            'uuid'                  =>  $unit->id
        ];


        $fields_string = http_build_query($fields);

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

        
        $result = curl_exec($ch);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        //return $json;


    }

    public function unit_delete_api($token,$unit) {

        $url = env('THINMOO_API_URL')."sqRoom/extapi/delete";

        //The data you want to send via POST

        $fields = [
            'accessToken'               => $token,
            'extCommunityUuid'          => $unit->account_id,
            'uuids'                     => $unit->id
        ];

        $fields_string = http_build_query($fields);

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

        
        $result = curl_exec($ch);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        //return $json;


    }

    public function unit_bulkadd_api($token,$units,$property) {

        $url = env('THINMOO_API_URL')."sqRoom/extapi/saveBatchRooms?accessToken=".$token."&extCommunityUuid=".$property;

       
        //The data you want to send via POST
        $fields = [
            'RoomList'          =>  $units
        ];
        $fields_string = json_encode($fields);
        //print_r($fields_string);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        print_r($result);
        //exit;
        

}
    public function search(Request $request)
    {
        return Unit::search($request->input('unit'))->get();
    }

    public function getDecryptedUnit(): string
    {
        return Crypt::decryptString($this->unit);
    }

    public function licenseplates(){
        return $this->hasMany('App\Models\v7\UserLicensePlate','unit_id');
    }
   
}
