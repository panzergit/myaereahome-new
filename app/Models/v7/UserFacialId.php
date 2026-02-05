<?php

namespace App\Models\v7;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;

class UserFacialId extends Model
{
    use Syncable;
    
    protected $fillable = [
      'user_id','option_id','account_id','unit_no','status','others','thinmoo_id','thinmoo_uuid','face_picture','face_picture_base64','reason'
    ]; 

    public function user(){
        return $this->belongsTo('App\Models\v7\User','user_id');
    }

    public function optionname(){
        return $this->belongsTo('App\Models\v7\FacialRecoOption','option_id');
    }
    public function getunit(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
      }

public function faceImage_api($token,$user,$facial,$account_id='') {

    $url = env('THINMOO_API_URL')."/persEmpHousehold/extapi/update";
    
    $name =  Crypt::decryptString($user->userinfo->first_name)." ".Crypt::decryptString($user->userinfo->last_name);
    //echo $name = $user->name." ".$user->userinfo->last_name;
    
    $active_account_id = $user->account_id;
    if($account_id >0)
        $active_account_id =  $account_id;

    $fields = [
        'accessToken'               => $token,
        'extCommunityUuid'          => $active_account_id,
        'uuid'                      => $user->id,
        'faceFileBase64Array'       => $facial->face_picture_base64,
        'name'                      => $name

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
    //print_r($result);
    //exit;
    return $json;


}

public function faceImage_add_api($token,$user,$facial,$account_id='') {

    $url = env('THINMOO_API_URL')."persEmpHousehold/extapi/add";

    $name =  Crypt::decryptString($user->userinfo->first_name)." ".Crypt::decryptString($user->userinfo->last_name);

    //$name = $user->name." ".$user->userinfo->last_name;
    $active_account_id = $user->account_id;
    if($account_id >0)
        $active_account_id =  $account_id;
    //The data you want to send via POST
    $fields = [
        'accessToken'               => $token,
        'extCommunityUuid'          => $active_account_id,
        'name'                      => $name,
        'faceFileBase64Array'       => $facial->face_picture_base64,
        'uuid'                      => $user->id
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


public function faceImage_delete_api($token,$user,$facial,$account_id='') {

    $url = env('THINMOO_API_URL')."persEmpHousehold/extapi/update";

    $name =  Crypt::decryptString($user->userinfo->first_name)." ".Crypt::decryptString($user->userinfo->last_name);

    //$name = $user->name." ".$user->userinfo->last_name;

    $active_account_id = $user->account_id;
    if($account_id >0)
        $active_account_id =  $account_id;

    $fields = [
        'accessToken'               => $token,
        'extCommunityUuid'          => $active_account_id,
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
    
    //$name =  Crypt::decryptString($user->name);

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

    //$name =  Crypt::decryptString($user->name);

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

    $name =  Crypt::decryptString($user->name);


  

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

    public function correctImageOrientation($filename)
    {
        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($filename);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if ($orientation != 1) {
                    $img = imagecreatefromjpeg($filename);
                    $deg = 0;
                    switch ($orientation) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }
                    if ($deg) {
                        $img = imagerotate($img, $deg, 0);
                    }
                    // then rewrite the rotated image back to the disk as $filename
                    imagejpeg($img, $filename->getPath() . DIRECTORY_SEPARATOR . $filename->getFilename(), 100);
                } // if there is some rotation necessary
            } // if have the exif orientation info
        } // if function exists
    }

}
