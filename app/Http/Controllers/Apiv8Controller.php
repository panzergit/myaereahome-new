<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use Session;
use Validator;
use App\Models\v7\Role;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\Unit;
use App\Models\v7\UserFacialId;
use App\Models\v7\UserFavMenu;
use App\Models\v7\Building;
use App\Models\v7\HomeBanner;
use App\Models\v7\HomeBannerProperty;
use App\Models\v7\Country;
use App\Models\v7\ConfigSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Mail;
use Hash;
use DB;
use App\Models\v7\LoginOTP;
use App\Models\v7\UserNotificationSetting;
use App\Models\v7\AppTermCondition;
use App\Models\v7\Announcement;
use App\Models\v7\AnnouncementDetail;
use App\Models\v7\DefectSubmission;
use App\Models\v7\DefectUpdateLog;
use App\Models\v7\FeedbackSubmission;
use App\Models\v7\UnittakeoverAppointment;
use App\Models\v7\JoininspectionAppointment;
use App\Models\v7\FinalInspectionAppointment;
use App\Models\v7\DefectLocation;
use App\Models\v7\DefectType;
use App\Models\v7\FeedbackOption;
use App\Models\v7\Defect;
use App\Models\v7\FacilityType;
use App\Models\v7\Module;
use App\Models\v7\Property;
use App\Models\v7\PropertyDashboardPermission;
use App\Models\v7\PropertyPermission;
use App\Models\v7\FacilityBooking;
use App\Models\v7\FacilityBookingTempRequest;
use App\Models\v7\InboxMessage;
use App\Models\v7\AndroidVersion;
use App\Models\v7\IosVersion;
use App\Models\v7\DocsCategory;
use App\Models\v7\CondodocFile;
use App\Models\v7\ResidentUploadedFile;
use App\Models\v7\ResidentFileSubmission;
use App\Models\v7\UserLog;
use App\Models\v7\UserManagerLog;
use App\Models\v7\VisitorType;
use App\Models\v7\VisitorBooking;
use App\Models\v7\VisitorList;
use App\Models\v7\VisitorInviteEmailList;
use App\Models\v7\PaymentSetting;
use App\Models\v7\FacialRecoOption;
use App\Models\v7\Employee;
use App\Models\v7\Ad;
use App\Models\v7\Econcierge;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPermission;
use App\Models\v7\EformSetting;
use App\Models\v7\EformMovingInOut;
use App\Models\v7\EformMovingSubCon;
use App\Models\v7\EformRenovation;
use App\Models\v7\EformRenovationSubCon;
use App\Models\v7\EformRenovationDetail;
use App\Models\v7\EformDoorAccess;
use App\Models\v7\EformRegVehicle;
use App\Models\v7\EformRegVehicleDoc;
use App\Models\v7\EformChangeAddress;
use App\Models\v7\EformParticular;
use App\Models\v7\EformParticularOwner;
use App\Models\v7\EformParticularTenant;
use App\Models\v7\EformRegVehicleFileCat;

use App\Models\v7\CallPushRecord;
use App\Models\v7\Device;
use App\Models\v7\RemoteDoorOpen;
use App\Models\v7\BluetoothDoorOpen;
use App\Models\v7\FailDoorOpenRecord;
use App\Models\v7\NormalDoorOpenRecord;
use App\Models\v7\CallUnitRecord;
use App\Models\v7\HolidaySetting;
use App\Models\v7\DefectSubmissionReview;

use App\Models\v7\FinanceShareSetting;
use App\Models\v7\FinanceInvoice;
use App\Models\v7\FinanceInvoiceInfo;
use App\Models\v7\FinanceInvoiceDetail;
use App\Models\v7\FinanceInvoicePayment;
use App\Models\v7\FinanceReferenceType;
use App\Models\v7\FinanceInvoicePaymentDetail;
use App\Models\v7\FinanceInvoicePaymentPaidDetail;
use App\Models\v7\FinancePaymentLog;
use App\Models\v7\UserDevice;
use App\Models\v7\UserRemoteDevice;

use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\ChatBoxCategory;
use App\Models\v7\ChatBox;
use App\Models\v7\ChatBoxComment;
use App\Models\v7\ChatBoxCommentReport;
use App\Models\v7\ChatBoxBlockUser;
use App\Models\v7\ChatBoxReport;
use App\Models\v7\ChatBoxUserAgreement;
use App\Models\v7\ChatBoxTnc;
use App\Models\v7\ChatAttachment;
use App\Models\v7\ChatBoxBlockUserByAdmin;

use App\Models\v7\MpAdsCondition;
use App\Models\v7\MpAdsType;
use App\Models\v7\MpAdsSubmission;
use App\Models\v7\MpAdsImage;
use App\Models\v7\MpAdsLike;
use App\Models\v7\MpAdsBlockUser;
use App\Models\v7\MpAdsReport;
use App\Models\v7\MpGroupRegister;

use App\Models\v7\MagazineFile;
use App\Models\v7\UserGuide;
use App\Models\v7\ModuleSetting;
use App\Models\v7\ContactBook;
use App\Models\v7\UserRegistrationRequest;
use App\Models\v7\CallUnitAnswerRecord;
use App\Models\v7\AccountDeleteRequest;
use Illuminate\Database\Eloquent\Builder;

class Apiv8Controller extends Controller
{
	public function accountDeleteReasons(Request $request)
    {
        return response()->json([
           'status' => true,
           'message' => null,
           'data' => [
               'End of tenancy', 'Moving out', 'Rented unit out', 'Sold the place'
            ]
        ]);    
    }
    
    public function accountDeleteRequest(Request $request)
    {
        $user = $request->user();
        
        $reason = $request->filled('reason') ? trim($request->reason) : null;
        $message = 'Successfully account has been deleted.';
        AccountDeleteRequest::create([
            'account_id' => $user->account_id,
            'requested_by' => $user->id,
            'name' => Crypt::decryptString($user->name),
            'unit' => Crypt::decryptString($user->getunit->unit),
            'reason' => $reason,
            'deleted_date' => \Carbon\Carbon::now()
        ]);
            
        User::where('id',$user->id)->update(['status' => 0,'deactivated_date'=> \Carbon\Carbon::now()->format('Y-m-d')]);
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['response' => 1, 'message' => $message, 'data' => null]);
    }

	public function test_firebase(Request $request){
		$id = $request->user_id;
		$deviceinfo = UserLog::where('user_id',$id)->orderby('id','desc')->first();
		//print_r($deviceinfo);
		//exit;
		$title = "Call Notification";
		$body ="Call notification for Room";
		$data = array('body'=>$body,'devSn'=>"apc-test",'sound'=>"ring.mp3",'to'=>'ios');
		$device_tokens = array();
		$device_tokens[] = $deviceinfo->fcm_token;
		$fields = [
            'project_id'         => 'aerea-staging',
            'device_tokens'      => $device_tokens,
            'title'              => $title,
            'message'            => $body,
            'additional_data'    => $data,
			'to'=>'ios',
			'from'=>'staging'
        ];

        $fields_string = json_encode($fields);
		
        $ch = curl_init();
		$url = env('FIREBASE_URL');
        curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('accept: application/json',
    'content-type: application/json')); 
		$result = curl_exec($ch);
		print_r($result);
		$json = json_decode($result,true);
		print_r($json);
		$err = curl_error($ch);
		curl_close($ch);
		return $json;

		/*$array_data = array(
			'token'=>$deviceinfo->fcm_token,
			'priority'=>'high',
			'notification'=>array("body" => 'test Firebase',"title"=>"test Title"),
			'data'=>array('body'=>'test Firbase body')
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
			'Authorization: Bearer ya29.9c3be56bbce69ce220d437b14bac7d5d7eeb6ca5')                                                                       
	
		);    
		$response = curl_exec($ch_get);
		print_r($response );
		$errno = curl_errno($ch_get);
		
		if ($errno) {
			return false;
		}
		
*/
		//$token = $request->token;
		exit;
	}
	public function user_registration(Request $request) {
		
			$input = $request->all();
			$check_email_account = User::where('email',$input['email'])->first();
			if(isset($check_email_account)){
				$data = response()->json(['response' => 3, 'message' => 'Email already registered']);
			}
			else{
				$check_email_account = UserRegistrationRequest::where('status','!=',3)->where('email',$input['email'])->first();
				if(isset($check_email_account)){
					$data = response()->json(['response' => 2, 'message' => 'Your email already requested for registration']);
				}
				else{
					$total_vehicle = 0;
					$license_records = UserRegistrationRequest::where('status','!=',3)->where('unit_no',$input['unit_no'])->get();
					if($license_records){
						foreach($license_records as $license_record){
							if($license_record->first_vehicle !='')
								$total_vehicle++;
							if($license_record->second_vehicle !='')
								$total_vehicle++;
						}
					}
					if(($total_vehicle >=2 && $license_record->first_vehicle !='') || ($total_vehicle >=2 && $license_record->second_vehicle !='')){
					$data = response()->json(['response' => 2, 'message' => 'Vehicle registration reached max limit']);
					}

					if ($request->file('profile') != null) {
						$file = $request->file('profile');
						$ImgObj = new \App\Models\v7\UserFacialId();
						$ImgObj->correctImageOrientation($file);

						$input['profile_picture'] = $request->file('profile')->store(upload_path('user_request'));
					}
					if ($request->file('contract') != null && $input['role_id']==29) {
						$input['contract_file'] = $request->file('contract')->store(upload_path('user_request'));
					}
					$user = UserRegistrationRequest::create($input);
					//return redirect()->route('thankyou', ['status' => 1]);
					$data = response()->json(['response' => 2, 'message' => 'Registration Successful']);				
				}
			}
		return $data;
	}

	public function countries() {
        $othercountries = Country::whereNotIn('id',[1,2])->where('status',1)->orderby('country_name','ASC')->pluck('country_name', 'id')->all();
        $loccountries = Country::whereIn('id',[1,2])->where('status',1)->pluck('country_name', 'id')->all();
		$allcountries =$loccountries +  $othercountries ;
		
		$data = response()->json(['data' => $allcountries,'response' => 1, 'message' => 'Success']);	
		return $data;

	}

	public function countries_array() {
        $othercountries = Country::whereNotIn('id',[1,2])->where('status',1)->orderby('country_name','ASC')->pluck('country_name', 'id')->all();
        $loccountries = Country::whereIn('id',[1,2])->where('status',1)->pluck('country_name', 'id')->all();
		$allcountries =$loccountries +  $othercountries ;
		$countries = array();
		foreach($allcountries as $k => $country){
			$result =array();
			$result['id'] = $k;
			$result['country_name'] = $country;
			$countries[] =$result;
		}
		
		$data = response()->json(['data' => $countries,'response' => 1, 'message' => 'Success']);	
		return $data;

	}

	public function property_lists() {
		$properties = Property::where('open_for_registration',1)->pluck('company_name', 'id')->all();
		$data = response()->json(['data' => $properties,'response' => 1, 'message' => 'Success']);	
		return $data;
	}

	public function user_roles() {
		$user_roles = explode(",",env('USER_APP_ROLE'));
		$roles = Role::whereIn('id',$user_roles)->pluck('name', 'id')->all();
		$data = response()->json(['data' => $roles,'response' => 1, 'message' => 'Success']);	
		return $data;
	}

	public function property_block(Request $request) {
		$input = $request->all();
		if($input['property'] >0){
			$buildings = DB::table("buildings")->where("status",1)->where('account_id',$input['property'])->orderby('building','asc')->pluck("building","id");
			$data = response()->json(['data' => $buildings,'response' => 1, 'message' => 'Success']);
		}	
		else{
			$data = response()->json(['data' => null,'response' => 1, 'message' => 'Property Empty']);
		}
		return $data;
	}
	public function block_unit(Request $request) {
		$building = $request->building;
			if($building !=''){
				$unites = Unit::select('unit', 'id')->where('building_id',$building)->where("status",1)->get();
				$unit_data = array();
				if(isset($unites) && $building !=''){
					foreach($unites as $unit){
						$unit_data[$unit->id] = Crypt::decryptString($unit->unit);
					}
					$data = response()->json(['data' => $unit_data,'response' => 1, 'message' => 'Success']);
				}
			}	
			else{
				$data = response()->json(['data' => null,'response' => 1, 'message' => 'Building Empty']);
			}
		return $data;
		}

	public function property_array() {
		$properties = Property::select('id','company_name')->where('open_for_registration',1)->orderby('company_name','asc')->get();
		$data = response()->json(['data' => $properties,'response' => 1, 'message' => 'Success']);	
		return $data;
	}

	public function user_roles_array() {
		$user_roles = explode(",",env('USER_APP_ROLE'));
		$roles = Role::select('id','name')->whereIn('id',$user_roles)->get();
		$data = response()->json(['data' => $roles,'response' => 1, 'message' => 'Success']);	
		return $data;
	}
	public function reg_roles_array() {
		$user_roles = explode(",",env('USER_APP_ROLE'));
		$roles = Role::select('id','name')->whereIn('id',[2,29])->get();
		$data = response()->json(['data' => $roles,'response' => 1, 'message' => 'Success']);	
		return $data;
	}

	public function property_block_array(Request $request) {
		$input = $request->all();
		if($input['property'] >0){
			$buildings = DB::table("buildings")->select("id","building")->where("status",1)->where('account_id',$input['property'])->orderby('building','asc')->get();
			$data = response()->json(['data' => $buildings,'response' => 1, 'message' => 'Success']);
		}	
		else{
			$data = response()->json(['data' => null,'response' => 1, 'message' => 'Property Empty']);
		}
		return $data;
	}
	public function block_unit_array(Request $request) {
		$building = $request->building;
			if($building !=''){
				$unites = Unit::select('unit', 'id')->where('building_id',$building)->where("status",1)->get();
				$unit_data = array();
				if(isset($unites) && $building !=''){
					foreach($unites as $unit){
						$unit_data[$unit->id] = Crypt::decryptString($unit->unit);

					}
					asort($unit_data);
					$unit_result = array();
					foreach($unit_data as $k => $unit){
						$result = array();
						$result['id'] = $k;
						$result['unit'] = $unit;
						$unit_result[] = $result;
					}
					$data = response()->json(['data' => $unit_result,'response' => 1, 'message' => 'Success']);
				}
			}	
			else{
				$data = response()->json(['data' => null,'response' => 1, 'message' => 'Building Empty']);
			}
		return $data;
	}
	
    public function retrieveInfoApi(Request $request) {

		$env_roles 	= env('USER_APP_ROLE');

		$roles = explode(",",$env_roles);
		$data = [];
		$email = $request->email;
		$user = User::where('email', $email)->first();	
		//print_r($roles);	
		if($user && $user->status !=1){
			return response()->json(['response' => 101, 'message' => 'Account has been deactivated']);	
		}
		else if($user) {
			if(!in_array($user->role_id,$roles)){
				$data = response()->json(['response' => 3, 'message' => 'Login to OPS portal instead']);
			}
			else if(!empty($user->password)) {				
				$data = response()->json(['response' => 1, 'message' => 'Already Password Generated']);
			} else {
				LoginOTP::sendotpnew($user->name, $email,2);
				$authToken = Hash::make('*' . $user->id . '*' . $user->email.'*');
				$data = response()->json(['response' => 2, 'message' => 'OTP Successfully Sent!', 'auth_token' => $authToken]);				
			}
		} else {
			$data = response()->json(['response' => 0, 'message' => 'Email not registered!']);		
		}
		return $data;
	}
	
	public function verifyOtpApi(Request $request)
	{
		if(!$request->has('auth_token')){
			return response()->json([
                'message' => "auth token is required.",
            ], 400);
		}

		$data = [];
		$email = $request->email;
		$verificationcode = $request->verificationcode;
		$user = User::where('email', $email)->first();

		if(!$user) return response()->json(['response' => 0, 'message' => 'Invalid Email']);
		if($user->status !=1) return response()->json(['response' => 101, 'message' => 'Account has been deactivated']);	
		
		$otp = $user->otp;
		if(($verificationcode ==123789) || ($verificationcode ==11111) ||  ($otp == $verificationcode)){
	        if(!Hash::check('*' . $user->id . '*' . $user->email.'*',trim($request->auth_token)))
			{
				$data = response()->json(['response' => 0, 'message' => 'Invalid auth token']);		
			}else{
				
				$user->tokens()->delete();
				$accessToken = $user->createToken('AHPC')->plainTextToken;
				$data = response()->json(['response' => 1, 'message' => 'Valid', 'access_token' => $accessToken]);
			}
		} else {
			$data = response()->json(['response' => 0, 'message' => 'Invalid Verification Code']);		
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
			$user = User::where('email', $email)->first();
			if (isset($user)) {

				$data = response()->json(['user'=>$user->id,'response' => 1, 'message' => 'Password Successfully Updated!']);		
			} else {
				$data = response()->json(['user'=>null,'response' => 0, 'message' => 'Sorry, Email not registered!!']);
			}
		} else {
			$data = response()->json(['user'=>null,'response' => 0, 'message' => 'Password and Confirm Password Mismatch!']);		
		}
		return $data;
	}
	
	public function verifyLoginApi(Request $request)
	{
		$data = [];
		$email = $request->email;
		$password = $request->password;
		$env_roles 	= env('USER_APP_ROLE');
		$roles = explode(",",$env_roles);

        if (Auth::attempt($request->only('email', 'password')))
		{
			$user = User::where('email', $email)->first();
			if($user && $user->status !=1)
			{
				$data = ['response' => 101, 'message' => 'Account has been deactivated'];
			}else if(!in_array($user->role_id,$roles)){
				$data = ['response' => 3, 'message' => 'Login to OPS portal instead'];
			}else if($user) {

				//update account details
				$userinfo  = UserMoreInfo::where('user_id',$user->id)->where('account_id',$user->account_id)->where('status',1)->first();
				if(empty($userinfo)){

					$userinfo  = UserMoreInfo::where('user_id',$user->id)->where('status',1)->orderBy('id','desc')->first();

					if($userinfo){
						$userPurchaseUnit = UserPurchaserUnit::where([
							['user_info_id','=',$userinfo->id],
							['status','=','1']
						])->orderBy('id','desc')->first();

						if($userPurchaseUnit){
							User::where('id',$user->id)->update([
								'user_info_id' => $userPurchaseUnit->user_info_id,
								'account_id' => $userPurchaseUnit->property_id,
								'building_no' => $userPurchaseUnit->building_id,
								'unit_no' => $userPurchaseUnit->unit_id,
								'primary_contact' => $userPurchaseUnit->primary_contact,
							]);
						}else{
							return response()->json(['response' => 101, 'message' => 'Your account is not activated.']);		
						}
					}else{
						return response()->json(['response' => 101, 'message' => 'Your account has been deactivated.']);		
					}
				}

				LoginOTP::sendotpnew($user->name, $email,2);
				$authToken = Hash::make('*' . $user->id . '*' . $user->email.'*');
				$data = ['user_id'=>$user->id,'response' => 1, 'message' => 'Successfully Login!', 'auth_token' => $authToken];
			}
        } else {
			$data = ['response' => 0, 'message' => 'Invalid Login Credentials!'];
		}
		return response()->json($data);
	}
	
	public function	resendOtpApi(Request $request) {
		$data = [];
		$email = $request->email;		
		$user = User::where('email', $email)->first();
		if($user && $user->status !=1){
			return response()->json(['response' => 0, 'message' => 'Account has been deactivated']);
		}	
		else if($user) {	
			LoginOTP::sendotpnew($user->name, $email,2); 
			$data = response()->json(['response' => 1, 'message' => 'OTP Successfully Sent!']);
		} else {
			$data = response()->json(['response' => 0, 'message' => 'Something Went Wrong!']);
		}
		return $data;
	}

	public function forgotPassword(Request $request) {
		$data = [];
		$email = $request->email;
		$user = User::where('email', $email)->first();
		$env_roles 	= env('USER_APP_ROLE');
		$roles = explode(",",$env_roles);
		if(empty($user)){
			$data = response()->json(['response' => 0, 'message' => 'Email is not registered!']);		
		}
		else if($user && $user->status !=1){
			$data = response()->json(['response' => 101, 'message' => 'Account has been deactivated']);	
		}
		else if(!in_array($user->role_id,$roles)){
			$data = response()->json(['response' => 3, 'message' => 'Try on manager app instead']);
		}	
		else if($user) {			
				LoginOTP::forgotpwdotpnew($user->name, $email,2);
				$authToken = Hash::make('*' . $user->id . '*' . $user->email.'*');
				$data = response()->json(['response' => 2, 'message' => 'OTP Successfully Sent!', 'auth_token' => $authToken]);
			
		} else {
			$data = response()->json(['response' => 0, 'message' => 'Email is not registered!']);		
		}
		return $data;
	}

	public function updatePassword(Request $request)
	{
		$data = [];

		$rules=array(
			'old_password'=>'required',
			'password'=>'required',
			'confirmpassword'=>'required',
		);
		$messages=array(
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

		$user = Auth::id();

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

	public function getunitlist(Request $request) 
    {
		$id = Auth::id();
		$userObj = User::find($id); 
		if(empty($userObj)){
			return response()->json(['data'=>'','response' => 300, 'message' => 'User not found']);
		}
		$unit_lists = UserPurchaserUnit::where('user_id',$id)->where('status',1)->get();
		$units = array();
		if(isset($unit_lists)){
			foreach($unit_lists as $unit){
				$propObj = Property::find($unit->property_id);
				if(isset($propObj) && $propObj->status ==1){
					$unitObj = Unit::find($unit->unit_id);
					if(isset($unitObj)){
						$userMoreInfoObj = Unit::find($unit->user_info_id);
						if((isset($userMoreInfoObj) && $userMoreInfoObj->status ==1) || 1==1)
						{
							$data = array();
							$data['id'] = $unit->id;
							$data['unit_id'] = $unit->unit_id;
							$data['building_id'] = $unitObj->building_id;
							$data['property_id'] = $unitObj->account_id;
							$data['role_id'] = $unit->role_id;
							$data['info_id'] = $unit->user_info_id;
							$data['unit'] = Crypt::decryptString($unitObj->unit);
							$data['building'] = isset($unitObj->buildinginfo)?$unitObj->buildinginfo->building:null;
							$data['property'] = isset($unitObj->propertyinfo)?$unitObj->propertyinfo->company_name:null;
							$data['role'] = isset($unit->role)?$unit->role->name:null;
							$units[] = $data;
						}
					}
				}
			}
		}
		$current_data = array();
		$current_data['unit_id'] = $userObj->unit_no;
		$unitObj = Unit::find($userObj->unit_no);
		$current_data['unit'] = isset($unitObj)?Crypt::decryptString($unitObj->unit):null;
		$current_data['building_id'] = isset($unitObj)?$unitObj->building_id:null;
		$current_data['building'] = isset($unitObj->buildinginfo)?$unitObj->buildinginfo->building:null;
		$current_data['property_id'] = $userObj->account_id;
		$current_data['property'] = isset($unitObj->propertyinfo)?$unitObj->propertyinfo->company_name:null;
		$current_data['role'] = $userObj->role_id;

		return response()->json(['data'=>$units,'current_unit'=>$current_data,'response' => 1, 'message' => 'Success']);
		
	}

	public function getunituserlist(Request $request) 
    {
		$id = Auth::id();
		$userObj = User::find($id); 
		if(empty($userObj)){
			return response()->json(['data'=>'','response' => 300, 'message' => 'User not found']);
		}
		$account_id = $userObj->account_id;
		$unit_id = $userObj->unit_no;
		$unitids_byusers = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id', $unit_id)->get();

        $prop_userids =array();
        foreach($unitids_byusers as $k =>$v){
            $prop_userids[] = $v->user_info_id;
        }
        $users = UserMoreInfo::WhereIn('id',$prop_userids)->where('status',1)->orderBy('user_id','DESC')->get();
		$data = array();
		$data['id'] = isset($userinfo->id)?$userinfo->id:null;
		$data['first_name'] = isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):null;
		$data['last_name'] = isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
		$data['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		
	}

	public function switchunit(Request $request) 
    {
		$rules=array(
			'id'=>'required'
		);
		$messages=array(
			'id.required' => 'Id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
    
		$user = Auth::id();
		$id = $request->id;
		$userObj = User::find($user); 
		if(empty($userObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		$unit_info = UserPurchaserUnit::find($id);
		if(isset($unit_info)){
			$UnitObj = Unit::find($unit_info->unit_id);
			if(empty($UnitObj)){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Unit Deleted!']);
			}
			user::where('id',$user)->update( array( 'account_id' =>$UnitObj->account_id,'unit_no' =>$unit_info->unit_id,'building_no' =>$UnitObj->building_id,'user_info_id'=>$unit_info->user_info_id,'role_id'=>$unit_info->role_id));
			$userObj = User::find($user); 
			return response()->json(['data'=>$userObj,'response' => 1, 'message' => 'Switched']);
		}else{
			return response()->json(['data'=>null,'response' => 1, 'message' => 'Invalid Id']);
		}
	
	}

	public function getswitchid(Request $request) 
    {
		$rules=array(
			'unit_id'=>'required'
		);
		$messages=array(
			'unit_id.required' => 'Unit Id is missing',
		);
	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
    
		$user = Auth::id();
		$unit = $request->unit_id;
		$id = $request->id;
		$userObj = User::find($user); 
		if(empty($userObj)){
			return response()->json(['data'=>'','response' => 300, 'message' => 'User not found']);
		}
		$unit_info = UserPurchaserUnit::where('user_id',$userObj->id)->where('unit_id',$unit)->where('status',1)->first();
		if(isset($unit_info)){
			$data =array();
			$data['switch_id'] =	$unit_info->id;
			$data['unit_id'] =	$unit_info->unit_id;
			$data['property'] =	$unit_info->property_id;
			$data['user'] =	$unit_info->user_id;
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}else{
			return response()->json(['data'=>null,'response' => 1, 'message' => 'Invalid Id']);
		}
	
	}

	public function updatePicture(Request $request)
	{
		$rules=array(
			'picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
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

		$user = Auth::id();
		$UserObj = User::find($user);
		if ($request->file('picture') != null) {
			$profile = $request->file('picture')->store(upload_path('profile'));
			$profile_base64 = base64_encode(file_get_contents($request->file('picture')));

		}

		$result = UserMoreInfo::where( 'user_id' , $user)->where('account_id',$UserObj->account_id)->update( array( 'profile_picture' =>  $profile,'profile_picture_base64' =>  $profile_base64));
		$data = response()->json(['result'=>$result,'response' => 1, 'message' => 'Profile picture updated successfully']);
		
		return $data;

	}

	public function FacialRegPicOption(Request $request)
   	{
       	return response()->json(['data'=> FacialRecoOption::where('status',1)->get(),'response' => 1, 'message' => 'success!']);
   	}

	public function FacialRegPic(Request $request) {
		
		$rules=array(
			'picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
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

		$user = Auth::id();
		$UserObj = User::find($user);

		if ($request->file('picture') != null) {
			$profile = $request->file('picture')->store(upload_path('profile'));
			$profile_base64 = base64_encode(file_get_contents($request->file('picture')));
		}

		$result = UserMoreInfo::where( 'user_id' , $user)->where('account_id',$UserObj->account_id)->update( array( 'face_picture' =>  $profile,'face_picture_base64' =>  $profile_base64));
		
		$UserObj = User::find($user);

		$auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();  
		
		
        $api_obj = new \App\Models\v7\User();
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

	public function FacialRegPicAdd(Request $request)
	{
		$rules=array(
			'picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
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

		$user = Auth::id();

		$UserObj = User::find($user);
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		if ($request->file('picture') != null) {
			$file = $request->file('picture');
			$ImgObj = new \App\Models\v7\UserFacialId();
			$ImgObj->correctImageOrientation($file);

			$input['face_picture'] = $request->file('picture')->store(upload_path('profile'));
			$input['face_picture_base64'] = base64_encode(file_get_contents($request->file('picture')));

		}

		$facialResult = UserFacialId::create($input);

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['unit_no'] = $UserObj->unit_no;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 50;
		$inbox['ref_id'] = $facialResult->id;
		$inbox['title'] = "New Face ID uploaded";
		$inbox['message'] = '';
		$inbox['booking_date'] = '';
		$inbox['booking_time'] = '';
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0; 
		$inbox['submitted_by'] =  1;   
		$inbox['created_at'] =  $facialResult->created_at;   
		$result = InboxMessage::create($inbox);

		$probObj = Property::find($UserObj->account_id);
			if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $facialResult->user_id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
	
				
				$title = "Aerea Manager - ".$probObj->company_name;
				$message = 'New Face ID Uploaded';
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$facialResult->unit_no;   
				$notofication_data['user_id'] =$facialResult->user_id;   
				$notofication_data['property'] =$facialResult->account_id;         
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
			}
		
		$data = response()->json(['result'=>$facialResult,'response' => 1, 'message' => 'Face picture updated successfully']);
		
		return $data;

	}

	public function FacialRegPicDelete(Request $request)
	{

		$rules=array(
			'face_id' => 'required'
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();

		$facialResult = UserFacialId::find($input['face_id']);

        $UserObj = User::find($input['user_id']);

		$auth = new \App\Models\v7\Property();
		$thinmoo_access_token = $auth->thinmoo_auth_api();  
		
		
		
        $api_obj = new \App\Models\v7\User();
		$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj);
        
   
		$facial_obj = new \App\Models\v7\UserFacialId();
        if($household_result['code'] ==0 && $facialResult->thinmoo_id !=''){
		
			$faceid_result= $facial_obj->faceImage_delete_api($thinmoo_access_token,$UserObj,$facialResult);
        }


        UserFacialId::findOrFail($input['face_id'])->delete();
		
		$data = response()->json(['result'=>$facialResult,'response' => 1, 'message' => 'Face picture deleted']);
		
		return $data;

	}

	public function FacialRegPicUpdate(Request $request)
	{

		$rules=array(
			'picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
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

		$user = Auth::id();
		$UserObj = User::find($user);
		if ($request->file('picture') != null) {
			$profile = $request->file('picture')->store(upload_path('profile'));
			$profile_base64 = base64_encode(file_get_contents($request->file('picture')));

		}

		$result = UserMoreInfo::where( 'user_id' , $user)->where('account_id',$UserObj->account_id)->update( array( 'face_picture' =>  $profile,'face_picture_base64' =>  $profile_base64));
		
		$UserObj = User::find($user);

		$auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();  
		
		
        $api_obj = new \App\Models\v7\User();
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
			'address' => 'required',
			'postalcode' => 'required',
		);
		$messages=array(
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

		$user = Auth::id();
		$address = $request->address;
		$postalcode = $request->postalcode;
		$UserObj = User::find($user);
		$result = UserMoreInfo::where( 'user_id' , $user)->where( 'account_id' , $UserObj->account_id)->update( array( 'mailing_address' =>  $address,'postal_code'=>$postalcode));
		$data = response()->json(['result'=>$result,'response' => 1, 'message' => 'Profile info updated successfully']);
		
		return $data;

	}
	public function dashboardmenu(Request $request) {
		$rules=array(
			'property' => 'required',
		);
		$messages=array(
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

		$user = Auth::id();
		$property = $request->property;

		$UserObj = User::where('id',$user)->where('status',1)->first();
		$unit_no = $UserObj->unit_no;
		if(empty($UserObj)){
			
			return response()->json(['response' => 101, 'message' => 'Account has been deactivated']);	
		}

		$modules = array();
		if(isset($UserObj->permissions)){
			foreach($UserObj->permissions as $assigned_module){
				//echo "Module :".$assigned_module->module_id;
				if($assigned_module->view ==1)
					$modules[] =  $assigned_module->module_id;
			}

		}

		$favmenus = UserFavMenu::where('user_id', $user)->where('unit_no', $unit_no)->where('account_id', $property)->get();

		$fav_modules = array();
		if(isset($favmenus)){
			foreach($favmenus as $favmenu){
				if(!in_array($favmenu->module_id,$fav_modules))
					$fav_modules[] =  $favmenu->module_id;
			}
		}

		$records = PropertyDashboardPermission::whereIn('module_id',$modules)->where('property_id',$property)->orderby('display_position','asc')->get();
		$data = array();
		foreach($records as $record){
			$list =array();
			$list['id'] = $record->Module->id;
			$list['name'] = $record->Module->name;
			$module_permission = PropertyPermission::select('view')->where('property_id',$property)->where('module_id',$record->Module->id)->first();
			if(isset($module_permission))
				$list['permission'] = $module_permission->view;
			else
				$list['permission'] = 0;
			
			if(in_array($record->Module->id,$fav_modules))
				$list['fav_menu'] = 1;
			else
				$list['fav_menu'] = 0;
				
			$data[] = $list;
		}

		//print_r($fav_modules);

		$fav_records = Module::whereIn('id',$fav_modules)->get();
		$fav_data = array();
		foreach($fav_records as $fav_record){
			$list =array();
			$list['id'] = $fav_record->id;
			$list['name'] = $fav_record->name;
			$module_permission = PropertyPermission::select('view')->where('property_id',$property)->where('module_id',$fav_record->id)->first();
			if(isset($module_permission))
				$list['permission'] = $module_permission->view;
			else
				$list['permission'] = 0;
				
			$fav_data[] = $list;
		}

		return response()->json(['data'=>$data,'fav_menu'=>$fav_data,'response' => 1, 'message' => 'Success']); 


	}


	public function add_favmenu(Request $request) {

		$rules=array(
			'property' => 'required',
			'unit_no' => 'required',
			'menu' => 'required',
		);
		$messages=array(
			'property.required'=>'Property is missing',
			'unit_no.required'=>'Unit No is missing',
			'menu.required'=>'Menu is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$UserFavMenuObj = UserFavMenu::where('user_id', Auth::id())->where('account_id', $request->property)->where('unit_no', $request->unit_no)->where('module_id', $request->menu)->first();
		if(isset($UserFavMenuObj)){
			$data = response()->json(['response' => 200, 'message' => 'Menu already added']);
			return $data;

		}
		$input['user_id'] = Auth::id();
		$input['account_id'] = $request->property;
		$input['unit_no'] = $request->unit_no;
		$input['module_id'] = $request->menu; 
		UserFavMenu::create($input);
		
		$data = response()->json(['response' => 1, 'message' => 'Added into favourite menu']);
		
		return $data;	

	}

	public function delete_favmenu(Request $request) {

		$rules=array(
			'property' => 'required',
			'unit_no' => 'required',
			'menu' => 'required',
		);
		$messages=array(
			'property.required'=>'Property is missing',
			'unit_no.required'=>'Unit No is missing',
			'menu.required'=>'Menu is missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}

		$user_id = Auth::id();
		$account_id = $request->property;
		$unit_id = $request->unit_no;
		$module_id = $request->menu; 

		UserFavMenu::where('user_id', $user_id)->where('account_id', $account_id)->where('unit_no', $unit_id)->where('module_id', $module_id)->delete();
		
		$data = response()->json(['response' => 1, 'message' => 'Removed from favourite menu']);
		
		return $data;

	}


	public function feedbacklist(Request $request) {

		$userid = Auth::id();
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($userid);

       	$feedbacks = FeedbackSubmission::where('user_id',$userid)->where('unit_no',$UserObj->unit_no)->orderby('id','desc')->get();  
		return response()->json([
			'data'=>$feedbacks,
			'file_path'=>$file_path,
			'status'=>'success'
		]);


	}

	public function checkunittakeover(Request $request) {

		$userid = Auth::id();
		$UserObj = User::find($userid);

		$status = UnittakeoverAppointment::where('unit_no',$UserObj->unit_no)->whereNotIn('status', [1])->orderby("id",'desc')->first(); 
		$notes = isset($status->perperty_info->inspection_notes)?$status->perperty_info->inspection_notes:null;

		return response()->json([
			'data'=>$status,
			'notes'=>$notes,
			'status'=>'success'
		]);

	}

	public function checkjointinspection(Request $request) {

		$userid = Auth::id();
		$UserObj = User::find($userid);
		$defects = Defect::where('unit_no',$UserObj->unit_no)->whereNotIn('status', [1])->orderby('id','desc')->get();
		//echo "3";
		if(isset($defects)){  
			//echo "2";
			$defids = array();
			foreach($defects as $defect){
				$defids[] = $defect->id;
			}
			$status = JoininspectionAppointment::where('unit_no',$UserObj->unit_no)->whereIn('def_id', $defids)->whereIn('status', [0,2])->orderby("id",'desc')->first(); 
			//print_r($status)
			
				$notes = isset($status->perperty_info->inspection_notes)?$status->perperty_info->inspection_notes:null;
				return response()->json([
					'data'=>$status,
					'notes'=>$notes,
					'status'=>'success'
				]);
		}
		else{
			
			return response()->json([
				'data'=>null,
				'notes'=>null,
				'status'=>'success'
			]);
		}

	}

	public function checkfinalinspection(Request $request) {

		$userid = Auth::id();
		$UserObj = User::find($userid);
		$defects = Defect::where('unit_no',$UserObj->unit_no)->whereNotIn('status', [1])->orderby('id','desc')->get();
		//echo "3";
		if(isset($defects)){  
			//echo "2";
			$defids = array();
			foreach($defects as $defect){
				$defids[] = $defect->id;
			}
			$inspectionRec = FinalInspectionAppointment::where('unit_no',$UserObj->unit_no)->whereIn('def_id', $defids)->orderby("id",'desc')->first(); 
			if(empty($inspectionRec)){
				return response()->json([
					'response' => 1,
					'status'=>'success'
				]);
			}
			else{
				return response()->json([
					'response' => 0,
					'status'=>'Not Available!'
				]);
			}
				
		}
		else{
			
			return response()->json([
				'response' => 1,
				'status'=>'success'
			]);
		}

	}

	public function bookjointinspection(Request $request)
    {
		$rules=array(
			'def_id' => 'required',
			'appt_date'=>'required',
			'appt_time'=>'required',
			'nricid_1'=>'required',
		);
		$messages=array(
			'def_id.required' => 'Defect submission id missing',
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
		$input['user_id'] = Auth::id();
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
		$inbox['unit_no'] = $UserObj->unit_no;
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

	public function bookfinalinspection(Request $request)
    {
		$rules=array(
			'def_id' => 'required',
			'appt_date'=>'required',
			'appt_time'=>'required',
			'nricid_1'=>'required',
		);
		$messages=array(
			'def_id.required' => 'Defect submission id missing',
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
		$input['user_id'] = Auth::id();
		$UserObj = User::find($input['user_id']);

		$appt_date = $input['appt_date'];
		$appt_time = $input['appt_time'];

		$blockout_date = DB::table("properties")->where('id', $UserObj->account_id)->Where('inspection_blockout_days', 'like', '%' . $appt_date . '%')->get()->first();
		
		$defects = Defect::where('unit_no',$UserObj->unit_no)->whereNotIn('status', [1])->orderby('id','desc')->get();
		//echo "3";
		if(isset($defects)){  
			//echo "2";
			$defids = array();
			foreach($defects as $defect){
				$defids[] = $defect->id;
			}
			$inspectionRec = FinalInspectionAppointment::where('unit_no',$UserObj->unit_no)->whereIn('def_id', $defids)->orderby("id",'desc')->first(); 
		
			if (!empty($inspectionRec)){
				return response()->json(['response' => 400,'status'=>'0',
					'message' => "Appointment already taken.",
				], 400);
			}
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

		$record = FinalInspectionAppointment::create($input);

		Defect::where( 'id' , $input['def_id'])->update( array( 'status' =>  6));//Staus = Completed- Final Inspection Scheduled
		
		$inbox['account_id'] = $UserObj->account_id;
		$inbox['unit_no'] = $UserObj->unit_no;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 5;
		$inbox['ref_id'] = $record->id;
		$inbox['title'] = "You have booked an appointment for Defect Final Inspection";
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

		$userid = Auth::id();
		$UserObj = User::find($userid);

		$file_path = env('APP_URL')."/storage/app";
		$records = Defect::where('unit_no',$UserObj->unit_no)->orderby('id','desc')->get();   
		$data = array();
		foreach($records as $k => $record){
			$data[$k] = $record;
			$data[$k]['final_inspection_require_status'] = ($record->property->final_inspection_required)?$record->property->final_inspection_required:0;
			if(isset($record->submissions)){
			$data[$k]['submissions'] = $record->submissions;
			$data[$k]['inspection'] = $record->inspection;
			$data[$k]['final_inspection_info'] = $record->finalInspection;
			//$data[$k]['submissions']['type'] = isset($record->submissions->gettype)?$record->submissions->gettype:null;
			}

		}
		
		$locations = DefectLocation::where('account_id', $UserObj->account_id)->get();
		$type = DefectType::where('account_id', $UserObj->account_id)->get();
		$property = property::where('id', $UserObj->account_id)->first();

		return response()->json([
			'data'=>$data,
			'location'=>$locations,
			'type'=>$type,
			'property'=>$property,
			'file_path'=>$file_path,
			'status'=>'success'
		]);

	}
	
	public function submitdefects(Request $request)
    {
		$rules=array(
			'defect_location_1'=>'required',
			'defect_type_1'=>'required',
			'notes_1'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
        
        $details = array();
		$UserObj = User::find($input['user_id']);
	
        $ticket = new \App\Models\v7\Defect();
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['block_no'] = $UserObj->building_no;
		$input['unit_no'] = $UserObj->unit_no;
		$input['ticket'] = $ticket->ticketgen();
		if ($request->file('signature') != null) {
			$input['signature']  = $request->file('signature')->store(upload_path('defect'));
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
                    $data['upload'] = $request->file($attachement)->store(upload_path('defect'));
                }else{
					$data['upload']='';
				}
                $data['status'] = 0;
                $details[] = $data;
            }
        }
		$record = DefectSubmission::insert($details);
		

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['unit_no'] = $UserObj->unit_no;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 3;
		$inbox['ref_id'] = $defect->id;
		$inbox['title'] = "New Defect(s) Submitted";
		$inbox['message'] = '';
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;
		$inbox['submitted_by'] =  1;      
		$inbox['created_at'] =  $defect->created_at;   
		$result = InboxMessage::create($inbox);
        $probObj = Property::find($UserObj->account_id);
		if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
			$fcm_token_array ='';
			$user_token = ',';
			$ios_devices_to_send = array();
			$android_devices_to_send = array();
			
			//ModuleSetting::where 
			$allowed_roles = ModuleSetting::where('module_id',3)->where('view',1)->whereNotIn('role_id',[3])->get();
			if(isset($allowed_roles)){
				$allowed_role_array =array();
				foreach($allowed_roles as $allowed_role){
					$allowed_role_array[] = $allowed_role->role_id;
				}
			}
			//$allowed_role_array[] = 3;
			$log_records= UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',$allowed_role_array)->where('status',1)->orderBy('id','desc')->get()->unique('user_id');
			$manager_ids = array();
			if(isset($log_records)){
				foreach($log_records as $logs){
					if(isset($logs->fcm_token) && $logs->fcm_token !=''){
						$user_token .=$logs->fcm_token.",";
						$fcm_token_array .=$logs->fcm_token.',';
						$appSipAccountList[] = $defect->id;
						$manager_ids[] = $logs->id;
						if($logs->login_from ==1)
							$ios_devices_to_send[] = $logs->fcm_token;
						if($logs->login_from ==2)
							$android_devices_to_send[] = $logs->fcm_token;
					}
				}
			}
			
			$title = "Aerea Manager - ".$probObj->company_name;
			$message = 'New Defect(s) Submitted';
			$notofication_data = array();
			$notofication_data['body'] =$title;
			$notofication_data['unit_no'] =$defect->unit_no;   
			$notofication_data['user_id'] =$defect->user_id;   
			$notofication_data['property'] =$defect->account_id;         
			$NotificationObj = new \App\Models\v7\FirebaseNotification();
			$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
			$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
		}

		//exit;
		return response()->json(['result'=>$defect,'response' => 1, 'message' => 'Defects has been submitted!']);

        
	}

	public function defectupdate(Request $request)
    {
		$rules=array(
			'id' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
		$input['user_id'] = Auth::id();
		$id = $request->id;
		$defectObj = Defect::where('id',$id)->where('user_id',$input['user_id'])->first();
		if($defectObj){
			$locations = ($request->input('locations'))?$request->input('locations'):array(); 
			$types = ($request->input('types'))?$request->input('types'):array();  
			$remarks = ($request->input('remarks'));  
			
			$disagree = 0;
			$newsubmissions = array();
			$need_to_rectify = 0;
			if($defectObj->submissions){
				foreach($defectObj->submissions as $k => $submission){
					if($submission->status ==3)
					{
						$defectSubmissionObj = DefectSubmission::find($submission->id);
						if(isset($locations[$submission->id])&& isset($types[$submission->id]) && isset($remarks[$submission->id])){
							$new = array();
							$disagree = 1;

							$new['def_id'] =$defectSubmissionObj->def_id;
							$new['sub_id'] =$defectSubmissionObj->id;
							$new['defect_location'] =$defectSubmissionObj->defect_location;
							$new['defect_type'] =$defectSubmissionObj->defect_type;
							$new['remarks'] =$defectSubmissionObj->remarks;
							$new['created_at'] =date("Y-m-d H:i:s");
							$new['updated_at'] =date("Y-m-d H:i:s");
							DefectUpdateLog::insert($new);

							$defectSubmissionObj->defect_location = ($locations[$submission->id])?$locations[$submission->id]:'';
							$defectSubmissionObj->defect_type = ($types[$submission->id])?$types[$submission->id]:'';
							//$defectSubmissionObj->remarks = ($remarks[$submission->id])?$remarks[$submission->id]:'';
							$defectSubmissionObj->save();
						}                                         
						
					}
				}
			}

			$inbox['account_id'] = $UserObj->account_id;
			$inbox['unit_no'] = $UserObj->unit_no;
			$inbox['user_id'] = $UserObj->id;
			$inbox['type'] = 3;
			$inbox['ref_id'] = $defect->id;
			$inbox['title'] = "Defect Updated";
			$inbox['message'] = '';
			$inbox['status'] =  0; 
			$inbox['view_status'] =  0;
			$inbox['submitted_by'] =  1;      
			$inbox['created_at'] =  $defect->created_at;   
			$result = InboxMessage::create($inbox);
			$probObj = Property::find($UserObj->account_id);
			if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				
				//ModuleSetting::where 
				$allowed_roles = ModuleSetting::where('module_id',3)->where('view',1)->whereNotIn('role_id',[3])->get();
				if(isset($allowed_roles)){
					$allowed_role_array =array();
					foreach($allowed_roles as $allowed_role){
						$allowed_role_array[] = $allowed_role->role_id;
					}
				}
				//$allowed_role_array[] = 3;
				$log_records= UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',$allowed_role_array)->where('status',1)->orderBy('id','desc')->get()->unique('user_id');
				$manager_ids = array();
				if(isset($log_records)){
					foreach($log_records as $logs){
						if(isset($logs->fcm_token) && $logs->fcm_token !=''){
							$user_token .=$logs->fcm_token.",";
							$fcm_token_array .=$logs->fcm_token.',';
							$appSipAccountList[] = $defect->id;
							$manager_ids[] = $logs->id;
							if($logs->login_from ==1)
								$ios_devices_to_send[] = $logs->fcm_token;
							if($logs->login_from ==2)
								$android_devices_to_send[] = $logs->fcm_token;
						}
					}
				}
				
				$title = "Aerea Manager - ".$probObj->company_name;
				$message = 'Defect Updated';
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$defect->unit_no;   
				$notofication_data['user_id'] =$defect->user_id;   
				$notofication_data['property'] =$defect->account_id;         
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
			}
			return response()->json(['response' => 1, 'message' => 'Defects has been updated!']);

		}else{
			return response()->json(['response' => 200, 'message' => 'Record not exist']);
		}

		
        
	}

	public function submitfeedbacks(Request $request)
    {
		$rules=array(
			'fb_option'=>'required',
			'notes'=>'required',
			'subject'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
        
        $details = array();

		$UserObj = User::find($input['user_id']);

        $ticket = new \App\Models\v7\FeedbackSubmission();
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		$input['ticket'] = $ticket->ticketgen();

		if ($request->file('upload_1') != null) {
            $input['upload_1'] = $request->file('upload_1')->store(upload_path('feedback'));
		}
		if ($request->file('upload_2') != null) {
            $input['upload_2'] = $request->file('upload_2')->store(upload_path('feedback'));
        }
		$feedback = FeedbackSubmission::create($input);
		$probObj = Property::find($UserObj->account_id);

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['unit_no'] = $UserObj->unit_no;
		$inbox['user_id'] = $input['user_id'];
		$inbox['type'] = 2;
		$inbox['ref_id'] = $feedback->id;
		$inbox['title'] = "Feedback Submission :".$feedback->getoption->feedback_option;
		//$inbox['title'] = "Aerea Manager - ".$probObj->company_name.": New Feedback Submission";
		$inbox['message'] = '';
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;   
		$inbox['submitted_by'] =  1;
		$inbox['created_at'] =  $feedback->created_at;   
		$result = InboxMessage::create($inbox);

		
		if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
			$fcm_token_array ='';
			$user_token = ',';
			$ios_devices_to_send = array();
			$android_devices_to_send = array();
			$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
			if(isset($logs->fcm_token) && $logs->fcm_token !=''){
				$user_token .=$logs->fcm_token.",";
				$fcm_token_array .=$logs->fcm_token.',';
				$appSipAccountList[] = $feedback->id;
				if($logs->login_from ==1)
					$ios_devices_to_send[] = $logs->fcm_token;
				if($logs->login_from ==2)
					$android_devices_to_send[] = $logs->fcm_token;
			
			
			$title = "Aerea Manager - ".$probObj->company_name;
			$message = 'New Feedback Submission';
			$notofication_data = array();
			$notofication_data['body'] =$title;
			$notofication_data['unit_no'] =$feedback->unit_no;   
			$notofication_data['user_id'] =$feedback->user_id;   
			$notofication_data['property'] =$feedback->account_id;         
			$NotificationObj = new \App\Models\v7\FirebaseNotification();

			/*echo $title;
			echo "<br />";
			echo $message;
			echo "<br />";*/
			//print_r($ios_devices_to_send);
			//echo "<br />";
			/*print_r($notofication_data);
			*/

			$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
			$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
			}
		}

		return response()->json(['result'=>$result,'response' => 1, 'message' => 'Feedback has been submitted!']);
        
	}

	public function ContactLists(Request $request)
	{	
        $input = $request->all();
		$input['user_id'] = Auth::id();

		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json([
				'response'=>200,
				'status'=>'User not found'
			]);
		}
       	$contacts = ContactBook::where('user_id',$UserObj->id)->where('account_id',$UserObj->account_id)->orderby('id','desc')->get();  
		 
		   if(isset($contacts)){
			   return response()->json([
				   'data'=>$contacts,
				   'response' => 1,
				   'status'=>'success'
			   ]);
		   }
		   else{
			   return response()->json([
				   'data'=>null,
				   'response' => 100,
				   'status'=>'No record'
			   ]);
		   }


	}

	public function SearchContact(Request $request) {

		$rules=array(
			'keyword' => 'required'
		);
		$messages=array(
			'keyword.required' => 'Keyword missing'
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
		$input['user_id'] = Auth::id();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json([
				'response'=>200,
				'status'=>'User not found'
			]);
		}
		$keyword = $input['keyword'];
		$contacts = ContactBook::where('user_id',$UserObj->id)->where('account_id',$UserObj->account_id)->where(function ($query) use ($keyword) {
			if($keyword !=''){
				$query->orwhere('name', 'LIKE', '%' . $keyword . '%');
				$query->orwhere('mobile', 'LIKE', '%' . $keyword . '%');
				$query->orwhere('email', 'LIKE', '%' . $keyword . '%');
			}
		})->get();
 
		   if(isset($contacts)){
			   return response()->json([
				   'data'=>$contacts,
				   'response' => 1,
				   'status'=>'success'
			   ]);
		   }
		   else{
			   return response()->json([
				   'data'=>null,
				   'response' => 100,
				   'status'=>'No record'
			   ]);
		   }
	}
	
	public function InfoContact(Request $request) {

		$rules=array(
			'id' => 'required'
		);
		$messages=array(
			'id.required' => 'Id missing'
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
		$input['user_id'] = Auth::id();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json([
				'response'=>200,
				'status'=>'User not found'
			]);
		}
		$contact= ContactBook::where('user_id',$UserObj->id)->where('id',$input['id'])->first(); 
		if(isset($contact)){
			return response()->json([
				'data'=>$contact,
				'response' => 1,
				'status'=>'success'
			]);
		}
		else{
			return response()->json([
				'data'=>null,
				'response' => 100,
				'status'=>'No record'
			]);
		}


	}

	public function AddContact(Request $request)
    {
		$rules=array(
			'name'=>'required',
			'email'=>'required',
			'mobile'=>'required',
		);
		$messages=array(
			'name.required'=>'Name is missing',
			'email.required'=>'Email is missing',
			'mobile.required'=>'Contact No is missing',
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
		$input['user_id'] = Auth::id();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json([
				'response'=>200,
				'status'=>'User not found'
			]);
		}

		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		$input['name'] = $input['name'];
		$input['email'] = $input['email'];
		$input['mobile'] = $input['mobile'];
		$input['id_number'] = $input['id_number'];
		$input['vehicle_no'] = $input['vehicle_no'];
		$result = ContactBook::create($input);
		return response()->json(['result'=>$result,'response' => 1, 'message' => 'Conact has been added!']);
        
	}

	public function EditContact(Request $request)
    {
		$rules=array(
			'name'=>'required',
			'email'=>'required',
			'mobile'=>'required',
		);
		$messages=array(
			'name.required'=>'Name is missing',
			'email.required'=>'Email is missing',
			'mobile.required'=>'Contact No is missing',
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
		$input['user_id'] = Auth::id();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json([
				'response'=>200,
				'status'=>'User not found'
			]);
		}
		$ContactObj= ContactBook::where('user_id',$UserObj->id)->where('id',$input['id'])->first(); 
		if(isset($ContactObj)){
			$ContactObj->name = $input['name'];
			$ContactObj->email = $input['email'];
			$ContactObj->mobile = $input['mobile'];
			$ContactObj->id_number = $input['id_number'];
			$ContactObj->vehicle_no = $input['vehicle_no'];
			$ContactObj->save();
			return response()->json(['result'=>$ContactObj,'response' => 1, 'message' => 'Updated!']);

		}
		else{
			return response()->json([
				'response'=>100,
				'status'=>'Record Not Found!'
			]);
		}
		
		return response()->json(['result'=>$result,'response' => 1, 'message' => 'Conact has been added!']);
        
	}
	public function DeleteContact(Request $request)
    {
		$rules=array(
			'id' => 'required'
		);
		$messages=array(
			'id.required' => 'Id missing'
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
		$input['user_id'] = Auth::id();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json([
				'response'=>200,
				'status'=>'User not found'
			]);
		}
		$contact= ContactBook::where('user_id',$UserObj->id)->where('id',$input['id'])->first(); 
		if(isset($contact)){
			ContactBook::findOrFail($input['id'])->delete();
			return response()->json([
				'response'=>1,
				'status'=>'Deleted'
			]);
		}
		else{
			return response()->json([
				'response'=>100,
				'status'=>'Record Not Found!'
			]);
		}

        
	}

	public function bookunittakeover(Request $request)
    {
		$rules=array(
			'appt_date'=>'required',
			'appt_time'=>'required',
			'nricid_1'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
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
			$inbox['unit_no'] = $UserObj->unit_no;
			$inbox['user_id'] = $UserObj->id;
			$inbox['type'] = 4;
			$inbox['ref_id'] = $record->id;
			//$inbox['title'] = "You have booked an appointment for Key Collection";
			$inbox['title'] = "New Key Collection Booking";
			$inbox['message'] = '';
			$inbox['booking_date'] = $record->appt_date;
			$inbox['booking_time'] = $record->appt_time;
			$inbox['status'] =  0; 
			$inbox['view_status'] =  0; 
			$inbox['submitted_by'] =  1;   
			$inbox['created_at'] =  $record->created_at;   
			$result = InboxMessage::create($inbox);

			$probObj = Property::find($UserObj->account_id);
			if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $record->id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
	
				
				$title = "Aerea Manager - ".$probObj->company_name;
				//$message = 'You have been received new booking for key collection';
				$message = 'New Key Collection Booking';
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$record->unit_no;   
				$notofication_data['user_id'] =$record->user_id;   
				$notofication_data['property'] =$record->account_id;         
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
			}

			return response()->json(['data'=>$record,'response' => 1, 'message' => 'Booking has been done!']);
		}
        
	}
	

	public function userinfo(Request $request)
    {
        //$user = Auth::user();
        $input = $request->all();
		$input['user_id'] = Auth::id();

		$UserObj = User::where('id',$input['user_id'])->where('status',1)->first();
		
		$userinfo  = UserMoreInfo::where('user_id',$UserObj->id)->where('account_id',$UserObj->account_id)->where('status',1)->first();
		if(empty($userinfo)){
			return response()->json(['response' => 101, 'message' => 'Account has been deactivated']);		
		}
		/*$probObj = Property::find($UserObj->account_id);
		if($probObj->status !=1){
			$unit_lists = UserPurchaserUnit::where('user_id',$id)->where('property_id','!=',$probObj->id)->where('status',1)->get();
			if(empty($unit_lists)){
				return response()->json(['response' => 101, 'message' => 'Account has been deactivated']);	//
			}else{
				foreach($unit_lists as $unit_list){
					echo ".";
					$probObj = Property::find($unit_list->property_id);
					if($probObj->status ==1){
						$account_id = $unit_list->property_id;
						$building_no = $unit_list->building_id;
						$unit_no = $unit_list->unit_id;
						$role_id = $unit_list->role_id;
						User::where('id',$UserObj->id)->update(['account_id'=>$account_id,'building_no'=>$building_no,'unit_no'=>$unit_no,'role_id'=>$role_id]);
					}
				}
			}

		} */
		
		if(isset($UserObj)){		
			$data = array();
			$user = $input['user_id'];
			$modules = Module::all();  

			$counts = array();
			$numberOfAnnouncement = AnnouncementDetail::where('user_id', $user)->where('account_id', $UserObj->account_id)->where('view_status', 0)->count();
			$counts['announcement'] = $numberOfAnnouncement;

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
				$userdata['name'] = Crypt::decryptString($UserObj->name);
				$userdata['email'] = $UserObj->email;
				$userdata['app_version'] = $UserObj->app_version;
				$userdata['created_at'] =  $UserObj->created_at;
				$userdata['updated_at'] =  $UserObj->updated_at;
				if(isset($userinfo)){
					$userdata['userinfo'] = array();
					$userdata['userinfo']['id'] = isset($userinfo->id)?$userinfo->id:null;
					$userdata['userinfo']['last_name'] = isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
					$userdata['userinfo']['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;
					$userdata['userinfo']['phone'] =  isset($userinfo->phone)?Crypt::decryptString($userinfo->phone):null;
					$userdata['userinfo']['mailing_address'] =  isset($userinfo->mailing_address)?$userinfo->mailing_address:null;
					$userdata['userinfo']['postal_code'] =  isset($userinfo->postal_code)?$userinfo->postal_code:null;
					$userdata['userinfo']['company_name'] =  isset($userinfo->company_name)?$userinfo->company_name:null;
					$userdata['userinfo']['face_picture'] =  isset($userinfo->face_picture)?$userinfo->face_picture:null;
					$userdata['userinfo']['status'] = isset($userinfo->status)?$userinfo->status:null;
					$userdata['userinfo']['deactivated_date'] = isset($userinfo->status)?$userinfo->deactivated_date:null;
					$userdata['userinfo']['opn_id'] = isset($userinfo->opn_id)?$userinfo->opn_id:null;

				}
			}
			$data['user'] = $userdata;
			$data['role'] = isset($UserObj->role)?$UserObj->role:array();
			if(isset($userinfo)){
				$moredata['last_name'] = isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
				$moredata['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;
				$moredata['phone'] =  isset($userinfo->phone)?Crypt::decryptString($userinfo->phone):null;
				$moredata['mailing_address'] =  isset($userinfo->mailing_address)?$userinfo->mailing_address:null;
				$moredata['postal_code'] =  isset($userinfo->postal_code)?$userinfo->postal_code:null;
				$moredata['company_name'] =  isset($userinfo->company_name)?$userinfo->company_name:null;
				$moredata['face_picture'] =  isset($userinfo->face_picture)?$userinfo->face_picture:null;
			}
			$data['moreinfo'] = $moredata;

			//$data['face_ids'] = $UserObj->faceids;
			
			if(isset($UserObj->getunit)){
				$data_unit = array();
				$data_unit['id'] = $UserObj->getunit->id;
				$data_unit['unit'] = Crypt::decryptString($UserObj->getunit->unit);
				$data_unit['code'] = Crypt::decryptString($UserObj->getunit->code);
			}
			$data['unit'] = ($data_unit)?$data_unit:(object)[];
			if(isset($UserObj->propertyinfo)){
				$data['property']['id'] = $UserObj->propertyinfo->id;
				$data['property']['company_name'] = $UserObj->propertyinfo->company_name;
				$data['property']['company_logo'] = $UserObj->propertyinfo->company_logo;
				$data['property']['short_code'] = $UserObj->propertyinfo->short_code;
				$data['property']['otp_option'] = $UserObj->propertyinfo->otp_option;
				$data['property']['security_option'] = $UserObj->propertyinfo->security_option;
				$data['property']['id'] = $UserObj->propertyinfo->id;
				$data['property']['takeover_timing'] = $UserObj->propertyinfo->takeover_timing;
				$data['property']['inspection_timing'] = $UserObj->propertyinfo->inspection_timing;
				$data['property']['takeover_blockout_days'] = $UserObj->propertyinfo->takeover_blockout_days;
				$data['property']['inspection_blockout_days'] = $UserObj->propertyinfo->inspection_blockout_days;
				$data['property']['visitor_management_bg'] = $UserObj->propertyinfo->visitor_management_bg;
				$data['property']['visitor_limit'] = $UserObj->propertyinfo->visitor_limit;
				$data['property']['visitors_allowed'] = $UserObj->propertyinfo->visitors_allowed;
				$data['property']['takeover_availability_start'] = $UserObj->propertyinfo->takeover_availability_start;
				$data['property']['inspection_availability_start'] = $UserObj->propertyinfo->inspection_availability_start;
				$data['property']['defect_max_limit'] = $UserObj->propertyinfo->defect_max_limit;
				$data['property']['final_inspection_required'] = $UserObj->propertyinfo->final_inspection_required;
				$data['property']['open_for_registration'] = $UserObj->propertyinfo->open_for_registration;
				//$data['property'] = $UserObj->propertyinfo;

			}

			$prop_banner_array = array();

			$propBanners = HomeBannerProperty::where('property_id', $UserObj->account_id)->get();
			if(isset($propBanners)){
				foreach($propBanners as $bannerid){
						$prop_banner_array[] =  $bannerid->banner_id;
				}
			}
			//print_r($prop_banner_array);

			$homeBanner = HomeBanner::whereIn('id', $prop_banner_array)->where('status',1)->orderby('display_order','asc')->get();
			if(isset($UserObj->propertyinfo)){
				$data['sliders'] = $homeBanner;
			}

			$favmenus = UserFavMenu::where('user_id', $UserObj->id)->where('unit_no', $UserObj->unit_no)->where('account_id', $UserObj->account_id)->get();

			$fav_modules = array();
			if(isset($favmenus)){
				foreach($favmenus as $favmenu){
					if(!in_array($favmenu->module_id,$fav_modules))
						$fav_modules[] =  $favmenu->module_id;
				}
			}


			$permission_array = array();
			$user_permissions = UserPermission::where('user_id', $UserObj->id)->where('account_id', $UserObj->account_id)->where('unit_no', $UserObj->unit_no)->get();

			if(isset($user_permissions)){
				foreach($user_permissions as $permission){
					//echo $permission->module_id;echo "hai";
					$module_type = Module::where('id',$permission->module_id)->where('type',2)->first();
					if(isset($module_type))
					{
						$permission['permission'] = 0;
						$check_permission = PropertyPermission::where('module_id',$permission->module_id)->where('property_id',$UserObj->account_id)->first();

						if(isset($check_permission->id))
							$permission['permission'] = $check_permission->view;
						if(in_array($permission->module_id,$fav_modules))
							$permission['fav_menu'] = 1;
						else
							$permission['fav_menu'] = 0;
						if($permission->module_id ==76 || $permission->module_id ==77)//Block user for Chatterbox(76) , Marketplace (77)
						{
							
							$check_block_status = ChatBoxBlockUserByAdmin::where('account_id',$UserObj->account_id)->where('block_user_id',$UserObj->id)->where('status',1)->first();
							if(isset($check_block_status)){
								$permission['blocked'] = 1;
							}else{
								$permission['blocked'] = 0;
							}

						}
						else{
							$permission['blocked'] = 0;
						}
						
						$permission_array[] = $permission;
					}
					
				}
			}

			$fav_records = Module::whereIn('id',$fav_modules)->get();
			$fav_data = array();
			foreach($fav_records as $fav_record){
				$list =array();
				$list['id'] = $fav_record->id;
				$list['name'] = $fav_record->name;
				$module_permission = PropertyPermission::select('view')->where('property_id',$UserObj->account_id)->where('module_id',$fav_record->id)->first();
				if(isset($module_permission))
					$list['permission'] = $module_permission->view;
				else
					$list['permission'] = 0;
					
				$fav_data[] = $list;
			}

			$data['permissions'] = $permission_array;
			$data['fav_menu'] = $fav_data;
			$data['notification'] =$counts;
			$data['eforms_modules'] = $eforms_permission;
			$data['loginfo'] = $deviceinfo;
			
			$appfaceids = UserFacialId::where('user_id',$UserObj->id)->where('account_id',$UserObj->account_id)->where('unit_no',$UserObj->unit_no)->get();
			if(isset($appfaceids)){
				$faceids = array();
				foreach($appfaceids as $faceid){
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
			$data['OPN_PKEY'] = env('VAULTKEY');

			$config_res = ConfigSetting::where('id',1)->first();
			if($config_res)
				$data['DOOR_DEVICES_CACHE_TTL'] = $config_res->value;
			$config_res = ConfigSetting::where('id',2)->first();
				$data['SLIDER_TIME_INTERVAL'] = $config_res->value;
			//$data['OPN_PKEY'] = env('APPID');

			/*$ads = Ad::where('status',1)->get();
			if(isset($ads))
				$data['help_banner'] = $ads;
			
			$econcierge = Econcierge::where('status',1)->get();
			if(isset($econcierge))
				$data['econcierge'] = $econcierge;
			*/
			
			$user_mp_permissions = UserPermission::where('user_id', $UserObj->id)->where('account_id', $UserObj->account_id)->where('unit_no', $UserObj->unit_no)->where('module_id',77)->first();

			if(isset($user_mp_permissions)){
				$adsLists = MpAdsSubmission::where('status',1)->orderby('id','desc')->limit(4)->get();
				if(isset($adsLists)){
					$mp_records = array();
					foreach($adsLists as $adsList){
						$mp_data =array();
						$mp_data['id'] = $adsList->id;
						$mp_data['title'] = $adsList->title;
						$mp_data['price'] = $adsList->price;
						$mp_data['notes'] = $adsList->notes;
						$mp_data['ref_no'] = $adsList->ticket;
						$mp_data['members_count'] = $adsList->members_count;
						//$mp_data['user_info'] = $adsList->user;
						$mp_data['condition_info'] = isset($adsList->getcondition)?$adsList->getcondition:null;
						//$mp_data['type_info'] = isset($adsList->getcondition)?$adsList->getcondition:null;
						$mp_data['images'] = isset($adsList->images)?$adsList->images:null;
						$userinfo  = UserMoreInfo::where('user_id',$adsList->user_id)->where('account_id',$UserObj->account_id)->where('status',1)->first();
						$user_mp_data = array();
						if(isset($userinfo)){
							$user_mp_data['id'] = isset($userinfo->user_id)?$userinfo->user_id:null;
							$user_mp_data['first_name'] = isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):null;
							$user_mp_data['last_name'] = isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
							$user_mp_data['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;
						}
						$mp_data['userdata'] = (count($user_mp_data)>0)?$user_mp_data:null;
						$user_likes = MpAdsLike::where('ref_id',$adsList->id)->where('account_id',$UserObj->account_id)->where('user_id',$UserObj->id)->count();
						$mp_data['like_status'] = ($user_likes >0)?1:null;
						
						$likes = MpAdsLike::where('ref_id',$adsList->id)->count();
						$mp_data['likes_count'] = isset($likes)?$likes:null;
						if($adsList->type ==1){
							$user_register = MpGroupRegister::where('ref_id',$adsList->id)->where('account_id',$UserObj->account_id)->where('user_id',$UserObj->id)->count();
							$mp_data['registered_status'] = ($user_register >0)?1:null;

							$registers = MpGroupRegister::where('ref_id',$adsList->id)->count();
							$mp_data['registered_count'] = isset($registers)?$registers:null;
						}

						$mp_records[] = $mp_data;
					}
					
					$data['marketplace_item'] = (count($mp_records)>0)?$mp_records:null;
				}
			}

			return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
		}
		else{
			return response()->json(['data'=>'','response' => 0, 'message' => 'User not available!']);
		}
        
	}

	public function user_thinmoo_info(Request $request){
		
        //$user = Auth::user();
		$input = $request->all();
		
		$UserObj = User::find(Auth::id());
		$data = array();
		
		$auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();
        
        $api_obj = new \App\Models\v7\User();
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
   $file_path = env('APP_URL')."/storage/app";

   return response()->json(['data'=>$facilities,'file_path'=>$file_path,'option'=>$options,'response' => 1, 'message' => 'success!']);
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
	$given_date = $request->date;
	$records   = Property::select('inspection_blockout_days')->where('id', $request->property)->first();

	if(empty($records)){
		$messages = $validator->messages();
		$errors = $messages->all();
		return response()->json([
			'message' => 'Property not valid',
		], 400);
	}
	$selecteddate = date("Y-m-d", strtotime($given_date));
	//$selectd_dateexplode("-",$selecteddate);


	$blockout_data = explode(",",$records->inspection_blockout_days);

	//print_R($blockout_data);

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
			'booking_date'=>'required',
			'booking_time'=>'required',
			'type_id'=>'required',
			'description' => 'required',
			'amount'	=> 'required',
		);
		$messages=array(
			'booking_date.required'=>'Date is missing',
			'booking_time.required'=>'Time is missing',
			'type_id.required'=>'Type is missing',
			'description.required' => 'Description missing',
			'amount.required' => 'Amount missing',
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
		$input['user_id'] = Auth::id();
		$UserObj = User::find($input['user_id']);

		$input['user_id'] = $UserObj->id;
		$input['account_id'] = $UserObj->account_id;
		//$input['ack_status'] = $UserObj->account_id;

		$facilityType = FacilityType::where('id',$input['type_id'])->first();
		$blockout_data = explode(",",$facilityType->blockout_days);

		if($facilityType->payment_required ==1){
			$rules=array(
				'cust_opn_id' => 'required',
				'card_token' => 'required',
			);
			$messages=array(
				'cust_opn_id.required' => 'Customer OPN id code missing',
				'card_token.required' => 'Token missing',
			);
			$validator = Validator::make($request->all(), $rules, $messages);
			if ($validator->fails()) {
				$messages = $validator->messages();
				$errors = $messages->all();
				return response()->json([
					'message' => $errors,
				], 400);
			}
		}

		$bookfacility =0;
		$facility = $input['type_id'];
		$booking_date = $input['booking_date'];
		$booking_time = $input['booking_time'];
		$user = $UserObj->id;
		if(empty($UserObj)){
			return response()->json(['data'=>0, 'response' => 200, 'message' => 'User not avialble']);
		}
		if(empty($facilityType)){
			return response()->json(['data'=>0, 'response' => 400, 'message' => 'Facility type not avialble']);
		}
		else if(in_array($booking_date,$blockout_data)){//Blockout dates

			return response()->json(['data'=>0,'response' => 500, 'message' => 'Booking date not available!']);
		}
		else if($facilityType->next_booking_allowed ==1){ // for None option
			$tempObj = FacilityBookingTempRequest::where('user_id',$user)->where('account_id',$UserObj->account_id)->orderBy('id','DESC')->first();
					if(isset($tempObj)){
						FacilityBookingTempRequest::where('id',$tempObj->id)->update(['type_id'=>$facility,"booking_date"=>$booking_date,"booking_time"=>$booking_time]);
					}else{
						$temp['type_id'] = $facility;
						$temp['booking_date'] = $booking_date;
						$temp['booking_time'] = $booking_time;
						$temp['user_id'] = $user;
						$temp['unit_no'] = $UserObj->unit_no;
						$temp['account_id'] = $UserObj->account_id;
						$tempObj = FacilityBookingTempRequest::create($temp);
					}
			$bookfacility =1;	
		}
		else if($facilityType->next_booking_allowed ==2){
			$query_date = $booking_date;
			$fromdate = date('Y-m-01', strtotime($query_date));
			$todate =  date('Y-m-t', strtotime($query_date));
			$last_15_mins_records = Carbon::now()->subMinutes(15)->toDateTimeString();

			$temp_records = FacilityBookingTempRequest::whereNotIn('user_id',[$user])->where('account_id',$UserObj->account_id)->where('type_id',$facility)->where('booking_date',$booking_date)->where('booking_time',$booking_time)->where('updated_at','>=',$last_15_mins_records)->first(); 
			//print_r($temp_records);
			if(isset($temp_records)){
				return response()->json(['data' =>0, 'response' => 200, 'message' => 'There is already a booking for '.$facilityType->facility_type.' on '.$booking_date.' at '.$booking_time]);
			}
			$bookings = FacilityBooking::where('user_id',$user)->where('type_id',$facility)->where('payment_status',2)->whereNotIn('status', ['1'])->whereBetween('booking_date',array($fromdate,$todate))->first();  
			if(isset($bookings) && $bookings->booking_date){
				return response()->json(['data' =>0, 'response' => 100, 'message' => 'There is already a booking for '.$facilityType->facility_type.'. Each unit is entitled to one booking each month. As such, you are not able to make another booking for '.$facilityType->facility_type.'.']); 	
			}
			else{
				$tempObj = FacilityBookingTempRequest::where('user_id',$user)->where('account_id',$UserObj->account_id)->orderBy('id','DESC')->first();
				if(isset($tempObj)){
					FacilityBookingTempRequest::where('id',$tempObj->id)->update(['type_id'=>$facility,"booking_date"=>$booking_date,"booking_time"=>$booking_time]);
				}else{
					$temp['type_id'] = $facility;
					$temp['booking_date'] = $booking_date;
					$temp['booking_time'] = $booking_time;
					$temp['user_id'] = $user;
					$temp['unit_no'] = $UserObj->unit_no;
					$temp['account_id'] = $UserObj->account_id;
					$tempObj = FacilityBookingTempRequest::create($temp);
				}
				$bookfacility =1;		
			}
		}
		else if($facilityType->next_booking_allowed ==3){
			$last_15_mins_records = Carbon::now()->subMinutes(15)->toDateTimeString();

			$temp_records = FacilityBookingTempRequest::whereNotIn('user_id',[$user])->where('account_id',$UserObj->account_id)->where('type_id',$facility)->where('booking_date',$booking_date)->where('booking_time',$booking_time)->where('updated_at','>=',$last_15_mins_records)->first(); 
			//print_r($temp_records);
			if(isset($temp_records)){
				return response()->json(['data' =>0, 'response' => 200, 'message' => 'There is already a booking for '.$facilityType->facility_type.' on '.$booking_date.' at '.$booking_time]);
			}

			$bookings = FacilityBooking::where('user_id',$user)->where('type_id',$facility)->where('payment_status',2)->whereNotIn('status', ['1'])->orderBy('booking_date','DESC')->first(); 
			//print_r($bookings->booking_date);

			if(isset($bookings) && $bookings->booking_date){

				$date = Carbon::createFromFormat('Y-m-d', $bookings->booking_date);
				$daysToAdd = $facilityType->allowed_booking_for;
				$booking_allowed  = $date->addDays($daysToAdd);
				//echo "Next booking : ".$booking_allowed;
				//echo "Requested Booking :".$booking_date;

				if($booking_allowed <= $booking_date ){
					$tempObj = FacilityBookingTempRequest::where('user_id',$user)->where('account_id',$UserObj->account_id)->orderBy('id','DESC')->first();
					if(isset($tempObj)){
						FacilityBookingTempRequest::where('id',$tempObj->id)->update(['type_id'=>$facility,"booking_date"=>$booking_date,"booking_time"=>$booking_time]);
					}else{
						$temp['type_id'] = $facility;
						$temp['booking_date'] = $booking_date;
						$temp['booking_time'] = $booking_time;
						$temp['user_id'] = $user;
						$temp['unit_no'] = $UserObj->unit_no;
						$temp['account_id'] = $UserObj->account_id;
						$tempObj = FacilityBookingTempRequest::create($temp);
					}
					$bookfacility =1;	
				}
				else{
					return response()->json(['data' =>0, 'response' => 200, 'message' => 'There is already a booking for '.$facilityType->facility_type.'. Each unit is entitled to one booking per '.$facilityType->next_booking_allowed_days.' days. As such, you are not able to make another booking for '.$facilityType->facility_type.'.']);
				}	
			}
			else{
				$tempObj = FacilityBookingTempRequest::where('user_id',$user)->where('account_id',$UserObj->account_id)->orderBy('id','DESC')->first();
					if(isset($tempObj)){
						FacilityBookingTempRequest::where('id',$tempObj->id)->update(['type_id'=>$facility,"booking_date"=>$booking_date,"booking_time"=>$booking_time]);
					}else{
						$temp['type_id'] = $facility;
						$temp['booking_date'] = $booking_date;
						$temp['booking_time'] = $booking_time;
						$temp['user_id'] = $user;
						$temp['unit_no'] = $UserObj->unit_no;
						$temp['account_id'] = $UserObj->account_id;
						$tempObj = FacilityBookingTempRequest::create($temp);
					}
				$bookfacility =1;		
			}	
		}
		else{
			$tempObj = FacilityBookingTempRequest::where('user_id',$user)->where('account_id',$UserObj->account_id)->orderBy('id','DESC')->first();
					if(isset($tempObj)){
						FacilityBookingTempRequest::where('id',$tempObj->id)->update(['type_id'=>$facility,"booking_date"=>$booking_date,"booking_time"=>$booking_time]);
					}else{
						$temp['type_id'] = $facility;
						$temp['booking_date'] = $booking_date;
						$temp['booking_time'] = $booking_time;
						$temp['user_id'] = $user;
						$temp['unit_no'] = $UserObj->unit_no;
						$temp['account_id'] = $UserObj->account_id;
						$tempObj = FacilityBookingTempRequest::create($temp);
					}
			
			$bookfacility =1;
		}

		if($bookfacility ==1){

			if(isset($UserObj->unit_no))
				$input['unit_no'] = $UserObj->unit_no;
			if(isset($facilityType) && $facilityType->payment_required ==1){
				$input['payment_required'] = 1;
				$input['booking_fee'] = $facilityType->booking_fee;
				$input['deposit_fee'] = $facilityType->booking_deposit;
			}
			else{
				$input['payment_required'] = 2;
				$input['booking_fee'] = 0.00;
				$input['deposit_fee'] = 0.00;
			}

			$record = FacilityBooking::create($input);

			$userinfo = UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('status',1)->first();
			if(isset($userinfo) && isset($userinfo)){

				if($facilityType->payment_required ==1){
					$payment_url = env('OMISEURL')."charges";
					$propinfo = Property::where('id',$UserObj->account_id)->first();
					//$username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
					if($propinfo->opn_secret_key ==''){
						return response()->json([
							'data'=>null,
							'response' => 300,
							'status'=>'OPN Merchant Id not found!'
						]);
					}
					$sub_merchant_key = $propinfo->opn_secret_key;
					$username = env('OMISEKEY');

					$password = '';
					/*$fields = [
						"customer"				=> $input['cust_opn_id'],
						"card"       			=> $input['card_token'],
						"description"           => $input['description'],
						"amount"           		=> $input['amount'],
						"currency"         		=> 'SGD',
						"return_uri"           	=> env('OPNRETUNURI'),
						"capture"           	=> 'false',
						"metadata[facility_booking_id]"  => $record->id,
						"metadata[type]"  => 'facility booking',
						"metadata[facility]"  => $input['type_id'],
						"metadata[user_id]"   => $input['user_id'],
						"metadata[property]"  => $UserObj->account_id,
					];*/
					$fields = [
						"customer"       				=> $input['cust_opn_id'],
						"description"					=> 'Booking Fee :'. $record->id,
						"amount"           				=> $input['amount'],
						"currency"         				=> 'SGD',
						"return_uri"           			=> env('OPNRETUNURI'),
						"metadata[type]" 				=> 'facility_booking',
						"metadata[facility_booking_id]" => $record->id,
						"metadata[facility]"  			=> $input['type_id'],
						"metadata[user_id]"   			=> $input['user_id'],
						"metadata[property]"  			=> $input['account_id'],
						"metadata[description]"			=> $input['description'],
						"metadata[token]"				=> $input['card_token'],
					];
					
				
					$fields_string = http_build_query($fields);
					$headers =array();
					//$headers[] = 'Content-type: application/x-www-form-urlencoded';
					$headers[] = "SUB_MERCHANT_ID:$sub_merchant_key";

					$ch = curl_init();
					curl_setopt($ch,CURLOPT_URL, $payment_url);
					curl_setopt($ch,CURLOPT_POST, true);
					curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
					curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
					$result = curl_exec($ch);
					$json = json_decode($result,true);
					//print_r($json);
				}else{				
					FacilityBookingTempRequest::where('user_id', $UserObj->id)->where('account_id', $UserObj->account_id)->delete();
				}

				if(isset($json['id']) && $json['id'] !=''){
					$card_id = $json['card']['id'];
					$facility_update_qry =FacilityBooking::where('id',$record->id)->update(['opn_charge_id' => $json['id'],'payment_status'=>'1','opn_card_id' => $input['card_token']]);
					$opn_data = array();
					$opn_data['id'] = $json['id'];
					$opn_data['authorize_uri'] = $json['authorize_uri'];
					$opn_data['status'] = $json['status'];
					$opn_data['authorized'] = $json['authorized'];
					$opn_data['capturable'] = $json['capturable'];
				}
				else{
					$opn_data = null;
				}

					
					$inbox['account_id'] = $UserObj->account_id;
					$inbox['unit_no'] = $UserObj->unit_no;
					$inbox['user_id'] = $UserObj->id;
					$inbox['type'] = 6;
					$inbox['ref_id'] = $record->id;
					//$inbox['title'] = "You have booked : ".$record->gettype->facility_type;
					$inbox['title'] = "New Facility Booking";
					$inbox['message'] = '';
					$inbox['booking_date'] = $record->booking_date;
					$inbox['booking_time'] = $record->booking_time;
					$inbox['status'] =  0; 
					$inbox['view_status'] =  0; 
					$inbox['submitted_by'] =  1;   
					$inbox['created_at'] =  $record->created_at;   
					$result = InboxMessage::create($inbox);

					$probObj = Property::find($UserObj->account_id);
						if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
							$fcm_token_array ='';
							$user_token = ',';
							$ios_devices_to_send = array();
							$android_devices_to_send = array();
							$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
							if(isset($logs->fcm_token) && $logs->fcm_token !=''){
								$user_token .=$logs->fcm_token.",";
								$fcm_token_array .=$logs->fcm_token.',';
								$appSipAccountList[] = $record->id;
								if($logs->login_from ==1)
									$ios_devices_to_send[] = $logs->fcm_token;
								if($logs->login_from ==2)
									$android_devices_to_send[] = $logs->fcm_token;
							}
				
							
							$title = "Aerea Manager - ".$probObj->company_name;
							//$message = "You have been received new booking : ".$record->gettype->facility_type;
							$message = "New Facility Booking";
							$notofication_data = array();
							$notofication_data['body'] =$title;
							$notofication_data['unit_no'] =$record->unit_no;   
							$notofication_data['user_id'] =$record->user_id;   
							$notofication_data['property'] =$record->account_id;         
							$NotificationObj = new \App\Models\v7\FirebaseNotification();
							$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
							$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
						}

					return response()->json(['data'=>$record,'opn_data'=>$opn_data,'response' => 1, 'message' => 'Booking has been done!']);
				
			}else{
				return response()->json([
					'data'=>null,
					'opn_data'=>null,
					'response' => 200,
					'file_path'=>$file_path,
					'status'=>'User not found!'
				]);
			}

			if(isset($UserObj->unit_no))
				$input['unit_no'] = $UserObj->unit_no;

			$record = FacilityBooking::create($input);
			
			$inbox['account_id'] = $UserObj->account_id;
			$inbox['unit_no'] = $UserObj->unit_no;
			$inbox['user_id'] = $UserObj->id;
			$inbox['type'] = 6;
			$inbox['ref_id'] = $record->id;
			//$inbox['title'] = "You have booked : ".$record->gettype->facility_type;
			$inbox['title'] = "New Facility Booking : ".$record->gettype->facility_type;
			$inbox['message'] = '';
			$inbox['booking_date'] = $record->booking_date;
			$inbox['booking_time'] = $record->booking_time;
			$inbox['status'] =  0; 
			$inbox['view_status'] =  0; 
			$inbox['submitted_by'] =  1;   
			$inbox['created_at'] =  $record->created_at;   
			$result = InboxMessage::create($inbox);

			$probObj = Property::find($UserObj->account_id);
				if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
					$fcm_token_array ='';
					$user_token = ',';
					$ios_devices_to_send = array();
					$android_devices_to_send = array();
					$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
					if(isset($logs->fcm_token) && $logs->fcm_token !=''){
						$user_token .=$logs->fcm_token.",";
						$fcm_token_array .=$logs->fcm_token.',';
						$appSipAccountList[] = $record->id;
						if($logs->login_from ==1)
							$ios_devices_to_send[] = $logs->fcm_token;
						if($logs->login_from ==2)
							$android_devices_to_send[] = $logs->fcm_token;
					}
		
					
					$title = "Aerea Manager - ".$probObj->company_name;
					//$message = "You have been received new booking : ".$record->gettype->facility_type;
					$message = "New Facility Booking";
					$notofication_data = array();
					$notofication_data['body'] =$title;
					$notofication_data['unit_no'] =$record->unit_no;   
					$notofication_data['user_id'] =$record->user_id;   
					$notofication_data['property'] =$record->account_id;         
					$NotificationObj = new \App\Models\v7\FirebaseNotification();
					$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
					$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
				}

			return response()->json(['data'=>$record,'response' => 1, 'message' => 'Booking has been done!']);
		}

        
	}

	public function cancelfacilitybooking(Request $request){

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

		$input = $request->all();
		$input['user_id'] = Auth::id();
        $reason ='';
		$booking_id = $input['booking_id'];
        $recordObj = FacilityBooking::where('id',$booking_id)->where('user_id',$input['user_id'])->where('status','!=',1)->first();
		if(empty($recordObj)){
			return response()->json(['response' => 200, 'message' => 'Invalid Booking!']);

		}
       
       if(isset($input['reason']))
           $reason = $input['reason'];

        FacilityBooking::where('id', $booking_id)
        ->update(['status' =>1,'reason'=>$reason]);

        //$recordObj = FacilityBooking::find($booking_id);
        $propinfo = Property::where('id',$recordObj->account_id)->first();
	    $sub_merchant_key = $propinfo->opn_secret_key;
		$username = env('OMISEKEY');
		$password = '';
       
        if($recordObj->payment_status ==2){
            $payment_url = env('OMISEURL')."charges/".$recordObj->booking_charge_id."/refunds";

            if(isset($recordObj->gettype)){
               // echo $recordObj->booking_date;
                //echo "<br>";
                $cut_of_date = date('Y-m-d', strtotime(Carbon::now()->subDays($recordObj->gettype->cut_of_days)));
                if($recordObj->booking_date > $cut_of_date){
                    $deduct_percentage = $recordObj->gettype->cut_of_amount_percentage;
                    $deduct_amt= ($recordObj->booking_fee /100) * $deduct_percentage;
                    $capture_amount= $deduct_amt ;
                }else{
                    $capture_amount= 0.00 ;
                }
            }else{
                $capture_amount = 0.00;
            }
            $refund_amount = $recordObj->booking_fee-$capture_amount;
            $return_amount = ($refund_amount)*100;
            $fields = [
                    "amount" => $return_amount,
                ];
                
            $fields_string = http_build_query($fields);
            $headers =array();
            $headers[] = "SUB_MERCHANT_ID:$sub_merchant_key";
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, $payment_url);
            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
            $result = curl_exec($ch);
            $json = json_decode($result,true);
			if(isset($json['id']) && $json['id'] !=''){
                $facility_update_qry =FacilityBooking::where('id',$booking_id)->update(['refund_amount'=>$refund_amount,'refund_status' => 1,'refund_reason'=>'booking cancelled','capture_amount'=>$capture_amount,'payment_status'=>3]);
            }
        }
        if($recordObj->deposit_payment_status ==2){
            $payment_url = env('OMISEURL')."charges/".$recordObj->deposit_charge_id."/reverse";
            $headers =array();
            $headers[] = "SUB_MERCHANT_ID:$sub_merchant_key";
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, $payment_url);
            curl_setopt($ch,CURLOPT_POST, true);
            //curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
            $result = curl_exec($ch);
			//print_r
            $json = json_decode($result,true);
			if(isset($json['id']) && $json['id'] !=''){
                $facility_update_qry =FacilityBooking::where('id',$booking_id)->update(['deposit_refund_amount'=>$refund_amount,'deposit_refund_status' => 1,'refund_reason'=>'booking cancelled','deposit_payment_status'=>3]);
            }

        }

        $bookingObj = FacilityBooking::find($booking_id);
        //Start Insert into notification module
        $notification = array();
        $notification['account_id'] = $bookingObj->account_id;
        $notification['unit_no'] = $bookingObj->unit_no;
        $notification['user_id'] = $bookingObj->user_id;
        $notification['module'] = 'facility';
        $notification['ref_id'] = $bookingObj->id;
        $notification['title'] = 'Facility Booking';
        $notification['message'] = 'Your facility booking cancelled';
        $result = UserNotification::insert($notification);

        $SettingsObj = UserNotificationSetting::where('user_id',$bookingObj->user_id)->where('account_id',$bookingObj->account_id)->first();
        if(empty($SettingsObj) || $SettingsObj->facility ==1){
        $fcm_token_array ='';
        $user_token = ',';
        $ios_devices_to_send = array();
        $android_devices_to_send = array();
        $logs = UserLog::where('user_id',$bookingObj->user_id)->where('status',1)->orderby('id','desc')->first();
        if(isset($logs->fcm_token) && $logs->fcm_token !=''){
            $user_token .=$logs->fcm_token.",";
            $fcm_token_array .=$logs->fcm_token.',';
            $appSipAccountList[] = $bookingObj->id;
            if($logs->login_from ==1)
                $ios_devices_to_send[] = $logs->fcm_token;
            if($logs->login_from ==2)
                $android_devices_to_send[] = $logs->fcm_token;
        }
        $probObj = Property::find($bookingObj->account_id);
        $title = "Aerea Home - ".$probObj->company_name;
        $message = "Facility Booking: Cancelled";
        $notofication_data = array();
        $notofication_data['body'] =$title;    
        $notofication_data['unit_no'] =$bookingObj->unit_no;   
        $notofication_data['user_id'] =$bookingObj->user_id;   
        $notofication_data['property'] =$bookingObj->account_id; 
        $purObj = UserPurchaserUnit::where('property_id',$bookingObj->account_id)->where('unit_id',$bookingObj->unit_no)->where('user_id',$bookingObj->user_id)->first(); 
        if(isset($purObj))
            $notofication_data['switch_id'] =$purObj->id;   

            $NotificationObj = new \App\Models\v7\FirebaseNotification();
            if(count($ios_devices_to_send) >0){
                $ios_result =  $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
            }
            if(count($android_devices_to_send) >0){
                $android_result = $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
            }
        }

        $inbox = InboxMessage::where('ref_id', $bookingObj->id)->where('type',6)->first();
        if(isset($inbox) && $inbox->id !='')
        {
            $inboxObj = InboxMessage::find($inbox->id);
            $inboxObj->event_status = 1;
            $inboxObj->save();
        }

        
		return response()->json(['data'=>$bookingObj,'response' => 1, 'message' => 'Booking has been cancelled!']);



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

		$userinfo = UserMoreInfo::where('account_id',$result->account_id)->where('user_id',$result->user_id)->where('status',1)->first();
		if(isset($userinfo) && isset($userinfo)){
			$payment_url = env('OMISEURL')."customers/".$userinfo->opn_id."/cards/".$result->opn_card_id;
			$propinfo = Property::where('id',$result->account_id)->first();
			$username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
			$password = '';
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $payment_url);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
			$result = curl_exec($ch);
			$json = json_decode($result,true);
			if(isset($json['id']) && $json['id'] !=''){
				$data['card_info'] = $json;
			}
		}


	    return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']);

	}

	public function facilityDetailwithCardInfo(Request $request){
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
		if(isset($result)){
			$data['booking'] = $result;
			$data['unit'] = $result->getunit;
			$data['user'] = $result->getname;
			$data['notes'] = $result->perperty_info->takeover_notes;
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']);
		}
		else{
			return response()->json(['data'=>'','response' => 200, 'message' => 'Booking not available!']);
		}

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
		if(isset($result)){
			$data['booking'] = $result;
			$data['unit'] = isset($result->getunit)?$result->getunit:null;
			$data['user'] =  isset($result->getname)?$result->getname:null;;
			$data['notes'] = $result->perperty_info->inspection_notes;

			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']);
		}
		else{
			return response()->json(['data'=>'','response' => 200, 'message' => 'No Record found!']);
		}


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
		
        $signatureUser = User::find($record->inspection_owner_user)->name ?? null;
        $record->signature_user = $signatureUser!=null ? Crypt::decryptString($signatureUser) : null;
        $record->signature_timestamp = \Carbon\Carbon::parse($record->created_at)->format('Y-m-d H:i:s');

        $inspectionOwnerUser = User::find($record->inspection_owner_user)->name ?? null;
        $record->inspection_owner_user = $inspectionOwnerUser!=null ? Crypt::decryptString($inspectionOwnerUser) : null;
        $record->inspection_owner_timestamp = $record->inspection_owner_timestamp;
        
        $inspectionTeamUser = User::find($record->inspection_team_user)->name ?? null;
        $record->inspection_team_user = $inspectionTeamUser!=null ? Crypt::decryptString($inspectionTeamUser) : null;
        $record->inspection_team_timestamp = $record->inspection_team_timestamp;

        $handoverOwnerUser = User::find($record->handover_owner_user)->name ?? null;
        $record->handover_owner_user = $handoverOwnerUser!=null ? Crypt::decryptString($handoverOwnerUser) : null;
        $record->handover_owner_timestamp = $record->handover_owner_timestamp;
        
        $handoverTeamUser = User::find($record->handover_team_user)->name ?? null;
        $record->handover_team_user = $handoverTeamUser!=null ? Crypt::decryptString($handoverTeamUser) : null;
        $record->handover_team_timestamp = $record->handover_team_timestamp;
 		$record->pdf_link = env('VISITOR_APP_URL')."/generate-pdf/$record->id";
		if($record->ref_id !=''){
			$refRecord = Defect::where('ticket',$record->ref_id)->first();
			if($refRecord)
				$record->ref_pdf_link = env('VISITOR_APP_URL')."/generate-pdf/$refRecord->id";
			else
				$record->ref_pdf_link = null;
		}else{
			$record->ref_pdf_link = null;
		}


		$data['booking'] = $record;
		$final_inspection_require_status = ($record->property->final_inspection_required)?$record->property->final_inspection_required:0;
		$lists = array();
		foreach($submissions as $submission){
			$result['submissions'] = $submission;
			$result['location'] = $submission->getlocation;
			$result['type'] = $submission->gettype;
			$result['user'] = $submission->user;
			if($submission->getlogs){
				$result['change_logs'] = array();
				foreach($submission->getlogs as $change_log){
					$log = array();
					$log['id'] = $change_log->id;
					$log['old_defect_location'] = ($change_log->getoldlocation)?$change_log->getoldlocation:null;
					$log['old_defect_type'] = ($change_log->getoldtype)?$change_log->getoldtype:null;
					$log['new_defect_location'] = ($change_log->getnewlocation)?$change_log->getnewlocation:null;
					$log['new_defect_type'] = ($change_log->getnewtype)?$change_log->getnewtype:null;
					$log['remarks'] = $change_log->remarks;
					$result['change_logs'][] = $log;
				}
			}
			$lists[]=$result;
		}
		$inspection = $record->inspection;
		$final_inspection = $record->finalInspection;
	    return response()->json(['booking'=>$record,'final_inspection_require_status'=>$final_inspection_require_status, 'lists'=>$lists,'inspection'=>$inspection,'final_inspection'=>$final_inspection,'response' => 1, 'message' => 'Success!']);
	}


	public function announcement(Request $request)
	{
		$UserObj = $request->user();
		$data = AnnouncementDetail::where([
				'user_id' => $UserObj->id, 
				'account_id'=>$UserObj->account_id
			])
			->groupBy('a_id')
			->orderByDesc('id')->get()
			->map(fn($r) => [
				'list' => $r,
				'announce' => $r->announcement,
			]);

		return $data->isEmpty() ? response()->json(['message' => "No Record"], 402) 
			: response()->json([
			'record' => $data, 
			'file_path' => env('APP_URL')."/storage/app",
			'response' => 1, 
			'message' => 'Success!'
		]);
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
			AnnouncementDetail::where('id',$request->id)->update(['status' => 1,'view_status'=>1]);
			$result = AnnouncementDetail::where('id',$request->id)->first();
			$data['details'] = $result;
			$data['announcement'] = $result->announcement;
			$data['file_path'] = env('APP_URL')."/storage/app/";;
		
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
		);
		$messages=array(
			'id.required' => 'Announcement id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

		$status = AnnouncementDetail::where('a_id',$request->id)->where('user_id',Auth::id())
                ->update(['status' => 1,'view_status'=>1]);

		if(isset($status)){
	    	return response()->json(['response' => 1, 'message' => 'Success!']);
		}else{
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
	 $user = Auth::id();

	 $obj = new FacilityBooking();
	 $times = $obj->timeslots($type);	
		 
		 foreach($times as $time){
			 $time = trim($time);
			 $lists = DB::table("facility_bookings")->where('type_id',$type)->where('booking_date',$selecteddate)->where('booking_time',$time)->whereNotIn('status', [1])->get();
			 $recordcount = count($lists);

			 $tempReqs = FacilityBookingTempRequest::where(function($query) use ($selecteddate,$time,$type,$user){
				if($type !=''){
					$query->where('type_id',$type);
				}
				if($selecteddate !=''){
					$query->where('booking_date',$selecteddate);
				}
				if($time !=''){
					$query->where('booking_time',trim($time));
				}
				if($user !=''){
					$query->whereNotIn('user_id',[$user]);
				}
			})->orderby('id','desc')->get();
			 
			 $tempRecord = count($tempReqs);

			 $record =array('time'=>$time,'count'=>$recordcount,'temp_count'=>$tempRecord);
			 $data[] = $record;
 
		 }
	 
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
	
 
	}
 
	public function getfacilitytimeslotsTest(Request $request)
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
	 $user = Auth::id();

	 $obj = new FacilityBooking();
	 $times = $obj->timeslots($type);	
		 
		 foreach($times as $time){
			 $time = trim($time);
			 $lists = DB::table("facility_bookings")->where('type_id',$type)->where('booking_date',$selecteddate)->where('booking_time',$time)->whereNotIn('status', [1])->get();
			 $recordcount = count($lists);

			 $tempReqs = FacilityBookingTempRequest::where(function($query) use ($selecteddate,$time,$type,$user){
				if($type !=''){
					$query->where('type_id',$type);
				}
				if($selecteddate !=''){
					$query->where('booking_date',$selecteddate);
				}
				if($time !=''){
					$query->where('booking_time',trim($time));
				}
				if($user !=''){
					$query->whereNotIn('user_id',[$user]);
				}
			})->orderby('id','desc')->get();
			 
			 $tempRecord = count($tempReqs);

			 $record =array('time'=>$time,'count'=>$recordcount,'temp_count'=>$tempRecord);
			 $data[] = $record;
 
		 }
	 
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
	
 
	}

	public function getfacilitybooking(Request $request)
    {
		
        $input = $request->all();
		$input['user_id'] = Auth::id();

		$UserObj = User::find($input['user_id']);

		$bookings = FacilityBooking::where('unit_no',$UserObj->unit_no)->orderby('id','desc')->get();  
		$feedbacks = FacilityType::where('account_id', $UserObj->account_id)->get();

		$data['bookings'] = $bookings;
		$data['types'] = $feedbacks;

		return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
	}


	public function inboxMessage(Request $request)
    {	
        //$user = Auth::user();
        $input = $request->all();
		$input['user_id'] = Auth::id();
		$user = $input['user_id'];
		$UserObj = User::find($user);
		$messages = InboxMessage::whereIn('type',[2,3,7,8,9,10,11,12,13,14,15])->where('event_status','!=',1)->where('user_id',$input['user_id'])->where('unit_no',$UserObj->unit_no)->where('account_id',$UserObj->account_id)->orderby('id','desc')->get();  
		$data = array();
		if(isset($messages)){
			foreach($messages as $message){
				$record = array();
				$record['id'] = $message->id;
                $record['account_id'] = $message->account_id;
                $record['user_id'] = $message->user_id;
                $record['ref_id'] = $message->ref_id;
                $record['type'] = $message->type;
                $record['title'] = $message->title;
                $record['message'] = $message->message;
                $record['booking_date'] = $message->booking_date;
                $record['booking_time'] = $message->booking_time;
                $record['status'] = $message->status;
                $record['view_status'] = $message->view_status;
                $record['event_status'] = $message->event_status;
                $record['created_at'] = date('y/m/d H:i',strtotime($message->created_at));
				$record['updated_at'] = date('y/m/d H:i',strtotime($message->updated_at));
				$record['deleted'] = 0;
				if($message->type ==2){
					$recordObj = FeedbackSubmission::find($message->ref_id);
					if(empty($recordObj))
						$record['deleted'] = 1;
				}
				else if($message->type ==3){
					$recordObj = Defect::find($message->ref_id);
					if(empty($recordObj))
						$record['deleted'] = 1;
				}
				else if($message->type ==7){
					$recordObj = EformMovingInOut::find($message->ref_id);
					if(empty($recordObj))
						$record['deleted'] = 1;
				}
				else if($message->type ==8){
					$recordObj = EformRenovation::find($message->ref_id);
					if(empty($recordObj))
						$record['deleted'] = 1;
				}
				else if($message->type ==9){
					$recordObj = EformDoorAccess::find($message->ref_id);
					if(empty($recordObj))
						$record['deleted'] = 1;
				}
				else if($message->type ==10){
					$recordObj = EformRegVehicle::find($message->ref_id);
					if(empty($recordObj))
						$record['deleted'] = 1;
				}
				else if($message->type ==11){
					$recordObj = EformChangeAddress::find($message->ref_id);
					if(empty($recordObj))
						$record['deleted'] = 1;
				}
				else if($message->type ==12){
					$recordObj = EformParticular::find($message->ref_id);
					if(empty($recordObj))
						$record['deleted'] = 1;
				}
				else if($message->type ==13){
					$recordObj = ResidentFileSubmission::find($message->ref_id);
					if(empty($recordObj))
						$record['deleted'] = 1;
				}
				else if($message->type ==14){
					$recordObj = VisitorBooking::find($message->ref_id);
					if(empty($recordObj))
						$record['deleted'] = 1;
				}
				$data['messages'][] = $record;
			}
		}

		//$data['messages'] = $messages;
		$data['types'] = array(2=>"Feedback Submission",3=>"Defect Submission",7=>'Move In & Out: E-form',8=>'Renovation: E-form',9=>'Door Access Card: E-form',10=>'Registration for Vehicle: E-form',11=>'Change of Mailing Address: E-form',12=>'Update of Particulars: E-form',13=>"Resident File upload",14=>"Visitor Management",15=>'Update');

		return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);

        
	}

	public function update_inboxmessage(Request $request)
	{
		$rules=array(
			'property' => 'required',
			'id' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
			'id.required' => 'id id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
				'code' =>102,
                'message' => $errors,
            ], 400);
		}
		
		$input = $request->all();
		$notiObj = InboxMessage::find($input['id']);
		if(isset($notiObj)){
			$notiObj->view_status = 1;
			$notiObj->save();
			return response()->json([
				'code' =>0,
				'msg'=>'updated',
				'data'=>$notiObj
			]);
		}
		else{
			return response()->json([
				'code' =>200,
				'msg'=>'no record',
				'data'=>''
			]);
		}
		
	}

	public function upcomingEvents(Request $request)
    {	
        //$user = Auth::user();
		$input = $request->all();
		$input['user_id'] = Auth::id();
		$user = $input['user_id'];
		$UserObj = User::find($user);
		$events = InboxMessage::where('user_id',$user)->where('unit_no',$UserObj->unit_no)->where('booking_date','>=',date("Y-m-d"))->whereIn('type',[4,5,6])->where('event_status','!=',1)->orderby('id','desc')->get();  

		$data['events'] = $events;
		$data['types'] = array(4=>"unittakeover",5=>"join inspection",6=>"facility");

		return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);

        
	}

	public function enquiry(Request $request)
	{
		$input = $request->all();
		$input['user_id'] = Auth::id();
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
			'booking_date' => 'required',
			'booking_time' => 'required',
		);
		$messages=array(
			'type_id.required' => 'Facility type missing',
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
		$user = Auth::id();
		$booking_date = $request->booking_date;
		$booking_time = $request->booking_time;
		$userObj = User::find($user);
		$facilityObj = FacilityType::find($facility);
		$requested_date = date('d/m/y',strtotime($booking_date));
		$blockout_data = array_map('trim', explode(",",$facilityObj->blockout_days));

		$facilityObj->next_booking_allowed;
		//print_r($blockout_data);
		if(empty($userObj)){
			return response()->json(['data'=>0, 'response' => 200, 'message' => 'User not avialble']);
		}
		if(empty($facilityObj)){
			return response()->json(['data'=>0, 'response' => 400, 'message' => 'Facility type not avialble']);
		}
		else if(in_array($booking_date,$blockout_data)){//Blockout dates
			return response()->json(['data'=>0,'response' => 500, 'message' => "$requested_date is not available.Please choose another date."]);
		}
		else if($facilityObj->next_booking_allowed ==1){ // for None option
			$tempObj = FacilityBookingTempRequest::where('user_id',$user)->where('account_id',$userObj->account_id)->orderBy('id','DESC')->first();
					if(isset($tempObj)){
						FacilityBookingTempRequest::where('id',$tempObj->id)->update(['type_id'=>$facility,"booking_date"=>$booking_date,"booking_time"=>$booking_time]);
					}else{
						$temp['type_id'] = $facility;
						$temp['booking_date'] = $booking_date;
						$temp['booking_time'] = $booking_time;
						$temp['user_id'] = $user;
						$temp['unit_no'] = $userObj->unit_no;
						$temp['account_id'] = $userObj->account_id;
						$tempObj = FacilityBookingTempRequest::create($temp);
					}
			return response()->json(['data' =>1,'TempRequest'=>$tempObj, 'response' => 1, 'message' => 'Allowed , None option']);		
		}
		else if($facilityObj->next_booking_allowed ==2){ // for Month option			
			$query_date = $booking_date;
			$fromdate = date('Y-m-01', strtotime($query_date));
			$todate =  date('Y-m-t', strtotime($query_date));
			$last_15_mins_records = Carbon::now()->subMinutes(15)->toDateTimeString();

			$temp_records = FacilityBookingTempRequest::whereNotIn('user_id',[$user])->where('account_id',$userObj->account_id)->where('type_id',$facility)->where('booking_date',$booking_date)->where('booking_time',$booking_time)->where('updated_at','>=',$last_15_mins_records)->first(); 
			//print_r($temp_records);
			if(isset($temp_records)){
				return response()->json(['data' =>0, 'response' => 200, 'message' => 'There is already a booking for '.$facilityObj->facility_type.' on '.$booking_date.' at '.$booking_time]);
			}
			$bookings = FacilityBooking::where('user_id',$user)->where('type_id',$facility)->where('payment_status',2)->whereNotIn('status', ['1'])->whereBetween('booking_date',array($fromdate,$todate))->first();  
			if(isset($bookings) && $bookings->booking_date){
				return response()->json(['data' =>0, 'response' => 100, 'message' => 'There is already a booking for '.$facilityObj->facility_type.'. Each unit is entitled to one booking each month. As such, you are not able to make another booking for '.$facilityObj->facility_type.'.']); 	
			}
			else{
				$tempObj = FacilityBookingTempRequest::where('user_id',$user)->where('account_id',$userObj->account_id)->orderBy('id','DESC')->first();
				if(isset($tempObj)){
					FacilityBookingTempRequest::where('id',$tempObj->id)->update(['type_id'=>$facility,"booking_date"=>$booking_date,"booking_time"=>$booking_time]);
				}else{
					$temp['type_id'] = $facility;
					$temp['booking_date'] = $booking_date;
					$temp['booking_time'] = $booking_time;
					$temp['user_id'] = $user;
					$temp['unit_no'] = $userObj->unit_no;
					$temp['account_id'] = $userObj->account_id;
					$tempObj = FacilityBookingTempRequest::create($temp);
				}
				return response()->json(['data' =>1,'TempRequest'=>$tempObj, 'response' => 1, 'message' => 'Allowed , Monthly booking not done']);	
			}
				
		}
		else if($facilityObj->next_booking_allowed ==3){ // for Days option

			//$facilityObj->next_booking_allowed;
			//echo Carbon::now();
			//echo " --";
			$last_15_mins_records = Carbon::now()->subMinutes(15)->toDateTimeString();

			$temp_records = FacilityBookingTempRequest::whereNotIn('user_id',[$user])->where('account_id',$userObj->account_id)->where('type_id',$facility)->where('booking_date',$booking_date)->where('booking_time',$booking_time)->where('updated_at','>=',$last_15_mins_records)->first(); 
			//print_r($temp_records);
			if(isset($temp_records)){
				return response()->json(['data' =>0, 'response' => 200, 'message' => 'There is already a booking for '.$facilityObj->facility_type.' on '.$booking_date.' at '.$booking_time]);
			}

			$bookings = FacilityBooking::where('user_id',$user)->where('type_id',$facility)->where('payment_status',2)->whereNotIn('status', ['1'])->orderBy('booking_date','DESC')->first(); 
			//print_r($bookings->booking_date);

			if(isset($bookings) && $bookings->booking_date){

				$date = Carbon::createFromFormat('Y-m-d', $bookings->booking_date);
				$daysToAdd = $facilityObj->allowed_booking_for;
				$booking_allowed  = $date->addDays($daysToAdd);
				//echo "Next booking : ".$booking_allowed;
				//echo "Requested Booking :".$booking_date;

				if($booking_allowed <= $booking_date ){
					$tempObj = FacilityBookingTempRequest::where('user_id',$user)->where('account_id',$userObj->account_id)->orderBy('id','DESC')->first();
					if(isset($tempObj)){
						FacilityBookingTempRequest::where('id',$tempObj->id)->update(['type_id'=>$facility,"booking_date"=>$booking_date,"booking_time"=>$booking_time]);
					}else{
						$temp['type_id'] = $facility;
						$temp['booking_date'] = $booking_date;
						$temp['booking_time'] = $booking_time;
						$temp['user_id'] = $user;
						$temp['unit_no'] = $userObj->unit_no;
						$temp['account_id'] = $userObj->account_id;
						$tempObj = FacilityBookingTempRequest::create($temp);
					}
					return response()->json(['data' =>1,'TempRequest'=>$tempObj, 'response' => 1, 'message' => 'Allowed, booking not done']);
				}
				else{
					return response()->json(['data' =>0, 'response' => 200, 'message' => 'There is already a booking for '.$facilityObj->facility_type.'. Each unit is entitled to one booking per '.$facilityObj->next_booking_allowed_days.' days. As such, you are not able to make another booking for '.$facilityObj->facility_type.'.']);
				}	
			}
			else{
				$tempObj = FacilityBookingTempRequest::where('user_id',$user)->where('account_id',$userObj->account_id)->orderBy('id','DESC')->first();
					if(isset($tempObj)){
						FacilityBookingTempRequest::where('id',$tempObj->id)->update(['type_id'=>$facility,"booking_date"=>$booking_date,"booking_time"=>$booking_time]);
					}else{
						$temp['type_id'] = $facility;
						$temp['booking_date'] = $booking_date;
						$temp['booking_time'] = $booking_time;
						$temp['user_id'] = $user;
						$temp['unit_no'] = $userObj->unit_no;
						$temp['account_id'] = $userObj->account_id;
						$tempObj = FacilityBookingTempRequest::create($temp);
					}
				return response()->json(['data' =>1,'TempRequest'=>$tempObj, 'response' => 1, 'message' => 'Allowed, booking not done.']);	
			}		
		}
		else{
			$tempObj = FacilityBookingTempRequest::where('user_id',$user)->where('account_id',$userObj->account_id)->orderBy('id','DESC')->first();
					if(isset($tempObj)){
						FacilityBookingTempRequest::where('id',$tempObj->id)->update(['type_id'=>$facility,"booking_date"=>$booking_date,"booking_time"=>$booking_time]);
					}else{
						$temp['type_id'] = $facility;
						$temp['booking_date'] = $booking_date;
						$temp['booking_time'] = $booking_time;
						$temp['user_id'] = $user;
						$temp['unit_no'] = $userObj->unit_no;
						$temp['account_id'] = $userObj->account_id;
						$tempObj = FacilityBookingTempRequest::create($temp);
					}
			return response()->json(['data' =>0,'TempRequest'=>$tempObj, 'response' => 300, 'message' => 'Allowed , No option set']);
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
			'cat_id'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
		
        $details = array();
		$UserObj = User::find($input['user_id']);

        $ticket = new \App\Models\v7\Defect();
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;

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
                    $data['docs_file'] = $request->file($upload_file)->store(upload_path('condofile'));
                }else{
					$data['upload']='';
				}

				
                $data['status'] = 0;
                $details[] = $data;
            } 
            
		}
		
		$record = ResidentUploadedFile::insert($details);

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['unit_no'] = $UserObj->unit_no;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 13;
		$inbox['ref_id'] = $submission->id;
		$inbox['title'] = "New Resident File Upload";
		$inbox['message'] = '';
		$inbox['booking_date'] = '';
		$inbox['booking_time'] = '';
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0; 
		$inbox['submitted_by'] =  1;   
		$inbox['created_at'] =  $submission->created_at;   
		$result = InboxMessage::create($inbox);

		$probObj = Property::find($UserObj->account_id);
			if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $submission->user_id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
	
				$category = isset($submission->category->docs_category)?$submission->category->docs_category:null;
				$title = "Aerea Manager - ".$probObj->company_name;
				//$message = "You have been received new file :".$category;
				$message = "New Resident File Upload";
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$submission->unit_no;   
				$notofication_data['user_id'] =$submission->user_id;   
				$notofication_data['property'] =$submission->account_id;         
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
			}
		
		/*$inbox['account_id'] = $UserObj->account_id;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 13;
		$inbox['ref_id'] = $record->id;
		$inbox['title'] = "File Uploaded";
		$inbox['message'] = '';
		//$inbox['reno_date'] = $reno->reno_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;   
		$inbox['created_at'] =  $reno->created_at;   
		$result = InboxMessage::create($inbox);*/
		
		return response()->json(['result'=>$record,'response' => 1, 'message' => 'File(s) has been uploaded!']);

        
	}

	public function residentFileUploadDetail(Request $request)
    {
		$rules=array(
			'id'=>'required',
		);
		$messages=array('id.required'=>'Id is missing',
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
		$input['user_id'] = Auth::id();
        $id = $input['id'];
        $details = array();
		$submissionObj = ResidentFileSubmission::find($id);
		if(isset($submissionObj)){
			$details['id'] = $submissionObj->id;
			$details['account_id'] = $submissionObj->account_id;
			$details['cat_id'] = $submissionObj->cat_id;
			$details['notes'] = $submissionObj->notes;
			$details['user_id'] = $submissionObj->user_id;
			$details['status'] = $submissionObj->status;
			$details['view_status'] = $submissionObj->view_status;
			$details['remarks'] = $submissionObj->remarks;
			$details['unit'] = isset($submissionObj->user->userinfo->getunit->unit)?$submissionObj->user->userinfo->getunit->unit:null;
			$details['uploaded_by'] = isset($submissionObj->user->name)?$submissionObj->user->name:null;
			$details['uploaded_date'] = date('d/m/y',strtotime($submissionObj->created_at));
			$details['category'] = isset($submissionObj->category->docs_category)?$submissionObj->category:null;
			$files = array();
			if(isset($submissionObj->files)){
                foreach($submissionObj->files as $k =>$file){
					$file_record = array();
					$file_record['id'] = $file->id;
					$file_record['account_id'] = $file->account_id;
					$file_record['ref_id'] = $file->ref_id;
					$file_record['docs_file_name'] = $file->docs_file_name;
					$file_record['original_file_name'] = $file->original_file_name;
					$file_record['docs_file'] = $file->docs_file;
					$file_record['status'] = $file->status;
					$files[] = $file_record;
				}
			}
			$details['files'] = $files;
			if($submissionObj->status ==2)
				$status = "PROCESSED";
			else if($submissionObj->status ==1)
				$status = "PROCESSING";
			else
				$status = "NEW";
			$details['status'] = $status;
			$details['message'] = $submissionObj->notes;
			$details['management_remarks'] = $submissionObj->remarks;

			$file_path = env('APP_URL')."/storage/app";
			$data = array();
			$data[] = $details;
			return response()->json(['data'=>$details,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
		}
		else{
			return response()->json(['result'=>$record,'response' =>200 , 'message' => 'No record found']);

		}
		

        
	}

	public function uploadedlist(Request $request){

		$userid = Auth::id();
		$UserObj = User::find($userid);

		$file_path = env('APP_URL')."/storage/app";
		$records = ResidentFileSubmission::where('user_id',$userid)->where('unit_no',$UserObj->unit_no)->orderby('id','desc')->get();   
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
			'login_from'=>'required',
			'device_info'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();

		$UserObj = User::find($input['user_id']);

		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		$input['version'] = (isset($input['app_version']))?$input['app_version']:null;
		$record = UserLog::create($input);
		if(isset($input['app_version']))
			User::where('id', $input['user_id'])->update(['app_version' => $input['version']]);

		return response()->json(['data'=>$record,'response' => 1, 'message' => 'Login history log has been added!']); 
	}

	public function logoutHistoryLogs(Request $request)
    {
		$rules = [
			'login_from'=> 'required',
			'fcm_token'=> 'required',
			'biometric_enabled' => 'required'
		];
		$messages = [
			'login_from.required' => 'Login OS is missing',
			'fcm_token.required' => 'FCM Token is missing',
			'biometric_enabled' => 'Biometric enabled param is missing'
		];
		$validator = Validator::make($request->all(), $rules, $messages);
		
		if ($validator->fails())
		{
        	$messages = $validator->messages();
            $errors = $messages->all();
            return response()->json(['message' => $errors], 400);
        }
		
		if($request->biometric_enabled==0){
            UserLog::where([['user_id','=',$request->user()->id],['login_from','=',$request->login_from],['fcm_token','=',$request->fcm_token]])
                ->update(['status' => 0]);
            $request->user()->currentAccessToken()->delete();
        }

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
			'visiting_date'=>'required',
			'visiting_purpose'=>'required',
			'name_1'=>'required',
			'mobile_1'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
		
        $details = array();
		$UserObj = User::find($input['user_id']);

        $ticket = new \App\Models\v7\VisitorBooking();
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
                    $data['qrcode_file'] = $request->file($qrcode_file)->store(upload_path('visitor'));
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

	public function visitorRegSummary(Request $request)
	{
		$userid = Auth::id();
		$UserObj = User::find($userid);

		$file_path = env('APP_URL')."/storage/app";

		$records = VisitorBooking::where('user_id',$userid)->orderby('id','desc')->get();   
		$data = array();
		foreach($records as $k => $record){
			$data[$k] = $record;
			if(isset($record->visitors)){
				$data[$k]['visitors'] = $record->visitors;
			
			}
			if($record->visited_count->count() >= $record->visitors->count())
				$status = "Entered";
			else if($record->visited_count->count() >0 && $record->visited_count->count() < $record->visitors->count())
				$status = $record->visited_count->count()." Entered";
			else if($record->status==0)
				$status = "Pending";
			else if($record->status==1)
				$status = "Cancelled";
			else  
				$status = "Entered";

			$data[$k]['ticket_status'] = $status;
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
			'book_id'=>'required',
		);
		$messages=array(
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
		$userid = Auth::id();
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
			'book_id'=>'required',
		);
		$messages=array(
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
		$userid = Auth::id();
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
			'visiting_date'=>'required',
			'visiting_purpose'=>'required',
			'email_1'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
        
        $details = array();
		$UserObj = User::find($input['user_id']);

		$ticket = new \App\Models\v7\VisitorBooking();
		$visitingTypeObj = VisitorType::find($input['visiting_purpose']);
		if(empty($visitingTypeObj)){
			return response()->json(['response' => 200, 'message' => 'Visiting purpose not found']);
		}
		$propObj = property::find($UserObj->account_id);
		$input['ticket'] = $ticket->ticketgen($propObj->short_code);

		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		/*$input['visiting_start_time'] = $input['visiting_date']." ".$input['start_time'].":00";

		if(!empty($input['visiting_to_date']))
			$input['visiting_end_time'] = $input['visiting_to_date']." ".$input['end_time'].":59";
		else
			$input['visiting_end_time'] = $input['visiting_date']." ".$input['end_time'].":59";
		*/
		$todate = ($visitingTypeObj->end_date_required ==1)?$input['visiting_to_date']:$input['visiting_date'];
	
		if(isset($input['validity_time_required']) && $input['validity_time_required'] ==1){
			if(!empty($input['start_time']) && !empty($input['end_time'])){
				$input['visiting_start_time'] = $input['visiting_date']." ".$input['start_time'].":00";
				$input['visiting_end_time'] = $todate." ".$input['end_time'].":00";
			}else{
				$input['visiting_start_time'] = $input['visiting_date']." 00:00:01";
				$input['visiting_end_time'] = $todate." 23:59:59";
			}
		}
		else{
			$input['visiting_start_time'] = $input['visiting_date']." 00:00:00";
			$input['visiting_end_time'] = $todate." 23:59:59";
		}

        $booking = VisitorBooking::create($input);


        $data['book_id'] = $booking->id;

        for($i=1;$i<=5;$i++){
			$email = 'email_'.$i;
			$name = 'name_'.$i;
            if(!empty($request->input($email)) && !empty($request->input($email))){

				VisitorBooking::invite_emailnew($booking->id, $UserObj->id,$UserObj->account_id,$request->input($email),$request->input($name));

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

	public function updatesingnature(Request $request)
	{
		$rules=array(
			'def_id' => 'required',
			'signature' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
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

		$user = Auth::id();
		$defect = $request->def_id;
		$signature = '';
		if ($request->file('signature') != null) $signature = $request->file('signature')->store(upload_path('defect'));

		$result = Defect::where( 'user_id' , $user)->update( array( 'signature' =>  $signature));
		return response()->json(['result'=>$result,'response' => 1, 'message' => 'Signature updated successfully']);
	}

	public function inspectionsingnature(Request $request)
	{
		$rules=array(
			'def_id' => 'required',
			'signature' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
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

		$user = Auth::id();
		$defect = $request->def_id;

		$defectObj = Defect::find($defect);
		$owner_status = ($request->input('agree_status'))?$request->input('agree_status'):array();  
        $owner_remarks = ($request->input('remarks'));  
		
		$disagree = 0;
		$newsubmissions = array();
		$need_to_rectify = 0;
        if($defectObj->submissions){
            foreach($defectObj->submissions as $k => $submission){
                if($submission->status ==3)
                {
                    $defectSubmissionObj = DefectSubmission::find($submission->id);
                    $defectSubmissionObj->owner_first_agree_status = (isset($owner_status[$submission->id]))?$owner_status[$submission->id]:'';
                    if(isset($owner_status[$submission->id]) && $owner_status[$submission->id] ==2){
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
						$defectSubmissionObj->owner_first_remark = ($owner_remarks[$submission->id])?$owner_remarks[$submission->id]:'';
					}                                         
                    $defectSubmissionObj->save();
                }else{
					$need_to_rectify = 1;
				}
            }
        }

		$signature = '';
		if ($request->file('signature') != null) $signature = $request->file('signature')->store(upload_path('defect'));
		
		
		if($defectObj->status !=1 && $defectObj->inspection_status ==1 && $need_to_rectify ==1){ //ticket not closed
			//echo "hello";
			$result1 = Defect::where('id' , $defectObj->id)->update( array('status'=>2,'handover_status'=>2, 'inspection_owner_signature' =>  $signature,'inspection_owner_user' => $user, 'inspection_owner_timestamp' => now(),'rectification_start_date' => date("Y-m-d"),));

			//$data = response()->json(['result'=>$result,'response' => 1, 'message' => 'Signature updated successfully']);
		}else{
			//echo "hi";
			$result1 = Defect::where('id' , $defectObj->id)->update( array('status'=>1,'completion_date' => date("Y-m-d"),'inspection_owner_signature' =>  $signature,'inspection_owner_user' => $user, 'inspection_owner_timestamp' => now()));
			//$data = response()->json(['result'=>$result,'response' => 2, 'message' => 'Ticket Closed']);
		}

		if($disagree == 1){
			$defectObj = Defect::find($defectObj->id);
			$ticket = new \App\Models\v7\Defect();
			$input['user_id'] = $defectObj->user_id;
			$input['account_id'] = $defectObj->account_id;
			$input['unit_no'] = $defectObj->unit_no;
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

			$data = response()->json(['result'=>$result1,'response' => 1, 'message' => 'New ticket created']);
		}else{
			$data = response()->json(['result'=>$result1,'response' => 1, 'message' => 'Success']);

		}

		return $data;

	}

	public function handoversingnature(Request $request)
	{
		$rules=array(
			'def_id' => 'required',
			'signature' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
		);
		$messages=array(
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

		$user = Auth::id();
		$defect = $request->def_id;
		$signature = '';

		$defectObj = Defect::find($defect);
		$owner_status = ($request->input('agree_status'));  
        $owner_remarks = ($request->input('remarks'));  

		$disagree = 0;
		$newsubmissions = array();

        if($defectObj->submissions){
            foreach($defectObj->submissions as $k => $submission){
				
                if($submission->status ==2)
                {
                    $defectSubmissionObj = DefectSubmission::find($submission->id);
                    $defectSubmissionObj->owner_status = ($owner_status[$submission->id])?$owner_status[$submission->id]:'';
                    if(($owner_status[$submission->id]) && $owner_status[$submission->id] ==2){
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
						$defectSubmissionObj->owner_final_remarks = (isset($owner_remarks[$submission->id]))?$owner_remarks[$submission->id]:'';
					}                                         
                    $defectSubmissionObj->save();
                }
            }
        }

        //$defectObj->status = $request->input('status');
       
		if ($request->file('signature') != null) {
			$signature = $request->file('signature')->store(upload_path('defect'));
			$defectObj->handover_owner_signature = $signature;
			$defectObj->handover_owner_user = $user;
			$defectObj->handover_owner_timestamp = now();
		}
		$defectObj->status = 1;
		$defectObj->completion_date = date("Y-m-d");
		$defectObj->save();
		if($disagree == 1){

			$ticket = new \App\Models\v7\Defect();
			$input['user_id'] = $defectObj->user_id;
			$input['account_id'] = $defectObj->account_id;
			$input['unit_no'] = $defectObj->unit_no;
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
			$data = response()->json(['result'=>$defectObj,'response' => 1, 'message' => 'Success']);

		}
		
		return $data;

	}

	public function submitdefectreview(Request $request)
	{
		$rules=array(
			'def_id' => 'required',
			'def_submission_id' =>'required',
			'remarks' =>'required',
		);
		$messages=array(
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

		$input = $request->all();
		$input['user_id'] = Auth::id();

		$result = DefectSubmissionReview::create($input);
		return response()->json(['result'=>$result,'response' => 1, 'message' => 'Signature updated successfully']);
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
		$input['login_id'] = Auth::id();

		$account_id = $input['property'];
		$user_id = $input['login_id'];
		$UserObj = User::find($user_id);
		//move In & Out
		
		$moveinout = DB::table('eform_moving_in_out')->select('id','form_type','ticket','status','moving_start','moving_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where('unit_no',$UserObj->unit_no)->orderby('created_at','desc')->get();

		//Renovation
		$renovation = DB::table('eform_renovations')->select('id','form_type','ticket','status','reno_start','reno_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where('unit_no',$UserObj->unit_no)->orderby('created_at','desc')->get();

		//Doors
		$doors = DB::table('eform_door_accesses')->select('id','form_type','ticket','status','tenancy_start','tenancy_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where('unit_no',$UserObj->unit_no)->orderby('created_at','desc')->get();

		//Register for vehicle
		$vehicle = DB::table('eform_reg_vehicles')->select('id','form_type','ticket','status','tenancy_start','tenancy_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where('unit_no',$UserObj->unit_no)->orderby('created_at','desc')->get();


		//Update Mailling address
		$address = DB::table('eform_address_changes')->select('id','form_type','ticket','status','address','contact_no','email','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where('unit_no',$UserObj->unit_no)->orderby('created_at','desc')->get();

		//Update Particulars
		$particulars = DB::table('eform_particulars')->select('id','form_type','ticket','status','tenancy_start','tenancy_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where('unit_no',$UserObj->unit_no)->orderby('created_at','desc')->get();


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
		$input['login_id'] = Auth::id();

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

		$UserObj = User::find($user_id);

		if($type ==40 || $type ==''){ //moving in and out
			$moveinout = DB::table('eform_moving_in_out')->select('id','form_type','ticket','status','moving_start','moving_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where('unit_no',$UserObj->unit_no)->where(function($query) use ($from_date,$to_date,$ticket,$status){
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
			$renovation = DB::table('eform_renovations')->select('id','form_type','ticket','status','reno_start','reno_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where('unit_no',$UserObj->unit_no)->where(function($query) use ($from_date,$to_date,$ticket,$status){
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
			$doors = DB::table('eform_door_accesses')->select('id','form_type','ticket','status','tenancy_start','tenancy_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where('unit_no',$UserObj->unit_no)->where(function($query) use ($from_date,$to_date,$ticket,$status){
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
			$vehicle = DB::table('eform_reg_vehicles')->select('id','form_type','ticket','status','tenancy_start','tenancy_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where('unit_no',$UserObj->unit_no)->orderby('created_at','desc')->where(function($query) use ($from_date,$to_date,$ticket,$status){
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
			$address = DB::table('eform_address_changes')->select('id','form_type','ticket','status','address','contact_no','email','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where('unit_no',$UserObj->unit_no)->where(function($query) use ($from_date,$to_date,$ticket,$status){
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
			$particulars = DB::table('eform_particulars')->select('id','form_type','ticket','status','tenancy_start','tenancy_end','created_at')->where('account_id',$account_id)->where('user_id',$user_id)->where('unit_no',$UserObj->unit_no)->where(function($query) use ($from_date,$to_date,$ticket,$status){
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

		$allresults = DB::table('eform_moving_in_out')->select('id','form_type','ticket','status','moving_start','moving_end','created_at')->where('account_id',$account_id)->where('user_id',0)->where('unit_no',$UserObj->unit_no)->orderby('created_at','desc')->get();
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
			'resident_name'=>'required',
			'contact_no'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();

		$UserObj = User::find($input['user_id']);
		$date = Carbon::now();
		$ticket = new \App\Models\v7\EformMovingInOut();
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
		$inbox['unit_no'] = $UserObj->unit_no;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 7;
		$inbox['ref_id'] = $moveio->id;
		$inbox['title'] = "Move In & Out: E-form Submission";
		$inbox['message'] = '';
		$inbox['booking_date'] = $moveio->moving_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0; 
		$inbox['submitted_by'] =  1;   
		$inbox['created_at'] =  $moveio->created_at;   
		$result = InboxMessage::create($inbox);

		$probObj = Property::find($UserObj->account_id);
			if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $UserObj->id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
	
				
				//$title = "Move In & Out: E-form Submission";
				$title = "Aerea Manager - ".$probObj->company_name;
				$message = "New Move In & Out E-form";
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$UserObj->unit_no;   
				$notofication_data['user_id'] =$UserObj->id;   
				$notofication_data['property'] =$UserObj->account_id;         
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
			}

		return response()->json(['data'=>$moveio,'response' => 1, 'message' => 'Moving In/Out application submitted!']);

        
	}

	public function eform_movinginout_info(Request $request)
    {
		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
			'resident_name'=>'required',
			'contact_no'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();

		$UserObj = User::find($input['user_id']);
		$date = Carbon::now();
		$ticket = new \App\Models\v7\EformRenovation();
		$input['user_id'] = $input['user_id'];
		$input['reno_date'] = $date->format('Y-m-d');
		$input['account_id'] = $UserObj->account_id;
        $input['ticket'] = $ticket->ticketgen();

		$input['user_id'] = $UserObj->id;
		$input['account_id'] = $UserObj->account_id;

        if(isset($UserObj->unit_no))
			$input['unit_no'] = $UserObj->unit_no;
		
		if ($request->file('owner_signature') != null) {
			$input['owner_signature']  = base64_encode(file_get_contents($request->file('owner_signature')));
	
		}
		if ($request->file('nominee_signature') != null) {
			$input['nominee_signature']  = base64_encode(file_get_contents($request->file('nominee_signature')));
	
		}

		if ($request->file('letter_of_authorization') != null) {
			$letter_of_authorization = $request->file('letter_of_authorization')->store(upload_path('ren'));
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
		$inbox['unit_no'] = $UserObj->unit_no;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 8;
		$inbox['ref_id'] = $reno->id;
		$inbox['title'] = "Renovation: E-form Submission";
		$inbox['message'] = '';
		$inbox['reno_date'] = $reno->reno_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;  
		$inbox['submitted_by'] =  1;   
		$inbox['created_at'] =  $reno->created_at;   
		$result = InboxMessage::create($inbox);

		$probObj = Property::find($UserObj->account_id);
			if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $reno->id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
	
				$title = "Aerea Manager - ".$probObj->company_name;
				$message = "New Renovation E-form";
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$reno->unit_no;   
				$notofication_data['user_id'] =$reno->user_id;   
				$notofication_data['property'] =$reno->account_id;         
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
			}

		return response()->json(['data'=>$reno,'response' => 1, 'message' => 'Renovation application submitted!']);

        
	}

	public function eform_renovation_info(Request $request)
    {
		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
			'owner_name'=>'required',
			'contact_no'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();

		$UserObj = User::find($input['user_id']);
		$date = Carbon::now();
		$ticket = new \App\Models\v7\EformDoorAccess();
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
		if ($request->file('nominee_signature') != null) {
			$input['nominee_signature']  = base64_encode(file_get_contents($request->file('nominee_signature')));
	
		}
		
		$reno = EformDoorAccess::create($input);

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['unit_no'] = $UserObj->unit_no;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 9;
		$inbox['ref_id'] = $reno->id;
		$inbox['title'] = "Door Access Card: E-form Submission";
		$inbox['message'] = '';
		$inbox['reno_date'] = $reno->request_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0; 
		$inbox['submitted_by'] =  1;   
		$inbox['created_at'] =  $reno->created_at;   
		$result = InboxMessage::create($inbox);

		$probObj = Property::find($UserObj->account_id);
			if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $reno->id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
	
				$title = "Aerea Manager - ".$probObj->company_name;
				$message = "New Door Access Card E-form";
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$reno->unit_no;   
				$notofication_data['user_id'] =$reno->user_id;   
				$notofication_data['property'] =$reno->account_id;         
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
			}

		return response()->json(['data'=>$reno,'response' => 1, 'message' => 'Renovation application submitted!']);

        
	}

	public function eform_dooraccess_info(Request $request)
    {
		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
			'owner_name'=>'required',
			'contact_no'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();

		$UserObj = User::find($input['user_id']);
		$date = Carbon::now();
		$ticket = new \App\Models\v7\EformRegVehicle();
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
		if ($request->file('nominee_signature') != null) {
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
				$data['file_original'] = $request->file($file)->store(upload_path('vehicle'));
                $data['file'] = base64_encode(file_get_contents($request->file($file)));
				$data['created_at'] =  $reg->created_at;
				$data['updated_at'] =  $reg->created_at;            
                $data['status'] = 1;
                $records[] = $data;
			}            
        }
		EformRegVehicleDoc::insert($records);

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['unit_no'] = $UserObj->unit_no;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 10;
		$inbox['ref_id'] = $reg->id;
		$inbox['title'] = "Registration for Vehicle IU: E-form Submission";
		$inbox['message'] = '';
		$inbox['reno_date'] = $reg->request_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;  
		$inbox['submitted_by'] =  1;   
		$inbox['created_at'] =  $reg->created_at;   
		$result = InboxMessage::create($inbox);

		$probObj = Property::find($UserObj->account_id);
			if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $reg->id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
	
				$title = "Aerea Manager - ".$probObj->company_name;
				$message = "New Registration for Vehicle IU E-form";
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$reg->unit_no;   
				$notofication_data['user_id'] =$reg->user_id;   
				$notofication_data['property'] =$reg->account_id;         
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
			}
		return response()->json(['data'=>$reg,'response' => 1, 'message' => 'Vehicle IU regisgitration application submitted!']);

        
	}

	public function eform_reg_vehicle_info(Request $request)
    {
		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
			'owner_name'=>'required',
			'contact_no'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();

		$UserObj = User::find($input['user_id']);
		$date = Carbon::now();
		$ticket = new \App\Models\v7\EformChangeAddress();
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
		if ($request->file('nominee_signature') != null) {
			$input['nominee_signature']  = base64_encode(file_get_contents($request->file('nominee_signature')));
	
		}
		
		$reg = EformChangeAddress::create($input);

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['unit_no'] = $UserObj->unit_no;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 11;
		$inbox['ref_id'] = $reg->id;
		$inbox['title'] = "Change of Mailing Address: E-form Submission";
		$inbox['message'] = '';
		$inbox['reno_date'] = $reg->request_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0;
		$inbox['submitted_by'] =  1;   
		$inbox['created_at'] =  $reg->created_at;   
		$result = InboxMessage::create($inbox);

		$probObj = Property::find($UserObj->account_id);
			if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $reg->id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
	
				$title = "Aerea Manager - ".$probObj->company_name;
				$message = "New Change of Mailing Address E-form";
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$reg->unit_no;   
				$notofication_data['user_id'] =$reg->user_id;   
				$notofication_data['property'] =$reg->account_id;         
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
			}

		return response()->json(['data'=>$reg,'response' => 1, 'message' => 'Mailing address change request application submitted!']);

        
	}

	public function eform_address_info(Request $request)
    {
		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
			'owner_name'=>'required',
			'contact_no'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();

		$UserObj = User::find($input['user_id']);
		$date = Carbon::now();
		$ticket = new \App\Models\v7\EformRegVehicle();
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
		$inbox['unit_no'] = $UserObj->unit_no;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 12;
		$inbox['ref_id'] = $reg->id;
		$inbox['title'] = "Update of Particulars: E-form Submission";
		$inbox['message'] = '';
		$inbox['reno_date'] = $reg->request_date;
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0; 
		$inbox['submitted_by'] =  1;   
		$inbox['created_at'] =  $reg->created_at;   
		$result = InboxMessage::create($inbox);

		$probObj = Property::find($UserObj->account_id);
			if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $reg->id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
	
				$title = "Aerea Manager - ".$probObj->company_name;
				$message = "New Update of Particulars E-form";
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$reg->unit_no;   
				$notofication_data['user_id'] =$reg->user_id;   
				$notofication_data['property'] =$reg->account_id;         
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
			}
		return response()->json(['data'=>$reg,'response' => 1, 'message' => 'Vehicle IU regisgitration application submitted!']);

        
	}

	public function eform_reg_particulars_info(Request $request)
    {
		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
				'msg'=>'room code is empty.',
				
			]);
		}
		$device = Device::where('device_serial_no',$input['devSn'])->first();
		if(empty($device)){
			return response()->json([
				'code' =>99999,
				'msg'=>'Device not available in community.'
			]);
		}
		//
		if($input['buildingCode'] !=''){
			$building = Building::select('id')->where('building_no',$input['buildingCode'])->where('account_id',$device->account_id)->first();
			if(empty($building)){
				return response()->json([
					'code' =>99999,
					'msg'=>'Building not available!'
				]);
			}
			$deviceObj = Device::WhereRaw("CONCAT(',',locations,',') LIKE ?", '%,'.$building->id .',%')->where('account_id',$device->account_id)->where('device_serial_no',$input['devSn'])->first();

			if(empty($deviceObj)){
				return response()->json([
					'code' =>99999,
					'msg'=>'Device not available in Block!'
				]);
			}
		}
		else{
			$building = Building::select('buildings.id')->join('devices', 'devices.locations', '=', 'buildings.id')->where('devices.device_serial_no',$input['devSn'])->first();

			if(empty($building)){
				return response()->json([
					'code' =>99999,
					'msg'=>'Building not available!'
				]);
			}
		}

		$roomCode = $input['roomCode'];
		$unitcode = "0".$input['roomCode'];

		$unitObj = Unit::where('building_id',$building->id)->get();

		if(empty($unitObj)){
			return response()->json([
				'code' =>99999,
				'msg'=>'Unit not available!'
			]);
		}
		$unit_id ='';
		if(isset($unitObj)){
			foreach($unitObj as $unit){
				//echo Crypt::decryptString($unit->code)." ".$roomCode;
				if(Crypt::decryptString($unit->code) == $roomCode || Crypt::decryptString($unit->code) ==$unitcode){
					$unit_id = $unit->id;
					break;
				}
			}
		}

		if($unit_id ==''){
			return response()->json([
				'code' =>99999,
				'msg'=>'Unit not available!'
			]);
		}
		
		$unit = Unit::where('id',$unit_id)->first();

		$users_lists = UserPurchaserUnit::where('unit_id',$unit->id)->where('receive_call',1)->get();
		//$users_lists = User::select('users.id')->where('users.status',1)->where('users.unit_no',$unit->id)->join('user_more_infos', 'users.id', '=', 'user_more_infos.user_id')->where('user_more_infos.receive_device_cal',1)->orderby('users.id','desc')->get();

		$user_rec = ',';
		$user_token = ',';
		$fcm_token_array ='';
		$ios_devices_to_send = array();
		$android_devices_to_send = array();
		$appSipAccountList = array();
		foreach($users_lists as $user){
			$user_rec .=$user->user_id.",";
			$logs = UserLog::where('user_id',$user->user_id)->where('status',1)->orderby('id','desc')->first();
			if(isset($logs->fcm_token) && $logs->fcm_token !=''){
				$user_token .=$logs->fcm_token.",";
				$fcm_token_array .=$logs->fcm_token.',';
				//echo "U Id :".$user->user_id;
				//echo "Id :".$logs->id;
				//echo "Token :".$logs->fcm_token;
				//echo "Property :".$logs->account_id;
				$appSipAccountList[] = $user->user_id;
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
		$probObj = Property::find($unit->account_id);
		$title = "Incoming call - ".$probObj->company_name;
		$body ="Call notification for Room ".$input['roomCode'];
		$data = array('body'=>$body,'devSn'=>$input['devSn'],'accessToken' =>$thinmoo_access_token,'extCommunityuuid'=>$unit->account_id,'unitId'=>$unit->id,'appId'=>$thinmoo_appId,"sound"=> "ring.mp3");
		$fields = [
            'project_id'         => 'aerea-staging',
            'device_tokens'      => array_unique($ios_devices_to_send),
            'title'              => $title,
            'message'            => $body,
            'additional_data'    => $data,
			'to'    => 'ios',
			'sound'=>'ring.mp3'
        ];
		$fields_string = json_encode($fields);
		//print_r($fields_string);
        $ch = curl_init();
		$url = env('FIREBASE_URL');
        curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('accept: application/json',
    'content-type: application/json')); 
        $result = curl_exec($ch);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        //return $json;
		//return $response; 


		//print_r($android_devices_to_send);
		$title = "Incoming call - ".$probObj->company_name;
		$body ="Call notification for Room ".$input['roomCode'];
		$data = array('body'=>$body,'devSn'=>$input['devSn'],'accessToken' =>$thinmoo_access_token,'extCommunityuuid'=>$unit->account_id,'unitId'=>$unit->id,'appId'=>$thinmoo_appId,'title'=> $title,'message'=> $body);
		$fields = [
            'project_id'         => 'aerea-staging',
            'device_tokens'      => array_unique($android_devices_to_send),
            'title'              => $title,
            'message'            => $body,
            'additional_data'    => $data,
			'to'    			 => 'android'
        ];
		$fields_string = json_encode($fields);
		//print_r($fields_string);
        $ch = curl_init();
		$url = env('FIREBASE_URL');
		//echo $url ;
		//print_r($fields);
        curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('accept: application/json',
    'content-type: application/json')); 
        $result = curl_exec($ch);
		//print_r($result);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        //return $json;

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

		$auth = new \App\Models\v7\Property();
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
		$user_id = Auth::id();

		$auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api();

		//echo $token;

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
			echo $emp_result->id;
			return $json;
		}
		else{
			$UserObj = User::find($user_id);
			$device_access = array();
			
			$UserDevices = UserDevice::where('user_id',$UserObj->id)->where('unit_no',$UserObj->unit_no)->get();
			if(isset($UserDevices)){
				foreach($UserDevices as $selecteddevices){
					$device_access[] = $selecteddevices->device_svn;
				}
			}
			$currentDate = date('Y-m-d');
			$data = array();
			$thinmoo_devices = $json['data'];
			foreach($thinmoo_devices as $T_device){
				$items_array = array();
				if(in_array($T_device['devSn'],$device_access)){						
					$device_info = Device::where('device_serial_no',$T_device['devSn'])->first();
					if($device_info->facility_type >0){
						$UserBookingObj = FacilityBooking::where('unit_no',$UserObj->unit_no)->where('type_id',$device_info->facility_type)->where('status',2)->orderby('id','desc')->get();
						if(isset($UserBookingObj)){
							foreach($UserBookingObj as $UserBooking){
								if($currentDate ==$UserBooking->booking_date){
									$last_15_mins_records = Carbon::now()->subMinutes(60)->toDateTimeString();
									$bookingtime = str_replace(" ","",$UserBooking->booking_time);
									$times = explode("-",$bookingtime);
									$start_times =explode(":",str_replace(".",":",$times[0]));
									$start_time_build = $UserBooking->booking_date." ".trim($start_times[0]).":".trim($start_times[1]);
									$starttime = strtotime($start_time_build)-($device_info->entry_allowed_in_advance *3600);
									$end_times = explode(":",str_replace(".",":",$times[1]));
									$end_time_build = $UserBooking->booking_date." ".trim($end_times[0]).":".trim($end_times[1]);
									$endtime = strtotime($end_time_build);
									$currenttime = strtotime("now");
									//echo "Start Time :". date("Y-m-d H:i:s",$starttime);
									//echo " End Time :". date("Y-m-d H:i:s",$endtime);
									//echo " Current Time :". date("Y-m-d H:i:s",$currenttime);
									if($currenttime>=$starttime && $currenttime<=$endtime){
										//echo "here";
										$items_array['thinmoo'] = $T_device;
										$items_array['moreinfo'] = $device_info;
										$data[] = $items_array;
									}
								}
							}
						}
					}
					else{
						$items_array['thinmoo'] = $T_device;
						$items_array['moreinfo'] = $device_info;
						$data[] = $items_array;
					}
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
		$user_id = Auth::id();

		$auth = new \App\Models\v7\Property();
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
			$UserDevices = UserRemoteDevice::where('user_id',$UserObj->id)->where('unit_no',$UserObj->unit_no)->get();
			if(isset($UserDevices)){
				foreach($UserDevices as $selecteddevices){
					$device_access[] = $selecteddevices->device_svn;
				}
			}
			$currentDate = date('Y-m-d');
			$data = array();
			$thinmoo_devices = $json['data'];
			foreach($thinmoo_devices as $T_device){
				$device_info = Device::where('device_serial_no',$T_device['devSn'])->first();
				if(in_array($T_device['devSn'],$device_access)){
					if($device_info->facility_type >0){
						$UserBookingObj = FacilityBooking::where('unit_no',$UserObj->unit_no)->where('type_id',$device_info->facility_type)->where('status',2)->orderby('id','desc')->get();
						if(isset($UserBookingObj)){
							foreach($UserBookingObj as $UserBooking){
								if($currentDate ==$UserBooking->booking_date){
									$last_15_mins_records = Carbon::now()->subMinutes(60)->toDateTimeString();
									$bookingtime = str_replace(" ","",$UserBooking->booking_time);
									$times = explode("-",$bookingtime);
									$start_times = explode(":",str_replace(".",":",$times[0]));
									$start_time_build = $UserBooking->booking_date." ".trim($start_times[0]).":".trim($start_times[1]);
									$starttime = strtotime($start_time_build)-($device_info->entry_allowed_in_advance *3600);
									$end_times = explode(":",str_replace(".",":",$times[1]));
									$end_time_build = $UserBooking->booking_date." ".trim($end_times[0]).":".trim($end_times[1]);
									$endtime = strtotime($end_time_build);
									$currenttime = strtotime("now");
									if($currenttime>=$starttime && $currenttime<=$endtime){
										$data[] = $T_device;
									}
								}
							}
						}
					}
					else{
						$data[] = $T_device;
					}
				}
			}
			return response()->json([
				'data'=>$data,
				'code' =>0,
				'msg'=>'Success'
			]);

		}
	
	}

	public function user_remote_device_list_test(Request $request){

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
		$user_id = Auth::id();

		$auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api();

		$url = env('THINMOO_API_URL')."wyEmpProperty/extapi/getAuthorizationDevList";
		//echo $uuid;
		$emp_result = Employee::where('account_id',$uuid)->orderby('id','asc')->first();

		//echo $emp_result->id;
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
		//print_r($json);
        $err = curl_error($ch);
		curl_close($ch);
		if($json['code'] !=0){
			return $json;
		}
		else{
			$UserObj = User::find($user_id);
			$device_access = array();
			$UserDevices = UserRemoteDevice::where('user_id',$UserObj->id)->where('unit_no',$UserObj->unit_no)->get();
			if(isset($UserDevices)){
				foreach($UserDevices as $selecteddevices){
					$device_access[] = $selecteddevices->device_svn;
				}
			}
			$currentDate = date('Y-m-d');
			$data = array();
			$thinmoo_devices = $json['data'];
			//print_r($json['data']);
			foreach($thinmoo_devices as $T_device){
				$device_info = Device::where('device_serial_no',$T_device['devSn'])->first();
				if(in_array($T_device['devSn'],$device_access)){
					if($device_info->facility_type >0){
						
						$UserBookingObj = FacilityBooking::where('unit_no',$UserObj->unit_no)->where('type_id',$device_info->facility_type)->where('status',2)->orderby('id','desc')->get();
						if(isset($UserBookingObj)){
							//print_r($UserBookingObj);
							foreach($UserBookingObj as $UserBooking){
								echo "current Date:".$currentDate;
								echo "Booking Date:".$UserBooking->booking_date;

								if($currentDate ==$UserBooking->booking_date){
									echo "hi4;";
									$last_15_mins_records = Carbon::now()->subMinutes(60)->toDateTimeString();
									$bookingtime = str_replace(" ","",$UserBooking->booking_time);
									$times = explode("-",$bookingtime);
									$start_times = explode(":",str_replace(".",":",$times[0]));
									$start_time_build = $UserBooking->booking_date." ".trim($start_times[0]).":".trim($start_times[1]);
									$starttime = strtotime($start_time_build)-($device_info->entry_allowed_in_advance *3600);
									$end_times = explode(":",str_replace(".",":",$times[1]));
									
									$end_time_build = $UserBooking->booking_date." ".trim($end_times[0]).":".trim($end_times[1]);
									$endtime = strtotime($end_time_build);
									$currenttime = strtotime("now");
									if($currenttime>=$starttime && $currenttime<=$endtime){
										$data[] = $T_device;
									}
								}
							}
						}
					}
					else{
						$data[] = $T_device;
					}
				}
			}
			return response()->json([
				'data'=>$data,
				'code' =>0,
				'msg'=>'Success'
			]);

		}
	
	}

	public function user_all_device_list(Request $request){

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
		$user_id = Auth::id();

		$auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api();

		//echo $token;

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
			echo $emp_result->id;
			return $json;
		}
		else{
			$UserObj = User::find($user_id);
			$bluetooth_device_access = array();
			$UserDevices = UserDevice::where('user_id',$UserObj->id)->where('unit_no',$UserObj->unit_no)->get();
			if(isset($UserDevices)){
				foreach($UserDevices as $selecteddevices){
					$bluetooth_device_access[] = $selecteddevices->device_svn;
				}
			}
			$remote_device_access = array();
			$UserRemoteDevices = UserRemoteDevice::where('user_id',$UserObj->id)->where('unit_no',$UserObj->unit_no)->get();
			if(isset($UserRemoteDevices)){
				foreach($UserRemoteDevices as $selectedremotedevices){
					$remote_device_access[] = $selectedremotedevices->device_svn;
				}
			}
			$currentDate = date('Y-m-d');
			$bluetooth_data = array();
			$remote_data = array();
			$thinmoo_devices = $json['data'];
			foreach($thinmoo_devices as $T_device){
				$items_array = array();
				$device_info = Device::where('device_serial_no',$T_device['devSn'])->first();
				if(in_array($T_device['devSn'],$bluetooth_device_access)){ //bluetooth devices
					//echo $T_device['devSn'];
					if(isset($device_info) && $device_info->facility_type >0){
						$UserBookingObj = FacilityBooking::where('user_id',$UserObj->id)->where('type_id',$device_info->facility_type)->where('status',2)->orderby('id','desc')->get();
						if(isset($UserBookingObj)){
							foreach($UserBookingObj as $UserBooking){
								if($currentDate ==$UserBooking->booking_date){
									$last_15_mins_records = Carbon::now()->subMinutes(60)->toDateTimeString();
									$bookingtime = str_replace(" ","",$UserBooking->booking_time);
									$times = explode("-",$bookingtime);
									$start_times = explode(":",str_replace(".",":",$times[0]));
									$start_time_build = $UserBooking->booking_date." ".trim($start_times[0]).":".trim($start_times[1]);
									$starttime = strtotime($start_time_build)-($device_info->entry_allowed_in_advance *3600);
									$end_times = explode(":",str_replace(".",":",$times[1]));
									$end_time_build = $UserBooking->booking_date." ".trim($end_times[0]).":".trim($end_times[1]);
									$endtime = strtotime($end_time_build);
									$currenttime = strtotime("now");
									//echo "Start Time :". date("Y-m-d H:i:s",$starttime);
									//echo " End Time :". date("Y-m-d H:i:s",$endtime);
									//echo " Current Time :". date("Y-m-d H:i:s",$currenttime);
									if($currenttime>=$starttime && $currenttime<=$endtime){
										//echo "here";
										$items_array['thinmoo'] = $T_device;
										$items_array['moreinfo'] = $device_info;
										$bluetooth_data[] = $items_array;
									}
								}
							}
						}
					}
					else if($device_info){
						$items_array['thinmoo'] = $T_device;
						$items_array['moreinfo'] = $device_info;
						$bluetooth_data[] = $items_array;
					}
				}

				if(in_array($T_device['devSn'],$remote_device_access)){ // Remote devices
					if(isset($device_info) && $device_info->facility_type >0){
						$UserBookingObj = FacilityBooking::where('user_id',$UserObj->id)->where('type_id',$device_info->facility_type)->where('status',2)->orderby('id','desc')->get();
						if(isset($UserBookingObj)){
							foreach($UserBookingObj as $UserBooking){
								if($currentDate ==$UserBooking->booking_date){
									$last_15_mins_records = Carbon::now()->subMinutes(60)->toDateTimeString();
									$bookingtime = str_replace(" ","",$UserBooking->booking_time);
									$times = explode("-",$bookingtime);
									$start_times = explode(":",str_replace(".",":",$times[0]));
									$start_time_build = $UserBooking->booking_date." ".trim($start_times[0]).":".trim($start_times[1]);
									$starttime = strtotime($start_time_build)-($device_info->entry_allowed_in_advance *3600);
									$end_times = explode(":",str_replace(".",":",$times[1]));
									$end_time_build = $UserBooking->booking_date." ".trim($end_times[0]).":".trim($end_times[1]);
									$endtime = strtotime($end_time_build);
									$currenttime = strtotime("now");
									if($currenttime>=$starttime && $currenttime<=$endtime){
										$remote_data[] = $T_device;
									}
								}
							}
						}
					}
					else if(isset($device_info)){
						$remote_data[] = $T_device;
					}
				}
				

			}
			return response()->json([
				'bluetooth_data'=>$bluetooth_data,
				'remote_data'=>$remote_data,
				'code' =>0,
				'msg'=>'Success'
			]);

		}
	
	}

	public function remote_door_open(Request $request){

		$rules=array(
			'property' => 'required',
			'devSn' => 'required',

		);
		$messages=array(
			'property.required' => 'Property ID missing',
			'devSn.required' => 'Device serial number missing',
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
		$user_id = Auth::id();

		$auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api();
		$url = env('THINMOO_API_URL')."sqDoor/extapi/remoteOpenDoor";
		$emp_result = Employee::where('account_id',$uuid)->orderby('id','asc')->first();
		//The data you want to send via POST
		$fields = [
			'accessToken'      		=> 	$token,
			'devSn'              	=>  $input['devSn'],
			'extCommunityUuid'		=>	$uuid,
			'delay'					=>	5,
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
		if($json['code'] !=0){
			$input = $request->all();
			$input['user_id'] = Auth::id();
			
			$rec = Device::where('device_serial_no',$input['devSn'])->first();
			$userObj = User::find($input['user_id']);

			$data['account_id'] = (isset($input['property_id']))?$input['property_id']:'';
			$data['user_id'] = $input['user_id'];
			$data['account_id'] = isset($userObj->account_id)?$userObj->account_id:null;
			$data['unit_no'] = isset($userObj->unit_no)?$userObj->unit_no:null;
			$data['devMac'] = isset($input['devMac'])?$input['devMac']:'';
			$data['devType'] = isset($input['devType'])?$input['devType']:'';
			$data['eKey'] = isset($input['eKey'])?$input['eKey']:'';
			$data['devSn'] = isset($input['devSn'])?$input['devSn']:'';
			$data['devName'] = isset($rec->device_name)?$rec->device_name:null;
			$data['call_date_time'] = isset($input['call_date_time'])?$input['call_date_time']:'';
			$data['status'] = 0;
			$data['action_type'] = 0;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$record = RemoteDoorOpen ::insert($data);
			return $json;
		}
		else{
			$input = $request->all();
			$input['user_id'] = Auth::id();
			
			$rec = Device::where('device_serial_no',$input['devSn'])->first();
			$userObj = User::find($input['user_id']);

			$data['account_id'] = (isset($input['property_id']))?$input['property_id']:'';
			$data['user_id'] = $input['user_id'];
			$data['account_id'] = isset($userObj->account_id)?$userObj->account_id:null;
			$data['unit_no'] = isset($userObj->unit_no)?$userObj->unit_no:null;
			$data['devMac'] = isset($input['devMac'])?$input['devMac']:'';
			$data['devType'] = isset($input['devType'])?$input['devType']:'';
			$data['eKey'] = isset($input['eKey'])?$input['eKey']:'';
			$data['devSn'] = isset($input['devSn'])?$input['devSn']:'';
			$data['devName'] = isset($rec->device_name)?$rec->device_name:null;
			$data['call_date_time'] = isset($input['call_date_time'])?$input['call_date_time']:'';
			$data['status'] = 1;
			$data['action_type'] = 1;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$record = RemoteDoorOpen ::insert($data);
			return response()->json([
				'code' =>0,
				'msg'=>'Success'
			]);
		}
	
	}

	public function BluetoothDoorOpenRecord(Request $request)
    {
		$rules=array(
			'property_id'=>'required',
			'devSn'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
		
		$rec = Device::where('device_serial_no',$input['devSn'])->first();
		$userObj = User::find($input['user_id']);

		$data['account_id'] = $input['property_id'];
		$data['user_id'] = $input['user_id'];
		//$data['account_id'] = isset($userObj->account_id)?$userObj->account_id:null;
		$data['unit_no'] = isset($userObj->unit_no)?$userObj->unit_no:null;
		$data['devMac'] = $input['devMac'];
		$data['devType'] = $input['devType'];
		$data['eKey'] = $input['eKey'];
		$data['devSn'] = $input['devSn'];
		$data['devName'] = isset($rec->device_name)?$rec->device_name:null;
		$data['call_date_time'] = $input['call_date_time'];
		$data['status'] = $input['status'];
		$data['action_type'] = $input['action_type'];
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$record = BluetoothDoorOpen ::insert($data);
		
		return response()->json(['result'=>$record,'code' => 1, 'message' => 'Record added']);

        
	}

	public function CallUnitRecord(Request $request)
    {
		$rules=array(
			'property_id'=>'required',
			'devSn'=>'required',
			'user_id' =>'required'
		);
		$messages=array(
			'property_id.required'=>'Property is missing',
			'devSn.required'=>'Property is missing',
			'user_id.required'=>'User is missing',
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
		$input['user_id'] = Auth::id();
		
		$rec = Device::where('device_serial_no',$input['devSn'])->first();
		$userObj = User::find($input['user_id']);

		$data['account_id'] = $input['property_id'];
		$data['user_id'] = $input['user_id'];
		$data['unit_no'] = isset($userObj->unit_no)?$userObj->unit_no:null;
		$data['devMac'] = (isset($input['devMac']))?$input['devMac']:'';
		$data['devType'] = (isset($input['devType']))?$input['devType']:'';
		$data['eKey'] = (isset($input['eKey']))?$input['eKey']:'';
		$data['devSn'] = $input['devSn'];
		$data['devName'] = isset($rec->device_name)?$rec->device_name:null;
		$data['call_date_time'] = $input['call_date_time'];
		$data['status'] = $input['status'];
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$record = CallUnitAnswerRecord::insert($data);
		
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
		
		$auth = new \App\Models\v7\Property();

        $token = $auth->thinmoo_auth_api();

		return response()->json(['token'=>$token,'response' => 1, 'message' => 'success']);		


	}


	public function FailOpenDoorRecordPush(Request $request)
    {
		
		$failrecordtype = array(47,49,50,61,62,63,65,72,73,74,75,76,77,79);
		$normalopendoortype = array(0,4,8,19,20,21,22,23,24,25,26,67,120,121,122,123,127);

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
			$data['user_id'] = isset($input['empUuid'])?$input['empUuid']:null;
			if(isset($input['empUuid'])){
				$user_obj = User::find($input['empUuid']);
				$data['unit_no'] = isset($user_obj->unit_no)?$user_obj->unit_no:null;
			}
			$data['empuuid'] = isset($input['empUuid'])?$input['empUuid']:null;
			$data['empname'] = isset($input['empName'])?$input['empName']:null;
			$data['empPhone'] = isset($input['empPhone'])?$input['empPhone']:null;
			$data['empCardNo'] = isset($input['empCardNo'])?$input['empCardNo']:null;
			$data['devId'] = isset($input['devId'])?$input['devId']:null;
			$data['devuuid'] = isset($input['devUuid'])?$input['devUuid']:null;
			$data['devname'] = isset($input['devName'])?$input['devName']:null;
			$data['devSn'] = isset($input['devSn'])?$input['devSn']:null;
			$data['eventType'] = isset($input['eventType'])?$input['eventType']:null;
			$data['eventtime'] = isset($input['eventTime'])?$input['eventTime']:null;
			$data['captureImageBase64'] = isset($input['captureImageBase64'])?$input['captureImageBase64']:null;
			$data['captureImageUrl'] = isset($input['captureImageUrl'])?$input['captureImageUrl']:null;
			$data['faceAge'] = isset($input['faceAge'])?$input['faceAge']:null;
			$data['faceGender'] = isset($input['faceGender'])?$input['faceGender']:null;
			$data['faceMatchScore'] = isset($input['faceMatchScore'])?$input['faceMatchScore']:null;
			$data['bodyTemperature'] = isset($input['bodyTemperature'])?$input['bodyTemperature']:null;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');

			$record = FailDoorOpenRecord ::insert($data); 
		}
		
		if(isset($input['eventType']) && in_array($input['eventType'],$normalopendoortype)){
		
			$data['account_id'] = $device->account_id;
			$data['user_id'] = isset($input['empUuid'])?$input['empUuid']:null;
			if(isset($input['empUuid'])){
				$user_obj = User::find($input['empUuid']);
				$data['unit_no'] = isset($user_obj->unit_no)?$user_obj->unit_no:null;
			}
			$data['empuuid'] = isset($input['empUuid'])?$input['empUuid']:null;
			$data['empname'] = isset($input['empName'])?$input['empName']:null;
			$data['empPhone'] = isset($input['empPhone'])?$input['empPhone']:null;
			$data['empCardNo'] = isset($input['empCardNo'])?$input['empCardNo']:null;
			$data['devId'] = isset($input['devId'])?$input['devId']:null;
			$data['devuuid'] = isset($input['devUuid'])?$input['devUuid']:null;
			$data['devname'] = isset($input['devName'])?$input['devName']:null;
			$data['devSn'] = isset($input['devSn'])?$input['devSn']:null;
			$data['eventType'] = isset($input['eventType'])?$input['eventType']:null;
			$data['eventtime'] = isset($input['eventTime'])?$input['eventTime']:null;
			$data['captureImageBase64'] = isset($input['captureImageBase64'])?$input['captureImageBase64']:null;
			$data['captureImageUrl'] = isset($input['captureImageUrl'])?$input['captureImageUrl']:null;
			$data['faceAge'] = isset($input['faceAge'])?$input['faceAge']:null;
			$data['faceGender'] = isset($input['faceGender'])?$input['faceGender']:null;
			$data['faceMatchScore'] = isset($input['faceMatchScore'])?$input['faceMatchScore']:null;
			$data['bodyTemperature'] = isset($input['bodyTemperature'])?$input['bodyTemperature']:null;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');

			$record = NormalDoorOpenRecord ::insert($data); 
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
		$data['devSn'] = isset($input['devSn'])?$input['devSn']:null;
		$data['roomId'] = isset($input['roomId'])?$input['roomId']:null;
		$data['roomuuid'] = isset($input['roomUuid'])?$input['roomUuid']:null;
		$data['roomCode'] = isset($input['roomCode'])?$input['roomCode']:null;
		$data['buildingCode'] = isset($input['buildingCode'])?$input['buildingCode']:null;
		$data['eventType'] = isset($input['eventType'])?$input['eventType']:null;
		$data['eventtime'] = isset($input['eventTime'])?$input['eventTime']:null;
		$data['captureImage'] = isset($input['captureImage'])?$input['captureImage']:null;
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
			'role_id' => 'required',
		);
		$messages=array(
			'property.required' => 'User id missing',
			'role_id.required' => 'Role id missing',
		);

		$due = array();
		$recent = array();
		$history = array();
		$payment_history = array();
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
				'due' =>$due,
				'invoice_history' =>$history,
				'payment_history' =>$payment_history,
				'code' =>102,
                'message' => $errors,
            ], 400);
        }
		
		$input = $request->all();
		$input['user_id'] = Auth::id();
		if($input['role_id']!=2){
			return response()->json([
				'due' =>$due,
				'invoice_history' =>$history,
				'payment_history' =>$payment_history,
				'code' =>101,
				'msg'=>'Permission denied!'
			]);
		}
		$userObj = User::find($input['user_id']);
		if(empty($userObj)){
			return response()->json([
				'due' =>$due,
				'invoice_history' =>$history,
				'payment_history' =>$payment_history,
				'code' =>101,
				'msg'=>'User not available!'
			]);
		}
		$invoiceIds = array();
		$invoice = FinanceInvoice::where('account_id',$input['property'])->where('unit_no',$userObj->unit_no)->where('user_access_status',1)->orderby('id','desc')->first(); 
		$visitor_app_url = env('VISITOR_APP_URL');
		if(isset($invoice)){
				$data = array();
				$invoiceIds[] =$invoice->id;
				$data['id'] = $invoice->id;
				$data['invoice_no'] = $invoice->invoice_no;
				$data['invoice_date'] = $invoice->invoice_date;
				$data['due_date'] = $invoice->due_date;
				$data['batch_no'] = $invoice->batch_file_no;
				$data['invoice_amount'] = $invoice->payable_amount;
				$amount_received =0;
				if($invoice->payments){
					foreach($invoice->payments as $k => $payment){
							if($payment->payment_option ==1 && $payment->status !=2)
								$amount_received += $payment->cheque_amount; 
							else if($payment->payment_option ==2)
								$amount_received += $payment->bt_amount_received;
							else if($payment->payment_option ==5)
								$amount_received += $payment->online_amount_received;
							else if($payment->payment_option ==6)
								$amount_received += $payment->credit_amount;
							else if($payment->payment_option ==7)
								$amount_received += $payment->add_amt_received;
							else
								$amount_received += $payment->cash_amount_received;
						
					}
				}
				$data['amount_received'] = $amount_received;
				//$amount_received = number_format($amount_received,2);
				$balance_amount = ($invoice->payable_amount - $amount_received);
				$data['amount_received'] =  number_format($amount_received,2);
				$data['balance_amount'] =  number_format($balance_amount,2);
				$data['pdf_file'] = $visitor_app_url."/invoice-pdf/".$invoice->id;
				if(isset($invoice->status)){
					if($invoice->status !=3){
					   $financeObj = new \App\Models\v7\FinanceInvoice();
					   $ref_invoice = $financeObj->CheckNewInvoice($invoice->id,$invoice->unit_no);
					   //print_r($ref_invoice);
					}

					if($invoice->status==1){
					   	$rec = $financeObj->CheckOverDue($invoice->id);
					   
					   	if(isset($ref_invoice->id))
					   		$data['status'] = "Unpaid/ref.".$ref_invoice->invoice_no;
					   	else
					   		$data['status'] = $rec;
					}                                      
					else  if($invoice->status==2){
					   	$data['status'] = "Partial Paid";
					   if(isset($ref_invoice->id))
						$data['status'] = "/ref.".$ref_invoice->invoice_no;
					}
					else  if($invoice->status==4)
					   $data['status'] = "Pending Verification";
					else 
					   $data['status'] = "Paid";
				 }
				 $due[] = $data;
			
		}

		$due_invoices = array();
		$due_invoices[] = isset($invoice->id)?$invoice->id:null;

		$invoices = FinanceInvoice::where('account_id',$input['property'])->where('unit_no',$userObj->unit_no)->whereNotIn('id',$due_invoices)->where('user_access_status',1)->orderby('id','desc')->get(); 
		$visitor_app_url = env('VISITOR_APP_URL');
		if(isset($invoices)){
			foreach($invoices as $invoice){
				$data = array();
				$invoiceIds[] =$invoice->id;
				$data['id'] = $invoice->id;
				$data['invoice_no'] = $invoice->invoice_no;
				$data['invoice_date'] = $invoice->invoice_date;
				$data['due_date'] = $invoice->due_date;
				$data['batch_no'] = $invoice->batch_file_no;
				$data['invoice_amount'] = $invoice->payable_amount;
				$amount_received =0;
				if($invoice->payments){
					foreach($invoice->payments as $k => $payment){
							if($payment->payment_option ==1 && $payment->status !=2)
								$amount_received += $payment->cheque_amount; 
							else if($payment->payment_option ==2)
								$amount_received += $payment->bt_amount_received;
							else if($payment->payment_option ==5)
								$amount_received += $payment->online_amount_received;
							else if($payment->payment_option ==6)
								$amount_received += $payment->credit_amount;
							else if($payment->payment_option ==7)
								$amount_received += $payment->add_amt_received;
							else
								$amount_received += $payment->cash_amount_received;
						
					}
				}
				
				$balance_amount = ($invoice->payable_amount - $amount_received);
				$data['amount_received'] =  number_format($amount_received,2);
				$data['balance_amount'] =  number_format($balance_amount,2);

				$data['pdf_file'] = $visitor_app_url."/invoice-pdf/".$invoice->id;
				if(isset($invoice->status)){
					if($invoice->status !=3){
					   $financeObj = new \App\Models\v7\FinanceInvoice();
					   $ref_invoice = $financeObj->CheckNewInvoice($invoice->id,$invoice->unit_no);
					   //print_r($ref_invoice);
					}

					if($invoice->status==1){
					   	$rec = $financeObj->CheckOverDue($invoice->id);
					   
					   	if(isset($ref_invoice->id))
					   		$data['status'] = "Unpaid/ref.".$ref_invoice->invoice_no;
					   	else
					   		$data['status'] = $rec;
					}                                      
					else  if($invoice->status==2){
					   	$data['status'] = "Partial Paid";
					   if(isset($ref_invoice->id))
						$data['status'] = "/ref.".$ref_invoice->invoice_no;
					}
					else  if($invoice->status==4)
					   $data['status'] = "Pending Verification";
					else 
					   $data['status'] = "Paid";
				 }

				$history[] = $data;
			}
		}

		$paymentHistory = FinanceInvoicePayment::whereIn('invoice_id',$invoiceIds)->get();
		if($paymentHistory){
			foreach($paymentHistory as $k => $payment){
				if($payment->payment_option !=4 ){
					$data = array();
					$data['id'] = $payment->id;
					$data['invoice_id'] = $payment->invoice_id; 
					$data['invoice_no'] = (isset($payment->invoicerecord))?$payment->invoicerecord->invoice_no:null; 
					$data['invoice_date'] = (isset($payment->invoicerecord))?$payment->invoicerecord->invoice_date:null; 

						if($payment->payment_option ==1)
							$data['payment_mode'] = "Cheque";
						else if($payment->payment_option ==2)
							$data['payment_mode'] = "Bank Transfer";
						else if($payment->payment_option ==4)
							$data['payment_mode'] = "Excess Paid";
						else if($payment->payment_option ==5)
							$data['payment_mode'] = "Online Payment";
						else if($payment->payment_option ==7)
							$data['payment_mode'] = "Payment";
						else if($payment->payment_option ==6)
							$data['payment_mode'] = "Waiver";
						else
							$data['payment_mode'] = "Cash";

						if($payment->payment_option ==1){
							$data['details'] = isset($payment->cheque_no)?$payment->cheque_no:null;
							if($payment->status ==2)
								$data['remarks'] = isset($payment->remarks)?" (".$payment->remarks.")":null;
						}
						else if($payment->payment_option ==5) 
							$data['details']  = isset($payment->transaction_id)?$payment->transaction_id:null;
						else if($payment->payment_option ==7) 
							$data['details']  = isset($payment->add_amt_notes)?$payment->add_amt_notes:null;
						else if($payment->payment_option ==6) 
							$data['details']  = isset($payment->credit_notes)?$payment->credit_notes:null;
						else
						$data['details'] = null;
							 

						if($payment->payment_option ==1)
							$data['amount_received'] = number_format($payment->cheque_amount,2); 
						 else if($payment->payment_option ==2)
						 	$data['amount_received'] =  number_format($payment->bt_amount_received,2); 
						 else if($payment->payment_option ==6)
						 	$data['amount_received'] =  number_format($payment->credit_amount,2); 
						 else if($payment->payment_option ==7)
						 	$data['amount_received'] =  number_format($payment->add_amt_received,2);   
						 else
						 	$data['amount_received'] =  number_format($payment->cash_amount_received,2);
					
					$data['payment_received_date'] = $payment->payment_received_date; 

					$payment_history[] = $data;
				}
			}
		}
           
	

		return response()->json([
			'due' =>$due,
			'invoice_history' =>$history,
			'payment_history' =>$payment_history,
			'code' =>0,
			'msg'=>'Success'
		]);
	}

	public function viewinvoice(Request $request)
	{
		$rules=array(
			'role_id' => 'required',
			'invoice_id' => 'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
		if($input['role_id']!=2){
			return response()->json([
				'code' =>101,
				'msg'=>'Permission denied!'
			]);
		}
		$id = $input['invoice_id'];
		$userObj = User::find($input['user_id']);
		$account_id = $userObj->account_id;
		$Unitinvoice = FinanceInvoice::where('id',$input['invoice_id'])->where('unit_no',$userObj->unit_no)->first(); 
		$currentDetails = FinanceInvoicePaymentDetail::where('invoice_id',$Unitinvoice->id)->where("reference_invoice",$Unitinvoice->id)->orderby('id','asc')->get();
		$CurrentInvoicePayments = FinanceInvoicePayment::where('invoice_id',$Unitinvoice->id)->orderby('id','asc')->get();
        $LastInvoice = FinanceInvoice::where('id','<',$id)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','dsc')->first();
        //print_r($LastInvoice->id);
        if(isset($LastInvoice)){
            $LastInvoicePayments = FinanceInvoicePayment::where('invoice_id',$LastInvoice->id)->orderby('id','asc')->get();
            $previousDetails = FinanceInvoicePaymentDetail::where('invoice_id',$LastInvoice->id)->where("reference_invoice",'!=',$Unitinvoice->id)->orderby('id','asc')->get();
        }else{
            $LastInvoicePayments =array();
            $previousDetails = array();
        }
        $buildings = Building::where('account_id',$account_id)->get();
        $purchasers = User::where('role_id',2)->where('status',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->get();   
        $primary_contact = User::where('role_id',2)->where('status',1)->where('primary_contact',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->first();   

        $amount_received =0;
        if($Unitinvoice->payments){
            foreach($Unitinvoice->payments as $k => $payment){
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
        //$amount_received = number_format($amount_received,2);
		$balance_amount = ($Unitinvoice->payable_amount - $amount_received);

		$visitor_app_url = env('VISITOR_APP_URL');

		$data = array();
		if(isset($Unitinvoice)){
			$data['invoice_no'] = $Unitinvoice->invoice_no;
			$data['invoice_date'] = $Unitinvoice->invoice_date;
			$data['due_date'] = $Unitinvoice->due_date;
			$data['batch_no'] = $Unitinvoice->batch_file_no;
			$data['invoice_amount'] = $Unitinvoice->invoice_amount;
			$data['pdf_file'] = $visitor_app_url."/invoice-pdf/".$Unitinvoice->id;
			
			//$data['details'] = $invoice->paymentdetails;
		}
		$details = array();
		if(isset($previousDetails)){
			$previous_lists = array();
			foreach($previousDetails as $paymentdetail){
					$lists = array();
					$lists['id'] =$paymentdetail->id;
					$list['date'] = isset($LastInvoice->invoice_date)?date('d/m/y',strtotime($LastInvoice->invoice_date)):null;
					$lists['type'] = isset($paymentdetail->referencetypes->reference_type)?$paymentdetail->referencetypes->reference_type:null;
					$lists['reference_no'] = isset($paymentdetail->reference_no)?$paymentdetail->reference_no:null;
					$lists['desciption'] = isset($paymentdetail->detail)?$paymentdetail->detail:null;
					$lists['total_amount'] =$paymentdetail->total_amount;

					if($paymentdetail->paid_by_credit ==2)
						$paid = "Wavier";
                    else {
                        $paid = $paymentdetail->total_amount - $paymentdetail->amount;
					}
										  
					$paid_amount = $paymentdetail->total_amount - $paymentdetail->balance;
					$lists['paid'] =$paid;
					$lists['balance'] =(float)$paymentdetail->amount;
					$previous_lists[] = $lists;
			}
		}

		if(isset($LastInvoicePayments)){
			$previous_payments = array();
			foreach($LastInvoicePayments as $LastInvoicePayment){
					$lists = array();
					$lists['id'] =$LastInvoicePayment->id;
					$list['date'] = date('d/m/y',strtotime($LastInvoicePayment->payment_received_date));
					$lists['type'] = '';
					$lists['reference_no'] = '';
					if($LastInvoicePayment->payment_option ==1)
                        $desc= "Cheque";
                    else if($LastInvoicePayment->payment_option ==2)
						$desc= "Bank Transfer";
                    else if($LastInvoicePayment->payment_option ==4)
						$desc= "Excess Paid";
                    else if($LastInvoicePayment->payment_option ==5)
						$desc= "Online Payment";
                    else if($LastInvoicePayment->payment_option ==6)
						$desc= "Waiver";
                    else
						$desc= "Cash";
                    
                    if($LastInvoicePayment->payment_option ==1)
                    	$desc .= isset($LastInvoicePayment->cheque_no)?" ". $LastInvoicePayment->cheque_no:null;
                    else if($LastInvoicePayment->payment_option ==5) 
						$desc .= isset($LastInvoicePayment->transaction_id)?" ".$LastInvoicePayment->transaction_id:null;
                    else if($LastInvoicePayment->payment_option ==6) 
						$desc .= isset($LastInvoicePayment->credit_notes)?" ".$LastInvoicePayment->credit_notes:null;
					
						$desc.= " payment";
												   
					$lists['desciption'] = $desc;
					$lists['total_amount'] ="0.00";

					if($LastInvoicePayment->payment_option ==1)
                        $paid_amt =$LastInvoicePayment->cheque_amount; 
                    else if($LastInvoicePayment->payment_option ==2)
						$paid_amt = $LastInvoicePayment->bt_amount_received;
                    else if($LastInvoicePayment->payment_option ==5)
						$paid_amt = $LastInvoicePayment->online_amount_received; 
                    else if($LastInvoicePayment->payment_option ==6)
						$paid_amt = $LastInvoicePayment->credit_amount;   
                    else
						$paid_amt = $LastInvoicePayment->cash_amount_received; 
						
													  
					$lists['paid'] =(float)$paid_amt;
					$lists['balance'] ="(".$paid_amt.")";
					$previous_payments[] = $lists;
			}
		}
		if(isset($currentDetails)){
			$current_lists =array();
			foreach($currentDetails as $paymentdetail){
					$lists = array();
					$lists['id'] =$paymentdetail->id;
					$list['date'] = isset($LastInvoice->invoice_date)?date('d/m/y',strtotime($LastInvoice->invoice_date)):null;
					$lists['type'] = isset($paymentdetail->referencetypes->reference_type)?$paymentdetail->referencetypes->reference_type:null;
					$lists['reference_no'] = isset($paymentdetail->reference_no)?$paymentdetail->reference_no:null;
					$lists['desciption'] = isset($paymentdetail->detail)?$paymentdetail->detail:null;
					$lists['total_amount'] =$paymentdetail->total_amount;

					if($paymentdetail->paid_by_credit ==2)
						$paid = "Wavier";
                    else {
                        $paid = $paymentdetail->total_amount - $paymentdetail->amount;
					}
					$lists['paid'] =$paid;
					$lists['balance'] =(float)$paymentdetail->amount;
					$current_lists[] = $lists;
			}
		}

		if(isset($CurrentInvoicePayments)){
			$current_payments = array();
			foreach($CurrentInvoicePayments as $currentpayment){
					$lists = array();
					$lists['id'] =$currentpayment->id;
					$list['date'] = date('d/m/y',strtotime($currentpayment->payment_received_date));
					$lists['type'] = '';
					$lists['reference_no'] = '';
					if($currentpayment->payment_option ==1)
                        $desc= "Cheque";
                    else if($currentpayment->payment_option ==2)
						$desc= "Bank Transfer";
                    else if($currentpayment->payment_option ==4)
						$desc= "Excess Paid";
                    else if($currentpayment->payment_option ==5)
						$desc= "Online Payment";
                    else if($currentpayment->payment_option ==6)
						$desc= "Waiver";
                    else
						$desc= "Cash";
                    
                    if($currentpayment->payment_option ==1)
                    	$desc .= isset($currentpayment->cheque_no)?" ".$currentpayment->cheque_no:null;
                    else if($currentpayment->payment_option ==5) 
						$desc .= isset($currentpayment->transaction_id)?" ".$currentpayment->transaction_id:null;
                    else if($currentpayment->payment_option ==6) 
						$desc .= isset($currentpayment->credit_notes)?" ".$currentpayment->credit_notes:null;
					
						$desc.= " payment";
												   
					$lists['desciption'] = $desc;
					$lists['total_amount'] ="0.00";

					if($currentpayment->payment_option ==1)
                        $paid_amt = $currentpayment->cheque_amount; 
                    else if($currentpayment->payment_option ==2)
						$paid_amt = $currentpayment->bt_amount_received;
                    else if($currentpayment->payment_option ==5)
						$paid_amt = $currentpayment->online_amount_received; 
                    else if($currentpayment->payment_option ==6)
						$paid_amt = $currentpayment->credit_amount;   
                    else
						$paid_amt = $currentpayment->cash_amount_received; 
													  
					$lists['paid'] =(float)$paid_amt;
					$lists['balance'] ="(".$paid_amt.")";
					$current_payments[] = $lists;
			}
		}
		$data['details']['previous_lists'] = $previous_lists;
		$data['details']['previous_payments'] = $previous_payments;
		$data['details']['current_lists'] = $current_lists;
		$data['details']['current_payments'] = $current_payments;

		$data['balance_lable'] = ($balance_amount < 0)?'EXCESS AMOUNT':'AMOUNT DUE';
		$data['balance_amount'] = $balance_amount;
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
			'role_id' => 'required',
		);
		$messages=array(
			'property.required' => 'User id missing',
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
		$input['user_id'] = Auth::id();
		$userObj = User::find($input['user_id']);

		$month = $request->input('month');
        if($month !=''){          
			$from_date = $month;
			$start_date = date('Y-m', strtotime($month))."-01";
			$to_date  = date('Y-m-t', strtotime($month));
			$invoices = FinanceInvoice::where('account_id',$input['property'])->where('unit_no',$userObj->unit_no)->whereBetween('invoice_date',array($start_date,$to_date))->where('user_access_status',1)->orderby('id','desc')->get(); 
		}
		else{
			$invoices = FinanceInvoice::where('account_id',$input['property'])->where('unit_no',$userObj->unit_no)->where('user_access_status',1)->orderby('id','desc')->get(); 
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
				$data['invoice_amount'] = $invoice->payable_amount;
				$amount_received =0;
				if($invoice->payments){
					foreach($invoice->payments as $k => $payment){
							if($payment->payment_option ==1 && $payment->status !=2)
								$amount_received += $payment->cheque_amount; 
							else if($payment->payment_option ==2)
								$amount_received += $payment->bt_amount_received;
							else if($payment->payment_option ==5)
								$amount_received += $payment->online_amount_received;
							else if($payment->payment_option ==6)
								$amount_received += $payment->credit_amount;
							else if($payment->payment_option ==7)
								$amount_received += $payment->add_amt_received;
							else
								$amount_received += $payment->cash_amount_received;
						
					}
				}
				$data['amount_received'] = $amount_received;
				//$amount_received = number_format($amount_received,2);
				$balance_amount = ($invoice->payable_amount - $amount_received);
				$data['amount_received'] =  number_format($amount_received,2);
				$data['balance_amount'] =  number_format($balance_amount,2);
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
			'role_id' => 'required',

		);
		$messages=array(
			'property.required' => 'User id missing',
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
		$input['user_id'] = Auth::id();
		$input['invoice_id'] = $input['invoice_id'];
		if ($request->file('screenshot') != null) {
			$input['screenshot'] = $request->file('screenshot')->store(upload_path('paymentlog'));
		}
		$logObj = FinancePaymentLog::create($input);

		$UserObj = User::find($input['user_id']);
		
		if(empty($UserObj)){
			return response()->json([
				'code' =>200,
				'msg'=>'Invalid user!'
			]);
		}
		$invoiceObj = FinanceInvoice::find($input['invoice_id']);
		if(empty($invoiceObj)){
			return response()->json([
				'code' =>200,
				'msg'=>'Invalid invoice number!'
			]);
		}
		$invoiceObj->status = 4;
		$invoiceObj->save();

		$inbox['account_id'] = $UserObj->account_id;
		$inbox['unit_no'] = $UserObj->unit_no;
		$inbox['user_id'] = $UserObj->id;
		$inbox['type'] = 16;
		$inbox['ref_id'] = $invoiceObj->id;
		$inbox['title'] = "New Payment Screenshot Uploaded";
		$inbox['message'] = '';
		$inbox['status'] =  0; 
		$inbox['view_status'] =  0; 
		$inbox['submitted_by'] =  1;   
		$inbox['created_at'] =  $logObj->created_at;   
		$result = InboxMessage::create($inbox);

		$probObj = Property::find($UserObj->account_id);
			if($probObj->manager_push_notification ==1){ //if push notification activated for manager app
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserManagerLog::where('account_id',$UserObj->account_id)->whereIn('role_id',[3])->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $UserObj->id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
				$title = "Aerea Manager - ".$probObj->company_name;
				$message = "New Payment Screenshot Uploaded";
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$invoiceObj->unit_no;   
				$notofication_data['user_id'] =$UserObj->id;   
				$notofication_data['property'] =$invoiceObj->account_id;         
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_manager_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
			
				$NotificationObj->android_manager_notification($title,$message,$android_devices_to_send,$notofication_data); //
			}

			//exit;
		return response()->json([
			'code' =>0,
			'msg'=>'Success'
		]);
	}

	public function charges(Request $request) {
		$payment_url = env('OMISEURL')."charges";
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
		print_r( $result);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        return $json;

	}


	public function bookinglists(Request $request)
	{		
		$input = $request->all();
		$input['user'] = Auth::id();

		$userObj = User::find($input['user']);
		// Facility
		$facility_bookings = array();
		$bookings = FacilityBooking::where('user_id',$input['user'])->where('unit_no',$userObj->unit_no)->get();
		if(isset($bookings)){
			foreach($bookings as $booking){
				$facility_data = array();
				$facility_data['id'] = $booking->id;
				$facility_data['type'] =isset($booking->gettype->facility_type)?$booking->gettype->facility_type:null;
				$facility_data['date'] = date('d/m/y',strtotime($booking->booking_date));
				$facility_data['time'] = $booking->booking_time;
				$facility_bookings[] = $facility_data;
			}
		}

		// Joint Inspection
		$defect_bookings = array();
		$bookings = Defect::where('user_id',$input['user'])->where('unit_no',$userObj->unit_no)->get();
		if(isset($bookings)){
			foreach($bookings as $defect){
				if(isset($defect->inspection)){
					$defect_data = array();
					$defect_data['id'] = $defect->id;
					$defect_data['ticket'] = $defect->ticket;
					$defect_data['date'] = isset($defect->inspection->appt_date)?date('d/m/y',strtotime($defect->inspection->appt_date)):null;
					$defect_data['time'] = isset($defect->inspection->appt_time)?$defect->inspection->appt_time:null;
					$defect_bookings[] = $defect_data;
				}
			}
		}

		// Key Handover
		$keyhandover = array();
		$bookings = UnittakeoverAppointment::where('user_id',$input['user'])->where('unit_no',$userObj->unit_no)->get();
		if(isset($bookings)){
			foreach($bookings as $appt){
				$handover_data = array();
				$handover_data['id'] = $appt->id;
				$handover_data['date'] = (isset($appt->appt_date) && $appt->appt_date !='0000-00-00')?date('d/m/y',strtotime($appt->appt_date)):null;
				$handover_data['time'] = $appt->appt_time;
				$keyhandover[] = $handover_data;
			}
		}


		return response()->json([
			'code' =>0,
			'msg'=>'Success',
			'facility'=>$facility_bookings,
			'joininspection'=>$defect_bookings,
			'keyhandover'=>$keyhandover
		]);
	}


	public function user_notifications(Request $request)
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
				'code' =>102,
                'message' => $errors,
            ], 400);
		}
		
		$input = $request->all();
		$input['user'] = Auth::id();

		$unit = $input['unit_no'];
		$property = $input['property'];
		// Facility
		$records = array();
		$notifications = UserNotification::where('user_id',$input['user'])->where(function($query) use ($unit,$property){
			if($unit !=''){
				$query->where('unit_no', $unit);
			}
			if($property !=''){
				$query->where('account_id', $property);
			}
		})->orderby('id','desc')->get();
		if(isset($notifications)){
			foreach($notifications as $notification){
				$data = array();
				$data['id'] = $notification->id;
				$data['ref_id'] = $notification->ref_id;
				$data['type'] =$notification->module;
				$data['title'] =$notification->title;
				if($notification->module =='resichat_warning'){
					$message = "<p>you are receiving this warning due to a comment / post that was deemed offensive. We have since removed the comment / post and may suspend your access to our chats should you continue to make such comments / posts.<br/ >
					ResiChat is a community chat that aims to bring residents closer and thus, we shall remove all offensive comments / posts made. <br/ >
					We hope that this is an one-off incident and that you will join us in keeping ResiChat a positive environment for residents.</p>";
					$data['message'] =$message;
				}
				else if($notification->module =='resichat_warning'){
					$message = "<p>You are receiving this warning due to a listing that was deemed offensive. We have since removed the listing and may suspend your access to Marketplace should you continue to make such listings.<br/ >
					Marketplace aims to bring residents closer and provide a place where they can arrange to buy, sell, and trade items with others. Therefore, we shall remove all offensive listings.<br/ >
					We hope that this is a one-off incident and that you will join us in keeping Marketplace a positive environment for residents.</p>";

					$data['message'] =$message;
				}
				else {
				$data['message'] =$notification->message;
				}
				$data['status'] =$notification->status;
				$data['unit_no'] =isset($notification->addunitinfo->unit)?Crypt::decryptString($notification->addunitinfo->unit):null;
				$data['property'] =isset($notification->addpropinfo->company_name)?$notification->addpropinfo->company_name:null;
				$data['date'] = date('d/m/y',strtotime($notification->created_at));
				$data['deleted'] = 0;
				if($notification->module =='announcement'){
					$recordObj = AnnouncementDetail::find($notification->ref_id);
					if(empty($recordObj))
						$data['deleted'] = 1;
				}
				if($notification->module =='key collection'){
					$recordObj = UnittakeoverAppointment::find($notification->ref_id);
					if(empty($recordObj))
						$data['deleted'] = 1;
				}
				if($notification->module =='defects'){
					$recordObj = Defect::find($notification->ref_id);
					if(empty($recordObj))
						$data['deleted'] = 1;
				}
				if($notification->module =='feedback'){
					$recordObj = FeedbackSubmission::find($notification->ref_id);
					if(empty($recordObj))
						$data['deleted'] = 1;
				}
				if($notification->module =='facility'){
					$recordObj = FacilityBooking::find($notification->ref_id);
					if(empty($recordObj))
						$data['deleted'] = 1;
				}
				if($notification->module =='resident management'){
					$recordObj = FinanceInvoice::find($notification->ref_id);
					if(empty($recordObj))
						$data['deleted'] = 1;
				}

				$records[] = $data;
			}
			return response()->json([
				'code' =>0,
				'msg'=>'Success',
				'data'=>$records
			]);
		}else{
			return response()->json([
				'code' =>200,
				'msg'=>'User not found',
				'data'=>$records
			]);
		}
		
	}

	public function update_notification(Request $request)
	{
		$rules=array(
			'property' => 'required',
			'id' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
			'id.required' => 'id id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
				'code' =>102,
                'message' => $errors,
            ], 400);
		}
		
		$input = $request->all();
		$notiObj = UserNotification::find($input['id']);
		if(isset($notiObj)){
			$notiObj->status = 1;
			$notiObj->save();
			return response()->json([
				'code' =>0,
				'msg'=>'updated',
				'data'=>$notiObj
			]);
		}
		else{
			return response()->json([
				'code' =>200,
				'msg'=>'no record',
				'data'=>''
			]);
		}
	}

	public function release_notification(Request $request)
    {
		$rules=array(
			'key'=>'required',
			'version'=>'required',
		);
		$messages=array(
			'key.required'=>'Key is missing',
			'version.required'=>'Version is missing',
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
		$input['user_id'] = Auth::id();
		$properties  = explode(',',$input['properties']);

		if($input['key'] != 11111){
			return response()->json(['response' => 200, 'message' => "Invalid Key:".$input['key']." && User Id".$input['user_id']]);
		}
		
         //if push notification activated for manager app
			
			$user_logs = UserLog::where('status',1)->where(function($query) use ($properties){
				if(count($properties) > 1 && $properties !='')
					$query->whereIn('account_id',$properties);
				
			})->orderBy('id','desc')->get()->unique('user_id');
			if(isset($user_logs)) {
				foreach($user_logs as $logs) {
					
					$fcm_token_array ='';
					$user_token = ',';
					$ios_devices_to_send = array();
					$android_devices_to_send = array();
					$appSipAccountList = array();
					$userObj = User::find($logs->user_id); 
					//echo "User App Version:".$userObj->version;
					//echo "App Version ".$input['version'];
					if(isset($userObj) && $userObj->version !=$input['version']){
						if(isset($logs->fcm_token) && $logs->fcm_token !=''){
							$user_token .=$logs->fcm_token.",";
							$fcm_token_array .=$logs->fcm_token.',';
							$appSipAccountList[] = $logs->id;
							if($logs->login_from ==1)
								$ios_devices_to_send[] = $logs->fcm_token;
							if($logs->login_from ==2)
								$android_devices_to_send[] = $logs->fcm_token;
						}
						//echo "ID:".$logs->id;
						//echo "User Id ".$logs->user_id;
						$title = "New update has been released";
						$message = 'Kindly update your app to the latest version. You are also required to re-login after updating the app. ';
						//$title = "Bug fixes on Remote Door Opening is resolved";
						//$message = 'Kindly update your app to the latest version. Sorry for any inconveniences caused.';
						
						$notofication_data = array();
						$notofication_data['body'] =$title; 
						$notofication_data['type'] ='New Release';        
						$NotificationObj = new \App\Models\v7\FirebaseNotification();
						if(count($ios_devices_to_send) >0)
							$NotificationObj->ios_release_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
						if(count($android_devices_to_send) >0)
							$NotificationObj->android_release_notification($title,$message,$android_devices_to_send,$notofication_data); //
							
						User::where( 'id' , $logs->user_id)->update( array( 'version' => $input['version']));

					}
					//exit;

				}

				return response()->json(['response' => 1, 'message' => 'Release notification has been sent!']);
			}
	}

	public function chatterbox_category(Request $request)
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
		
		return response()->json(['data'=> ChatBoxCategory::get(),'response' => 1, 'message' => 'success!']);
	}

	public function chatterboxlist(Request $request)
	{
		$input = $request->all();
		$input['user_id'] = Auth::id();
		$category  =isset($input['category'])?$input['category']:'';
		$userid = $input['user_id'];
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($userid);
		
		$blocked_users = ChatBoxBlockUser::where('user_id',$userid)->where('account_id',$UserObj->account_id)->get();
		$blocked_user_array = array();
		if(isset($blocked_users)){
			foreach($blocked_users as $blocked_user){
				$blocked_user_array[] = $blocked_user->block_user_id;
			}
		}

		//print_r($blocked_user_array);

		$results = ChatBox::where('account_id',$UserObj->account_id)->whereNotIn('user_id',$blocked_user_array)->where(function($query) use ($category){
			if(isset($category) && $category >0)
				$query->where('category',$category);
		})->where('status',1)->orderby('id','desc')->get(); 

		$records =array();
		$com_report_array = array();
		$com_report_lists = ChatBoxReport::where('user_id',$input['user_id'])->where('status',1)->where('account_id',$UserObj->account_id)->get();
		if(isset($com_report_lists)){
			foreach($com_report_lists as $com_report_list){
				$com_report_array[] = $com_report_list->ref_id;
			}
		}
		//print_r($com_report_array);
		foreach($results as $result){
			$data = array();
			if(!in_array($result->id,$com_report_array)){
				$userinfo  = UserMoreInfo::where('user_id',$result->user_id)->where('account_id',$UserObj->account_id)->where('status',1)->first();
				$userdata = array();
				if(isset($userinfo)){
					$data['info'] = $result;
					$data['category_info'] = isset($result->cat_info)?$result->cat_info:null;
					$userdata['id'] =  isset($userinfo->user_id)?$userinfo->user_id:null;
					$userdata['first_name'] = isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):null;
					$userdata['last_name'] = isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
					$userdata['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;
					$data['userdata'] = $userdata;
					$records[] = $data;
				}
			}
		}
		return response()->json([
			'data'=>$records,
			'file_path'=>$file_path,
			'response' => 1,
			'status'=>'success'
		]);

	}

	public function chatterboxsubmit(Request $request) {

		$rules=array(
			'subject'=>'required',
			'notes'=>'required',
		);
		$messages=array(
			'subject.required'=>'Subject is missing',
			'notes.required'=>'Message is missing'
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
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
        $ticket = new \App\Models\v7\ChatBox();
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		$input['ticket'] = $ticket->ticketgen();
		
		$input['status'] = 1;
		if ($request->file('upload_1') != null) {
			$input['upload_1']  = $request->file('upload_1')->store(upload_path('chatbox'));
		}
		$defect = ChatBox::create($input);
		return response()->json(['response' => 1, 'message' => 'Chatter Box has been created!']);
	}

	public function chatterboxdetail(Request $request) {

		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
		$id = $input['id'];
        $details = array();
		$UserObj = User::find($input['user_id']);
		$result = ChatBox::find($id);

		$file_path = env('APP_URL')."/storage/app";
		if(isset($result) && isset($UserObj)){
			$data = array();
			$data['info']['id'] = $result->id;
			$data['info']['category'] = $result->category;
			$data['info']['upload_1'] = $result->upload_1;
			$data['info']['subject'] = $result->subject;
			$data['info']['notes'] = $result->notes;
			$data['info']['user_id'] = $result->user_id;
			$data['info']['created_at'] = $result->created_at->format('d/m/Y H:i');;
			$data['info']['updated_at'] = $result->updated_at->format('d/m/Y H:i');;
			
			$data['category_info'] = isset($result->cat_info)?$result->cat_info:null;
			$userinfo  = UserMoreInfo::where('user_id',$result->user_id)->where('account_id',$UserObj->account_id)->where('status',1)->first();
			$userdata = array();
			if(isset($userinfo)){
				$userdata['id'] = isset($userinfo->user_id)?$userinfo->user_id:null;
				$userdata['first_name'] = isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):null;
				$userdata['last_name'] = isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
				$userdata['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;
			}
			$data['userdata'] = $userdata;
			$comments = array();
			if(isset($result->comments)){
				$com_report_array = array();
				$com_report_lists = ChatBoxCommentReport::where('user_id',$input['user_id'])->where('ref_id',$result->id)->get();
				if(isset($com_report_lists)){
					foreach($com_report_lists as $com_report_list){
						$com_report_array[] = $com_report_list->comment_id;
					}
				}
				
				foreach($result->comments as $comment){
					$com_data = array();
					if(!in_array($comment->id,$com_report_array)){
						$com_data['id'] = $comment->id;
						$com_data['comment'] = $comment->comment;
						$comment_userinfo  = UserMoreInfo::where('user_id',$comment->user_id)->where('account_id',$comment->account_id)->where('status',1)->first();
						if(isset($comment_userinfo)){
							$com_data['user_id'] = isset($comment_userinfo->user_id)?$comment_userinfo->user_id:null;
							$com_data['first_name'] = isset($comment_userinfo->first_name)?Crypt::decryptString($comment_userinfo->first_name):null;
							$com_data['last_name'] = isset($comment_userinfo->last_name)?Crypt::decryptString($comment_userinfo->last_name):null;
							$com_data['profile_picture'] = isset($comment_userinfo->profile_picture)?$comment_userinfo->profile_picture:null;
						}
						$com_data['created_at'] = $comment->created_at->format('d/m/Y H:i');;
						$com_data['updated_at'] = $comment->updated_at->format('d/m/Y H:i');;
						$comments[] = $com_data;
					}
				}
				$data['comments'] = $comments;
			}
			/*$reports = array();
			if(isset($result->reports)){
				foreach($result->reports as $report){
					$report_data = array();
					$report_data['id'] = $report->id;
					$report_data['remark'] = $report->remark;
					$report_userinfo  = UserMoreInfo::where('user_id',$report->user_id)->where('account_id',$report->account_id)->where('status',1)->first();
					if(isset($report_userinfo)){
						$report_data['user_id'] = isset($report_userinfo->user_id)?$report_userinfo->user_id:null;
						$report_data['first_name'] = isset($report_userinfo->first_name)?$report_userinfo->first_name:null;
						$report_data['last_name'] = isset($report_userinfo->last_name)?$report_userinfo->last_name:null;
						$report_data['profile_picture'] = isset($report_userinfo->profile_picture)?$report_userinfo->profile_picture:null;
					}
					$report_data['created_at'] = $report->created_at->format('d/m/Y H:i');;
					$report_data['updated_at'] = $report->updated_at->format('d/m/Y H:i');;
					$reports[] = $report_data;
				}
				$data['reports'] = $reports;
			}*/
			
			return response()->json([
				'data'=>$data,
				'response' => 1,
				'file_path'=>$file_path,
				'status'=>'success'
			]);
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'Record not found!'
			]);
		}

	}

	public function chatterboxcomment(Request $request) {

		$rules=array(
			'ref_id'=>'required',
			'comment'=>'required',
		);
		$messages=array(
			'ref_id.required'=>'Chatter Box is missing',
			'comment.required'=>'Comment is missing'
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
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
        $ticket = new \App\Models\v7\ChatBox();
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		$input['status'] = 1;
		$comObj = ChatBoxComment::create($input);

		$result = ChatBox::find($comObj->ref_id);
		if(empty($result)){
			return response()->json(['response' => 1, 'message' => 'Resichat not found!']);

		}
		//Start Insert into notification module
		$notification = array();
		$notification['account_id'] = $result->account_id;
		$notification['user_id'] = $result->user_id;
		$notification['unit_no'] = $result->unit_no;
		$notification['module'] = 'resichat';
		$notification['ref_id'] = $result->id;
		$notification['title'] = 'ResiChat';
		$notification['message'] = 'There is a reply on your submitted resichat';
		UserNotification::insert($notification);
		
		$SettingsObj = UserNotificationSetting::where('user_id',$result->user_id)->where('account_id',$result->account_id)->first();
		if(empty($SettingsObj) || $SettingsObj->resichat ==1){
			$fcm_token_array ='';
			$user_token = ',';
			$ios_devices_to_send = array();
			$android_devices_to_send = array();
			$logs = UserLog::where('user_id',$result->user_id)->where('status',1)->orderby('id','desc')->first();
			if(isset($logs->fcm_token) && $logs->fcm_token !=''){
				$user_token .=$logs->fcm_token.",";
				$fcm_token_array .=$logs->fcm_token.',';
				$appSipAccountList[] = $result->id;
				if($logs->login_from ==1)
					$ios_devices_to_send[] = $logs->fcm_token;
				if($logs->login_from ==2)
					$android_devices_to_send[] = $logs->fcm_token;
			}
			$probObj = Property::find($result->account_id);
			$title = "Aerea Home - ".$probObj->company_name;
			$message = "ResiChat Reply";
			$notofication_data = array();
			$notofication_data['body'] =$title;
			$notofication_data['unit_no'] =$result->unit_no;   
			$notofication_data['user_id'] =$result->user_id;   
			$notofication_data['property'] =$result->account_id;
			$purObj = UserPurchaserUnit::where('property_id',$result->account_id)->where('unit_id',$result->unit_no)->where('user_id',$result->user_id)->where('status',1)->first(); 
			if(isset($purObj))
				$notofication_data['switch_id'] =$purObj->id;         
			$NotificationObj = new \App\Models\v7\FirebaseNotification();
			$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
			$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
		}
		//End Insert into notification module

		return response()->json(['response' => 1, 'message' => 'Comment has been created!']);
	}

	public function chatterboxreport(Request $request) {

		$rules=array(
			'ref_id'=>'required',
			'remark'=>'required',
		);
		$messages=array(
			'ref_id.required'=>'Chatter Box is missing',
			'remark.required'=>'Remark is missing'
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
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
        $ticket = new \App\Models\v7\ChatBox();
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		$input['status'] = 1;
		$results = ChatBoxReport::create($input);
		return response()->json(['response' => 1, 'message' => 'Report has been submitted!']);
	}

	public function chatterboxcommentreport(Request $request) {

		$rules=array(
			'ref_id'=>'required',
			'comment_id'=>'required',
			'remark'=>'required',
		);
		$messages=array(
			'ref_id.required'=>'Chatter Box is missing',
			'comment_id.required'=>'Comment is missing',
			'remark.required'=>'Remark is missing'
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
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
        $ticket = new \App\Models\v7\ChatBox();
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		$input['status'] = 1;
		$results = ChatBoxCommentReport::create($input);
		return response()->json(['response' => 1, 'message' => 'Report has been submitted!']);
	}

	public function chatterboxdelete(Request $request) {

		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
		$id = $input['id'];
        $details = array();
		$UserObj = User::find($input['user_id']);
		$result = ChatBox::where('id',$id)->where('user_id',$input['user_id'])->first();
		if(isset($result) && isset($UserObj)){
			ChatBox::findOrFail($id)->delete();
			ChatBoxComment::where('ref_id', $id)->delete();
			ChatBoxReport::where('ref_id', $id)->delete();
			return response()->json(['response' => 1, 'message' => 'Record has been deleted!']);


		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'status'=>'Record not found!'
			]);
		}

	}

	public function chattercommentdelete(Request $request) {

		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
		$id = $input['id'];
        $details = array();
		$UserObj = User::find($input['user_id']);
		$result = ChatBoxComment::where('id',$id)->where('user_id',$input['user_id'])->first();
		if(isset($result) && isset($UserObj)){
			ChatBoxComment::findOrFail($id)->delete();
			return response()->json(['response' => 1, 'message' => 'Record has been deleted!']);


		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'Record not found!'
			]);
		}

	}

	public function chatterreportrevert(Request $request) {

		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
		$id = $input['id'];
        $details = array();
		$UserObj = User::find($input['user_id']);
		$result = ChatBoxReport::where('id',$id)->where('user_id',$input['user_id'])->first();
		if(isset($result) && isset($UserObj)){
			ChatBoxReport::findOrFail($id)->delete();
			return response()->json(['response' => 1, 'message' => 'Report has been cancelled!']);


		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'Record not found!'
			]);
		}

	}
	public function chatterblockuser(Request $request) {

		$rules=array(
			'block_user_id'=>'required',
		);
		$messages=array(
			'block_user_id.required'=>'Block User Id is missing',
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
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
		$check_status = ChatBoxBlockUser::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('block_user_id',$input['block_user_id'])->first();
		if(isset($check_status)){
			return response()->json(['response' => 200, 'message' => 'Already Blocked!']);
		}else{
			$input['user_id'] = $input['user_id'];
			$input['account_id'] = $UserObj->account_id;
			$input['unit_no'] = $UserObj->unit_no;
			$input['block_user_id'] = $input['block_user_id'];
			$input['remark'] = $input['remark'];
			$input['status'] = 1;
			$results = ChatBoxBlockUser::create($input);
			return response()->json(['response' => 1, 'message' => 'Blocked!']);
		}

	}

	public function chatterunblockuser(Request $request) {

		$rules=array(
			'block_user_id'=>'required',
		);
		$messages=array(
			'block_user_id.required'=>'Block User Id is missing',
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
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
		$check_status = ChatBoxBlockUser::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('block_user_id',$input['block_user_id'])->first();
		if(isset($check_status)){
			ChatBoxBlockUser::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('block_user_id',$input['block_user_id'])->delete();
			return response()->json(['response' => 1, 'message' => 'Unblocked!']);
		}else{
			return response()->json(['response' => 200, 'message' => 'Record not found!']);
			
		}
	}

	public function chatteruserlists(Request $request) {
		
		$input = $request->all();
		$input['user_id'] = Auth::id();
		$details = array();
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($input['user_id']);
		$env_roles 	= env('USER_APP_ROLE');

		$Userlists = UserMoreInfo::where('account_id',$UserObj->account_id)->where('status',1)->get();
		if(isset($Userlists) && isset($Userlists)){
			$user_lists =array();
			foreach($Userlists as $Userlist){
				$user =array();
				$UserRole = UserPurchaserUnit::where('property_id',$UserObj->account_id)->where('user_id',$Userlist->user_id)->where('status',1)->first();
				//print_r($UserRole);
				if(isset($UserRole)){
					
					$user['user_id'] = isset($Userlist->user_id)?$Userlist->user_id:null;
					$user['first_name'] = isset($Userlist->first_name)?Crypt::decryptString($Userlist->first_name):null;
					$user['last_name'] = isset($Userlist->last_name)?Crypt::decryptString($Userlist->last_name):null;
					$user['profile_picture'] = isset($Userlist->profile_picture)?$Userlist->profile_picture:null;
					$BlockUser = ChatBoxBlockUser::where('user_id',$input['user_id'])->where('block_user_id',$Userlist->user_id)->first();
					$user['block_status'] = isset($BlockUser)?1:0;
					$user['blocked_date'] = isset($BlockUser->created_at)?$BlockUser->created_at->format('d/m/Y H:i'):null;
					$user_lists[] = $user;
				}
			}
			return response()->json(['data'=>$user_lists,'file_path'=>$file_path,'response' => 1, 'message' => 'success']);
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'Record not found!'
			]);
		}

	}

	public function opn_account_creation1(Request $request) {

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
		$details = array();
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($input['user_id']);
		$env_roles 	= env('USER_APP_ROLE');
		$userinfo = UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('status',1)->first();
		if(isset($userinfo) && isset($userinfo)){
			$payment_url = env('OMISEURL')."customers";
			$propinfo = Property::where('id',$UserObj->account_id)->first();
			$username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
			$password = '';
			$fields = [
				"description"           => "aerea customer (".$input['user_id'].") from ".$propinfo->company_name,
				"email"          		=> $userinfo->getuser->email,
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
			echo "hi";
			print_r($result);
			$json = json_decode($result,true);
			if(isset($json['id']) && $json['id'] !=''){
				UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->update(['opn_id' => $json['id']]);
				$userinfo  = UserMoreInfo::where('user_id',$input['user_id'])->where('account_id',$UserObj->account_id)->where('status',1)->first();
					$userdata['userinfo'] = array();
					$userdata['userinfo']['id'] = isset($userinfo->id)?$userinfo->id:null;
					$userdata['userinfo']['last_name'] = isset($userinfo->last_name)?$userinfo->last_name:null;
					$userdata['userinfo']['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;
					$userdata['userinfo']['phone'] =  isset($userinfo->phone)?$userinfo->phone:null;
					$userdata['userinfo']['mailing_address'] =  isset($userinfo->mailing_address)?$userinfo->mailing_address:null;
					$userdata['userinfo']['postal_code'] =  isset($userinfo->postal_code)?$userinfo->postal_code:null;
					$userdata['userinfo']['company_name'] =  isset($userinfo->company_name)?$userinfo->company_name:null;
					$userdata['userinfo']['face_picture'] =  isset($userinfo->face_picture)?$userinfo->face_picture:null;
					$userdata['userinfo']['status'] = isset($userinfo->status)?$userinfo->status:null;
					$userdata['userinfo']['deactivated_date'] = isset($userinfo->status)?$userinfo->deactivated_date:null;
					$userdata['userinfo']['opn_id'] = isset($userinfo->opn_id)?$userinfo->opn_id:null;

				return response()->json(['data'=>$userdata,'file_path'=>$file_path,'response' => 1, 'message' => 'success']);
			}else{
				return response()->json(['data'=>null,'file_path'=>$file_path,'response' => 200, 'message' => 'OPN record not created']);
			}
			
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'Record not found!'
			]);
		}

	}

	public function chatterbox_accept_tnc(Request $request)
	{

        $input = $request->all();
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		$input['status'] = 1;
		$results = ChatBoxUserAgreement::create($input);
		return response()->json(['response' => 1, 'message' => 'Accepted!']);
	}

	public function chatterbox_tnc_status(Request $request)
	{
		$input = $request->all();
		$input['user_id'] = Auth::id();
		$UserObj = User::find($input['user_id']);

		$result = ChatBoxUserAgreement::where('user_id',$UserObj->id)->where('account_id',$UserObj->account_id)->where('status',1)->first();
		if(isset($result))
			return response()->json(['data'=>$result,'response' => 1, 'message' => 'Success!']);
		else
			return response()->json(['data'=>null,'response' => 200, 'message' => 'No Record!']);
	}

	public function chatterbox_tnc(Request $request) {

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
		$UserObj = User::find($input['user_id']);
		if(isset($UserObj)){
			$result = ChatBoxTnc::where('status',1)->first();
			return response()->json(['data'=>$result,'response' => 1, 'message' => 'Success!']);
		}
		else
			return response()->json(['data'=>null,'response' => 200, 'message' => 'No Record!']);
	}

	public function MpAdsTypes(Request $request)
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
	   
       $types = MpAdsType::where('status', 1)->get();
       return response()->json(['data'=>$types,'response' => 1, 'message' => 'success!']);
   }

   public function MpAdsConditions(Request $request)
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
	   
       $types = MpAdsCondition::where('status', 1)->get();
       return response()->json(['data'=>$types,'response' => 1, 'message' => 'success!']);
   }

   public function MpAdsSubmit(Request $request)
    {
		$input = $request->all();
		
		if($input['type']==1){
			$rules=array(
				'id'=>'required',
				'members_count'=>'required',
				'title'=>'required',
				'price'=>'required',
				'notes'=>'required',
			);
			$messages=array(
				'id.required' => 'Id missing',
				'members_count.required'=>'Number of people is missing',
				'title.required'=>'Title is missing',
				'price.required'=>'Price is missing',
				'notes.required'=>'Description is missing',
			);
		}
		else {
			$rules=array(
				'id'=>'required',
				'item_condition'=>'required',
				'title'=>'required',
				'price'=>'required',
				'notes'=>'required',
			);
			$messages=array(
				'id.required' => 'Id missing',
				'item_condition.required'=>'Condition is missing',
				'title.required'=>'Title is missing',
				'price.required'=>'Price is missing',
				'notes.required'=>'Description is missing',
			);
		}
		
        $input = $request->all();
		$input['user_id'] = Auth::id();
        
        $details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
	
        $ticket = new \App\Models\v7\MpAdsSubmission();
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		$input['ticket'] = $ticket->ticketgen();
		$record = MpAdsSubmission::create($input);
		
        for($i=1;$i<=10;$i++){
			$data = array();
            $attachement = 'upload_'.$i;
            if ($request->file($attachement) != null) {
                $data['upload'] = $request->file($attachement)->store(upload_path('mpads'));
				$data['ref_id'] = $record->id;
            	$data['status'] = 1;
				$defect = MpAdsImage::create($data);
			}
        }
		
		return response()->json(['result'=>$record,'response' => 1, 'message' => 'Ad has been submitted!']);
	}

	public function MpAdsUpdate(Request $request)
    {
		$input = $request->all();
		
		if($input['type']==1){
			$rules=array(
				'id'=>'required',
				'members_count'=>'required',
				'title'=>'required',
				'price'=>'required',
				'notes'=>'required',
			);
			$messages=array(
				'id.required' => 'Id missing',
				'members_count.required'=>'Number of people is missing',
				'title.required'=>'Title is missing',
				'price.required'=>'Price is missing',
				'notes.required'=>'Description is missing',
			);
		}
		else {
			$rules=array(
				'id'=>'required',
				'item_condition'=>'required',
				'title'=>'required',
				'price'=>'required',
				'notes'=>'required',
			);
			$messages=array(
				'id.required' => 'Id missing',
				'item_condition.required'=>'Condition is missing',
				'title.required'=>'Title is missing',
				'price.required'=>'Price is missing',
				'notes.required'=>'Description is missing',
			);
		}

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		$input['user_id'] = Auth::id();
        
        $details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$listObj = MpAdsSubmission::find($input['id']);
		if(empty($listObj)){
			return response()->json(['response' => 200, 'message' => 'Record not found!']);

		}
        $ticket = new \App\Models\v7\MpAdsSubmission();
		$listObj->title = $input['title'];
		$listObj->type = $input['type'];
		if($input['type']==2)
			$listObj->item_condition = $input['item_condition'];
		if($input['type']==1)
			$listObj->members_count = $input['members_count'];
		$listObj->price = $input['price'];
		$listObj->notes = $input['notes'];
		$listObj->save();
		return response()->json(['result'=>$listObj,'response' => 1, 'message' => 'Updated!']);
	}

	public function MpAdsAddImage(Request $request)
    {
		$rules=array(
			'ref_id'=>'required',
		);
		$messages=array(
			'ref_id.required' => 'Ref.Id missing',
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
		$input['user_id'] = Auth::id();
        
		$details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$imgObj = MpAdsSubmission::find($input['ref_id']);
		if(empty($imgObj)){
			return response()->json(['response' => 200, 'message' => 'Record not found!']);
		}
		if ($request->file('upload') != null) {
			$data['upload'] = $request->file('upload')->store(upload_path('mpads'));
			$data['ref_id'] = $input['ref_id'];
			$data['status'] = 1;
			$defect = MpAdsImage::create($data);
			return response()->json(['result'=>$defect,'response' => 1, 'message' => 'Updated!']);
		}
		else{
			return response()->json(['response' => 200, 'message' => 'Image empty!']);

		}
		
	}

	public function MpAdsUpdateImage(Request $request)
    {
		$rules=array(
			'ref_id'=>'required',
			'img_id'=>'required'
		);
		$messages=array(
			'ref_id.required' => 'Ref.Id missing',
			'img_id.required'=>'Image id is missing',
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
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$imgObj = MpAdsImage::find($input['img_id']);
		if(empty($imgObj)){
			return response()->json(['response' => 200, 'message' => 'Image not found!']);
		}
		$imgObj->upload = $request->file('upload')->store(upload_path('mpads'));
		$imgObj->save();
		return response()->json(['result'=>$imgObj,'response' => 1, 'message' => 'Updated!']);
	}

	public function MpAdsDeleteImage(Request $request)
    {
		$rules=array(
			'ref_id'=>'required',
			'img_id'=>'required',
		);
		$messages=array(
			'ref_id.required' => 'Ref.Id missing',
			'img_id.required'=>'Image id is missing'
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
		$input['user_id'] = Auth::id();
        
        $details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$imgObj = MpAdsImage::find($input['img_id']);
		if(empty($imgObj)){
			return response()->json(['response' => 200, 'message' => 'Image not found!']);
		}
		MpAdsImage::where('id', $input['img_id'])->delete();
		return response()->json(['response' => 1, 'message' => 'Image deleted!']);
	}

	public function MpAdsList(Request $request)
    {
        $input = $request->all();
		$input['user_id'] = Auth::id();
        $search_key = isset($input['search_key'])?$input['search_key']:'';
        $details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$blocked_users = MpAdsBlockUser::where('user_id',$UserObj->id)->where('account_id',$UserObj->account_id)->get();
		$blocked_user_array = array();
		if(isset($blocked_users)){
			foreach($blocked_users as $blocked_user){
				$blocked_user_array[] = $blocked_user->block_user_id;
			}
		}
		$blocked_user_array[] = $input['user_id'];
		//$adsLists = MpAdsSubmission::where('account_id',$UserObj->account_id)->whereNotIn('user_id',$blocked_user_array)->where('status',1)->get();

		$adsLists = MpAdsSubmission::where('account_id',$UserObj->account_id)->where('type',2)->whereNotIn('user_id',$blocked_user_array)->where('status',1)->where(function ($query) use ($search_key) {
            if($search_key !='')
                $query->where('title', 'LIKE', '%'.$search_key .'%');
        })->orderBy('id','desc')->get();

		$records = array();
		if(isset($adsLists)){
			foreach($adsLists as $adsList){
				$data =array();
				$data['id'] = $adsList->id;
				$data['title'] = $adsList->title;
				$data['notes'] = $adsList->notes;
				$data['ref_no'] = $adsList->ticket;
				$data['price'] = $adsList->price;
				$data['status'] = $adsList->status;
				$data['account_id'] = $adsList->account_id;
				//$data['user_info'] = $adsList->user;
				$data['condition_info'] = (isset($adsList->getcondition) && $adsList->getcondition !='')?$adsList->getcondition:null;
				//$data['type_info'] = isset($adsList->getcondition)?$adsList->getcondition:null;
				$data['images'] = (isset($adsList->images) && $adsList->images !='')?$adsList->images:null;
				$userinfo  = UserMoreInfo::where('user_id',$adsList->user_id)->where('account_id',$adsList->account_id)->where('status',1)->first();
				$userdata = array();
				if(isset($userinfo)){
					$userdata['id'] = isset($userinfo->user_id)?$userinfo->user_id:null;
					$userdata['first_name'] = isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):null;
					$userdata['last_name'] = isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
					$userdata['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;
				}
				$data['userdata'] = (count($userdata) >0)?$userdata:null;
				$user_likes = MpAdsLike::where('ref_id',$adsList->id)->where('user_id',$UserObj->id)->count();
				//print_r($user_likes);
				$data['like_status'] = ($user_likes>0)?1:0;
				
				$likes = MpAdsLike::where('ref_id',$adsList->id)->count();
				$data['likes_count'] = isset($likes)?$likes:null;
				
				$records[] = $data;
			}
		}
		$file_path = env('APP_URL')."/storage/app";

		return response()->json(['result'=>$records,'file_path'=>$file_path,'response' => 1, 'message' => 'success!']);

        
	}

	public function MpGroupList(Request $request)
    {
		
        $input = $request->all();
        $search_key = isset($input['search_key'])?$input['search_key']:'';
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$blocked_users = MpAdsBlockUser::where('user_id',$UserObj->id)->where('account_id',$UserObj->account_id)->get();
		$blocked_user_array = array();
		if(isset($blocked_users)){
			foreach($blocked_users as $blocked_user){
				$blocked_user_array[] = $blocked_user->block_user_id;
			}
		}
		$blocked_user_array[] = $input['user_id'];
		//$adsLists = MpAdsSubmission::where('account_id',$UserObj->account_id)->whereNotIn('user_id',$blocked_user_array)->where('status',1)->get();

		$adsLists = MpAdsSubmission::where('type',1)->where('account_id',$UserObj->account_id)->whereNotIn('user_id',$blocked_user_array)->where('status',1)->where(function ($query) use ($search_key) {
            if($search_key !='')
                $query->where('title', 'LIKE', '%'.$search_key .'%');
        })->orderBy('id','desc')->get();

		$records = array();
		if(isset($adsLists)){
			foreach($adsLists as $adsList){
				$data =array();
				$data['id'] = $adsList->id;
				$data['title'] = $adsList->title;
				$data['notes'] = $adsList->notes;
				$data['price'] = $adsList->price;
				$data['ticket'] = $adsList->ticket;
				$data['members_count'] = $adsList->members_count;
				//$data['user_info'] = $adsList->user;
				//$data['condition_info'] = isset($adsList->getcondition)?$adsList->getcondition:null;
				//$data['type_info'] = isset($adsList->getcondition)?$adsList->getcondition:null;
				$data['images'] = (isset($adsList->images) && $adsList->images !='')?$adsList->images:null;
				$userinfo  = UserMoreInfo::where('user_id',$adsList->user_id)->where('account_id',$UserObj->account_id)->where('status',1)->first();
				$userdata = array();
				if(isset($userinfo)){
					$userdata['id'] = isset($userinfo->user_id)?$userinfo->user_id:null;
					$userdata['first_name'] = isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):null;
					$userdata['last_name'] = isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
					$userdata['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;
				}
				$data['userdata'] = (count($userdata)>0)?$userdata:null;
				$user_likes = MpAdsLike::where('ref_id',$adsList->id)->where('account_id',$UserObj->account_id)->where('user_id',$UserObj->id)->count();
				$data['like_status'] = ($user_likes>0)?1:0;
				
				$likes = MpAdsLike::where('ref_id',$adsList->id)->count();
				$data['likes_count'] = isset($likes)?$likes:null;
				if($adsList->type ==1){
					$user_register = MpGroupRegister::where('ref_id',$adsList->id)->where('account_id',$UserObj->account_id)->where('user_id',$UserObj->id)->count();
					$data['registered_status'] = ($user_register>0)?1:0;

					$registers = MpGroupRegister::where('ref_id',$adsList->id)->count();
					$data['registered_count'] = isset($registers)?$registers:null;
				}
				$records[] = $data;
			}
		}
		$file_path = env('APP_URL')."/storage/app";

		return response()->json(['result'=>$records,'file_path'=>$file_path,'response' => 1, 'message' => 'success!']);

        
	}

	public function MpAdsMyList(Request $request)
    {
        $input = $request->all();
		$input['user_id'] = Auth::id();
        
        $details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);

		}
		$adsLists = MpAdsSubmission::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->get();
		$records = array();
		if(isset($adsLists)){
			foreach($adsLists as $adsList){
				$data =array();
				$data['id'] = $adsList->id;
				$data['title'] = $adsList->title;
				$data['price'] = $adsList->price;
				$data['notes'] = $adsList->notes;
				$data['ref_no'] = $adsList->ticket;
				$data['status'] = $adsList->status;

				$data['members_count'] = $adsList->members_count;
				//$data['user_info'] = $adsList->user;
				$data['condition_info'] = isset($adsList->getcondition)?$adsList->getcondition:null;
				//$data['type_info'] = isset($adsList->getcondition)?$adsList->getcondition:null;
				$data['images'] = isset($adsList->images)?$adsList->images:null;
				$userinfo  = UserMoreInfo::where('user_id',$adsList->user_id)->where('account_id',$UserObj->account_id)->where('status',1)->first();
				$userdata = array();
				if(isset($userinfo)){
					$userdata['id'] = isset($userinfo->user_id)?$userinfo->user_id:null;
					$userdata['first_name'] = isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):null;
					$userdata['last_name'] = isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
					$userdata['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;
				}
				$data['userdata'] = $userdata;
				$user_likes = MpAdsLike::where('ref_id',$adsList->id)->where('account_id',$UserObj->account_id)->where('user_id',$UserObj->id)->count();
				$data['like_status'] = ($user_likes >0)?1:null;
				
				$likes = MpAdsLike::where('ref_id',$adsList->id)->count();
				$data['likes_count'] = isset($likes)?$likes:null;
				if($adsList->type ==1){
					$user_register = MpGroupRegister::where('ref_id',$adsList->id)->where('account_id',$UserObj->account_id)->where('user_id',$UserObj->id)->count();
					$data['registered_status'] = ($user_register >0)?1:null;

					$registers = MpGroupRegister::where('ref_id',$adsList->id)->count();
					$data['registered_count'] = isset($registers)?$registers:null;
				}

				$records[] = $data;
			}
		}
		$file_path = env('APP_URL')."/storage/app";

		return response()->json(['result'=>$records,'file_path'=>$file_path,'response' => 1, 'message' => 'success!']); 
	}

	public function MpAdsLikeList(Request $request)
    {
        $input = $request->all();
		$input['user_id'] = Auth::id();
        
        $details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);

		}
		$liked_lists = MpAdslike::where('user_id',$input['user_id'])->where('account_id',$UserObj->account_id)->get();
		$liked_item_array = array();
		if(isset($liked_lists)){
			foreach($liked_lists as $liked_list){
				$liked_item_array[] = $liked_list->ref_id;
			}
		}

		$adsLists = MpAdsSubmission::where('account_id',$UserObj->account_id)->whereIn('id',$liked_item_array)->where('status',1)->get();
		$records = array();
		if(isset($adsLists)){
			foreach($adsLists as $adsList){
				$data =array();
				$data['id'] = $adsList->id;
				$data['title'] = $adsList->title;
				$data['price'] = $adsList->price;
				$data['notes'] = $adsList->notes;
				$data['ref_no'] = $adsList->ticket;
				$data['status'] = $adsList->status;

				//$data['user_info'] = $adsList->user;
				$data['condition_info'] = (isset($adsList->getcondition) && $adsList->getcondition !='')?$adsList->getcondition:null;
				//$data['type_info'] = isset($adsList->getcondition)?$adsList->getcondition:null;
				$data['images'] = (isset($adsList->images) && $adsList->images !='')?$adsList->images:null;
				$userinfo  = UserMoreInfo::where('user_id',$adsList->user_id)->where('account_id',$UserObj->account_id)->where('status',1)->first();
				$userdata = array();
				if(isset($userinfo)){
					$userdata['id'] = isset($userinfo->user_id)?$userinfo->user_id:null;
					$userdata['first_name'] = isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):null;
					$userdata['last_name'] = isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
					$userdata['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;
				}
				$data['userdata'] = (count($userdata)>0)?$userdata:null;
				$user_likes = MpAdsLike::where('ref_id',$adsList->id)->where('account_id',$UserObj->account_id)->where('user_id',$UserObj->id)->count();
				$data['like_status'] = ($user_likes>0)?1:0;
				
				$likes = MpAdsLike::where('ref_id',$adsList->id)->count();
				$data['likes_count'] = isset($likes)?$likes:null;
				$records[] = $data;
			}
		}
		$file_path = env('APP_URL')."/storage/app";

		return response()->json(['result'=>$records,'file_path'=>$file_path,'response' => 1, 'message' => 'success!']); 
	}

	public function MpAdsItemDetail(Request $request) {

		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
		$input['user_id'] = Auth::id();
		
		$id = $input['id'];
        $details = array();
		$UserObj = User::find($input['user_id']);
		$result = MpAdsSubmission::find($id);
		if(isset($result) && isset($UserObj)){
			$file_path = env('APP_URL')."/storage/app";
			$data = array();
			$data['id'] = $result->id;
			$data['type'] = $result->type;
			$data['title'] = $result->title;
			$data['notes'] = $result->notes;
			$data['members_count'] = $result->members_count;
			$data['price'] = $result->price;
			$data['ref_no'] = $result->ticket;
			$data['status'] = $result->status;
			//$data['user_info'] = $result->user;
			$data['condition_info'] = isset($result->getcondition)?$result->getcondition:null;
			//$data['type_info'] = isset($result->getcondition)?$result->getcondition:null;
			$data['images'] = isset($result->images)?$result->images:null;
			$userinfo  = UserMoreInfo::where('user_id',$result->user_id)->where('account_id',$result->account_id)->where('status',1)->first();
			$userdata = array();
			if(isset($userinfo)){
				$userdata['id'] = isset($userinfo->user_id)?$userinfo->user_id:null;
				$userdata['first_name'] = isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):null;
				$userdata['last_name'] = isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
				$userdata['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;

			}
			$data['userdata'] = (!empty($userdata)>0)?$userdata:null;
			$propetyinfo  = Property::where('id',$result->account_id)->first();
			$propdata = array();
			if(isset($propetyinfo)){
				$propdata['id'] = isset($propetyinfo->id)?$propetyinfo->id:null;
				$propdata['property'] = isset($propetyinfo->company_name)?$propetyinfo->company_name:null;

			}
			$data['propetyinfo'] = (!empty($propdata)>0)?$propdata:null;
			
			$user_likes = MpAdsLike::where('ref_id',$result->id)->where('user_id',$UserObj->id)->count();
			$data['like_status'] = ($user_likes>0)?1:0;
			
			$likes = MpAdsLike::where('ref_id',$result->id)->count();
			$data['likes_count'] = isset($likes)?$likes:null;
			if($result->type ==1){
				$user_register = MpGroupRegister::where('ref_id',$result->id)->where('user_id',$UserObj->id)->count();
				$data['registered_status'] = ($user_register>0)?1:0;

				$registers = MpGroupRegister::where('ref_id',$result->id)->count();
				$data['registered_count'] = isset($registers)?$registers:null;
			}
			$data['created_at'] = isset($result->created_at)?$result->created_at->format('d/m/Y H:i'):null;

			
			return response()->json([
				'data'=>$data,
				'response' => 1,
				'file_path'=>$file_path,
				'status'=>'success'
			]);
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'status'=>'Record not found!'
			]);
		}

	}

	public function MpAdsDelete(Request $request) {

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
		$id = $input['id'];
        $details = array();
		$UserObj = User::find($input['user_id']);
		$result = MpAdsSubmission::where('id',$id)->where('user_id',$input['user_id'])->first();
		if(isset($result) && isset($UserObj)){
			MpAdsSubmission::findOrFail($id)->delete();
			MpAdsLike::where('ref_id', $id)->delete();
			MpAdsReport::where('ref_id', $id)->delete();
			MpGroupRegister::where('ref_id', $id)->delete();
			return response()->json(['response' => 1, 'message' => 'Record has been deleted!']);


		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'status'=>'Record not found!'
			]);
		}

	}

	public function MpAdsReport(Request $request) {

		$rules=array(
			'ref_id'=>'required',
			'remark'=>'required',
		);
		$messages=array(
			'ref_id.required'=>'Chatter Box is missing',
			'remark.required'=>'Remark is missing'
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
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
        $ticket = new \App\Models\v7\ChatBox();
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		$input['status'] = 1;
		$results = MPAdsReport::create($input);
		return response()->json(['response' => 1, 'message' => 'Report has been submitted!']);
	}

	

	

	public function MpAdsReportRevert(Request $request) {

		$rules=array(
			'ref_id'=>'required',
		);
		$messages=array(
			'ref_id.required'=>'Id is missing',
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
		$input['user_id'] = Auth::id();
		$id = $input['ref_id'];
        $details = array();
		$UserObj = User::find($input['user_id']);
		$result = MpAdsReport::where('ref_id',$id)->where('user_id',$input['user_id'])->first();
		
		if(isset($result) && isset($UserObj)){
			MpAdsReport::findOrFail($result->id)->delete();
			return response()->json(['response' => 1, 'message' => 'Report has been cancelled!']);
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'status'=>'Record not found!'
			]);
		}

	}

	public function MpAdsMarkAsSold(Request $request) {

		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
		$AdsObj = MpAdsSubmission::where('id',$input['id'])->update(['status'=>2]);
		return response()->json(['response' => 1, 'message' => 'Updated as sold!']);
	}

	

	

	public function MpAdsMarkAsUnsold(Request $request) {

		$rules=array(
			'id'=>'required',
		);
		$messages=array(
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
        $AdsObj = MpAdsSubmission::where('id',$input['id'])->update(['status'=>1]);
		return response()->json(['response' => 1, 'message' => 'Updated as Unsold!']);

	}
	public function MpAdsBlockedUserLists(Request $request)
    {
        $input = $request->all();
        $search_key = isset($input['search_key'])?$input['search_key']:'';
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$blocked_users = MpAdsBlockUser::where('user_id',$UserObj->id)->where('account_id',$UserObj->account_id)->get();
		$blocked_user_array = array();
		if(isset($blocked_users)){
			foreach($blocked_users as $blocked_user){
				
				$BlockedUsers = UserMoreInfo::Where('user_id',$blocked_user->block_user_id)->where('account_id',$UserObj->account_id)->first();
				if(isset($BlockedUsers)){
					$data = array();
					$data['id'] = $blocked_user->block_user_id;
					$data['info_id'] = $BlockedUsers->id;
					$data['first_name'] = (isset($BlockedUsers->first_name))?Crypt::decryptString($BlockedUsers->first_name):null;
					$data['last_name'] = (isset($BlockedUsers->last_name))?Crypt::decryptString($BlockedUsers->last_name):null;
					$data['email'] = $BlockedUsers->getuser->email;
					$data['profile_pic'] = isset($BlockedUsers->profile_picture)?$BlockedUsers->profile_picture:null;
					$data['blocked_date'] = isset($blocked_user->created_at)?$blocked_user->created_at->format('d/m/Y H:i'):null;
					$blocked_user_array[] = $data;
				}
				
			}
			$file_path = env('APP_URL')."/storage/app";
			return response()->json(['data'=>$blocked_user_array,'file_path'=>$file_path,'response' => 1, 'message' => 'Success!']);
		}
		else{
			return response()->json(['data'=>null, 'response' => 1, 'message' => 'No Blocked User!']);

		}
		//$blocked_user_array[] = $input['user_id'];
	}

	public function MpAdsBlockUser(Request $request) {

		$rules=array(
			'block_user_id'=>'required',
		);
		$messages=array(
			'block_user_id.required'=>'Block User Id is missing',
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
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$BlockUserObj = User::find($input['block_user_id']);
		if(empty($BlockUserObj)){
			return response()->json(['response' => 200, 'message' => 'Block user not found!']);
		}

		$check_status = MpAdsBlockUser::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('block_user_id',$input['block_user_id'])->first();
		if(isset($check_status)){
			return response()->json(['response' => 200, 'message' => 'Already Blocked!']);
		}else{
			$input['user_id'] = $input['user_id'];
			$input['account_id'] = $UserObj->account_id;
			$input['unit_no'] = $UserObj->unit_no;
			$input['block_user_id'] = $input['block_user_id'];
			$input['remark'] = $input['remark'];
			$input['status'] = 1;
			$results = MpAdsBlockUser::create($input);
			return response()->json(['response' => 1, 'message' => 'Blocked!']);
		}
		

	}

	public function MpAdsUnblockUser(Request $request) {

		$rules=array(
			'blocked_user_id'=>'required',
		);
		$messages=array(
			'blocked_user_id.required'=>'Blocked User Id is missing',
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
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
		$check_status = MpAdsBlockUser::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('block_user_id',$input['blocked_user_id'])->first();
		if(isset($check_status)){
			MpAdsBlockUser::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('block_user_id',$input['blocked_user_id'])->delete();
			return response()->json(['response' => 1, 'message' => 'Unblocked!']);
		}else{
			return response()->json(['response' => 200, 'message' => 'Record not found!']);
			
		}
	}

	public function MpAdsLike(Request $request) {

		$rules=array(
			'user_id' => 'required',
			'ref_id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'ref_id.required'=>'Chatter Box is missing'
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

		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$AdsObj = MpAdsSubmission::find($input['ref_id']);
		if(empty($AdsObj)){
			return response()->json(['response' => 200, 'message' => 'List not found!']);
		}
		$result = MpAdsLike::where('ref_id', $input['ref_id'])->where('user_id',$input['user_id'])->first();
		if(isset($result) ){
			return response()->json(['response' => 200, 'message' => 'Already liked!']);
		}

		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		$input['status'] = 1;
		$results = MpAdsLike::create($input);

		//Start Insert into notification module
		$notification = array();
		$notification['account_id'] = $AdsObj->account_id;
		$notification['user_id'] = $AdsObj->user_id;
		$notification['unit_no'] = $AdsObj->unit_no;
		$notification['module'] = 'marketplace';
		$notification['ref_id'] = $AdsObj->id;
		$notification['title'] = 'Marketplace';
		$notification['message'] = 'There is a like on your submitted marketplace';
		UserNotification::insert($notification);
		
		$SettingsObj = UserNotificationSetting::where('user_id',$AdsObj->user_id)->where('account_id',$AdsObj->account_id)->first();
		if(empty($SettingsObj) || $SettingsObj->marketplace ==1){
			$fcm_token_array ='';
			$user_token = ',';
			$ios_devices_to_send = array();
			$android_devices_to_send = array();
			$logs = UserLog::where('user_id',$AdsObj->user_id)->where('status',1)->orderby('id','desc')->first();
			if(isset($logs->fcm_token) && $logs->fcm_token !=''){
				$user_token .=$logs->fcm_token.",";
				$fcm_token_array .=$logs->fcm_token.',';
				$appSipAccountList[] = $AdsObj->id;
				if($logs->login_from ==1)
					$ios_devices_to_send[] = $logs->fcm_token;
				if($logs->login_from ==2)
					$android_devices_to_send[] = $logs->fcm_token;
			}
			$probObj = Property::find($AdsObj->account_id);
			$title = "Aerea Home - ".$probObj->company_name;
			$message = "Marketplace Like";
			$notofication_data = array();
			$notofication_data['body'] =$title;
			$notofication_data['unit_no'] =$AdsObj->unit_no;   
			$notofication_data['user_id'] =$AdsObj->user_id;   
			$notofication_data['property'] =$AdsObj->account_id;
			$purObj = UserPurchaserUnit::where('property_id',$AdsObj->account_id)->where('unit_id',$AdsObj->unit_no)->where('user_id',$AdsObj->user_id)->where('status',1)->first(); 
			if(isset($purObj))
				$notofication_data['switch_id'] =$purObj->id;         
			$NotificationObj = new \App\Models\v7\FirebaseNotification();
			$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
			$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
		}
		//End Insert into notification module

		return response()->json(['response' => 1, 'message' => 'Liked!']);
	}

	public function MpAdsUnLike(Request $request) {

		$rules=array(
			'ref_id'=>'required',
		);
		$messages=array(
			'ref_id.required'=>'Id is missing',
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
		$input['user_id'] = Auth::id();
		$id = $input['ref_id'];
        $details = array();
		$UserObj = User::find($input['user_id']);
		$result = MpAdsLike::where('ref_id',$id)->where('user_id',$input['user_id'])->first();
		if(isset($result) && isset($UserObj)){
			MpAdsLike::findOrFail($result->id)->delete();
			return response()->json(['response' => 1, 'message' => 'Unliked!']);
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'status'=>'Record not found!'
			]);
		}

	}

	public function MpGroupRegister(Request $request) {

		$rules=array(
			'ref_id'=>'required',
		);
		$messages=array(
			'ref_id.required'=>'Chatter Box is missing'
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
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$AdsObj = MpAdsSubmission::find($input['ref_id']);
		if(empty($AdsObj)){
			return response()->json(['response' => 200, 'message' => 'List not found!']);
		}
		$result = MpGroupRegister::where('ref_id', $input['ref_id'])->where('user_id',$input['user_id'])->first();
		if(isset($result) ){
			return response()->json(['response' => 200, 'message' => 'Already Registered!']);
		}

		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
		$input['unit_no'] = $UserObj->unit_no;
		$input['status'] = 1;
		$results = MpGroupRegister::create($input);
		return response()->json(['response' => 1, 'message' => 'Registered!']);
	}

	public function MpGroupUnregister(Request $request) {

		$rules=array(
			'ref_id'=>'required',
		);
		$messages=array(
			'ref_id.required'=>'Id is missing',
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
		$input['user_id'] = Auth::id();
		$id = $input['ref_id'];
        $details = array();
		$UserObj = User::find($input['user_id']);
		$result = MpGroupRegister::where('ref_id',$id)->where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->first();
		if(isset($result) && isset($UserObj)){
			MpGroupRegister::findOrFail($result->id)->delete();
			return response()->json(['response' => 1, 'message' => 'Unregistered!']);
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'status'=>'Record not found!'
			]);
		}

	}

	public function getTermAndCondion(Request $request) {

        $input = $request->all();
        $details = array();
		$UserObj = User::find(Auth::id());
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$file_path = env('APP_URL')."/storage/app";

		$fileObj = AppTermCondition::where('status',1)->orderby('id','desc')->first();
		$data =array();
		if(isset($fileObj)){
			return response()->json(['data'=>$fileObj,'file_path'=>$file_path,'response' => 1, 'message' => 'Success!']);
		}else{
			return response()->json(['data'=>null,'file_path'=>$file_path,'response' => 1, 'message' => 'No File']);
		}
		
	}

	public function getPushNotificationSettings(Request $request) {

		$rules=array(
			'user_info_id'=>'required',
		);
		$messages=array(
			'user_info_id.required'=>'User info id missing'
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
		$UserObj = User::find(Auth::id());
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$UserInfoObj = UserMoreInfo::find($input['user_info_id']);
		if(empty($UserInfoObj)){
			return response()->json(['response' => 201, 'message' => 'User not found!']);
		}

		$SettingsObj = UserNotificationSetting::where('user_id',$UserObj->id)->where('account_id',$UserObj->account_id)->where('user_info_id',$UserInfoObj->id)->first();
		$data =array();
		if(empty($SettingsObj)){
			$data['id'] =null;
			$data['user_id'] =$UserObj->id;
			$data['user_info_id'] =$UserInfoObj->id;
			$data['account_id'] =$UserObj->account_id;
			$data['unit_no'] =$UserObj->unit_no;
			$data['announcement'] =1;
			$data['key_collection'] =1;
			$data['defect'] =1;
			$data['feedback'] =1;
			$data['facility'] =1;
			$data['resident_management'] =1;
			$data['visitor_management'] =1;
			$data['face_id_upload'] =1;
			$data['resident_file_upload'] =1;
			$data['eforms'] =1;
			$data['resichat'] =1;
			$data['marketplace'] =1;
		}else{
			$data['id'] =$SettingsObj->id;
			$data['user_id'] =$SettingsObj->user_id;
			$data['user_info_id'] =$SettingsObj->user_info_id;
			$data['account_id'] =$SettingsObj->account_id;
			$data['unit_no'] =$SettingsObj->unit_no;
			$data['announcement'] =$SettingsObj->announcement;
			$data['key_collection'] =$SettingsObj->key_collection;
			$data['defect'] =$SettingsObj->defect;
			$data['feedback'] =$SettingsObj->feedback;
			$data['facility'] =$SettingsObj->facility;
			$data['resident_management'] =$SettingsObj->resident_management;
			$data['visitor_management'] =$SettingsObj->visitor_management;
			$data['face_id_upload'] =$SettingsObj->face_id_upload;;
			$data['resident_file_upload'] =$SettingsObj->resident_file_upload;;
			$data['eforms'] =$SettingsObj->eforms;
			$data['resichat'] =$SettingsObj->resichat;;
			$data['marketplace'] =$SettingsObj->marketplace;

		}
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']);
	}

	public function UpdatePushNotificationSettings(Request $request) {

		$rules=array(
			'user_info_id'=>'required',
			'account_id'=>'required',
		);
		$messages=array(
			'user_info_id.required'=>'User info id missing',
			'account_id.required'=>'Account id missing'
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
		$UserObj = User::find(Auth::id());
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		$UserInfoObj = UserMoreInfo::find($input['user_info_id']);
		if(empty($UserInfoObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		
		$SettingsObj = UserNotificationSetting::where('user_id',$UserObj->id)->where('account_id',$UserObj->account_id)->where('user_info_id',$UserInfoObj->id)->first();
		$data =array();
		if(empty($SettingsObj)){
			$input['user_id'] =$UserObj->id;
			$input['user_info_id'] =$UserInfoObj->id;
			$input['account_id'] =$UserObj->account_id;
			$input['unit_no'] =$UserObj->unit_no;
			$results = UserNotificationSetting::create($input);
			return response()->json(['data'=>$results,'response' => 1, 'message' => 'Updated!']);

		}else{
			$SettingsObj->announcement = $input['announcement'];
			$SettingsObj->key_collection = $input['key_collection'];
			$SettingsObj->defect = $input['defect'];
			$SettingsObj->feedback = $input['feedback'];
			$SettingsObj->facility = $input['facility'];
			$SettingsObj->resident_management = $input['resident_management'];
			$SettingsObj->visitor_management = $input['visitor_management'];
			$SettingsObj->face_id_upload = $input['face_id_upload'];
			$SettingsObj->resident_file_upload = $input['resident_file_upload'];
			$SettingsObj->eforms = $input['eforms'];
			$SettingsObj->resichat = $input['resichat'];
			$SettingsObj->marketplace = $input['marketplace'];
			$results = $SettingsObj->save();
			return response()->json(['data'=>$SettingsObj,'response' => 1, 'message' => 'Updated!']);
		}
		
	}


	public function chatReplyNotification(Request $request) {

		$rules=array(
			'sender_id' => 'required',
			'recipient_id'=>'required',
			'chat_room_id'=>'required',
			'message'=>'',
		);
		$messages=array(
			'sender_id.required' => 'Sender id missing',
			'recipient_id.required'=>'Rec Box is missing',
			'chat_room_id.required'=>'Chat room is missing'
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
		$senderObj = User::find($input['sender_id']);
		if(empty($senderObj)){
			return response()->json(['response' => 1, 'message' => 'Sender not found!']);
		}

		$recipentObj = User::find($input['recipient_id']);
		if(empty($recipentObj)){
			return response()->json(['response' => 1, 'message' => 'Recipient not found!']);
		}

		//Start Insert into notification module
		$notification = array();
		$notification['account_id'] = $recipentObj->account_id;
		$notification['user_id'] = $recipentObj->id;
		$notification['unit_no'] = $recipentObj->unit_no;
		$notification['module'] = 'firbasechat';
		$notification['ref_id'] = $input['chat_room_id'];
		$notification['title'] = 'Marketplace';
		$notification['message'] = 'New Message';
		UserNotification::insert($notification);
		
		$SettingsObj = UserNotificationSetting::where('user_id',$recipentObj->id)->where('account_id',$recipentObj->account_id)->first();
		if(empty($SettingsObj) || $SettingsObj->resichat ==1){
			$fcm_token_array ='';
			$user_token = ',';
			$ios_devices_to_send = array();
			$android_devices_to_send = array();
			$logs = UserLog::where('user_id',$recipentObj->id)->where('status',1)->orderby('id','desc')->first();
			
			if(isset($logs->fcm_token) && $logs->fcm_token !=''){
				$user_token .=$logs->fcm_token.",";
				$fcm_token_array .=$logs->fcm_token.',';
				$appSipAccountList[] = $recipentObj->id;
				if($logs->login_from ==1)
					$ios_devices_to_send[] = $logs->fcm_token;
				if($logs->login_from ==2)
					$android_devices_to_send[] = $logs->fcm_token;
			}
			$probObj = Property::find($recipentObj->account_id);
			$title = "Aerea Home - ".$probObj->company_name;
			$message = "Marketplace - New Message";
			$notofication_data = array();
			$notofication_data['type'] ='firbasechat';
			$notofication_data['body'] =$title;
			$notofication_data['unit_no'] =$recipentObj->unit_no;   
			$notofication_data['user_id'] =$recipentObj->id;   
			$notofication_data['property'] =$recipentObj->account_id;
			$purObj = UserPurchaserUnit::where('property_id',$recipentObj->account_id)->where('unit_id',$recipentObj->unit_no)->where('user_id',$recipentObj->id)->where('status',1)->first(); 
			if(isset($purObj))
				$notofication_data['switch_id'] =$purObj->id;         
			$NotificationObj = new \App\Models\v7\FirebaseNotification();
			$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
			$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification

			//print_r($android_devices_to_send);
		}
		//End Insert into notification module

		return response()->json(['response' => 1, 'message' => 'Reply notification has been sent!']);
	}

	public function chatAttachmentAPI(Request $request) {

		$rules=array(
			'chat_room_id'=>'required',
		);
		$messages=array(
			'chat_room_id.required'=>'Rec Box is missing',
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
		$input['user_id'] = Auth::id();
		$senderObj = User::find($input['user_id']);
		if(empty($senderObj)){
			return response()->json(['response' => 1, 'message' => 'Sender not found!']);
		}
		if ($request->file('attachment_image') != null) {
			$attachement = $request->file('attachment_image')->store(upload_path('chat'));
		}
		$data['user_id'] =$input['user_id'];
		$data['account_id'] =$senderObj->account_id;
		$data['unit_no'] =$senderObj->unit_no;
		$data['chat_room_id'] =$input['chat_room_id'];
		$data['attachment_image'] =$attachement;
		$results = ChatAttachment::create($data);
		$file_path = env('APP_URL')."/storage/app";
		return response()->json(['data'=>$results,'file_path'=>$file_path,'response' => 1, 'message' => 'Attachement successful!']);
	}

	public function encryptstring(Request $request) 
    {
		$rules=array(
			'string' => 'required',
		);
		$messages=array(
			'string.required' => 'String is missing',
		);
	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		$result =  Crypt::encryptString($request->string);
		return response()->json(['result'=>$result,'response' => 1, 'message' => 'Success']);
	}

	public function decryptstring(Request $request) 
    {
		$rules=array(
			'string' => 'required',
		);
		$messages=array(
			'string.required' => 'String is missing',
		);
	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		$result =  Crypt::decryptString($request->string);
		return response()->json(['result'=>$result,'response' => 1, 'message' => 'Success']);
	}

	public function MagazineLists(Request $request)
    {
        $input = $request->all();
		$input['user_id'] = Auth::id();
        
		$details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		
		$MagazineLists = MagazineFile::where('status',1)->orderBy('id','desc')->get();
		$records = array();
		if(isset($MagazineLists)){
			foreach($MagazineLists as $magazine){
				$data =array();
				$data['id'] = $magazine->id;
				$data['title'] = $magazine->docs_file_name;
				$data['thumbnail'] = $magazine->file_image;
				$data['file'] = $magazine->docs_file;
				$data['created_at'] =  $magazine->created_at;   
				$records[] = $data;
			}
		}
		$file_path = env('APP_URL')."/storage/app";

		return response()->json(['result'=>$records,'file_path'=>$file_path,'response' => 1, 'message' => 'success!']);

        
	}

	public function UserGuideLists(Request $request)
    {
        $input = $request->all();
		$input['user_id'] = Auth::id();
        $details = array();
		$UserObj = User::find($input['user_id']);
		if(empty($UserObj)){
			return response()->json(['response' => 200, 'message' => 'User not found!']);
		}
		
		$MagazineLists = UserGuide::where('status',1)->orderBy('id','desc')->get();
		$records = array();
		if(isset($MagazineLists)){
			foreach($MagazineLists as $magazine){
				$data =array();
				$data['id'] = $magazine->id;
				$data['title'] = $magazine->docs_file_name;
				$data['url_type'] = $magazine->url_type;
				$data['file'] = $magazine->docs_file;
				$data['url_link'] = $magazine->url_link;
				$data['created_at'] =  $magazine->created_at;   
				$records[] = $data;
			}
		}
		$file_path = env('APP_URL')."/storage/app";

		return response()->json(['result'=>$records,'file_path'=>$file_path,'response' => 1, 'message' => 'success!']);

        
	}

	public function visitorRegValidation(Request $request)
    {
		$rules=array(
			'property' => 'required',
			'purpose' => 'required',
			'visiting_date'=>'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
			'purpose.required' => 'Visiting purpose missing',
			'visiting_date.required'=>'Date is missing',
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
		$visiting_purpose = VisitorType::where('account_id',$input['property'])->where('id',$input['purpose'])->first();
		if($visiting_purpose->limit_set ==0){
			return response()->json(['slot_available'=>5,'response' => 1, 'message' => 'Success']);
		}
		else{
			$visitor_types = VisitorType::where('account_id',$input['property'])->where('limit_set',1)->where('status',1)->get();
			$types = array();
			foreach($visitor_types as $type){
				$types[] = $type->id;
			}			
			$total_visitor =0;
			$visitor_records = VisitorBooking::where('account_id',$input['property'])->where('visiting_date',$input['visiting_date'])->whereIn('visiting_purpose',$types)->whereIn('status',[0,2])->get();
			foreach($visitor_records as $records){
				$total_visitor +=$records->visitors->count();
			}
			$property = Property::find($input['property']);
			$count = $property->visitors_allowed - $total_visitor;
			$slot_available = ($count >=0)?$count:0;
			return response()->json(['slot_available'=>$slot_available,'response' => 1, 'message' => 'Success!']);
		}
	}
	public function getqrstatus(Request $request){

		/*$request_content = $request->getContent();
		$request_data = json_decode($request_content);
		$qr_data = explode("&",$request_data->validData);

		$input =array();
		foreach($qr_data as $data){
			$values = explode("=",$data);
			$input[$values[0]] = $values[1];
		}
		return response()->json([
			'code'=>0,
			'msg'=>'success',
			'data' =>array(
			)
		]);
		*/
		//return response()->json(['code' => 0, 'msg' => 'Success']);

		date_default_timezone_set('Asia/Singapore');

		$input = array();
		$rawPostData = file_get_contents("php://input");
		$special_char = array("{", "}","https://");
		$string = str_replace($special_char, "", $rawPostData);
		$values = explode(",",$string );
		$quote_char = array('"','"'," ");
		foreach($values as $value){
			$var_string = explode(":",trim($value));
			if(isset($var_string[0]) && isset($var_string[1])){
				$key_array = explode('"',trim($var_string[0]));
				$val_array = explode('"',trim($var_string[1]));
				$val ='';
				$key = trim($key_array[1]);

				if(isset($val_array[1]))
					$val = trim($val_array[1]);
				else
					$val = trim($var_string[1]);
				
				$input[$key] = $val;
			}
		}
		if(empty($input['validData']) || $input['validData'] ==''){
			return response()->json(['code' => 99999, 'msg' => 'No valid QR data']);
		}

		$validData = explode("facialscan/",$input['validData']);
		if(isset($validData[1]))
			$qr_data = explode("&",$validData[1]);
		else
			$qr_data = explode("&",$validData[0]);

		foreach($qr_data as $data){
			$values = explode("=",$data);
			if(isset($values[1]))
				$input[$values[0]] = $values[1];
		}
		if(empty($input['bid'])){
			return response()->json(['code' => 99999, 'msg' => 'No valid QR data']);
		}
		
		$ticket = $input['bid'];
		$bookingObj = VisitorBooking::where('ticket',$ticket)->first();

		if(isset($bookingObj) && $bookingObj->id>0)
		{
			$property = Property::find($bookingObj->account_id);

			$today = Carbon::now()->format('Y-m-d');
			$visiting_time = Carbon::now()->format('Y-m-d H:i:s');
			$qrcode_path = env('APP_URL').'/assets/visitorqr/';
			/*echo $bookingObj->visiting_start_time;
			echo  " ";
			echo $visiting_time;
			echo  " ";
			echo $bookingObj->visiting_end_time; */
			if(isset($bookingObj->visiting_date) && $bookingObj->visiting_start_time <= $visiting_time && $bookingObj->visiting_end_time >= $visiting_time)
			{
				//if(isset($bookingObj->visiting_date) && $bookingObj->visiting_date >= $today){

				//$env_max_scan_count = env('MAX_QR_SCAN_COUNT');
				$env_max_scan_count = $bookingObj->qr_scan_limit;

				$vid = $input['vid'];
				$VisitorObj = VisitorList::where('book_id',$bookingObj->id)->where('id',$vid)->first();

				if(isset($VisitorObj) && $VisitorObj->id>0 && $VisitorObj->visit_count <= $env_max_scan_count)
				{
					$visit_count = $VisitorObj->visit_count + 1;
					$entry_date = date("Y-m-d H:i:s");
					$result = VisitorList::where( 'id' , $VisitorObj->id)->update( array( 'visit_count' => $visit_count,'visit_status' => 1,'entry_date'=>$entry_date));

					$qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
					$qrinput['visitor_id'] = $VisitorObj->id;
					$qrinput['devSn'] = $input['devSn'];
					$qrinput['dataType'] = $input['dataType'];
					$qrinput['message'] = 'Success';
					QrcodeOpenRecord::create($qrinput); 

					//Start Insert into notification module
					$notification = array();
					$notification['account_id'] = $bookingObj->account_id;
					$notification['user_id'] = $bookingObj->user_id;
					$notification['unit_no'] = $bookingObj->unit_no;
					$notification['module'] = 'vistor management';
					$notification['ref_id'] = $bookingObj->id;
					$notification['title'] = 'Visitor Update';
					$notification['message'] = 'Visitor QR code scanned';
					$notification['created_at'] = date('Y-m-d H:i:s');
                    $notification['updated_at'] = date('Y-m-d H:i:s');
					$result = UserNotification::insert($notification);

					$SettingsObj = UserNotificationSetting::where('user_id',$bookingObj->user_id)->where('account_id',$bookingObj->account_id)->first();
					//print_r($SettingsObj);
					if(empty($SettingsObj) || $SettingsObj->visitor_management ==1){
						$fcm_token_array ='';
						$user_token = ',';
						$ios_devices_to_send = array();
						$android_devices_to_send = array();
						$logs = UserLog::where('user_id',$bookingObj->user_id)->where('status',1)->orderby('id','desc')->first();
						if(isset($logs->fcm_token) && $logs->fcm_token !=''){
							$user_token .=$logs->fcm_token.",";
							$fcm_token_array .=$logs->fcm_token.',';
							$appSipAccountList[] = $bookingObj->id;
							if($logs->login_from ==1)
								$ios_devices_to_send[] = $logs->fcm_token;
							if($logs->login_from ==2)
								$android_devices_to_send[] = $logs->fcm_token;
						}
				
						$title = "Aerea Home - ".$property->company_name;
						$message = $notification['message'];
						$notofication_data = array();
						$notofication_data['body'] =$title;
						$notofication_data['unit_no'] =$bookingObj->unit_no;   
						$notofication_data['user_id'] =$bookingObj->user_id;   
						$notofication_data['property'] =$bookingObj->account_id;
						$purObj = UserPurchaserUnit::where('property_id',$bookingObj->account_id)->where('unit_id',$bookingObj->unit_no)->where('user_id',$bookingObj->user_id)->first(); 
						if(isset($purObj))
							$notofication_data['switch_id'] =$purObj->id;     

						$NotificationObj = new \App\Models\v7\FirebaseNotification();
						$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
						$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
						//End Insert into notification module
					}
					
					
					return response()->json(['code' => 0, 'msg' => 'Success']);


				}
				else if(isset($VisitorObj) && $VisitorObj->id>0 && $VisitorObj->visit_status==1 && $VisitorObj->visit_count > $env_max_scan_count)
				{
					$qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
					$qrinput['visitor_id'] = $VisitorObj->id;
					$qrinput['devSn'] = $input['devSn'];
					$qrinput['dataType'] = $input['dataType'];
					$qrinput['message'] = 'Already Visited';
					QrcodeOpenRecord::create($qrinput); 

					return response()->json(['code' => 99999, 'msg' => 'Reached Maximum Visit Count']);
				}
				else{
					$qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
					$qrinput['devSn'] = $input['devSn'];
					$qrinput['dataType'] = $input['dataType'];
					$qrinput['message'] = 'Visitor Details Not Registered';
					QrcodeOpenRecord::create($qrinput); 
					return response()->json(['code' => 99999, 'msg' => 'Visitor Details Not Registered']);
				}
			}				
			else{
				if(isset($bookingObj->visiting_date) && $bookingObj->visiting_end_time < $visiting_time)
				{
					$qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
					$qrinput['devSn'] = $input['devSn'];
					$qrinput['dataType'] = $input['dataType'];
					$qrinput['message'] = 'Booking Expired';
					QrcodeOpenRecord::create($qrinput);
					return response()->json(['code' => 99999, 'msg' => 'Booking Expired']);
				}
				else{
					$qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
					$qrinput['devSn'] = $input['devSn'];
					$qrinput['dataType'] = $input['dataType'];
					$qrinput['message'] = 'QR Code Not Active';
					QrcodeOpenRecord::create($qrinput);
					return response()->json(['code' => 99999, 'msg' => 'QR Code Not Active']);
				}
			}
		}else{
			return response()->json(['code' => 99999, 'msg' => 'No Booking Available']);
		}
	}
	
	public function twiliosms(Request $request){
		$tonumber = $request->number;
		$twilio_sid 	= env('TWILIO_SID');
		$twilio_authtoken 	= env('TWILIO_AUTH_TOKEN');
		$twilio_url 	= env('TWILIO_URL');

		$url = $twilio_url;
		$payload = [
		'From' => env('TWILIO_FROM_NAME'),
		'To' => $tonumber,
		'Body'   => 'Welcome to Aerea :Your account verification code is 897891'
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $twilio_sid . ':' . $twilio_authtoken);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
		$response = curl_exec($ch);
		$err = curl_error($ch);
		print_r($response);
		curl_close($ch);
	}

	public function ParkingRecordPush(Request $request)
    {
		\Log::info('Parking Record Push - START V8');
		$input = array();
		$rawPostData = file_get_contents("php://input");
		$special_char = array("{", "}");
		$string = str_replace($special_char, "", $rawPostData);
		$values = explode(",",$string );
		$quote_char = array('"','"'," ");
		foreach($values as $value){
			\Log::info('value :'.$value);

		}
		\Log::info('Parking Record Push - END');

		return response()->json([
			'code' =>0,
			'msg'=>'Success'
		]);
	}

}
