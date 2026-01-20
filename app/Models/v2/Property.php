<?php

namespace App\Models\v2;
use Session;
use DateTime;


use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $table = "properties";
    
    protected $fillable = [
        'company_name','short_code','company_email','company_contact','company_address','management_company_name','management_company_addr','invoice_notes','mcst_code','third_party_option','manager_push_notification','enquiry_email','enquiry_contact','otp_option','qrcode_option','opn_secret_key','opn_password','sms_username','sms_password','security_option','company_logo','default_bg','inspection_bg','announcement_bg','defect_bg','faq_bg','feedback_bg','facilities_bg','takeover_bg','condodocs_bg','resident_fileupload_bg','visitor_management_bg','facial_reg_bg','takeover_timing','takeover_availability_start','inspection_timing','inspection_availability_start','takeover_blockout_days','inspection_blockout_days','takeover_notes','visitor_limit','public_holidays','visitors_allowed','inspection_notes','due_date','status'
    ];

    public function permissions(){
        return $this->hasMany('App\Models\v2\PropertyPermission','property_id');
    }
    public function dashboard_permissions(){
        return $this->hasMany('App\Models\v2\PropertyDashboardPermission','property_id');
    }

    public function check_property_permission($menu,$property,$rights){
       
        if($rights ==1) //view 
         {
             $records = PropertyPermission::where('property_id',$property)->where('module_id',$menu)->where('view',1)->first();
         }
         if($rights ==2) //Create 
         {
             $records = PropertyPermission::where('property_id',$property)->where('module_id',$menu)->where('create',1)->first();
         }
         if($rights ==3) //Edit 
         {
             $records = PropertyPermission::where('property_id',$property)->where('module_id',$menu)->where('edit',1)->first();
         }
         if($rights ==4) //delete 
         {
             $records = PropertyPermission::where('property_id',$property)->where('module_id',$menu)->where('delete',1)->first();
         }
 
         // $records;
 
        return $records;
 
     }

     public function property_add_api($token,$uuid,$name) {

        $url = env('THINMOO_API_URL')."sqCommunity/extapiAdmin/add";

        //The data you want to send via POST
        $fields = [
            'accessToken'       => $token,
            'uuid'              => $uuid,
            'name'              => $name,
            'cityCode'          => 'SGP-O',
            'defDeptName'       =>"Managing Agent",
            'defDeptUuid'       =>3,

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

public function property_modify_api($token,$uuid,$name) {

    $url = env('THINMOO_API_URL')."sqCommunity/extapiAdmin/update";

    //The data you want to send via POST
    $fields = [
        'accessToken'      => $token,
        'uuid'              => $uuid,
        'name'              => $name
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

public function property_check_record($token,$name) {

    $url = env('THINMOO_API_URL')."sqCommunity/extapiAdmin/list";

    //The data you want to send via POST
    $fields = [
        'accessToken'       => $token,
        'name'  => $name,
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

public function property_get_api($token,$uuid) {

    $url = env('THINMOO_API_URL')."sqCommunity/extapiAdmin/get";

    //The data you want to send via POST
    $fields = [
        'accessToken'      => $token,
        'uuid'              => $uuid
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




public function thinmoo_auth_api() {

    $url = env('THINMOO_API_URL')."platCompany/extapi/getAccessToken";

    //The data you want to send via POST
    $fields = [
        'appId'      => env('APPID'),
        'appSecret'   => env('APP_SECRET')
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

    //return response()->json_decode($result);
    //var_dump($json);
    //echo $json['data']['accessToken'];

    if(isset($json['data']['accessToken']) && $json['data']['accessToken'] !=''){
        Session::put('thinmoo_acess_tocken', $json['data']['accessToken']);
        //session()->put('thinmoo_acess_tocken', $json['data']['accessToken']);
        }

    return $json['data']['accessToken'];



}
 
public function CheckOverDue($id){
    $prop = Property::where('id',$id)->first();

    $due_date = new DateTime($prop->due_date);
    $today = new DateTime(date("y-m-d"));

    if($today > $due_date){
        $days = $today->diff($due_date)->format("%a"); 
        $result = "<font color='red'>".$days ." Day(s) Over Due</font>";
    }   
    else{
        $days = $due_date->diff($today)->format("%a"); 
        if($days<=60)
            $result = "<font color='orange'>".$days ." Day(s) Left</font>";
        else
            $result = $days ." Day(s) Remaining";
    }

        return $result;

}

}
