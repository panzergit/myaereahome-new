<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\Role;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\UserFacialId;
use App\Models\v7\FacialRecoOption;
use App\Models\v7\Unit;
use App\Models\v7\Building;

use App\Models\v7\UserLog;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserNotification;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\Property;
use App\Models\v7\UserNotificationSetting;

use App\Models\v7\Employee;

use Auth;
use DB;
use File;
use Validator;
use Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;



class UserFacialIdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $option = $relationship = $name = $last_name = $faceids = $unit = $building = '';
        $login_id = Auth::user()->id;
        $account_id = Auth::user()->account_id;
        $permission = Auth::user();
        $permission = $permission->check_permission(50,$permission->role_id); 

        if(isset($permission)){
            $faceids = UserFacialId::where('account_id',$account_id)->where('status',2)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        }
        else{
            $faceids = UserFacialId::where('account_id',$account_id)->where('user_id',$login_id)->where('status',2)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        }
        $file_path = env('APP_URL')."/storage/app";

        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }

        return view('admin.faceid.index', compact('faceids','relationships','relationship','name','option','unit','file_path','buildings','building'));
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function new()
    {
        
        $option = $relationship = $name = $last_name = $faceids = $unit = $building ='';
      
        $account_id = Auth::user()->account_id;

        $faceids = UserFacialId::where('account_id',$account_id)->where('status',1)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        $file_path = env('APP_URL')."/storage/app";

        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

        return view('admin.faceid.new', compact('faceids','relationships','relationship','name','option','unit','file_path','buildings','building'));
    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        

        $account_id = Auth::user()->account_id;
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        $units = Unit::select('unit', 'id')->where('account_id',$account_id)->get();
        $unites = array();
        if(isset($units)){
            foreach($units as $unit){
                $unites[$unit->id] = Crypt::decryptString($unit->unit);
            }
        }

        $users = UserMoreInfo::whereNotIn('status',[2])->where('account_id',$account_id)->orderBy('first_name','ASC')->get();

        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();
        $roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')->orWhere('id',3)->orWhere('type',1)->orderby('name','asc')->pluck('name', 'id')->all();        


        return view('admin.faceid.create', compact('unites','buildings','users','relationships','roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $account_id = Auth::user()->account_id;

        $input['account_id'] = $account_id;
        $input['status'] =2;
		if ($request->file('picture') != null) {
			$input['face_picture'] = remove_upload_path($request->file('picture')->store(upload_path('profile')));
			$input['face_picture_base64'] = base64_encode(file_get_contents($request->file('picture')));
        }
       /* if(Auth::user()->role_id !=3 && Auth::user()->role_id !=201 && Auth::user()->role_id !=197 ){
            $input['user_id'] = Auth::user()->id;
            $input['option_id'] =1;
        }
        */
       
        $facialResult = UserFacialId::create($input);
       
        $UserMoreInfoObj = UserMoreInfo::where(['user_id' => $facialResult->user_id, 'status' => 1])->first();
        if($facialResult->option_id ==1 && $UserMoreInfoObj->face_picture ==''){
            UserMoreInfo::where('id' , $UserMoreInfoObj->id)->update(['face_picture' => $facialResult->face_picture, 
            'face_picture_base64' =>  $facialResult->face_picture_base64]);
        }
        $UserObj = User::find($facialResult->user_id);

		$auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();  
        $facial_obj = new \App\Models\v7\UserFacialId();
        
        $user_roles = explode(",",env('USER_APP_ROLE'));
        if(in_array($UserObj->role_id,$user_roles))
        {    
            $api_obj = new \App\Models\v7\User();
            $household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj,$account_id);

            $faceid_result = ($household_result['code'] ==0) ? 
                $facial_obj->faceImage_api($thinmoo_access_token,$UserObj,$facialResult,$account_id) : 
                $facial_obj->faceImage_add_api($thinmoo_access_token,$UserObj,$facialResult,$account_id);
        }else{
            $emp_result = Employee::where(['account_id' => $account_id, 'uuid' => $UserObj->id])->latest('id')->first();
            $emp_result['role_id'] = $UserObj->role_id;
            
            $emp_obj = new \App\Models\v7\Employee();
            $household_result = $emp_obj->employee_check_record($thinmoo_access_token,$emp_result);

            $faceid_result = ($household_result['code'] ==0) ? 
                $facial_obj->faceImage_emp_api($thinmoo_access_token,$emp_result,$facialResult) : 
                $facial_obj->faceImage_add_emp_api($thinmoo_access_token,$emp_result,$facialResult);
        }
   
        if(isset($faceid_result['data']['faceImageIds'])) UserFacialId::where('id', $facialResult->id)->update(['thinmoo_id' => $faceid_result['data']['faceImageIds']]);
       
        return redirect('opslogin/faceid')->with('status', 'Record has been added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\v7\UserMoreInfo  $userMoreInfo
     * @return \Illuminate\Http\Response
     */
    public function show(UserMoreInfo $userMoreInfo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\v7\UserMoreInfo  $userMoreInfo
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //$user = Category::pluck('category','id')->all();

        $UserFaceObj = UserFacialId::find($id);
        $account_id = Auth::user()->account_id;
        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();

        //print_r($UserObj->permissions);
        $file_path = env('APP_URL')."/storage/app";
        return view('admin.faceid.edit', compact('UserFaceObj','relationships', 'file_path'));
    }


    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\v7\UserMoreInfo  $userMoreInfo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $faceObj = UserFacialId::find($id);
        $faceObj->option_id = $request->input('option_id');
        $faceObj->save();

        //$UserObj = User::find($facialResult->user_id);

        $UserMoreInfoObj = UserMoreInfo::where('user_id',$faceObj->user_id)->where('account_id',$faceObj->account_id)->where('status',1)->first();
		if($faceObj->option_id ==1 && $UserMoreInfoObj->face_picture ==''){
			UserMoreInfo::where('id' , $UserMoreInfoObj->id)->update( array('face_picture' =>  $faceObj->face_picture,'face_picture_base64' =>  $faceObj->face_picture_base64));
		}

        return redirect('opslogin/faceid')->with('status', 'Relationship has been updated!');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\UserMoreInfo  $userMoreInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
        
        $facialResult = UserFacialId::find($id);
        
        if(isset($facialResult->user_id))
            $UserObj = User::find($facialResult->user_id);

		$auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();  
        
        $facial_obj = new \App\Models\v7\UserFacialId();
        $user_roles = explode(",",env('USER_APP_ROLE'));
        
        if(isset($UserObj)){
            if(in_array($UserObj->role_id,$user_roles)){
                $api_obj = new \App\Models\v7\User();
                $household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj);
                if($household_result['code'] ==0){
                    $faceid_result= $facial_obj->faceImage_delete_api($thinmoo_access_token,$UserObj,$facialResult);
                }
            }else{
                $emp_obj = new \App\Models\v7\Employee();
                $household_result = $emp_obj->employee_check_record($thinmoo_access_token,$UserObj);
                if($household_result['code'] ==0){
                    $faceid_result= $facial_obj->faceImage_delete_emp_api($thinmoo_access_token,$UserObj,$facialResult);
                }      
            }
        }
        UserFacialId::findOrFail($id)->delete();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/faceid?page=$page#fi")->with('status', 'Face Id deleted successfully!');}
        else
            return redirect('opslogin/faceid#fi')->with('status', 'Face Id deleted successfully!');
    }

    public function deleteUserFile(Request $request)
    {

        $id = $request->input('id');
        $file_path = $request->input('file_path');

        $file_path = $file_path; // Value is not URL but directory file path
        if (File::exists($file_path)) {
            File::delete($file_path);
            UserDocument::where('id', $id)->delete();
        }

        $success['message'] = "success";

        $request->session()->flash('message', " File has been removed.");
        $request->session()->flash('message-type', 'success');

        return response()->json(['success' => $success], 200);
    }

    public function search(Request $request)
    {
        $option = $relationship = $name = $last_name = $faceids = $unit = $building = '';
        $option = $request->input('option'); 
        $account_id = Auth::user()->account_id;
        $name = $request->input('name');
        $relationship = $request->input('relationship');
        $unit = $request->input('unit');
        $building = $request->input('building');

        $units = array();
        if($unit !='' || $building !=''){   
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit,$building) {
                if($building !='')
                    $query->where('building_id',$building);
            })->get();   

            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
                        $units[] = $unitid->id;
                    else if ($request->input('unit') =='')
                        $units[] = $unitid->id;
                }
            }
        }

        $userids = array();
        if($name !=''){      
            $userObj = UserMoreInfo::select('user_id')->where('account_id',$account_id)->where('first_name', 'LIKE', '%'.$name.'%')
            ->orWhere('last_name', 'LIKE', '%'.$name.'%')->get();    
            if(isset($userObj)){
                foreach($userObj as $user){
                    $userids[] = $user->user_id;
                }
            }
        }
        //print_r($userids);
        $faceids =  UserFacialId::where('account_id',$account_id)->where('status',1)->where(function ($query) use ($relationship,$name,$userids,$unit,$units,$building) {
            if($name !='')
                $query->whereIn('user_id', $userids);
            if($relationship !='')
                $query->where('option_id',$relationship);
            if($unit !='' || $building !='')
                $query->whereIn('unit_no', $units);

        })->paginate(env('PAGINATION_ROWS'));
        
            $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

            $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();
            $file_path = env('APP_URL')."/storage/app";
            
            return view('admin.faceid.new', compact('faceids','relationships','relationship','name','option','unit','file_path','buildings','building'));


        
    }

    public function summarysearch(Request $request)
    {
        $option = $relationship = $name = $last_name = $faceids = $unit ='';
        $option = $request->input('option'); 
        $account_id = Auth::user()->account_id;
        $name = $request->input('name');
        $relationship = $request->input('relationship');
        $unit = $request->input('unit');
        $building = $request->input('building');

        $units = array();
        if($unit !='' || $building !=''){   
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit,$building) {
                if($building !='')
                    $query->where('building_id',$building);
            })->get();   

            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
                        $units[] = $unitid->id;
                    else if ($request->input('unit') =='')
                        $units[] = $unitid->id;
                }
            }
        }
        $userids = array();
        if($name !=''){      
           /* $userObj = UserMoreInfo::select('user_id')->where('account_id',$account_id)->where('first_name', 'LIKE', '%'.$name.'%')
            ->orWhere('last_name', 'LIKE', '%'.$name.'%')->get();    
            if(isset($userObj)){
                foreach($userObj as $user){
                    $userids[] = $user->user_id;
                }
            }*/
            $user_more_info = UserMoreInfo::select('id','user_id','first_name','last_name')->where('account_id',$account_id)->whereNotIn('status',[2])->orderby('id','desc')->get();
            
            foreach($user_more_info as $k =>$v){
                $firstname = strtolower(Crypt::decryptString($v->first_name));
                $lastname = strtolower(Crypt::decryptString($v->last_name));
                if(str_contains($firstname,strtolower($name)) || str_contains($lastname,strtolower($name))){
                    $userids[] = $v->user_id;
                   
                }
            }
        }

        $faceids =  UserFacialId::where('account_id',$account_id)->where('status',2)->where(function ($query) use ($relationship,$name,$userids,$unit,$units,$building) {
            if($name !='')
                $query->whereIn('user_id', array_unique($userids));
            if($relationship !='')
                $query->where('option_id',$relationship);
            if($unit !='' || $building !='')
                $query->whereIn('unit_no', $units);

        })->paginate(env('PAGINATION_ROWS'));

            $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

            $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();
            $file_path = env('APP_URL')."/storage/app";
            
            return view('admin.faceid.index', compact('faceids','relationships','relationship','name','option','unit','file_path','buildings','building'));
    }

    public function updatecancelstatus(Request $request){

         $input = $request->all();
          $reason ='';

        if(isset($input['reason']))
            $reason = $input['reason'];

        $bookid = $input['bookId'];
        $status = $input['status']; //cancelled

        UserFacialId::where('id', $bookid)
                ->update(['status' => $status,'reason'=>$reason]);

        $faceidObj  =  UserFacialId::find($bookid);  
        //Start Insert into notification module
        $notification = array();
        $notification['account_id'] = $faceidObj->account_id;
        $notification['user_id'] = $faceidObj->user_id;
        $notification['unit_no'] = $faceidObj->unit_no;
        $notification['module'] = 'faceid';
        $notification['ref_id'] = $faceidObj->id;
        $notification['title'] = 'Facial Recognition';
        $notification['message'] = 'There is an update from the management in regards to your facial recognition';
        $result = UserNotification::insert($notification);

        $SettingsObj = UserNotificationSetting::where('user_id',$faceidObj->user_id)->where('account_id',$faceidObj->account_id)->first();
		if(empty($SettingsObj) || $SettingsObj->face_id_upload ==1){
            $fcm_token_array ='';
            $user_token = ',';
            $ios_devices_to_send = array();
            $android_devices_to_send = array();
            $logs = UserLog::where('user_id',$faceidObj->user_id)->where('status',1)->orderby('id','desc')->first();
            if(isset($logs->fcm_token) && $logs->fcm_token !=''){
                $user_token .=$logs->fcm_token.",";
                $fcm_token_array .=$logs->fcm_token.',';
                $appSipAccountList[] = $faceidObj->id;
                if($logs->login_from ==1)
                    $ios_devices_to_send[] = $logs->fcm_token;
                if($logs->login_from ==2)
                    $android_devices_to_send[] = $logs->fcm_token;
            }

            $probObj = Property::find($faceidObj->account_id);
			$title = "Aerea Home - ".$probObj->company_name;
			$message = "Facial Recognition Cancelled";
            $notofication_data = array();
            $notofication_data['body'] =$title;
            $notofication_data['unit_no'] =$faceidObj->unit_no;   
            $notofication_data['user_id'] =$faceidObj->user_id;   
            $notofication_data['property'] =$faceidObj->account_id; 
            $purObj = UserPurchaserUnit::where('property_id',$faceidObj->account_id)->where('unit_id',$faceidObj->unit_no)->where('user_id',$faceidObj->user_id)->first(); 
            if(isset($purObj))
                $notofication_data['switch_id'] =$purObj->id;        
            $NotificationObj = new \App\Models\v7\FirebaseNotification();
            $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
            $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
        }
        
        if(isset($input['return_url']))
            return redirect('opslogin/faceid/new#fi')->with('status', "Registration Cancelled!!");
        else
            return redirect('opslogin/faceid/new#fi')->with('status', "Registration Cancelled!!");
     }



     public function updateconfirmstatus(Request $request){


         $input = $request->all();
          $reason ='';

        $bookid = $input['Id'];
        $status = $input['status']; //cancelled
        
        UserFacialId::where('id', $bookid)
                ->update(['status' => $status,'updated_at'=>date('Y-m-d H:i:s')]);
        
        $facialResult = UserFacialId::find($bookid);
       
        $UserObj = User::find($facialResult->user_id);

        if($facialResult->option_id ==1 && $UserObj->face_picture ==''){
			//UserMoreInfo::where( 'user_id' , $facialResult->user_id)->update( array( 'face_picture' =>  $facialResult->face_picture,'face_picture_base64' =>  $facialResult->face_picture_base64));
            UserMoreInfo::where( 'user_id' , $facialResult->user_id)->update( array( 'face_picture' =>  $facialResult->face_picture));
		}

        $UserObj = User::find($facialResult->user_id);

		$auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();  
		
		
        $api_obj = new \App\Models\v7\User();
		$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj);
        
   
		$facial_obj = new \App\Models\v7\UserFacialId();
        if($household_result['code'] ==0){

            $faceid_result= $facial_obj->faceImage_api($thinmoo_access_token,$UserObj,$facialResult);
        }
        else{
			$faceid_result= $facial_obj->faceImage_add_api($thinmoo_access_token,$UserObj,$facialResult);
        }
        
     
        if(isset($faceid_result['data'])){
            $file = 'app/'. $facialResult->face_picture;
            $path = storage_path($file);
            if (File::exists($path)) {
                File::delete($path);
            }
            UserFacialId::where('id', $bookid)
        ->update(['face_picture_base64'=>'','thinmoo_id' => $faceid_result['data']['faceImageIds']]);
        }
        $faceidObj  =  UserFacialId::find($bookid);  
        //Start Insert into notification module
        $notification = array();
        $notification['account_id'] = $faceidObj->account_id;
        $notification['user_id'] = $faceidObj->user_id;
        $notification['unit_no'] = $faceidObj->unit_no;
        $notification['module'] = 'faceid';
        $notification['ref_id'] = $faceidObj->id;
        $notification['title'] = 'Facial Recognition';
        $notification['message'] = 'There is an update from the management in regards to your facial recognition';
        $result = UserNotification::insert($notification);

        $SettingsObj = UserNotificationSetting::where('user_id',$faceidObj->user_id)->where('account_id',$faceidObj->account_id)->first();
		if(empty($SettingsObj) || $SettingsObj->face_id_upload ==1){
            $fcm_token_array ='';
            $user_token = ',';
            $ios_devices_to_send = array();
            $android_devices_to_send = array();
            $logs = UserLog::where('user_id',$faceidObj->user_id)->where('status',1)->orderby('id','desc')->first();
            if(isset($logs->fcm_token) && $logs->fcm_token !=''){
                $user_token .=$logs->fcm_token.",";
                $fcm_token_array .=$logs->fcm_token.',';
                $appSipAccountList[] = $faceidObj->id;
                if($logs->login_from ==1)
                    $ios_devices_to_send[] = $logs->fcm_token;
                if($logs->login_from ==2)
                    $android_devices_to_send[] = $logs->fcm_token;
            }

            $probObj = Property::find($faceidObj->account_id);
			$title = "Aerea Home - ".$probObj->company_name;
			$message = "Facial Recognition Approved";
            $notofication_data = array();
            $notofication_data['body'] =$title;
            $notofication_data['unit_no'] =$faceidObj->unit_no;   
            $notofication_data['user_id'] =$faceidObj->user_id;   
            $notofication_data['property'] =$faceidObj->account_id; 
            $purObj = UserPurchaserUnit::where('property_id',$faceidObj->account_id)->where('unit_id',$faceidObj->unit_no)->where('user_id',$faceidObj->user_id)->first(); 
            if(isset($purObj))
                $notofication_data['switch_id'] =$purObj->id;        
            $NotificationObj = new \App\Models\v7\FirebaseNotification();
            $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
            $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
        }
       
        return redirect('opslogin/faceid/new#fi')->with('status', "Registration Confirmed!!");
        
     }


     public function accessfaceid(Request $request){

        $input = $request->all();
        $id = $input['id'];
        $code = $input['code']; //access code
        $faceidObj  =  UserFacialId::find($id);
        if(isset($faceidObj)){
            $userId =  Auth::user()->id;
            $MoreInfoObj = UserMoreInfo::where('user_id',$userId)->whereNotIn('status',[2])->first();
            if(isset($MoreInfoObj)){
                if($code == $MoreInfoObj->faceid_access_code){
                    $result['status'] = 1;
                    $result['img'] =  $file_path = env('APP_URL')."/storage/app/".$faceidObj->face_picture;
                    $result['64img'] = $faceidObj->face_picture_base64;
                }
                else{
                    $result['status'] = 3;
                }
            }
            else{
                $result['status'] = 2;
            }
        }
        else{
            $result['status'] = 0;
        }
        return json_encode($result);
    }

  


}
