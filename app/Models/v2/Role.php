<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

   // protected $table = 'unites';
    protected $fillable = [
        'account_id','name',
    ];


    public function Permissions(){
        return $this->hasMany('App\Models\v2\ModuleSetting','role_id');
    }

    public function propertyinfo(){
        return $this->belongsTo('App\Models\v2\Property','account_id');
    }

    public function roledevices(){
        return $this->hasMany('App\Models\v2\RoleDevice','role_id');
    }

    public function roleremotedevices(){
        return $this->hasMany('App\Models\v2\RoleRemoteDevice','role_id');
    }

    public function role_add_api($token,$account_id='', $result='') {

        $url = env('THINMOO_API_URL')."/wyDept/extapi/add";

       /* echo " token".$token;
        echo " account_id".$account_id;
        echo " uuid".$result->id;
        echo " name".$result->name;
        echo " parentUuid".$result->parentUuid;
        exit;*/
        
        $fields = [
            'accessToken'           => $token,
            'extCommunityUuid'      => $account_id,
            'uuid'                  => $result->id,
            'name'                  => $result->name,
            'parentUuid'            => $result->parentUuid
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

public function role_modify_api($token,$roleobj) {

    $url = env('THINMOO_API_URL')."/wyDept/extapi/update";

    //The data you want to send via POST
    $fields = [
        'accessToken'           => $token,
        'extCommunityUuid'      => $roleobj->account_id,
        'uuid'                  => $roleobj->id,
        'name'                  => $roleobj->name,
        'parentUuid'            =>3
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

public function role_check_record($token,$account_id,$uuid) {

    $url = env('THINMOO_API_URL')."wyDept/extapi/get";

    //print_r($id);
    //The data you want to send via POST
   
    $fields = [
        'accessToken'      => $token,
        'extCommunityUuid' => $account_id,
        'uuid'             =>$uuid
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

public function role_delete_api($token,$role) {

    $url = env('THINMOO_API_URL')."/wyDept/extapi/delete";

    
    $fields = [
        'accessToken'           =>   $token,
        'extCommunityUuid'      =>   $role->account_id,
        'uuids'                 =>   $role->id,
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

public function role_access_api($token,$role,$devices) {

    $url = env('THINMOO_API_URL')."wyDept/extapi/saveDeptPermission";
    
    $fields = [
        'accessToken'                   =>   $token,
        'extCommunityUuid'              =>   $role->account_id,
        'uuid'                          =>   $role->id,
        'permCallAnswerAuthorize'       =>   1,
        'permDoorAuthorize'             =>   2,
        'coverAuthorizationDevSns'      =>  $devices
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
