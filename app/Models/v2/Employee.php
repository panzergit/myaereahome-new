<?php

namespace App\Models\v2;
use Session;


use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = "employees";
    
    protected $fillable = [
        'account_id','name','uuid','status','emp_type'
    ];
    

     public function employee_add_api($token,$result,$department) {

        $url = env('THINMOO_API_URL')."wyEmpProperty/extapi/add";

        $name = $result->name." Employee";
        //The data you want to send via POST
        $fields = [
            'accessToken'           => $token,
            'extCommunityUuid'      => $result->account_id,
            'uuid'                  => $result->id,
            'name'                  => $result->name,
            'deptUuid'              => $department
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

public function employee_modify_api($token,$user,$department) {

    $url = env('THINMOO_API_URL')."wyEmpProperty/extapi/update";

    //The data you want to send via POST
    $fields = [
        'accessToken'           => $token,
        'extCommunityUuid'      => $user->account_id,
        'uuid'                  => $user->id,
        'name'                  => $user->name,
        'deptUuid'              => $department
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

public function employee_check_record($token,$user) {

    $url = env('THINMOO_API_URL')."wyEmpProperty/extapi/get";

    
    $fields = [
        'accessToken'      => $token,
        'extCommunityUuid' => $user->account_id,
        'uuid'             =>$user->id,
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

public function employee_delete_record($token,$account_id,$id) {

    $url = env('THINMOO_API_URL')."wyEmpProperty/extapi/delete";

    $fields = [
        'accessToken'      => $token,
        'extCommunityUuid' => $account_id,
        'uuids'             => $id,
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

public function employee_activate_api($token,$user,$department) {

    $url = env('THINMOO_API_URL')."/wyEmpProperty/extapi/update";

    //The data you want to send via POST
    $startdate = date("Y-m-d H:i:s",strtotime($user->created_at));
    $enddate =  date("Y-m-d H:i:s",strtotime($user->updated_at));
    $fields = [
        'accessToken'           => $token,
        'extCommunityUuid'      => $user->account_id,
        'uuid'                  => $user->id,
        'name'                  => $user->name,
        'deptUuid'              => $department,
        'disableDevice'         =>'0',
        'accStartdatetime'      => '',
        'accEnddatetime'        => ''
    ];

    //print_r($fields);

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

public function employee_deactivate_api($token,$user,$department) {

    $url = env('THINMOO_API_URL')."/wyEmpProperty/extapi/update";

    //The data you want to send via POST
    $name = $user->name;
    $startdate = date("Y-m-d H:i:s",strtotime($user->created_at));
    $enddate =  date("Y-m-d H:i:s",strtotime($user->updated_at));
    $fields = [
        'accessToken'           => $token,
        'extCommunityUuid'      => $user->account_id,
        'uuid'                  => $user->id,
        'name'                  => $user->name,
        'deptUuid'              => $department,
        'disableDevice'         =>'1',
        'accStartdatetime'      => $startdate,
        'accEnddatetime'        => $enddate
    ];

    //print_r($fields);

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


 

}
