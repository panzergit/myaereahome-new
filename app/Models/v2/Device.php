<?php

namespace App\Models\v2;
use Session;


use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = "devices";
    
    protected $fillable = [
        'account_id','device_name','device_serial_no','locations','model','position_type','position_type','proximity_setting','status'
    ];

    public function buildinginfo(){
        return $this->belongsTo('App\Models\v2\Building','locations');
    }
    public function propertyinfo(){
        return $this->belongsTo('App\Models\v2\Property','account_id');
    }

    public function device_status($token,$comunity,$serialno) {
        $url = env('THINMOO_API_URL')."/devDevice/extapi/get";

       
            //The data you want to send via POST
            $fields = [
                'accessToken'       => $token,
                'extCommunityUuid'  => $comunity,
                'devSn'             => $serialno
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
            
           
            if(isset($json['data']))
                return $json['data'];
            else
                return 0;
    }
        public function device_check_record($token,$device) {

            $url = env('THINMOO_API_URL')."/devDevice/extapi/get";
        
            //The data you want to send via POST
            $fields = [
                'accessToken'       => $token,
                'extCommunityUuid'  => $device->account_id,
                'devSn'             => $device->device_serial_no,
                'uuid'              => $device->id
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

        public function device_add_api($token,$device) {

            $url = env('THINMOO_API_URL')."devDevice/extapi/add";

            //The data you want to send via POST
            $fields = [
                'accessToken'           =>  $token,
                'extCommunityUuid'      =>  $device->account_id,
                'name'                  =>  $device->device_name,
                'devSn'                 =>  $device->device_serial_no,
                'positionType'          =>  1, // actually it was 0 
                'uuid'                  =>  $device->id,
                'accInoutStatus'        =>  1,
                'deviceModelname'       =>  $device->model,
                'positionUuids'         =>  $device->locations

            ];

            //print_r($fields);

            $fields_string = http_build_query($fields);

            $ch = curl_init();

            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

            
            $result = curl_exec($ch);
            $err = curl_error($ch);
            $json = json_decode($result,true);
            curl_close($ch);
            return $json;

    }


    public function device_update_api($token,$device) {

        $url = env('THINMOO_API_URL')."/devDevice/extapi/update";

        

        $fields = [
            'accessToken'           =>  $token,
            'extCommunityUuid'      =>  $device->account_id, 
            'name'                  =>  $device->device_name,
            'devSn'                 =>  $device->device_serial_no,
            'positionType'          =>  1,
            'uuid'                  =>  $device->id,
            'accInoutStatus'        =>  1,
            'deviceModelname'       =>  $device->model,
            'positionUuid'         =>  $device->locations
        ];

        //print_r($fields);
        $fields_string = http_build_query($fields);
        // print_r($fields_string);

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
        //exit;


    }

    public function device_delete_api($token,$device) {

        $url = env('THINMOO_API_URL')."devDevice/extapi/delete";

        //The data you want to send via POST

        $fields = [
            'accessToken'               => $token,
            'extCommunityUuid'          => $device->account_id,
            'uuids'                     => $device->id
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

    public function device_restart($token,$device) {

        $url = env('THINMOO_API_URL')."devDevice/extapi/restart";
    
        //The data you want to send via POST
        $fields = [
            'accessToken'       => $token,
            'extCommunityUuid'  => $device->account_id,
            'devSns'             => $device->device_serial_no,
            'uuids'              => $device->id
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
       // return $json;
    
    }

    public function device_lists_api($token,$account_id) {

        $url = env('THINMOO_API_URL')."devDevice/extapi/list";
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
        $lists = array();
        if(isset($json['data'])){
            $lists = $json['data']['list'];
        }

        return $lists;


    }

    public function device_count_api($token,$account_id) {

        $url = env('THINMOO_API_URL')."devDevice/extapi/list";
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
