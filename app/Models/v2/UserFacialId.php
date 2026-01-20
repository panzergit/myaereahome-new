<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class UserFacialId extends Model
{
    protected $fillable = [
      'user_id','option_id','account_id','unit_no','status','others','thinmoo_id','thinmoo_uuid','face_picture','face_picture_base64','reason'
    ]; 

    public function user(){
        return $this->belongsTo('App\Models\v2\User','user_id');
    }

    public function optionname(){
        return $this->belongsTo('App\Models\v2\FacialRecoOption','option_id');
    }
    public function getunit(){
        return $this->belongsTo('App\Models\v2\Unit','unit_no');
      }

public function faceImage_api($token,$user,$facial) {

    $url = env('THINMOO_API_URL')."/persEmpHousehold/extapi/update";
    
    $name = $user->name." ".$user->userinfo->last_name;
   

    $fields = [
        'accessToken'               => $token,
        'extCommunityUuid'          => $user->account_id,
        'uuid'                      => $user->id,
        'faceFileBase64Array'       => $facial->face_picture_base64,
        'name'                      => $name

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

public function faceImage_add_api($token,$user,$facial) {

    $url = env('THINMOO_API_URL')."persEmpHousehold/extapi/add";

    $name = $user->name." ".$user->userinfo->last_name;

    //The data you want to send via POST
    $fields = [
        'accessToken'               => $token,
        'extCommunityUuid'          => $user->account_id,
        'name'                      => $name,
        'faceFileBase64Array'       => $facial->face_picture_base64,
        'uuid'                      => $user->id
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


public function faceImage_delete_api($token,$user,$facial) {

    $url = env('THINMOO_API_URL')."persEmpHousehold/extapi/update";

    $name = $user->name." ".$user->userinfo->last_name;

  

    $fields = [
        'accessToken'               => $token,
        'extCommunityUuid'          => $user->account_id,
        'name'                      => $name,
        'uuid'                      => $user->id,
        'removeFaceImageIds'        => $facial->thinmoo_id
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


public function faceImage_emp_api($token,$user,$facial) {

    $url = env('THINMOO_API_URL')."wyEmpProperty/extapi/update";
    
    $name = $user->name;
   

    $fields = [
        'accessToken'               => $token,
        'extCommunityUuid'          => $user->account_id,
        'uuid'                      => $user->id,
        'faceFileBase64Array'       => $facial->face_picture_base64,
        'name'                      => $name,
        'deptUuid'                  => $user->role_id

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

public function faceImage_add_emp_api($token,$user,$facial) {

    $url = env('THINMOO_API_URL')."wyEmpProperty/extapi/add";

    $name = $user->name;

    //The data you want to send via POST
    $fields = [
        'accessToken'               => $token,
        'extCommunityUuid'          => $user->account_id,
        'name'                      => $name,
        'faceFileBase64Array'       => $facial->face_picture_base64,
        'uuid'                      => $user->id,
        'deptUuid'                  => $user->role_id
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


public function faceImage_delete_emp_api($token,$user,$facial) {

    $url = env('THINMOO_API_URL')."wyEmpProperty/extapi/update";

    $name = $user->name;

  

    $fields = [
        'accessToken'               => $token,
        'extCommunityUuid'          => $user->account_id,
        'name'                      => $name,
        'uuid'                      => $user->id,
        'removeFaceImageIds'        => $facial->thinmoo_id,
        'deptUuid'                  => $user->role_id

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

}
