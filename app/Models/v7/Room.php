<?php

namespace App\Models\v7;
use Session;


use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = "devices";
    
    protected $fillable = [
        'account_id','room_name','room_code','device_id','status'
    ];

    public function room_check_record($token,$device) {

        $url = env('THINMOO_API_URL')."sqRoom/extapi/get";
    
        //The data you want to send via POST
        $fields = [
            'accessToken'       => $token,
            'extCommunityUuid'  => $device->account_id,
            'devSn'              => $device->room_serial_no
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

    public function room_add_api($token,$device) {

        $url = env('THINMOO_API_URL')."sqRoom/extapi/add";

        //The data you want to send via POST
        $fields = [
            'accessToken'           =>  $token,
            'extCommunityUuid'      =>  $device->account_id,
            'name'                  =>  $device->room_name,
            'devSn'                 =>  $device->room_serial_no,
            'positionType'          =>  1,
            'positionId'            =>  1

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
        echo $result;

}


public function room_update_api($token,$device) {

    $url = env('THINMOO_API_URL')."/sqRoom/extapi/update";

    //The data you want to send via POST
    $name = $user->name." ".$userinfo->last_name;

    $fields = [
        'accessToken'       => $token,
        'extCommunityUuid'  => $user->account_id,
        'name'              => $name,
        'uuid'              => $user->id,
        'phone'              => $user->phone
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

public function room_delete_api($token,$user) {

    $url = env('THINMOO_API_URL')."sqRoom/extapi/delete";

    //The data you want to send via POST

    $fields = [
        'accessToken'               => $token,
        'extCommunityUuid'          => $user->account_id,
        'uuids'                     => $user->id
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

public function room_count_api($token,$account_id) {

    $url = env('THINMOO_API_URL')."sqRoom/extapi/list";
    $fields = [
        'accessToken'           =>  $token,
        'extCommunityUuid'      =>  $account_id
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
    $count = '';
    if(isset($json['data'])){
        $count = $json['data']['totalCount'];
    }

    return $count;


}
 

}
