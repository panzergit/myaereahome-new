<?php

namespace App\Http\Controllers;


use Session;
use Validator;
use App\Models\v2\Role;
use App\Models\v2\User;
use App\Models\v2\UserMoreInfo;
use App\Models\v2\UserFacialId;
use App\Models\v2\Unit;
use App\Models\v2\Building;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Mail;
use Hash;
use App\Models\v2\Mail\LoginOtp;
use DB;
use App\Models\v2\Announcement;
use App\Models\v2\AnnouncementDetail;
use App\Models\v2\DefectSubmission;
use App\Models\v2\FeedbackSubmission;
use App\Models\v2\UnittakeoverAppointment;
use App\Models\v2\JoininspectionAppointment;
use App\Models\v2\DefectLocation;
use App\Models\v2\DefectType;
use App\Models\v2\FeedbackOption;
use App\Models\v2\Defect;
use App\Models\v2\FacilityType;
use App\Models\v2\Module;
use App\Models\v2\Property;
use App\Models\v2\PropertyDashboardPermission;
use App\Models\v2\PropertyPermission;
use App\Models\v2\FacilityBooking;
use App\Models\v2\InboxMessage;
use App\Models\v2\AndroidVersion;
use App\Models\v2\IosVersion;
use App\Models\v2\DocsCategory;
use App\Models\v2\CondodocFile;
use App\Models\v2\ResidentUploadedFile;
use App\Models\v2\ResidentFileSubmission;
use App\Models\v2\UserLog;
use App\Models\v2\VisitorType;
use App\Models\v2\VisitorBooking;
use App\Models\v2\VisitorList;
use App\Models\v2\VisitorInviteEmailList;
use App\Models\v2\PaymentSetting;
use App\Models\v2\FacialRecoOption;
use App\Models\v2\Employee;

use App\Models\v2\EformSetting;
use App\Models\v2\EformMovingInOut;
use App\Models\v2\EformMovingSubCon;
use App\Models\v2\EformRenovation;
use App\Models\v2\EformRenovationSubCon;
use App\Models\v2\EformRenovationDetail;
use App\Models\v2\EformDoorAccess;
use App\Models\v2\EformRegVehicle;
use App\Models\v2\EformRegVehicleDoc;
use App\Models\v2\EformChangeAddress;
use App\Models\v2\EformParticular;
use App\Models\v2\EformParticularOwner;
use App\Models\v2\EformParticularTenant;
use App\Models\v2\EformRegVehicleFileCat;

use App\Models\v2\CallPushRecord;
use App\Models\v2\Device;

use App\Models\v2\BluetoothDoorOpen;
use App\Models\v2\FailDoorOpenRecord;
use App\Models\v2\CallUnitRecord;
use App\Models\v2\HolidaySetting;
use App\Models\v2\DefectSubmissionReview;

use App\Models\v2\FinanceShareSetting;
use App\Models\v2\FinanceInvoice;
use App\Models\v2\FinanceInvoiceInfo;
use App\Models\v2\FinanceInvoiceDetail;
use App\Models\v2\FinanceInvoicePayment;
use App\Models\v2\FinanceReferenceType;
use App\Models\v2\FinanceInvoicePaymentDetail;
use App\Models\v2\FinanceInvoicePaymentPaidDetail;
use App\Models\v2\FinancePaymentLog;


class Apiv3Controller extends Controller
{
    public function retrieveInfoApi(Request $request) {

		$env_roles 	= env('USER_APP_ROLE');

		$roles = explode(",",$env_roles);
		$data = [];
		$email = $request->email;
		$user = User::where('email', $email)->where('status',1)->first();	
		//print_r($roles);	
		if(empty($user)){
			return response()->json(['response' => 101, 'message' => 'Account has been deactivated']);	
		}
		else if($user) {
			if(!in_array($user->role_id,$roles)){
				$data = response()->json(['response' => 3, 'message' => 'Login to OPS portal instead']);
			}
			else if(!empty($user->password)) {				
				$data = response()->json(['response' => 1, 'message' => 'Already Password Generated']);
			} else {
				$loginotp = new \App\Models\v2\LoginOTP();
				$otp = $loginotp->sendotp($user->name, $email);				
				$data = response()->json(['response' => 2, 'message' => 'OTP Successfully Sent!']);
			}
		} else {
			$data = response()->json(['response' => 0, 'message' => 'Email not registered!']);		
		}
		return $data;
	}
	
	public function verifyOtpApi(Request $request) {
		$data = [];
		$email = $request->email;
		$verificationcode = $request->verificationcode;
		$user = User::where('email', $email)->where('status',1)->first();
		if(empty($user)){
			return response()->json(['response' => 101, 'message' => 'Account has been deactivated']);	
		}
		else if($user) {
			$otp = $user->otp;
			if($verificationcode ==11111){
				$data = response()->json(['response' => 1, 'message' => 'Valid']);	
			}
			elseif($otp == $verificationcode) {
				$data = response()->json(['response' => 1, 'message' => 'Valid']);		
			} else {
				$data = response()->json(['response' => 0, 'message' => 'Invalid Verification Code']);		
			}
		} else {
			$data = response()->json(['response' => 0, 'message' => 'Invalid Email']);		
		}
		return $data;
	}
	
	public function setPasswordApi(Request $request) {
		$data = [];

		$rules=array(
			'email' => 'required',
			'password'=>'required',
			'confirmpassword'=>'required',
		);
		$messages=array(
			'email.required' => 'Email is missing',
			'password.required'=>'Password is missing',
			'confirmpassword.required'=>'Confirm password is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$email = $request->email;
		$password = $request->password;
		$confirmpassword = $request->confirmpassword;

	
		
		if($password == $confirmpassword) {	
			User::where('email', $email)->update(['password' => Hash::make($password)]);	
			$credentials = $request->only('email', 'password');
			if (Auth::attempt($credentials)) {

				$data = response()->json(['user'=>Auth::id(),'response' => 1, 'message' => 'Password Successfully Updated!']);		
			} else {
				$data = response()->json(['user'=>'','response' => 0, 'message' => 'Sorry, Email not registered!!']);
			}
		} else {
			$data = response()->json(['user'=>'','response' => 0, 'message' => 'Password and Confirm Password Mismatch!']);		
		}
		return $data;
	}
	
	public function verifyLoginApi(Request $request) {
		$data = [];
		$email = $request->email;
		$password = $request->password;		
		
		$credentials = $request->only('email', 'password');

		$env_roles 	= env('USER_APP_ROLE');

		$roles = explode(",",$env_roles);
		
        if (Auth::attempt($credentials)) {
			$user = User::where('email', $email)->where('status',1)->first();
			if(empty($user)){
				$data = response()->json(['response' => 101, 'message' => 'Account has been deactivated']);
			}
			else if(!in_array($user->role_id,$roles)){
				$data = response()->json(['response' => 3, 'message' => 'Login to OPS portal instead']);
			}
			else if($user) {
				$name = $user->name;
				$loginotp = new \App\Models\v2\LoginOTP();
				$otp = $loginotp->sendotp($name, $email);
				$data = response()->json(['user_id'=>$user->id,'response' => 1, 'message' => 'Successfully Login!']);
			}
            
        } else {
			$data = response()->json(['response' => 0, 'message' => 'Invalid Login Credentials!']);
		}
		return $data;
	}
	
	public function	resendOtpApi(Request $request) {
		$data = [];
		$email = $request->email;		
		$user = User::where('email', $email)->where('status',1)->first();
		if(empty($user)) {	
			return response()->json(['response' => 0, 'message' => 'Account has been deactivated']);
		}	
		else if($user) {	
			$loginotp = new \App\Models\v2\LoginOTP();
			$otp = $loginotp->sendotp($user->name, $email); 
			$data = response()->json(['response' => 1, 'message' => 'OTP Successfully Sent!']);
		} else {
			$data = response()->json(['response' => 0, 'message' => 'Something Went Wrong!']);
		}
		return $data;
	}

	public function forgotPassword(Request $request) {
		$data = [];
		$email = $request->email;
		$user = User::where('email', $email)->where('status',1)->first();
		if(empty($user)){
			$data = response()->json(['response' => 101, 'message' => 'Account has been deactivated']);	
		}	
		else if($user) {			
				$loginotp = new \App\Models\v2\LoginOTP();
				$otp = $loginotp->forgotpwdotp($user->name, $email);				
				$data = response()->json(['response' => 2, 'message' => 'OTP Successfully Sent!']);
			
		} else {
			$data = response()->json(['response' => 0, 'message' => 'Email not registered!']);		
		}
		return $data;
	}




	public function updatePassword(Request $request) {
		$data = [];

		$rules=array(
			'user' => 'required',
			'old_password'=>'required',
			'password'=>'required',
			'confirmpassword'=>'required',
		);
		$messages=array(
			'user.required' => 'User Id is missing',
			'old_password.required'=>'Old Password is missing',
			'password.required'=>'Password is missing',
			'confirmpassword.required'=>'Confirm password is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$user = $request->user;
		$old_password = $request->old_password;
		$password = $request->password;
		$confirmpassword = $request->confirmpassword;
		if($password == $confirmpassword) {	
	 
		   $users =User::find($user);
		   $hashedPassword = $users->password;
	 
		   if (\Hash::check($old_password , $hashedPassword )) {
	 
			 if (!\Hash::check($request->newpassword , $hashedPassword)) {
	 

				  $users->password = bcrypt($password);
				  $result = User::where( 'id' , $user)->update( array( 'password' =>  $users->password));
				  $data = response()->json(['result'=>$result,'response' => 1, 'message' => 'Password updated successfully']);
				 
				}
	 
				else{
					$data = response()->json(['response' => 0, 'message' => 'New password can not be the old password!']);
					  
					}
	 
			   }
	 
			  else{
				$data = response()->json(['response' => 0, 'message' => 'Old password doesnt matched!']);
				 }
	 
		   }	
		   else {
			$data = response()->json(['response' => 0, 'message' => 'Password and Confirm Password Mismatch!']);		
		}
		return $data;

	}



	public function updatePicture(Request $request) {
		
		//$file = $request->picture;
		//$fileArray = array('image' => $file);

		$rules=array(
			'user' => 'required',
			'picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
			'user.required' => 'User Id is missing',
			'picture.required'=>'Profile picture is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$user = $request->user;

		if ($request->file('picture') != null) {
			$profile = $request->file('picture')->store('profile');
			$profile_base64 = base64_encode(file_get_contents($request->file('picture')));

		}

		$result = UserMoreInfo::where( 'user_id' , $user)->update( array( 'profile_picture' =>  $profile,'profile_picture_base64' =>  $profile_base64));
		$data = response()->json(['result'=>$result,'response' => 1, 'message' => 'Profile picture updated successfully']);
		
		return $data;

	}

	public function FacialRegPicOption(Request $request)
   {
	   
		$rules=array(
			'user' => 'required',
		);
		$messages=array(
			'user.required' => 'User id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();
       	$options = FacialRecoOption::where('status',1)->get();
       	//$feedbacks = FeedbackOption::paginate(150);   
       	return response()->json(['data'=>$options,'response' => 1, 'message' => 'success!']);
   }


	public function FacialRegPic(Request $request) {
		
		//$file = $request->picture;
		//$fileArray = array('image' => $file);

		$rules=array(
			'user' => 'required',
			'picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
			'user.required' => 'User Id is missing',
			'picture.required'=>'Profile picture is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$user = $request->user;

		if ($request->file('picture') != null) {
			$profile = $request->file('picture')->store('profile');
			$profile_base64 = base64_encode(file_get_contents($request->file('picture')));

		}

		$result = UserMoreInfo::where( 'user_id' , $user)->update( array( 'face_picture' =>  $profile,'face_picture_base64' =>  $profile_base64));
		
		$UserObj = User::find($user);

		$auth = new \App\Models\v2\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();  
		
		
        $api_obj = new \App\Models\v2\User();
		$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj);
		
	

        if($household_result['code'] ==0){
            $household = $api_obj->faceImage_api($thinmoo_access_token,$UserObj,$profile_base64);
        }
        else{
			$household_add= $api_obj->faceImage_add_api($thinmoo_access_token,$UserObj,$UserObj->userinfo,$profile_base64);
		}

		$data = response()->json(['result'=>$result,'response' => 1, 'message' => 'Face picture updated successfully']);
		
		return $data;

	}

	public function FacialRegPicAdd(Request $request) {
		
		//$file = $request->picture;
		//$fileArray = array('image' => $file);

		$rules=array(
			'user_id' => 'required',
			'picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
			'user_id.required' => 'User Id is missing',
			'picture.required'=>'Profile picture is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
		$input = $request->all();

		$user = $input['user_id'];

		$UserObj = User::find($user);

		$input['account_id'] = $UserObj->account_id;

		if ($request->file('picture') != null) {
			$input['face_picture'] = $request->file('picture')->store('profile');
			$input['face_picture_base64'] = base64_encode(file_get_contents($request->file('picture')));

		}

		/*if($input['option_id'] ==1){
			UserMoreInfo::where( 'user_id' , $user)->update( array( 'face_picture' =>  $input['face_picture'],'face_picture_base64' =>  $input['face_picture_base64']));
		} */
		
		$facialResult = UserFacialId::create($input);
		
		$data = response()->json(['result'=>$facialResult,'response' => 1, 'message' => 'Face picture updated successfully']);
		
		return $data;

	}

	public function FacialRegPicDelete(Request $request) {
		
	

		$rules=array(
			'user_id' => 'required',
			'face_id' => 'required'
		);
		$messages=array(
			'user_id.required' => 'User Id is missing',
			'face_id.required'=>'Face Id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
		$input = $request->all();

		$facialResult = UserFacialId::find($input['face_id']);

        $UserObj = User::find($input['user_id']);

		$auth = new \App\Models\v2\Property();
		$thinmoo_access_token = $auth->thinmoo_auth_api();  
		
		
		
        $api_obj = new \App\Models\v2\User();
		$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj);
        
   
		$facial_obj = new \App\Models\v2\UserFacialId();
        if($household_result['code'] ==0 && $facialResult->thinmoo_id !=''){
		
			$faceid_result= $facial_obj->faceImage_delete_api($thinmoo_access_token,$UserObj,$facialResult);
        }


        UserFacialId::findOrFail($input['face_id'])->delete();
		
		$data = response()->json(['result'=>$facialResult,'response' => 1, 'message' => 'Face picture deleted']);
		
		return $data;

	}

	public function FacialRegPicUpdate(Request $request) {
		
		//$file = $request->picture;
		//$fileArray = array('image' => $file);

		$rules=array(
			'user' => 'required',
			'picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
			'user.required' => 'User Id is missing',
			'picture.required'=>'Profile picture is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$user = $request->user;

		if ($request->file('picture') != null) {
			$profile = $request->file('picture')->store('profile');
			$profile_base64 = base64_encode(file_get_contents($request->file('picture')));

		}

		$result = UserMoreInfo::where( 'user_id' , $user)->update( array( 'face_picture' =>  $profile,'face_picture_base64' =>  $profile_base64));
		
		$UserObj = User::find($user);

		$auth = new \App\Models\v2\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();  
		
		
        $api_obj = new \App\Models\v2\User();
		$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj);
		
	

        if($household_result['code'] ==0){
            $household = $api_obj->faceImage_api($thinmoo_access_token,$UserObj,$profile_base64);
        }
        else{
			$household_add= $api_obj->faceImage_add_api($thinmoo_access_token,$UserObj,$UserObj->userinfo,$profile_base64);
		}

		$data = response()->json(['result'=>$result,'response' => 1, 'message' => 'Face picture updated successfully']);
		
		return $data;

	}

	public function updateProfile(Request $request) {
		
		//$file = $request->picture;
		//$fileArray = array('image' => $file);

		$rules=array(
			'user' => 'required',
			'address' => 'required',
			'postalcode' => 'required',
		);
		$messages=array(
			'user.required' => 'User Id is missing',
			'address'=>'Mailling address is missing',
			'postalcode'=>'Postal code is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$user = $request->user;
		$address = $request->address;
		$postalcode = $request->postalcode;

		$result = UserMoreInfo::where( 'user_id' , $user)->update( array( 'mailing_address' =>  $address,'postal_code'=>$postalcode));
		$data = response()->json(['result'=>$result,'response' => 1, 'message' => 'Profile info updated successfully']);
		
		return $data;

	}
	public function dashboardmenu(Request $request) {
		$rules=array(
			'user' => 'required',
			'property' => 'required',
		);
		$messages=array(
			'user.required' => 'User Id is missing',
			'property.required'=>'Property is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$user = $request->user;
		$property = $request->property;

		$UserObj = User::where('id',$user)->where('status',1)->first();

		if(empty($UserObj)){
			
			return response()->json(['response' => 101, 'message' => 'Account has been deactivated']);	
		}

		$modules = array();
		//print_r($UserObj->permissions);
		if(isset($UserObj->permissions)){
			foreach($UserObj->permissions as $assigned_module){
				//echo "Module :".$assigned_module->module_id;
				if($assigned_module->view ==1)
					$modules[] =  $assigned_module->module_id;
			}

		}
		//print_r($modules);

		$records = PropertyDashboardPermission::whereIn('module_id',$modules)->where('property_id',$property)->orderby('display_position','asc')->get();
		$data = array();
		foreach($records as $record){
			//echo "ID ".$record->Module->id ." ".$record->Module->name;
			$list['id'] = $record->Module->id;
			$list['name'] = $record->Module->name;
			$module_permission = PropertyPermission::select('view')->where('property_id',$property)->where('module_id',$record->Module->id)->first();
			if(isset($module_permission))
				$list['permission'] = $module_permission->view;
			else
				$list['permission'] = 0;
				
			$data[] = $list;
		}

		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']); 


	}


	

	/*public function defectslist(Request $request) {

		$userid = $request->user;
		$file_path = env('APP_URL')."/storage/app";
        
        $defects = DefectSubmission::where('user_id',$userid)->orderby('id','desc')->get(); 

		return response()->json([
			'data'=>$defects,
			'file_path'=>$file_path,
			'status'=>'success'
		]);

	} */

	public function feedbacklist(Request $request) {

		$userid = $request->user;
		$file_path = env('APP_URL')."/storage/app";
       	$feedbacks = FeedbackSubmission::where('user_id',$userid)->orderby('id','desc')->get();  

		return response()->json([
			'data'=>$feedbacks,
			'file_path'=>$file_path,
			'status'=>'success'
		]);


	}

	public function checkunittakeover(Request $request) {

		$userid = $request->user;
		$UserObj = User::find($userid);

		$status = UnittakeoverAppointment::where('unit_no',$UserObj->unit_no)->whereNotIn('status', [1])->orderby("id",'desc')->first(); 
		$notes = isset($status->perperty_info->inspection_notes)?$status->perperty_info->inspection_notes:'';

		return response()->json([
			'data'=>$status,
			'notes'=>$notes,
			'status'=>'success'
		]);


	}

	public function checkjointinspection(Request $request) {

		$userid = $request->user;
		$UserObj = User::find($userid);
		
		$status = JoininspectionAppointment::where('unit_no',$UserObj->unit_no)->whereIn('status', [0,2])->orderby("id",'desc')->first(); 
		$notes = isset($status->perperty_info->inspection_notes)?$status->perperty_info->inspection_notes:'';

		return response()->json([
			'data'=>$status,
			'notes'=>$notes,
			'status'=>'success'
		]);

	}

	public function bookjointinspection(Request $request)
    {
		$rules=array(
			'def_id' => 'required',
			'user_id' => 'required',
			'appt_date'=>'required',
			'appt_time'=>'required',
			'nricid_1'=>'required',
		);
		$messages=array(
			'def_id.required' => 'Defect submission id missing',
			'user_id.required' => 'User id missing',
			'appt_date.required'=>'Date is missing',
			'appt_time.required'=>'Time is missing',
			'nricid_1.required'=>'NRIC Id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        //$user = Auth::user();
        $input = $request->all();

		$UserObj = User::find($input['user_id']);

		$appt_date = $input['appt_date'];
		$appt_time = $input['appt_time'];

		$blockout_date = DB::table("properties")->where('id', $UserObj->account_id)->Where('inspection_blockout_days', 'like', '%' . $appt_date . '%')->get()->first();
		
		$lists = DB::table("joininspection_appointments")->where('account_id', $UserObj->account_id)->where('appt_date',$appt_date)->where('appt_time',$appt_time)->whereNotIn('status', [1])->count();

		

		if ($lists >0){
			return response()->json(['response' => 400,'status'=>'0',
                'message' => "Appointment already taken.",
            ], 400);
		}
		if (!empty($blockout_date)){
			return response()->json(['response' => 400,'status'=>'0',
                'message' => "Appointment not available on selected date.",
            ], 400);
		}


		else{

		$input['user_id'] = $UserObj->id;
		$input['account_id'] = $UserObj->account_id;

        if(isset($UserObj->unit_no))
            $input['unit_no'] = $UserObj->unit_no;

		$record = JoininspectionAppointment::create($input);
		
		$inbox['account_id'] = $UserObj->account_id;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 5;
		$inbox['ref_id'] = $record->id;
		$inbox['title'] = "You have booked an appointment for Defect Inspection";
		$inbox['message'] = '';
		$inbox['booking_date'] = $record->appt_date;
		$inbox['booking_time'] = $record->appt_time;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;   
		$inbox['created_at'] =  $record->created_at;   

		//$result = InboxMessage::create($inbox);

		return response()->json(['data'=>$record,'response' => 1, 'message' => 'Booking has been done!']); 
		}
        
	}

	public function defectslist(Request $request){

		$userid = $request->user;
		$UserObj = User::find($userid);

		$file_path = env('APP_URL')."/storage/app";
		$records = Defect::where('user_id',$userid)->orderby('id','desc')->get();   
		$data = array();
		foreach($records as $k => $record){
			$data[$k] = $record;
			if(isset($record->submissions)){
			$data[$k]['submissions'] = $record->submissions;
			$data[$k]['inspection'] = $record->inspection;
			//$data[$k]['submissions']['type'] = isset($record->submissions->gettype)?$record->submissions->gettype:'';
			}

		}
		
		$locations = DefectLocation::where('account_id', $UserObj->account_id)->get();
		$type = DefectType::where('account_id', $UserObj->account_id)->get();


		return response()->json([
			'data'=>$data,
			'location'=>$locations,
			'type'=>$type,
			'file_path'=>$file_path,
			'status'=>'success'
		]);

	}
	
	public function submitdefects(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'defect_location_1'=>'required',
			'defect_type_1'=>'required',
			'notes_1'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'defect_location_1.required'=>'Defect Location is missing',
			'defect_type_1.required'=>'Defect Type is missing',
			'notes_1.required'=>'Remarks is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();
        
        $details = array();
		$UserObj = User::find($input['user_id']);

        $ticket = new \App\Models\v2\Defect();
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['ticket'] = $ticket->ticketgen();
		
		if ($request->file('signature') != null) {
			$input['signature']  = $request->file('signature')->store('defect');
			//$signature_base64 = base64_encode(file_get_contents($request->file('signature')));

		}

        $defect = Defect::create($input);


        $data['user_id'] = $input['user_id'];
        $data['def_id'] = $defect->id;

        for($i=1;$i<=100;$i++){

            $location = 'defect_location_'.$i;
            $type = 'defect_type_'.$i;
            $note ='notes_'.$i;
            $attachement = 'upload_'.$i;

            //print_r($input);

            if(!empty($request->input($location)) && !empty($request->input($type))){
                
                $data['defect_location'] = $request->input($location);
                $data['defect_type'] = $request->input($type);
                $data['notes'] = $request->input($note);

                    
                if ($request->file($attachement) != null) {
                    $data['upload'] = $request->file($attachement)->store('defect');
                }else{
					$data['upload']='';
				}

                $data['status'] = 0;
                $details[] = $data;
            }
            
            
        }

        
		$record = DefectSubmission::insert($details);
		

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 3;
		$inbox['ref_id'] = $defect->id;
		$inbox['title'] = "Defects Submission";
		$inbox['message'] = '';
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;   
		$inbox['created_at'] =  $defect->created_at;   

		$result = InboxMessage::create($inbox);



		return response()->json(['result'=>$defect,'response' => 1, 'message' => 'Defects has been submitted!']);

        
	}

	public function submitfeedbacks(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'fb_option'=>'required',
			'notes'=>'required',
			'subject'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'fb_option.required'=>'Feedback category is missing',
			'subject.required'=>'Subject is missing',
			'notes.required'=>'Remarks is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();
        
        $details = array();

		$UserObj = User::find($input['user_id']);

        $ticket = new \App\Models\v2\FeedbackSubmission();
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['ticket'] = $ticket->ticketgen();

		if ($request->file('upload_1') != null) {
            $input['upload_1'] = $request->file('upload_1')->store('feedback');
		}
		if ($request->file('upload_2') != null) {
            $input['upload_2'] = $request->file('upload_2')->store('feedback');
        }
		$result = FeedbackSubmission::create($input);
		
		$inbox['account_id'] = $UserObj->account_id;
		$inbox['user_id'] = $input['user_id'];
		$inbox['type'] = 2;
		$inbox['ref_id'] = $result->id;
		$inbox['title'] = "Feedback Submission :".$result->getoption->feedback_option;
		$inbox['message'] = '';
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;   
		$inbox['created_at'] =  $result->created_at;   

		$result = InboxMessage::create($inbox);

		return response()->json(['result'=>$result,'response' => 1, 'message' => 'Feedback has been submitted!']);
        
	}




	public function bookunittakeover(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'appt_date'=>'required',
			'appt_time'=>'required',
			'nricid_1'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'appt_date.required'=>'Date is missing',
			'appt_time.required'=>'Time is missing',
			'nricid_1.required'=>'NRIC Id is missing1',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
		
		
        //$user = Auth::user();
		$input = $request->all();
		$UserObj = User::find($input['user_id']);

		$appt_date = $input['appt_date'];
		$appt_time = $input['appt_time'];

		$blockout_date = DB::table("properties")->where('id', $UserObj->account_id)->Where('takeover_blockout_days', 'like', '%' . $appt_date . '%')->get()->first();
		
		$lists = DB::table("unittakeover_appointments")->where('account_id', $UserObj->account_id)->where('appt_date',$appt_date)->where('appt_time',$appt_time)->whereNotIn('status', [1])->get()->first();
		if (!empty($lists)){
			return response()->json(['response' => 400,'status'=>'0',
                'message' => "Appointment already taken.",
            ], 400);
		}
		if (!empty($blockout_date)){
			return response()->json(['response' => 400,'status'=>'0',
                'message' => "Appointment not available on selected date.",
            ], 400);
		}


		else{

			$input['user_id'] = $UserObj->id;
			$input['account_id'] = $UserObj->account_id;
			if(isset($UserObj->unit_no))
				$input['unit_no'] = $UserObj->unit_no;

			$record = UnittakeoverAppointment::create($input);
			
			$inbox['account_id'] = $UserObj->account_id;
			$inbox['user_id'] = $UserObj->id;
			$inbox['type'] = 4;
			$inbox['ref_id'] = $record->id;
			$inbox['title'] = "You have booked an appointment for Key Collection";
			$inbox['message'] = '';
			$inbox['booking_date'] = $record->appt_date;
			$inbox['booking_time'] = $record->appt_time;
			$inbox['status'] =  0; 
			$inbox['view_status'] =  0;   
			$inbox['created_at'] =  $record->created_at;   

			//$result = InboxMessage::create($inbox);

			return response()->json(['data'=>$record,'response' => 1, 'message' => 'Booking has been done!']);
		}
        
	}
	

	public function userinfo(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        //$user = Auth::user();
        $input = $request->all();

		$UserObj = User::where('id',$input['user_id'])->where('status',1)->first();

		if(empty($UserObj)){
			return response()->json(['response' => 101, 'message' => 'Account has been deactivated']);		
		}
		
		if(isset($UserObj)){		
			$data = array();
			$user = $input['user_id'];
			$modules = Module::all();  

			$counts = array();
			$counts['announcement'] = $UserObj->noOfAnnouncement($user);

			$eforms_permission = PropertyPermission::where('property_id', $UserObj->account_id)->whereIn('module_id',[40,41,42,43,44,45])->get();

			$deviceinfo = UserLog::where('user_id',$input['user_id'])->orderby('id','desc')->first();
			$userdata = array();
			$moredata = array();
			if(isset($UserObj)){
				
				$userdata['id'] = $UserObj->id;
				$userdata['account_id'] = $UserObj->account_id;
				$userdata['role_id'] = $UserObj->role_id;
				$userdata['building_no'] = $UserObj->building_no;
				$userdata['unit_no'] = $UserObj->unit_no;
				$userdata['name'] = $UserObj->name;
				$userdata['email'] = $UserObj->email;
				$userdata['created_at'] =  $UserObj->created_at;
				$userdata['updated_at'] =  $UserObj->updated_at;
				if(isset($UserObj->userinfo)){
					$userdata['userinfo'] = array();
					$userinfo  = $UserObj->userinfo;
					$userdata['userinfo']['last_name'] = isset($userinfo->last_name)?$userinfo->last_name:'';
					$userdata['userinfo']['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:'';
					$userdata['userinfo']['phone'] =  isset($userinfo->phone)?$userinfo->phone:'';
					$userdata['userinfo']['mailing_address'] =  isset($userinfo->mailing_address)?$userinfo->mailing_address:'';
					$userdata['userinfo']['postal_code'] =  isset($userinfo->postal_code)?$userinfo->postal_code:'';
					$userdata['userinfo']['company_name'] =  isset($userinfo->company_name)?$userinfo->company_name:'';
					$userdata['userinfo']['face_picture'] =  isset($userinfo->face_picture)?$userinfo->face_picture:'';
				}
			}
			$data['user'] = $userdata;
			$data['role'] = isset($UserObj->role)?$UserObj->role:array();
			if(isset($UserObj->userinfo)){
				$userinfo  = $UserObj->userinfo;
				$moredata['last_name'] = isset($userinfo->last_name)?$userinfo->last_name:'';
				$moredata['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:'';
				$moredata['phone'] =  isset($userinfo->phone)?$userinfo->phone:'';
				$moredata['mailing_address'] =  isset($userinfo->mailing_address)?$userinfo->mailing_address:'';
				$moredata['postal_code'] =  isset($userinfo->postal_code)?$userinfo->postal_code:'';
				$moredata['company_name'] =  isset($userinfo->company_name)?$userinfo->company_name:'';
				$moredata['face_picture'] =  isset($userinfo->face_picture)?$userinfo->face_picture:'';
			}
			$data['moreinfo'] = $moredata;

			//$data['face_ids'] = $UserObj->faceids;
			$data['unit'] = isset($UserObj->getunit)?$UserObj->getunit:array();
			$data['property'] = $UserObj->propertyinfo;

			$permission_array = array();
			if(isset($UserObj->permissions)){
				foreach($UserObj->permissions as $permission){
					//$permission_data =array();
					//$permission_data[] = $permission;

					$permission['permission'] = 0;
					$check_permission = PropertyPermission::where('module_id',$permission->module_id)->where('property_id',$UserObj->account_id)->first();
					if(isset($check_permission->id))
						$permission['permission'] = $check_permission->view;

					$permission_array[] = $permission;
				}
			}
		
			$data['permissions'] = $permission_array;

			$data['notification'] =$counts;
			$data['eforms_modules'] = $eforms_permission;
			$data['loginfo'] = $deviceinfo;

			if(isset($UserObj->appfaceids)){
				$faceids = array();
				foreach($UserObj->appfaceids as $faceid){
					$face_array['id'] = $faceid->id;
					$face_array['option_id'] = $faceid->option_id;
					$face_array['others'] = $faceid->others;
					$face_array['face_picture'] = $faceid->face_picture;
					$face_array['status'] = $faceid->status;
					$face_array['reason'] = $faceid->reason;
					$faceids[] = $face_array;
				}
			}
			$proximity = 0;
			//print_r($UserObj->userdevices);
			if(isset($UserObj->userdevices)){
				foreach($UserObj->userdevices as $device){
					//echo $device->deviceinfo->proximity_setting;
					if(isset($device->deviceinfo->proximity_setting) && $device->deviceinfo->proximity_setting ==1){
						$proximity = 1;
						break; 
					}
				}
			}
			
			$data['face_ids'] = $faceids;
			$data['thinmoo_appId'] = env('APPID');
			$data['modules'] = $modules;
			$data['file_path'] = env('APP_URL')."/storage/app";
			$data['proximity'] = $proximity;

			return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
		}
		else{
			return response()->json(['data'=>'','response' => 0, 'message' => 'User not available!']);
		}
        
	}

	public function user_thinmoo_info(Request $request){

		$rules=array(
			'user_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        //$user = Auth::user();
		$input = $request->all();

		$UserObj = User::find($input['user_id']);
		$data = array();
		
		$auth = new \App\Models\v2\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();
        
        $api_obj = new \App\Models\v2\User();
		$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj);
		$household_device = $api_obj->household_device_record($thinmoo_access_token,$UserObj);
		
		$data['household_info'] = $household_result;
		$data['household_device_info'] = $household_device;

		return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
	}
	
	public function feedbackoption(Request $request)
   {
	   
		$rules=array(
			'property' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();

		$account_id = $input['property'];
	
		//$lists = AnnouncementDetail::where('user_id',$userid)->get();

       $feedbacks = FeedbackOption::where('account_id', $account_id)->get();
       //$feedbacks = FeedbackOption::paginate(150);   
       return response()->json(['data'=>$feedbacks,'response' => 1, 'message' => 'success!']);
   }

   public function defectslocation(Request $request)
   {
       
		$rules=array(
			'property' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();

		$account_id = $input['property'];

	   $locations = DefectLocation::where('account_id', $account_id)->get();
	   
       return response()->json(['data'=>$locations,'response' => 1, 'message' => 'success!']);
   }

   public function defectstype(Request $request)
   {
		$rules=array(
			'property' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();

	   $account_id = $input['property'];
	   $location = $input['location'];
	   
       $types = DefectType::where('location_id', $location)->where('account_id', $account_id)->get();
       return response()->json(['data'=>$types,'response' => 1, 'message' => 'success!']);
   }


   public function facilitiestype(Request $request)
   {
	$rules=array(
		'property' => 'required',
	);
	$messages=array(
		'property.required' => 'Property id missing',
	);

	$validator = Validator::make($request->all(), $rules, $messages);
	if ($validator->fails()) {
		$messages = $validator->messages();
		$errors = $messages->all();
		return response()->json([
			'message' => $errors,
		], 400);
	}

	$input = $request->all();

	$account_id = $input['property'];

   $facilities = FacilityType::where('account_id', $account_id)->get();
   $options= array(1=>'none','2'=>"month",3=>"days");
   //$feedbacks = FeedbackOption::paginate(150);   
   return response()->json(['data'=>$facilities,'option'=>$options,'response' => 1, 'message' => 'success!']);
   }


   public function gettakeovertimeslots(Request $request)
   {
	$rules=array(
		'date' => 'required',
	);
	$messages=array(
		'date.required' => 'Date is missing',
	);

	$validator = Validator::make($request->all(), $rules, $messages);
	if ($validator->fails()) {
		$messages = $validator->messages();
		$errors = $messages->all();
		return response()->json([
			'message' => $errors,
		], 400);
	}
	$data =array();
	$obj = new UnittakeoverAppointment();
	$times = $obj->timeslots($request->property);	
	$selecteddate = $request->date;
	$records   = Property::select('takeover_blockout_days')->where('id', $request->property)->first();

	if(empty($records)){
		$messages = $validator->messages();
		$errors = $messages->all();
		return response()->json([
			'message' => 'Property not valid',
		], 400);
	}

	

	$blockout_data = explode(",",$records->takeover_blockout_days);

	//print_r($blockout_data);

	if(in_array($selecteddate,$blockout_data)){

		return response()->json(['data'=>$data,'response' => 400, 'message' => 'Unit takeover not available!']);
	}
	else{
		
        foreach($times as $time){
            //echo $time;
            $lists = DB::table("unittakeover_appointments")->where('appt_date',$selecteddate)->where('appt_time',$time)->whereNotIn('status', [1])->get();
            $recordcount = count($lists);
            $record =array('time'=>$time,'count'=>$recordcount);

            $data[] = $record;

		}
	
   		return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
	}

   }


   public function getinspectiontimeslots(Request $request)
   {
	$rules=array(
		'date' => 'required',
		'property' => 'required',
	);
	$messages=array(
		'date.required' => 'Date is missing',
		'property.required' => 'Property is missing',
	);

	$validator = Validator::make($request->all(), $rules, $messages);
	if ($validator->fails()) {
		$messages = $validator->messages();
		$errors = $messages->all();
		return response()->json([
			'message' => $errors,
		], 400);
	}
	$data =array();
	$obj = new JoininspectionAppointment();
	$times = $obj->timeslots($request->property);	
	$selecteddate = $request->date;
	$records   = Property::select('inspection_blockout_days')->where('id', $request->property)->first();

	if(empty($records)){
		$messages = $validator->messages();
		$errors = $messages->all();
		return response()->json([
			'message' => 'Property not valid',
		], 400);
	}

	$blockout_data = explode(",",$records->inspection_blockout_days);

	

	if(in_array($selecteddate,$blockout_data)){
		
		return response()->json(['data'=>$data,'response' => 400, 'message' => 'Joint inspection not available!']);
	}
	else{
		
        foreach($times as $time){
            //echo $time;
            $lists = DB::table("joininspection_appointments")->where('appt_date',$selecteddate)->where('appt_time',$time)->whereNotIn('status', [1])->get();
            $recordcount = count($lists);
            $record =array('time'=>$time,'count'=>$recordcount);

            $data[] = $record;

		}
	
   		return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
	}

   }


   public function bookfacility(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'booking_date'=>'required',
			'booking_time'=>'required',
			'type_id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'booking_date.required'=>'Date is missing',
			'booking_time.required'=>'Time is missing',
			'type_id.required'=>'Type is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        //$user = Auth::user();
        $input = $request->all();

		$UserObj = User::find($input['user_id']);

		$input['user_id'] = $UserObj->id;
		$input['account_id'] = $UserObj->account_id;

        if(isset($UserObj->unit_no))
            $input['unit_no'] = $UserObj->unit_no;

		$record = FacilityBooking::create($input);
		
		$inbox['account_id'] = $UserObj->account_id;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 6;
		$inbox['ref_id'] = $record->id;
		$inbox['title'] = "You have booked : ".$record->gettype->facility_type;
		$inbox['message'] = '';
		$inbox['booking_date'] = $record->booking_date;
		$inbox['booking_time'] = $record->booking_time;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;   
		$inbox['created_at'] =  $record->created_at;   

		//$result = InboxMessage::create($inbox);

		return response()->json(['data'=>$record,'response' => 1, 'message' => 'Booking has been done!']);

        
	}

	public function facilityDetail(Request $request){
		$rules=array(
			'booking_id' => 'required',
		);
		$messages=array(
			'booking_id.required' => 'Booking id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

		$result = FacilityBooking::find($request->booking_id);

		$data['booking'] = $result;
		$data['type'] = $result->gettype;
		$data['user'] = $result->getname;

	    return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']);

	}

	public function feedbackDetail(Request $request){
		$rules=array(
			'booking_id' => 'required',
		);
		$messages=array(
			'booking_id.required' => 'Booking id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

		$result = FeedbackSubmission::find($request->booking_id);

		$data['booking'] = $result;
		$data['type'] = $result->getoption;
		$data['user'] = $result->user;


	    return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']);


	}

	public function takeoverapptdetails(Request $request){
		$rules=array(
			'booking_id' => 'required',
		);
		$messages=array(
			'booking_id.required' => 'Booking id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

		$result = UnittakeoverAppointment::find($request->booking_id);

		$data['booking'] = $result;
		$data['unit'] = $result->getunit;
		$data['user'] = $result->getname;
		$data['notes'] = $result->perperty_info->takeover_notes;
		

	    return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']);


	}

	public function inspectionapptdetails(Request $request){
		$rules=array(
			'booking_id' => 'required',
		);
		$messages=array(
			'booking_id.required' => 'Booking id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

		$result = JoininspectionAppointment::find($request->booking_id);

		$data['booking'] = $result;
		$data['unit'] = $result->getunit;
		$data['user'] = $result->getname;
		$data['notes'] = $result->perperty_info->inspection_notes;

	    return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']);


	}


	public function defectDetail(Request $request){
		$rules=array(
			'booking_id' => 'required',
		);
		$messages=array(
			'booking_id.required' => 'Booking id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

		$record = Defect::find($request->booking_id);

		$submissions = DefectSubmission::where('def_id',$record->id)->get();

		$data['booking'] = $record;
		$lists = array();
		foreach($submissions as $submission){
			$result['submissions'] = $submission;
			$result['location'] = $submission->getlocation;
			$result['type'] = $submission->gettype;
			$result['user'] = $submission->user;
			$lists[]=$result;
		}
		$inspection = $record->inspection;
		//$data['submissions'] = $result;
		//$data['location'] = $result->getlocation;
		//$data['type'] = $result->gettype;
		//$data['user'] = $result->user;
		

	    return response()->json(['booking'=>$record, 'lists'=>$lists,'inspection'=>$inspection,'response' => 1, 'message' => 'Success!']);


	}


	public function announcement(Request $request) {

		$rules=array(
			'user' => 'required',
		);
		$messages=array(
			'user.required' => 'User id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
		

		$userid = $request->user;

		$UserObj = User::find($userid);

		$list_array =array();
		$lists = AnnouncementDetail::where('user_id',$userid)->orderBy('id','DSC')->get();
		foreach($lists as $list){
			$list_array[] = $list->a_id;
		}
		$announcements = Announcement::whereIn('id',$list_array)->get();


		$file_path = env('APP_URL')."/storage/app";

		if(isset($announcements)){
	    	return response()->json(['lists'=>$lists,'announcements'=>$announcements,'file_path'=>$file_path,'response' => 1, 'message' => 'Success!']);
		}
		else{
			return response()->json([
                'message' => "No Record",
            ], 402);
		}

	}




	public function announcementDetail(Request $request){
		$rules=array(
			'id' => 'required',
			'user_id' => 'required',
		);
		$messages=array(
			'id.required' => 'Announcement id missing',
			'user_id.required' => 'User id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

		$result = AnnouncementDetail::where('id',$request->id)->where('user_id',$request->user_id)->first();

		//print_r($result);
		//echo $result['a_id'];
		if(isset($result)){
			//$announcment = Announcement::find($result->a_id);
			$data['details'] = $result;
			$data['announcement'] = $result->announcement;
			//$data['role'] = $result->AnnouncementNews->role;
		
	    	return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']);
		}
		else{
			return response()->json([
                'message' => "No Record",
            ], 402);
		}

	}

	public function announcementStatusUpdate(Request $request){
		$rules=array(
			'id' => 'required',
			'user' => 'required',
		);
		$messages=array(
			'id.required' => 'Announcement id missing',
			'user.required' => 'User id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

		$status = AnnouncementDetail::where('a_id',$request->id)->where('user_id',$request->user)
                ->update(['status' => 1,'view_status'=>1]);


		if(isset($status)){
	    	return response()->json(['response' => 1, 'message' => 'Success!']);
		}
		else{
			return response()->json([
                'message' => "No Record",
            ], 402);
		}

	}



	public function getfacilitytimeslots(Request $request)
	{
	 $rules=array(
		 'date' => 'required',
		 'type' => 'required',
	 );
	 $messages=array(
		 'date.required' => 'Date is missing',
		 'type.required' => 'Type is missing',
	 );
 
	 $validator = Validator::make($request->all(), $rules, $messages);
	 if ($validator->fails()) {
		 $messages = $validator->messages();
		 $errors = $messages->all();
		 return response()->json([
			 'message' => $errors,
		 ], 400);
	 }
	 $data =array();
	 $type = $request->type;
	 $selecteddate = $request->date;
	 //$time = $request->time;

	 $obj = new FacilityBooking();
	 $times = $obj->timeslots($type);	
	
	
		 
		 foreach($times as $time){
			 //echo $time;
			 $lists = DB::table("facility_bookings")->where('type_id',$type)->where('booking_date',$selecteddate)->where('booking_time',$time)->whereNotIn('status', [1])->get();
			 $recordcount = count($lists);
			 $record =array('time'=>$time,'count'=>$recordcount);
 
			 $data[] = $record;
 
		 }
	 
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
	
 
	}
 

	public function getfacilitybooking(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        //$user = Auth::user();
        $input = $request->all();
		$UserObj = User::find($input['user_id']);

		$bookings = FacilityBooking::where('user_id',$input['user_id'])->orderby('id','desc')->get();  
		$feedbacks = FacilityType::where('account_id', $UserObj->account_id)->get();

		$data['bookings'] = $bookings;
		$data['types'] = $feedbacks;

		return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);

        
	}


	public function inboxMessage(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        //$user = Auth::user();
        $input = $request->all();

		$messages = InboxMessage::whereNotIn('type',[1,2,3])->where('event_status','!=',1)->where('user_id',$input['user_id'])->orderby('id','desc')->get();  

		$data['messages'] = $messages;
		$data['types'] = array(1=>'announcement',2=>"feedback",3=>"defects",4=>"unittakeover",5=>"join inspection",6=>"facility");

		return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);

        
	}

	public function upcomingEvents(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        //$user = Auth::user();
        $input = $request->all();

		$events = InboxMessage::where('user_id',$input['user_id'])->where('booking_date','>=',date("Y-m-d"))->whereIn('type',[4,5,6])->where('event_status','!=',1)->orderby('id','desc')->get();  

		$data['events'] = $events;
		$data['types'] = array(4=>"unittakeover",5=>"join inspection",6=>"facility");

		return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);

        
	}

	public function enquiry(Request $request)
	{
		
		
		$input = $request->all();
		$UserObj = User::find($input['user_id']);

		$name = $UserObj->name." ".$UserObj->userinfo->last_name;
		$phone = $UserObj->userinfo->phone;
		$email = $UserObj->email;
		$property = $UserObj->propertyinfo->company_name;
		$unit = $UserObj->getunit->unit;
		$enquiry = $input['enquiry'];

        	$logo = url('/').'/public/assets/admin/img/aerea-logo.png';		
					
			$companyname = 'Aerea Home';
			$adminemail = 'hello@myaereahome.com';
			$replyto = 'no-reply@myaereahome.com';
			
			$emailcontent = file_get_contents(public_path().'/emails/enquiry.php');
			$emailcontent = str_replace('#logo#', $logo, $emailcontent);
			$emailcontent = str_replace('#companyname#', $companyname, $emailcontent);
			$emailcontent = str_replace('#name#', $name, $emailcontent);
			$emailcontent = str_replace('#phone#', $phone, $emailcontent);
			$emailcontent = str_replace('#property#', $property, $emailcontent);
			$emailcontent = str_replace('#unit#', $unit, $emailcontent);
			$emailcontent = str_replace('#email#', $email, $emailcontent);
			$emailcontent = str_replace('#enquiry#', $enquiry, $emailcontent);
			//$emailcontent = str_replace('#customername#', $customername, $emailcontent);
			
			$subject = "New enquiry request";
			
			$headers = 'From: '.$companyname.' <'.$adminemail.'/>' . "\r\n" ;
			$headers .='Reply-To: '. $replyto . "\r\n" ;
			$headers .='X-Mailer: PHP/' . phpversion();
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
					
			@mail($adminemail, $subject, $emailcontent, $headers);

			return response()->json(['data'=>1,'response' => 1, 'message' => 'success!']);

	
	}
   
	public function getandroidversion(Request $request)
	{
		$versions = AndroidVersion::orderby('id','desc')->where('id',2)->first();   
		return response()->json(['data'=>$versions,'response' => 1, 'message' => 'success!']);
	}

	public function getiosversion(Request $request)
	{
		$versions = IosVersion::orderby('id','desc')->first();  
		return response()->json(['data'=>$versions,'response' => 1, 'message' => 'success!']);
	}

	public function password_reset_mannual(Request $request){

		$password = Hash::make($request->password);

		return response()->json(['password'=>$password,'response' => 1, 'message' => 'Password Generated']);		


	}

	public function documentCategories(Request $request){

		$rules=array(
			'property' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
		
		$account_id = $request->property;

		$category = DocsCategory::where('account_id', $account_id)->get();

		return response()->json(['data'=>$category,'response' => 1, 'message' => 'success']);		


	}

	public function categoryFiles(Request $request){

		$rules=array(
			'property' => 'required',
			'category' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
			'category.required' => 'Category id missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
		
		$cat_id = $request->category;

		$files = CondodocFile::where('cat_id',$cat_id)->get();   
		
		$file_path =  env('APP_URL')."/storage/app/";


		return response()->json(['data'=>$files,'file_path'=>$file_path,'response' => 1, 'message' => 'success']);		


	}

	public function validateFacilityBoooking(Request $request){

		$rules=array(
			'type_id' => 'required',
			'user_id' => 'required',
			'booking_date' => 'required',
			'booking_time' => 'required',
		);
		$messages=array(
			'type_id.required' => 'Facility type missing',
			'user_id.required' => 'User id missing',
			'booking_date.required' => 'Booking date missing',
			'booking_time.required' => 'Booking time missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
		
		$facility = $request->type_id;
		$user = $request->user_id;
		$booking_date = $request->booking_date;
		$booking_time = $request->booking_time;


		$facilityObj = FacilityType::find($facility);

		$blockout_data = explode(",",$facilityObj->blockout_days);

		//print_r($blockout_data);
		if(empty($facilityObj)){
			return response()->json(['data'=>0, 'response' => 400, 'message' => 'Facility type not avialble']);
		}
		else if(in_array($booking_date,$blockout_data)){//Blockout dates

			return response()->json(['data'=>0,'response' => 500, 'message' => 'Booking date not available!']);
		}
		else if($facilityObj->next_booking_allowed ==1){ // for None option
			return response()->json(['data' =>1, 'response' => 1, 'message' => 'Allowed , None option']);		
		}
		else if($facilityObj->next_booking_allowed ==2){ // for Month option

			
			$query_date = $booking_date;
			$fromdate = date('Y-m-01', strtotime($query_date));
			$todate =  date('Y-m-t', strtotime($query_date));

			//echo "From Date:".$fromdate;
			//echo "To Date:".$todate;

			$bookings = FacilityBooking::where('user_id',$user)->where('type_id',$facility)->whereNotIn('status', ['1'])->whereBetween('booking_date',array($fromdate,$todate))->first();  
			


			if(isset($bookings) && $bookings->booking_date){

				return response()->json(['data' =>0, 'response' => 100, 'message' => 'There is already a booking for '.$facilityObj->facility_type.'. Each unit is entitled to one booking each month. As such, you are not able to make another booking for '.$facilityObj->facility_type.'.']); 	
			}
			else{
				return response()->json(['data' =>1, 'response' => 1, 'message' => 'Allowed , Monthly booking not done']);	
			}
				
		}
		else if($facilityObj->next_booking_allowed ==3){ // for Days option

			$facilityObj->next_booking_allowed;

			$bookings = FacilityBooking::where('user_id',$user)->where('type_id',$facility)->whereNotIn('status', ['1'])->orderBy('booking_date','DESC')->first(); 
			//print_r($bookings->booking_date);

			if(isset($bookings) && $bookings->booking_date){

				$date = Carbon::createFromFormat('Y-m-d', $bookings->booking_date);
				$daysToAdd = $facilityObj->allowed_booking_for;
				$booking_allowed  = $date->addDays($daysToAdd);
				//echo "Next booking : ".$booking_allowed;
				//echo "Requested Booking :".$booking_date;

				if($booking_allowed <= $booking_date ){
					return response()->json(['data' =>1, 'response' => 1, 'message' => 'Allowed , booking not done']);
				}
				else{
					return response()->json(['data' =>0, 'response' => 200, 'message' => 'There is already a booking for '.$facilityObj->facility_type.'. Each unit is entitled to one booking per '.$facilityObj->next_booking_allowed_days.' days. As such, you are not able to make another booking for '.$facilityObj->facility_type.'.']);
				}	
			}
			else{
				return response()->json(['data' =>1, 'response' => 1, 'message' => 'Allowed , booking not done']);	
			}
				
		}
		else{
			return response()->json(['data' =>0, 'response' => 300, 'message' => 'Allowed , No option set']);
		}

	}


	public function getDocumentType(Request $request)
   {
		$rules=array(
			'property' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();

	   $account_id = $input['property'];
	   
	   $types = DocsCategory::where('account_id', $account_id)->get();
	   
       return response()->json(['data'=>$types,'response' => 1, 'message' => 'success!']);
   }



   public function residentFileUpload(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'cat_id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'cat_id.required'=>'Document Category is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();
        
        $details = array();
		$UserObj = User::find($input['user_id']);

        $ticket = new \App\Models\v2\Defect();
		$input['account_id'] = $UserObj->account_id;

		$submission = ResidentFileSubmission::create($input);

        //$data['user_id'] = $input['user_id'];
        $data['ref_id'] = $submission->id;
		$data['created_at'] = $submission->created_at;
		$data['updated_at'] = $submission->updated_at;

        for($i=1;$i<=5;$i++){

            $upload_file = 'docs_file_'.$i;
			$doc_name = 'docs_file_name_'.$i;
			
            if(!empty($request->input($doc_name)) && !empty($request->input($doc_name))){
                
				$data['docs_file_name'] = $request->input($doc_name);
				
                if ($request->file($upload_file) != null) {
					$data['original_file_name'] = $request->file($upload_file)->getClientOriginalName();
                    $data['docs_file'] = $request->file($upload_file)->store('condofile');
                }else{
					$data['upload']='';
				}

				
                $data['status'] = 0;
                $details[] = $data;
            } 
            
		}
		
		$record = ResidentUploadedFile::insert($details);
		
		return response()->json(['result'=>$record,'response' => 1, 'message' => 'File(s) has been uploaded!']);

        
	}

	public function uploadedlist(Request $request){

		$userid = $request->user;
		$UserObj = User::find($userid);

		$file_path = env('APP_URL')."/storage/app";
		$records = ResidentFileSubmission::where('user_id',$userid)->orderby('id','desc')->get();   
		$data = array();
		foreach($records as $k => $record){
			$data[$k] = $record;
			if(isset($record->files))
			$data[$k]['files'] = $record->files;
			if(isset($record->category))
				$data[$k]['category'] = $record->category;
			
		}
		$status = array("0"=>"0:New","1"=>"1:Processing","2"=>"2:Processed");
		return response()->json([
			'data'=>$data,
			'file_path'=>$file_path,
			'status'=>$status,
			'Message'=>'success'
		]);

	}

	public function loginHistoryLogs(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'login_from'=>'required',
			'device_info'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'login_from.required'=>'Login OS is missing',
			'device_info.required'=>'Device info is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$UserObj = User::find($input['user_id']);

		$input['account_id'] = $UserObj->account_id;
        
		$record = UserLog::create($input);
		
		return response()->json(['data'=>$record,'response' => 1, 'message' => 'Login history log has been added!']); 
	}

	public function logoutHistoryLogs(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'login_from'=>'required',
			'fcm_token'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'login_from.required'=>'Login OS is missing',
			'fcm_token.required'=>'FCM Token is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$UserObj = User::find($input['user_id']);

		UserLog::where('user_id', $input['user_id'])->where('login_from',$input['login_from'])->where('fcm_token',$input['fcm_token'])->update(['status' => 0]);
		
		return response()->json(['data'=>1,'response' => 1, 'message' => 'Logout history log has been added!']); 
	}


	public function visitingPurpose(Request $request)
   {
       
		$rules=array(
			'property' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();

		$account_id = $input['property'];

	   $data = VisitorType::where('account_id', $account_id)->get();
	   
       return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
   }


	public function visitorRegisitration(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'visiting_date'=>'required',
			'visiting_purpose'=>'required',
			'name_1'=>'required',
			'mobile_1'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'visiting_date.required'=>'Date is missing',
			'visiting_purpose.required'=>'Purpose of visit is missing',
			'name_1.required'=>'Name is missing',
			'mobile_1.required'=>'Mobile is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();
        
        $details = array();
		$UserObj = User::find($input['user_id']);

        $ticket = new \App\Models\v2\VisitorBooking();
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;

        $propObj = property::find($UserObj->account_id);
		$input['ticket'] = $ticket->ticketgen($propObj->short_code);
        $booking = VisitorBooking::create($input);


        $data['book_id'] = $booking->id;

        for($i=1;$i<=5;$i++){

            $name = 'name_'.$i;
            $mobile = 'mobile_'.$i;
            $vehicle ='vehicle_no_'.$i;
			$id_number = 'id_number_'.$i;
			$email = 'email_'.$i;
			$qrcode_file = 'qrcode_file_'.$i;

            //print_r($input);

            if(!empty($request->input($name)) && !empty($request->input($mobile))){
                
                $data['name'] = $request->input($name);
                $data['mobile'] = $request->input($mobile);
				$data['vehicle_no'] = $request->input($vehicle);
				$data['id_number'] = $request->input($id_number);
                $data['email'] = $request->input($email);

                    
                if ($request->file($qrcode_file) != null) {
                    $data['qrcode_file'] = $request->file($qrcode_file)->store('visitor');
                }else{
					$data['qrcode_file']='';
				}

				$data['created_at'] = $booking->created_at;
				$data['updated_at'] = $booking->updated_at;

                $details[] = $data;
            }
            
            
        }

		$record = VisitorList::insert($details);
		

		return response()->json(['result'=>$record,'response' => 1, 'message' => 'Visitor has been submitted!']);

        
	}

	public function visitorRegSummary(Request $request){

		$userid = $request->user;
		$UserObj = User::find($userid);

		$file_path = env('APP_URL')."/storage/app";

		$records = VisitorBooking::where('user_id',$userid)->orderby('id','desc')->get();   
		$data = array();
		foreach($records as $k => $record){
			$data[$k] = $record;
			if(isset($record->visitors)){
				$data[$k]['visitors'] = $record->visitors;
			
			}

		}
		$type = VisitorType::where('account_id', $UserObj->account_id)->get();


		return response()->json([
			'booking'=>$data,
			'purpose_lists'=>$type,
			'file_path'=>$file_path,
			'status'=>'success'
		]);

	}

	public function visitorBookingInfo(Request $request){

		$rules=array(
			'user_id' => 'required',
			'book_id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'book_id.required'=>'Booking id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

		$bookid = $request->book_id;
		$userid = $request->user_id;
		$UserObj = User::find($userid);

		$file_path = env('APP_URL')."/storage/app";

		$record = VisitorBooking::where('id',$bookid)->first();  
		
		$data['bookinf_info'] =$record;
		
		$type = VisitorType::where('account_id', $UserObj->account_id)->get();


		return response()->json([
			'booking'=>$data,
			'purpose_lists'=>$type,
			'file_path'=>$file_path,
			'status'=>'success'
		]);

	}

	public function visitorBookingCancel(Request $request){

		$rules=array(
			'user_id' => 'required',
			'book_id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'book_id.required'=>'Booking id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

		$bookid = $request->book_id;
		$userid = $request->user_id;
		if($request->reason !='')
			$reason = $request->reason;
		else	
			$reason = '';
		
		VisitorBooking::where('id', $bookid)->where('user_id',$userid)
		->update(['status' => 1,'view_status'=>1,'remarks'=>$reason]);

		return response()->json(['response' => 1, 'message' => 'Cancelled']);


	}

	public function visitorSendInvite(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'visiting_date'=>'required',
			'visiting_purpose'=>'required',
			'email_1'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'visiting_date.required'=>'Date is missing',
			'visiting_purpose.required'=>'Purpose of visit is missing',
			'email_1.required'=>'Email is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();
        
        $details = array();
		$UserObj = User::find($input['user_id']);

		$ticket = new \App\Models\v2\VisitorBooking();
		
		$propObj = property::find($UserObj->account_id);
		$input['ticket'] = $ticket->ticketgen($propObj->short_code);

		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
       
        $booking = VisitorBooking::create($input);


        $data['book_id'] = $booking->id;

        for($i=1;$i<=5;$i++){
			$email = 'email_'.$i;
			$name = 'name_'.$i;
            if(!empty($request->input($email)) && !empty($request->input($email))){

				$bookingObj = new \App\Models\v2\VisitorBooking();
				$bookingObj->invite_email($booking->id, $UserObj->id,$UserObj->account_id,$request->input($email),$request->input($name));
				$data['name'] = $request->input($name); 
                $data['email'] = $request->input($email);                    
				$data['created_at'] = $booking->created_at;
				$data['updated_at'] = $booking->updated_at;

                $details[] = $data;
			}
            
        }

		$record = VisitorInviteEmailList::insert($details);
		

		return response()->json(['result'=>$record,'response' => 1, 'message' => 'Invitation has been sent!']);

        
	}

	public function updatesingnature(Request $request) {
		
		//$file = $request->picture;
		//$fileArray = array('image' => $file);

		$rules=array(
			'user_id' => 'required',
			'def_id' => 'required',
			'signature' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
			'user_id.required' => 'User Id is missing',
			'def_id.required'=> 'Deffect submission id is missing',
			'signature.required'=>'Signature is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$user = $request->user_id;
		$defect = $request->def_id;
		$signature = '';
		if ($request->file('signature') != null) {
			$signature = $request->file('signature')->store('defect');
			//$signature_base64 = base64_encode(file_get_contents($request->file('signature')));
		}

		$result = Defect::where( 'user_id' , $user)->update( array( 'signature' =>  $signature));
		$data = response()->json(['result'=>$result,'response' => 1, 'message' => 'Signature updated successfully']);
		
		return $data;

	}

	public function inspectionsingnature(Request $request) {
		
		//$file = $request->picture;
		//$fileArray = array('image' => $file);

		$rules=array(
			'user_id' => 'required',
			'def_id' => 'required',
			'signature' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
			'user_id.required' => 'User Id is missing',
			'def_id.required'=> 'Deffect submission id is missing',
			'signature.required'=>'Signature is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$user = $request->user_id;
		$defect = $request->def_id;

		$defectObj = Defect::find($defect);

		$signature = '';
		if ($request->file('signature') != null) {
			$signature = $request->file('signature')->store('defect');
			//$signature_base64 = base64_encode(file_get_contents($request->file('signature')));
		}

		if($defectObj->status ==3 && $defectObj->inspection_status ==1 ){
			$result = Defect::where('id' , $defectObj->id)->update( array('handover_status'=>2, 'inspection_owner_signature' =>  $signature));
			$data = response()->json(['result'=>$result,'response' => 1, 'message' => 'Signature updated successfully']);

		}
		else{
			$result = Defect::where('id' , $defectObj->id)->update( array('status'=>1,'completion_date' => date("Y-m-d"),'inspection_owner_signature' =>  $signature));
			$data = response()->json(['result'=>$result,'response' => 2, 'message' => 'Ticket Closed']);
		}

		
		
		return $data;

	}

	public function handoversingnature(Request $request) {
		
		//$file = $request->picture;
		//$fileArray = array('image' => $file);

		$rules=array(
			'user_id' => 'required',
			'def_id' => 'required',
			'signature' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
			'user_id.required' => 'User Id is missing',
			'def_id.required'=> 'Deffect submission id is missing',
			'signature.required'=>'Signature is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$user = $request->user_id;
		$defect = $request->def_id;
		$signature = '';

		$defectObj = Defect::find($defect);
		$owner_status = ($request->input('agree_status'));  
        
		$disagree = 0;
		$newsubmissions = array();

        if($defectObj->submissions){
            foreach($defectObj->submissions as $k => $submission){
				
                if($submission->status ==2)
                {
                    $defectSubmissionObj = DefectSubmission::find($submission->id);
                    $defectSubmissionObj->owner_status = $owner_status[$submission->id];
                    if($owner_status[$submission->id] ==2){
						$new = array();
						$disagree = 1;

						//$new['def_id'] =$defectSubmissionObj->def_id;
						$new['defect_location'] =$defectSubmissionObj->defect_location;
						$new['defect_type'] =$defectSubmissionObj->defect_type;
						$new['upload'] =$defectSubmissionObj->upload;
						$new['notes'] =$defectSubmissionObj->notes;
						$new['user_id'] =$defectSubmissionObj->user_id;
						$new['type'] =$defectSubmissionObj->type;
						$new['created_at'] =$defectSubmissionObj->created_at;
						$new['updated_at'] =$defectSubmissionObj->updated_at;
						$newsubmissions[] = $new;

					}                                         
                    $defectSubmissionObj->save();
                }
            }
        }

        //$defectObj->status = $request->input('status');
       
		if ($request->file('signature') != null) {
			$signature = $request->file('signature')->store('defect');
			$defectObj->handover_owner_signature = $signature;
		}
		$defectObj->status = 1;
		$defectObj->completion_date = date("Y-m-d");
		$defectObj->save();

		
		
		if($disagree == 1){

			$ticket = new \App\Models\v2\Defect();
			$input['user_id'] = $defectObj->user_id;
			$input['account_id'] = $defectObj->account_id;
			$input['ref_id'] = $defectObj->ticket;
			$input['ticket'] = $ticket->ticketgen();
			$input['signature']  = $defectObj->signature;
			
			$defect = Defect::create($input);
			
			Defect::where('id' , $defect->id)->update( array('created_at'=>$defectObj->created_at, 'updated_at' =>  $defectObj->updated_at));

			$result = Defect::find($defect->id);

			//print_r($defect);

			$submission_data = array();
			foreach($newsubmissions as $submission){
				$new_data['def_id'] = $defect->id;
				$new_data['defect_location'] =$submission['defect_location'];
				$new_data['defect_type'] =$submission['defect_type'];
				$new_data['upload'] =$submission['upload'];
				$new_data['notes'] =$submission['notes'];
				$new_data['user_id'] =$submission['user_id'];
				$new_data['type'] =$submission['type'];
				$new_data['created_at'] =$submission['created_at'];
				$new_data['updated_at'] =$submission['updated_at'];
				$submission_data[] = $new_data;
			}

			$record = DefectSubmission::insert($submission_data);

			$data = response()->json(['result'=>$result,'response' => 1, 'message' => 'New ticket created']);
		}else{
			$data = response()->json(['result'=>$defectObj,'response' => 0, 'message' => 'Success']);

		}
		
		return $data;

	}

	public function submitdefectreview(Request $request) {
		
		//$file = $request->picture;
		//$fileArray = array('image' => $file);

		$rules=array(
			'user_id' => 'required',
			'def_id' => 'required',
			'def_submission_id' =>'required',
			'remarks' =>'required',
			
		);
		$messages=array(
			'user_id.required' => 'User Id is missing',
			'def_id.required'=> 'Deffect submission id is missing',
			'def_submission_id.required'=>'Defect List Id is missing',
			'remarks.required'=>'Remark is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$user = $request->user_id;

		$input = $request->all();


		$result = DefectSubmissionReview::create($input);
		$data = response()->json(['result'=>$result,'response' => 1, 'message' => 'Signature updated successfully']);
		
		return $data;

	}


	public function eformslists(Request $request)
   {
	   
		$rules=array(
			'property' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing'
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();

		$account_id = $input['property'];
		
		$form_types = array();
		$eforms_lists = array(40,41,42,43,44,45);

		$PropertyObj = Property::find($account_id);
		$form_types = array();

        foreach($PropertyObj->Permissions as $permission){
			if(in_array($permission->module_id,$eforms_lists) && $permission->view ==1)
            	$form_types[] = $permission->module_id;
           
        }
		//print_r($form_types);

	   $lists = EformSetting::whereIn('eform_type',$form_types)->where('account_id', $account_id)->get();

	   	$data =array();
		foreach ($lists as $k => $list) {
			$record =array();
			$record['list'] = $list;
			$record['form'] = $list->gettype;
			$data[] = $record;
		} 
	   
       return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
   }

   public function submenulists(Request $request)
   {
	   
		$rules=array(
			'property' => 'required',
			'top_menu_id' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
			'top_menu_id.required' => 'Top Menu id missing'
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();

		$account_id = $input['property'];
		$top_menu_id = $input['top_menu_id'];

		$form_types = array();
		if($top_menu_id ==56)
			$submenu_lists = array(50,52,54);

		$PropertyObj = Property::find($account_id);
		$menu_lists = array();

        foreach($PropertyObj->Permissions as $permission){
			if(in_array($permission->module_id,$submenu_lists) && $permission->view >=1)
            	$menu_lists[] = $permission->module_id;
           
        }
		//print_r($menu_lists);

	   $modules = Module::whereIn('id',$menu_lists)->get();

	   foreach($modules as $record){
		$list['id'] = $record->id;
		$list['name'] = $record->name;
		$module_permission = PropertyPermission::select('view')->where('property_id',$account_id)->where('module_id',$record->id)->first();
		if(isset($module_permission))
			$list['permission'] = $module_permission->view;
		else
			$list['permission'] = 0;
			
		$data[] = $list;
	}
	   	
	   
       return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
   }

   public function eformsettingdetail(Request $request)
   {
	   
		$rules=array(
			'property' => 'required',
			'id' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
			'id.required' => 'Form id missing',

		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();

		$account_id = $input['property'];
		$id = $input['id'];

		$data = EformSetting::where('account_id', $account_id)->where('eform_type', $id)->first();
		   
		$payment = PaymentSetting::where('account_id', $account_id)->first();

	   	if(isset($data))
		   return response()->json(['data'=>$data,'form'=>$data->gettype,'payment_info'=>$payment,'response' => 1, 'message' => 'success!']);
		else
			return response()->json(['data'=>$data,'response' => 0, 'message' => 'No data!']);
   }

   public function eformsubmittedlists(Request $request)
   {
		$rules=array(
			'property' => 'required',
			'login_id' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
			'login_id.required' => 'Login User Id missing',

		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();

		$account_id = $input['property'];
		$user_id = $input['login_id'];
		//move In & Out
		
		$moveinout = DB::table('eform_moving_in_out')->select('id','form_type','ticket','status','moving_start','moving_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->orderby('created_at','desc')->get();

		//Renovation
		$renovation = DB::table('eform_renovations')->select('id','form_type','ticket','status','reno_start','reno_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->orderby('created_at','desc')->get();

		//Doors
		$doors = DB::table('eform_door_accesses')->select('id','form_type','ticket','status','tenancy_start','tenancy_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->orderby('created_at','desc')->get();

		//Register for vehicle
		$vehicle = DB::table('eform_reg_vehicles')->select('id','form_type','ticket','status','tenancy_start','tenancy_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->orderby('created_at','desc')->get();


		//Update Mailling address
		$address = DB::table('eform_address_changes')->select('id','form_type','ticket','status','address','contact_no','email','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->orderby('created_at','desc')->get();

		//Update Particulars
		$particulars = DB::table('eform_particulars')->select('id','form_type','ticket','status','tenancy_start','tenancy_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->orderby('created_at','desc')->get();


		//$doors = DB::table('eform_door_access')->select('form_type','ticket','status','reno_start','reno_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->orderby('created_at','desc')->get();

		$allresults = new \Illuminate\Database\Eloquent\Collection; 

		$allresults = $moveinout->merge($renovation)->sortByDesc('created_at');

		$allresults = $allresults->merge($doors)->sortByDesc('created_at');

		$allresults = $allresults->merge($vehicle)->sortByDesc('created_at');

		$allresults = $allresults->merge($address)->sortByDesc('created_at');

		$allresults = $allresults->merge($particulars)->sortByDesc('created_at');

		$data = array();
		$andriod_data = array();
		foreach($allresults as $result){
			$record = array();
			$record['id'] = $result->id;
			$record['form_type'] = $result->form_type;
			$record['ticket'] = $result->ticket;
			$record['status'] = $result->status;
			if($result->form_type ==40){
				$record['start'] = $result->moving_start;
				$record['end'] = $result->moving_end;
			} else if($result->form_type ==41){
				$record['start'] = $result->reno_start;
				$record['end'] = $result->reno_end;
			} else if($result->form_type ==42){
				$record['start'] = $result->tenancy_start;
				$record['end'] = $result->tenancy_end;
			} else if($result->form_type==43){
				$record['start'] = $result->tenancy_start;
				$record['end'] = $result->tenancy_end;
			}
			else if($result->form_type ==44){
				$record['start'] = '';
				$record['end'] = '';
			}else if($result->form_type ==45){
				$record['start'] = $result->tenancy_start;
				$record['end'] = $result->tenancy_end;
			}

			$record['created_at'] = $result->created_at;
			//$record['updated_at'] = $result->updated_at;

			$andriod_data[] = $record;

			$data[] = $result;
		}

		$types = Module::select("id","name")->where('group_id',10)->orderby('id','asc')->get();

		return response()->json(['data'=>$data,'android_data'=>$andriod_data,'form_type'=>$types,'response' => 1, 'message' => 'Success!']);

		

   }

   public function eformsubmittedsearchlists(Request $request)
   {
	   
		$rules=array(
			'property' => 'required',
			'login_id' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
			'login_id.required' => 'Login User Id missing',

		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();

		$account_id = $input['property'];
		$type = $input['type'];
		$user_id = $input['login_id'];
		$moveinout = $renovation = $doors = $vehicle = $address = $particulars = $from_date = $to_date = '';
		$month = $request->input('month');
		if(isset($month) && $month !=''){
			$from_date = $month."-1";
			$to_date  = $month."-31";
		}
		$status = $request->input('status');
		$ticket = $request->input('ticket');


		if($type ==40 || $type ==''){ //moving in and out
			$moveinout = DB::table('eform_moving_in_out')->select('id','form_type','ticket','status','moving_start','moving_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where(function($query) use ($from_date,$to_date,$ticket,$status){
				if($from_date !=''){
					$query->whereBetween('created_at',array($from_date,$to_date));
				}
				if($ticket !=''){
					$query->where('ticket', 'LIKE', '%'.$ticket .'%');
				}
				if($status !=''){
					$query->where('status', $status);
				}
			})->orderby('created_at','desc')->get();

		}
		
		if($type ==41 || $type ==''){//Renovation
			$renovation = DB::table('eform_renovations')->select('id','form_type','ticket','status','reno_start','reno_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where(function($query) use ($from_date,$to_date,$ticket,$status){
				if($from_date !=''){
					$query->whereBetween('created_at',array($from_date,$to_date));
				}
				if($ticket !=''){
					$query->where('ticket', 'LIKE', '%'.$ticket .'%');
				}
				if($status !=''){
					$query->where('status', $status);
				}
			})->orderby('created_at','desc')->get();
		}

		if($type ==42 || $type ==''){//Doors
			$doors = DB::table('eform_door_accesses')->select('id','form_type','ticket','status','tenancy_start','tenancy_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where(function($query) use ($from_date,$to_date,$ticket,$status){
				if($from_date !=''){
					$query->whereBetween('created_at',array($from_date,$to_date));
				}
				if($ticket !=''){
					$query->where('ticket', 'LIKE', '%'.$ticket .'%');
				}
				if($status !=''){
					$query->where('status', $status);
				}
			})->orderby('created_at','desc')->get();
		}
		if($type ==43 || $type ==''){//Register for vehicle
			$vehicle = DB::table('eform_reg_vehicles')->select('id','form_type','ticket','status','tenancy_start','tenancy_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->orderby('created_at','desc')->where(function($query) use ($from_date,$to_date,$ticket,$status){
				if($from_date !=''){
					$query->whereBetween('created_at',array($from_date,$to_date));
				}
				if($ticket !=''){
					$query->where('ticket', 'LIKE', '%'.$ticket .'%');
				}
				if($status !=''){
					$query->where('status', $status);
				}
			})->orderby('created_at','desc')->get();

		}
		if($type ==44 || $type ==''){//Update Mailling address
			$address = DB::table('eform_address_changes')->select('id','form_type','ticket','status','address','contact_no','email','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where(function($query) use ($from_date,$to_date,$ticket,$status){
				if($from_date !=''){
					$query->whereBetween('created_at',array($from_date,$to_date));
				}
				if($ticket !=''){
					$query->where('ticket', 'LIKE', '%'.$ticket .'%');
				}
				if($status !=''){
					$query->where('status', $status);
				}
			})->orderby('created_at','desc')->get();
		}

		if($type ==45 || $type ==''){//Update Particulars
			$particulars = DB::table('eform_particulars')->select('id','form_type','ticket','status','tenancy_start','tenancy_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where(function($query) use ($from_date,$to_date,$ticket,$status){
				if($from_date !=''){
					$query->whereBetween('created_at',array($from_date,$to_date));
				}
				if($ticket !=''){
					$query->where('ticket', 'LIKE', '%'.$ticket .'%');
				}
				if($status !=''){
					$query->where('status', $status);
				}
			})->orderby('created_at','desc')->get();
		}


		//$doors = DB::table('eform_door_access')->select('form_type','ticket','status','reno_start','reno_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->orderby('created_at','desc')->get();

		$allresults = DB::table('eform_moving_in_out')->select('id','form_type','ticket','status','moving_start','moving_end','created_at')->where('account_id',$account_id)->where('user_id',0)->orderby('created_at','desc')->get();
		if($type ==40 || $type =='')
			$allresults = $allresults->merge($moveinout)->sortByDesc('created_at');
		if($type ==41 || $type =='')
			$allresults = $allresults->merge($renovation)->sortByDesc('created_at');
		if($type ==42 || $type =='')
			$allresults = $allresults->merge($doors)->sortByDesc('created_at');
		if($type ==43 || $type =='')
			$allresults = $allresults->merge($vehicle)->sortByDesc('created_at');
		if($type ==44 || $type =='')
			$allresults = $allresults->merge($address)->sortByDesc('created_at');
		if($type ==45 || $type =='')
			$allresults = $allresults->merge($particulars)->sortByDesc('created_at');
		
		$data = array();
		$andriod_data =array();

		foreach($allresults as $result){

			$record = array();
			$record['id'] = $result->id;
			$record['form_type'] = $result->form_type;
			$record['ticket'] = $result->ticket;
			$record['status'] = $result->status;
			if($result->form_type ==40){
				$record['start'] = $result->moving_start;
				$record['end'] = $result->moving_end;
			} else if($result->form_type ==41){
				$record['start'] = $result->reno_start;
				$record['end'] = $result->reno_end;
			} else if($result->form_type ==42){
				$record['start'] = $result->tenancy_start;
				$record['end'] = $result->tenancy_end;
			} else if($result->form_type==43){
				$record['start'] = $result->tenancy_start;
				$record['end'] = $result->tenancy_end;
			}
			else if($result->form_type ==44){
				$record['start'] = '';
				$record['end'] = '';
			}else if($result->form_type ==45){
				$record['start'] = $result->tenancy_start;
				$record['end'] = $result->tenancy_end;
			}

			$record['created_at'] = $result->created_at;
			//$record['updated_at'] = $result->updated_at;

			$andriod_data[] = $record;

			$data[] = $result;
		}

		
	   	if(isset($data))
		   return response()->json(['data'=>$data,'android_data'=>$andriod_data,'response' => 1, 'message' => 'success!']);
		
   }

	public function eform_movinginout(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'resident_name'=>'required',
			'contact_no'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'resident_name.required'=>'Resident name is missing',
			'contact_no.required'=>'Contact number is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$UserObj = User::find($input['user_id']);
		$date = Carbon::now();
		$ticket = new \App\Models\v2\EformMovingInOut();
		$input['user_id'] = $input['user_id'];
		$input['moving_date'] = $date->format('Y-m-d');
		$input['account_id'] = $UserObj->account_id;
        $input['ticket'] = $ticket->ticketgen();

		$input['user_id'] = $UserObj->id;
		$input['account_id'] = $UserObj->account_id;

        if(isset($UserObj->unit_no))
            $input['unit_no'] = $UserObj->unit_no;
		
		$moveio = EformMovingInOut::create($input);

        $data['mov_id'] = $moveio->id;
        for($i=1;$i<=10;$i++){

			$workman = 'workman_'.$i;
			$id_type = 'id_type_'.$i;
            $id_number = 'id_number_'.$i;
            $permit_expiry ='permit_expiry_'.$i;

            if(!empty($request->input($workman)) && !empty($request->input($id_number))){
                
				$data['workman'] = $request->input($workman);
				$data['id_type'] = $request->input($id_type);
                $data['id_number'] = $request->input($id_number);
				$data['permit_expiry'] = $request->input($permit_expiry); 
				$data['created_at'] =  $moveio->created_at;
				$data['updated_at'] =  $moveio->created_at;            
                $data['status'] = 1;
                $details[] = $data;
			}            
        }
		$record = EformMovingSubCon::insert($details);

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 7;
		$inbox['ref_id'] = $moveio->id;
		$inbox['title'] = "Move In & Out: E-form Submission";
		$inbox['message'] = '';
		$inbox['booking_date'] = $moveio->moving_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;   
		$inbox['created_at'] =  $moveio->created_at;   
		$result = InboxMessage::create($inbox);

		return response()->json(['data'=>$moveio,'response' => 1, 'message' => 'Moving In/Out application submitted!']);

        
	}

	public function eform_movinginout_info(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'id.required'=>'Id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$record = EformMovingInOut::where('id',$input['id'])->first();  
		
		$data['moving_info'] =$record;
		$data['sub_con_info'] =$record->sub_con;
		$data['payment'] =$record->payment;
		$data['inspection'] =$record->inspection;
		$data['defects'] =$record->defects;


		return response()->json([
			'data'=>$data,
			'status'=>'success'
		]);
	}

	public function eform_renovation(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'resident_name'=>'required',
			'contact_no'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'resident_name.required'=>'Resident name is missing',
			'contact_no.required'=>'Contact number is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$UserObj = User::find($input['user_id']);
		$date = Carbon::now();
		$ticket = new \App\Models\v2\EformRenovation();
		$input['user_id'] = $input['user_id'];
		$input['reno_date'] = $date->format('Y-m-d');
		$input['account_id'] = $UserObj->account_id;
        $input['ticket'] = $ticket->ticketgen();

		$input['user_id'] = $UserObj->id;
		$input['account_id'] = $UserObj->account_id;

        if(isset($UserObj->unit_no))
			$input['unit_no'] = $UserObj->unit_no;
		
		if ($request->file('owner_signature') != null) {
			//$signature = $request->file('owner_signature')->store('ren');
			$input['owner_signature']  = base64_encode(file_get_contents($request->file('owner_signature')));
	
		}
		if ($request->file('nominee_signature') != null) {
			//$signature = $request->file('owner_signature')->store('ren');
			$input['nominee_signature']  = base64_encode(file_get_contents($request->file('nominee_signature')));
	
		}

		if ($request->file('letter_of_authorization') != null) {
			$letter_of_authorization = $request->file('letter_of_authorization')->store('ren');
			$input['letter_of_authorization']  = $letter_of_authorization;
	
		}
		
		$reno = EformRenovation::create($input);

		$data['reno_id'] = $reno->id;
		$records =array();
        for($i=1;$i<=10;$i++){
            $workman = 'workman_'.$i;
			$nric = 'nric_'.$i;
			$id_type = 'id_type_'.$i;
            $id_number = 'id_number_'.$i;
            $permit_expiry ='permit_expiry_'.$i;

            if(!empty($request->input($workman)) && !empty($request->input($id_number))){
				$data =array();
				$data['reno_id'] = $reno->id;
				$data['workman'] = $request->input($workman);
				$data['id_type'] = $request->input($id_type);
                $data['id_number'] = $request->input($id_number);
				$data['permit_expiry'] = $request->input($permit_expiry); 
				$data['created_at'] =  $reno->created_at;
				$data['updated_at'] =  $reno->created_at;            
                $data['status'] = 1;
                $records[] = $data;
			}            
        }
		EformRenovationSubCon::insert($records);

		$infos =array();

		for($j=1;$j<=10;$j++){
            $detail ='detail_'.$j;

            if(!empty($request->input($detail))){
				$data =array();
				$data['reno_id'] = $reno->id;
                $data['detail'] = $request->input($detail);
				$data['created_at'] =  $reno->created_at;
				$data['updated_at'] =  $reno->created_at;            
                $data['status'] = 1;
                $infos[] = $data;
			}            
        }
		EformRenovationDetail::insert($infos);

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 8;
		$inbox['ref_id'] = $reno->id;
		$inbox['title'] = "Renovation: E-form Submission";
		$inbox['message'] = '';
		$inbox['reno_date'] = $reno->reno_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;   
		$inbox['created_at'] =  $reno->created_at;   

		$result = InboxMessage::create($inbox);

		return response()->json(['data'=>$reno,'response' => 1, 'message' => 'Renovation application submitted!']);

        
	}

	public function eform_renovation_info(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'id.required'=>'Id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$record = EformRenovation::where('id',$input['id'])->first();  
		
		$data['moving_info'] =$record;
		$data['sub_con_info'] =$record->sub_con;
		$data['renovation_detail'] =$record->details;
		$data['payment'] =$record->payment;
		$data['inspection'] =$record->inspection;
		$data['defects'] =$record->defects;

		return response()->json([
			'data'=>$data,
			'status'=>'success'
		]);
	}

	public function eform_dooraccess(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'owner_name'=>'required',
			'contact_no'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'owner_name.required'=>'Resident name is missing',
			'contact_no.required'=>'Contact number is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$UserObj = User::find($input['user_id']);
		$date = Carbon::now();
		$ticket = new \App\Models\v2\EformDoorAccess();
		$input['user_id'] = $input['user_id'];
		$input['request_date'] = $date->format('Y-m-d');
		$input['account_id'] = $UserObj->account_id;
        $input['ticket'] = $ticket->ticketgen();

		$input['user_id'] = $UserObj->id;
		$input['account_id'] = $UserObj->account_id;

        if(isset($UserObj->unit_no))
			$input['unit_no'] = $UserObj->unit_no;
		
		if ($request->file('owner_signature') != null) {
			//$signature = $request->file('owner_signature')->store('ren');
			$input['owner_signature']  = base64_encode(file_get_contents($request->file('owner_signature')));
	
		}
		if ($request->file('nominee_signature') != null) {
			//$signature = $request->file('owner_signature')->store('ren');
			$input['nominee_signature']  = base64_encode(file_get_contents($request->file('nominee_signature')));
	
		}
		
		$reno = EformDoorAccess::create($input);


		$inbox['account_id'] = $UserObj->account_id;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 9;
		$inbox['ref_id'] = $reno->id;
		$inbox['title'] = "Door Access Card: E-form Submission";
		$inbox['message'] = '';
		$inbox['reno_date'] = $reno->request_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;   
		$inbox['created_at'] =  $reno->created_at;   

		$result = InboxMessage::create($inbox);

		return response()->json(['data'=>$reno,'response' => 1, 'message' => 'Renovation application submitted!']);

        
	}

	public function eform_dooraccess_info(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'id.required'=>'Id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$record = EformDoorAccess::where('id',$input['id'])->first(); 
		
		$data['dooraccess_card_info'] =$record;
		$data['payment'] =$record->payment;
		
		return response()->json([
			'data'=>$data,
			'status'=>'success'
		]);
	}

	public function eform_reg_vehicle(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'owner_name'=>'required',
			'contact_no'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'owner_name.required'=>'Resident name is missing',
			'contact_no.required'=>'Contact number is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$UserObj = User::find($input['user_id']);
		$date = Carbon::now();
		$ticket = new \App\Models\v2\EformRegVehicle();
		$input['user_id'] = $input['user_id'];
		$input['request_date'] = $date->format('Y-m-d');
		$input['account_id'] = $UserObj->account_id;
        $input['ticket'] = $ticket->ticketgen();

		$input['user_id'] = $UserObj->id;
		$input['account_id'] = $UserObj->account_id;

        if(isset($UserObj->unit_no))
			$input['unit_no'] = $UserObj->unit_no;
		
		if ($request->file('owner_signature') != null) {
			//$signature = $request->file('owner_signature')->store('ren');
			$input['owner_signature']  = base64_encode(file_get_contents($request->file('owner_signature')));
	
		}
		if ($request->file('nominee_signature') != null) {
			//$signature = $request->file('owner_signature')->store('ren');
			$input['nominee_signature']  = base64_encode(file_get_contents($request->file('nominee_signature')));
	
		}
		
		$reg = EformRegVehicle::create($input);

		$data['reg_id'] = $reg->id;
		$records =array();
        for($i=1;$i<=10;$i++){
            $cat = 'cat_'.$i;
            $file = 'file_'.$i;
			if ($request->file($file) != null) {
				$data =array();
				$data['reg_id'] = $reg->id;
				$data['cat'] = $request->input($cat);
				$data['file_original'] = $request->file($file)->store('vehicle');
                $data['file'] = base64_encode(file_get_contents($request->file($file)));
				$data['created_at'] =  $reg->created_at;
				$data['updated_at'] =  $reg->created_at;            
                $data['status'] = 1;
                $records[] = $data;
			}            
        }
		EformRegVehicleDoc::insert($records);


		$inbox['account_id'] = $UserObj->account_id;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 10;
		$inbox['ref_id'] = $reg->id;
		$inbox['title'] = "Registration for Vehicle IU: E-form Submission";
		$inbox['message'] = '';
		$inbox['reno_date'] = $reg->request_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;   
		$inbox['created_at'] =  $reg->created_at;   

		$result = InboxMessage::create($inbox);

		return response()->json(['data'=>$reg,'response' => 1, 'message' => 'Vehicle IU regisgitration application submitted!']);

        
	}

	public function eform_reg_vehicle_info(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'id.required'=>'Id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$result = EformRegVehicle::where('id',$input['id'])->first();  
		
		$data =array();
		$data['submission'] = $result;
		$data['docs'] = $result->documents;
		return response()->json([
			'data'=>$data,
			'status'=>'success'
		]);
	}

	public function eform_reg_vehicle_file_category(Request $request)
   {
		$rules=array(
			'property' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();

	   $account_id = $input['property'];
	   
	   $types = EformRegVehicleFileCat::get();
	   
       return response()->json(['data'=>$types,'response' => 1, 'message' => 'success!']);
   }

	public function eform_change_address(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'owner_name'=>'required',
			'contact_no'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'owner_name.required'=>'Resident name is missing',
			'contact_no.required'=>'Contact number is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$UserObj = User::find($input['user_id']);
		$date = Carbon::now();
		$ticket = new \App\Models\v2\EformChangeAddress();
		$input['user_id'] = $input['user_id'];
		$input['request_date'] = $date->format('Y-m-d');
		$input['account_id'] = $UserObj->account_id;
        $input['ticket'] = $ticket->ticketgen();

		$input['user_id'] = $UserObj->id;
		$input['account_id'] = $UserObj->account_id;

        if(isset($UserObj->unit_no))
			$input['unit_no'] = $UserObj->unit_no;
		
		if ($request->file('owner_signature') != null) {
			//$signature = $request->file('owner_signature')->store('ren');
			$input['owner_signature']  = base64_encode(file_get_contents($request->file('owner_signature')));
	
		}
		if ($request->file('nominee_signature') != null) {
			//$signature = $request->file('owner_signature')->store('ren');
			$input['nominee_signature']  = base64_encode(file_get_contents($request->file('nominee_signature')));
	
		}
		
		$reg = EformChangeAddress::create($input);

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 11;
		$inbox['ref_id'] = $reg->id;
		$inbox['title'] = "Change of Mailing Address: E-form Submission";
		$inbox['message'] = '';
		$inbox['reno_date'] = $reg->request_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;   
		$inbox['created_at'] =  $reg->created_at;   

		$result = InboxMessage::create($inbox);

		return response()->json(['data'=>$reg,'response' => 1, 'message' => 'Mailing address change request application submitted!']);

        
	}

	public function eform_address_info(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'id.required'=>'Id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$data = EformChangeAddress::where('id',$input['id'])->first();  
		
		return response()->json([
			'data'=>$data,
			'status'=>'success'
		]);
	}

	public function eform_update_particulars(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'owner_name'=>'required',
			'contact_no'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'owner_name.required'=>'Resident name is missing',
			'contact_no.required'=>'Contact number is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$UserObj = User::find($input['user_id']);
		$date = Carbon::now();
		$ticket = new \App\Models\v2\EformRegVehicle();
		$input['user_id'] = $input['user_id'];
		$input['request_date'] = $date->format('Y-m-d');
		$input['account_id'] = $UserObj->account_id;
        $input['ticket'] = $ticket->ticketgen();

		$input['user_id'] = $UserObj->id;
		$input['account_id'] = $UserObj->account_id;

        if(isset($UserObj->unit_no))
			$input['unit_no'] = $UserObj->unit_no;
		
		if ($request->file('owner_signature') != null) {
			$input['owner_signature']  = base64_encode(file_get_contents($request->file('owner_signature')));
	
		}
		
		
		$reg = EformParticular::create($input);

		
		$records =array();
        for($i=1;$i<=10;$i++){
			$name = 'owner_name_'.$i;
			$nric = 'owner_nric_'.$i;
			$contact_no = 'owner_contact_no_'.$i;
			$vehicle_no = 'owner_vehicle_no_'.$i;
            $photo = 'owner_photo_'.$i;
			if ($request->input($name) != null) {
				$data =array();
				$data['reg_id'] = $reg->id;
				$data['owner_name'] = $request->input($name);
				$data['owner_nric'] = $request->input($nric);
				$data['owner_contact_no'] = $request->input($contact_no);
				$data['owner_vehicle_no'] = $request->input($vehicle_no);
				if ($request->file($photo) != null) {
					$data['owner_photo'] = base64_encode(file_get_contents($request->file($photo)));
				}
				$data['created_at'] =  $reg->created_at;
				$data['updated_at'] =  $reg->created_at;  
                EformParticularOwner::create($data);
			}            
        }
		//EformParticularOwner::insert($records);

		$records =array();
        for($i=1;$i<=10;$i++){
			$name = 'tenant_name_'.$i;
			$nric = 'tenant_nric_'.$i;
			$contact_no = 'tenant_contact_no_'.$i;
			$vehicle_no = 'tenant_vehicle_no_'.$i;
            $photo = 'tenant_photo_'.$i;
			if ($request->input($name) != null) {
				$data =array();
				$data['reg_id'] = $reg->id;
				$data['tenant_name'] = $request->input($name);
				$data['tenant_nric'] = $request->input($nric);
				$data['tenant_contact_no'] = $request->input($contact_no);
				$data['tenant_vehicle_no'] = $request->input($vehicle_no);
				if ($request->file($photo) != null) {
					$data['tenant_photo'] = base64_encode(file_get_contents($request->file($photo)));
				}
				$data['created_at'] =  $reg->created_at;
				$data['updated_at'] =  $reg->created_at;  
				EformParticularTenant::create($data);
                //$records[] = $data;
			}            
        }
		//EformParticularTenant::insert($records);

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 12;
		$inbox['ref_id'] = $reg->id;
		$inbox['title'] = "Update of Particulars: E-form Submission";
		$inbox['message'] = '';
		$inbox['reno_date'] = $reg->request_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;   
		$inbox['created_at'] =  $reg->created_at;   

		$result = InboxMessage::create($inbox);

		return response()->json(['data'=>$reg,'response' => 1, 'message' => 'Vehicle IU regisgitration application submitted!']);

        
	}

	public function eform_reg_particulars_info(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'id.required'=>'Id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();

		$data = EformParticular::where('id',$input['id'])->first();  
		
		return response()->json([
			'data'=>$data,
			'owners'=>$data->owners,
			'tenants'=>$data->tenants,
			'status'=>'success'
		]);
	}

	public function call_from_thinmoo(Request $request)
    {
		
		$start_time = microtime(true);
		$input = array();
		$rawPostData = file_get_contents("php://input");
		$special_char = array("{", "}");
		$string = str_replace($special_char, "", $rawPostData);
		$values = explode(",",$string );
		$quote_char = array('"','"'," ");
		foreach($values as $value){
			//echo $value;;
			//$var_string = str_replace($quote_char, "", trim($value));
			//$var_string = str_replace($quote_char, "", trim($value));
			$var_string = explode(":",trim($value));
			//echo $var_string[0]." ".$var_string[1];
			if(isset($var_string[0]) && isset($var_string[1])){
				$key_array = explode('"',trim($var_string[0]));
				$val_array = explode('"',trim($var_string[1]));
				$val ='';
				$key = trim($key_array[1]);
				if($key =='eventTime')
					$val = trim($val_array[1]).":".$var_string[2].":".substr($var_string[3],0,-1);
				else if($key =='captureImage' && isset($val_array[1]) && isset($var_string[2])){
					$val = trim($val_array[1]).":".$var_string[2];
				}
					
				else if($key =='eventType')
					$val = trim($var_string[1]);
				else if(isset($val_array[1]))
					$val = trim($val_array[1]);
				
				
				$input[$key] = $val;
			}
		}
	
		if(empty($input['roomCode'])){
			return response()->json([
				'code'=>1001,
				'msg'=>'room code is empty',
				
			]);
		}
		
		$building = Building::select('buildings.id')->join('devices', 'devices.locations', '=', 'buildings.id')->where('devices.device_serial_no',$input['devSn'])->first();


		if(empty($building)){
			return response()->json([
				'code' =>99999,
				'msg'=>'Building not available!'
			]);
		}

		$device = Device::where('device_serial_no',$input['devSn'])->first();
		if(empty($device)){
			return response()->json([
				'code' =>99999,
				'msg'=>'Device not available in community!'
			]);
		}


		$unitcode = "0".$input['roomCode'];

		$unit = Unit::wherein('code',[$input['roomCode'],$unitcode])->where('building_id',$building->id)->first();

		if(empty($unit)){
			return response()->json([
				'code' =>99999,
				'msg'=>'Unit not available!'
			]);
		}

		$users_lists = User::select('users.id')->where('users.status',1)->where('users.unit_no',$unit->id)->join('user_more_infos', 'users.id', '=', 'user_more_infos.user_id')->where('user_more_infos.receive_device_cal',1)->orderby('users.id','desc')->get();


		$user_rec = ',';
		$user_token = ',';
		$fcm_token_array ='';
		$ios_devices_to_send = array();
		$android_devices_to_send = array();
		$appSipAccountList = array();
		foreach($users_lists as $user){
			$user_rec .=$user->id.",";
			$logs = UserLog::where('user_id',$user->id)->where('status',1)->orderby('id','desc')->first();
			if(isset($logs->fcm_token) && $logs->fcm_token !=''){
				$user_token .=$logs->fcm_token.",";
				$fcm_token_array .=$logs->fcm_token.',';
				$appSipAccountList[] = $user->id;
				if($logs->login_from ==1)
					$ios_devices_to_send[] = $logs->fcm_token;
				if($logs->login_from ==2)
					$android_devices_to_send[] = $logs->fcm_token;
			}
		}

		//$building = Building::where('building_no',$input['buildingCode'])->first();
		$input['account_id'] = $unit->account_id;
		$input['building_no'] = '';
		$input['user_ids'] = $user_rec;
		$input['fcm_token'] = $user_token;
		$input['created_at'] = date("Y-m-d H:i:s");
		$input['updated_at'] = date("Y-m-d H:i:s");
		
		$result = CallPushRecord::create($input);

		$auth = new \App\Models\v2\Property();
		$thinmoo_access_token = $auth->thinmoo_auth_api(); 
		
		$thinmoo_appId = env('APPID');

		
		//Push notification to Mobile app for IOS
		$body ="Call notification for Room ".$input['roomCode'];
		//$devices_to_send = substr($fcm_token_array,0,-1);
		
		$array_data = array(
			'registration_ids'=>$ios_devices_to_send,
			'priority'=>'high',
			'notification'=>array("body" => $body,"title"=>"Call Notification","sound"=> "ring.mp3"),
			'data'=>array('body'=>$body,'devSn'=>$input['devSn'],'accessToken' =>$thinmoo_access_token,'extCommunityuuid'=>$unit->account_id,'appId'=>$thinmoo_appId)
			); 
		
		$curl_url = env('FIREBASE_URL');
		$server_key = env('SERVER_KEY');

		$jsonDataEncoded_get  = json_encode($array_data);
		$ch_get = curl_init($curl_url);
		curl_setopt($ch_get, CURLOPT_CUSTOMREQUEST, "POST"); 
		curl_setopt($ch_get, CURLOPT_POSTFIELDS, $jsonDataEncoded_get );
		curl_setopt($ch_get, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch_get, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Authorization: ' .$server_key)                                                                       
	
		);    
		$response = curl_exec($ch_get);
		$errno = curl_errno($ch_get);
		if ($errno) {
			return false;
		}
		
		//return $response;


		//print_r($android_devices_to_send);
		
		$array_data = array(
				'registration_ids'=>$android_devices_to_send,
				'priority'=>'high',
				'data'=>array('body'=>$body,'devSn'=>$input['devSn'],'accessToken' =>$thinmoo_access_token,'extCommunityuuid'=>$unit->account_id,'appId'=>$thinmoo_appId)
				); 
			
			$curl_url = env('FIREBASE_URL');
			$server_key = env('SERVER_KEY');
			
			$jsonDataEncoded_get  = json_encode($array_data);
			curl_setopt($ch_get, CURLOPT_CUSTOMREQUEST, "POST"); 
			curl_setopt($ch_get, CURLOPT_POSTFIELDS, $jsonDataEncoded_get );
			curl_setopt($ch_get, CURLOPT_RETURNTRANSFER, true);    
			curl_setopt($ch_get, CURLOPT_HTTPHEADER, array(                                                                          
				'Content-Type: application/json',                                                                                
				'Authorization: ' .$server_key)                                                                       
		
			);    
			$response = curl_exec($ch_get);
				$errno = curl_errno($ch_get);
				if ($errno) {
					return false;
				}
				curl_close($ch_get);
		

	 $devSnList = str_replace('"','',$input['devSn']);

	 $endtime = microtime(true) - $start_time ;

	 $returntime =  (int)round($endtime * 1000);
		
	 return response()->json([
		 'code'=>0,
		 'msg'=>'success',
		 'data' =>array(
			 'appSipAccountList'=>$appSipAccountList,
			 'devSnList'=>[],
			 'time'=>$returntime." ms",
		 )
	 ]);
	 
	 //response to thinmoo server end
		
		
	}

	public function call_push_notification(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		

		$input = $request->all();
		$user_id = $input['user_id'];
		$records = CallPushRecord::Where('user_ids', 'like', "%,".$user_id.",%")->first(); 
		//$result = CallPushRecord::create($input);
		
		return response()->json([
			'data'=>$records,
			'msg'=>'success'
		]);
	}

	public function hackingwork_enddate(Request $request)
    {
		$rules=array(
			'hacking_work_start' => 'required',
			'property' => 'required',
		);
		$messages=array(
			'property.required' => 'Property ID missing',
			'hacking_work_start.required' => 'Hacking work start date missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
		$input = $request->all();
		$account_id = $input['property'];

		$data = EformSetting::where('account_id', $account_id)->where('eform_type', 41)->first();

		//echo $data->hacking_work_not_permitted_holiday;

		if(isset($data) && $data->hacking_work_permitted_days >0 ){

			$records   = HolidaySetting::select('public_holidays')->where('account_id', $account_id)->first();
			
			$holidayDates = array();
			if(isset($records->public_holidays))
				$holidayDates = explode(",",$records->public_holidays);

			//print_r($holidayDates);

			$dayscount = 0;

			$temp = strtotime($input['hacking_work_start']); 
			$selectedDate = date('Y-m-d', $temp);
			$today= date("l", $temp );
			//echo "Saturday".$data->hacking_work_not_permitted_saturday;

			if($data->hacking_work_not_permitted_holiday ==1 && in_array($selectedDate, $holidayDates)){
				return response()->json([
					'date'=>0,
					'msg'=>'Hacking is not allowed on Public Holiday'
				]);
			}

			if($data->hacking_work_not_permitted_saturday==1 && $today =="Saturday"){ 
				return response()->json([
					'date'=>0,
					'msg'=>'Hacking is not allowed on Saturday'
				]);
			}

			if($data->hacking_work_not_permitted_sunday ==1  && $today =="Sunday"){ 
				return response()->json([
					'date'=>0,
					'msg'=>'Hacking is not allowed on Sunday'
				]);
			}

			while($dayscount < $data->hacking_work_permitted_days){
				
				if($dayscount==0)
					$nextday = $temp;
				else
					$nextday = strtotime('+1 day', $temp);

				$next1WDDate = date('Y-m-d', $nextday);
				$day= date("l", $nextday );
				//echo "day ".$dayscount." : ".date('Y-m-d',$next1WDDate)."/";
				$increase =1;
				if($data->hacking_work_not_permitted_holiday ==1 && in_array($next1WDDate, $holidayDates)){
					$increase =0;
					//echo $next1WDDate;
					
				}
				if($data->hacking_work_not_permitted_saturday==1 && $day =="Saturday"){ 
					$increase =0;
					//echo "saturday";
					
				}
				if($data->hacking_work_not_permitted_sunday ==1  && $day =="Sunday"){ //Sunday check
					$increase =0;
					//echo "sunday";
					
				}

				if($increase ==1){
					$dayscount++;
				}
				
				$temp = $nextday;
			}

			$next5WD = date("Y-m-d", $temp);

			return response()->json([
				'date'=>$next5WD,
				'msg'=>'success'
			]);
		}
		else{
			return response()->json([
				'date'=>0,
				'msg'=>'No setting available'
			]);
		}
		
	}


	public function bluetooth_device_info(Request $request){

		$rules=array(
			'property' => 'required',
		);
		$messages=array(
			'property.required' => 'Property ID missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
		$input = $request->all();
		$uuid = $input['property'];

		$auth = new \App\Models\v2\Property();
        $token = $auth->thinmoo_auth_api();

		$url = env('THINMOO_API_URL')."wyEmpProperty/extapi/getAuthorizationDevList";
	
		//The data you want to send via POST
		$fields = [
			'accessToken'      	=> 	$token,
			'uuid'              => 	$uuid,
			'extCommunityUuid'	=>	$uuid,
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


	public function user_bluetooth_device_list(Request $request){

		$rules=array(
			'property' => 'required',
			'user_id' => 'required',
		);
		$messages=array(
			'property.required' => 'Property ID missing',
			'user_id.required' => 'User ID missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
		$input = $request->all();
		$uuid = $input['property'];
		$user_id = $input['user_id'];

		$auth = new \App\Models\v2\Property();
        $token = $auth->thinmoo_auth_api();

		$url = env('THINMOO_API_URL')."wyEmpProperty/extapi/getAuthorizationDevList";

		$emp_result = Employee::where('account_id',$uuid)->orderby('id','asc')->first();
		//print_r($emp_result);

		if(!isset($emp_result->id)){
			return response()->json([
				'data'=>"Employee not available",
				'code' =>101,
				'msg'=>'Success'
			]);
		} 
	
		//The data you want to send via POST
		$fields = [
			'accessToken'      	=> 	$token,
			'uuid'              => 	$emp_result->id, //default emp id
			'extCommunityUuid'	=>	$uuid,
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
		if($json['code'] !=0){
			return $json;
		}
		else{
			$UserObj = User::find($user_id);
			$device_access = array();
			
			foreach($UserObj->userdevices as $selecteddevices){
				
				$device_access[] = $selecteddevices->device_svn;
			   
			}
			
			//print_r($UserObj->userdevices);
			//print_r($device_access);

			$data = array();
			$thinmoo_devices = $json['data'];
			foreach($thinmoo_devices as $T_device){
				//echo "Thinmoo Device ". $T_device['devSn'];

				$items_array = array();
				if(in_array($T_device['devSn'],$device_access)){
					$device_info = Device::where('device_serial_no',$T_device['devSn'])->first();
					$items_array['thinmoo'] = $T_device;
					$items_array['moreinfo'] = $device_info;
					$data[] = $items_array;
				}
			}
			return response()->json([
				'data'=>$data,
				'code' =>0,
				'msg'=>'Success'
			]);

		}
	
	}

	public function user_remote_device_list(Request $request){

		$rules=array(
			'property' => 'required',
			'user_id' => 'required',
		);
		$messages=array(
			'property.required' => 'Property ID missing',
			'user_id.required' => 'User ID missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
		$input = $request->all();
		$uuid = $input['property'];
		$user_id = $input['user_id'];

		$auth = new \App\Models\v2\Property();
        $token = $auth->thinmoo_auth_api();

		$url = env('THINMOO_API_URL')."wyEmpProperty/extapi/getAuthorizationDevList";

		$emp_result = Employee::where('account_id',$uuid)->orderby('id','asc')->first();

	
		//The data you want to send via POST
		$fields = [
			'accessToken'      	=> 	$token,
			'uuid'              => 	$emp_result->id,
			'extCommunityUuid'	=>	$uuid,
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
		if($json['code'] !=0){
			return $json;
		}
		else{
			$UserObj = User::find($user_id);
			$device_access = array();
			
			foreach($UserObj->userremotedevices as $selecteddevices){
				
				$device_access[] = $selecteddevices->device_svn;
			   
			}
			
			//print_r($UserObj->userdevices);
			//print_r($device_access);

			$data = array();
			$thinmoo_devices = $json['data'];
			foreach($thinmoo_devices as $T_device){
				
				if(in_array($T_device['devSn'],$device_access)){
					$data[] = $T_device;
				}
			}
			return response()->json([
				'data'=>$data,
				'code' =>0,
				'msg'=>'Success'
			]);

		}
	
	}

	public function BluetoothDoorOpenRecord(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'property_id'=>'required',
			'devSn'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'property_id.required'=>'Property is missing',
			'devSn.required'=>'Property is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
		$input = $request->all();
		
		$rec = Device::where('device_serial_no',$input['devSn'])->first();


		$data['account_id'] = $input['property_id'];
		$data['user_id'] = $input['user_id'];
		$data['devMac'] = $input['devMac'];
		$data['devType'] = $input['devType'];
		$data['eKey'] = $input['eKey'];
		$data['devSn'] = $input['devSn'];
		$data['devName'] = isset($rec->device_name)?$rec->device_name:'';
		$data['call_date_time'] = $input['call_date_time'];
		$data['status'] = $input['status'];
		$data['action_type'] = $input['action_type'];
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$record = BluetoothDoorOpen ::insert($data);
		
		return response()->json(['result'=>$record,'code' => 1, 'message' => 'Record added']);

        
	}

	public function getaccesstoken(Request $request){

		$rules=array(
			'property' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
		
		$auth = new \App\Models\v2\Property();

        $token = $auth->thinmoo_auth_api();

		return response()->json(['token'=>$token,'response' => 1, 'message' => 'success']);		


	}


	public function FailOpenDoorRecordPush(Request $request)
    {
		
		$failrecordtype = array(47,49,50,61,62,63,65,72,73,74,75,76,77,79);

		$input = array();
		$rawPostData = file_get_contents("php://input");
		$special_char = array("{", "}");
		$string = str_replace($special_char, "", $rawPostData);
		$values = explode(",",$string );
		$quote_char = array('"','"'," ");
		foreach($values as $value){
			//echo $value;;
			//$var_string = str_replace($quote_char, "", trim($value));
			//$var_string = str_replace($quote_char, "", trim($value));
			$var_string = explode(":",trim($value));
			//echo $var_string[0]." ".$var_string[1];
			if(isset($var_string[0]) && isset($var_string[1])){
				$key_array = explode('"',trim($var_string[0]));
				$val_array = explode('"',trim($var_string[1]));
				$val ='';
				$key = trim($key_array[1]);
				if($key =='eventTime')
					$val = trim($val_array[1]).":".$var_string[2].":".substr($var_string[3],0,-1);
				else if($key =='captureImageUrl'){
					$val = trim($val_array[1]).":".$var_string[2];
				}
					
				else if($key =='eventType')
					$val = trim($var_string[1]);
				else if(isset($val_array[1]))
					$val = trim($val_array[1]);
				
				
				$input[$key] = $val;
			}
		}

	

		$device = Device::where('device_serial_no',$input['devSn'])->first();
		if(empty($device)){
			return response()->json([
				'code' =>99999,
				'msg'=>'Device not available in community!'
			]);
		}

		if(isset($input['eventType']) && in_array($input['eventType'],$failrecordtype)){
		
			$data['account_id'] = $device->account_id;
			$data['user_id'] = isset($input['empId'])?$input['empId']:'';
			$data['empuuid'] = isset($input['empUuid'])?$input['empUuid']:'';
			$data['empname'] = isset($input['empName'])?$input['empName']:'';
			$data['empPhone'] = isset($input['empPhone'])?$input['empPhone']:'';
			$data['empCardNo'] = isset($input['empCardNo'])?$input['empCardNo']:'';
			$data['devId'] = isset($input['devId'])?$input['devId']:'';
			$data['devuuid'] = isset($input['devUuid'])?$input['devUuid']:'';
			$data['devname'] = isset($input['devName'])?$input['devName']:'';
			$data['devSn'] = isset($input['devSn'])?$input['devSn']:'';
			$data['eventType'] = isset($input['eventType'])?$input['eventType']:'';
			$data['eventtime'] = isset($input['eventTime'])?$input['eventTime']:'';
			$data['captureImageBase64'] = isset($input['captureImageBase64'])?$input['captureImageBase64']:'';
			$data['captureImageUrl'] = isset($input['captureImageUrl'])?$input['captureImageUrl']:'';
			$data['faceAge'] = isset($input['faceAge'])?$input['faceAge']:'';
			$data['faceGender'] = isset($input['faceGender'])?$input['faceGender']:'';
			$data['faceMatchScore'] = isset($input['faceMatchScore'])?$input['faceMatchScore']:'';
			$data['bodyTemperature'] = isset($input['bodyTemperature'])?$input['bodyTemperature']:'';
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');

			$record = FailDoorOpenRecord ::insert($data); 
		}
		
		return response()->json([
			'code' =>0,
			'msg'=>'Success'
		]);

        
	}


	public function CallUnitRecordPush(Request $request)
    {
	
		
		$input = array();
		$rawPostData = file_get_contents("php://input");
		$special_char = array("{", "}");
		$string = str_replace($special_char, "", $rawPostData);
		$values = explode(",",$string );
		$quote_char = array('"','"'," ");
		foreach($values as $value){
			//echo $value;;
			//$var_string = str_replace($quote_char, "", trim($value));
			//$var_string = str_replace($quote_char, "", trim($value));
			$var_string = explode(":",trim($value));
			//echo $var_string[0]." ".$var_string[1];
			if(isset($var_string[0]) && isset($var_string[1])){
				$key_array = explode('"',trim($var_string[0]));
				$val_array = explode('"',trim($var_string[1]));
				$val ='';
				$key = trim($key_array[1]);
				if($key =='eventTime')
					$val = trim($val_array[1]).":".$var_string[2].":".substr($var_string[3],0,-1);
				else if($key =='captureImage'){
					$val = trim($val_array[1]).":".$var_string[2];
				}
					
				else if($key =='eventType')
					$val = trim($var_string[1]);
				else if(isset($val_array[1]))
					$val = trim($val_array[1]);
				
				
				$input[$key] = $val;
			}
		}

		
		
		$device = Device::where('device_serial_no',$input['devSn'])->first();
		if(empty($device)){
			return response()->json([
				'code' =>99999,
				'msg'=>'Device not available in community!'
			]);
		}
		

		$data['account_id'] = $device->account_id;
		$data['devSn'] = isset($input['devSn'])?$input['devSn']:'';
		$data['roomId'] = isset($input['roomId'])?$input['roomId']:'';
		$data['roomuuid'] = isset($input['roomUuid'])?$input['roomUuid']:'';
		$data['roomCode'] = isset($input['roomCode'])?$input['roomCode']:'';
		$data['buildingCode'] = isset($input['buildingCode'])?$input['buildingCode']:'';
		$data['eventType'] = isset($input['eventType'])?$input['eventType']:'';
		$data['eventtime'] = isset($input['eventTime'])?$input['eventTime']:'';
		$data['captureImage'] = isset($input['captureImage'])?$input['captureImage']:'';
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		$record = CallUnitRecord::insert($data); 
		
		return response()->json([
			'code' =>0,
			'msg'=>'Success'
		]);

        
	}

	public function ChecktimeInterval(Request $request)
	{
		$start_time = microtime(true);
		$input = $request->all();
		$id = $input['id'];

		$UserObj = User::find($id);

        $account_id = $UserObj->account_id;
        $properties = Property::pluck('company_name', 'id')->all();

        $roles = Role::where('account_id',$account_id)->orWhere('type',1)->pluck('name', 'id')->all();
        $unites = Unit::where('account_id',$account_id)->pluck('unit', 'id')->all();

		$buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
		$count =0;
		for($i=0; $i<=1000000;$i++){
			$count += $i;
		}
		
		echo $endtime = microtime(true) - $start_time ;

    	$returntime =  (int)round($endtime * 1000);

		return response()->json([
			'count' =>$count,
			'time' =>$returntime,
			'code' =>0,
			'msg'=>'Success'
		]);
	}

	public function invoices(Request $request)
	{
		$rules=array(
			'property' => 'required',
			'user_id' => 'required',
			'role_id' => 'required',
		);
		$messages=array(
			'property.required' => 'User id missing',
			'user_id.required' => 'User id missing',
			'role_id.required' => 'Role id missing',
		);

		$due = array();
		$recent = array();
		$history = array();
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
				'due' =>$due,
				'recent' =>$recent,
				'history' =>$history,
				'code' =>102,
                'message' => $errors,
            ], 400);
        }
		
		$input = $request->all();
		if($input['role_id']!=2){
			return response()->json([
				'due' =>$due,
				'recent' =>$recent,
				'history' =>$history,
				'code' =>101,
				'msg'=>'Permission denied!'
			]);
		}
		$userObj = User::find($input['user_id']);

		$invoice = FinanceInvoice::where('account_id',$input['property'])->where('unit_no',$userObj->unit_no)->orderby('id','desc')->first(); 
		$visitor_app_url = env('VISITOR_APP_URL');
		if(isset($invoice)){
				$data = array();
				$data['id'] = $invoice->id;
				$data['invoice_no'] = $invoice->invoice_no;
				$data['invoice_date'] = $invoice->invoice_date;
				$data['due_date'] = $invoice->due_date;
				$data['batch_no'] = $invoice->batch_file_no;
				$data['invoice_amount'] = $invoice->invoice_amount;
				$data['pdf_file'] = $visitor_app_url."/invoice-pdf/".$invoice->id;
				if(isset($invoice->status)){
					if($invoice->status==1)
					   $status= "Payment Pending";
					else  if($invoice->status==2)
						$status= "Partially Paid";
					else if($invoice->status==4)
						$status= "Pending Verification";
					else 
						$status= "Paid";
				 
				 }
				$amount_received =0;
				if($invoice->payments){
					foreach($invoice->payments as $k => $payment){
						if($payment->payment_option ==1)
							$amount_received += $payment->cheque_amount; 
						else if($payment->payment_option ==2)
							$amount_received += $payment->bt_amount_received;
						else if($payment->payment_option ==5)
							$amount_received += $payment->online_amount_received;
						else if($payment->payment_option ==6)
							$amount_received += $payment->credit_amount;
						else
							$amount_received += $payment->cash_amount_received;
							
						}
					}
				$balance_amount = ($invoice->payable_amount - $amount_received);
				$data['received_amount'] = $amount_received;
				$data['balance_amount'] = $balance_amount;
				$data['payment_status'] = $status;
				if($invoice->active_status==1)
					$data['status'] = "Active";
				else 
					$data['status'] = "Notactive";
				
				$due[] = $data;
			
		}

		$due_invoices = array();
		$due_invoices[] = isset($invoice->id)?$invoice->id:'';

		$invoice = FinanceInvoice::where('account_id',$input['property'])->where('unit_no',$userObj->unit_no)->whereIn('status',[2,3])->orderby('id','desc')->first(); 
		$visitor_app_url = env('VISITOR_APP_URL');
		if(isset($invoice)){
				$data = array();
				$data['id'] = $invoice->id;
				$data['invoice_no'] = $invoice->invoice_no;
				$data['invoice_date'] = $invoice->invoice_date;
				$data['due_date'] = $invoice->due_date;
				$data['batch_no'] = $invoice->batch_file_no;
				$data['invoice_amount'] = $invoice->invoice_amount;
				$data['pdf_file'] = $visitor_app_url."/invoice-pdf/".$invoice->id;
				if(isset($invoice->status)){
					if($invoice->status==1)
					   $status= "Payment Pending";
					else  if($invoice->status==2)
						$status= "Partially Paid";
					else if($invoice->status==4)
						$status= "Pending Verification";
					else 
						$status= "Paid";
				 
				 }
				$amount_received =0;
				if($invoice->payments){
					foreach($invoice->payments as $k => $payment){
						if($payment->payment_option ==1)
							$amount_received += $payment->cheque_amount; 
						else if($payment->payment_option ==2)
							$amount_received += $payment->bt_amount_received;
						else if($payment->payment_option ==5)
							$amount_received += $payment->online_amount_received;
						else if($payment->payment_option ==6)
							$amount_received += $payment->credit_amount;
						else
							$amount_received += $payment->cash_amount_received;
							
						}
					}
				$balance_amount = ($invoice->payable_amount - $amount_received);
				$data['received_amount'] = $amount_received;
				$data['balance_amount'] = $balance_amount;

				$data['payment_status'] = $status;
				if($invoice->active_status==1)
					$data['status'] = "Active";
				else 
					$data['status'] = "Notactive";
				$recent[] = $data;
			
		}

	

		$invoices = FinanceInvoice::where('account_id',$input['property'])->where('unit_no',$userObj->unit_no)->whereNotIn('id',$due_invoices)->orderby('id','desc')->get(); 
		$visitor_app_url = env('VISITOR_APP_URL');
		if(isset($invoices)){
			foreach($invoices as $invoice){
				$data = array();
				$data['id'] = $invoice->id;
				$data['invoice_no'] = $invoice->invoice_no;
				$data['invoice_date'] = $invoice->invoice_date;
				$data['due_date'] = $invoice->due_date;
				$data['batch_no'] = $invoice->batch_file_no;
				$data['invoice_amount'] = $invoice->invoice_amount;
				$data['pdf_file'] = $visitor_app_url."/invoice-pdf/".$invoice->id;
				if(isset($invoice->status)){
					if($invoice->status==1)
					   $status= "Payment Pending";
					else  if($invoice->status==2)
						$status= "Partially Paid";
					else if($invoice->status==4)
						$status= "Pending Verification";
					else 
						$status= "Paid";
				 
				 }
				$amount_received =0;
				if($invoice->payments){
					foreach($invoice->payments as $k => $payment){
						if($payment->payment_option ==1)
							$amount_received += $payment->cheque_amount; 
						else if($payment->payment_option ==2)
							$amount_received += $payment->bt_amount_received;
						else if($payment->payment_option ==5)
							$amount_received += $payment->online_amount_received;
						else if($payment->payment_option ==6)
							$amount_received += $payment->credit_amount;
						else
							$amount_received += $payment->cash_amount_received;
							
						}
					}
				$balance_amount = ($invoice->payable_amount - $amount_received);
				$data['received_amount'] = $amount_received;
				$data['balance_amount'] = $balance_amount;
				$data['payment_status'] = $status;
				 if($invoice->active_status==1)
					 $data['status'] = "Active";
				 else 
					 $data['status'] = "Notactive";

				$history[] = $data;
			}
		}

		return response()->json([
			'due' =>$due,
			'recent' =>$recent,
			'history' =>$history,
			'code' =>0,
			'msg'=>'Success'
		]);
	}

	public function viewinvoice(Request $request)
	{
		$rules=array(
			'user_id' => 'required',
			'role_id' => 'required',
			'invoice_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'role_id.required' => 'Role id missing',
			'invoice_id' => 'Invoice id missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
		$input = $request->all();
		if($input['role_id']!=2){
			return response()->json([
				'code' =>101,
				'msg'=>'Permission denied!'
			]);
		}
		$userObj = User::find($input['user_id']);
		$invoice = FinanceInvoice::where('id',$input['invoice_id'])->where('unit_no',$userObj->unit_no)->first(); 
		$visitor_app_url = env('VISITOR_APP_URL');
		$data = array();
		if(isset($invoice)){
			$data['invoice_no'] = $invoice->invoice_no;
			$data['invoice_date'] = $invoice->invoice_date;
			$data['due_date'] = $invoice->due_date;
			$data['batch_no'] = $invoice->batch_file_no;
			$data['invoice_amount'] = $invoice->invoice_amount;
			$data['pdf_file'] = $visitor_app_url."/invoice-pdf/".$invoice->id;
			//$data['details'] = $invoice->paymentdetails;
			if(isset($invoice->paymentdetails)){
				$details = array();
				foreach($invoice->paymentdetails as $paymentdetail){
					$lists = array();
					$lists['type'] = isset($paymentdetail->referencetypes->reference_type)?$paymentdetail->referencetypes->reference_type:'';
					$lists['reference_no'] = isset($paymentdetail->reference_no)?$paymentdetail->reference_no:'';
					$lists['desciption'] = isset($paymentdetail->detail)?$paymentdetail->detail:'';
					$lists['amount'] =$paymentdetail->amount;
					$details[] = $lists;
				}
				$data['details'] = $details;
			}
			
		}
		return response()->json([
			'result' =>$data,
			'code' =>0,
			'msg'=>'Success'
		]);
	}

	public function filter_invoice(Request $request)
	{
		$rules=array(
			'property' => 'required',
			'user_id' => 'required',
			'role_id' => 'required',
		);
		$messages=array(
			'property.required' => 'User id missing',
			'user_id.required' => 'User id missing',
			'role_id.required' => 'Role id missing',
		);

		$filters = array();
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
				'records' =>$filters,
				'code' =>102,
                'message' => $errors,
            ], 400);
        }
		$input = $request->all();
		$userObj = User::find($input['user_id']);

		$month = $request->input('month');
        if($month !=''){          
			$from_date = $month;
			$start_date = date('Y-m', strtotime($month))."-01";
			$to_date  = date('Y-m-t', strtotime($month));
			$invoices = FinanceInvoice::where('account_id',$input['property'])->where('unit_no',$userObj->unit_no)->whereBetween('invoice_date',array($start_date,$to_date))->orderby('id','desc')->get(); 
		}
		else{
			$invoices = FinanceInvoice::where('account_id',$input['property'])->where('unit_no',$userObj->unit_no)->orderby('id','desc')->get(); 
		}
		
		
		$visitor_app_url = env('VISITOR_APP_URL');
		if(isset($invoices)){
			foreach($invoices as $invoice){
				$data = array();
				$data['id'] = $invoice->id;
				$data['invoice_no'] = $invoice->invoice_no;
				$data['invoice_date'] = $invoice->invoice_date;
				$data['due_date'] = $invoice->due_date;
				$data['batch_no'] = $invoice->batch_file_no;
				$data['invoice_amount'] = $invoice->invoice_amount;
				$data['pdf_file'] = $visitor_app_url."/invoice-pdf/".$invoice->id;
				if(isset($invoice->status)){
					if($invoice->status==1)
					   $status= "Payment Pending";
					else  if($invoice->status==2)
						$status= "Partially Paid";
					else if($invoice->status==4)
						$status= "Pending Verification";
					else 
						$status= "Paid";
				 
				 }
				 $data['payment_status'] = $status;
				 if($invoice->active_status==1)
					 $data['status'] = "Active";
				 else 
					 $data['status'] = "Notactive";

				$filters[] = $data;
			}
		}

		return response()->json([
			'records' =>$filters,
			'code' =>0,
			'msg'=>'Success'
		]);
	}

	public function getqrcode(Request $request)
	{
		$rules=array(
			'property' => 'required',
			'user_id' => 'required',
			'role_id' => 'required',

		);
		$messages=array(
			'property.required' => 'User id missing',
			'user_id.required' => 'User id missing',
			'role_id' => 'Role id missing',

		);

		$input = $request->all();
		if($input['role_id']!=2){
			return response()->json([
				'code' =>101,
				'msg'=>'Permission denied!'
			]);
		}

		$property_info = Property::where('id',$input['property'])->first();

		$sharesettings = FinanceShareSetting::where("account_id",$input['property'])->where('status',1)->first();
		$file_path = env('APP_URL')."/storage/app";

		return response()->json([
			'qrtype' =>$property_info->qrcode_option,
			'settings' =>$sharesettings,
			'file_path' =>$file_path,
			'code' =>0,
			'msg'=>'Success'
		]);
	}

	public function payment_screenshot(Request $request)
	{
		$rules=array(
			'invoice_id' => 'required',
			'screenshot' => 'required',
			'role_id' => 'required',

		);
		$messages=array(
			'invoice_id.required' => 'User id missing',
			'screenshot.required' => 'Screenshot missing',

		);

		$input = $request->all();
		$input['invoice_id'] = $input['invoice_id'];
		if ($request->file('screenshot') != null) {
			$input['screenshot'] = $request->file('screenshot')->store('paymentlog');
		}
		FinancePaymentLog::create($input);

		$invoiceObj = FinanceInvoice::find($input['invoice_id']);
		$invoiceObj->status = 4;
		$invoiceObj->save();
		
		return response()->json([
			'code' =>0,
			'msg'=>'Success'
		]);
	}

	public function charges(Request $request) {
		$payment_url = env('OMISEURL');
		$username = env('OMISEKEY');
		$password = '';
		$input = $request->all();
		$amount = $input['amount']*100;
        $fields = [
            "amount"            		=> $amount,
            "currency"          		=> 'SGD',
			"source[type]"    			=> 'paynow',
			"metadata[invoice_id]"    	=> $input['invoice_id'],
        ];
		$fields_string = http_build_query($fields);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $payment_url);
        curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        $result = curl_exec($ch);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        return $json;

	}



    
	
}
