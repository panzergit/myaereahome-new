<?php

namespace App\Models\v2;
use Session;
use App\Models\v2\User;
use Auth;
use App\Models\v2\Notification;
use App\Models\v2\AnnouncementDetails;
use App\Models\v2\UnittakeoverAppointment;
use App\Models\v2\JoininspectionAppointment;
use App\Models\v2\Defect;
use App\Models\v2\FeedbackSubmission;
use App\Models\v2\FacilityBooking;
use App\Models\v2\UserFacialId;
use App\Models\v2\UserProperty;
use App\Models\v2\FinanceInvoice;
use App\Models\v2\FinanceInvoiceInfo;


use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id','name','user_info_id','building_no','unit_no', 'email', 'password','role_id','primary_contact', 'account_enabled','deactivated_date','version','app_version'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role(){
        return $this->belongsTo('App\Models\v2\Role','role_id');
    }

    public function getunit(){
        return $this->belongsTo('App\Models\v2\Unit','unit_no');
    }

    public function buildinginfo(){
        return $this->belongsTo('App\Models\v2\Building','building_no');
    }

    public function userinfo($account_id = ''){
        //$account_id = Auth::user()->account_id;
        if($account_id >0)
            return $this->hasOne('App\Models\v2\UserMoreInfo','user_id')->where('account_id',$account_id);
        else
            return $this->hasOne('App\Models\v2\UserMoreInfo','user_id');


    }
    public function userinfo_fromadmin(){
        $account_id = Auth::user()->account_id;
        return $this->hasOne('App\Models\v2\UserMoreInfo','user_id')->where('account_id',$account_id);;
    }

    public function empinfo(){
        return $this->hasOne('App\Models\v2\UserMoreInfo','user_id');
    }

    public function faceids(){
        return $this->hasMany('App\Models\v2\UserFacialId','user_id')->where('option_id','!=',1);
    }

    public function appfaceids(){
        return $this->hasMany('App\Models\v2\UserFacialId','user_id');
    }

    public function propertyinfo(){
        return $this->belongsTo('App\Models\v2\Property','account_id');
    }

    public function userdevices(){
        return $this->hasMany('App\Models\v2\UserDevice','user_id');
    }

    public function userremotedevices(){
        return $this->hasMany('App\Models\v2\UserRemoteDevice','user_id');
    }

    public function userproperties(){
        return $this->hasMany('App\Models\v2\UserProperty','user_id');
    }

    public function propdropdown($id){
        $props = UserProperty::select('properties.id','properties.company_name','properties.company_logo')->where('user_properties.user_id',$id)->join('properties', 'user_properties.property_id', '=', 'properties.id')
                    ->orderby('properties.company_name','asc')->get();
       return $props;

    }



    public function noOfAnnouncement($emp){
       
        $records = AnnouncementDetail::where('user_id', $emp)
        ->where('view_status', 0)
        ->count();
        return $records;

    }

      public function noOfTakeover($user){
       
        $records = UnittakeoverAppointment::where('notification_status', 0)
        ->whereDate('appt_date', '>=', Carbon::now('Asia/Singapore')) 
        ->where('account_id', $user)
        ->where('status', 0)
        ->count();
        return $records;

    }

    public function noOfInspection($user){
       
        $records = JoininspectionAppointment::where('notification_status', 0)
        ->whereDate('appt_date', '>=', Carbon::now('Asia/Singapore')) 
        ->where('account_id', $user)
        ->where('status', 0)
        ->count();
        return $records;

    }

    public function noOfNotification($type,$emp){
        \Session::put('country', 'KH');
        $records = Notification::where('emp_id', $emp)
        ->where('type', $type)
        ->where('status', 1)
        ->where('notification', 1)
        ->count();
        return $records;

    }

    public function noOfMyNotification($type,$emp){
        \Session::put('country', 'KH');
        $records = Notification::where('emp_id', $emp)
        ->where('type', $type)
        ->where('notification', 2)
        ->where('status', 1)
        ->count();
        return $records;

    }

    public function noOfDefects($user){
        $date = Carbon::now()->subDays(7);
        /*$records = Defect::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)->where('view_status',0)->where('created_at', '>=', $date)
        ->count();*/
        $records = Defect::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)->where('view_status',0)
        ->count();
        return $records;

    }

    public function noOfFeedback($user){
        $date = Carbon::now()->subDays(7);
        /*$records = FeedbackSubmission::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)->where('created_at', '>=', $date)
        ->count();*/
        $records = FeedbackSubmission::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)
        ->count();
        return $records;

    }

    public function noOfFacilityBooking($user){
        $records = FacilityBooking::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)
        ->count();
        return $records;

    }

    public function noOfFileupload($user){
        /*$records = ResidentFileSubmission::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)
        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->count();*/
        $records = ResidentFileSubmission::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)
        ->count();
        return $records;

    }

    public function noOfVisitors($user){
        $records = VisitorBooking::where('account_id', $user)
        ->where('view_status',0)->where('status',0)
        ->count();
        return $records;

    }

    public function noOfMovinginout($user){
        $records = EformMovingInOut::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)
        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->count();
        return $records;

    }

    public function noOfRenovation($user){
        $records = EformRenovation::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)
        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->count();
        return $records;

    }

    public function noOfRegvehicle($user){
        $records = EformRegVehicle::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)
        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->count();
        return $records;

    }

    public function noOfDooraccess($user){
        $records = EformDoorAccess::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)
        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->count();
        return $records;

    }

    public function noOfMailing($user){
        $records = EformChangeAddress::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)
        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->count();
        return $records;

    }

    public function noOfparticulars($user){
        $records = EformParticular::where('view_status', 0)
        ->where('account_id', $user)
        ->where('status', 0)
        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->count();
        return $records;

    }

    public function noOfFaceids($user){
        //$date = Carbon::now()->subDays(7);
        $records = UserFacialId::where('status', 1)
        ->where('account_id', $user)
        ->count();
        return $records;

    }

    public function noOfPendingVerificationPayment($user){
        //$date = Carbon::now()->subDays(7);
        $records = FinanceInvoice::where('status', 4)
        ->where('account_id', $user)
        ->count();
        return $records;

    }

    public function check_importcsv_permission($property){
        $batch = FinanceInvoiceInfo::where('account_id',$property)->count();
        return $batch;
    }

    public function check_menu_permission($menu,$role,$rights){

        $account_id = Auth::user()->account_id;

        $role_id = Auth::user()->role_id;

        $records ='';
        
        $prop_reco = PropertyPermission::where('property_id',$account_id)->where('module_id',$menu)->first();

        if(($role_id ==1) || (isset($prop_reco) && $prop_reco->view >=1)){
       
            if($rights ==1) //view 
            {
                $records = ModuleSetting::where('role_id',$role)->where('module_id',$menu)->WhereNotIn('view',[0])->first();
            }
            if($rights ==2) //Create 
            {
                $records = ModuleSetting::where('role_id',$role)->where('module_id',$menu)->where('create',1)->first();
            }
            if($rights ==3) //Edit 
            {
                $records = ModuleSetting::where('role_id',$role)->where('module_id',$menu)->where('edit',1)->first();
            }
            if($rights ==4) //delete 
            {
                $records = ModuleSetting::where('role_id',$role)->where('module_id',$menu)->where('delete',1)->first();
            }
            
            return $records;

        }

    }


      public function check_user_permission($menu,$user,$rights){
       
       if($rights ==1) //view 
        {
            $records = UserPermission::where('user_id',$user)->where('module_id',$menu)->where('view',1)->first();
        }
        if($rights ==2) //Create 
        {
            $records = UserPermission::where('user_id',$user)->where('module_id',$menu)->where('create',1)->first();
        }
        if($rights ==3) //Edit 
        {
            $records = UserPermission::where('user_id',$user)->where('module_id',$menu)->where('edit',1)->first();
        }
        if($rights ==4) //delete 
        {
            $records = UserPermission::where('user_id',$user)->where('module_id',$menu)->where('delete',1)->first();
        }

        // $records;

       return $records;

    }

    public function check_permission($menu,$role){
        $records = ModuleSetting::where('role_id',$role)->where('module_id',$menu)->first();
        return $records;
 
     }

     public function check_menu_permission_level($menu,$property){
        $records = PropertyPermission::where('property_id',$property)->where('module_id',$menu)->first();
        return $records;
 
     }

    public function permissions(){
        return $this->hasMany('App\Models\v2\UserPermission','user_id');
    }

    public function favmenu(){
        return $this->hasMany('App\Models\v2\UserFavMenu','user_id');
    }


    public function logs(){
        return $this->hasMany('App\Models\v2\UserLog','user_id');
    }

    public function getos() {
        return $this->hasOne('App\Models\v2\UserLog','user_id')->latest();
    }
    

    public function household_add_api($token,$account,$name,$uuid,$roomuuids) {

        $url = env('THINMOO_API_URL')."persEmpHousehold/extapi/add";
       /*echo  "account :".$account."<br>";
       echo  "name :".$name."<br>";
       echo  "uuid :".$uuid."<br>";
       echo  "roomuuid :".$roomuuids."<br>"; */
    
        
        $fields = [
            'accessToken'       => $token,
            'extCommunityUuid'  => $account,
            'name'              => $name,
            'uuid'              => $uuid,
            'roomUuids'         => $roomuuids,

        ];

        $fields_string = http_build_query($fields);

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

        
        $result = curl_exec($ch);
        //print_r($result)
       
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        return $json;
    

    }

    public function household_modify_api($token,$account,$name, $uuid, $roomuuids) {

        $url = env('THINMOO_API_URL')."/persEmpHousehold/extapi/update";
        
       /* echo  "account :".$account."<br>";
       echo  "name :".$name."<br>";
       echo  "uuid :".$uuid."<br>";
       echo  "roomuuid :".$roomuuids."<br>"; */

        $fields = [
            'accessToken'       => $token,
            'extCommunityUuid'  => $account,
            'name'              => $name,
            'uuid'              => $uuid,
            'roomUuids'         => $roomuuids,
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



    public function household_delete_api($token,$user) {

        $url = env('THINMOO_API_URL')."persEmpHousehold/extapi/delete";

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

    public function household_check_record($token,$UserObj,$property='') {

        $url = env('THINMOO_API_URL')."/persEmpHousehold/extapi/get";
        if($property !='')
            $account = $property;
        else
            $account = $UserObj->account_id;
        //The data you want to send via POST
        
        $fields = [
            'accessToken'       => $token,
            'extCommunityUuid'  => $account,
            'uuid'              => $UserObj->id
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

    public function household_device_record($token,$UserObj) {

        $url = env('THINMOO_API_URL')."persEmpHousehold/extapi/getAuthorizationDevList";

        //The data you want to send via POST
        $fields = [
            'accessToken'       => $token,
            'extCommunityUuid'  => $UserObj->account_id,
            'uuid'              => $UserObj->id
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

    public function household_count_api($token,$account_id) {

        $url = env('THINMOO_API_URL')."persEmpHousehold/extapi/list";
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

    public function household_activate_api($token,$property,$name,$uuid) {

        $url = env('THINMOO_API_URL')."/persEmpHousehold/extapi/update";

        //The data you want to send via POST
        //$name = $user->name." ".$user->userinfo->last_name;
        //$startdate = date("Y-m-d H:i:s",strtotime($user->created_at));
        //$enddate =  date("Y-m-d H:i:s",strtotime($user->updated_at));
        $fields = [
            'accessToken'           => $token,
            'extCommunityUuid'      => $property,
            'name'                  => $name,
            'uuid'                  => $uuid,
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

    public function household_deactivate_api($token,$property,$name,$uuid,$start_date,$end_date) {

        $url = env('THINMOO_API_URL')."/persEmpHousehold/extapi/update";

        //The data you want to send via POST
        //$name = $user->name." ".$user->userinfo->last_name;
        $startdate = date("Y-m-d H:i:s",strtotime($start_date));
        $enddate =  date("Y-m-d H:i:s",strtotime($end_date));
        $fields = [
            'accessToken'           => $token,
            'extCommunityUuid'      => $property,
            'name'                  => $name,
            'uuid'                  => $uuid,
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
