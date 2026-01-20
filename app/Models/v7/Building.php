<?php

namespace App\Models\v7;
use Session;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'buildings';
    protected $fillable = [
        'account_id','building','building_no'
    ];


    public function propertyinfo(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }

    public function unites(){
        return $this->hasMany('App\Models\v7\Unit','building_id');
    }

    public function building_check_record($token,$building) {

        $url = env('THINMOO_API_URL')."/sqBuilding/extapi/get";
    
        //The data you want to send via POST
        $fields = [
            'accessToken'       => $token,
            'extCommunityUuid'  => $building->account_id,
            'uuid'              => $building->id
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

    public function building_add_api($token,$building) {

        $url = env('THINMOO_API_URL')."sqBuilding/extapi/add";

        //The data you want to send via POST
        $fields = [
            'accessToken'           =>  $token,
            'extCommunityUuid'      =>  $building->account_id,
            'name'                  =>  $building->building,
            'uuid'                  =>  $building->id,
            'code'                  =>  $building->building_no

        ];

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


    public function building_update_api($token,$building) {

        $url = env('THINMOO_API_URL')."sqBuilding/extapi/update";

        $fields = [
            'accessToken'           =>  $token,
            'extCommunityUuid'      =>  $building->account_id, 
            'name'                  =>  $building->building,
            'code'                  =>  $building->building_no,
            'uuid'                  =>  $building->id
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

    public function building_delete_api($token,$building) {

        $url = env('THINMOO_API_URL')."/sqBuilding/extapi/delete";

        //The data you want to send via POST

        $fields = [
            'accessToken'               => $token,
            'extCommunityUuid'          => $building->account_id,
            'uuids'                     => $building->id
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

    public function building_bulkadd_api($token,$buildings,$property) {

        $url = env('THINMOO_API_URL')."sqBuilding/extapi/saveBatch?accessToken=".$token."&extCommunityUuid=".$property;
       
        //The data you want to send via POST
        $fields = [
            'BuildingList'          =>  $buildings
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
        //print_r($result);
        //exit;
        

}

   
}
