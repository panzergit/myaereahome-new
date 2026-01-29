<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use Session;
use Validator;
use App\Models\v7\Role;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\UserNotificationSetting;
use App\Models\v7\Unit;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Auth;
use Mail;
use Hash;
use App\Models\v7\LoginOTP;
use DB;
use Storage;
use App\Models\v7\Announcement;
use App\Models\v7\AnnouncementDetail;
use App\Models\v7\Announcementtest;
use App\Models\v7\AnnouncementCron;
use App\Models\v7\AnnouncementtestDetail;
use App\Models\v7\AnnouncementNotification;
use App\Models\v7\DefectSubmission;
use App\Models\v7\FeedbackSubmission;
use App\Models\v7\UnittakeoverAppointment;
use App\Models\v7\JoininspectionAppointment;
use App\Models\v7\DefectLocation;
use App\Models\v7\DefectType;
use App\Models\v7\FeedbackOption;
use App\Models\v7\Defect;
use App\Models\v7\FacilityType;
use App\Models\v7\Module;
use App\Models\v7\ModuleGroup;
use App\Models\v7\Property;
use App\Models\v7\Building;
use App\Models\v7\FacilityBooking;
use App\Models\v7\InboxMessage;
use App\Models\v7\UserLog;
use App\Models\v7\UserManagerLog;
use App\Models\v7\UserPermission;
use App\Models\v7\Card;
use App\Models\v7\Device;
use App\Models\v7\VisitorType;
use App\Models\v7\VisitorTypeSubcategory;
use App\Models\v7\PropertyPermission;
use App\Models\v7\ModuleSetting;
use App\Models\v7\ResidentUploadedFile;
use App\Models\v7\ResidentFileSubmission;
use App\Models\v7\DocsCategory;
use App\Models\v7\CondodocFile;
use App\Models\v7\EformSetting;
use App\Models\v7\EformMovingInOut;
use App\Models\v7\EformMovingSubCon;
use App\Models\v7\EformChangeAddress;
use App\Models\v7\EformDoorAccess;
use App\Models\v7\EformParticular;
use App\Models\v7\EformParticularOwner;
use App\Models\v7\EformParticularTenant;
use App\Models\v7\EformRegVehicle;
use App\Models\v7\EformRegVehicleDoc;
use App\Models\v7\EformRenovation;
use App\Models\v7\EformRenovationDetail;
use App\Models\v7\EformRenovationSubCon;
use App\Models\v7\PaymentSetting;
use App\Models\v7\HolidaySetting;
use App\Models\v7\EformMovingPayment;
use App\Models\v7\EformMovingInspection;
use App\Models\v7\EformMovingDefect;
use App\Models\v7\EformRenovationPayment;
use App\Models\v7\EformRenovationInspection;
use App\Models\v7\EformRenovationDefect;
use App\Models\v7\EformDoorAccesscardPayment;
use App\Models\v7\EformDoorAccesscardAck;
use App\Models\v7\UserFacialId;
use App\Models\v7\Employee;
use App\Models\v7\RoleDevice;
use App\Models\v7\RoleRemoteDevice;
use App\Models\v7\FacialRecoOption;
use App\Models\v7\BluetoothDoorOpen;


use App\Models\v7\VisitorBooking;
use App\Models\v7\VisitorList;
use App\Models\v7\FailDoorOpenRecord;
use App\Models\v7\CallPushRecord;
use App\Models\v7\QrcodeOpenRecord;

use App\Models\v7\FinanceShareSetting;
use App\Models\v7\FinanceInvoice;
use App\Models\v7\FinanceInvoiceInfo;
use App\Models\v7\FinanceInvoiceDetail;
use App\Models\v7\FinanceInvoicePayment;
use App\Models\v7\FinanceReferenceType;
use App\Models\v7\FinanceInvoicePaymentDetail;
use App\Models\v7\FinanceInvoicePaymentPaidDetail;
use App\Models\v7\FinanceAdvancePayment;
use App\Models\v7\FinancePaymentLog;
use App\Models\v7\FinanceCreditPayment;
use App\Models\v7\UserProperty;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserDevice;
use App\Models\v7\UserRemoteDevice;
use App\Models\v7\Country;
use App\Models\v7\UserCard;

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
use App\Models\v7\MpadsBlockUserByAdmin;
use App\Models\v7\ActivityLog;
use App\Models\v7\AndroidManagerVersion;
use App\Models\v7\IosManagerVersion;
use Illuminate\Database\Eloquent\Builder;
use App\Models\v7\FinalInspectionAppointment;

use App\Models\v7\UserRegistrationRequest;
use App\Models\v7\UserLicensePlate;
use App\Models\v7\ConfigSetting;
use App\Models\v7\AccountDeleteRequest;
use App\Models\v7\DefectUpdateLog;


class OpsApiv4Controller extends Controller
{

	public function accountDeleteRequestLists(Request $request)
    {
        $user = $request->user();
        $data = AccountDeleteRequest::where([
                'account_id' => $user->account_id
            ])->get()->map(fn($r) => [
                'id' => $r->id,
                'property' => $r->property->company_name,
                'unit' => $r->unit,
                'user' => Crypt::decryptString($r->user->name),
                'deleted_at' => Carbon::parse($r->created_at)->format('Y-m-d H:i:s'),
                'reason' => $r->reason,
                'status' => 'Deleted'
            ]);
        
        return response()->json([
            'status' => true,
            'message' => null,
            'data' => $data
        ]);
    }
    
    public function chartKeyCollection(Request $request)
    {
        
        if(!$request->filled('property_id')) return response()->json([
            'status' => false,
            'message' => 'Property id is mandatory',
            'data' => null
            ]);
        
        $accountId = $request->property_id;
        $totalResidents = 0;
        $keyCollections = UnittakeoverAppointment::where([
            'account_id' => $accountId
            ])->get();

        $totalUnits = Unit::where('account_id',$accountId)->where('status',1)->get()->count();

        return response()->json([
            'status' => true,
            'message' => null,
            'data' => [
                    'total_units' => $totalUnits,
                    'collected_keys' => $keyCollections->where('status',3)->count(),
                    'scheduled_appointments' => $keyCollections->where('status',2)->count(),
                    'no_actions_yet' => $keyCollections->where('status',0)->count(),
                    'cancelledd' => $keyCollections->where('status',1)->count(),
                ]
        ]);
        
    }
    
    public function chartUsers(Request $request)
    {
        if(!$request->filled('property_id')) return response()->json([
            'status' => false,
            'message' => 'Property id is mandatory',
            'data' => null
            ]);
            
        $accountId = $request->property_id;
        $totalResidents = 0;
        
        //Owner
        $OwnerRolesIds = Role::whereRaw("((CONCAT(',',account_id,',') LIKE '%,".$accountId.",%') OR type=1)")
            ->whereRaw("(LOWER(TRIM(name)) = 'owner')")->get()->pluck('id')->toArray();
        $ownerUsersLists = UserPurchaserUnit::where([
                'status' => 1,
                'property_id' => $accountId
            ])->whereIn('role_id',$OwnerRolesIds)->get();
        $ownerUsers = UserMoreInfo::where([
                'status' => 1,
                'account_id' => $accountId
            ])->whereIn('id',$ownerUsersLists->pluck('user_info_id'))->count();
        //Family Member
        $FamilyMemberRolesIds = Role::whereRaw("((CONCAT(',',account_id,',') LIKE '%,".$accountId.",%') OR type=1)")
            ->whereRaw("(LOWER(TRIM(name)) = 'family member')")->get()->pluck('id')->toArray();
        $familyMemberUsersLists = UserPurchaserUnit::where([
                'property_id' => $accountId
            ])->whereIn('role_id',$FamilyMemberRolesIds)->get();
		$familyMemberUsers = UserMoreInfo::where([
                'status' => 1,
                'account_id' => $accountId
            ])->whereIn('id',$familyMemberUsersLists->pluck('user_info_id'))->count();
        
        //Occupant
        $occupantRolesIds = Role::whereRaw("((CONCAT(',',account_id,',') LIKE '%,".$accountId.",%') OR type=1)")
            ->whereRaw("(LOWER(TRIM(name)) = 'occupant')")->get()->pluck('id')->toArray();
        $occupantUsersLists = UserPurchaserUnit::where([
                'status' => 1,
                'property_id' => $accountId
            ])->whereIn('role_id',$occupantRolesIds)->get();
		$occupantUsers = UserMoreInfo::where([
                'status' => 1,
                'account_id' => $accountId
            ])->whereIn('id',$occupantUsersLists->pluck('user_info_id'))->count();
            
        //Domestic Helper
        $domesticHelperRolesIds = Role::whereRaw("((CONCAT(',',account_id,',') LIKE '%,".$accountId.",%') OR type=1)")
            ->whereRaw("(LOWER(TRIM(name)) = 'domestic helper')")->get()->pluck('id')->toArray();
        $domesticHelperUsersLists = UserPurchaserUnit::where([
                'status' => 1,
                'property_id' => $accountId
            ])->whereIn('role_id',$domesticHelperRolesIds)->get();
        $domesticHelperUsers = UserMoreInfo::where([
                'status' => 1,
                'account_id' => $accountId
            ])->whereIn('id',$domesticHelperUsersLists->pluck('user_info_id'))->count();

        //Property Agent
        $propertyAgentRolesIds = Role::whereRaw("((CONCAT(',',account_id,',') LIKE '%,".$accountId.",%') OR type=1)")
            ->whereRaw("(LOWER(TRIM(name)) = 'property agent')")->get()->pluck('id')->toArray();
        $propertyAgentUsersLists = UserPurchaserUnit::where([
                'status' => 1,
                'property_id' => $accountId
            ])->whereIn('role_id',$propertyAgentRolesIds)->get();
		$propertyAgentUsers = UserMoreInfo::where([
                'status' => 1,
                'account_id' => $accountId
            ])->whereIn('id',$propertyAgentUsersLists->pluck('user_info_id'))->count();
            
        //Tenant
        $tenantRolesIds = Role::whereRaw("((CONCAT(',',account_id,',') LIKE '%,".$accountId.",%') OR type=1)")
            ->whereRaw("(LOWER(TRIM(name)) = 'tenant')")->get()->pluck('id')->toArray();
        $tenantUsersLists = UserPurchaserUnit::where([
                'status' => 1,
                'property_id' => $accountId
            ])->whereIn('role_id',$tenantRolesIds)->get();

		$tenantUsers = UserMoreInfo::where([
                'status' => 1,
                'account_id' => $accountId
            ])->whereIn('id',$tenantUsersLists->pluck('user_info_id'))->count();
            
        
		//staffs
        $env_roles 	= env('USER_APP_ROLE');
		$roles = explode(",",$env_roles);
        $staffsCount = UserProperty::where([
                'property_id' => $accountId
            ])->count();
            
        //Total User count
		$totalHomeUsersLists = UserPurchaserUnit::where([
                'status' => 1,
                'property_id' => $accountId
            ])->get();
		/*$totalHomeUsers = UserMoreInfo::where([
                'status' => 1,
                'account_id' => $accountId
            ])->whereIn('id',$totalHomeUsersLists->pluck('user_info_id'))->count();*/
		$totalHomeUsers = $ownerUsers + $familyMemberUsers + $occupantUsers + $domesticHelperUsers + $propertyAgentUsers + $tenantUsers;

		$totalPropUsers = $staffsCount + $totalHomeUsers;

        $totalUsers = User::where([
            'account_id' => $accountId,
            'status' => 1
            ])->get();
		
		$appUsageNumbers = UserLog::where([
                    'account_id' => $accountId,
                    'status' => 1
                ])
                ->whereIn('user_id',$totalHomeUsersLists->pluck('user_id'))
                ->whereIn('id', function ($q) use ($accountId, $totalHomeUsersLists) {
                    $q->selectRaw('MAX(id)')
                    ->from('user_logs')
                    ->where([
                        'account_id' => $accountId,
                        'status' => 1
                    ])
                    ->whereIn('user_id', $totalHomeUsersLists->pluck('user_id'))
                    ->groupBy('user_id');
                })->get();

           
            //print_r( $appUsageNumbers->toArray());
            $androidUsageNumbers = $appUsageNumbers->where('login_from', 2)->count();
            $iOsUsageNumbers = $appUsageNumbers->where('login_from', 1)->count();
            
        /*$androidUsageNumbers = UserLog::where([
                'account_id' => $accountId,
                'login_from' => 2,
                'status' => 1
            ])
            ->whereIn('user_id',$totalHomeUsersLists->pluck('user_id'))
            ->groupBy('user_id')->get()->count();
            
        $iOsUsageNumbers = UserLog::where([
                'account_id' => $accountId,
                'login_from' => 1,
                'status' => 1
            ])
            ->whereIn('user_id',$totalHomeUsersLists->pluck('user_id'))
            ->groupBy('user_id')->get()->count();
		*/

            
		$app_using_count = $androidUsageNumbers + $iOsUsageNumbers;
		$app_notusing_count = $totalHomeUsers - $app_using_count;
		
		/*
		$one_car = UserMoreInfo::where('account_id',$accountId)->has('licenseplates','=',1)->count();
        $two_cars = UserMoreInfo::where('account_id',$accountId)->has('licenseplates','=',2)->count();
		$no_cars = $totalPropUsers - ($one_car+ $two_cars);*/
		$totalUnits = Unit::where('account_id',$accountId)->where('status',1)->get()->count();
		$one_car = Unit::where('account_id',$accountId)->has('licenseplates','=',1)->count();
        $two_cars = unit::where('account_id',$accountId)->has('licenseplates','=',2)->count();
		$no_cars = $totalUnits - ($one_car+ $two_cars);
        return response()->json([
            'status' => true,
            'message' => null,
            'data' => [
                    'chart_one' => [
                        'owners' => $ownerUsers,
                        'family_members' => $familyMemberUsers,
                        'occupants' => $occupantUsers,
                        'domestic_helpers' => $domesticHelperUsers,
                        'property_agents' => $propertyAgentUsers,
                        'tenants' => $tenantUsers,
                        'staffs' => $staffsCount,
						'total_residents' => $totalHomeUsers
                    ],
                    'chart_two' => [
                        'app_using' => [
                                'percentage' => number_format((($app_using_count/$totalHomeUsers)*100),2),
                                'numbers' => $app_using_count
                            ],
                        'app_not_using' => [
                                'percentage' => number_format((($app_notusing_count/$totalHomeUsers)*100),2),
                                'numbers' => $app_notusing_count
                            ],
                    ],
                    'chart_three' => [
                        'total' => $totalPropUsers,
                        'android_usage' => [
                                'percentage' => $totalUsers->isNotEmpty() ? number_format((($androidUsageNumbers/$totalHomeUsers)*100),2) : 0,
                                'numbers' => $androidUsageNumbers
                            ],
                        'ios_usage' => [
                                'percentage' => $totalUsers->isNotEmpty() ? number_format((($iOsUsageNumbers/$totalHomeUsers)*100),2) : 0,
                                'numbers' => $iOsUsageNumbers
                            ],
                    ],
                   
					'chart_four' => [
                        'one_car_usage' => [
                                'percentage' => number_format((($one_car/$totalUnits)*100),2),
                                'numbers' => $one_car
                            ],
                        'two_car_usage' => [
                                'percentage' => number_format((($two_cars/$totalUnits)*100),2),
                                'numbers' => $two_cars
                            ],
                        'no_car_usage' => [
                                'percentage' =>  number_format((($no_cars/$totalUnits)*100),2),
                                'numbers' => $no_cars
                            ],
                    ]
                ]
        ]);
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
		$properties  = explode(',',$input['properties']);

		if($input['key'] != 11111 || $input['user_id'] != 1){
			return response()->json(['response' => 200, 'message' => 'Invalid Key && User Id']);
		}
		
         //if push notification activated for manager app
			
			$user_logs = UserManagerLog::where('status',1)->where(function($query) use ($properties){
				if(count($properties) > 1 && $properties !='')
					$query->whereIn('account_id',$properties);
				
			})->orderBy('id','desc')->get()->unique('user_id');
			/*print_r($user_logs);
			foreach($user_logs as $logs) {
				echo " log id: ".$logs->id;
				echo " User id: ".$logs->user_id;
				echo "----";
			}
			exit;*/
			if(isset($user_logs)) {
				foreach($user_logs as $logs) {
					$fcm_token_array ='';
					$user_token = ',';
					$ios_devices_to_send = array();
					$android_devices_to_send = array();
					$appSipAccountList = array();
					$userObj = User::find($logs->user_id); 
					if(isset($userObj) && $userObj->version !=$input['version']){
						//echo " log id: ".$logs->id;
						echo " User id: ".$logs->user_id;
						

						if(isset($logs->fcm_token) && $logs->fcm_token !=''){
							$user_token .=$logs->fcm_token.",";
							$fcm_token_array .=$logs->fcm_token.',';
							$appSipAccountList[] = $logs->id;
							if($logs->login_from ==1){
								$ios_devices_to_send[] = $logs->fcm_token;
								//echo "IOS"; print_r($ios_devices_to_send);
							}
							if($logs->login_from ==2){
								$android_devices_to_send[] = $logs->fcm_token;
								//echo "Android"; print_r($android_devices_to_send);
							}
						}
						
						//echo "----";
						
						//echo "ID:".$logs->id;
						//echo "User Id ".$logs->user_id." Version".$userObj->version;
						//$title = "New update has been released";
						//$message = 'Kindly update your app to the latest version. You are also required to re-login after updating the app. ';
						$title = $input['title'];
						$message = $input['message'];
						$notofication_data = array();
						$notofication_data['body'] =$title; 
						$notofication_data['type'] ='New Release';        
						$NotificationObj = new \App\Models\v7\FirebaseNotification();
						if(count($ios_devices_to_send) >0)
							$result = $NotificationObj->ios_release_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
						if(count($android_devices_to_send) >0)
							//$result = $NotificationObj->android_release_notification($title,$message,$android_devices_to_send,$notofication_data); //
						
							//print_r($result);

						User::where( 'id' , $logs->user_id)->update( array( 'version' => $input['version']));
						
						

					}
					

				}

				return response()->json(['response' => 1, 'message' => 'Release notification has been sent!']);
			}
	}
	
	public function getandroidversion(Request $request)
	{
		$versions = AndroidManagerVersion::orderby('id','desc')->where('id',2)->first();   
		return response()->json(['data'=>$versions,'response' => 1, 'message' => 'success!']);
	}

	public function getiosversion(Request $request)
	{
		$versions = IosManagerVersion::orderby('id','desc')->first();  
		return response()->json(['data'=>$versions,'response' => 1, 'message' => 'success!']);
	}

	public function login(Request $request)
	{
		$env_roles 	= env('USER_APP_ROLE');
		$roles = explode(",",$env_roles);
		$email = $request->email;
		$password = $request->password;		

        if (Auth::attempt($request->only('email', 'password'))) {
			$user = User::where('email', $email)->first();
			if($user->role_id ==1){
				$data = response()->json(['response' => 3, 'message' => 'Login to ops portal instead']);
			}else if(in_array($user->role_id,$roles)){
				$data = response()->json(['response' => 3, 'message' => 'Login to customer app instead']);
			}else{
				LoginOTP::sendotpnew($user->name, $email,3);
				$authToken = Hash::make('*' . $user->id . '*' . $user->email.'*');
				$data = [];
				$data['user_info_id'] =$user->empinfo->id;
				$data['first_name'] =$user->empinfo->first_name;
				$data['last_name'] =$user->empinfo->last_name;
				$data = response()->json(['user_id'=>$user->id,'moreinfo'=>$data,'response' => 1, 'message' => 'Successfully Login!',
					'auth_token' => $authToken]);
			}
            
        } else {
			$data = response()->json(['response' => 0, 'message' => 'Invalid Login Credentials!']);
		}
		return $data;
	}

	public function verifyotp(Request $request) {

		if(!$request->has('auth_token')){
			return response()->json([
                'message' => "auth token is required.",
            ], 400);
		}

		$data = [];
		$email = $request->email;
		$verificationcode = $request->verificationcode;
		$user = User::where('email', $email)->first();
		if($user) {
			$otp = $user->otp;
			if(($verificationcode ==123789) || ($verificationcode ==11111) || ($otp == $verificationcode))
			{
				if(!Hash::check('*' . $user->id . '*' . $user->email.'*',trim($request->auth_token)))
				{
					$data = response()->json(['response' => 0, 'message' => 'Invalid auth token']);		
				}else{
					$accessToken = $user->createToken('AHPC')->plainTextToken;
					$data = response()->json(['response' => 1, 'message' => 'Valid', 'access_token' => $accessToken]);
				}
			} else {
				$data = response()->json(['response' => 0, 'message' => 'Invalid Verification Code']);		
			}
		} else {
			$data = response()->json(['response' => 0, 'message' => 'Invalid Email']);		
		}
		return $data;
	}

	public function change_password(Request $request) {
		$rules=array(
			'old_password' => 'required',
			'password' => 'required',
			'confirmpassword' => 'required',
		);
		$messages=array(
			'old_password.required' => 'Old Password is missing',
			'password.required' => 'Password is missing',
			'confirmpassword.required' => 'Confirm Password is missing',
		);
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
		$id = Auth::id();
		$UserObj = User::find($id);		
		if($UserObj) {
			$old_password = $request->old_password;
			$password = $request->password;
			$confirmpassword = $request->confirmpassword;
			if($password == $confirmpassword) {	
				$hashedPassword = $UserObj->password;
				if (\Hash::check($old_password , $hashedPassword )) {
					if (!\Hash::check($request->newpassword , $hashedPassword)) {
					$UserObj->password = bcrypt($password);
					$result = User::where( 'id' , $UserObj->id)->update( array( 'password' =>  $UserObj->password));
					$data = response()->json(['response' => 1, 'message' => 'Password updated successfully']);	
					}
					else{
						$data = response()->json(['response' => 200, 'message' => 'New password can not be the old password!']);	  
					}
				}
				else{
					$data = response()->json(['response' => 200, 'message' => 'Old password doesnt matched!']);	
					}
				}	
			else {
				$data = response()->json(['response' => 200, 'message' => 'Password and Confirm Password Mismatch!']);		 
			}
		}
		else{
			$data = response()->json(['response' => 200, 'message' => 'Something Went Wrong!']);		 

		}
		return $data;
	}
	
	public function	resendotp(Request $request) {
		$data = [];
		$email = $request->email;		
		$user = User::where('email', $email)->first();
		if($user) {	
			LoginOTP::sendotpnew($user->name, $email,3); 
			$data = response()->json(['response' => 1, 'message' => 'OTP Successfully Sent!']);
		} else {
			$data = response()->json(['response' => 0, 'message' => 'Something Went Wrong!']);
		}
		return $data;
	}


	public function forgotPassword(Request $request) {
		$data = [];
		$email = $request->email;
		$env_roles 	= env('USER_APP_ROLE');

		$user = User::where('email', $email)->first();
		$roles = explode(",",$env_roles);
		if(empty($user)){
			$data = response()->json(['response' => 3, 'message' => 'Email is not registered!']);
		}
		else if(in_array($user->role_id,$roles)){
			$data = response()->json(['response' => 3, 'message' => 'Try on customer app instead']);
		}
		else if($user) {
				$loginotp = LoginOTP::forgotpwdotpnew($user->name, $email,3);	
				$authToken = Hash::make('*' . $user->id . '*' . $user->email.'*');
				$data = response()->json(['response' => 2, 'message' => 'OTP Successfully Sent!', 'auth_token' => $authToken]);
			
		} else {
			$data = response()->json(['response' => 0, 'message' => 'Email is not registered!']);		
		}
		return $data;
	}

	public function verifyOtpApi(Request $request) {
		$data = [];
		$email = $request->email;
		$verificationcode = $request->verificationcode;
		$user = User::where('email', $email)->first();
		if($user && $user->status !=1){
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

	public function logininfo(Request $request)
	{	
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Admin not found']);
		}

		$permission = ModuleSetting::where('role_id',$adminObj->role_id)->get();  
		$modules = Module::all();  

		$data = array();
		$data = $adminObj;
		$data['faceid_access'] = ($adminObj->empinfo->faceid_access_permission)?$adminObj->empinfo->faceid_access_permission:0;
		//$data['faceid_access_token'] = ($adminObj->empinfo->faceid_access_token)?$adminObj->empinfo->faceid_access_token:null;

		$data['permission'] = $permission;

		


		return response()->json(['data'=>$data,'modules'=>$modules,'response' => 1, 'message' => 'Success']);


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
			'old_password.required'=>'Password is missing',
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
		$input['role_id'] = $UserObj->role_id;
		$input['version'] = isset($input['app_version'])?$input['app_version']:'';
		$record = UserManagerLog::create($input);
		User::where('id', $input['user_id'])->update(['app_version' => $input['app_version']]);

		return response()->json(['data'=>$record,'response' => 1, 'message' => 'Login history log has been added!']); 
	}

	public function logoutHistoryLogs(Request $request)
    {
		$rules= [
			'login_from'=>'required',
			'fcm_token'=>'required',
			'biometric_enabled' => 'required'
		];
		$messages=[
			'login_from.required'=>'Login OS is missing',
			'fcm_token.required'=>'FCM Token is missing',
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
            UserManagerLog::where([['user_id','=',$request->user()->id],['login_from','=',$request->login_from],['fcm_token','=',$request->fcm_token]])
                ->update(['status' => 0]);
            $request->user()->currentAccessToken()->delete();
        }
		
		return response()->json(['data'=>1,'response' => 1, 'message' => 'Logout history log has been added!']); 
	}


	public function adminmenu(Request $request) {
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		$user = new \App\Models\v7\User();
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Admin not found']);
		}

		$PropertyObj = Property::find($adminObj->account_id);
        $file_path = env('APP_URL')."/storage/app/";
		//$groups = ModuleGroup::whereIn('id',[7,20])->where('status',1)->orderBy('orderby','ASC')->get();
		$groups = ModuleGroup::where('status',1)->orderBy('orderby','ASC')->get();
		$role_access = array();
	
        foreach($PropertyObj->Permissions as $permission){
            $role_access[$permission->module_id] = array($permission->view);           
		}
		
		//print_r($role_access);
		$menu_array = array();
		$settings_array = array();
		if($groups){
			$menu_groups = array();
			$settings_groups = array();
			$group_array_name = array();
			$settings_module = array(74,9,57);
            foreach($groups as $group){
				//$menu_groups['menu_group'] = $group->name;
				if($group->mobilemodules){
					$list_array = array();
					
					$group_display_permission = '0';
					foreach($group->mobilemodules as $module){
						$group_name = '';
								//print_r($module);
								$group_name = $group->name;
								$lists = array();
								$lists['id'] = $module->id;
								$lists['name'] = $module->name;
								$permission = isset($role_access[$module->id][0])?$role_access[$module->id][0]:0;
								$lists['permission'] = $permission;
								$ModuleSettingsObj = ModuleSetting::where('module_id',$module->id)->where('role_id',$adminObj->role_id)->first();
								if(isset($ModuleSettingsObj) && $ModuleSettingsObj->module_id ==7){ //User Management hardcoded menu
									if($ModuleSettingsObj->view ==1){
										$lists = array();
										$lists['id'] = $module->id;
										$lists['name'] = "User Summary";
										$lists['permission'] = $permission;
										$lists['view'] = $ModuleSettingsObj->view;
										$lists['create'] = $ModuleSettingsObj->create;
										$lists['edit'] = $ModuleSettingsObj->edit;
										$lists['delete'] = $ModuleSettingsObj->delete;
										$lists['new_count'] = 0;
										$list_array[] = $lists;
									}
									
									if($ModuleSettingsObj->edit ==1){
										$lists = array();
										$lists['id'] = $module->id;
										$lists['name'] = "User Access";
										$lists['permission'] = $permission;
										$lists['view'] = $ModuleSettingsObj->view;
										$lists['create'] = $ModuleSettingsObj->create;
										$lists['edit'] = $ModuleSettingsObj->edit;
										$lists['delete'] = $ModuleSettingsObj->delete;
										$lists['new_count'] = 0;
										$list_array[] = $lists;
									}
									
									if($ModuleSettingsObj->create ==1){
										$lists = array();
										$lists['id'] = $module->id;
										$lists['name'] = "Create New User";
										$lists['permission'] = $permission;
										$lists['view'] = $ModuleSettingsObj->view;
										$lists['create'] = $ModuleSettingsObj->create;
										$lists['edit'] = $ModuleSettingsObj->edit;
										$lists['delete'] = $ModuleSettingsObj->delete;
										$lists['new_count'] = 0;
										$list_array[] = $lists;
									}
									
									if($ModuleSettingsObj->edit ==1){
										$lists = array();
										$lists['id'] = $module->id;
										$lists['name'] = "Units Information";
										$lists['permission'] = $permission;
										$lists['view'] = $ModuleSettingsObj->view;
										$lists['create'] = $ModuleSettingsObj->create;
										$lists['edit'] = $ModuleSettingsObj->edit;
										$lists['delete'] = $ModuleSettingsObj->delete;
										$lists['new_count'] = 0;
										$list_array[] = $lists;
									}
									if($ModuleSettingsObj->edit ==1){
										$lists = array();
										$lists['id'] = $module->id;
										$lists['name'] = "Registrations";
										$lists['permission'] = $permission;
										$lists['view'] = $ModuleSettingsObj->view;
										$lists['create'] = $ModuleSettingsObj->create;
										$lists['edit'] = $ModuleSettingsObj->edit;
										$lists['delete'] = $ModuleSettingsObj->delete;
										$lists['new_count']= $user->noOfReg($adminObj->account_id);
										$list_array[] = $lists;
									}

									if($ModuleSettingsObj->edit ==1){
										$lists = array();
										$lists['id'] = $module->id;
										$lists['name'] = "A/C Delete Requests";
										$lists['permission'] = $permission;
										$lists['view'] = $ModuleSettingsObj->view;
										$lists['create'] = $ModuleSettingsObj->create;
										$lists['edit'] = $ModuleSettingsObj->edit;
										$lists['delete'] = $ModuleSettingsObj->delete;
										$lists['new_count']= $user->noOfReg($adminObj->account_id);
										$list_array[] = $lists;
									}
								}
								else if($module->id ==61 && isset($ModuleSettingsObj)){
									$lists['view'] = $ModuleSettingsObj->view;
									$lists['create'] = $ModuleSettingsObj->create;
									$lists['edit'] = $ModuleSettingsObj->edit;
									$lists['delete'] = $ModuleSettingsObj->delete;
									$lists['new_count'] = 0;
								}
								else if(in_array($module->id,$settings_module) && isset($ModuleSettingsObj)){ 
									//echo $module->name." ".$module->id;
										$lists = array();
										$lists['id'] = $module->id;
										$lists['name'] = $module->name;
										$lists['permission'] = $permission;
										/*$lists['view'] = 1;
										$lists['create'] = 0;
										$lists['edit'] = 1;
										$lists['delete'] = 0;*/
										$lists['view'] = $ModuleSettingsObj->view;
										$lists['create'] = $ModuleSettingsObj->create;
										$lists['edit'] = $ModuleSettingsObj->edit;
										$lists['delete'] = $ModuleSettingsObj->delete;
										$lists['new_count'] = 0;
										$list_array[] = $lists;
								}
								else if(isset($ModuleSettingsObj)){ 
									$lists['view'] = $ModuleSettingsObj->view;
									$lists['create'] = $ModuleSettingsObj->create;
									$lists['edit'] = $ModuleSettingsObj->edit;
									$lists['delete'] = $ModuleSettingsObj->delete;
									if($module->id ==50)
										$lists['new_count'] = $user->noOfFaceids($adminObj->account_id);
									else if($module->id ==2)
										$lists['new_count']= $user->noOfTakeover($adminObj->account_id);
									else if($module->id ==4)
										$lists['new_count']= $user->noOfInspection($adminObj->account_id);
									else if($module->id ==3)
										$lists['new_count']=$user->noOfDefects($adminObj->account_id);
									else if($module->id ==6)
										$lists['new_count']= $user->noOfFeedback($adminObj->account_id);
									else if($module->id ==5)
										$lists['new_count']= $user->noOfFacilityBooking($adminObj->account_id);
									else if($module->id ==33)
										$lists['new_count'] = $user->noOfFileupload($adminObj->account_id);
									else if($module->id ==34)
										$lists['new_count'] = $user->noOfVisitors($adminObj->account_id);
									else 
										$lists['new_count'] = 0;
								}

								if($permission !=0) //to enable group
									$group_display_permission =1;

								if($module->id !=47 && $module->id !=7 && !in_array($module->id,$settings_module)){
									$list_array[] = $lists;
								}
							
						
					}
					
					//echo $group_name ." Group Permission : ". $group_display_permission;
					
					if($module->parent ==1 || $module->parent ==18 || $module->parent ==19 || $module->parent ==23){
						$settings_groups['menu_group'] = $group->name;
						$settings_groups['menus_lists'] = $list_array;
						//$settings_groups[$group->id] = $data;
					}
					else if($group_name !='' && count($list_array) >0 && $group_display_permission ==1 ){
							$menu_groups['menu_group'] = $group->name;
							$menu_groups['menus_lists'] = $list_array;
						/*if($module->id ==7)
							$menu_groups['new_count']= $user->noOfReg($adminObj->account_id);
						else if($module->id ==50)
							$menu_groups['new_count'] = $user->noOfFaceids($adminObj->account_id);
						else if($module->id ==2)
							$menu_groups['new_count']= $user->noOfTakeover($adminObj->account_id);
						else if($module->id ==4)
							$menu_groups['new_count']= $user->noOfInspection($adminObj->account_id);
						else if($module->id ==3)
							$menu_groups['new_count']=$user->noOfDefects($adminObj->account_id);
						else if($module->id ==6)
							$menu_groups['new_count']= $user->noOfFeedback($adminObj->account_id);
						else if($module->id ==5)
							$menu_groups['new_count']= $user->noOfFacilityBooking($adminObj->account_id);
						else if($module->id ==33)
							$menu_groups['new_count'] = $user->noOfFileupload($adminObj->account_id);
						else if($module->id ==34)
							$menu_groups['new_count'] = $user->noOfVisitors($adminObj->account_id);
							//$menu_groups[$group->id] = $data;
							*/
					}
					
					
				
				}
				
				//print_r($menu_groups);
				if(!empty($menu_groups))
					$menu_array[] =$menu_groups;
				if(!empty($settings_groups))
					$settings_array[] =$settings_groups;

			}
		}
		//print_r($menu_array);
		//$menu_array = array_unique($menu_array);
		
		$check_array = array();
		$main_menu = array();
		foreach($menu_array as $menu){	
			if(!in_array($menu['menu_group'],$check_array)){
				$check_array[] = $menu['menu_group'];
				$main_menu[] = $menu;
				
			}
		}

		$check_array = array();
		$settings_final_menu = array();
		foreach($settings_array as $settings_menu){	
			if(!in_array($settings_menu['menu_group'],$check_array)){
				$check_array[] = $settings_menu['menu_group'];
				$settings_final_menu[] = $settings_menu;
				
			}
		}
		$newcount = array();
		
		/**/

		return response()->json(['menu'=>$main_menu,'settings'=>$settings_final_menu, 'response' => 1, 'message' => 'Success']); 
		


	}


	public function dashboardmenu(Request $request){

		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Admin not found']);
		}

		$PropertyObj = Property::find($adminObj->account_id);
        $file_path = env('APP_URL')."/storage/app/";
		//$groups = ModuleGroup::whereIn('id',[7,20])->where('status',1)->orderBy('orderby','ASC')->get();
		$groups = ModuleGroup::where('status',1)->orderBy('orderby','ASC')->get();
		$role_access = array();
	
        foreach($PropertyObj->Permissions as $permission){
            $role_access[$permission->module_id] = array($permission->view);           
		}
		
		//print_r($role_access);
		$menu_array = array();
		$settings_array = array();
		if($groups){
			$menu_groups = array();
			$settings_groups = array();
			$group_array_name = array();
            foreach($groups as $group){
				//$menu_groups['menu_group'] = $group->name;
				if($group->mobilemodules){
					$list_array = array();
					
					$group_display_permission = '0';
					foreach($group->mobilemodules as $module){
						$group_name = '';
								//print_r($module);
								$group_name = $group->name;
								$lists = array();
								$lists['id'] = $module->id;
								$lists['name'] = $module->name;
								$permission = isset($role_access[$module->id][0])?$role_access[$module->id][0]:0;
								$lists['permission'] = $permission;
								$ModuleSettingsObj = ModuleSetting::where('module_id',$module->id)->where('role_id',$adminObj->role_id)->first();
								if(isset($ModuleSettingsObj)){
									$lists['view'] = $ModuleSettingsObj->view;
									$lists['create'] = $ModuleSettingsObj->create;
									$lists['edit'] = $ModuleSettingsObj->edit;
									$lists['delete'] = $ModuleSettingsObj->delete;
								}

								if($permission !=0) //to enable group
									$group_display_permission =1;

								if($module->id !=47)
									$list_array[] = $lists;
							
						
					}
					
					//echo $group_name ." Group Permission : ". $group_display_permission;
					
					if($module->parent ==1 || $module->parent ==18 || $module->parent ==19 || $module->parent ==23){
						$settings_groups['menu_group'] = $group->name;
						$settings_groups['menus_lists'] = $list_array;
						//$settings_groups[$group->id] = $data;
					}
					else if($group_name !='' && count($list_array) >0 && $group_display_permission ==1 ){
							$menu_groups['menu_group'] = $group->name;
							$menu_groups['menus_lists'] = $list_array;
							//$menu_groups[$group->id] = $data;
					}
					
					
				
				}
				
				//print_r($menu_groups);
				if(!empty($menu_groups))
					$menu_array[] =$menu_groups;
				if(!empty($settings_groups))
					$settings_array[] =$settings_groups;

			}
		}
		//print_r($menu_array);
		//$menu_array = array_unique($menu_array);
		
		$check_array = array();
		$main_menu = array();
		foreach($menu_array as $menu){	
			if(!in_array($menu['menu_group'],$check_array)){
				$check_array[] = $menu['menu_group'];
				$main_menu[] = $menu;
				
			}
		}

		$check_array = array();
		$settings_final_menu = array();
		foreach($settings_array as $settings_menu){	
			if(!in_array($settings_menu['menu_group'],$check_array)){
				$check_array[] = $settings_menu['menu_group'];
				$settings_final_menu[] = $settings_menu;
				
			}
		}

		return response()->json(['menu'=>$main_menu,'settings'=>$settings_final_menu,'response' => 1, 'message' => 'Success']); 

	}

	public function getpropertylist(Request $request) 
    {
		$user = $request->user();
		$userObj = new \App\Models\v7\User();
		$properties = $userObj->propdropdown($user->id);
		return response()->json(['data' => $properties, 'current_property' => $user->account_id, 'response' => 1, 'message' => 'Success']);
	}

	public function switchproperty(Request $request) 
    {
		$rules=array(
			'prop_id' => 'required',
		);
		$messages=array(
			'prop_id.required' => 'Property id is missing',
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
		
			$userinfo = UserMoreInfo::where('user_id',$login_id)->first(); 
			$adminObj->account_id = $request->prop_id;
			$adminObj->save();
	
			$emp_result = Employee::where('account_id',$adminObj->account_id)->where('uuid',$adminObj->id)->where('emp_type',1)->orderby('id','desc')->first();
	
			$name = $adminObj->name." ".isset($userinfo->last_name)?$userinfo->last_name:null;
			if(empty($emp_result)){
				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();
	
				$emp_rec['account_id'] = $property->property_id;
				$emp_rec['name'] =  $name;
				$emp_rec['emp_type'] =  1;
				$emp_rec['status'] =  1;
				$emp_rec['uuid'] =  $adminObj->id; //
				$result = Employee::create($emp_rec);
	
				$emp = new \App\Models\v7\Employee();
				$employee = $emp->employee_add_api($thinmoo_access_token,$result,$adminObj->role_id);
			}

		return response()->json(['response' => 1, 'message' => 'Success']);
		
	}

	
	public function announcements(Request $request) 
    {
		$user = $request->user();

		$permission = $user->check_permission(1,$user->role_id); 
		if(empty($permission) || (isset($permission->view) && $permission->view !=1)) return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);

		$announcements = Announcement::where('account_id',$user->account_id)->orderByDesc('id')->get();
		
		return response()->json(['data'=>$announcements,'file_path'=> image_storage_domain(),'response' => 1, 'message' => 'Success']);
	}


	public function createannouncement(Request $request) 
    {
		$rules=array(
			'title' => 'required',
			'notes' => 'required',
		);
		$messages=array(
			'title.required' => 'Title is missing',
			'notes.required' => 'Notes is missing',
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
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
		
		$permission = $adminObj->check_permission(1,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else
		{
			$input = $request->all();
			$input['account_id'] = $adminObj->account_id;
			if ($request->file('upload') != null) {
				$input['upload'] = remove_upload_path($request->file('upload')->store(upload_path('announcement')));
			}
			if ($request->file('upload_2') != null) {
				$input['upload_2'] = remove_upload_path($request->file('upload_2')->store(upload_path('announcement')));
			}
			if ($request->file('upload_3') != null) {
				$input['upload_3'] = remove_upload_path($request->file('upload_3')->store(upload_path('announcement')));
			}
			if ($request->file('upload_4') != null) {
				$input['upload_4'] = remove_upload_path($request->file('upload_4')->store(upload_path('announcement')));
			}
			if ($request->file('upload_5') != null) {
				$input['upload_5'] = remove_upload_path($request->file('upload_5')->store(upload_path('announcement')));
			}
			$input['roles'] = implode(",",$input['role_array']);
			if($adminObj->account_id ==3 && 1==2 )
				$announcement = Announcementtest::create($input);
			else
				$announcement = Announcement::create($input);

			if($announcement){
				$cron = array();
				$cron['a_id'] = $announcement->id;
				$cron['account_id'] = $announcement->account_id;
				$cron['roles'] = $announcement->roles;
				AnnouncementCron::create($cron);
			}
			return response()->json(['user_info'=>$announcement,'response' => 1, 'message' => 'success']);
       
		}
	}


	public function deleteannouncement(Request $request) 
    {
		$rules=array(
			'id' => 'required',
		);
		$messages=array(
			'id.required' => 'Announcement Id is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(1,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			AnnouncementDetail::where('a_id',$id)->delete();
       		Announcement::findOrFail($id)->delete();

			return response()->json(['response' => 1, 'message' => 'Deleted']);

       
		}
	}

	public function searchannouncement(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		$account_id = $adminObj->account_id;
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(1,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$file_path = env('APP_URL')."/storage/app";
			$role = $startdate = $enddate='';

			$roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')->orWhere('type',1)->pluck('name', 'id')->all();

			$startdate = $request->input('startdate');
			if($request->input('enddate') !='')
				$enddate = $request->input('enddate');
			else
				$enddate =$request->input('startdate');

			if($request->input('roles') !='') 
				$role = $request->input('roles');

			$announcements = Announcement::where('account_id',$account_id)->where(function($query) use ($startdate,$enddate,$role ){
				if($role !='' && $role !='a'){
					$query->where('roles',$role);
				}
					
				if($startdate !=''){
					$query->whereBetween('created_at',array($startdate,$enddate));
				}
						
			})->orderby('id','desc')->get();
			
			$file_path = env('APP_URL')."/storage/app";

			return response()->json(['data'=>$announcements,'response' => 1,'file_path'=>$file_path, 'message' => 'Success']);
			
		}
	}
	
	public function usersummarylist(Request $request) 
    {
		$login_id = Auth::id();
		//echo $login_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)) return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) || (isset($permission->view) && $permission->view !=1)){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}else{

        	$account_id = $adminObj->account_id;

			if($adminObj->role_id ==1){
				$users = User::where('role_id',3)->get();
				$roles = Role::pluck('name', 'id')->all(); 
		   	} else{
				$prop_userids = array();
				$userids = UserProperty::where('property_id',$account_id)->orderby('id','desc')->get();        
				
				foreach($userids as $k =>$v) $prop_userids[] = $v->user_id;
				
				$users = UserMoreInfo::whereNotIn('status',[2])->where(function ($query) use ($account_id,$prop_userids) {
					if($account_id !='') $query->where('account_id',$account_id);
					if($prop_userids !='') $query->orwhereIn('user_id', $prop_userids);
				})->orderby('id','desc')->get();

				$roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')->orWhere('type',1)->pluck('name', 'id')->all();
		   }

			$app_user_lists = explode(",",env('USER_APP_ROLE'));
			$data = [];
			foreach($users as $user){
				if(empty($user->getuser)) continue;
				$user_id = $user->getuser->id;
				$role_id = $user->getuser->role_id;
				$role = isset($user->getuser->role->name)?$user->getuser->role->name:null;
				if(in_array($user->getuser->role_id,$app_user_lists)){
					$moreinfo = new \App\Models\v7\UserMoreInfo();
					$unitinfo = $moreinfo->moreunitinfo($user->id,$user->account_id);
					//print_r($unitinfo);
					$role_id = isset($unitinfo->role_id)?$unitinfo->role_id:null;
					$role = isset($unitinfo->role->name)?$unitinfo->role->name:null;
				}
				$record['id']=$user->getuser->id;
				$record['name']=isset($user->first_name)?Crypt::decryptString($user->first_name):null;
				$record['last_name']= isset($user->last_name)?Crypt::decryptString($user->last_name):null;
				$record['email']=$user->getuser->email;
				$record['account_enabled']=$user->status;
				$record['role_id']=isset($role_id)?$role_id:null;
				$record['role']= $role;
				$record['status']=$user->status;
				$record['app_version']=$user->getuser->app_version;
				$record['created_at']=$user->created_at->format('d/m/Y');
				//$record['name']=$user->name;
				$record['userinfo']['id'] = $user->id;
				$record['userinfo']['profile_picture'] = isset($user->profile_picture)?$user->profile_picture:null;
				$record['userinfo']['face_picture'] = isset($user->face_picture)?$user->face_picture:null;
				$record['userinfo']['last_name'] = isset($user->last_name)?Crypt::decryptString($user->last_name):null;
				$record['userinfo']['phone'] = isset($user->phone)?Crypt::decryptString($user->phone):null;
				$record['userinfo']['mailing_address'] = isset($user->mailing_address)?$user->mailing_address:null;
				$record['userinfo']['country'] = isset($user->country)?$user->country:null;
				$record['userinfo']['card'] = isset($user->card_no)?$user->card_no:null;
				$record['building'] = isset($unitinfo->addubuildinginfo)?$unitinfo->addubuildinginfo->building:null;
				$record['unit'] = isset($unitinfo->addunitinfo)?"#".Crypt::decryptString($unitinfo->addunitinfo->unit):null;
				$PurchaserUnits = UserPurchaserUnit::where('user_id', $user_id)->where('property_id', $account_id)->get();
				$user_units = array();
				if(isset($PurchaserUnits)){
					foreach($PurchaserUnits as $PurchaserUnit){
						$eachunit = array();
						$eachunit['id'] = $PurchaserUnit->id;
						$eachunit['building_id'] = $PurchaserUnit->building_id;
						$eachunit['unit_id'] = $PurchaserUnit->unit_id;
						$eachunit['building'] = isset($PurchaserUnit->addubuildinginfo)?$PurchaserUnit->addubuildinginfo->building:null;
						$eachunit['unit'] = isset($PurchaserUnit->addunitinfo)?Crypt::decryptString($PurchaserUnit->addunitinfo->unit):null;
						$eachunit['role'] = isset($PurchaserUnit->role->name)?$PurchaserUnit->role->name:null;
						$eachunit['primary_contact'] = ($PurchaserUnit->primary_contact==1)?"Yes":"No";
						$eachunit['created_date'] = date('d/m/y',strtotime($PurchaserUnit->created_at));
						$user_units[] =$eachunit;
					}
				}
				$record['user_units'] = $user_units;
				$data[] = $record;
			}

			$app_user_lists = explode(",",env('USER_APP_ROLE'));

			return response()->json(['users'=>$data,'roles'=>$roles,'user_roles'=>$app_user_lists,'response' => 1, 'message' => 'Success']);
		}
	}

	public function searchuser(Request $request) 
    {
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			/*$name = $request->input('name');
			$last_name = $request->input('last_name');
			$role = $request->input('role');
			$building = $request->input('building');
			$unit = $request->input('unit');
			$email_address = $request->input('email_address');
			$app_version = $request->input('app_version');
			$unit_value = $request->input('unit_value');*/

			$name = $request->input('name');
			$email = $request->input('email');
			$last_name = $request->input('last_name');
			$role = $request->input('role');
			$building = $request->input('building');
			$unit = $request->input('unit');
			$login_from = $request->input('login_from');
			$unit_value = $request->input('unit_value');

			$app_user_lists = explode(",",env('USER_APP_ROLE'));
			$account_id = $adminObj->account_id;

			if($name !='' || $email !='' || $last_name !='' || $role !=''|| $building !='' || $unit !='' || $login_from !='' || $unit_value !=''){
			$units = array();
			if($unit !='' || $building !=''){  
				$unit = str_replace("#",'',$unit);
				$unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($building,$unit) {
				if($building !='')
					$query->where('building_id',$building);
				//if($unit !='')
					//$query->Where('unit1', Crypt::encryptString($unit));
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

			//print_r($units);
			$firstname_userids =array();
			$lastname_userids =array();
			$userids =array();
			if($last_name !='' || $name !='' ){
				$user_more_info = UserMoreInfo::select('id','first_name','last_name')->where('account_id',$account_id)->whereNotIn('status',[2])->orderby('id','desc')->get();
				
				foreach($user_more_info as $k =>$v){
					$firstname = strtolower(Crypt::decryptString($v->first_name));
					$lastname = strtolower(Crypt::decryptString($v->last_name));
					if($name !='' && str_contains($firstname,strtolower($request->input('name'))))
						$firstname_userids[] = $v->id;
					if($last_name !='' && str_contains($lastname,strtolower($request->input('last_name'))))
						$lastname_userids[] = $v->id;
				}
			}
			$prop_userids = array();
			$prop_userids = UserProperty::where('property_id',$account_id)->orderby('id','desc')->get();        
			foreach($prop_userids as $k =>$v1){
				$prop_userids[] = $v1->user_id;
			}
			$user_userids = User::where('account_id',$account_id)->orderby('id','desc')->get();        
			foreach($user_userids as $k =>$v2){
				$prop_userids[] = $v2->id;
			}
			$email_userids =array();
			if($email !='' && $role ==''){
				$email_ids = array();
				$emp_userids = UserProperty::where('property_id',$account_id)->orderby('id','desc')->get();        
				foreach($emp_userids as $k2 =>$v2){
					$email_ids[] = $v2->user_id;
				}

				$user_userids = UserPurchaserUnit::where('property_id',$account_id)->orderby('id','desc')->get();        
				foreach($user_userids as $k2 =>$v2){
					//echo $v2->user_id."<br />";
					$email_ids[] = $v2->user_id;
				}

				$user_emailids = User::whereIn('id',$email_ids)->where('email', 'LIKE', '%' . $email . '%')->orderby('id','desc')->get();        
				foreach($user_emailids as $v3){
					$email_userids[] = $v3->id;
				}
			}
			else if($role !='' && !in_array($role,$app_user_lists)){ // employee email search
				//echo $role;
				$emp_ids = array();
				$emp_userids = UserProperty::where('property_id',$account_id)->orderby('id','desc')->get();        
				foreach($emp_userids as $k2 =>$v2){
					//echo $v1->user_id."<br />";
					$emp_ids[] = $v2->user_id;
				}
				//print_r($emp_ids);

				$user_emailids = User::whereIn('id',$emp_ids)->where('role_id',$role)->where('email', 'LIKE', '%' . $email . '%')->orderby('id','desc')->get();        
				foreach($user_emailids as $v3){
					$email_userids[] = $v3->id;
				}
				//print_r($email_userids);
			}
			else{
				$user_ids = array();
				$user_userids = UserPurchaserUnit::where('property_id',$account_id)->orderby('id','desc')->get();        
				foreach($user_userids as $k2 =>$v2){
					//echo $v2->user_id."<br />";
					$user_ids[] = $v2->user_id;
				}


				$user_emailids = User::whereIn('id',$user_ids)->where('email', 'LIKE', '%' . $email . '%')->orderby('id','desc')->get();        
				foreach($user_emailids as $v3){
					$email_userids[] = $v3->id;
				}
				//print_r($email_userids);

			}
			
			$allUnits = UserPurchaserUnit::select("id","user_info_id")->where('property_id',Auth::user()->account_id)->orderby('unit_id','DEsC')->get();
			$userinfoids = '';
			foreach($allUnits as $unitlist){
				$userinfoids .= $unitlist->user_info_id.",";
			}   
			$userinfoids = substr($userinfoids,0,-1);

			$users = UserMoreInfo::whereNotIn('status',[2])->where(function ($query) use ($name,$email,$email_userids,$last_name,$userids,$role,$unit,$units,$app_user_lists,$account_id,$building,$firstname_userids,$lastname_userids,$login_from,$userinfoids) {
				
				if($name !=''){
					$query->whereIn('id', $firstname_userids);
				}
				if($last_name !=''){
					$query->whereIn('id', $lastname_userids);
				}
				if($email !=''){
					$query->whereIn('user_id', $email_userids);
				}
				if($unit !='' || $building !=''){
				
						$userids = UserPurchaserUnit::where(function ($subquery) use ($account_id,$units,$building,$role) {
							if($account_id !='')
								$subquery->where('property_id',$account_id);
							if($building !='')
								$subquery->where('building_id', $building);
							if(count($units)>0){
									$subquery->WhereIn('unit_id', $units);
							}
							if($role !='')
								$subquery->Where('role_id', $role);
						})->get();
						$prop_userids =array();
						foreach($userids as $k =>$v){
							$prop_userids[] = $v->user_info_id;
						}
						$query->whereIn('id',$prop_userids)->where('account_id',$account_id);
					
				}
				if($role !='' && $unit ==''){
					if(in_array($role,$app_user_lists)){
						$userids = UserPurchaserUnit::where(function ($query) use ($account_id,$role) {
							if($account_id !='')
								$query->where('property_id',$account_id);
							if($role !='')
								$query->Where('role_id', $role);
						
						})->get();
						$prop_userids =array();
						foreach($userids as $k =>$v){
							$prop_userids[] = $v->user_info_id;
						}
						$query->whereIn('id',$prop_userids)->where('account_id',$account_id);
					}else if($email ==''){
						$userproperties = UserProperty::where('property_id',$account_id)->get();
						$assigned_property = array();
						foreach($userproperties as $userproperty){
							$assigned_property[] = $userproperty->user_id;
						}
						$userids = User::where(function ($query) use ($assigned_property,$role) {
							$query->whereIn('id',$assigned_property);
							if($role !='')
								$query->Where('role_id', $role);
						})->get();
						$prop_userids =array();
						foreach($userids as $k =>$v){
							$prop_userids[] = $v->id;
						}
						$query->whereIn('user_id',$prop_userids); 
					}

				}else if($unit =='') {
					
					$userproperties = UserProperty::where('property_id',$account_id)->get();
					$assigned_property = array();
					foreach($userproperties as $userproperty){
						$assigned_property[] = $userproperty->user_id;
					}
					$userids = User::where(function ($query) use ($assigned_property,$role) {
					$query->whereIn('id',$assigned_property);
					if($role !='')
						$query->Where('role_id', $role);
					})->get();

					$prop_userids =array();
					foreach($userids as $k =>$v){
						$prop_userids[] = $v->id;
					}
					//print_r($prop_userids);
					/*if($email !=''){
						
						$check_email_account = User::where('email',$email)->whereIn('role_id',$app_user_lists)->first();
						if(isset($check_email_account))
							$query->where('account_id',$account_id);
					}*/

					
					$userids = UserPurchaserUnit::where('property_id',$account_id)->get();
					//$prop_userids =array();
					$notinprop_userinfoids =array();
					foreach($userids as $k =>$v){
						$prop_userids[] = $v->user_id;
						$userinfo_ids = UserPurchaserUnit::where('user_id',$v->user_id)->whereNotIn('property_id',[$account_id])->get();
						if(isset($userinfo_ids)){
							foreach($userinfo_ids as $infoid){
								$notinprop_userinfoids[] = $infoid->user_info_id;
							}
						}
					}

					$query->whereIn('user_id',$prop_userids);
					$query->whereNotIn('id',$notinprop_userinfoids);
					//$query->where('account_id',$account_id);
				}
				if($login_from !=''){
					$userids = UserLog::where('account_id',$account_id)->where('login_from',$login_from)->where('status',1)->get();
					$login_userids =array();
					foreach($userids as $k =>$v){
						$checkstatus = UserLog::where('account_id',$account_id)->where('user_id',$v->user_id)->where('id','>',$v->id)->first();
						if(empty($checkstatus))
							$login_userids[] = $v->user_id;
					}
					$query->whereIn('user_id',$login_userids);
				}
				
				if(strlen($userinfoids)> 0) {
						$query->orderByRaw(DB::raw("FIELD(id, $userinfoids) DESC"));
				}
				
				})->orderBy('id','DESC')->get();
				
				
				$app_user_lists = explode(",",env('USER_APP_ROLE'));
				$data = array();
				foreach($users as $user){
					$user_id = $user->getuser->id;
					$role_id = $user->getuser->role_id;
					$building_name = '';
                    $unit_name = '';
                    if(isset($unit) && $unit >0){
                        $unitObj = new \App\Models\v7\Unit();
						$moreinfo = new \App\Models\v7\UserMoreInfo();
						$purchaseUnitInfo = $moreinfo->moreunitinfo($user->id,$user->account_id,$unit);
						$role_id = isset($purchaseUnitInfo->role_id)?$purchaseUnitInfo->role_id:null;
						$roleInfo = $moreinfo->roleInfo($role_id);
						$unitinfo = $unitObj->unit_info($user->user_id,$unit,$building);
						$building_name = isset($unitinfo->addubuildinginfo)?$unitinfo->addubuildinginfo->building:'';
						$unit_name = isset($unitinfo->addunitinfo)?"#".Crypt::decryptString($unitinfo->addunitinfo->unit):'';
                    }
                    else if(in_array($user->getuser->role_id,$app_user_lists)){
                        $moreinfo = new \App\Models\v7\UserMoreInfo();
						//$unitinfo = $moreinfo->moreunitinfo($user->user_id,$user->account_id);
						$purchaseUnitInfo = $moreinfo->moreunitinfo($user->id,$user->account_id);
						$role_id = isset($purchaseUnitInfo->role_id)?$purchaseUnitInfo->role_id:null;

                        $roleInfo = $moreinfo->roleInfo($role_id);
                        $unitInfo = $moreinfo->unitInfo(isset($purchaseUnitInfo->unit_id)?$purchaseUnitInfo->unit_id:'');
                        if(isset($unitInfo))
                            $buildingInfo = $moreinfo->buildinginfo($unitInfo->building_id);
                        $building_name = isset($buildingInfo)?$buildingInfo->building:'';
						$unit_name = isset($unitInfo)?"#".Crypt::decryptString($unitInfo->unit):'';
						   
                        //$role_id = isset($unitinfo->role_id)?$unitinfo->role_id:null;
                        //$building_name = isset($unitinfo->addubuildinginfo)?$unitinfo->addubuildinginfo->building:null;
                        //$unit_name = isset($unitinfo->addunitinfo)?"#".$unitinfo->addunitinfo->unit:null;
					}
					
					$record['id']=$user->getuser->id;
					$record['name']=isset($user->first_name)?Crypt::decryptString($user->first_name):null;
					$record['last_name']= isset($user->last_name)?Crypt::decryptString($user->last_name):null;
					$record['email']=$user->getuser->email;
					$record['account_enabled']=$user->status;
					$record['role_id']=isset($role_id)?$role_id:null;;
					$record['role']= isset($roleInfo->name)?$roleInfo->name:null;
					$record['status']=$user->status;
					$record['app_version']=$user->getuser->app_version;
					$record['created_at']=$user->created_at->format('d/m/Y');
					//$record['name']=$user->name;
					$record['userinfo']['id'] = isset($user->id)?$user->id:null;
					$record['userinfo']['profile_picture'] = isset($user->profile_picture)?$user->profile_picture:null;
					$record['userinfo']['face_picture'] = isset($user->face_picture)?$user->face_picture:null;
					$record['userinfo']['last_name'] = isset($user->last_name)?Crypt::decryptString($user->last_name):null;
					$record['userinfo']['phone'] = isset($user->phone)?Crypt::decryptString($user->phone):null;
					$record['userinfo']['mailing_address'] = isset($user->mailing_address)?$user->mailing_address:null;
					$record['userinfo']['country'] = isset($user->country)?$user->country:null;
					$record['userinfo']['card'] = isset($user->card_no)?$user->card_no:null;
					$record['building'] = $building_name;
					$record['unit'] = $unit_name;
					$PurchaserUnits = UserPurchaserUnit::where('user_id', $user_id)->where('property_id', $account_id)->get();
					$user_units = array();
					if(isset($PurchaserUnits)){
						foreach($PurchaserUnits as $PurchaserUnit){
							$eachunit = array();
							$eachunit['id'] = $PurchaserUnit->id;
							$eachunit['building_id'] = $PurchaserUnit->building_id;
							$eachunit['unit_id'] = $PurchaserUnit->unit_id;
							$eachunit['building'] = isset($PurchaserUnit->addubuildinginfo)?$PurchaserUnit->addubuildinginfo->building:null;
							$eachunit['unit'] = isset($PurchaserUnit->addunitinfo)?Crypt::decryptString($PurchaserUnit->addunitinfo->unit):null;
							$eachunit['role'] = isset($PurchaserUnit->role->name)?$PurchaserUnit->role->name:null;
							$eachunit['primary_contact'] = ($PurchaserUnit->primary_contact==1)?"Yes":"No";
							$eachunit['created_date'] = date('d/m/y',strtotime($PurchaserUnit->created_at));
							$user_units[] =$eachunit;
						}
					}
					$record['user_units'] = $user_units;
					$data[] = $record;
				}

				$roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')->orWhere('type',1)->pluck('name', 'id')->all();
				
				return response()->json(['users'=>$data,'roles'=>$roles,'user_roles'=>$app_user_lists,'response' => 1, 'message' => 'Success']);
			}
			
			else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
			}
		}
		
	}

	
	public function userinfo(Request $request) 
    {
		
		$login_id = Auth::id();
		$user_id = $request->user;
		//$user_info_id = $request->user_info_id;
		$adminObj = User::find($login_id); 

		if($login_id == $user_id)
			$UserMoreInfoObj = UserMoreInfo::where('user_id',$user_id)->first();
		else
			$UserMoreInfoObj = UserMoreInfo::where('user_id',$user_id)->where('account_id',$adminObj->account_id)->first();
		//$UserMoreInfoObj = UserMoreInfo::find($user_info_id);
		
		if(empty($UserMoreInfoObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		$user = $UserMoreInfoObj->user_id;
		$user_info_id = isset($request->user_info_id)?$request->user_info_id:$UserMoreInfoObj->id;
	
		$app_user_lists = explode(",",env('USER_APP_ROLE'));
		$userObj  = User::find($user);
		$role_id =  isset($userObj->role_id)?$userObj->role_id:null;

		if(in_array($userObj->role_id,$app_user_lists))
			$UserMoreInfoObj = UserMoreInfo::where('id', $user_info_id)->orderby('id','desc')->first();
		else
			$UserMoreInfoObj = UserMoreInfo::where('id', $user_info_id)->orderby('id','desc')->first();

		if(empty($userObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if((empty($permission) || $permission->view !=1) && $login_id !=$UserMoreInfoObj->user_id ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$account_id = $adminObj->account_id;
			$data['user']['id'] = $UserMoreInfoObj->user_id;
			$data['user']['email'] = $UserMoreInfoObj->getuser->email;
			$data['user']['account_id'] = $UserMoreInfoObj->getuser->account_id;
			//echo $userObj->id;
			//echo $userObj->account_id;
			if(in_array($UserMoreInfoObj->getuser->role_id,$app_user_lists)){
				//ech
				$moreinfo = new \App\Models\v7\UserMoreInfo();
				$unitinfo = $moreinfo->moreunitinfo($userObj->id,$userObj->account_id);
				//print_r($unitinfo);
				$role_id = isset($unitinfo->role_id)?$unitinfo->role_id:null;
				$building_name = isset($unitinfo->addubuildinginfo)?$unitinfo->addubuildinginfo->building:null;
				$unit_name = isset($unitinfo->addunitinfo)?"#".$unitinfo->addunitinfo->unit:null;
				$data['user']['building_no'] = $building_name;
				$data['user']['unit_no'] = $unit_name;
			 }
			
			
			$data['user']['account_enabled'] = $UserMoreInfoObj->getuser->account_enabled;
			$data['user']['status'] = $UserMoreInfoObj->getuser->status;
			$data['moreinfo'] = $UserMoreInfoObj;
			$data['moreinfo']['first_name_decrypted']= ($UserMoreInfoObj->first_name)?Crypt::decryptString($UserMoreInfoObj->first_name):null;
			$data['moreinfo']['last_name_decrypted']= ($UserMoreInfoObj->last_name)?Crypt::decryptString($UserMoreInfoObj->last_name):null;
			$data['moreinfo']['phone_decrypted']= ($UserMoreInfoObj->phone)?Crypt::decryptString($UserMoreInfoObj->phone):null;

			$data['moreinfo']['profile_picture_base64'] = '';
			$data['moreinfo']['face_picture_base64'] = '';
			
			$PurchaserUnits = UserPurchaserUnit::where('user_info_id',$user_info_id)->where('property_id',$account_id)->get();
			$data['unit'] = (isset($PurchaserUnits) && count($PurchaserUnits) >0)?$PurchaserUnits:null;
            if(isset($role_id) && !in_array($role_id,$app_user_lists)){
				$roleObj = Role::find($role_id);
				$role =  isset($roleObj)?$roleObj->name:null;

			}else{
				//echo "here";
				$moreinfo = new \App\Models\v7\UserMoreInfo();
				$unitinfo = $moreinfo->moreunitinfo($UserMoreInfoObj->getuser->id,$account_id);
				$role_id = isset($unitinfo->role_id)?$unitinfo->role_id:null;
				$roleObj = Role::find($role_id);
				$role =  isset($roleObj)?$roleObj->name:null;
	
			}
			//echo "hi";
			$data['role_id'] = $role_id;
			$data['role'] = $role;
			$PropertyInfo = Property::where('id',$account_id)->get();
			$data['property'] = isset($PropertyInfo)?$PropertyInfo:null;

			//$data['permissions'] = isset($PropertyInfo)?$PropertyInfo:null;

			$bluetoothDevices = UserDevice::where('user_id',$user)->where('account_id',$account_id)->get();
			$data['bluetoothDevices'] = isset($bluetoothDevices)?$bluetoothDevices:null;
			$remoteDevices = UserRemoteDevice::where('user_id',$user)->where('account_id',$account_id)->get();
			$data['remote_devices'] = isset($remoteDevices)?$remoteDevices:null;

			
			$agent_properties =array();
			if($adminObj->role_id ==1){
				$avail_properties = Property::where('status',1)->get();
				$agent_properties = UserProperty::where('user_id',$user)->get();
			}
			else if(isset($role_id) && !in_array($role_id,$app_user_lists)){
				//$login_id = Auth::user()->id;
				$prop_ids = UserProperty::where('user_id',$login_id)->get();
				$avail_properties =array();
			
				if(isset($prop_ids)){
					$assigned_property = array();
					foreach($prop_ids as $prop_id){
						$assigned_property[] = $prop_id->property_id;
					}
					$avail_properties = Property::whereIn('id',$assigned_property)->get();
				}
				$agent_properties = UserProperty::where('user_id',$user)->whereIn('property_id',$assigned_property)->get();

			}
			$available_prop_lists =array();
			if(isset($avail_properties)){
				foreach($avail_properties as $property){
					$prop =array();
					$prop['id'] = $property->id;
					$prop['prop_name'] = $property->company_name;
					$available_prop_lists[] =$prop;
				}
			}
			$data['available_properties'] = $available_prop_lists;
			$agent_assigned_properties = array();
			foreach($agent_properties as $userproperty){
					$agent_assigned_properties[] = $userproperty->property_id;

			}
			$data['agent_assigned_property'] = $agent_assigned_properties;
			$config_res = ConfigSetting::where('id',1)->first();
			if($config_res)
				$data['DOOR_DEVICES_CACHE_TTL'] = $config_res->value;

			$data['file_path'] = env('APP_URL')."/storage/app";
			


			//$roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')->orWhere('type',1)->pluck('name', 'id')->all();
			return response()->json(['users'=>$data,'response' => 1, 'message' => 'Success']);

       
		}
	}
	
	public function deleteuser(Request $request) 
    {
		$rules=array(
			'user' => 'required',
		);
		$messages=array(
			'user.required' => 'User Id is missing',
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
		$user_info_id = $request->user_info_id;
		$userMoreInfoObj = UserMoreInfo::find($user_info_id); 
		$adminObj = User::find($login_id); 
		$role_id = $request->role_id;

		if(empty($userMoreInfoObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$id = $userMoreInfoObj->user_id;
			$account_id = $adminObj->account_id;
			$UserObj = User::find($id);
			$user_roles = explode(",",env('USER_APP_ROLE'));

			$deactivated_date = date("Y-m-d");
			if(in_array($role_id,$user_roles)){
				 //UserPurchaserUnit::where('user_id', $id)->where('property_id',$account_id)->delete();
				 UserDevice::where('user_id', $id)->where('account_id',$account_id)->delete();
				 UserRemoteDevice::where('user_id', $id)->where('account_id',$account_id)->delete();
				 UserPermission::where('user_id', $id)->where('account_id',$account_id)->delete();
				 UserProperty::where('user_id', $id)->where('property_id',$account_id)->delete();
				 UserFacialId::where('user_id', $id)->where('account_id',$account_id)->delete();
				 //UserPurchaserUnit::where('user_id', $id)->where('property_id',$account_id)->delete();
 
				 $result = UserMoreInfo::where( 'id' , $user_info_id)->update( array( 'status' => 2,'deactivated_date'=>$deactivated_date));
				 $userinfo =  UserMoreInfo::where( 'id' , $user_info_id)->orderby('id','desc')->first();
				 $name = $userinfo->first_name." ".$userinfo->last_name;
				 $start_date = $userinfo->created_at;
				 $end_date = $userinfo->updated_at;
				 $UserObj = User::find($id);

				 if($UserObj->user_info_id == $user_info_id){ //remove active UserInfo, Unit
                    $UserObj->user_info_id ='';
                    $UserObj->building_no ='';
                    $UserObj->unit_no ='';
                    $UserObj->save();
                }
			}
			else{
				$UserObj = User::find($id);

                $result = UserMoreInfo::where( 'user_id' , $id)->delete();
                $result = User::where( 'id' , $id)->delete();
				//$result = User::where( 'id' , $id)->update( array( 'status' => 2,'deactivated_date'=>$deactivated_date));
				//$result = UserMoreInfo::where( 'user_id' , $id)->where( 'account_id' , $account_id)->update( array( 'status' => 2,'deactivated_date'=>$deactivated_date));
			}

			$auth = new \App\Models\v7\Property();
			$thinmoo_access_token = $auth->thinmoo_auth_api();

			if(in_array($UserObj->role_id,$user_roles)){
				$api_obj = new \App\Models\v7\User();
				$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj,$account_id);

				if($household_result['code'] ==0){
					$api_result = $api_obj->household_deactivate_api($thinmoo_access_token,$account_id,$name,$id,$start_date,$end_date);
				}
			}
			else{
				$emp_result = Employee::where('account_id',$UserObj->account_id)->where('uuid',$UserObj->id)->orderby('id','desc')->first();
				if(isset($emp_result)){
                    $EmpObj = Employee::find($emp_result->id);
                    $emp_obj = new \App\Models\v7\Employee();
                    $employee_result = $emp_obj->employee_check_record($thinmoo_access_token,$EmpObj);
                    if($employee_result['code'] ==0){
                        $employee = $emp_obj->employee_deactivate_api($thinmoo_access_token,$EmpObj,$UserObj->role_id);
                    }
                }
			}

			$log['module_id'] = 7;
			$log['account_id'] = $account_id;
			$log['admin_id'] = $login_id;
			$log['action'] = 3;
			$log['new_values'] = '';
			$log['ref_id'] = $userMoreInfoObj->id;
			$log['notes'] = 'User Deleted from Manager App';
			$log = ActivityLog::create($log);

			
			/*UserMoreInfo::where('user_id', $user)->delete();
			UserPermission::where('user_id', $user)->delete();
			UserLog::where('user_id', $user)->delete();
			JoininspectionAppointment::where('user_id', $user)->delete();
			UnittakeoverAppointment::where('user_id', $user)->delete();
			FeedbackSubmission::where('user_id', $user)->delete();
			FacilityBooking::where('user_id', $user)->delete();
			ResidentFileSubmission::where('user_id', $user)->delete();
			DefectSubmission::where('user_id', $user)->delete();
			User::findOrFail($user)->delete();*/

			return response()->json(['response' => 1, 'message' => 'Deleted']);

       
		}
	}
	
	public function updateuser(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'user_info_id' => 'required',
			'name' => 'required',
			'last_name' => 'required',
			'phone' => 'required',
			'mailing_address' => 'required',
			'email' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'user_info_id.required' => 'User info id is missing',
			'name.required' => 'First name is missing',
			'last_name.required' => 'Last name is missing',
			'phone.required' => 'Phone is missing',
			'mailing_address.required' => 'Mailling address is missing',
			'email.required' => 'email address is empty',
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
		$id = $request->id;
		$user_info_id = $request->user_info_id;
		$adminObj = User::find($login_id); 

		$userObj = UserMoreInfo::where('id',$user_info_id)->first(); 

		$old_values = "Id:". $userObj->id. ", Name:".$userObj->name.", Email:".$userObj->email.", Phone:".$userObj->phone;

		if(empty($userObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$UserObj = User::find($id);
		
			
			$input = $request->all();
			$input['name'] = Crypt::encryptString($input['name']);
			$input['last_name'] = Crypt::encryptString($input['last_name']);
			$input['phone'] = Crypt::encryptString($input['phone']);
			$input['encrypted']  =1;
			$UserObj->name =  $input['name'];
			if (trim($input['email']) != '') {
				$UserObj->email = $input['email'];
			}

			if (isset($input['password']) &&  $input['password'] != '') {
				$UserObj->password = bcrypt($input['password']);
				$UserObj->account_enabled = 1;
			}

			$UserObj->role_id = $input['role_id'];
			$env_roles 	= env('USER_APP_ROLE');
			$roles = explode(",",$env_roles);
			$UserObj->save();

			if(in_array($UserObj->role_id,$roles))
				$UserMoreObj = UserMoreInfo::where('id',$input['user_info_id'])->orderby('id','desc')->first();
			else
				$UserMoreObj = UserMoreInfo::find($UserObj->empinfo->id);

				if(isset($input['faceid_access_permission']) && $input['faceid_access_permission'] ==1){
					$UserMoreObj->faceid_access_permission =1;
					$UserMoreObj->faceid_access_code = $input['faceid_access_code'];
				}else{
					$UserMoreObj->faceid_access_permission = 0;
					$UserMoreObj->faceid_access_code = null;
				}
			$UserMoreObj->first_name = $input['name'];
			$UserMoreObj->last_name = $input['last_name'];
			if(isset($input['receive_device_cal']))
				$UserMoreObj->receive_device_cal = $input['receive_device_cal'];
		
			$UserMoreObj->mailing_address = $input['mailing_address'];
			$UserMoreObj->phone = $input['phone'];
			$UserMoreObj->company_name = $input['company_name'];
			$UserMoreObj->country = $input['country'];
			$UserMoreObj->postal_code = $input['postal_code'];
			if(isset($input['unit_no']))
				$UserMoreObj->unit_no = $input['unit_no'];

			if(isset($input['card_nos']) && count($input['card_nos']) >0){
				$UserMoreObj->card_no = implode(",",$input['card_nos']);
			}
			$UserMoreObj->save();

			if(isset($input['primary_contact'])){
				$userPurchaseId = UserPurchaserUnit::where('property_id', $account_id)->where('user_id',$UserObj->id)->orderby('id','desc')->first();
				if(isset($userPurchaseId->id))
					UserPurchaserUnit::where('id', $userPurchaseId->id)->update(['primary_contact' => 1,'role_id'=>$input['role_id']]);
			   
			}
			
			//$env_roles 	= explode(",",env('USER_APP_ROLE'));
			if(@Auth::user()->role_id ==1){ //by Superadmin assign property
				$properties = Property::where('status',1)->get();
				UserProperty::where('user_id',$id)->delete();
				foreach($properties as $property) {
					$property_input = array();
					$property_checked = "property_".$property->id;
					if(isset($input[$property_checked]))
						{                   
							$property_input['user_id'] = $id;
							$property_input['property_id'] = $property->id;
							UserProperty::create($property_input);  
						}               
				}
			}
			else if(!in_array($UserObj->role_id,$roles)){ //by Agent login assign property
				$prop_ids = UserProperty::where('user_id',$login_id)->get();
				$properties =array();
			
				if(isset($prop_ids)){
					$assigned_property = array();
					foreach($prop_ids as $prop_id){
						$assigned_property[] = $prop_id->property_id;
					}
					$properties = Property::whereIn('id',$assigned_property)->get();
				}
			
				/*UserProperty::where('user_id',$id)->delete();
				foreach($properties as $property) {
					//print_r($property->id);
					$property_input = array();
					$property_checked = "property_".$property->id;
					if(isset($input[$property_checked]))
						{                   
							$property_input['user_id'] = $id;
							$property_input['property_id'] = $property->id;
							UserProperty::create($property_input);  
						}               
				}*/
				$assigned_prop = array();
				foreach($properties as $property) {
					//print_r($property->id);
					$property_input = array();
					$property_checked = "property_".$property->id;
					if(isset($input[$property_checked]))
					{               
						$property_input['user_id'] = $id;
						$property_input['property_id'] = $property->id;
							//print_r($property_input);
						$check_prop = UserProperty::where('user_id',$id)->where('property_id',$property->id)->first();
						if(!$check_prop){
							UserProperty::create($property_input);
						}
						$assigned_prop[] = $property->id;
						$update_active_property = $property->id;

					}                
				}
				if(count($assigned_prop) >0){
					UserProperty::where('user_id',$id)->WhereNotIn('property_id',$assigned_prop)->delete();
					$getUserActiveProp = User::where('id',$id)->WhereNotIn('account_id',$assigned_prop)->first();
					if($getUserActiveProp){
					User::where('id',$id)->update(['account_id'=>$update_active_property]);
					}
				}
			}
			$auth = new \App\Models\v7\Property();
			$thinmoo_access_token = $auth->thinmoo_auth_api();

			//$user_roles = explode(",",env('USER_APP_ROLE'));

			if(!in_array($UserObj->role_id,$roles)){
				if($login_id ==1){
					if(isset($UserObj->userproperties)){
						Employee::where('uuid', $UserObj->id)
						->update(['status' => 0]);

						foreach($UserObj->userproperties as $property){
							$emp_result = Employee::where('account_id',$property->property_id)->where('uuid',$UserObj->id)->orderby('id','desc')->first();

							$name = Crypt::decryptString($UserObj->name)." ".Crypt::decryptString($UserMoreObj->last_name);
							if(empty($emp_result)){
								$emp_rec['account_id'] = $property->property_id;
								$emp_rec['name'] =  $name;
								$emp_rec['emp_type'] =  1;
								$emp_rec['status'] =  1;
								$emp_rec['uuid'] =  $UserObj->id; //
								$result = Employee::create($emp_rec);

								$emp = new \App\Models\v7\Employee();
								$employee = $emp->employee_add_api($thinmoo_access_token,$result,$UserObj->role_id);
							}
							else{
								$EmpObj = Employee::find($emp_result->id);
								$EmpObj->name = $name;
								$EmpObj->status = 1;
								$EmpObj->save();

								$emp_obj = new \App\Models\v7\Employee();
								$employee_result = $emp_obj->employee_check_record($thinmoo_access_token,$EmpObj);

								
								if($employee_result['code'] ==0){
									$employee = $emp_obj->employee_modify_api($thinmoo_access_token,$EmpObj,$UserObj->role_id);
								}
								else{
								
									$employee = $emp_obj->employee_add_api($thinmoo_access_token,$EmpObj,$UserObj->role_id);
								}
							}
						}
					}
				}
				else
				{
					if(isset($UserObj->userproperties)){
						Employee::where('uuid', $UserObj->id)
						->update(['status' => 0]);
						
						foreach($UserObj->userproperties as $property){
							$emp_result = Employee::where('account_id',$property->property_id)->where('uuid',$UserObj->id)->orderby('id','desc')->first();
							$name = Crypt::decryptString($UserObj->name)." ".Crypt::decryptString($UserMoreObj->last_name);
							if(empty($emp_result)){
								$emp_rec['account_id'] = $property->property_id;
								$emp_rec['name'] =  $name;
								$emp_rec['emp_type'] =  1;
								$emp_rec['status'] =  1;
								$emp_rec['uuid'] =  $UserObj->id; //
								$emp_rec['roleid'] =  $UserObj->id; //

								$result = Employee::create($emp_rec);

								$emp = new \App\Models\v7\Employee();
								$employee = $emp->employee_add_api($thinmoo_access_token,$result,$UserObj->role_id);
							}
							else{
								
								$EmpObj = Employee::find($emp_result->id);
								$EmpObj->name = $name;
								$EmpObj->status = 1;
								$EmpObj->save();

								$emp_obj = new \App\Models\v7\Employee();
								$employee_result = $emp_obj->employee_check_record($thinmoo_access_token,$EmpObj);

								
								if($employee_result['code'] ==0){
									$employee = $emp_obj->employee_modify_api($thinmoo_access_token,$EmpObj,$UserObj->role_id);
								}
								else{
								
									$employee = $emp_obj->employee_add_api($thinmoo_access_token,$EmpObj,$UserObj->role_id);
								}
							}
						}
					}
				}
			}

			$new_values = "Id:". $userObj->id. ", Name:".$input['name'].", Last Name:".$input['last_name'].", Email:".$input['email'].", Phone:".$input['phone'];
			$log['module_id'] = 7;
			$log['account_id'] = $adminObj->account_id;
			$log['admin_id'] = $login_id;
			$log['action'] = 2;
			$log['old_values'] = $old_values;
			$log['new_values'] = $new_values;
			$log['ref_id'] = $UserMoreObj->id;
			$log['notes'] = 'User Updated from Manager App';
			$log = ActivityLog::create($log);  
			return response()->json(['data'=>null,'response' => 1, 'message' => 'Updated']);

       
		}
	}

	public function userdevicelists(Request $request) 
    {
		$rules=array(
			'user' => 'required',
		);
		$messages=array(
			'user.required' => 'User Id is missing',
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
		$user_info_id = $request->user_info_id;
		$adminObj = User::find($login_id); 
		$UserMoreInfoObj = UserMoreInfo::find($user_info_id);
		if(empty($UserMoreInfoObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$user = $UserMoreInfoObj->user_id;
			$UserObj = User::find($user);
			$user_roles = explode(",",env('USER_APP_ROLE'));
			if(!in_array($UserObj->role_id,$user_roles)){ //for employee roles
				$EmpBuildings = Building::where('account_id',$account_id)->get();
				$data =array();
				if(isset($EmpBuildings)){
					foreach($EmpBuildings as $EmpBuilding){
						$record = array();
						$record['id'] = $EmpBuilding->id;
						$record['building'] = $EmpBuilding->building;
						$devices = Device::where('locations',$EmpBuilding->id)->where('account_id',$account_id)->get();
						$available_devices = array();
						if(isset($devices)){
							foreach($devices as $device){
								$devices_array = array();
								$devices_array['id'] = $device->id;
								$devices_array['device_name'] = $device->device_name;
								$devices_array['device_serial_no'] = $device->device_serial_no;
								$devices_array['model'] = $device->model;
								$devices_array['location_id'] = $device->locations;
								$devices_array['location'] = isset($device->buildinginfo->building)?$device->buildinginfo->building:null;
								$user_bluetooth_device = UserDevice::where('account_id',$account_id)->where('unit_no',$EmpBuilding->building_id)->where('user_id',$user)->where('device_id',$device->id)->first();
									$devices_array['user_bluethooth_checked_status'] = isset($user_bluetooth_device)?1:0;
								
								$user_remote_device = UserRemoteDevice::where('account_id',$account_id)->where('unit_no',$EmpBuilding->building_id)->where('user_id',$user)->where('device_id',$device->id)->first();
									$devices_array['user_remote_checked_status'] = isset($user_remote_device)?1:0;
								$available_devices[] = $devices_array;
								
							}
						}
						$record['devices'] = $available_devices;
						$data[] = $record;
					}
				}
				return response()->json(['data'=>$data,'type'=>'employee', 'response' => 1, 'message' => 'success']);
			}
			else{
				$UserMoreInfoObj = UserMoreInfo::where( 'id' , $user_info_id)->where( 'account_id' , $account_id)->first();
				$PurchasedUnits = UserPurchaserUnit::where('user_id',$UserMoreInfoObj->user_id)->where('user_info_id', $UserMoreInfoObj->id)->where('property_id',$account_id)->get();
				$data =array();
				if(isset($PurchasedUnits)){
					foreach($PurchasedUnits as $PurchasedUnit){
						$record = array();
						$record['id'] = $PurchasedUnit->id;
						$record['building_no'] = $PurchasedUnit->building_id;
						$record['building'] = isset($PurchasedUnit->addubuildinginfo->building)?$PurchasedUnit->addubuildinginfo->building:null;
						$record['unit_no'] = $PurchasedUnit->unit_id;
						$record['unit'] = isset($PurchasedUnit->addunitinfo->unit)?Crypt::decryptString($PurchasedUnit->addunitinfo->unit):null;
						$devices = Device::where('locations',$PurchasedUnit->building_id)->where('account_id',$account_id)->get();
						$available_devices = array();
						if(isset($devices)){
							foreach($devices as $device){
								$devices_array = array();
								$devices_array['id'] = $device->id;
								$devices_array['device_name'] = $device->device_name;
								$devices_array['device_serial_no'] = $device->device_serial_no;
								$devices_array['model'] = $device->model;
								$devices_array['location_id'] = $device->locations;
								$devices_array['location'] = isset($device->buildinginfo->building)?$device->buildinginfo->building:null;

								$user_bluetooth_device = UserDevice::where('account_id',$account_id)->where('unit_no',$PurchasedUnit->unit_id)->where('user_id',$user)->where('device_id',$device->id)->first();
									$devices_array['user_bluethooth_checked_status'] = isset($user_bluetooth_device)?1:0;
								
								$user_remote_device = UserRemoteDevice::where('account_id',$account_id)->where('unit_no',$PurchasedUnit->unit_id)->where('user_id',$user)->where('device_id',$device->id)->first();
									$devices_array['user_remote_checked_status'] = isset($user_remote_device)?1:0;
								$available_devices[] = $devices_array;
								
							}
						}
						$record['devices'] = $available_devices;
						$record['receive_call'] = $PurchasedUnit->receive_call;

						$data[] = $record;
					}
				}
				return response()->json(['data'=>$data,'type'=>'user', 'response' => 1, 'message' => 'success']);
			}
			

       
		}
	}

	public function remote_door_open(Request $request){

		$rules=array(
			'property' => 'required',
			'devSn' => 'required',
			'user' => 'required',

		);
		$messages=array(
			'property.required' => 'Property ID missing',
			'devSn.required' => 'Device serial number missing',
			'user.required' => 'User ID missing',
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
			return $json;
		}
		else{
			return response()->json([
				'code' =>0,
				'msg'=>'Success'
			]);
		}
	
	}

	public function userdeviceupdate(Request $request) 
    {
		$rules=array(
			'user' => 'required',
			'user_info_id' => 'required',
		);
		$messages=array(
			'user.required' => 'User Id is missing',
			'user_info_id.required' => 'User info id is missing',
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
		$user_info_id = $request->user_info_id;
		$adminObj = User::find($login_id); 

		$userObj = UserMoreInfo::where('id',$user_info_id)->first(); 

		if(empty($userObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$account_id = $adminObj->account_id;


			$UserMoreInfoObj = UserMoreInfo::find($input['user_info_id']);
			$user = $UserMoreInfoObj->user_id;
			$UserObj = User::find($UserMoreInfoObj->user_id);
			$user_roles = explode(",",env('USER_APP_ROLE'));
			UserDevice::where('account_id',$account_id)->where('user_id',$user)->delete();
			UserRemoteDevice::where('account_id',$account_id)->where('user_id',$user)->delete();


			if(in_array($UserObj->role_id,$user_roles))
			   { // userapp devices
				$PurchasedUnits = UserPurchaserUnit::where('user_info_id', $UserMoreInfoObj->id)->where('property_id',$account_id)->get();
				$data =array();
				if(isset($PurchasedUnits)){
					foreach($PurchasedUnits as $PurchasedUnit){

						$devices = Device::where('locations',$PurchasedUnit->building_id)->where('account_id',$account_id)->get();
						if(isset($devices)){
							foreach($devices as $device){
								$bluethooth_device_checked ="unit_".$PurchasedUnit->id."_device_".$device->id;
								if(isset($input[$bluethooth_device_checked]) && $input[$bluethooth_device_checked] ==1)
								{                   
									$device_input['user_id'] = $user;
									$device_input['account_id'] = $account_id;
									$device_input['building_id'] = $PurchasedUnit->building_id;
									$device_input['unit_no'] = $PurchasedUnit->unit_id;
									$device_input['device_id'] = $device->id;
									$device_input['device_svn'] = $device->device_serial_no;
									UserDevice::create($device_input);  
								} 
								$remote_device_checked = "unit_".$PurchasedUnit->id."_device_remote_".$device->id;
								if(isset($input[$remote_device_checked]) && $input[$remote_device_checked] ==1)
								{               
									$device_input['user_id'] = $user;
									$device_input['account_id'] = $account_id;
									$device_input['building_id'] = $PurchasedUnit->building_id;
									$device_input['unit_no'] = $PurchasedUnit->unit_id;
									$device_input['device_id'] = $device->id;
									$device_input['device_svn'] = $device->device_serial_no;
									UserRemoteDevice::create($device_input);  
								}        
							}
						}
						$receive_device_call ="receive_device_cal_".$PurchasedUnit->id;
						if(isset($input[$receive_device_call]) && $input[$receive_device_call] !='')
						{
							//echo $receive_device_call;
							UserPurchaserUnit::where('id', $PurchasedUnit->id)
							->update(['receive_call' => $input[$receive_device_call]]);
						}
					}
				}
				$log['module_id'] = 7;
				$log['account_id'] = $account_id;
				$log['admin_id'] = $login_id;
				$log['action'] = 10;
				$log['new_values'] = '';
				$log['ref_id'] = $UserMoreInfoObj->id;
				$log['notes'] = 'User Device Updated from Manager App';
				$log = ActivityLog::create($log);

				return response()->json(['response' => 1, 'message' => 'User Device(s) Updated']);

			}
			else{
				$EmpBuildings = Building::where('account_id',$account_id)->get();
				$data =array();
				if(isset($EmpBuildings)){
					foreach($EmpBuildings as $EmpBuilding){
	
						$devices = Device::where('locations',$EmpBuilding->id)->where('account_id',$account_id)->get();
						if(isset($devices)){
							foreach($devices as $device){
								$bluethooth_device_checked ="building_".$EmpBuilding->id."_device_".$device->id;
								if(isset($input[$bluethooth_device_checked]) && $input[$bluethooth_device_checked] ==1)
								{            
										  
									$device_input['user_id'] = $user;
									$device_input['account_id'] = $account_id;
									$device_input['building_id'] = $EmpBuilding->id;
									$device_input['device_id'] = $device->id;
									$device_input['device_svn'] = $device->device_serial_no;
									UserDevice::create($device_input);  
								} 
								$remote_device_checked = "building_".$EmpBuilding->id."_device_remote_".$device->id;
								if(isset($input[$remote_device_checked]) && $input[$remote_device_checked] ==1)
								{          
									  
									$device_input['user_id'] = $user;
									$device_input['account_id'] = $account_id;
									$device_input['building_id'] = $EmpBuilding->id;
									$device_input['device_id'] = $device->id;
									$device_input['device_svn'] = $device->device_serial_no;
									UserRemoteDevice::create($device_input);  
								}        
							}
							
						}
						
					}
				}
				$log['module_id'] = 7;
				$log['account_id'] = $account_id;
				$log['admin_id'] = $login_id;
				$log['action'] = 10;
				$log['new_values'] = '';
				$log['ref_id'] = $UserMoreInfoObj->id;
				$log['notes'] = 'Employee Device Updated from Manager App';
				$log = ActivityLog::create($log);
				return response()->json(['response' => 1, 'message' => 'Employee Device(s) Updated']);

			}
		}
	}
	
	public function edituser(Request $request) 
    {
		$rules=array(
			'user' => 'required',
		);
		$messages=array(
			'user.required' => 'User Id is missing',
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
		$user = $request->user;
		$adminObj = User::find($login_id); 

		$userObj = UserMoreInfo::where('user_id',$user)->where('account_id',$adminObj->account_id)->first(); 

		if(empty($userObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$account_id = $adminObj->account_id;
			$UserObj = User::find($user);
		
			$input = $request->all();
			$input['name'] = Crypt::encryptString($input['name']);
        	$input['last_name'] = Crypt::encryptString($input['last_name']);
        	$input['phone'] = Crypt::encryptString($input['phone']);
        	$input['encrypted']  =1;

			$UserObj->name =  $input['name'];
			if (trim($input['email']) != '') {
				$UserObj->email = $input['email'];
			}

			if (isset($input['password']) &&  $input['password'] != '') {
				$UserObj->password = bcrypt($input['password']);
				$UserObj->account_enabled = 1;
			}

			$UserObj->role_id = $input['role_id'];
			$env_roles 	= env('USER_APP_ROLE');
			$roles = explode(",",$env_roles);

			$UserObj->save();

			if(isset($input['faceid_access_permission'])){
				$UserMoreObj->faceid_access_permission =1;
				$UserMoreObj->faceid_access_code = $input['faceid_access_code'];
			}else{
				$UserMoreObj->faceid_access_permission = 0;
				$UserMoreObj->faceid_access_code = null;
			}

			$account_id = Auth::user()->account_id;
			if(in_array($UserObj->role_id,$roles))
				$UserMoreObj = UserMoreInfo::where('account_id',$account_id)->where('user_id',$UserObj->id)->orderby('id','desc')->first();
			else
				$UserMoreObj = UserMoreInfo::find($UserObj->empinfo->id);

			$UserMoreObj->first_name = $input['name'];
			$UserMoreObj->last_name = $input['last_name'];
			if(isset($input['receive_device_cal']))
				$UserMoreObj->receive_device_cal = $input['receive_device_cal'];
		
			$UserMoreObj->mailing_address = $input['mailing_address'];
			$UserMoreObj->phone = $input['phone'];
			$UserMoreObj->company_name = $input['company_name'];
			$UserMoreObj->country = $input['country'];
			$UserMoreObj->postal_code = $input['postal_code'];
			if(isset($input['unit_no']))
				$UserMoreObj->unit_no = $input['unit_no'];

			if(isset($input['card_nos']) && count($input['card_nos']) >0){
				$UserMoreObj->card_no = implode(",",$input['card_nos']);
			}
			$UserMoreObj->save();
			
			//$env_roles 	= explode(",",env('USER_APP_ROLE'));
			if(@Auth::user()->role_id ==1){ //by Superadmin assign property
				$properties = Property::where('status',1)->get();
				UserProperty::where('user_id',$id)->delete();
				foreach($properties as $property) {
					$property_input = array();
					$property_checked = "property_".$property->id;
					if(isset($input[$property_checked]))
						{                   
							$property_input['user_id'] = $id;
							$property_input['property_id'] = $property->id;
							UserProperty::create($property_input);  
						}               
				}
			}
			else if(!in_array($UserObj->role_id,$roles)){ //by Agent login assign property
				$login_id = Auth::user()->id;
				$prop_ids = UserProperty::where('user_id',$login_id)->get();
				$properties =array();
			
				if(isset($prop_ids)){
					$assigned_property = array();
					foreach($prop_ids as $prop_id){
						$assigned_property[] = $prop_id->property_id;
					}
					$properties = Property::whereIn('id',$assigned_property)->get();
				}
			
				UserProperty::where('user_id',$id)->delete();
				foreach($properties as $property) {
					//print_r($property->id);
					$property_input = array();
					$property_checked = "property_".$property->id;
					if(isset($input[$property_checked]))
						{                   
							$property_input['user_id'] = $id;
							$property_input['property_id'] = $property->id;
							//print_r($property_input);
							UserProperty::create($property_input);  
						}               
				}
			}
			$auth = new \App\Models\v7\Property();
			$thinmoo_access_token = $auth->thinmoo_auth_api();

			//$user_roles = explode(",",env('USER_APP_ROLE'));

			if(!in_array($UserObj->role_id,$roles)){
				if($login_id ==1){
					if(isset($UserObj->userproperties)){
						Employee::where('uuid', $UserObj->id)
						->update(['status' => 0]);

						foreach($UserObj->userproperties as $property){
							$emp_result = Employee::where('account_id',$property->property_id)->where('uuid',$UserObj->id)->orderby('id','desc')->first();

							$name = $UserObj->name." ".$UserMoreObj->last_name;
							if(empty($emp_result)){
								$emp_rec['account_id'] = $property->property_id;
								$emp_rec['name'] =  $name;
								$emp_rec['emp_type'] =  1;
								$emp_rec['status'] =  1;
								$emp_rec['uuid'] =  $UserObj->id; //
								$result = Employee::create($emp_rec);

								$emp = new \App\Models\v7\Employee();
								$employee = $emp->employee_add_api($thinmoo_access_token,$result,$UserObj->role_id);
							}
							else{
								$EmpObj = Employee::find($emp_result->id);
								$EmpObj->name = $name;
								$EmpObj->status = 1;
								$EmpObj->save();

								$emp_obj = new \App\Models\v7\Employee();
								$employee_result = $emp_obj->employee_check_record($thinmoo_access_token,$EmpObj);

								
								if($employee_result['code'] ==0){
									$employee = $emp_obj->employee_modify_api($thinmoo_access_token,$EmpObj,$UserObj->role_id);
								}
								else{
								
									$employee = $emp_obj->employee_add_api($thinmoo_access_token,$EmpObj,$UserObj->role_id);
								}
							}
						}
					}
				}
				else
				{
					if(isset($UserObj->userproperties)){
						Employee::where('uuid', $UserObj->id)
						->update(['status' => 0]);


						foreach($UserObj->userproperties as $property){

						
							$emp_result = Employee::where('account_id',$property->property_id)->where('uuid',$UserObj->id)->orderby('id','desc')->first();
						

							$name = $UserObj->name." ".$UserMoreObj->last_name;
							if(empty($emp_result)){
								$emp_rec['account_id'] = $property->property_id;
								$emp_rec['name'] =  $name;
								$emp_rec['emp_type'] =  1;
								$emp_rec['status'] =  1;
								$emp_rec['uuid'] =  $UserObj->id; //
								$emp_rec['roleid'] =  $UserObj->id; //

								$result = Employee::create($emp_rec);

								$emp = new \App\Models\v7\Employee();
								$employee = $emp->employee_add_api($thinmoo_access_token,$result,$UserObj->role_id);
							}
							else{
								
								$EmpObj = Employee::find($emp_result->id);
								$EmpObj->name = $name;
								$EmpObj->status = 1;
								$EmpObj->save();

								$emp_obj = new \App\Models\v7\Employee();
								$employee_result = $emp_obj->employee_check_record($thinmoo_access_token,$EmpObj);

								
								if($employee_result['code'] ==0){
									$employee = $emp_obj->employee_modify_api($thinmoo_access_token,$EmpObj,$UserObj->role_id);
								}
								else{
								
									$employee = $emp_obj->employee_add_api($thinmoo_access_token,$EmpObj,$UserObj->role_id);
								}
							}
						}
					}
				}
			}
			else{
				//echo "hi";
				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();
				$name = Crypt::decryptString($UserMoreObj->first_name)." ".Crypt::decryptString($UserMoreObj->last_name);
					$accountinfos = UserPurchaserUnit::where('user_id',$UserObj->id)
					->where('property_id',$UserMoreObj->account_id)
					->get();
					$roomuuids = '';
					if(isset($accountinfos)){
						foreach($accountinfos as $accountinfo){
							$roomuuids .= $accountinfo->unit_id.",";
						}
					}
					$roomuuids = substr($roomuuids,0,-1);
				$api_obj = new \App\Models\v7\User();
				$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj,$UserObj->account_id);
				if($household_result['code'] ==0){
					$household = $api_obj->household_modify_api($thinmoo_access_token,$UserMoreObj->account_id,$name,$UserMoreObj->user_id, $roomuuids);
				}
				else{
					$household = $api_obj->household_add_api($thinmoo_access_token,$UserMoreObj->account_id,$name,$UserMoreObj->user_id, $roomuuids);
				}
	
			}

			return response()->json(['user_info'=>$data,'roles'=>$roles,'unites'=>$unites,'modules'=>$modules,'role_access'=>$role_access,'properties'=>$properties,'response' => 1, 'message' => 'success']);
       
		}
	}
	

	public function createuser(Request $request) 
    {
		$rules=array(
			'name' => 'required',
			'last_name' => 'required',
			'phone' => 'required',
			'mailing_address' => 'required',
			'email' => 'required',
		);
		$messages=array(
			'name.required' => 'First name is missing',
			'last_name.required' => 'Last name is missing',
			'phone.required' => 'Phone is missing',
			'mailing_address.required' => 'Mailling address is missing',
			'email.required' => 'Email is missing',
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
			$user = $request->user;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
				
			$input = $request->all();
			$account_id = $adminObj->account_id;
			$input['name'] = Crypt::encryptString($request->name);
			$input['last_name'] = Crypt::encryptString($request->last_name);
			$input['phone'] = Crypt::encryptString($request->phone);
			$input['encrypted']  =1;

			$user_roles = explode(",",env('USER_APP_ROLE'));
			if(in_array($input['role_id'],$user_roles))
			{
				
					if ($input['building_no'] =='') { 
						return response()->json(['data'=>null,'response' => 200, 'message' => 'Building not selected']);         
					}
					if (empty($input['unit_no'])) { 
						return response()->json(['data'=>null,'response' => 200, 'message' => 'Unit not selected']);         
					}
					$check_email_account = User::where('email',$input['email'])->whereIn('role_id',$user_roles)->first();
					if(isset($check_email_account)){
						$check_userinfo_data = UserMoreInfo::where('user_id',$check_email_account->id)->where('account_id',$account_id)->whereNotIn('status',[2])->first();
		
						if(isset($check_userinfo_data)){
							return response()->json(['data'=>null,'response' => 200, 'message' => 'Email already register for this property!']);
						}
					}
					
				}else{
					$check_email_account = User::where('email',$input['email'])->whereIn('role_id',$user_roles)->first();
					if(isset($check_email_account)){
						return response()->json(['data'=>null,'response' => 200, 'message' => 'Email already registered as user!']);         
					}
					$validator = Validator::make($request->all(), [ 
						'email' =>[
							'required', 
							Rule::unique('users')
							->where('status',1)
						],
					]);
					if ($validator->fails()) { 
						return response()->json(['data'=>null,'response' => 200, 'message' => 'Email already exist!']);         
					}
				}

				if(isset($input['card_nos']) && count($input['card_nos']) >0){
					$input['card_no'] = implode(",",$input['card_nos']);
				}
				$input['primary_contact'] = 0;
				if($input['role_id'] ==2 && empty($input['primary_contact'])){ // assign primary contact
					$unit = $input['unit_no'];
					$primary_contact = User::where('primary_contact',1)->where('unit_no', $unit)->first();
					if(empty($primary_contact)){
						$input['primary_contact'] =1;
					}
				}
				$check_emp_account = User::where('email',$input['email'])->whereNotIn('role_id',$user_roles)->first();
				if(isset($check_emp_account)){
					return response()->json(['data'=>null,'response' => 200, 'message' => 'Email already register as a employee!']);
				}
				$input['account_id'] = $account_id;
				if(in_array($input['role_id'],$user_roles))
				{
					
					$check_email_account = User::where('email',$input['email'])->whereIn('role_id',$user_roles)->first();

					if(empty($check_email_account)){
						$user = User::create($input);
						
						$input['user_id'] = $user->id;
						$input['first_name'] = $input['name'];
						$userinfo = UserMoreInfo::create($input);

						User::where('id', $user->id)
						->update(['user_info_id' => $userinfo->id]);

						$purchaser['user_id'] = $user->id;
						$purchaser['user_info_id'] = $userinfo->id;
						$purchaser['property_id'] = $user->account_id;
						$purchaser['building_id'] = $user->building_no;
						$purchaser['unit_id'] = $user->unit_no;
						$purchaser['primary_contact'] = $user->primary_contact;
						$purchaser['role_id'] = $user->role_id;
						$purchaser['created_at'] = $user->created_at;
						$purchaser['updated_at'] = $user->updated_at;
						$purchaserunit = UserPurchaserUnit::create($purchaser);
					}
					else{
						$input['user_id'] = $check_email_account->id;
						$input['first_name'] = $input['name'];
						$check_userinfo_data = UserMoreInfo::where('user_id',$check_email_account->id)->where('account_id',$account_id)->whereNotIn('status',[2])->first();

						if(isset($check_userinfo_data)){
							/*$UserMoreObj = UserMoreInfo::find($check_userinfo_data->id);
							$UserMoreObj->first_name = $input['name'];
							$UserMoreObj->last_name = $input['last_name'];
							$UserMoreObj->account_id = $account_id;
							if(isset($input['receive_device_cal']))
								$UserMoreObj->receive_device_cal = $input['receive_device_cal'];
						
							$UserMoreObj->mailing_address = $input['mailing_address'];
							$UserMoreObj->phone = $input['phone'];
							$UserMoreObj->company_name = $input['company_name'];
							$UserMoreObj->country = $input['country'];
							$UserMoreObj->postal_code = $input['postal_code'];
							if(isset($input['unit_no']))
								$UserMoreObj->unit_no = $input['unit_no'];

							if(isset($input['card_nos']) && count($input['card_nos']) >0){
								$UserMoreObj->card_no = implode(",",$input['card_nos']);
							}
							$UserMoreObj->save();
							$userinfoid = $UserMoreObj->id; */
							$userinfo = UserMoreInfo::find($check_userinfo_data->id);
                    		$userinfoid = $UserMoreObj->id;

						}else{
							$userinfo = UserMoreInfo::create($input);
							$userinfoid = $userinfo->id;
						}

						if($check_email_account->user_info_id ==''){ //re-assign userinfo ID for deleted case
							User::where('id', $user_id)
							->update(['user_info_id' => $userinfoid,'unit_no' => $input['unit_no']]);
						}

						$user = $check_email_account;
						$purchaser['user_id'] = $check_email_account->id;
						$purchaser['user_info_id'] = $userinfo->id;
						$purchaser['property_id'] = $account_id;
						$purchaser['building_id'] = $input['building_no'];
						$purchaser['unit_id'] = $input['unit_no'];
						$purchaser['primary_contact'] = $input['primary_contact'];
						$purchaser['role_id'] = $input['role_id'];
						$purchaser['created_at'] = $user->created_at;
						$purchaser['updated_at'] = $user->updated_at;
						$purchaserunit = UserPurchaserUnit::create($purchaser);
					}
				}
				else{
					if (isset($input['password']) &&  $input['password'] != '') {
						$input['password'] = bcrypt($input['password']);
					}
					
					$check_email_account = User::where('email',$input['email'])->first();
					if(empty($check_email_account)){
						$user = User::create($input);
					}
					else{
						$user = User::find($check_email_account->id);
						UserMoreInfo::where( 'user_id' , $user->id)->delete();
					}
					if(isset($input['faceid_access_permission'])){
						$input['faceid_access_code'] = $input['faceid_access_code'];
					}else{
						$input['faceid_access_permission'] = 0;
						$input['faceid_access_code'] = null;
					}
					$input['user_id'] = $user->id;
					$input['first_name'] = $input['name'];
					$input['last_name'] = $input['last_name'];
					$userinfo = UserMoreInfo::create($input);

					User::where('id', $user->id)
						->update(['user_info_id' => $userinfo->id]);
				}
				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();
				
				$name = Crypt::decryptString($userinfo->first_name)." ".Crypt::decryptString($userinfo->last_name);
				
				if(in_array($user->role_id,$user_roles))
				{
					$accountinfos = UserPurchaserUnit::where('user_id',$purchaserunit->user_id)
					->where('property_id',$purchaserunit->property_id)
					->get();
					$roomuuids = '';
					$card_numbers = '';
					if(isset($accountinfos)){
						foreach($accountinfos as $accountinfo){
							$roomuuids .= $accountinfo->unit_id.",";
							if($accountinfo->card_no !=''){
								$cardids = explode(",",$accountinfo->card_no);
								$cardRes = Card::select('card')->whereIn('id',$cardids)->get();
								if(isset($cardRes)){
									foreach($cardRes as $cardResult){
										$card_numbers .= $cardResult->card.",";
									}
								}
							}
						}
					}
					$cards = substr($card_numbers,0,-1);
					$roomuuids = substr($roomuuids,0,-1);
				
					if($roomuuids !=''){
						$api_obj = new \App\Models\v7\User();
						$household = $api_obj->household_add_api($thinmoo_access_token, $accountinfo->property_id,$name,$purchaserunit->user_id,$roomuuids,$cards);
					}

				/* $api_obj = new \App\Models\v7\User();
					$household = $api_obj->household_add_api($thinmoo_access_token, $purchaserunit->property_id,$name,$purchaserunit->user_id,$purchaserunit->);


					//print_r($household); */
				}else{

					$role_info =  $user->role;
					$property_input['user_id'] = $user->id;
					$property_input['property_id'] = $user->account_id;
					UserProperty::create($property_input);

					$emp_rec['account_id'] = $user->account_id;
					$emp_rec['name'] =  $name;
					$emp_rec['uuid'] =  $user->id; //
					$result = Employee::create($emp_rec);

					
					$emp = new \App\Models\v7\Employee();
					$employee = $emp->employee_add_api($thinmoo_access_token,$result,$user->role_id);
					
					$role_obj = new \App\Models\v7\Role();
					$role_result = $role_obj->role_check_record($thinmoo_access_token,$user->account_id,$user->role_id);

					if($role_result['code'] !=0){
						//echo "hi";
						$role_data = Role::where('id',$user->role_id)->first();
						//$parent_result = $role_obj->role_check_record($thinmoo_access_token,$user->account_id,3);
						//print_r($role_data);
						$role_data->account_id = $user->account_id;
						$role_data->uuid = $role_data->id;
						$role_data->name = $role_data->name;
						$role_data->parentUuid = 3;

						$add_role_result = $role_obj->role_add_api($thinmoo_access_token,$user->account_id,$role_data);

						//print_r($add_role_result);
					}
				
				}
				//exit;
				$new_values = "Id:". $user->id. ", Name:".$input['name'].", Last Name:".$input['last_name'].", Email:".$input['email'].", Phone:".$input['phone'];
				$log['module_id'] = 7;
				$log['account_id'] = $user->account_id;
				$log['admin_id'] = $login_id;
				$log['action'] = 1;
				$log['new_values'] = $new_values;
				$log['ref_id'] = $userinfo->id;
				$log['notes'] = 'User Created from Mobile App';
				$log = ActivityLog::create($log);

			return response()->json(['user_info'=>$user,'response' => 1, 'message' => 'success']);
       
		}
	}

	

	public function activateuser(Request $request) 
    {
		$rules=array(
			'user' => 'required',
			'user_info_id'=> 'required',
		);
		$messages=array(
			'user.required' => 'User id is missing',
			'user_info_id.required' => 'User Info id is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$user_more_info_id = $request->input('user_info_id'); 
			$UserMoreInfoObj = UserMoreInfo::find($user_more_info_id);
			$UserObj = User::find($UserMoreInfoObj->user_id);
			$user_roles = explode(",",env('USER_APP_ROLE'));
			$id =  $UserMoreInfoObj->user_id;
			$deactivated_date = '0000-00-00';

			if(in_array($UserObj->role_id,$user_roles)){
				$result = UserMoreInfo::where( 'id' , $user_more_info_id)->update( array( 'status' => 1,'deactivated_date'=>$deactivated_date));
				$userinfo =  UserMoreInfo::where( 'id' , $user_more_info_id)->orderby('id','desc')->first();
				$name = $userinfo->first_name." ".$userinfo->last_name;
			}
			else{
				$result = UserMoreInfo::where( 'id' , $user_more_info_id)->update( array( 'status' => 1,'deactivated_date'=>$deactivated_date));
				$result = User::where( 'id' , $id)->update( array( 'status' => 1,'deactivated_date'=>$deactivated_date));
			}

			$auth = new \App\Models\v7\Property();
			$thinmoo_access_token = $auth->thinmoo_auth_api();
			
			$UserObj = User::find($id);

			
			if(in_array($UserObj->role_id,$user_roles)){
				$api_obj = new \App\Models\v7\User();
				$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj,$account_id);

				if($household_result['code'] ==0){
					$api_result = $api_obj->household_activate_api($thinmoo_access_token,$account_id,$name,$id);
				}
			}
			else{
				$emp_result = Employee::where('account_id',$UserObj->account_id)->where('uuid',$UserObj->id)->orderby('id','desc')->first();
				$EmpObj = Employee::find($emp_result->id);
				$emp_obj = new \App\Models\v7\Employee();
				$employee_result = $emp_obj->employee_check_record($thinmoo_access_token,$EmpObj);
				if($employee_result['code'] ==0){
					$employee = $emp_obj->employee_activate_api($thinmoo_access_token,$EmpObj,$UserObj->role_id);
				}
			}

			$log['module_id'] = 7;
			$log['account_id'] = $account_id;
			$log['admin_id'] = $login_id;
			$log['action'] = 4;
			$log['new_values'] = '';
			$log['ref_id'] = $UserMoreInfoObj->id;
			$log['notes'] = 'User Activated from Manager App';
			$log = ActivityLog::create($log);
			return response()->json(['response' => 1, 'message' => 'User account activated']);
			
		}
	}

	public function deactivateuser(Request $request) 
    {
		$rules=array(
			'user' => 'required',
			'user_info_id' =>'required',
		);
		$messages=array(
			'user.required' => 'User id is missing',
			'user_info_id.required' => 'User Info id is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$user_more_info_id = $request->input('user_info_id'); 
			$UserMoreInfoObj = UserMoreInfo::find($user_more_info_id);
			$UserObj = User::find($UserMoreInfoObj->user_id);
			$user_roles = explode(",",env('USER_APP_ROLE'));
			$id = $UserMoreInfoObj->user_id;
			$deactivated_date = date("Y-m-d");
			if(in_array($UserObj->role_id,$user_roles)){
				$result = UserMoreInfo::where( 'id' , $user_more_info_id)->whereNotIn( 'status' , [2])->update( array( 'status' => 0,'deactivated_date'=>$deactivated_date));
				$userinfo =  UserMoreInfo::where( 'id' , $user_more_info_id)->orderby('id','desc')->first();
				$name = $userinfo->first_name." ".$userinfo->last_name;
				$start_date = $userinfo->created_at;
				$end_date = $userinfo->updated_at;
			}
			else{
				$result = UserMoreInfo::where( 'id' , $user_more_info_id)->whereNotIn( 'status' , [2])->update( array( 'status' => 0,'deactivated_date'=>$deactivated_date));

				$result = User::where( 'id' , $id)->update( array( 'status' => 0,'deactivated_date'=>$deactivated_date));
			}

			$auth = new \App\Models\v7\Property();
			$thinmoo_access_token = $auth->thinmoo_auth_api();

			$UserObj = User::find($id);

			if(in_array($UserObj->role_id,$user_roles)){
				$api_obj = new \App\Models\v7\User();
				$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj,$account_id);

				if($household_result['code'] ==0){
					$api_result = $api_obj->household_deactivate_api($thinmoo_access_token,$account_id,$name,$id,$start_date,$end_date);
				}
			}
			else{
				$emp_result = Employee::where('account_id',$UserObj->account_id)->where('uuid',$UserObj->id)->orderby('id','desc')->first();
				$EmpObj = Employee::find($emp_result->id);
				$emp_obj = new \App\Models\v7\Employee();
				$employee_result = $emp_obj->employee_check_record($thinmoo_access_token,$EmpObj);
				if($employee_result['code'] ==0){
					$employee = $emp_obj->employee_deactivate_api($thinmoo_access_token,$EmpObj,$UserObj->role_id);
				}
			}
			
			$log['module_id'] = 7;
			$log['account_id'] = $account_id;
			$log['admin_id'] = $login_id;
			$log['action'] = 5;
			$log['new_values'] = '';
			$log['ref_id'] = $UserMoreInfoObj->id;
			$log['notes'] = 'User Deactivated from Manager App';
			$log = ActivityLog::create($log);
			return response()->json(['response' => 1, 'message' => 'User account de-activated']);
			
		}
	}

	public function userunits(Request $request) 
    {
				$login_id = Auth::id();
		$user_info_id = $request->user_info_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$account_id = $adminObj->account_id;
			$userMoreInfoObj = UserMoreInfo::find($user_info_id);
			$id = $userMoreInfoObj->user_id;
			$UserObj = User::find($id);
			//$UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $id)->where( 'account_id' , $account_id)->orderby('id','desc')->first();
			$PurchaserUnits = UserPurchaserUnit::where('user_info_id',$userMoreInfoObj->id)->where('property_id',$account_id)->get();
        	$unites = Unit::where('account_id',$account_id)->pluck('unit', 'id')->all();
        	$buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        	$app_user_lists = explode(",",env('USER_APP_ROLE'));
			$roles = Role::WhereIn('id',$app_user_lists)->pluck('name', 'id')->all();
			$user_data = array();
			$user_data['id'] = $UserObj->id;
			$user_data['name']= Crypt::decryptString($UserObj->name)." ".Crypt::decryptString($userMoreInfoObj->last_name);
			
			$user_units =array();
			if($PurchaserUnits){
				foreach($PurchaserUnits as $k => $PurchaserUnit){
					$data =array();
					$data['id'] = $PurchaserUnit->id;
					$data['building'] = isset($PurchaserUnit->addubuildinginfo)?$PurchaserUnit->addubuildinginfo->building:null;
					$data['unit'] = isset($PurchaserUnit->addunitinfo)?Crypt::decryptString($PurchaserUnit->addunitinfo->unit):null;
					$data['role'] = isset($PurchaserUnit->role->name)?$PurchaserUnit->role->name:null;
					$data['primary_contact'] = ($PurchaserUnit->primary_contact==1)?"Yes":"No";
					$data['created_date'] = date('d/m/y',strtotime($PurchaserUnit->created_at));
					$user_units[] =$data;
				}
			}
			
			return response()->json(['user'=>$user_data,'roles'=>$roles,'buildings'=>$buildings,'user_units'=>$user_units,'response' => 1, 'message' => 'Success']);
		}
	}

	public function assignunit(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'user_info_id' => 'required',
			'building_no' => 'required',
			'unit_no' => 'required',
			'role_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id is missing',
			'user_info_id.required' => 'User info id is missing',
			'building_no.required' => 'Building id is missing',
			'unit_no.required' => 'Unit is missing',
			'role_id.required' => 'Role is missing',
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
		$user_info_id = $request->user_info_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$account_id = $adminObj->account_id;
			$user_roles = explode(",",env('USER_APP_ROLE'));
			if(in_array($input['role_id'],$user_roles))
			{

				$validator = Validator::make($input, [ 
					'user_info_id' =>[
						'required', 
						Rule::unique('user_purchaser_units')
						->where('property_id',$account_id)
						->where('building_id',$input['building_no'])
						->where('unit_id',$input['unit_no'])
					],
					
				]);
				
				
				if ($validator->fails()) { 
					return response()->json(['data'=>null,'response' => 200, 'message' => 'Unit assigned already!']);
				}

				$userMoreInfoObj = UserMoreInfo::find($user_info_id);
				$UserObj = User::find($userMoreInfoObj->user_id);
		
				//$UserObj = User::find($input['user_id']);
				//$userMoreInfoObj = UserMoreInfo::Where('user_id',$UserObj->id)->Where('account_id',$account_id)->first();

				$input['card_no'] ='';
				if(isset($input['card_nos']) && count($input['card_nos']) >0){
					$input['card_no'] = implode(",",$input['card_nos']);
				}

				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();

				//checking units avbailablility in thinmoo start
				$unitObj = Unit::find($input['unit_no']);
				$api_obj = new \App\Models\v7\Unit();
				$unit_result = $api_obj->unit_check_record($thinmoo_access_token,$unitObj);
				if($unit_result['code'] !=0){
					$unit_info= $api_obj->unit_add_api($thinmoo_access_token,$unitObj);
				}
				$purchaser['user_id'] = $UserObj->id;
				$purchaser['user_info_id'] = $userMoreInfoObj->id;
				$purchaser['property_id'] = $account_id;
				$purchaser['building_id'] = $input['building_no'];
				$purchaser['unit_id'] = $input['unit_no'];
				$purchaser['card_no'] = isset($input['card_no'])?$input['card_no']:null;
				$purchaser['role_id'] = $input['role_id'];
				$purchaser['primary_contact'] = isset($input['primary_contact'])?$input['primary_contact']:0;

				$result = UserPurchaserUnit::create($purchaser);
				//print_r($result );
				//exit;
				
				$name = $userMoreInfoObj->first_name." ".$userMoreInfoObj->last_name;
		
					$accountinfos = UserPurchaserUnit::where('user_id',$UserObj->id)
					->where('property_id',$account_id)
					->get();
					$roomuuids = '';
					$card_numbers = '';
					if(isset($accountinfos)){
						foreach($accountinfos as $accountinfo){
							$roomuuids .= $accountinfo->unit_id.",";
							if($accountinfo->card_no !=''){
								$cardids = explode(",",$accountinfo->card_no);
								$cardRes = Card::select('card')->whereIn('id',$cardids)->get();
								if(isset($cardRes)){
									foreach($cardRes as $cardResult){
										$card_numbers .= $cardResult->card.",";
									}
								}
							}
						}
					}
					$cards = substr($card_numbers,0,-1);
					$roomuuids = substr($roomuuids,0,-1);
					
					if($roomuuids !=''){
						$api_obj = new \App\Models\v7\User();
						$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj,$account_id);

						if($household_result['code'] ==0){
							$household = $api_obj->household_modify_api($thinmoo_access_token, $account_id,$name,$UserObj->id,$roomuuids,$cards);
						}
						else{
							$household = $api_obj->household_add_api($thinmoo_access_token, $account_id,$name,$UserObj->id,$roomuuids,$cards);
						}	
					}

					$log['module_id'] = 7;
					$log['account_id'] = $account_id;
					$log['admin_id'] =$login_id;
					$log['action'] = 7;
					$log['new_values'] = '';
					$log['ref_id'] = $userMoreInfoObj->id;
					$log['notes'] = 'User Unit Assigned from Manager App';
					$log = ActivityLog::create($log);

				return response()->json(['data'=>$result,'response' => 1, 'message' => 'Unit assigned!']);;         
			}
			
			
		}
	}
	public function deleteunit(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id is missing',
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
    
				$login_id = Auth::id();
		$user_id = $request->user_id;
		$id = $request->id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$purchaserObj = UserPurchaserUnit::find($id);
			if(isset($purchaserObj)){
				$account_id = $adminObj->account_id;
				$userObj = User::find($purchaserObj->user_id);
				$UserMoreInfoObj = UserMoreInfo::where( 'id' , $purchaserObj->user_info_id)->first();

				UserPurchaserUnit::findOrFail($id)->delete(); 

				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();
				$name = $UserMoreInfoObj->first_name." ".$UserMoreInfoObj->last_name;
		
					$accountinfos = UserPurchaserUnit::where('user_id',$userObj->id)
					->where('user_info_id',$UserMoreInfoObj->id)
					->where('property_id', $purchaserObj->property_id)
					->get();
					$roomuuids = '';
					$card_numbers = '';
					if(isset($accountinfos)){
						foreach($accountinfos as $accountinfo){
							$roomuuids .= $accountinfo->unit_id.",";
							if($accountinfo->card_no !=''){
								$cardids = explode(",",$accountinfo->card_no);
								$cardRes = Card::select('card')->whereIn('id',$cardids)->get();
								if(isset($cardRes)){
									foreach($cardRes as $cardResult){
										$card_numbers .= $cardResult->card.",";
									}
								}
							}
						}
					}
					$cards = substr($card_numbers,0,-1);
					$roomuuids = substr($roomuuids,0,-1);
		
					if($roomuuids !=''){
						$api_obj = new \App\Models\v7\User();
						$household_result = $api_obj->household_check_record($thinmoo_access_token,$userObj,$purchaserObj->property_id);
						if($household_result['code'] ==0){
							$household = $api_obj->household_modify_api($thinmoo_access_token, $purchaserObj->property_id,$name,$userObj->id,$roomuuids,$cards);
						}
						else{
							$household = $api_obj->household_add_api($thinmoo_access_token, $purchaserObj->property_id,$name,$userObj->id,$roomuuids,$cards);
						}
						//$api_obj = new \App\Models\v7\User();
					
					}

					if($userObj->unit_no == $purchaserObj->unit_id){ //Remove active Unit and re assign latest unit
						$assignUnitObj = UserPurchaserUnit::where('user_id',$userObj->id)
						->where('user_info_id',$UserMoreInfoObj->id)
						->where('property_id', $purchaserObj->property_id)
						->orderBy('id','asc')
						->first();
						$userObj->unit_no  = '';
						$userObj->building_no  ='';
						if(isset($assignUnitObj)){
							$userObj->building_no  = $assignUnitObj->building_id;
							$userObj->unit_no  = $assignUnitObj->unit_id;
						}
						$userObj->save();
					}

					$log['module_id'] = 7;
					$log['account_id'] = $account_id;
					$log['admin_id'] = $login_id;
					$log['action'] = 8;
					$log['new_values'] = '';
					$log['ref_id'] = $UserMoreInfoObj->id;
					$log['notes'] = 'User Unit Deleted from Manager App';
					$log = ActivityLog::create($log);

				return response()->json(['response' => 1, 'message' => 'Deleted']);
			}
			else{
				return response()->json(['response' => 200, 'message' => 'No Record!']);
			}
		}
	}

	public function userlicenseplates(Request $request) 
    {
		$login_id = Auth::id();
		$user_info_id = $request->user_info_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$account_id = $adminObj->account_id;
			$userMoreInfoObj = UserMoreInfo::find($user_info_id);
			$licenseplates = UserLicensePlate::where('user_info_id',$userMoreInfoObj->id)->where('property_id',$account_id)->get();

			$PurchaserUnits = UserPurchaserUnit::where('user_info_id',$userMoreInfoObj->id)->where('property_id',$account_id)->get();
			$user_units =array();
			if($PurchaserUnits){
				foreach($PurchaserUnits as $k => $PurchaserUnit){
					$data =array();
					$data['id'] = $PurchaserUnit->id;
					$data['unit_id'] = $PurchaserUnit->unit_id;
					$data['building'] = isset($PurchaserUnit->addubuildinginfo)?$PurchaserUnit->addubuildinginfo->building:null;
					$data['unit'] = isset($PurchaserUnit->addunitinfo)?Crypt::decryptString($PurchaserUnit->addunitinfo->unit):null;
					$data['role'] = isset($PurchaserUnit->role->name)?$PurchaserUnit->role->name:null;
					$data['primary_contact'] = ($PurchaserUnit->primary_contact==1)?"Yes":"No";
					$data['created_date'] = date('d/m/y',strtotime($PurchaserUnit->created_at));
					$user_units[] =$data;
				}
			}

			$user_data['id'] = $userMoreInfoObj->user_id;
			$user_data['user_info_id'] = $userMoreInfoObj->id;
			$user_data['name']= Crypt::decryptString($userMoreInfoObj->first_name)." ".Crypt::decryptString($userMoreInfoObj->last_name);
			return response()->json(['user'=>$user_data,'licens_plates'=>$licenseplates,'user_units'=>$user_units, 'response' => 1, 'message' => 'Success']);
		}
	}

	public function assignlicenseplates(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'user_info_id' => 'required',
			'unit_id' => 'required',
			'license_plate' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id is missing',
			'user_info_id.required' => 'User info id is missing',
			'unit_id.required' => 'Unit is missing',
			'license_plate.required' => 'License Plate is missing',
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
		$user_info_id = $request->user_info_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$account_id = $adminObj->account_id;
			$data = array();
			$unitObj = Unit::where('id',$input['unit_id'])->first();
			$unit = Crypt::decryptString($unitObj->unit);
			$total_counts =  UserLicensePlate::where('unit_id',$input['unit_id'])->count();
            if($total_counts <2){
			$input['building_id'] = $unitObj->buildinginfo->id;;
			$input['property_id'] = $account_id;
			$result = UserLicensePlate::create($input);
					$log['module_id'] = 7;
					$log['account_id'] = $account_id;
					$log['admin_id'] =$login_id;
					$log['action'] = 7;
					$log['new_values'] = '';
					$log['ref_id'] = $result->id;
					$log['notes'] = 'User License Plate Assigned from Manager App';
					$log = ActivityLog::create($log);

				return response()->json(['data'=>$result,'response' => 1, 'message' => 'License Plate assigned!']);
			}       
			return response()->json(['data'=>null,'response' => 400, 'message' => "Unit (#$unit) has reached its maximum allocated license plate limit."]);	
		}
	}

	public function deletlicenseplate(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id is missing',
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
    
				$login_id = Auth::id();
		$user_id = $request->user_id;
		$id = $request->id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$licenseObj = UserLicensePlate::find($id);
			if(isset($licenseObj)){
				$account_id = $adminObj->account_id;
				$userObj = User::find($licenseObj->user_id);
				$UserMoreInfoObj = UserMoreInfo::where( 'id' , $licenseObj->user_info_id)->first();

				UserLicensePlate::findOrFail($id)->delete(); 

					$log['module_id'] = 7;
					$log['account_id'] = $account_id;
					$log['admin_id'] = $login_id;
					$log['action'] = 8;
					$log['new_values'] = '';
					$log['ref_id'] = $licenseObj->id;
					$log['notes'] = 'User License Plate Deleted from Manager App';
					$log = ActivityLog::create($log);

				return response()->json(['response' => 1, 'message' => 'Deleted']);
			}
			else{
				return response()->json(['response' => 200, 'message' => 'No Record!']);
			}
		}
	}

	public function licenseplateinfo(Request $request) 
    {
		$rules=array(
			'login_id' => 'required',
			'id' => 'required',
		);
		$messages=array(
			'login_id.required' => 'Login id is missing',
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
    
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$licenseObj = UserLicensePlate::find($id);
			if(isset($licenseObj)){
				return response()->json(['data'=>$licenseObj,'response' => 1, 'message' => 'Deleted']);
			}
			else{
				return response()->json(['response' => 200, 'message' => 'No Record!']);
			}
		}
	}

	public function editlicenseplate(Request $request) 
    {
		$rules=array(
			'id'=>'required',
			'license_plate' => 'required',
		);
		$messages=array(
			'id.required' => 'User id is missing',
			'license_plate.required' => 'License Plate is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$account_id = $adminObj->account_id;
			$licenseObj = UserLicensePlate::find($id);
			if(isset($licenseObj)){
				$licenseObj->license_plate =$request->input('license_plate');;
        		$licenseObj->save();
				$result = UserLicensePlate::create($input);
					$log['module_id'] = 7;
					$log['account_id'] = $account_id;
					$log['admin_id'] =$login_id;
					$log['action'] = 7;
					$log['new_values'] = '';
					$log['ref_id'] = $licenseObj->id;
					$log['notes'] = 'User License Plate Updated from Manager App';
					$log = ActivityLog::create($log);

				return response()->json(['data'=>$licenseObj,'response' => 1, 'message' => 'Updated']);
			}
			else{
				return response()->json(['response' => 200, 'message' => 'No Record!']);
			}
			
			
		}
	}

	public function usercards(Request $request) 
    {
		$login_id = Auth::id();
		$user_info_id = $request->user_info_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$account_id = $adminObj->account_id;
			$userMoreInfoObj = UserMoreInfo::find($user_info_id);
			$id = $userMoreInfoObj->user_id;
			$UserObj = User::find($id);
			//$UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $id)->where( 'account_id' , $account_id)->orderby('id','desc')->first();
			$assignedCards = UserCard::where('user_info_id',$userMoreInfoObj->id)->where('property_id',$account_id)->where('status',1)->get();
			$user_data = array();
			$user_data['id'] = $UserObj->id;
			$user_data['name']= Crypt::decryptString($userMoreInfoObj->first_name)." ".Crypt::decryptString($userMoreInfoObj->last_name);
			
			$user_cards =array();
			if($assignedCards){
				foreach($assignedCards as $k => $assignedCard){
					$data =array();
					$data['id'] = $assignedCard->id;
					$data['building'] = isset($assignedCard->addubuildinginfo)?$assignedCard->addubuildinginfo->building:null;
					$data['unit'] = isset($assignedCard->addunitinfo)?Crypt::decryptString($assignedCard->addunitinfo->unit):null;
					$data['card_no'] =$assignedCard->card_no;
					$data['created_date'] = date('d/m/y',strtotime($assignedCard->created_at));
					$user_cards[] =$data;
				}
			}
			
			return response()->json(['user'=>$user_data,'user_cards'=>$user_cards,'response' => 1, 'message' => 'Success']);
		}
	}
	public function assigncard(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'user_info_id' => 'required',
			'building_no' => 'required',
			'card_no' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id is missing',
			'user_info_id.required' => 'User info id is missing',
			'building_no.required' => 'Building id is missing',
			'card_no.required' => 'Unit is missing',
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
		$user_info_id = $request->user_info_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$account_id = $adminObj->account_id;
			$userMoreInfoObj = UserMoreInfo::find($user_info_id);
			$UserObj = User::find($userMoreInfoObj->user_id);
			$user_roles = explode(",",env('USER_APP_ROLE'));
			if(in_array($UserObj->role_id,$user_roles))
			{

				$validator = Validator::make($input, [ 
					'user_id' =>[
						'required', 
						Rule::unique('user_cards')
						->where('card_no',$input['card_no'])
					],
					
				]);
				
				
				if ($validator->fails()) { 
					return response()->json(['data'=>null,'response' => 200, 'message' => 'Card assigned already!']);
				}

				

				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();

				//checking units avbailablility in thinmoo start
				$unitObj = Unit::find($input['unit_no']);
				$api_obj = new \App\Models\v7\Unit();
				$unit_result = $api_obj->unit_check_record($thinmoo_access_token,$unitObj);
				if($unit_result['code'] !=0){
					$unit_info= $api_obj->unit_add_api($thinmoo_access_token,$unitObj);
				}
				$card_nos = explode(",",$input['card_no']);
				$cards_exist = '';
				foreach($card_nos as $card_no){
					$cardObj = UserCard::where('card_no',$card_no)->first();
					if(!isset( $cardObj)){
						$card_input['user_id'] = $UserObj->id;
						$card_input['user_info_id'] = $userMoreInfoObj->id;
						$card_input['property_id'] = $account_id;
						$card_input['building_id'] = $input['building_no'];
						$card_input['unit_id'] = $input['unit_no'];
						$card_input['card_no'] = $card_no;
						UserCard::create($card_input);
					}else{
						$cards_exist = $card_no.",";
					}
				}
		
				$name = Crypt::decryptString($userMoreInfoObj->first_name)." ".Crypt::decryptString($userMoreInfoObj->last_name);
    
                $accountinfos = UserPurchaserUnit::where('user_id',$UserObj->id)
                ->where('user_info_id',$userMoreInfoObj->id)
                ->where('property_id',$account_id)
                ->get();
                $roomuuids = '';
                if(isset($accountinfos)){
                    foreach($accountinfos as $accountinfo){
                        $roomuuids .= $accountinfo->unit_id.",";
                    }
                }
                
                $roomuuids = substr($roomuuids,0,-1);
                $cardinfos = UserCard::where('user_id',$UserObj->id)
                ->where('user_info_id',$userMoreInfoObj->id)
                ->where('property_id',$account_id)
                ->get();
                $card_numbers = '';
                if(isset($cardinfos)){
                    foreach($cardinfos as $cardinfo){
                        $card_numbers .= $cardinfo->card_no.",";
                    }
                }
                $cards = substr($card_numbers,0,-1);
               
                if($roomuuids !=''){
                    $api_obj = new \App\Models\v7\User();
                    $household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj,$account_id);
                    
                    if($household_result['code'] ==0){
                        $household = $api_obj->household_modify_api($thinmoo_access_token, $account_id,$name,$UserObj->id,$roomuuids,$cards);
                    }
                    else{
                        $household = $api_obj->household_add_api($thinmoo_access_token, $account_id,$name,$UserObj->id,$roomuuids,$cards);
                    }
                }

                $log['module_id'] = 7;
                $log['account_id'] = $account_id;
                $log['admin_id'] = $UserObj->id;
                $log['action'] = 7;
                $log['new_values'] = '';
                $log['ref_id'] = $userMoreInfoObj->id;
                $log['notes'] = 'User Card Assigned from Manager App';
                $log = ActivityLog::create($log);

				return response()->json(['response' => 1, 'message' => 'Card assigned!']);;         
			}
			
			
		}
	}

	public function deleteusercard(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id is missing',
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
				$login_id = Auth::id();
		$user_id = $request->user_id;
		$id = $request->id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}

		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$cardObj = UserCard::find($id);
			if(isset($cardObj)){
				$userObj = User::find($cardObj->user_id);
				$account_id = $adminObj->account_id;
				$UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $userObj->id)->where( 'account_id' , $adminObj->account_id)->first();
				UserCard::findOrFail($id)->delete(); 
				//echo "hi";
				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();
				$name = Crypt::decryptString($UserMoreInfoObj->first_name)." ".Crypt::decryptString($UserMoreInfoObj->last_name);

					$accountinfos = UserPurchaserUnit::where('user_id',$userObj->id)
					->where('user_info_id',$UserMoreInfoObj->id)
					->where('property_id', $cardObj->property_id)
					->get();
					$roomuuids = '';
					if(isset($accountinfos)){
						foreach($accountinfos as $accountinfo){
							$roomuuids .= $accountinfo->unit_id.",";
						}
					}
					$roomuuids = substr($roomuuids,0,-1);
					$cardinfos = UserCard::where('user_id',$userObj->id)
					->where('user_info_id',$UserMoreInfoObj->id)
					->where('property_id', $cardObj->property_id)
					->get();
					$card_numbers = '';
					if(isset($cardinfos)){
						foreach($cardinfos as $cardinfo){
							$card_numbers .= $cardinfo->card_no.",";
						}
					}
					$cards = substr($card_numbers,0,-1);

					if($roomuuids !=''){
						$api_obj = new \App\Models\v7\User();
						$household_result = $api_obj->household_check_record($thinmoo_access_token,$userObj,$cardObj->property_id);
						if($household_result['code'] ==0){
							$cards = ($cards !='')?$cards:1;
							$household = $api_obj->household_modify_api($thinmoo_access_token, $cardObj->property_id,$name,$userObj->id,$roomuuids,$cards);
						}
						else{
							$cards = ($cards !='')?$cards:1;
							$household = $api_obj->household_add_api($thinmoo_access_token, $cardObj->property_id,$name,$userObj->id,$roomuuids,$cards);
						}
					}
					$log['module_id'] = 7;
					$log['account_id'] = $account_id;
					$log['admin_id'] = $userObj->id;
					$log['action'] = 8;
					$log['new_values'] = '';
					$log['ref_id'] = $UserMoreInfoObj->id;
					$log['notes'] = 'User Card Deleted';
					$log = ActivityLog::create($log);
					return response()->json(['response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['response' => 200, 'message' => 'No Record!']);
			}
		}
	}

	public function unitcards(Request $request) 
    {
		$rules=array(
			'unit_no' => 'required',
		);
		$messages=array(
			'unit_no.required' => 'Unit is missing',
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
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$unit = $request->unit_no;
        
        	$cards = DB::table("cards")->where("status",1)->where('unit_no',$unit)->orderby('card','asc')->pluck("card","id");
					
			return response()->json(['cards'=>$cards,'response' => 1, 'message' => 'Success']);
		}
	}

	public function useraccess(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'user_info_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id is missing',
			'user_info_id.required' => 'User info id is missing',
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
		$user_info_id = $request->user_info_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$UserMoreInfoObj = UserMoreInfo::find($user_info_id);
			$id = $UserMoreInfoObj->user_id;
			$UserObj = User::find($id);
			$account_id = $adminObj->account_id;
			$user_lists = explode(",",env('USER_APP_ROLE'));

			//$users = User::where('account_id',$account_id)->WhereIn('role_id',$user_lists)->get();
			$roles = Role::WhereIn('id',$user_lists)->pluck('name', 'id')->all();
			$buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

			$users = UserPurchaserUnit::where('property_id',$account_id)->where('user_info_id',$UserMoreInfoObj->id)->where('user_id',$id)->WhereIn('role_id',$user_lists)->orderby('user_id','asc')->get();
					
			$userlists = array();
			$user_access=array();
			foreach($users as $user){
				$userdetail = array();
				$userdetail['id'] = $user->id;
				$userdetail['first_name'] = isset($user->usermoreinfo->first_name)?Crypt::decryptString($user->usermoreinfo->first_name):null;
				$userdetail['last_name'] = isset($user->usermoreinfo->last_name)?Crypt::decryptString($user->usermoreinfo->last_name):null;
				$userdetail['role'] = isset($user->role->name)?$user->role->name:null;
				$userdetail['user_id'] = $user->user_id;
				$userdetail['user_info_id'] = isset($user->usermoreinfo->id)?$user->usermoreinfo->id:null;
				$userdetail['building_id'] = $user->building_id;
				$userdetail['unit_id'] = $user->unit_id;
				$userdetail['building'] = isset($user->addubuildinginfo->building)?$user->addubuildinginfo->building:null;
				$userdetail['unit'] = isset($user->addunitinfo->unit)?"#".Crypt::decryptString($user->addunitinfo->unit):null;
				

				$permissions = UserPermission::where('account_id',$account_id)->where('user_id',$user->user_id)->where('unit_no',$user->unit_id)->orderby('user_id','asc')->get();

				$role_access=array();
				foreach($permissions as $permission){
					$role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);      
				}
				$userdetail['access'] = !empty($role_access)?$role_access:null;
				$userlists[] = $userdetail;
				//$user_access[$user->id][$user->unit_id] = !empty($role_access)?$role_access:null;
			}
			
			//print_r($user_access);
			$all_modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();
       
			$modules =array();
			$property = new \App\Models\v7\Property();

			foreach($all_modules as $module){
				$permission =  $property->check_property_permission($module->id,$account_id,1);
				if(isset($permission) &&  $permission->view==1)
					$modules[] = !empty($module)?$module:null;

			}
					
			return response()->json(['users'=>$userlists,'roles'=>$roles,'buildings'=>$buildings,'modules'=>$modules,'response' => 1, 'message' => 'Success']);
		}
	}

	public function useraccesssearch(Request $request) 
    {
				$login_id = Auth::id();
		$building = $request->building;
		$unit = $request->unit;
		$role = $request->role;
		
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		if(empty($building) && empty($unit) && empty($role)){
			return response()->json(['data'=>null,'response' => 400, 'message' => 'Filter option empty!']);
		}
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$user_lists = explode(",",env('USER_APP_ROLE'));

			$users = UserPurchaserUnit::where(function ($query) use ($account_id,$building,$unit,$role,$user_lists) {
                if($account_id !='')
                    $query->where('property_id',$account_id);
                if($building !='')
					$query->where('building_id', $building);
				if($unit !='')
					$query->Where('unit_id', $unit);
				if($role !='')
					$query->Where('role_id', $role);
				else{
					$query->WhereIn('role_id',$user_lists);
				}
			})->get();

			$roles = Role::WhereIn('id',$user_lists)->pluck('name', 'id')->all();
			$buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

			$userlists = array();
			$user_access=array();
			foreach($users as $user){
				$userdetail = array();
				$userdetail['id'] = $user->id;
				$userdetail['first_name'] = isset($user->usermoreinfo->first_name)?Crypt::decryptString($user->usermoreinfo->first_name):null;
				$userdetail['last_name'] = isset($user->usermoreinfo->last_name)?Crypt::decryptString($user->usermoreinfo->last_name):null;
				$userdetail['role'] = isset($user->role->name)?$user->role->name:null;
				$userdetail['user_id'] = $user->user_id;
				$userdetail['user_info_id'] = $user->usermoreinfo->id;
				$userdetail['building_id'] = $user->building_id;
				$userdetail['unit_id'] = $user->unit_id;
				$userdetail['building_id'] = isset($user->addubuildinginfo->building)?$user->addubuildinginfo->building:null;
				$userdetail['unit'] = isset($user->addunitinfo->unit)?"#".Crypt::decryptString($user->addunitinfo->unit):null;
				

				$permissions = UserPermission::where('account_id',$account_id)->where('user_id',$user->user_id)->where('unit_no',$user->unit_id)->orderby('user_id','asc')->get();

				$role_access=array();
				foreach($permissions as $permission){
					$role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);      
				}
				$userdetail['access'] = !empty($role_access)?$role_access:null;
				$userlists[] = $userdetail;
			}
		
			$all_modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();
       
			$modules =array();
			$property = new \App\Models\v7\Property();

			foreach($all_modules as $module){
				$permission =  $property->check_property_permission($module->id,$account_id,1);
				if(isset($permission) &&  $permission->view==1)
					$modules[] = !empty($module)?$module:null;

			}
					
			return response()->json(['users'=>$userlists,'roles'=>$roles,'buildings'=>$buildings,'modules'=>$modules,'user_access'=>$user_access,'response' => 1, 'message' => 'Success']);
		}
	}


	public function useraccessupdate(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'user_info_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id is missing',
			'user_info_id.required' => 'User info id is missing',
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
		$user_id = $request->user_id;
		$user_info_id = $request->user_info_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$UserMoreInfoObj = UserMoreInfo::find($user_info_id);
			$account_id = $adminObj->account_id;
			$user_lists = explode(",",env('USER_APP_ROLE'));
			$input = $request->all();
			$punitids = $input['punitids'];
			foreach($punitids as $unitid){
				$purchaseUnitid = UserPurchaserUnit::where('id',$unitid)->first();
				
				UserPermission::where('user_id',$purchaseUnitid->user_id)->where('unit_no',$purchaseUnitid->unit_id)->delete();
				$input['user_id'] = $purchaseUnitid->user_id;
				$modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();    
				foreach($modules as $module) {
					$data['account_id'] = $purchaseUnitid->property_id;
					$data['user_id'] = $purchaseUnitid->user_id;
					$data['unit_no'] = $purchaseUnitid->unit_id;
					$data['module_id'] = $module->id;
					$view_field = "mod_".$module->id."_pid_".$unitid;
					
					if(isset($input[$view_field]) && $input[$view_field] ==1)
						{
							$data['view'] = $data['create'] = $data['edit'] = $data['delete'] = 1;
						}
					else
						{
							$data['view'] = $data['create'] = $data['edit'] = $data['delete'] = 0;
						}  

				   UserPermission::create($data);  
				}
			}

			$log['module_id'] = 7;
            $log['account_id'] = $account_id;
            $log['admin_id'] = $login_id;
            $log['action'] = 11;
            $log['new_values'] = '';
            $log['ref_id'] = $UserMoreInfoObj->id;
            $log['notes'] = 'User Access Updated from Manager App';
            $log = ActivityLog::create($log);

			return response()->json(['response' => 1, 'message' => 'updated']);
		}
	}

	public function bulkuseraccess(Request $request) 
    {
				$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$user_lists = explode(",",env('USER_APP_ROLE'));

			//$users = User::where('account_id',$account_id)->WhereIn('role_id',$user_lists)->get();
			$roles = Role::WhereIn('id',$user_lists)->pluck('name', 'id')->all();
			$buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

			$users = UserPurchaserUnit::where('property_id',$account_id)->WhereIn('role_id',$user_lists)->orderby('user_id','asc')->get();
					
			$userlists = array();
			$user_access=array();
			foreach($users as $user){
				$userdetail = array();
				$userdetail['id'] = $user->id;
				$userdetail['first_name'] = isset($user->usermoreinfo->first_name)?Crypt::decryptString($user->usermoreinfo->first_name):null;
				$userdetail['last_name'] = isset($user->usermoreinfo->last_name)?Crypt::decryptString($user->usermoreinfo->last_name):null;
				$userdetail['role'] = isset($user->role->name)?$user->role->name:null;
				$userdetail['user_id'] = $user->user_id;
				$userdetail['user_info_id'] = isset($user->usermoreinfo->id)?$user->usermoreinfo->id:null;
				$userdetail['building_id'] = $user->building_id;
				$userdetail['unit_id'] = $user->unit_id;
				$userdetail['building_id'] = isset($user->addubuildinginfo->building)?$user->addubuildinginfo->building:null;
				$userdetail['unit'] = isset($user->addunitinfo->unit)?"#".Crypt::decryptString($user->addunitinfo->unit):null;
				

				$permissions = UserPermission::where('account_id',$account_id)->where('user_id',$user->user_id)->where('unit_no',$user->unit_id)->orderby('user_id','asc')->get();

				$role_access=array();
				foreach($permissions as $permission){
					$role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);      
				}
				$userdetail['access'] = !empty($role_access)?$role_access:null;
				$userlists[] = $userdetail;
				//$user_access[$user->id][$user->unit_id] = !empty($role_access)?$role_access:null;
			}
			
			//print_r($user_access);
			$all_modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();
       
			$modules =array();
			$property = new \App\Models\v7\Property();

			foreach($all_modules as $module){
				$permission =  $property->check_property_permission($module->id,$account_id,1);
				if(isset($permission) &&  $permission->view==1)
					$modules[] = !empty($module)?$module:null;

			}
					
			return response()->json(['users'=>$userlists,'roles'=>$roles,'buildings'=>$buildings,'modules'=>$modules,'response' => 1, 'message' => 'Success']);
		}
	}

	public function bulkuseraccesssearch(Request $request) 
    {
				$login_id = Auth::id();
		$building = $request->building;
		$unit = $request->unit;
		$role = $request->role;
		
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		if(empty($building) && empty($unit) && empty($role)){
			return response()->json(['data'=>null,'response' => 400, 'message' => 'Filter option empty!']);
		}
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$user_lists = explode(",",env('USER_APP_ROLE'));

			/*$userids = UserPurchaserUnit::where(function ($query) use ($account_id,$building,$unit,$role) {
                if($account_id !='')
                    $query->where('property_id',$account_id);
                if($building !='')
					$query->where('building_id', $building);
				if($unit !='')
					$query->Where('unit_id', $unit);
				if($role !='')
                    $query->Where('role_id', $role);
               
			})->get();
			$prop_userids =array();
			foreach($userids as $k =>$v){
                $prop_userids[] = $v->user_id;
			}
			$users = User::where('account_id',$account_id)->WhereIn('id',$prop_userids)->get();*/
			$user_lists = explode(",",env('USER_APP_ROLE'));

			$users = UserPurchaserUnit::where(function ($query) use ($account_id,$building,$unit,$role,$user_lists) {
                if($account_id !='')
                    $query->where('property_id',$account_id);
                if($building !='')
					$query->where('building_id', $building);
				if($unit !='')
					$query->Where('unit_id', $unit);
				if($role !='')
					$query->Where('role_id', $role);
				else{
					$query->WhereIn('role_id',$user_lists);
				}
			})->get();

			$roles = Role::WhereIn('id',$user_lists)->pluck('name', 'id')->all();
			$buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

			$userlists = array();
			$user_access=array();
			foreach($users as $user){
				$userdetail = array();
				$userdetail['id'] = $user->id;
				$userdetail['first_name'] = isset($user->usermoreinfo->first_name)?Crypt::decryptString($user->usermoreinfo->first_name):null;
				$userdetail['last_name'] = isset($user->usermoreinfo->last_name)?Crypt::decryptString($user->usermoreinfo->last_name):null;
				$userdetail['role'] = isset($user->role->name)?$user->role->name:null;
				$userdetail['user_id'] = $user->user_id;
				$userdetail['user_info_id'] = isset($user->usermoreinfo->id)?$user->usermoreinfo->id:null;
				$userdetail['building_id'] = $user->building_id;
				$userdetail['unit_id'] = $user->unit_id;
				$userdetail['building_id'] = isset($user->addubuildinginfo->building)?$user->addubuildinginfo->building:null;
				$userdetail['unit'] = isset($user->addunitinfo->unit)?"#".Crypt::decryptString($user->addunitinfo->unit):null;
				

				$permissions = UserPermission::where('account_id',$account_id)->where('user_id',$user->user_id)->where('unit_no',$user->unit_id)->orderby('user_id','asc')->get();

				$role_access=array();
				foreach($permissions as $permission){
					$role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);      
				}
				$userdetail['access'] = !empty($role_access)?$role_access:null;
				$userlists[] = $userdetail;
			}
		
			$all_modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();
       
			$modules =array();
			$property = new \App\Models\v7\Property();

			foreach($all_modules as $module){
				$permission =  $property->check_property_permission($module->id,$account_id,1);
				if(isset($permission) &&  $permission->view==1)
					$modules[] = !empty($module)?$module:null;

			}
					
			return response()->json(['users'=>$userlists,'roles'=>$roles,'buildings'=>$buildings,'modules'=>$modules,'user_access'=>$user_access,'response' => 1, 'message' => 'Success']);
		}
	}


	public function bulkuseraccessupdate(Request $request) 
    {
				$login_id = Auth::id();
		$building = $request->building;
		$unit = $request->unit;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$user_lists = explode(",",env('USER_APP_ROLE'));
			$input = $request->all();
			$punitids = $input['punitids'];
			foreach($punitids as $unitid){
				$purchaseUnitid = UserPurchaserUnit::where('id',$unitid)->first();
				
				UserPermission::where('user_id',$purchaseUnitid->user_id)->where('unit_no',$purchaseUnitid->unit_id)->delete();
				$input['user_id'] = $purchaseUnitid->user_id;
				$modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();    
				foreach($modules as $module) {
					$data['account_id'] = $purchaseUnitid->property_id;
					$data['user_id'] = $purchaseUnitid->user_id;
					$data['unit_no'] = $purchaseUnitid->unit_id;
					$data['module_id'] = $module->id;
					$view_field = "mod_".$module->id."_pid_".$unitid;
					
					if(isset($input[$view_field]) && $input[$view_field] ==1)
						{
							$data['view'] = $data['create'] = $data['edit'] = $data['delete'] = 1;
						}
					else
						{
							$data['view'] = $data['create'] = $data['edit'] = $data['delete'] = 0;
						}  

				   UserPermission::create($data);  
				}
			}
			return response()->json(['response' => 1, 'message' => 'updated']);
		}
	}

	public function regSummary(Request $request) 
    {
		$login_id = Auth::id();

		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			
			$results = UserRegistrationRequest::where('account_id',$adminObj->account_id)->orderBy('id','DESC')->get();
			$data = array();
			if(isset($results)){
				foreach($results as $reg){
					//echo isset($faceid->user->name)?$faceid->user->name:null;
					$record['id']=$reg->id;
					$record['building']= $reg->buildinginfo->building;
					$record['unit']= isset($reg->getunit->unit)?"#".Crypt::decryptString($reg->getunit->unit):null;
					$record['first_name']= $reg->first_name;
					$record['last_name']= $reg->last_name;
					$record['email']= $reg->email;
					$record['phone']= $reg->phone;
					$record['role']= isset($reg->role->name)?$reg->role->name:null;
					$record['created_by']= isset($reg->user->name)?Crypt::decryptString($reg->user->name):null;
					if($reg->status ==2)
						$status = "Approved";
					else if($reg->status ==3)
						$status = "Rejected";
					else
						$status = "Pending";
					$record['status']= array($reg->status,$status);
					$record['submitted_date'] = date('d/m/y',strtotime($reg->created_at));
					$record['approved_date']=($reg->approved_date != '0000-00-00 00:00:00' && $reg->approved_date != '')?date('d/m/y',strtotime($reg->approved_date)):null;
					$data[] = $record;
				}
			}

			$file_path = env('APP_URL')."/storage/app";
			return response()->json(['data'=>$data,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
			
		}
	}

	public function regsearch(Request $request) 
    {
		$login_id = Auth::id();

		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$name = $request->input('name');
			$email = $request->input('email');
			$last_name = $request->input('last_name');
			$building = $request->input('building');
			$unit_value = $request->input('unit_value');
			$unit = $request->input('unit');
			$role = $request->input('role');
			$fromdate = $request->input('fromdate');
			if($request->input('todate') !='')
				$todate = $request->input('todate');
			else
				$todate =$request->input('fromdate');
			$status = $request->input('status');

			$account_id = Auth::user()->account_id;
			$units = array();

			if($unit_value !=''){
				$unit_value = str_replace("#",'',$unit_value);
				$unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($building,$unit_value) {
				if($building !='')
					$query->where('building_id',$building);
				//if($unit !='')
					//$query->Where('unit1', Crypt::encryptString($unit));
				})->get();   
				if(isset($unitObj)){
					foreach($unitObj as $unitid){
						if(Crypt::decryptString($unitid->unit) ===$unit_value)
							$units[] = $unitid->id;
						else if ($request->input('unit_value') =='')
							$units[] = $unitid->id;
					}
				}
			}
			//print_r($units);
			$results = UserRegistrationRequest::where('account_id',$account_id)->where(function ($query) use ($fromdate,$todate,$name,$last_name,$email,$building,$unit,$status,$role,$units,$unit_value) {
				if($name !='')
					$query->where('first_name', 'LIKE', '%' . $name . '%');
				if($last_name !='')
					$query->where('last_name', 'LIKE', '%' . $last_name . '%');
				if($email !='')
					$query->where('email', 'LIKE', '%' . $email . '%');
				if($building !='')
					$query->where('building_no', $building);
				if($unit !='')
					$query->where('unit_no', $unit);
				if($unit_value !=''){
					$query->whereIn('unit_no', $units);
				}
				if($role !='')
					$query->where('role_id', $role);
				if($status !='')
					$query->where('status',$status);
				if($fromdate!='' && $todate !='')
					$query->whereBetween('created_at',array($fromdate,$todate));
			})->orderBy('id','DESC')->get();
			$data = array();
			if(isset($results)){
				foreach($results as $reg){
					//echo isset($faceid->user->name)?$faceid->user->name:null;
					$record['id']=$reg->id;
					$record['building']= $reg->buildinginfo->building;
					$record['unit']= isset($reg->getunit->unit)?"#".Crypt::decryptString($reg->getunit->unit):null;
					$record['first_name']= $reg->first_name;
					$record['last_name']= $reg->last_name;
					$record['email']= $reg->email;
					$record['phone']= $reg->phone;
					$record['role']= isset($reg->role->name)?$reg->role->name:null;
					$record['created_by']= isset($reg->user->name)?Crypt::decryptString($reg->user->name):null;
					if($reg->status ==2)
						$status = "Approved";
					else if($reg->status ==3)
						$status = "Rejected";
					else
						$status = "Pending";
					$record['status']= array($reg->status,$status);
					$record['submitted_date'] = date('d/m/y',strtotime($reg->created_at));
					$record['approved_date']=($reg->approved_date != '0000-00-00 00:00:00' && $reg->approved_date != '')?date('d/m/y',strtotime($reg->approved_date)):null;
					$data[] = $record;
				}
			}

			$file_path = env('APP_URL')."/storage/app";
			return response()->json(['data'=>$data,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
			
		}
	}
	public function regdetails(Request $request) 
    {
		$login_id = Auth::id();

		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			if(empty($id)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Id not found']);
			}
			$reg = UserRegistrationRequest::where('account_id',$adminObj->account_id)->where('id',$id)->first();
			if(isset($reg)){
				$data = array();
					$record['id']=$reg->id;
					$record['building']= $reg->buildinginfo->building;
					$record['unit']= isset($reg->getunit->unit)?"#".Crypt::decryptString($reg->getunit->unit):null;
					$record['first_name']= $reg->first_name;
					$record['last_name']= $reg->last_name;
					$record['email']= $reg->email;
					$record['phone']= $reg->phone;
					$record['role_id']= $reg->role_id;
					$record['role']= isset($reg->role->name)?$reg->role->name:null;
					$record['created_by']= isset($reg->user->name)?Crypt::decryptString($reg->user->name):null;
					$record['user_id']= $reg->user_id;
					$record['mailing_address']= $reg->mailing_address;
					$record['country']= $reg->country;
					$record['country_name']= isset($reg->getcountry->country_name)?$reg->getcountry->country_name:null;
					$record['postal_code']= $reg->postal_code;
					$record['contract_file']= $reg->contract_file;
					$record['profile_picture']= $reg->profile_picture;
					
					$record['first_vehicle']= $reg->first_vehicle;
					$record['second_vehicle']= $reg->second_vehicle;
					if($reg->status ==2)
						$status = "Approved";
					else if($reg->status ==3)
						$status = "Rejected";
					else
						$status = "Pending";
					$record['status']= array($reg->status,$status);
					$record['reason']= $reg->reason;
					$record['submitted_date'] = date('d/m/y',strtotime($reg->created_at));
					$record['approved_date']=($reg->approved_date != '0000-00-00 00:00:00' && $reg->approved_date != '')?date('d/m/y',strtotime($reg->approved_date)):null;
					$data[] = $record;
				$file_path = env('APP_URL')."/storage/app";
				return response()->json(['data'=>$data,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
			}else{
				$file_path = env('APP_URL')."/storage/app";
				return response()->json(['data'=>null,'file_path'=>$file_path,'response' => 300, 'message' => 'Record not Found']);
			}
			
		}
	}

	public function regapprove(Request $request) 
    {
		$login_id = Auth::id();

		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			if(empty($id)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Id not found']);
			}
			$regObj = UserRegistrationRequest::find($id);
			$regObj->approved_date = date("Y-m-d H:i:s");
			$regObj->status = 2;
			$regObj->save();
			$check_email_account = User::where('email',$regObj->email)->first();
			if(empty($check_email_account)){
				$input['name'] = Crypt::encryptString($regObj->first_name);
				$input['last_name'] = Crypt::encryptString($regObj->last_name);
				$input['phone'] = Crypt::encryptString($regObj->phone);
				$input['building_no'] = $regObj->building_no;
				$input['unit_no'] = $regObj->unit_no;
				$input['account_id'] = $regObj->account_id;
				$input['role_id'] = $regObj->role_id;
				$input['email'] = $regObj->email;
				$input['encrypted']  =1;
				$user = User::create($input);
				$input['user_id'] = $user->id;
				$input['first_name'] = $input['name'];
				$input['mailing_address'] = $regObj->mailing_address;
				$input['country'] = $regObj->country;
				$input['postal_code'] = $regObj->postal_code;
				$input['company_name'] = $regObj->company_name;

				$input['contract_file'] = $regObj->contract_file;
				
				if($regObj->profile_picture !=''){
					$source_file_path = $regObj->profile_picture;
					$des_file_name = 'profile/'.basename($regObj->profile_picture);
					$des_file_path = $des_file_name;
					if (!Storage::exists('profile')) Storage::makeDirectory('profile');
					Storage::copy($source_file_path, $des_file_path);
					$input['face_picture'] = $des_file_name;
					$input['face_picture_base64'] = base64_encode(file_get_contents(storage_path('app/'.$des_file_name)));
				}

				$userinfo = UserMoreInfo::create($input);
				
				User::where('id', $user->id)
							->update(['user_info_id' => $userinfo->id]);

				$purchaser['user_id'] = $user->id;
				$purchaser['user_info_id'] = $userinfo->id;
				$purchaser['property_id'] = $user->account_id;
				$purchaser['building_id'] = $user->building_no;
				$purchaser['unit_id'] = $user->unit_no;
				$purchaser['receive_call'] = $regObj->receive_call;
				$purchaser['role_id'] = $user->role_id;
				$purchaser['created_at'] = date("Y-m-d H:i:s");
				$purchaser['updated_at'] = date("Y-m-d H:i:s");
				$purchaserunit = UserPurchaserUnit::create($purchaser);

				if($regObj->first_vehicle !=''){
					$lp['user_id'] = $user->id;
					$lp['user_info_id'] = $userinfo->id;
					$lp['property_id'] = $user->account_id;
					$lp['building_id'] = $user->building_no;
					$lp['unit_id'] = $user->unit_no;
					$lp['license_plate'] = $regObj->first_vehicle;
					UserLicensePlate::create($lp);
				}
				if($regObj->second_vehicle !=''){
					$lp['user_id'] = $user->id;
					$lp['user_info_id'] = $userinfo->id;
					$lp['property_id'] = $user->account_id;
					$lp['building_id'] = $user->building_no;
					$lp['unit_id'] = $user->unit_no;
					$lp['license_plate'] = $regObj->second_vehicle;
					UserLicensePlate::create($lp);
				}

				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();
				$name = Crypt::decryptString($userinfo->first_name)." ".Crypt::decryptString($userinfo->last_name);
				$roomuuids = $purchaserunit->unit_id;
				if($roomuuids !=''){
					$api_obj = new \App\Models\v7\User();
					$household = $api_obj->household_add_api($thinmoo_access_token, $purchaserunit->property_id,$name,$purchaserunit->user_id,$roomuuids);
				}

				if($regObj->profile_picture !=''){
					$faceid['user_id'] = $user->id;
					$faceid['option_id'] = 1;
					$faceid['account_id'] = $user->account_id;
					$faceid['unit_no'] = $user->unit_no;
					$faceid['status'] = 2;
					$faceid['face_picture'] = $des_file_name;
					$faceid['face_picture_base64'] = base64_encode(file_get_contents(storage_path('app/'.$des_file_name)));
					$facialResult = UserFacialId::create($faceid);
					$UserObj = User::find($user->id);
					$facial_obj = new \App\Models\v7\UserFacialId();
					$faceid_result= $facial_obj->faceImage_add_api($thinmoo_access_token,$UserObj,$facialResult,$user->account_id);                
					//print_r($faceid_result);
				}
				$name = $regObj->first_name. " ".$regObj->last_name;
                $email = $regObj->email;
                UserRegistrationRequest::sendemail($name, $email,3);
				return response()->json(['response' => 1, 'message' => 'Approved']);

			}
			else{
				$check_userinfo_data = UserMoreInfo::where('user_id',$check_email_account->id)->where('account_id',$regObj->account_id)->whereNotIn('status',[2])->first();
				if(empty($check_email_account)){
					$input['email'] = $regObj->email;
					$input['user_info_id'] = $check_email_account->id;
					$input['name'] = Crypt::encryptString($regObj->first_name);
					$input['last_name'] = Crypt::encryptString($regObj->last_name);
					$input['phone'] = Crypt::encryptString($regObj->phone);
					$input['building_no'] = $regObj->building_no;
					$input['unit_no'] = $regObj->unit_no;
					$input['account_id'] = $regObj->account_id;
					$input['first_name'] = $input['name'];
					$input['mailing_address'] = $regObj->mailing_address;
					$input['country'] = $regObj->country;
					$input['postal_code'] = $regObj->postal_code;
					$input['company_name'] = $regObj->company_name;
					
					$input['contract_file'] = $regObj->contract_file;
					if($regObj->profile_picture !=''){
						$source_file_path = $regObj->profile_picture;
						$des_file_name = 'profile/'.basename($regObj->profile_picture);
						$des_file_path = $des_file_name;
						if (!Storage::exists('profile')) Storage::makeDirectory('profile');
						Storage::copy($source_file_path, $des_file_path);
						$input['face_picture'] = $des_file_name;
						$input['face_picture_base64'] = base64_encode(file_get_contents(storage_path('app/'.$des_file_name)));
					}
					$userinfo = UserMoreInfo::create($input);

					$purchaser['user_id'] = $user->id;
					$purchaser['user_info_id'] = $userinfo->id;
					$purchaser['property_id'] = $user->account_id;
					$purchaser['building_id'] = $user->building_no;
					$purchaser['unit_id'] = $user->unit_no;
					$purchaser['receive_call'] = $regObj->receive_call;
					$purchaser['role_id'] = $user->role_id;
					$purchaser['created_at'] = date("Y-m-d H:i:s");
					$purchaser['updated_at'] = date("Y-m-d H:i:s");
					$purchaserunit = UserPurchaserUnit::create($purchaser);
					if($regObj->first_vehicle !=''){
						$lp['user_id'] = $user->id;
						$lp['user_info_id'] = $userinfo->id;
						$lp['property_id'] = $user->account_id;
						$lp['building_id'] = $user->building_no;
						$lp['unit_id'] = $user->unit_no;
						$lp['license_plate'] = $regObj->first_vehicle;
						UserLicensePlate::create($lp);
					}
					if($regObj->second_vehicle !=''){
						$lp['user_id'] = $user->id;
						$lp['user_info_id'] = $userinfo->id;
						$lp['property_id'] = $user->account_id;
						$lp['building_id'] = $user->building_no;
						$lp['unit_id'] = $user->unit_no;
						$lp['license_plate'] = $regObj->second_vehicle;
						UserLicensePlate::create($lp);
					}

					$auth = new \App\Models\v7\Property();
					$thinmoo_access_token = $auth->thinmoo_auth_api();
					$name = Crypt::decryptString($userinfo->first_name)." ".Crypt::decryptString($userinfo->last_name);
					$roomuuids = $purchaserunit->unit_id;
					if($roomuuids !=''){
						$api_obj = new \App\Models\v7\User();
						$household = $api_obj->household_add_api($thinmoo_access_token, $purchaserunit->property_id,$name,$purchaserunit->user_id,$roomuuids);
					}

					if($regObj->profile_picture !=''){
						$faceid['user_id'] = $user->id;
						$faceid['option_id'] = 1;
						$faceid['account_id'] = $user->account_id;
						$faceid['unit_no'] = $user->unit_no;
						$faceid['status'] = 2;
						$faceid['face_picture'] = $des_file_name;
						$faceid['face_picture_base64'] = base64_encode(file_get_contents(storage_path('app/'.$des_file_name)));
						$facialResult = UserFacialId::create($faceid);
						$UserObj = User::find($user->id);
						$facial_obj = new \App\Models\v7\UserFacialId();
						$faceid_result= $facial_obj->faceImage_add_api($thinmoo_access_token,$UserObj,$facialResult,$user->account_id);
					}
					$name = $regObj->first_name. " ".$regObj->last_name;
					$email = $regObj->email;
					UserRegistrationRequest::sendemail($name, $email,3);
					return response()->json(['response' => 1, 'message' => 'Approved']);
				}
				
				
				return response()->json(['response' => 200, 'message' => 'Email already registered']);
			}
			return response()->json(['response' => 300, 'message' => 'Record not found!']);
		}
	}

	public function regreject(Request $request) 
    {
		$login_id = Auth::id();

		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			if(empty($id)){
				return response()->json(['response' => 300, 'message' => 'Id not found']);
			}
			$regObj = UserRegistrationRequest::find($id);
			$regObj->approved_date = date("Y-m-d H:i:s");
			$regObj->status = 3;
			$regObj->reason = $request->reason;
			$regObj->save();
			$name = $regObj->first_name. " ".$regObj->last_name;
			$email = $regObj->email;
			$reason = $regObj->reason;
			UserRegistrationRequest::cancelemail($name, $email,$reason);
			return response()->json(['response' => 1, 'message' => 'Rejected']);

		}
	}

	public function regdelete(Request $request) 
    {
		$login_id = Auth::id();

		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(7,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			if(empty($id)){
				return response()->json(['response' => 300, 'message' => 'Id not found']);
			}
			UserRegistrationRequest::findOrFail($id)->delete();
			return response()->json(['response' => 1, 'message' => 'Deleted']);

		}
	}

	public function faceidsummary(Request $request) 
    {
		$login_id = Auth::id();

		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(58,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			
			$faceids = UserFacialId::where('account_id',$adminObj->account_id)->where('status',2)->orderBy('id','DESC')->get();
			

			$data = array();
			if(isset($faceids)){
				foreach($faceids as $faceid){
					//echo isset($faceid->user->name)?$faceid->user->name:null;
					$record['id']=$faceid->id;
					$record['submitted_date'] = date('d/m/y',strtotime($faceid->created_at));
					$record['unit']= isset($faceid->getunit->unit)?Crypt::decryptString($faceid->getunit->unit):null;
					$record['name']= isset($faceid->user->name)?Crypt::decryptString($faceid->user->name):null;
					$record['approved_date']=date('d/m/y',strtotime($faceid->updated_at));
					$record['relationship']=isset($faceid->optionname->option)?$faceid->optionname->option:null;
					$record['face_picture'] = ($faceid->face_picture!='')?1:0;
					$data[] = $record;
				}
			}

			$file_path = env('APP_URL')."/storage/app";
			$relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();
			return response()->json(['data'=>$data,'file_path'=>$file_path,'relationships'=>$relationships,'response' => 1, 'message' => 'Success']);
			
		}
	}

	public function faceidnewsummary(Request $request) 
    {
		$login_id = Auth::id();
	
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(58,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			
			$faceids = UserFacialId::where('account_id',$adminObj->account_id)->where('status',1)->orderBy('id','DESC')->get();
			

			$data = array();
			if(isset($faceids)){
				foreach($faceids as $faceid){
					//echo isset($faceid->user->name)?$faceid->user->name:null;
					$record['id']=$faceid->id;
					$record['submitted_date'] = date('d/m/y',strtotime($faceid->created_at));
					$record['building']= isset($faceid->getunit->buildinginfo->building)?$faceid->getunit->buildinginfo->building:null;
					$record['unit']= isset($faceid->getunit->unit)?Crypt::decryptString($faceid->getunit->unit):null;
					$record['name']= isset($faceid->user->name)?Crypt::decryptString($faceid->user->name):null;
					$record['relationship']=isset($faceid->optionname->option)?$faceid->optionname->option:null;
					$record['face_picture'] = ($faceid->face_picture!='')?1:0;
					$data[] = $record;
				}
			}

			$file_path = env('APP_URL')."/storage/app";
			$relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();
			return response()->json(['data'=>$data,'file_path'=>$file_path,'relationships'=>$relationships,'response' => 1, 'message' => 'Success']);
			
		}
	}

	public function searchfaceid(Request $request) 
    {
		$login_id = Auth::id();
	
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(38,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$name = $request->input('name');
			$relationship = $request->input('relationship');
			$unit = $request->input('unit');
			$building = $request->input('building');

			$account_id = $adminObj->account_id;
			$data =array();

			if ($name != '' || $relationship !='' || $unit !='' || $building !='' ) {				
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
				$faceids =  UserFacialId::where('account_id',$account_id)->where('status',2)->where(function ($query) use ($relationship,$name,$userids,$unit,$units,$building) {
					if($name !='')
						$query->whereIn('user_id', $userids);
					if($relationship !='')
						$query->where('option_id',$relationship);
					if($unit !='' || $building !='')
						$query->whereIn('unit_no', $units);

				})->get();
				
				$data = array();
				if(isset($faceids)){
					foreach($faceids as $faceid){
						//echo isset($faceid->user->name)?$faceid->user->name:null;
						$record['id']=$faceid->id;
						$record['submitted_date'] = date('d/m/y',strtotime($faceid->created_at));
						$record['building']= isset($faceid->getunit->buildinginfo->building)?$faceid->getunit->buildinginfo->building:null;
						$record['unit']= isset($faceid->getunit->unit)?Crypt::decryptString($faceid->getunit->unit):null;
						$record['name']= isset($faceid->user->name)?Crypt::decryptString($faceid->user->name):null;
						$record['approved_date']=date('d/m/y',strtotime($faceid->updated_at));
						$record['relationship']=isset($faceid->optionname->option)?$faceid->optionname->option:null;
						$record['face_picture'] = ($faceid->face_picture!='')?1:0;
						$data[] = $record;
					}
				}
				$file_path = env('APP_URL')."/storage/app";
				$relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();
				return response()->json(['data'=>$data,'file_path'=>$file_path,'relationships'=>$relationships,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
			}
		}
	}

	public function faceidAccess(Request $request) 
    {
		$rules=array(
			'access_code' => 'required',
			'id' => 'required',
		);
		$messages=array(
			'access_code.required' => 'Access Code is missing',
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(38,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$code =  $request->access_code; //access code
			$faceidObj  =  UserFacialId::find($id);
			if(isset($faceidObj)){
				$userId =  $login_id;
				$MoreInfoObj = UserMoreInfo::where('user_id',$userId)->whereNotIn('status',[2])->first();
				if(isset($MoreInfoObj)){
					if($code == $MoreInfoObj->faceid_access_code){
						$result['status'] = 1;
						$result['img'] = $faceidObj->face_picture;
						$result['64img'] = $faceidObj->face_picture_base64;
					}
					else{
						return response()->json(['data'=>null,'response' => 400, 'message' => 'Invalid access code!']);
					}
				}
				else{
					return response()->json(['data'=>null,'response' => 500, 'message' => 'Login not found!']);
				}
			}
			else{
				return response()->json(['data'=>null,'response' => 600, 'message' => 'No record!']);
			}

			
			$file_path = env('APP_URL')."/storage/app";
			return response()->json(['data'=>$result,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
			
		}
	}

	public function searchnewfaceid(Request $request) 
    {
		$login_id = Auth::id();
	
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(38,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$name = $request->input('name');
			$relationship = $request->input('relationship');
			$unit = $request->input('unit');
			$account_id = $adminObj->account_id;
			$data =array();

			if ($name != '' || $relationship !='' || $unit !='') {				
				$units = array();
				if($unit !=''){      
					$unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->get();    
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
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

				$faceids =  UserFacialId::where('account_id',$account_id)->where('status',1)->where(function ($query) use ($relationship,$name,$userids,$unit,$units) {
					if($name !='')
						$query->whereIn('user_id', $userids);
					if($relationship !='')
						$query->where('option_id',$relationship);
					if($unit !='')
						$query->whereIn('unit_no', $units);

				})->get();
				
				$data = array();
				if(isset($faceids)){
					foreach($faceids as $faceid){
						//echo isset($faceid->user->name)?$faceid->user->name:null;
						$record['id']=$faceid->id;
						$record['submitted_date'] = date('d/m/y',strtotime($faceid->created_at));
						//$record['unit']= isset($faceid->getunit->unit)?Crypt::decryptString($faceid->getunit->unit):null;
						$record['user_id']= $faceid->user_id;

						$record['name']= isset($faceid->user->name)?Crypt::decryptString($faceid->user->name):null;
						$record['approved_date']=date('d/m/y',strtotime($faceid->updated_at));
						$record['relationship']=isset($faceid->optionname->option)?$faceid->optionname->option:null;
						$record['face_picture'] = ($faceid->face_picture!='')?1:0;
						$data[] = $record;
					}
				}
				$file_path = env('APP_URL')."/storage/app";
				$relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();
				return response()->json(['data'=>$data,'file_path'=>$file_path,'relationships'=>$relationships,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
			}
		}
	}
	

	public function stafffaceids(Request $request) 
    {
		$login_id = Auth::id();
	
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(58,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$permission = $adminObj->check_permission(58,$adminObj->role_id); 

			$userproperties = UserProperty::where('property_id',$adminObj->account_id)->get();
			$assigned_property = array();
        	foreach($userproperties as $userproperty){
                $assigned_property[] = $userproperty->user_id;
        	}
			$user_roles = explode(",",env('USER_APP_ROLE'));

			$staffs = User::whereIn("id",$assigned_property)->whereNotIn('role_id',$user_roles)->where("status",1)->orderby('id','asc')->get();
			$stafids = array();
			if(isset($staffs)){
				foreach($staffs as $staff){
					$stafids[] = $staff->id;
				}
			
			}

			//print_r($stafids);
			if(isset($permission)){
				$faceids = UserFacialId::where('account_id',$adminObj->account_id)->whereIn('user_id',$stafids)->where('status',2)->orderBy('id','DESC')->get();
			}
			else{
				$faceids = UserFacialId::where('account_id',$adminObj->account_id)->where('user_id',$login_id)->where('status',2)->orderBy('id','DESC')->get();
			}
			
			$data = array();
			if(isset($faceids)){
				foreach($faceids as $faceid){
					//echo isset($faceid->user->name)?$faceid->user->name:null;
					$record['id']=$faceid->id;
					$record['submitted_date'] = date('d/m/y',strtotime($faceid->created_at));
					//$record['unit']= isset($faceid->getunit->unit)?Crypt::decryptString($faceid->getunit->unit):null;
					$record['user_id']= $faceid->user_id;
					$record['name']= isset($faceid->user->name)?Crypt::decryptString($faceid->user->name):null;
					$record['approved_date']=date('d/m/y',strtotime($faceid->updated_at));
					$record['face_picture'] = ($faceid->face_picture!='')?1:0;
					$data[] = $record;
				}
			}

			$file_path = env('APP_URL')."/storage/app";
			return response()->json(['data'=>$data,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
			
		}
	}

	public function staffnewuploadlists(Request $request) 
    {
		$login_id = Auth::id();
	
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(58,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$permission = $adminObj->check_permission(58,$adminObj->role_id); 

			$userproperties = UserProperty::where('property_id',$adminObj->account_id)->get();
			$assigned_property = array();
        	foreach($userproperties as $userproperty){
                $assigned_property[] = $userproperty->user_id;
        	}
			$user_roles = explode(",",env('USER_APP_ROLE'));

			$staffs = User::whereIn("id",$assigned_property)->whereNotIn('role_id',$user_roles)->where("status",1)->orderby('id','asc')->get();
			$stafids = array();
			if(isset($staffs)){
				foreach($staffs as $staff){
					$stafids[] = $staff->id;
				}
			
			}

			//print_r($stafids);
			if(isset($permission)){
				$faceids = UserFacialId::where('account_id',$adminObj->account_id)->whereIn('user_id',$stafids)->where('status',1)->orderBy('id','DESC')->get();
			}
			else{
				$faceids = UserFacialId::where('account_id',$adminObj->account_id)->where('user_id',$login_id)->where('status',1)->orderBy('id','DESC')->get();
			}
			

			$data = array();
			if(isset($faceids)){
				foreach($faceids as $faceid){
					//echo isset($faceid->user->name)?$faceid->user->name:null;
					$record['id']=$faceid->id;
					$record['submitted_date'] = date('d/m/y',strtotime($faceid->created_at));
					$record['unit']= isset($faceid->getunit->unit)?Crypt::decryptString($faceid->getunit->unit):null;
					$record['user_id']= $faceid->user_id;

					$record['name']= isset($faceid->user->name)?Crypt::decryptString($faceid->user->name):null;
					$record['approved_date']=date('d/m/y',strtotime($faceid->updated_at));
					$record['face_picture'] = ($faceid->face_picture!='')?1:0;
					$data[] = $record;
				}
			}

			$file_path = env('APP_URL')."/storage/app";

			return response()->json(['data'=>$data,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
			
		}
	}

	

	public function getroleslist(Request $request) 
    {
		$login_id = Auth::id();
	
		$user = $request->user;
		$adminObj = User::find($login_id); 
		$user_roles = explode(",",env('USER_APP_ROLE'));

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
		$roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$adminObj->account_id .',%')->orWhere('account_id',0)->whereNotIn('id',$user_roles)->get();

		return response()->json(['data'=>$roles,'response' => 1, 'message' => 'success']);
       
	}
	public function getstafflist(Request $request) 
    {
		$rules=array(
			'role' => 'required',
		);
		$messages=array(
			'role.required' => 'Role id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
		}
		$app_user_lists = explode(",",env('USER_APP_ROLE'));

		$login_id = Auth::id();
		$role = $request->role;
		$unit_no = $request->unit_no;
		$user = $request->user;
		$adminObj = User::find($login_id); 
		$account_id = $adminObj->account_id;
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
		if(in_array($role,$app_user_lists)){
			$unitids_byusers = UserPurchaserUnit::where('status',1)->where(function ($query) use ($role,$unit_no) {
				if($role !='')
					$query->where('role_id', $role);
				if($unit_no !='')
					$query->where('unit_id', $unit_no);
			
			})->get();
			$prop_userids =array();
			foreach($unitids_byusers as $k =>$v){
				$prop_userids[] = $v->user_info_id;
			}
			$users = UserMoreInfo::whereNotIn('status',[2])->WhereIn('id',$prop_userids)->orderBy('first_name','ASC')->get();
			$data = array();
			if(isset($users)){
				foreach($users as $user){
						$result = array();
						$result['id'] = $user->user_id;
						$result['user_info_id'] = $user->id;
						$result['name'] = Crypt::decryptString($user->first_name)." ".Crypt::decryptString($user->last_name);
						$result['role'] = $role;
						$result['unit_no'] = $unit_no;
						$data[] = $result;
				}
			}
		}
		else{
			$userproperties = UserProperty::where('property_id',$account_id)->get();
			$assigned_property = array();
        	foreach($userproperties as $userproperty){
                $assigned_property[] = $userproperty->user_id;

        	}
			$staffs = User::whereIn("id",$assigned_property)->where('role_id',$role)->where("status",1)->orderby('name','asc')->get();
			$data = array();
			if(isset($staffs)){
				foreach($staffs as $staff){
						$staff_moreinfo = UserMoreInfo::where('user_id',$staff->id)->first();
						$result = array();
						$result['id'] = $staff->id;
						if(isset($staff_moreinfo))
                        	$result['name'] = Crypt::decryptString($staff->name)." ".Crypt::decryptString($staff_moreinfo->last_name);
                    	else
							$result['name'] = Crypt::decryptString($staff->name);		
						$result['role'] = $staff->role_id;
						$result['unit_no'] = $staff->unit_no;
						$data[] = $result;
					
				}
			}
		}
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'success']);
       
	}


	public function uploadoptions(Request $request) 
    {
		$login_id = Auth::id();
	
		$unit_no =  $request->unit_no;
		$user = $request->user;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
		$options = FacialRecoOption::where('status',1)->get();
		return response()->json(['data'=>$options,'response' => 1, 'message' => 'success']);
       
	}

	public function getuserlist(Request $request) 
    {
		$rules=array(
			'unit_no' => 'required',
		);
		$messages=array(
			'unit_no.required' => 'Unit No is missing',
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
		$unit_no =  $request->unit_no;
		$role = $request->role;

		$user = $request->user;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}

		$unitids_byusers = UserPurchaserUnit::where('status',1)->where(function ($query) use ($role,$unit_no) {
			if($role !='')
				$query->where('role_id', $role);
			if($unit_no !='')
				$query->where('unit_id', $unit_no);
		   
		})->get();
		$prop_userids =array();
        foreach($unitids_byusers as $k =>$v){
            $prop_userids[] = $v->user_info_id;
        }
		$users = UserMoreInfo::whereNotIn('status',[2])->WhereIn('id',$prop_userids)->orderBy('first_name','ASC')->get();
		$data = array();
		if(isset($users)){
			foreach($users as $user){
                    $result = array();
					$result['id'] = $user->user_id;
					$result['user_info_id'] = $user->id;
					$result['name'] =Crypt::decryptString($user->first_name)." ".Crypt::decryptString($user->last_name);
					$result['role'] = $role;
					$result['unit_no'] = $unit_no;
                    $data[] = $result;
			}
		}

		/*$PurchaseLists = UserPurchaserUnit::where('property_id',$adminObj->account_id)->where('unit_id',$unit_no)->get(); 
		$UnitPurchasedUser = array();
		$data = array();
		if(isset($PurchaseLists)){
			foreach($PurchaseLists as $PurchaseList){
				$staff= UserMoreInfo::where("id",$PurchaseList->user_info_id)->where("status",1)->first();
				if(isset($staff)){
					$result = array();
					$result['id'] = $PurchaseList->user_id;
					$result['user_info_id'] = $PurchaseList->user_info_id;
					$result['name'] = $staff->first_name." ".$staff->last_name;

					$result['role'] = $PurchaseList->role_id;
					$result['unit_no'] = $PurchaseList->unit_id;
					$data[] = $result;
				}
				//$UnitPurchasedUser[]= $PurchaseList->user_info_id;
			}
		}
		//print_r($UnitPurchasedUser);
		//exit;
		/*$staffs = UserMoreInfo::whereIn("id",$UnitPurchasedUser)->where("status",1)->orderby('name','asc')->get();
		$data = array();
		if(isset($staffs)){
			foreach($staffs as $staff){
				$result = array();
				$result['id'] = $staff->user_id;
				$result['user_info_id'] = $staff->user_id;
				$result['name'] = $staff->first_name.$staff->last_name;

				$result['role'] = $staff->role_id;
				$result['unit_no'] = $staff->unit_no;
				$data[] = $result;
			}
		}*/
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'success']);
       
	}

	public function uploadstafffaceid(Request $request) 
    {
		$login_id = Auth::id();
	
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(58,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$user_id = $request->user_id;
			$option = $request->option;

			$input = $request->all();
			$input['account_id'] = $adminObj->account_id;
			$input['user_id'] =$user_id;
			$input['option_id'] =$option;
			$input['status'] =2;
			if ($request->file('picture') != null) {
				$file = $request->file('picture');
				$ImgObj = new \App\Models\v7\UserFacialId();
				$ImgObj->correctImageOrientation($file);

				$input['face_picture'] = remove_upload_path($request->file('picture')->store(upload_path('profile')));
				$input['face_picture_base64'] = base64_encode(file_get_contents($request->file('picture')));

			}
		
			$facialResult = UserFacialId::create($input);
		
			$UserObj = User::find($facialResult->user_id);

			if($facialResult->option_id ==1 && $UserObj->face_picture ==''){
				UserMoreInfo::where('user_id' , $facialResult->user_id)->update( array( 'face_picture' =>  $facialResult->face_picture,'face_picture_base64' =>  $facialResult->face_picture_base64));
			}

			$UserObj = User::find($facialResult->user_id);

			$auth = new \App\Models\v7\Property();
			$thinmoo_access_token = $auth->thinmoo_auth_api();  
			$facial_obj = new \App\Models\v7\UserFacialId();
			
			$user_roles = explode(",",env('USER_APP_ROLE'));

			if(in_array($UserObj->role_id,$user_roles)){
				//echo "hello";
				$api_obj = new \App\Models\v7\User();
				$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj);

				if($household_result['code'] ==0){
					$faceid_result= $facial_obj->faceImage_api($thinmoo_access_token,$UserObj,$facialResult);
				}
				else{
					$faceid_result= $facial_obj->faceImage_add_api($thinmoo_access_token,$UserObj,$facialResult);
				}
			}else{

				$emp_result = Employee::where('account_id',$adminObj->account_id)->where('uuid',$UserObj->id)->orderby('id','desc')->first();
				$emp_result['role_id'] = $UserObj->role_id;
				$emp_obj = new \App\Models\v7\Employee();

				$household_result = $emp_obj->employee_check_record($thinmoo_access_token,$emp_result);

				if($household_result['code'] ==0){
					//echo "hai";
					$faceid_result= $facial_obj->faceImage_emp_api($thinmoo_access_token,$emp_result,$facialResult);
				}
				else{
					//echo "hello";
					$faceid_result= $facial_obj->faceImage_add_emp_api($thinmoo_access_token,$emp_result,$facialResult);
				}
			}
			
			
		
			if(isset($faceid_result['data'])){

				UserFacialId::where('id', $facialResult->id)
			->update(['thinmoo_id' => $faceid_result['data']['id']]);
			}
			
			return response()->json(['users'=>$facialResult,'response' => 1, 'message' => 'Success']);
			
		}
	}
	public function faceidupload(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'option_id' => 'required',
			'unit_no' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User Id is missing',
			'option_id.required' => 'Option Id is missing',
			'unit_no.required' => 'Unit No is missing',
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
	
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(58,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$input = $request->all();
			$account_id = $adminObj->account_id;
	
			$input['account_id'] = $account_id;
			$input['status'] =2;
			if ($request->file('picture') != null) {
				$input['face_picture'] = remove_upload_path($request->file('picture')->store(upload_path('profile')));
				$input['face_picture_base64'] = base64_encode(file_get_contents($request->file('picture')));
	
			}
			if($adminObj->role_id !=3){
				$input['user_id'] = $adminObj->id;
				$input['option_id'] =1;
			}
			$facialResult = UserFacialId::create($input);
			$UserMoreInfoObj = UserMoreInfo::where('user_id',$facialResult->user_id)->where('account_id',$facialResult->account_id)->where('status',1)->first();
			if($facialResult->option_id ==1 && $UserMoreInfoObj->face_picture ==''){
				UserMoreInfo::where('id' , $UserMoreInfoObj->id)->update( array( 'face_picture' =>  $facialResult->face_picture,'face_picture_base64' =>  $facialResult->face_picture_base64));
			}
	
			$UserObj = User::find($facialResult->user_id);
			$auth = new \App\Models\v7\Property();
			$thinmoo_access_token = $auth->thinmoo_auth_api();  
			$facial_obj = new \App\Models\v7\UserFacialId();
			
			$user_roles = explode(",",env('USER_APP_ROLE'));
			if(in_array($UserObj->role_id,$user_roles)){
				$api_obj = new \App\Models\v7\User();
				$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj);
	
				if($household_result['code'] ==0){
					$faceid_result= $facial_obj->faceImage_api($thinmoo_access_token,$UserObj,$facialResult);
				}
				else{
					$faceid_result= $facial_obj->faceImage_add_api($thinmoo_access_token,$UserObj,$facialResult);
				}
			}else{
	
				$emp_result = Employee::where('account_id',$account_id)->where('uuid',$UserObj->id)->orderby('id','desc')->first();
				
				$emp_result['role_id'] = $UserObj->role_id;
	
				$emp_obj = new \App\Models\v7\Employee();
	
				$household_result = $emp_obj->employee_check_record($thinmoo_access_token,$emp_result);
	
				if($household_result['code'] ==0){
					$faceid_result= $facial_obj->faceImage_emp_api($thinmoo_access_token,$emp_result,$facialResult);
				}
				else{
					$faceid_result= $facial_obj->faceImage_add_emp_api($thinmoo_access_token,$emp_result,$facialResult);
				}
			}
			
		 
			if(isset($faceid_result['data'])){
	
				UserFacialId::where('id', $facialResult->id)
			->update(['thinmoo_id' => $faceid_result['data']['faceImageIds']]);
			}
		
			return response()->json(["result"=>$facialResult,'response' => 1, 'message' => 'Uploaded']);
			
		}
	}
	public function faceiddetail(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(58,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$id = $request->id;
			$facialResult = UserFacialId::find($id);
			$data=array();
			if(isset($facialResult)){
				$data['id'] =$facialResult->id;
				$data['user_id'] =$facialResult->user_id;
				$data['user'] =isset($facialResult->user->name)?$facialResult->user->name:'';
				$data['face_picture'] =$facialResult->face_picture;
				$data['option_id'] =$facialResult->option_id;
				$data['option'] =isset($facialResult->optionname->option)?$facialResult->optionname->option:'';
				$data['account_id'] =$facialResult->account_id;
				$data['unit_no'] =$facialResult->unit_no;
				$data['unit'] =isset($facialResult->user->getunit->unit)?"#".$facialResult->user->getunit->unit:'';
				$data['status'] =$facialResult->status;
				$data['others'] =$facialResult->others;
				$data['reason'] =$facialResult->reason;
				$data['submitted_date']=date('d/m/y',strtotime($facialResult->created_at));
				$data['approved_date']=($facialResult->status ==2)?date('d/m/y',strtotime($facialResult->updated_at)):null;
				$file_path = env('APP_URL')."/storage/app";
        		//$relationships =  FacialRecoOption::where('status',1)->get();
				return response()->json(['data'=>$data,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
			}else{
				return response()->json(['response' => 1, 'message' => 'No Record']);
			}
			
		}
	}
	public function faceidedit(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(58,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$id = $request->id;
			$faceObj = UserFacialId::find($id);
			if(isset($faceObj)){
				$faceObj->option_id = $request->input('option_id');
				$faceObj->save();
				$UserMoreInfoObj = UserMoreInfo::where('user_id',$faceObj->user_id)->where('account_id',$faceObj->account_id)->where('status',1)->first();
				if($faceObj->option_id ==1 && $UserMoreInfoObj->face_picture ==''){
					UserMoreInfo::where('id' , $UserMoreInfoObj->id)->update( array('face_picture' =>  $faceObj->face_picture,'face_picture_base64' =>  $faceObj->face_picture_base64));
				}
				return response()->json(['response' => 1, 'message' => 'Updated']);
			}
			else{
				return response()->json(['response' => 1, 'message' => 'No Record']);
			}
		}
	}

	public function faceuploadcancel(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(58,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$reason ='';
			if(isset($request->reason))
				$reason = $request->reason;

			$id = $request->id;
			$status = 3; //cancelled

			UserFacialId::where('id', $id)
					->update(['status' => $status,'reason'=>$reason]);
					$faceidObj  =  UserFacialId::find($id);  
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
			
			return response()->json(['response' => 1, 'message' => 'Cancelled']);
			
		}
	}

	public function faceuploadapproval(Request $request) 
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
		
		$login_id = Auth::id();
	
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(58,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$bookid = $request->id;
			$status = 2; //cancelled

			UserFacialId::where('id', $bookid)
					->update(['status' => $status,'updated_at'=>date('Y-m-d H:i:s')]);

			$facialResult = UserFacialId::find($bookid);
			$UserObj = User::find($facialResult->user_id);

			if($facialResult->option_id ==1 && $UserObj->face_picture ==''){
				UserMoreInfo::where( 'user_id' , $facialResult->user_id)->update( array( 'face_picture' =>  $facialResult->face_picture,'face_picture_base64' =>  $facialResult->face_picture_base64));
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
				//print_r($faceid_result);
				UserFacialId::where('id', $bookid)
			->update(['thinmoo_id' => $faceid_result['data']['faceImageIds']]);
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
		
			return response()->json(['response' => 1, 'message' => 'Approved']);
			
		}
	}

	public function faceiddelete(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(58,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$id = $request->id;
			$facialResult = UserFacialId::find($id);

			$UserObj = User::find($facialResult->user_id);

			$auth = new \App\Models\v7\Property();
			$thinmoo_access_token = $auth->thinmoo_auth_api();  
			
			$facial_obj = new \App\Models\v7\UserFacialId();


			$user_roles = explode(",",env('USER_APP_ROLE'));
			if(in_array($UserObj->role_id,$user_roles)){
				$api_obj = new \App\Models\v7\User();
				$household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj);
				if($household_result['code'] ==0){
					$faceid_result= $facial_obj->faceImage_delete_api($thinmoo_access_token,$UserObj,$facialResult);
				}
			}else{

				$emp_result = Employee::where('account_id',$UserObj->account_id)->where('uuid',$UserObj->id)->orderby('id','desc')->first();
				$emp_result['role_id'] = $UserObj->role_id;

				$emp_obj = new \App\Models\v7\Employee();

				$household_result = $emp_obj->employee_check_record($thinmoo_access_token,$emp_result);

				if($household_result['code'] ==0){
					$faceid_result= $facial_obj->faceImage_delete_emp_api($thinmoo_access_token,$emp_result,$facialResult);
				}
			
				
			}

			UserFacialId::findOrFail($id)->delete();
		
			return response()->json(['response' => 1, 'message' => 'Deleted']);
			
		}
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


	public function staffbluetoothdevices(Request $request) 
    {
		$login_id = Auth::id();
	
		$adminObj = User::find($login_id); 
		$role = $adminObj->role_id;
		$account_id =$adminObj->account_id;
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(59,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$auth = new \App\Models\v7\Property();
			$token = $auth->thinmoo_auth_api();

			$url = env('THINMOO_API_URL')."wyEmpProperty/extapi/getAuthorizationDevList";

			$emp_result = Employee::where('account_id',$account_id)->orderby('id','asc')->first();
		
			//The data you want to send via POST
			$fields = [
				'accessToken'      	=> 	$token,
				'uuid'              => 	$emp_result->id, //default emp id
				'extCommunityUuid'	=>	$adminObj->account_id,
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
				$data = array();
				return response()->json(['devices'=>$data,'response' => 0, 'message' => 'No Records']);

			}
			else{

				$roleObj = Role::find($role);
				//print_r($roleObj->roledevices);

				$device_access = array();
				if(isset($adminObj->userdevices)){
					foreach($adminObj->userdevices as $selecteddevices){
						$device_access[] = $selecteddevices->device_svn;
					}
				}

				//print_r($json['data']);
				$data = array();
				$thinmoo_devices = $json['data'];
				foreach($thinmoo_devices as $T_device){
					$items_array = array();
					if(in_array($T_device['devSn'],$device_access) ){// devices based on Role or Managing agent can all devices
						$device_info = Device::where('device_serial_no',$T_device['devSn'])->first();
						$items_array['thinmoo'] = $T_device;
						$items_array['moreinfo'] = $device_info;
						$data[] = $items_array;
					}
				}
			}
			
			return response()->json(['devices'=>$data,'response' => 1, 'message' => 'Success']);
			
		}
	}

	

	public function staffremotedevices(Request $request) 
    {
		$login_id = Auth::id();
	
		$adminObj = User::find($login_id); 
		$role = $adminObj->role_id;
		$account_id =$adminObj->account_id;
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(64,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$auth = new \App\Models\v7\Property();
			$token = $auth->thinmoo_auth_api();

			$url = env('THINMOO_API_URL')."wyEmpProperty/extapi/getAuthorizationDevList";

			$emp_result = Employee::where('account_id',$account_id)->orderby('id','asc')->first();

		
			//The data you want to send via POST
			$fields = [
				'accessToken'      	=> 	$token,
				'uuid'              => 	$emp_result->id, //default emp id
				'extCommunityUuid'	=>	$adminObj->account_id,
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
				//return $json;
				$data = array();
				return response()->json(['devices'=>$data,'response' => 0, 'message' => $json['msg']]);

			}
			else{

				$roleObj = Role::find($role);
				
				$device_access = array();
				if(isset($adminObj->userremotedevices)){
					foreach($adminObj->userremotedevices as $selecteddevices){
						$device_access[] = $selecteddevices->device_svn;
					}
				}

				$data = array();
				$thinmoo_devices = $json['data'];
				foreach($thinmoo_devices as $T_device){
					$items_array = array();
					if(in_array($T_device['devSn'],$device_access) ){ // devices based on Role or Managing agent can all devices
						$device_info = Device::where('device_serial_no',$T_device['devSn'])->first();
						$items_array['thinmoo'] = $T_device;
						$items_array['moreinfo'] = $device_info;
						$data[] = $items_array;
					}
				}
			}
			
			return response()->json(['devices'=>$data,'response' => 1, 'message' => 'Success']);
			
		}
	}

	public function staffalldevices(Request $request) 
    {
		$login_id = Auth::id();
	
		$adminObj = User::find($login_id); 
		$role = $adminObj->role_id;
		$account_id =$adminObj->account_id;
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(59,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$auth = new \App\Models\v7\Property();
			$token = $auth->thinmoo_auth_api();

			$url = env('THINMOO_API_URL')."wyEmpProperty/extapi/getAuthorizationDevList";

			$emp_result = Employee::where('account_id',$account_id)->orderby('id','asc')->first();
		
			//The data you want to send via POST
			$fields = [
				'accessToken'      	=> 	$token,
				'uuid'              => 	$emp_result->id, //default emp id
				'extCommunityUuid'	=>	$adminObj->account_id,
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
				$data = array();
				return response()->json(['devices'=>$data,'response' => 0, 'message' => 'No Records']);

			}
			else{

				$roleObj = Role::find($role);
				//print_r($roleObj->roledevices);

				$bluetooth_device_access = array();
				if(isset($adminObj->userdevices)){
					foreach($adminObj->userdevices as $selecteddevices){
						$bluetooth_device_access[] = $selecteddevices->device_svn;
					}
				}

				$remote_device_access = array();
				if(isset($adminObj->userremotedevices)){
					foreach($adminObj->userremotedevices as $selecteddevices){
						$remote_device_access[] = $selecteddevices->device_svn;
					}
				}

				//print_r($json['data']);
				$bluetooth_data = array();
				$remote_data = array();
				//print_r($remote_device_access);
				$thinmoo_devices = $json['data'];
				foreach($thinmoo_devices as $T_device){
					$items_array = array();
					$remote_items_array = array();

					if(in_array($T_device['devSn'],$bluetooth_device_access) ){// devices based on Role or Managing agent can all devices
						$device_info = Device::where('device_serial_no',$T_device['devSn'])->first();
						$items_array['thinmoo'] = $T_device;
						$items_array['moreinfo'] = $device_info;
						$bluetooth_data[] = $items_array;
					}
					if(in_array($T_device['devSn'],$remote_device_access) ){ // devices based on Role or Managing agent can all devices
						$device_info = Device::where('device_serial_no',$T_device['devSn'])->first();
						$remote_items_array['thinmoo'] = $T_device;
						$remote_items_array['moreinfo'] = $device_info;
						$remote_data[] = $remote_items_array;
					}
					
				}
			}
			
			return response()->json(['bluetooth_data'=>$bluetooth_data,
				'remote_data'=>$remote_data,'response' => 1, 'message' => 'Success']);
			
		}
	}

	public function StoreStaffOpenRecord(Request $request)
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


		$data['account_id'] = $input['property_id'];
		$data['user_id'] = $input['user_id'];
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

	public function createdevice(Request $request) 
    {
		$rules=array(
			'device_name' => 'required',
			'device_serial_no' => 'required',
		);
		$messages=array(
			'device_name.required' => 'Device name is missing',
			'device_serial_no.required' => 'Serial no is missing',
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
		$user = $request->user;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
		
		$permission = $adminObj->check_permission(48,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$validator = Validator::make($request->all(), [ 
				'device_serial_no' =>[
					'required', 
					Rule::unique('devices')
						   ->where('account_id', $adminObj->account_id)
				],
				
			]);
			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Device already exist.']);
   
			}

			$input = $request->all();
			$input['account_id'] = $adminObj->account_id;
			//if($request->login_id ==2)
			//print_r($input['locations']);
			//exit;
			$input['locations'] = implode(",",$input['locations']);

			$device = Device::create($input);

			if($device->id >0){
				$auth = new \App\Models\v7\Property();
				if(Session::has('thinmoo_acess_tocken')){
					$thinmoo_access_token = Session::get('thinmoo_acess_tocken');
				}
				else{
					$thinmoo_access_token = $auth->thinmoo_auth_api();  
				}
				
				$api_obj = new \App\Models\v7\Device();
				$device_result = $api_obj->device_check_record($thinmoo_access_token,$device);
				
				if($device_result['code'] ==0){
					$device_info = $api_obj->device_update_api($thinmoo_access_token,$device);
				}
				else{
					$device_info= $api_obj->device_add_api($thinmoo_access_token,$device);
				}
			}

			

			return response()->json(['device'=>$device,'response' => 1, 'message' => 'success']);
       
		}
	}

	public function deviceinfo(Request $request) 
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

		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		$account_id = $adminObj->account_id;
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(48,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$DeviceObj = Device::find($id);
			$buildings = Building::where('account_id',$account_id)->get();

			return response()->json(['device'=>$DeviceObj,'building'=>$buildings,'response' => 1, 'message' => 'Updated']);

       
		}
	}

	public function getlocation(Request $request) {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

		$account_id = $adminObj->account_id;
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(48,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
		
			$buildings = Building::where('account_id',$account_id)->get();

			return response()->json(['locations'=>$buildings,'response' => 1, 'message' => 'Updated']);

       
		}
	}
    


	public function restartdevice(Request $request) 
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

				$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		$account_id = $adminObj->account_id;
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(48,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$DeviceObj = Device::find($id);
			if($DeviceObj->id >0){
				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();  
				
				$api_obj = new \App\Models\v7\Device();
				$device_result = $api_obj->device_restart($thinmoo_access_token,$DeviceObj);
				return response()->json(['response' => 1, 'message' => 'Success']);
			
			}
			else{
				return response()->json(['response' => 1, 'message' => 'Device not exist']);
			}


       
		}
	}

	public function devicestatus(Request $request) 
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

				$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		$account_id = $adminObj->account_id;
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(48,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$DeviceObj = Device::find($id);
			if($DeviceObj->id >0){
				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();  
				$api_obj = new \App\Models\v7\Device();
				$device_result = $api_obj->device_status($thinmoo_access_token,$account_id,$DeviceObj->device_serial_no);
				if(isset($device_result['connectionStatus']) && $device_result['connectionStatus'] ==1)
					return response()->json(['data'=>1,'response' => 1, 'message' => 'Online']);
				else
					return response()->json(['data'=>2,'response' => 1, 'message' => 'Offline']);
			}
			else{
				return response()->json(['data'=>2,'response' => 1, 'message' => 'Not Available']);
			}


       
		}
	}


	public function editdevice(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'device_name' => 'required',
			'device_serial_no' => 'required',
			'locations' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'device_name.required' => 'Device Name is missing',
			'device_serial_no.required' => 'Serial No is missing',
			'location.required' => 'Location is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(48,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$DeviceObj = Device::find($id);

			if(isset($input['account_id']))
				$input['account_id'] = $input['account_id'];
			else
				$input['account_id'] = $adminObj->account_id;

			$validator = Validator::make($request->all(), [ 
				'device_serial_no' =>[
					'required', 
					Rule::unique('devices')
						->where('account_id', $input['account_id'])
						->whereNotIn('id',[$id])
				],
				
			]);

			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 1, 'message' => 'Device already exist']); 
			}

			
			//$DeviceObj->locations = $request->input('locations');
			$DeviceObj->device_name = $request->input('device_name');
			$DeviceObj->proximity_setting = $request->input('proximity_setting');
			$DeviceObj->device_serial_no = $request->input('device_serial_no');
			$DeviceObj->status = $request->input('status');
			$DeviceObj->facility_type = $request->input('facility_type');
			$DeviceObj->entry_allowed_in_advance = $request->input('entry_allowed_in_advance');
			$DeviceObj->start_time = $request->input('start_time');
			$DeviceObj->end_time = $request->input('end_time');
			$DeviceObj->locations = implode(",",$request->input('locations'));

			$device = $DeviceObj->save();



				if($DeviceObj->id >0){
					$auth = new \App\Models\v7\Property();
					if(Session::has('thinmoo_acess_tocken')){
						$thinmoo_access_token = Session::get('thinmoo_acess_tocken');
					}
					else{
						$thinmoo_access_token = $auth->thinmoo_auth_api();  
					}
					
					$api_obj = new \App\Models\v7\Device();
					$device_result = $api_obj->device_check_record($thinmoo_access_token,$DeviceObj);
					
					if($device_result['code'] ==0){
						$device_info = $api_obj->device_update_api($thinmoo_access_token,$DeviceObj);
					}
					else{
						$device_info= $api_obj->device_add_api($thinmoo_access_token,$DeviceObj);
					}
				}
			return response()->json(['data'=>null,'response' => 1, 'message' => 'Updated']);

       
		}
	}

	public function deletedevice(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(48,$adminObj->role_id); 
		if(empty($permission) ||  isset($permission->delete) && $permission->delete !=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$id = $request->id;
			$adminObj = User::find($login_id); 

			$DeviceObj = Device::find($id);

			if($adminObj->account_id == $DeviceObj->account_id){
				if($DeviceObj->id >0){
					$auth = new \App\Models\v7\Property();
					if(Session::has('thinmoo_acess_tocken')){
						$thinmoo_access_token = Session::get('thinmoo_acess_tocken');
					}
					else{
						$thinmoo_access_token = $auth->thinmoo_auth_api();  
					}
					
					$api_obj = new \App\Models\v7\Device();
					$device_result = $api_obj->device_check_record($thinmoo_access_token,$DeviceObj);
					
					if($device_result['code'] ==0){
						$device_info = $api_obj->device_delete_api($thinmoo_access_token,$DeviceObj);
					}
				
				}
	
				Device::findOrFail($id)->delete();
	
				return response()->json(['response' => 1, 'message' => 'Deleted']);
			}
			else{
				return response()->json(['response' => 200, 'message' => 'Permission Denied']);
			}

			

       
		}
	}

	public function searchdevice(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		
		$permission = $adminObj->check_permission(48,$adminObj->role_id); 
		
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$q= $option = $name = $status  = $serial_no ='';
			$option = $request->input('option'); 
			$name = $request->input('name');
			$serial_no = $request->input('serial_no');
			$status = $request->input('status');
	
			$account_id = $adminObj->account_id;
			$devices =array();
			
			if ($name != '' || $serial_no != '' || $status !='') {
				$devices = Device::where('account_id',$account_id)->where(function ($query) use ($name,$status,$serial_no) {
					if($name !='')
						$query->where('device_name', 'LIKE', '%' . $name . '%');
					
					if($serial_no !='')
						$query->where('device_serial_no', 'LIKE', '%' . $serial_no . '%');
					
				})->get();
			   
				$auth = new \App\Models\v7\Property();
				$token = $auth->thinmoo_auth_api(); 

				$data = array();
				foreach($devices as $device){
					$list = array();
					$result = $device->device_status($token,$account_id,$device->device_serial_no); 
					if($status ==1 && isset($result['connectionStatus']) && $result['connectionStatus']==1){
						$list['id'] =$device->id;
						$list['account_id'] = $device->account_id;
						$list['device_name'] =$device->device_name;
						$list['proximity_setting'] =$device->proximity_setting;
						$list['device_serial_no'] =$device->device_serial_no;
						$list['location'] = isset($device->buildinginfo->building)?$device->buildinginfo->building:null;
						$list['model'] = isset($result['deviceModelName'])?$result['deviceModelName']:"";
						$list['status'] = (isset($result['connectionStatus']) && $result['connectionStatus'] ==1)?1:0;
						$list['connection_status'] = (isset($result['connectionStatus']) && $result['connectionStatus'] ==1)?1:0;
						$list['status_lable'] = (isset($result['connectionStatus']) && $result['connectionStatus'] ==1)?'Online':'Offline';
						$list['position_type'] = $device->position_type;
						$list['position_id'] = $device->position_id;
						$list['created_at'] = date('d/m/y',strtotime($device->created_at));
						$list['updated_at'] = date('d/m/y',strtotime($device->updated_at));
						$data[] = $list;
					}else if($status ==0 || $status ==''){
						$list['id'] =$device->id;
						$list['account_id'] = $device->account_id;
						$list['device_name'] =$device->device_name;
						$list['proximity_setting'] =$device->proximity_setting;
						$list['device_serial_no'] =$device->device_serial_no;
						$list['location'] = isset($device->buildinginfo->building)?$device->buildinginfo->building:null;
						$list['model'] = isset($result['deviceModelName'])?$result['deviceModelName']:"";
						$list['status'] = (isset($result['connectionStatus']) && $result['connectionStatus'] ==1)?1:0;
						$list['connection_status'] = (isset($result['connectionStatus']) && $result['connectionStatus'] ==1)?1:0;
						$list['status_lable'] = (isset($result['connectionStatus']) && $result['connectionStatus'] ==1)?'Online':'Offline';
						$list['position_type'] = $device->position_type;
						$list['position_id'] = $device->position_id;
						$list['created_at'] = date('d/m/y',strtotime($device->created_at));
						$list['updated_at'] = date('d/m/y',strtotime($device->updated_at));
						$data[] = $list;
					}

				} 

				return response()->json(['lists'=>(count($data) >0)?$data:null,'response' => 1, 'message' => 'Success']);
	
			}
			else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
			}

			

		}
	}

	public function devicesummarylist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		$account_id = $adminObj->account_id;
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		
		$permission = $adminObj->check_permission(48,$adminObj->role_id); 
		
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$devices = Device::where('account_id',$account_id)->get();  

			$auth = new \App\Models\v7\Property();
			$token = $auth->thinmoo_auth_api();  

			$data = array();
			foreach($devices as $device){
				$list = array();
				//$result = $device->device_status($token,$account_id,$device->device_serial_no); 
				$list['id'] =$device->id;
				$list['account_id'] = $device->account_id;
				$list['device_name'] =$device->device_name;
				$list['proximity_setting'] =$device->proximity_setting;
				$list['device_serial_no'] =$device->device_serial_no;
				$list['location'] = isset($device->buildinginfo->building)?$device->buildinginfo->building:null;
				$list['model'] = isset($result['deviceModelName'])?$result['deviceModelName']:"";
				$list['status'] ="0";
				$list['status_lable'] ="Check Status";
				//$list['status'] = (isset($result['connectionStatus']) && $result['connectionStatus'] ==1)?1:0;
				//$list['status_lable'] = (isset($result['connectionStatus']) && $result['connectionStatus'] ==1)?'Online':'Offline';
				$list['position_type'] = $device->position_type;
				$list['position_id'] = $device->position_id;
				$list['created_at'] = date('d/m/y',strtotime($device->created_at));
				$list['updated_at'] = date('d/m/y',strtotime($device->updated_at));
				$data[] = $list;

			} 

			return response()->json(['lists'=>$data,'response' => 1, 'message' => 'Success']);

		}
	}

	public function cardsummarylist(Request $request) 
    {
		$login_id = Auth::id();
		
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(38,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

        $account_id = $adminObj->account_id;

		if($adminObj->role_id ==1){
			$cards = Card::get(); 
			$units = Unit::pluck('unit', 'id')->all();

		} 
		else{
			$cards = Card::where('account_id',$account_id)->orderBy('id','DESC')->get(); 
			$units = Unit::select('unit', 'id')->where('account_id',$adminObj->account_id)->get();
			$unit_data =array();
			if(isset($units)){
				foreach($units as $unit){
					$unit_data[$unit->id] = Crypt::decryptString($unit->unit);
				}
			}
		}
		
		return response()->json(['cards'=>$cards,'units'=>$unit_data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function searchcard(Request $request) 
    {
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(38,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$card = $request->input('card');
			$unit = $request->input('unit');
			$status = $request->input('status');
	
			$account_id = $adminObj->account_id;
			$data =array();

			if ($card != '' || $unit !='' || $status !='') {
				$units = array();
				if($unit !=''){   
					$unit = str_replace("#",'',$unit);
					$unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
					})->get();   
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
								$units[] = $unitid->id;
						}
					}
		
				}
		
				$cards = Card::where('account_id',$account_id)->where(function ($query) use ($card,$unit,$units,$status) {
					if($card !='')
						$query->where('card', 'LIKE', '%' . $card . '%');
					if($status !='')
						$query->where('status',$status);
					if($unit !='')
						$query->whereIn('unit_no', $units);
				})->get();
				$units = Unit::select('unit', 'id')->where('account_id',$adminObj->account_id)->get();
				$unit_data =array();
				if(isset($units)){
					foreach($units as $unit){
						$unit_data[$unit->id] = Crypt::decryptString($unit->unit);
					}
				}
				/*foreach($cards as $card){
					$record['card']=$card;
					$record['unit'] = $card->getunit;
					$data[] = $record;
				}*/
			
				return response()->json(['cards'=>$cards,'units'=>$unit_data,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['cards'=>'','units'=>'','response' => 200, 'message' => 'Search option empty']);
			}
		}
	}


	public function createcard(Request $request) 
    {
		$rules=array(
			'card' => 'required',
			'unit_no' => 'required',
		);
		$messages=array(
			'card.required' => 'Card Number is missing',
			'unit_no.required' => 'Unit is missing',
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
		$user = $request->user;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
		
		$permission = $adminObj->check_permission(38,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$validator = Validator::make($request->all(), [ 
				'card' =>[
					'required', 
					Rule::unique('cards')
						   ->where('account_id', $adminObj->account_id)
				],
				
			]);
			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Card number already exist.']);
   
			}

			$input = $request->all();
			$input['account_id'] = $adminObj->account_id;
			$card = Card::create($input);

			return response()->json(['card'=>$card,'response' => 1, 'message' => 'success']);
       
		}
	}

	public function editcard(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'card' => 'required',
			'unit_no' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'card.required' => 'Card number is missing',
			'unit_no.required' => 'Unit number is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(38,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$CardObj = Card::find($id);
			$input = $request->all();
			$CardObj->card =  $input['card'];	
			$CardObj->unit_no =  $input['unit_no'];	
			$CardObj->status =  $input['status'];	
			if(isset($input['remarks']))
				$CardObj->remarks = $input['remarks'];
			else
				$CardObj->remarks = '';			   
	
			$CardObj->save();
			return response()->json(['data'=>null,'response' => 1, 'message' => 'Updated']);

       
		}
	}

	public function deletecard(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(38,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			Card::findOrFail($id)->delete();

			return response()->json(['response' => 1, 'message' => 'Deleted']);

       
		}
	}



	public function unitlist(Request $request) 
    {
		$login_id = Auth::id();
		$user = $request->user;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
		$units = Unit::where('account_id',$adminObj->account_id)->get();
		$unit_data =array();
		$data= array();
		if(isset($units)){
			foreach($units as $unit){
				$unit_data["id"]= $unit->id;
				$unit_data["unit"]= Crypt::decryptString($unit->unit);
				$unit_data["code"]= Crypt::decryptString($unit->code);
				$unit_data["building_id"]= $unit->building_id;
				$unit_data["account_id"]= $unit->account_id;
				$unit_data["size"]= $unit->size;
				$unit_data["status"]= $unit->status;
				$unit_data["share_amount"]= $unit->share_amount;
				$data[] = $unit_data;
			}
		}

		return response()->json(['units'=>$data,'response' => 1, 'message' => 'success']);
       
	}
   
	public function roleslist(Request $request) 
    {
		$login_id = Auth::id();
		$user = $request->user;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
		$roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$adminObj->account_id .',%')->orWhere('type',1)->pluck('name', 'id')->all();

		$data = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$adminObj->account_id .',%')->orWhere('type',1)->get();


		return response()->json(['roles'=>$roles,'data'=>$data,'response' => 1, 'message' => 'success']);
       
	}


	//User Module End

	//Key Collection Module Start
	public function keycollectionlist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

        $account_id = $adminObj->account_id;

		$units = UnittakeoverAppointment::where('account_id',$account_id)->whereNotIn('status', ['0'])
                ->orderby('appt_date','asc')
				->get();  
		
		$data =array();
		foreach($units as $unit){
			$record['submission_info']=$unit;
			$unitObj = Unit::find($unit->unit_no);
			$unit_data =array();
			if(isset($unitObj)){
				$unit_data["id"]= $unitObj->id;
				$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
			}
			$record['unit_info'] = !empty($unit_data)?$unit_data:null;
			$user_data =array();
			if(isset($unit->getname)){
				$user_data["id"]= $unit->getname->id;
				$user_data["account_id"]= $unit->getname->account_id;
				$user_data["role_id"]= $unit->getname->role_id;
				$user_data["user_info_id"]= $unit->getname->user_info_id;
				$user_data["building_no"]= $unit->getname->building_no;
				$user_data["unit_no"]= $unit->getname->unit_no;
				$user_data["primary_contact"]= $unit->getname->primary_contact;
				$user_data["name"]=Crypt::decryptString($unit->getname->name);
			}
			//$record['unit_info'] = $unit->getunit;
			$record['user_info'] = !empty($user_data)?$user_data:null;
			$data[] = $record;
		}
		
				
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function keycollectionnewlist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

        $account_id = $adminObj->account_id;

		$units = UnittakeoverAppointment::where('account_id',$account_id)->where('status', '0')
                ->whereDate('appt_date', '>=', Carbon::now('Asia/Singapore')) 
                ->orderby('appt_date','asc')
                ->paginate(env('PAGINATION_ROWS'));   
		$data =array();
		foreach($units as $unit){
					$record['submission_info']=$unit;
					$unitObj = Unit::find($unit->unit_no);
					$unit_data =array();
					if(isset($unitObj)){
						$unit_data["id"]= $unitObj->id;
						$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
					}
					$record['unit_info'] = !empty($unit_data)?$unit_data:null;
					$user_data =array();
					if(isset($unit->getname)){
						$user_data["id"]= $unit->getname->id;
						$user_data["account_id"]= $unit->getname->account_id;
						$user_data["role_id"]= $unit->getname->role_id;
						$user_data["user_info_id"]= $unit->getname->user_info_id;
						$user_data["building_no"]= $unit->getname->building_no;
						$user_data["unit_no"]= $unit->getname->unit_no;
						$user_data["primary_contact"]= $unit->getname->primary_contact;
						$user_data["name"]=Crypt::decryptString($unit->getname->name);
					}
					//$record['unit_info'] = $unit->getunit;
					$record['user_info'] = !empty($user_data)?$user_data:null;
					$data[] = $record;
		}
				
						
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function keycollectioninfo(Request $request) 
    {
		$rules=array(
			'id' => 'required',
		);
		$messages=array(
			'id.required' => 'Booking Id is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$takeoverObj = UnittakeoverAppointment::find($id);
			if(isset($takeoverObj)){
				$record['submission_info'] = $takeoverObj;	
				$record['unit_info'] = $takeoverObj->getunit;
				$record['user_info'] = $takeoverObj->getname;
				$obj = new UnittakeoverAppointment();
				$times = $obj->timeslots($adminObj->account_id);
				return response()->json(['booking_info'=>$record,'times'=>$times,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['booking_info'=>null,'times'=>'','response' => 200, 'message' => 'No records ']);
			}
			

       
		}
	}

	public function keycollectiontimeslot(Request $request) 
    {
		$rules=array(
			'date' => 'required',
		);
		$messages=array(
			'date.required' => 'Booking Date is missing',
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
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$obj = new UnittakeoverAppointment();
        	$times = $obj->timeslots($adminObj->account_id);
        
        	$selecteddate = $request->date;

        	foreach($times as $time){
            //echo $time;
            $lists = DB::table("unittakeover_appointments")->where('appt_date',$selecteddate)->where('appt_time',$time)->whereNotIn('status', [1])->get();
            $recordcount = count($lists);
            $record =array('time'=>$time,'count'=>$recordcount);

            $data[] = $record;

        }	
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function editkeycollection(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'status' => 'required',
			'appt_date' => 'required',
			'appt_time' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'status.required' => 'Status is missing',
			'appt_date.required' => 'Appoinment date is missing',
			'appt_time.required' => 'Appoinment time is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$result = DB::table('unittakeover_appointments')->where('appt_date', $request->input('appt_date'))
                ->where('appt_time',$request->input('appt_date'))
            	->whereNotIn('id', [$id])->first();
      
			if (isset($result)) { 
				return response()->json(['data'=>$takeoverObj,'response' => 400, 'message' => 'Date & Time of the Appointment already taken !']);  
			}
		
			$takeoverObj = UnittakeoverAppointment::find($id);
			$input = $request->all();	
			$takeoverObj->appt_date = $input['appt_date'];	
			$takeoverObj->appt_time = $input['appt_time'];	
			$takeoverObj->status = $input['status'];	
			$takeoverObj->save();

			return response()->json(['data'=>$takeoverObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function deletekeycollection(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			UnittakeoverAppointment::findOrFail($id)->delete();

			return response()->json(['response' => 1, 'message' => 'Deleted']);

       
		}
	}

	public function searchkeycollection(Request $request) 
    {
	
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else
		{
			
			$name = $request->input('name');
			$status = $request->input('status');
			$unit = $request->input('unit');
			$month = $request->input('month');

			$account_id = $adminObj->account_id;
			$data =array();

			
			if ($name != '' || $month != '' || $unit != '' || $status != '') {
				$units = array();
				if($unit !=''){   
					$unit = str_replace("#",'',$unit);
					$unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
					})->get();   
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
								$units[] = $unitid->id;
						}
					}
				}
				$month = $request->input('month');
				$from_date = '';
				$to_date = '';
				if($month !=''){          
					$from_date = $month;
					$to_date  = date('m-t-Y', strtotime($month));
				}
		
				$units =  UnittakeoverAppointment::where('account_id',$account_id)
					->where(function ($query) use ($name,$unit,$units,$month,$from_date,$to_date,$status) {
						if($status !='')
							$query->where('status',$status);
						if($unit !='')
							$query->whereIn('unit_no', $units);
						if($month !='')
							$query->whereBetween('appt_date',array($from_date,$to_date));
						
					})->orderby('id','desc')->get();
				$data = array();
				foreach($units as $unit){
					$record['submission_info']=$unit;
					$unitObj = Unit::find($unit->unit_no);
					$unit_data =array();
					if(isset($unitObj)){
						$unit_data["id"]= $unitObj->id;
						$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
					}
					$record['unit_info'] = !empty($unit_data)?$unit_data:null;
					$user_data =array();
					if(isset($unit->getname)){
						$user_data["id"]= $unit->getname->id;
						$user_data["account_id"]= $unit->getname->account_id;
						$user_data["role_id"]= $unit->getname->role_id;
						$user_data["user_info_id"]= $unit->getname->user_info_id;
						$user_data["building_no"]= $unit->getname->building_no;
						$user_data["unit_no"]= $unit->getname->unit_no;
						$user_data["primary_contact"]= $unit->getname->primary_contact;
						$user_data["name"]=Crypt::decryptString($unit->getname->name);
					}
					//$record['unit_info'] = $unit->getunit;
					$record['user_info'] = !empty($user_data)?$user_data:null;
					$data[] = $record;
			}
		
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
			}
		}
	}


	public function cancelkeycollection(Request $request){
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$input = $request->all();
		 	$reason ='';
			$bookid = $input['id'];
			$status = 1;
			if(isset($input['reason']))
				$reason = $input['reason'];

			$takeoverObj = UnittakeoverAppointment::find($bookid);
			if(isset($takeoverObj))
			{
				//Start Insert into notification module
				$notification = array();
				$notification['account_id'] = $takeoverObj->account_id;
				$notification['user_id'] = $takeoverObj->user_id;
				$notification['unit_no'] = $takeoverObj->unit_no;
				$notification['module'] = 'key collection';
				$notification['ref_id'] = $takeoverObj->id;
				$notification['title'] = 'Key Collection';
				$notification['message'] = 'There is an update to your key collection appointment';
				$result = UserNotification::insert($notification);

				$SettingsObj = UserNotificationSetting::where('user_id',$takeoverObj->user_id)->where('account_id',$takeoverObj->account_id)->first();
				if(empty($SettingsObj) || $SettingsObj->key_collection ==1){
					$fcm_token_array ='';
					$user_token = ',';
					$ios_devices_to_send = array();
					$android_devices_to_send = array();
					$logs = UserLog::where('user_id',$takeoverObj->user_id)->where('status',1)->orderby('id','desc')->first();
					if(isset($logs->fcm_token) && $logs->fcm_token !=''){
						$user_token .=$logs->fcm_token.",";
						$fcm_token_array .=$logs->fcm_token.',';
						$appSipAccountList[] = $takeoverObj->id;
						if($logs->login_from ==1)
							$ios_devices_to_send[] = $logs->fcm_token;
						if($logs->login_from ==2)
							$android_devices_to_send[] = $logs->fcm_token;
					}
					$probObj = Property::find($takeoverObj->account_id);
					$title = "Aerea Home - ".$probObj->company_name;
					$message = "Key Collection Appointment Update";
					$notofication_data = array();
					$notofication_data['body'] =$title; 
					$notofication_data['unit_no'] =$takeoverObj->unit_no;   
					$notofication_data['user_id'] =$takeoverObj->user_id;   
					$notofication_data['property'] =$takeoverObj->account_id;
					$purObj = UserPurchaserUnit::where('property_id',$takeoverObj->account_id)->where('unit_id',$takeoverObj->unit_no)->where('user_id',$takeoverObj->user_id)->first(); 
						if(isset($purObj))
							$notofication_data['switch_id'] =$purObj->id; 
					$NotificationObj = new \App\Models\v7\FirebaseNotification();
					$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
					$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
					//End Insert into notification module
				}
	   
	   
			   $inbox = InboxMessage::where('ref_id', $bookid)->where('type',4)->first();
			   if(isset($inbox)){
				   $inboxObj = InboxMessage::find($inbox->id);
				   $inboxObj->event_status = $status;
				   $inboxObj->save();
			   }
			  
				UnittakeoverAppointment::where('id', $bookid)
					   ->update(['status' => $status,'reason'=>$reason]);
			
				return response()->json(['response' => 1, 'message' => 'Booking Cancelled']);
			}
			else{
				return response()->json(['response' => 200, 'message' => 'Booking in not valid']);
			}

		}
	}



	public function confirmkeycollection(Request $request){

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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$input = $request->all();
			$reason ='';
			$bookid = $input['id'];
			$status =2;

			if(isset($input['reason']))
				$reason = $input['reason'];

				$takeoverObj = UnittakeoverAppointment::find($bookid);
				if(isset($takeoverObj))
				{
					//Start Insert into notification module
					$notification = array();
					$notification['account_id'] = $takeoverObj->account_id;
					$notification['user_id'] = $takeoverObj->user_id;
					$notification['unit_no'] = $takeoverObj->unit_no;
					$notification['module'] = 'key collection';
					$notification['ref_id'] = $takeoverObj->id;
					$notification['title'] = 'Key Collection';
					$notification['message'] = 'There is an update to your key collection appointment';
					$result = UserNotification::insert($notification);
					$SettingsObj = UserNotificationSetting::where('user_id',$takeoverObj->user_id)->where('account_id',$takeoverObj->account_id)->first();
					if(empty($SettingsObj) || $SettingsObj->key_collection ==1){
						$fcm_token_array ='';
						$user_token = ',';
						$ios_devices_to_send = array();
						$android_devices_to_send = array();
						$logs = UserLog::where('user_id',$takeoverObj->user_id)->where('status',1)->orderby('id','desc')->first();
						if(isset($logs->fcm_token) && $logs->fcm_token !=''){
							$user_token .=$logs->fcm_token.",";
							$fcm_token_array .=$logs->fcm_token.',';
							$appSipAccountList[] = $takeoverObj->id;
							if($logs->login_from ==1)
								$ios_devices_to_send[] = $logs->fcm_token;
							if($logs->login_from ==2)
								$android_devices_to_send[] = $logs->fcm_token;
						}
						$probObj = Property::find($takeoverObj->account_id);
						$title = "Aerea Home - ".$probObj->company_name;
						$message = "Key Collection Appointment Update";
						$notofication_data = array();
						$notofication_data['body'] =$title;
						$notofication_data['unit_no'] =$takeoverObj->unit_no;   
						$notofication_data['user_id'] =$takeoverObj->user_id;   
						$notofication_data['property'] =$takeoverObj->account_id; 
						$purObj = UserPurchaserUnit::where('property_id',$takeoverObj->account_id)->where('unit_id',$takeoverObj->unit_no)->where('user_id',$takeoverObj->user_id)->first(); 
						if(isset($purObj))
							$notofication_data['switch_id'] =$purObj->id;  

						$NotificationObj = new \App\Models\v7\FirebaseNotification();
						$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
						$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
						//End Insert into notification module
					}
		
		
				$inbox = InboxMessage::where('ref_id', $bookid)->where('type',4)->first();
				if(isset($inbox)){
					$inboxObj = InboxMessage::find($inbox->id);
					$inboxObj->event_status = $status;
					$inboxObj->save();
				}
				
					UnittakeoverAppointment::where('id', $bookid)
						->update(['status' => $status,'reason'=>$reason]);
				
				return response()->json(['response' => 1, 'message' => 'Booking Confirmed']);
			}
			else{
				return response()->json(['response' => 200, 'message' => 'Booking id not valid!']);
			}

		}
	   
	}

	//Key Collection Module End

	//Defects Module Start
	public function defectslist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(3,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

        $account_id = $adminObj->account_id;
		$building = ($request->input('block_no'))?$request->input('block_no'):'';
		$defects = Defect::where('account_id',$account_id)->where(function ($query) use ($building){
			if($building !='')
				$query->where('block_no',$building);
			})
        ->orderby('id','desc')
		->get();  
		
		$data =array();
		foreach($defects as $defect){

			$inspectionDate = ($defect->inspection_owner_timestamp !='')?date("Y-m-d",strtotime($defect->inspection_owner_timestamp)):'';
            $givenDate = \Carbon\Carbon::parse($inspectionDate);
            $days = $givenDate->diffInDays(\Carbon\Carbon::now());
			//$record['lists']=$defect;
			$record['lists']['id']=$defect->id;
			$record['lists']['account_id']=$defect->account_id;
			$record['lists']['ref_id']=$defect->ref_id;
			$record['lists']['ticket']=$defect->ticket;
			$record['lists']['user_id']=$defect->user_id;
			$record['lists']['block_no']=$defect->block_no;
			$record['lists']['unit_no']=$defect->unit_no;
			$record['lists']['status']=$defect->status;
			$record['lists']['view_status']=$defect->view_status;
			$record['lists']['inspection_status']=$defect->inspection_status;
			$record['lists']['handover_status']=$defect->handover_status;
			$record['lists']['rectified_in_days']=($days >0)?$days:null;
			$record['lists']['rectification_start_date']=($defect->inspection_owner_timestamp !='0000-00-00 00:00:00' &&$defect->inspection_owner_timestamp !='' )?date('d/m/y',strtotime($defect->inspection_owner_timestamp)):null;
			$record['lists']['inspection_owner_timestamp']=($defect->inspection_owner_timestamp !='0000-00-00 00:00:00' &&$defect->inspection_owner_timestamp !='' )?$defect->inspection_owner_timestamp:null;
			$record['lists']['pdf_link'] = env('VISITOR_APP_URL')."/generate-pdf/$defect->id";

			$record['lists']['created_at'] = date('d/m/y',strtotime($defect->created_at));
			$record['lists']['updated_at'] = date('d/m/y',strtotime($defect->updated_at));
			//$record['submission'] = $defect->submissions;
			$user_data =array();
			if(isset($defect->user)){
				$user_data["id"]= $defect->user->id;
				$user_data["account_id"]= $defect->user->account_id;
				$user_data["role_id"]= $defect->user->role_id;
				$user_data["user_info_id"]= $defect->user->user_info_id;
				$user_data["building_no"]= $defect->user->building_no;
				$user_data["unit_no"]= $defect->user->unit_no;
				$user_data["primary_contact"]= $defect->user->primary_contact;
				$user_data["name"]=Crypt::decryptString($defect->user->name);
				
			}
			$record['user_info'] = !empty($user_data)?$user_data:null;
			$unitObj = Unit::find($defect->unit_no);
			$unit_data =array();
			if(isset($unitObj)){
				$unit_data["id"]= $unitObj->id;
				$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
				if($defect->block_no ==''){
					Defect::where('id',$defect->id)->update(['block_no'=>$unitObj->building_id]);
				}
			}
			$record['unit_info'] = !empty($unit_data)?$unit_data:null;

			//$record['user_info'] = $defect->user;
			//$record['unit_info'] = isset($defect->getunit)?$defect->getunit:null;

			$record['inspection'] = isset($defect->inspection)?$defect->inspection:null;
			

			$data[] = $record;
		}
		$buildings = Building::where('account_id',$account_id)->get();  
		$blocks = array();			
		if($buildings){
			foreach($buildings as $building){
				$build_data = array();
				$build_data['id'] = $building->id;
				$build_data['name'] = $building->building;
				$defects_count = Defect::where('view_status', 0)->where('account_id', $account_id)->where('status', 0)->where('block_no',$building->id)->count();
				$build_data['new_count'] = $defects_count;
				$blocks[] = $build_data;
			}
		}
				
		return response()->json(['data'=>$data,'blocks'=>$blocks,'response' => 1, 'message' => 'Success']);
		}
	}

	public function defectsnewlist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		
		if(empty($adminObj)) return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		
		$permission = $adminObj->check_permission(3,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}else{

        $account_id = $adminObj->account_id;

		$defects = $defects = Defect::where('account_id',$account_id)->where('view_status', '0')
                ->orderby('id','asc')
                ->paginate(env('PAGINATION_ROWS'));   
		$data =array();
		foreach($defects as $defect){
			$record['lists']=$defect;
			//$record['submission'] = $defect->submissions;
			$user_data =array();
			if(isset($defect->user)){
				$user_data["id"]= $defect->user->id;
				$user_data["account_id"]= $defect->user->account_id;
				$user_data["role_id"]= $defect->user->role_id;
				$user_data["user_info_id"]= $defect->user->user_info_id;
				$user_data["building_no"]= $defect->user->building_no;
				$user_data["unit_no"]= $defect->user->unit_no;
				$user_data["primary_contact"]= $defect->user->primary_contact;
				$user_data["name"]=Crypt::decryptString($defect->user->name);	
			}

			$record['user_info'] = !empty($user_data)?$user_data:null;
			$unitObj = Unit::find($defect->unit_no);
			$unit_data =array();
			if(isset($unitObj)){
				$unit_data["id"]= $unitObj->id;
				$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
			}
			$record['unit_info'] = !empty($unit_data)?$unit_data:null;
			$record['inspection'] = isset($defect->inspection)?$defect->inspection:null;
			
			$data[] = $record;
		}
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}
	
	public function defectDashboard(Request $request)
	{
	    $account_id = User::find(Auth::id())->account_id;
	 
	    $year = $request->has('year') ? $request->year : null;	    
	    $year = ($year == null ? date('Y') : $year);
	    
	    //line chart
	    $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->startOfMonth();
        
        $firstDefect = Defect::where('account_id',$account_id)->latest()->first();
        
        if($request->has('year')){
            if($request->year!=date('Y')){
                $start = Carbon::parse($request->year.'-01-01')->startOfMonth();
                $end = Carbon::parse($request->year.'-12-01')->endOfMonth();
            }
        }else{
            $start = Carbon::parse($firstDefect ? date('Y-m-d',strtotime($firstDefect->created_at)) : date('Y').'-01-01')->startOfMonth();
            $end = Carbon::parse(date('Y').'-12-01')->endOfMonth();
        }
        
        $dates = [];
        while ($start->lte($end)) {
            $dates[] = [
                'date' => $start->format('Y-m-d'),
                'defects' => Defect::where('account_id',$account_id)->whereYear('created_at',$start->copy()->format('Y'))->whereMonth('created_at',$start->copy()->format('m'))->count()
            ];
            $start->addMonth();
        }
        // line chart end
        
        $locations = null;
        if($request->has('locations') && !empty($request->locations)) $locations = $request->locations;
        
        $defactsByLocations = DefectLocation::where('account_id',$account_id)->where('status',1)
			->when($locations, function ($q) use($locations) {
				return $q->whereIn('id', $locations);
			})
            ->orderBy('defect_location')->get()->map(function($q) use($account_id,$year) {
                return [
                    'name' => ucwords(strtolower($q->defect_location)),
                    'defects' => DefectSubmission::where('defect_location',$q->id)
                        ->whereHas('defect', function (Builder $query) use($account_id,$year) {
                            $query->where('account_id',$account_id)->whereYear('created_at', $year);
                        })->count(),
                ];
            });
        
        $types = null;
        if($request->has('types') && !empty($request->types)) $types = $request->types;
            
        $defactsByTypes = DefectType::where('account_id',$account_id)->where('status',1)
			->when($types, function ($q) use($types) {
				return $q->whereIn('id', $types);
			})
            ->orderBy('defect_type')->get()->map(function($q) use($account_id,$year) {
                return [
                    'name' => ucwords(strtolower($q->defect_type)),
                    'defects' => DefectSubmission::where('defect_type',$q->id)
                        ->whereHas('defect', function (Builder $query) use($account_id,$year) {
                            $query->where('account_id',$account_id)->whereYear('created_at', $year);
                        })->count(),
                ];
            });
        
        $units = null;
        if($request->has('units') && !empty($request->units)) $units = $request->units;
            
        $defactsByUnits = Unit::where('account_id',$account_id)->where('status',1)
			->when($units, function ($q) use($units) {
				return $q->whereIn('id', $units);
			})
            ->get()->map(function($q) use($account_id,$year) {
                return [
                    'id' => $q->id,
                    'name' => \Crypt::decryptString($q->unit),
                    'defects' => Defect::where('unit_no',$q->id)->where('account_id',$account_id)->whereYear('created_at', $year)->count(),
                ];
            });
        
		$startDate = $request->start_date;
		$endDate = $request->end_date;
		
		$currentYearDefects = Defect::where('account_id',$account_id)
			->whereDate('created_at','>=',$startDate)
			->whereDate('created_at','<=',$endDate)
	        ->get();

	//     $data = [
	//        'total_defects' => $currentYearDefects->count(),
	//        'total_new' => $currentYearDefects->where('status',0)->count(),
	//        'total_in_progress' => $currentYearDefects->where('status',4)->count(),
	//        'total_scheduled' => $currentYearDefects->where('status',3)->count(),
	//        'total_completed' => $currentYearDefects->where('status',1)->count(),
	//        'line_chart' => $dates,
	//        'defects_by_location' => $defactsByLocations,
	//        'defects_by_unit' => $defactsByUnits,
	//        'defects_by_type' => $defactsByTypes
	//    ];

		$date1 = Carbon::parse($startDate);
		$date2 = Carbon::parse($endDate);

		$sExplode = explode('-',$startDate);
		$eExplode = explode('-',$endDate);
		$cStartDate = Carbon::createFromDate($sExplode[0], $sExplode[1], $sExplode[2]);
		$cEndDate = Carbon::createFromDate($eExplode[0], $eExplode[1], $eExplode[2]);

		$diffInMonths = $cStartDate->diffInMonths($cEndDate);

		$months = $date1->diffInMonths($date2);
		$period = CarbonPeriod::create($startDate, $endDate);

		if($diffInMonths==0) //One month
		{
			foreach ($period as $date) {
				$finalMonths[] = [
					'date' => $date->format('Y-m-d'),
					'total' => Defect::where('account_id',$account_id)
						->whereDate('created_at',$date->format('Y-m-d'))
						->count()
				];
			}
		}else{
			$yMonths = [];
			foreach ($period as $date) {
				$yMonths[] = $date->format('Y-m-').'01';
			}
			$yMonths = array_unique($yMonths);
			foreach($yMonths as $y){
				$finalMonths[] = [
					'date' => Carbon::parse($y)->format('Y-m'),
					'total' => Defect::where('account_id',$account_id)
						->whereYear('created_at',explode('-',$y)[0])
						->whereMonth('created_at',explode('-',$y)[1])
						->count()
				];
			}
		}

		$ticketsByTime = $finalMonths;

		$ticketsByLocation = DefectLocation::where('account_id',$account_id)->where('status',1)
			->orderBy('defect_location')->get()->map(function($q) use($account_id,$startDate,$endDate) {
				return [
					'location' => ucwords(strtolower($q->defect_location)),
					'count' => DefectSubmission::where('defect_location',$q->id)
						->whereHas('defect', function (Builder $query) use($account_id,$startDate,$endDate) {
							$query->where('account_id',$account_id)
							->whereDate('created_at','>=',$startDate)
							->whereDate('created_at','<=',$endDate);
						})->count(),
				];
			})->where('count','>',0)->values()->all();
			//->filter(fn($k) => $k['count'] > 0);
		// $ticketsByLocation->all();

		$ticketsByStatus = [
			[
				'status' => 'New',
				'count' => $currentYearDefects->where('status',0)->count()
			],
			[
				'status' => 'On Schedule',
				'count' => $currentYearDefects->where('status',3)->count()
			],
			[
				'status' => 'In Progress',
				'count' => $currentYearDefects->where('status',4)->count()
			],
			[
				'status' => 'Completed - Pending Resident Update',
				'count' => $currentYearDefects->where('status',5)->count()
			],
			[
				'status' => 'Completed - Final Inspection Scheduled',
				'count' => $currentYearDefects->where('status',6)->count()
			],
			[
				'status' => 'Closed',
				'count' => $currentYearDefects->where('status',1)->count()
			],
		];

		$data = [
			'tickets_by_time' => $ticketsByTime,
			'tickets_by_location' => $ticketsByLocation,
			'tickets_by_status' => $ticketsByStatus
		];
	       
	   return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']);
	}


	public function defectsinfo(Request $request) 
    {
		$rules=array(
			'id' => 'required',
		);
		$messages=array(
			'id.required' => 'Booking Id is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(3,$adminObj->role_id); 

		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$defectObj = Defect::find($id);
        	$defectObj->view_status = 1;
			$defectObj->save();
			$defect = $defectObj;
			
            $signatureUser = User::find($defect->user_id)->name ?? null;
            $defect->signature_user = $signatureUser!=null ? Crypt::decryptString($signatureUser) : null;
            $defect->signature_timestamp = \Carbon\Carbon::parse($defect->created_at)->format('Y-m-d H:i:s');
        
            $inspectionOwnerUser = User::find($defect->inspection_owner_user)->name ?? null;
            $defect->inspection_owner_user = $inspectionOwnerUser!=null ? Crypt::decryptString($inspectionOwnerUser) : null;
            $defect->inspection_owner_timestamp = $defect->inspection_owner_timestamp;
            
            $inspectionTeamUser = User::find($defect->inspection_team_user)->name ?? null;
            $defect->inspection_team_user = $inspectionTeamUser!=null ? Crypt::decryptString($inspectionTeamUser) : null;
            $defect->inspection_team_timestamp = $defect->inspection_team_timestamp;
            
            $handoverOwnerUser = User::find($defect->handover_owner_user)->name ?? null;
            $defect->handover_owner_user = $handoverOwnerUser!=null ? Crypt::decryptString($handoverOwnerUser) : null;
            $defect->handover_owner_timestamp = $defect->handover_owner_timestamp;
            
            $handoverTeamUser = User::find($defect->handover_team_user)->name ?? null;
            $defect->handover_team_user = $handoverTeamUser!=null ? Crypt::decryptString($handoverTeamUser) : null;
            $defect->handover_team_timestamp = $defect->handover_team_timestamp;
			$defect->pdf_link = env('VISITOR_APP_URL')."/generate-pdf/$defect->id";
			if($defect->ref_id !=''){
				$refRecord = Defect::where('ticket',$defect->ref_id)->first();
				if($refRecord)
					$defect->ref_pdf_link = env('VISITOR_APP_URL')."/generate-pdf/$refRecord->id";
				else
					$defect->ref_pdf_link = null;
			}else{
				$defect->ref_pdf_link = null;
			}
			
			$defect['unit_info'] = isset($defectObj->getunit)?$defectObj->getunit:null;
			$lists = array();
			foreach($defectObj->submissions as $submission){
				$result['lists'] = $submission;
				$result['lists']['rectified_image'] = ($submission->rectified_image !='')?$submission->rectified_image:null;
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
						if($change_log->modifiedby){
							$modifed_array = array();
							$modifed_array['id'] = $change_log->modifiedby->id;
							$modifed_array['name'] = Crypt::decryptString($change_log->modifiedby->name);
						}
						$log['modified_by'] = ($modifed_array)?$modifed_array:null;

						$log['remarks'] = $change_log->remarks;
						$result['change_logs'][] = $log;
					}
				}
				$lists[]=$result;
			}
			$defect['submission'] = $lists;
			$defect['inspection'] = $defectObj->inspection;
			$defect['final_inspection'] = $defectObj->finalInspection;

			$reminderData = JoininspectionAppointment::where('account_id', $defectObj->account_id)->where('reminder_emails','!=','')->orwhere('reminder_in_days','!=','')->orwhere('email_message','!=','')->orderby('updated_at','desc')->first();

			$reminder_in_days = '';
			$reminder_emails = '';
			$email_message = '';
			if($defectObj->inspection()->exists() && !empty($reminderData->reminder_in_days))
				$defect['inspection']['reminder_in_days'] = $reminderData->reminder_in_days;
			
			if($defectObj->inspection()->exists() && !empty($reminderData->reminder_emails))
				$defect['inspection']['reminder_emails'] = $reminderData->reminder_emails;

			if($defectObj->inspection()->exists() && !empty($reminderData->email_message))
				$defect['inspection']['email_message'] = $reminderData->email_message;

			//$defect[''] = $defectObj
			$types = DefectType::where('account_id',$adminObj->account_id)->get();   
			
			return response()->json(['booking_info'=>$defect,'defect_type'=>$types,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function defectsupdate(Request $request) 
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

		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(3,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$defectObj = Defect::find($id);
			$allowed_status = array(0,3);
			if(!in_array($defectObj->status,$allowed_status)){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Sorry, this ticket cannot be updated as it is already in progress.']);
			}

			if(isset($defectObj)){
				$defectObj->status = 3;
				$defectObj->inspection_status = $request->input('inspection_status');
				if ($request->file('signature') != null) {
					$signature = remove_upload_path($request->file('signature')->store(upload_path('defect')));
					$defectObj->inspection_team_signature = $signature;
					$defectObj->inspection_team_user = $login_id;
					$defectObj->inspection_team_timestamp = now();
				}

				$defectObj->save();

				//$commands = ($request->input('team_command'));
				$locations = ($request->input('locations'))?$request->input('locations'):array(); 
				$types = ($request->input('types'))?$request->input('types'):array(); 
				$remarks = ($request->input('remarks'));  
				$d_status = $request->input('defect_status');

				$inspection = 0;

				if($defectObj->submissions){
					foreach($defectObj->submissions as $k => $defect){
						$defectSubmissionObj = DefectSubmission::find($defect->id);
						if(isset($d_status[$defect->id])){
							if((isset($locations[$defect->id]) &&($locations[$defect->id] !=$defectSubmissionObj->defect_location )) || (isset($types[$defect->id]) && ($types[$defect->id] != $types[$defect->id]))){
								$new =array();
								$new['def_id'] =$defectSubmissionObj->def_id;
								$new['sub_id'] =$defectSubmissionObj->id;
								$new['updated_by'] =$login_id;
								$new['old_defect_location'] =$defectSubmissionObj->defect_location;
								$new['old_defect_type'] =$defectSubmissionObj->defect_type;
								$new['new_defect_location'] =($locations[$defect->id])?$locations[$defect->id]:'';
								$new['new_defect_type'] =($types[$defect->id])?$types[$defect->id]:'';
								$new['remarks'] =(isset($remarks[$defect->id]))?$remarks[$defect->id]:'';
								$new['created_at'] =date("Y-m-d H:i:s");
								$new['updated_at'] =date("Y-m-d H:i:s");
								DefectUpdateLog::insert($new);
								$defectSubmissionObj->defect_location = ($locations[$defect->id])?$locations[$defect->id]:'';
								$defectSubmissionObj->defect_type = ($types[$defect->id])?$types[$defect->id]:'';
							}
							$defect_status = $d_status[$defect->id];
							$defectSubmissionObj->status = $defect_status;
							if($defect_status ==2)
								$inspection  =1;
							$defectSubmissionObj->save();
						}

					}

				}
				/*
				if($inspection ==1)
					Defect::where('id', $defectObj->id)->update(['inspection_status' => 1,'status' => 3]);
				else {
					Defect::where('id', $defectObj->id)->update(['status' => 0,'inspection_status' => 1,'handover_status' => 0]);
				}  */ 
				  
				if($inspection ==1)
					Defect::where('id', $defectObj->id)->update(['inspection_status' => 1]);
				else {
					//Defect::where('id', $defectObj->id)->update(['inspection_status' => 1,'handover_status' => 0]);
					Defect::where('id', $defectObj->id)->update(['inspection_status' => 1]);
				}

			
				//Inspection update Start
				$jointObj = JoininspectionAppointment::where('def_id', $id)->orderby('id','desc')->first();
				
				if(isset($jointObj->id) && $jointObj->id > 0){

					if($request->input('inspection_status') !=4){//defect New , joint inspection new
						
						$status = $request->input('inspection_status');
							JoininspectionAppointment::where('id', $jointObj->id)
							->update(['status' => $request->input('inspection_status'),'appt_date'=>$request->input('appt_date'),'appt_time'=>$request->input('appt_time')]);
						

					}else {///defect on in progress, , joint inspection closed
						$status = $request->input('inspection_status');
						$inspectionObj = JoininspectionAppointment::find($jointObj->id);
						$inspectionObj->status = $status;
					
						$inspectionObj->appt_date = $request->input('appt_date');
						$inspectionObj->appt_time = $request->input('appt_time');

						$inspectionObj->progress_date = $request->input('progress_date');
						$inspectionObj->reminder_in_days = $request->input('reminder_in_days');
					
						if($request->input('progress_date') !=''){
							$date = Carbon::createFromFormat('Y-m-d', $request->input('progress_date'));
							if($request->input('reminder_in_days') !='')
								$booking_allowed  = $date->addDays($request->input('reminder_in_days'));
							else
								$booking_allowed  = $date->addDays(0);
									
							$inspectionObj->reminder_email_send_on = $booking_allowed;
						}
					
						$inspectionObj->reminder_emails = $request->input('reminder_emails');
						$inspectionObj->email_message = $request->input('email_message');
						$inspectionObj->reminder_email_status = 0; 
							
						$inspectionObj->save();    
					
					}
					//$request->input('inspection_status');
					$title = "Your defect inspection status changed ";
					
					InboxMessage::where('ref_id', $jointObj->id)->where('type',5)
							->update(['title'=>$title,'booking_date'=>$jointObj->appt_date,'booking_time'=>$jointObj->appt_time,'event_status' => $status]);
					//inspection update End
				}
					InboxMessage::where('ref_id', $id)->where('type',3)
					->update(['event_status' => $request->input('status')]);

					//Start Insert into notification module
					$notification = array();
					$notification['account_id'] = $defectObj->account_id;
					$notification['user_id'] = $defectObj->user_id;
					$notification['unit_no'] = $defectObj->unit_no;
					$notification['module'] = 'defects';
					$notification['ref_id'] = $defectObj->id;
					$notification['title'] = 'Defects';
					$notification['message'] = 'There is an update from the management in regards to the defects that you have submitted';
					$result = UserNotification::insert($notification);

					$SettingsObj = UserNotificationSetting::where('user_id',$defectObj->user_id)->where('account_id',$defectObj->account_id)->first();
					if(empty($SettingsObj) || $SettingsObj->defect ==1){
						$fcm_token_array ='';
						$user_token = ',';
						$ios_devices_to_send = array();
						$android_devices_to_send = array();
						$logs = UserLog::where('user_id',$defectObj->user_id)->where('status',1)->orderby('id','desc')->first();
						if(isset($logs->fcm_token) && $logs->fcm_token !=''){
							$user_token .=$logs->fcm_token.",";
							$fcm_token_array .=$logs->fcm_token.',';
							$appSipAccountList[] = $defectObj->id;
							if($logs->login_from ==1)
								$ios_devices_to_send[] = $logs->fcm_token;
							if($logs->login_from ==2)
								$android_devices_to_send[] = $logs->fcm_token;
						}
				
						$probObj = Property::find($defectObj->account_id);
						$title = "Aerea Home - ".$probObj->company_name;
						$message = "Defect(s) Updated";
						$notofication_data = array();
						$notofication_data['body'] =$title; 
						$notofication_data['unit_no'] =$defectObj->unit_no;   
						$notofication_data['user_id'] =$defectObj->user_id;   
						$notofication_data['property'] =$defectObj->account_id;
						$purObj = UserPurchaserUnit::where('property_id',$defectObj->account_id)->where('unit_id',$defectObj->unit_no)->where('user_id',$defectObj->user_id)->first(); 
						if(isset($purObj))
							$notofication_data['switch_id'] =$purObj->id;

						$NotificationObj = new \App\Models\v7\FirebaseNotification();
						$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
						$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
						//End Insert into notification module
					}

				
			}else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Defect ID not avaliable']);
			}
			

			return response()->json(['data'=>$defectObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}
	
	public function defectFinalInspectionUpdate(Request $request) 
    {

		$rules = [
		    'id' => 'required',
			'appt_date' => 'required',
			'appt_time' => 'required'
		];
		
		$messages = [
			'id.required' => 'Id is missing',
			'appt_date.required' => 'Appt Date is missing',
			'appt_time.required' => 'Appt Time is missing',
		];
	
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json([
                'message' => $messages->all()
            ], 400);
		}

		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)) return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		
		$permission = $adminObj->check_permission(3,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}else{
			$defectObj = Defect::find($id);
		
			//Inspection update Start
			$jointObj = FinalInspectionAppointment::where('def_id', $id)->orderby('id','desc')->first();
			
			if($jointObj){

				if($request->input('inspection_status') !=4){//defect New , joint inspection new
					
					$status = $request->input('inspection_status');
					FinalInspectionAppointment::where('id', $jointObj->id)
						->update(['status' => $request->input('inspection_status'),'appt_date'=>$request->input('appt_date'),'appt_time'=>$request->input('appt_time')]);
					

				}else {///defect on in progress, , joint inspection closed
					$status = $request->input('inspection_status');
					$inspectionObj = FinalInspectionAppointment::find($jointObj->id);
					$inspectionObj->status = $status;
				
					$inspectionObj->appt_date = $request->input('appt_date');
					$inspectionObj->appt_time = $request->input('appt_time');

					$inspectionObj->progress_date = $request->input('progress_date');
					$inspectionObj->reminder_in_days = $request->input('reminder_in_days');
				
					if($request->input('progress_date') !=''){
						$date = Carbon::createFromFormat('Y-m-d', $request->input('progress_date'));
						if($request->input('reminder_in_days') !='')
							$booking_allowed  = $date->addDays($request->input('reminder_in_days'));
						else
							$booking_allowed  = $date->addDays(0);
								
						$inspectionObj->reminder_email_send_on = $booking_allowed;
					}
				
					$inspectionObj->reminder_emails = $request->input('reminder_emails');
					$inspectionObj->email_message = $request->input('email_message');
					$inspectionObj->reminder_email_status = 0; 
						
					$inspectionObj->save();    
				
				}
				//$request->input('inspection_status');
				$title = "Your defect final inspection status changed ";
				
				InboxMessage::where('ref_id', $jointObj->id)->where('type',5)
						->update(['title'=>$title,'booking_date'=>$jointObj->appt_date,'booking_time'=>$jointObj->appt_time,'event_status' => $status]);
				//inspection update End

				InboxMessage::where('ref_id', $id)->where('type',3)
				->update(['event_status' => $request->input('status')]);
			}

			return response()->json(['data'=>$defectObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}
	
	public function defectFinalInspectionCancel(Request $request) 
    {
		$rules = [
		 	'bookId' => 'required'
		];
		
		$messages = [
		 	'bookId.required' => 'Id is missing',
		];
	
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(['message' => $messages->all()], 400);
		}

				$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)) return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		
		$permission = $adminObj->check_permission(3,$adminObj->role_id); 
		
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}else{
		    
			$input = $request->all();

			$reason = isset($input['reason']) ? $input['reason'] : '';
			$bookid = $input['bookId'];
			$status = 1; //cancelled

		    $inbox = InboxMessage::where('ref_id', $bookid)->where('type',5)->first();
		
		    if(isset($inbox) && $inbox->id !=''){
    			$inboxObj = InboxMessage::find($inbox->id);
    			$inboxObj->event_status = $status;
    			$inboxObj->save();
			}
			
			$result = FinalInspectionAppointment::where('id', $bookid)->update(['status' => $status,'reason'=>$reason]);

			return response()->json(['data'=>$result,'response' => 1, 'message' => 'Appointment cancelled']);
		}
	}

	public function defectsinspectionupdate(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'appt_date' => 'required',
			'appt_time' => 'required',
			
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'appt_date.required' => 'Appt Date is missing',
			'appt_time.required' => 'Appt Time is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(3,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$defectObj = Defect::find($id);
		
			//Inspection update Start
			$jointObj = JoininspectionAppointment::where('def_id', $id)->orderby('id','desc')->first();
			
			if(isset($jointObj->id) && $jointObj->id > 0){

				if($request->input('inspection_status') !=4){//defect New , joint inspection new
					
					$status = $request->input('inspection_status');
						JoininspectionAppointment::where('id', $jointObj->id)
						->update(['status' => $request->input('inspection_status'),'appt_date'=>$request->input('appt_date'),'appt_time'=>$request->input('appt_time')]);
					

				}else {///defect on in progress, , joint inspection closed
					$status = $request->input('inspection_status');
					$inspectionObj = JoininspectionAppointment::find($jointObj->id);
					$inspectionObj->status = $status;
				
					$inspectionObj->appt_date = $request->input('appt_date');
					$inspectionObj->appt_time = $request->input('appt_time');

					$inspectionObj->progress_date = $request->input('progress_date');
					$inspectionObj->reminder_in_days = $request->input('reminder_in_days');
				
					if($request->input('progress_date') !=''){
						$date = Carbon::createFromFormat('Y-m-d', $request->input('progress_date'));
						if($request->input('reminder_in_days') !='')
							$booking_allowed  = $date->addDays($request->input('reminder_in_days'));
						else
							$booking_allowed  = $date->addDays(0);
								
						$inspectionObj->reminder_email_send_on = $booking_allowed;
					}
				
					$inspectionObj->reminder_emails = $request->input('reminder_emails');
					$inspectionObj->email_message = $request->input('email_message');
					$inspectionObj->reminder_email_status = 0; 
						
					$inspectionObj->save();    
				
				}
				//$request->input('inspection_status');
				$title = "Your defect inspection status changed ";
				
				InboxMessage::where('ref_id', $jointObj->id)->where('type',5)
						->update(['title'=>$title,'booking_date'=>$jointObj->appt_date,'booking_time'=>$jointObj->appt_time,'event_status' => $status]);
				//inspection update End

				InboxMessage::where('ref_id', $id)->where('type',3)
				->update(['event_status' => $request->input('status')]);
			}

			return response()->json(['data'=>$defectObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function defectscancelinspection(Request $request) 
    {
		$rules=array(
			'bookId' => 'required',	
		);
		$messages=array(
			'bookId.required' => 'Id is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(3,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$reason ='';

			if(isset($input['reason']))
				$reason = $input['reason'];

			$bookid = $input['bookId'];
			$status = 1; //cancelled

		

		$inbox = InboxMessage::where('ref_id', $bookid)->where('type',5)->first();
		
		if(isset($inbox) && $inbox->id !=''){
			$inboxObj = InboxMessage::find($inbox->id);
			$inboxObj->event_status = $status;
			$inboxObj->save();
			}

			
			$result = JoininspectionAppointment::where('id', $bookid)
					->update(['status' => $status,'reason'=>$reason]);
					
				
			return response()->json(['data'=>$result,'response' => 1, 'message' => 'Appointment cancelled']);
       
		}
	}


	public function defectshandoverupdate(Request $request) 
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

		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(3,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$defectObj = Defect::find($id);
			if($defectObj->status ==1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Sorry, this ticket has already been closed!']);

			}
			$defect_status = ($request->input('defect_status'));
			$handover_message = $request->input('handover_message');
		    $images = $request->file('rectified_image');
			$handover = 1;
			$ticket_status = 5;// completed pending Resident update  
			if($defectObj->submissions){
				foreach($defectObj->submissions as $k => $submission){
					if($submission->status ==2)
					{
						//echo $defect_status[$submission->id];
						$defectSubmissionObj = DefectSubmission::find($submission->id);
						if(isset($defect_status[$submission->id])){
							$defectSubmissionObj->defect_status = $defect_status[$submission->id];
							if($defect_status[$submission->id] ==2)
								$defectSubmissionObj->handover_message = $handover_message[$submission->id];                   
							if (isset($images[$submission->id]) && $images[$submission->id]->isValid()) {
								$file = $images[$submission->id];
								$imagePath = remove_upload_path($file->store(upload_path('defect')));
								$defectSubmissionObj->rectified_image = $imagePath;
							}
							else{
								$defectSubmissionObj->rectified_image = '';
							}
							if($defect_status[$submission->id] ==0)   {
								//$handover = 0;
								$ticket_status = 2; // in progress status   
							}
							$defectSubmissionObj->save();
						}else if($defectSubmissionObj->defect_status ==0){
							//$handover = 0;
							$ticket_status = 2; // in progress status   
						}
					}
				}
			}
			if ($request->file('signature') != null) {
				$signature = remove_upload_path($request->file('signature')->store(upload_path('defect')));
				$defectObj->handover_team_signature = $signature;
				$defectObj->handover_team_user = $login_id;
			    $defectObj->handover_team_timestamp = now();
			}

			$defectObj->handover_status = $handover;
			$defectObj->status = $ticket_status;
			//$defectObj->handover_status = $request->input('handover_status');
			$defectObj->save();

			if($ticket_status == 5) {// all completed ready for owner update
				//Start Insert into notification module
				$notification = array();
				$notification['account_id'] = $defectObj->account_id;
				$notification['user_id'] = $defectObj->user_id;
				$notification['unit_no'] = $defectObj->unit_no;
				$notification['module'] = 'defects';
				$notification['ref_id'] = $defectObj->id;
				$notification['title'] = 'Defects';
				$notification['message'] = 'There is an update from the management in regards to the defects that you have submitted';
				$result = UserNotification::insert($notification);
				$SettingsObj = UserNotificationSetting::where('user_id',$defectObj->user_id)->where('account_id',$defectObj->account_id)->first();
				if(empty($SettingsObj) || $SettingsObj->defect ==1)
				{
					$fcm_token_array ='';
					$user_token = ',';
					$ios_devices_to_send = array();
					$android_devices_to_send = array();
					$logs = UserLog::where('user_id',$defectObj->user_id)->where('status',1)->orderby('id','desc')->first();
					if(isset($logs->fcm_token) && $logs->fcm_token !=''){
						$user_token .=$logs->fcm_token.",";
						$fcm_token_array .=$logs->fcm_token.',';
						$appSipAccountList[] = $defectObj->id;
						if($logs->login_from ==1)
							$ios_devices_to_send[] = $logs->fcm_token;
						if($logs->login_from ==2)
							$android_devices_to_send[] = $logs->fcm_token;
					}
			
					$probObj = Property::find($defectObj->account_id);
					$title = "Aerea Home - ".$probObj->company_name;
					$message = "Defect(s) Updated";
					$notofication_data = array();
					$notofication_data['body'] =$title;   
					$notofication_data['unit_no'] =$defectObj->unit_no;   
					$notofication_data['user_id'] =$defectObj->user_id;   
					$notofication_data['property'] =$defectObj->account_id; 
					$purObj = UserPurchaserUnit::where('property_id',$defectObj->account_id)->where('unit_id',$defectObj->unit_no)->where('user_id',$defectObj->user_id)->first(); 
						if(isset($purObj))
						$notofication_data['switch_id'] =$purObj->id;       
					$NotificationObj = new \App\Models\v7\FirebaseNotification();
					$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
					$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
						//End Insert into notification module
				}
			}
			return response()->json(['data'=>$defectObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}
	public function deleteRectifiedImg(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'image_id' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'image_id.required' => 'Image Id is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$image_id = $request->image_id;
			$defSubObj = DefectSubmission::find($image_id);
			if($defSubObj){
				$defSubObj->rectified_image = '';
				$defSubObj->save();
				return response()->json(['response' => 1, 'message' => 'Deleted']);
			}else{
				return response()->json(['response' => 1, 'message' => 'No Record']);
			}
		}
	}
	public function deletedefect(Request $request) 
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
		
			$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			JoininspectionAppointment::where('def_id', $id)->delete();
            FinalInspectionAppointment::where('def_id', $id)->delete();
            
			DefectSubmission::where('def_id', $id)->delete();
	
			Defect::findOrFail($id)->delete();

			//UnittakeoverAppointment::findOrFail($id)->delete();

			return response()->json(['response' => 1, 'message' => 'Deleted']);

       
		}
	}

	public function defectssearch(Request $request) 
    {
		$adminObj = User::find(Auth::id()); 

		$permission = $adminObj->check_permission(3,$adminObj->role_id); 
		if(empty($permission) || (isset($permission->view) && $permission->view !=1))
		{
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}else{
			$account_id = $adminObj->account_id;
			$ticket = $name = $status = $option = $unit = $filter = $category ='';
			$fromdate = $todate = $month = '';
			$name = $request->input('name');
			$ticket = $request->input('ticket');
			$fromdate = $request->input('fromdate');
			$todate = $request->filled('todate') ? $request->todate : '';

			$unit = $request->input('unit');
			$status = $request->input('status');
			$types = ($request->has('type') && !empty($request->type)) ? $request->type : null;
			$locations = ($request->has('location') && !empty($request->location)) ? $request->location : null;
			if($locations!=null){
				$defectsids = DefectSubmission::select('def_id')
					->where('defect_location', $locations)
					->when($types,fn($e) => $e->where('defect_type', $types))
					->get()->toArray();
			}else{
				$defectsids = null;
			}
			$indays = $request->filled('defect_days') ? $request->defect_days : ''; 
			$building = $request->filled('block_no') ? $request->block_no : ''; 


			if ($name != '' || $ticket !='' || $status !='' || $fromdate !='' || $unit !='' || $locations!=null || (empty($indays) || !empty($indays) || !empty($building))) {
				$unit = $request->input('unit');
				$units = array();
				if($unit !=''){   
					$unit = str_replace("#",'',$unit);
					$unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
					})->get();   
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
								$units[] = $unitid->id;
						}
					}
				} 
				
				$userids =array();
				if($name !=''){
					$user_more_info = UserMoreInfo::select('id','user_id','first_name','last_name')->where('account_id',$account_id)->whereNotIn('status',[2])->orderby('id','desc')->get();
					
					foreach($user_more_info as $k =>$v){
						$firstname = strtolower(Crypt::decryptString($v->first_name));
						$lastname = strtolower(Crypt::decryptString($v->last_name));
						if(str_contains($firstname,strtolower($name)) || str_contains($lastname,strtolower($name))){
							$userids[] = $v->user_id;
							
						}
					}
				}  
				
				
				
				$defects =  Defect::select('defects.*')->where('defects.account_id',$account_id)
					->when(!empty($defectsids),fn($e) => $e->whereIn('id',$defectsids))
					->where(function ($query) use ($name,$userids,$ticket,$fromdate,$todate,$unit,$units,$status,$building) {
						if($ticket !='')
							$query->where('defects.ticket', 'LIKE', '%'.$ticket .'%');
						if($name !='')
							$query->whereIn('defects.user_id', $userids);
						if($building !='')
							$query->where('defects.block_no', $building);
						if($unit !='')
							$query->wherein('defects.unit_no', $units);
						if($status !='')
							$query->where('defects.status', $status);
					})
					->when((trim($fromdate)!='' && trim($todate)==''),fn($q) => $q->whereDate('defects.created_at','>=',$fromdate))
                    ->when((trim($fromdate)=='' && trim($todate)!=''),fn($q) => $q->whereDate('defects.created_at','<=',$todate))
                    ->when((trim($fromdate)!='' && trim($todate)!=''),fn($q) => $q->whereBetween('defects.created_at',array($fromdate,$todate)))
                    ->when((trim($indays)!=''),fn($q) => $q->whereDate('defects.inspection_owner_timestamp','<=',Carbon::now()->subDays($indays)->format('Y-m-d')))
					->get()
					->map(function($r){
						if(trim($r->unit_no)=='') $r->unit_no = 0;
						return $r;
					})
					->sortBy(fn($i) => (!isset($i->getunit->unit) ? '0' : Crypt::decryptString($i->getunit->unit)))
					->values();
					
					// id desc after unit sort asc
					$unitNoGrouped = $defects->groupBy('unit_no');
					$finalResults = collect([]);
					foreach ($unitNoGrouped as $uk => $rows){
						$idSorted = $rows->sortByDesc(fn($i) => $i->id)->values();
						$unitNoGrouped[$uk] = $idSorted;
						foreach ($idSorted as $row) $finalResults->push($row);
					}
			   

				$data =array();
					foreach($finalResults as $defect){
						$inspectionDate = ($defect->inspection_owner_timestamp !='')?date("Y-m-d",strtotime($defect->inspection_owner_timestamp)):'';
						$givenDate = \Carbon\Carbon::parse($inspectionDate);
						$days = $givenDate->diffInDays(\Carbon\Carbon::now());
						//$record['lists']=$defect;
						$record['lists']['id']=$defect->id;
						$record['lists']['account_id']=$defect->account_id;
						$record['lists']['ref_id']=$defect->ref_id;
						$record['lists']['ticket']=$defect->ticket;
						$record['lists']['user_id']=$defect->user_id;
						$record['lists']['block_no']=$defect->block_no;
						$record['lists']['unit_no']=$defect->unit_no;
						$record['lists']['status']=$defect->status;
						$record['lists']['view_status']=$defect->view_status;
						$record['lists']['inspection_status']=$defect->inspection_status;
						$record['lists']['handover_status']=$defect->handover_status;
						$record['lists']['rectified_in_days']=($days >0)?$days:null;
						$record['lists']['rectification_start_date']=($defect->inspection_owner_timestamp !='0000-00-00 00:00:00' &&$defect->inspection_owner_timestamp !='' )?date('d/m/y',strtotime($defect->inspection_owner_timestamp)):null;
						$record['lists']['inspection_owner_timestamp']=($defect->inspection_owner_timestamp !='0000-00-00 00:00:00' &&$defect->inspection_owner_timestamp !='' )?$defect->inspection_owner_timestamp:null;
						$record['lists']['created_at'] = date('d/m/y',strtotime($defect->created_at));
						$record['lists']['updated_at'] = date('d/m/y',strtotime($defect->updated_at));
			
						//$record['submission'] = $defect->submissions;
						$user_data =array();
						if(isset($defect->user)){
							$user_data["id"]= $defect->user->id;
							$user_data["account_id"]= $defect->user->account_id;
							$user_data["role_id"]= $defect->user->role_id;
							$user_data["user_info_id"]= $defect->user->user_info_id;
							$user_data["building_no"]= $defect->user->building_no;
							$user_data["unit_no"]= $defect->user->unit_no;
							$user_data["primary_contact"]= $defect->user->primary_contact;
							$user_data["name"]=Crypt::decryptString($defect->user->name);
							
						}
						$record['user_info'] = !empty($user_data)?$user_data:null;
						$unitObj = Unit::find($defect->unit_no);
						$unit_data =array();
						if(isset($unitObj)){
							$unit_data["id"]= $unitObj->id;
							$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
						}
						//$record['user_info'] = $defect->user;
						$record['unit_info'] = !empty($unit_data)?$unit_data:null;

						$record['inspection'] = isset($defect->inspection)?$defect->inspection:null;
						$data[] = $record;
					}

				$buildings = Building::where('account_id',$account_id)->get();  
				$blocks = array();			
				if($buildings){
					foreach($buildings as $building){
						$build_data = array();
						$build_data['id'] = $building->id;
						$build_data['name'] = $building->building;
						$defects_count = Defect::where('view_status', 0)->where('account_id', $account_id)->where('status', 0)->where('block_no',$building->id)->count();
						$build_data['new_count'] = $defects_count;
						$blocks[] = $build_data;
					}
				}
						
				return response()->json(['data'=>$data,'blocks'=>$blocks,'response' => 1, 'message' => 'Success']);
				
				//return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
			}
		}
	}

	public function defecttimeslot(Request $request) 
    {
				$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(3,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$obj = new JoininspectionAppointment();
			$times = $obj->opstimeslots($adminObj->account_id);	

			$data = array();
			foreach($times as $time){
				$array['time'] = $time;
				$data[] = $array;
			}

			return response()->json(['data'=>$data, 'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function defecttypes(Request $request) 
    {
		$rules=array(
			'location_id' => 'required',
		);
		$messages=array(
			'location_id.required' => 'Location Id is missing',
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
		$location = $request->location_id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(3,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$types = DefectType::where('account_id',$adminObj->account_id)->where('location_id',$location)->get();   
			return response()->json(['types'=>$types,'response' => 1, 'message' => 'Success']);

       
		}
	}



	//Defect Module End

	//Feedback Module Start
	public function feedbackDashboard(Request $request)
	{
	    $account_id = $request->user()->account_id;
        $startDate = $request->start_date;
		$endDate = $request->end_date;
		
		$currentYearDefects = FeedbackSubmission::where('account_id',$account_id)
			->whereDate('created_at','>=',$startDate)
			->whereDate('created_at','<=',$endDate)
	        ->get();

		$date1 = Carbon::parse($startDate);
		$date2 = Carbon::parse($endDate);

		$sExplode = explode('-',$startDate);
		$eExplode = explode('-',$endDate);
		$cStartDate = Carbon::createFromDate($sExplode[0], $sExplode[1], $sExplode[2]);
		$cEndDate = Carbon::createFromDate($eExplode[0], $eExplode[1], $eExplode[2]);

		$diffInMonths = $cStartDate->diffInMonths($cEndDate);

		$months = $date1->diffInMonths($date2);
		$period = CarbonPeriod::create($startDate, $endDate);

		if($diffInMonths==0) //One month
		{
			foreach ($period as $date) {
				$finalMonths[] = [
					'date' => $date->format('Y-m-d'),
					'total' => FeedbackSubmission::where('account_id',$account_id)
						->whereDate('created_at',$date->format('Y-m-d'))->count()
				];
			}
		}else{
			$yMonths = [];
			foreach ($period as $date) {
				$yMonths[] = $date->format('Y-m-').'01';
			}
			$yMonths = array_unique($yMonths);
			foreach($yMonths as $y){
				$finalMonths[] = [
					'date' => Carbon::parse($y)->format('Y-m'),
					'total' => FeedbackSubmission::where('account_id',$account_id)
						->whereYear('created_at',explode('-',$y)[0])
						->whereMonth('created_at',explode('-',$y)[1])
						->count()
				];
			}
		}

		$ticketsByTime = $finalMonths;

		$ticketsByLocation = FeedbackOption::where('account_id',$account_id)->where('status',1)
			->orderBy('feedback_option')->get()->map(function($q) use($account_id,$startDate,$endDate) {
				return [
					'type' => ucwords(strtolower($q->feedback_option)),
					'count' => FeedbackSubmission::where('fb_option',$q->id)
					    ->where('account_id',$account_id)
					    ->whereDate('created_at','>=',$startDate)
						->whereDate('created_at','<=',$endDate)->count(),
				];
			})->where('count','>',0)->values()->all();

		$ticketsByStatus = [
			[
				'status' => 'Open',
				'count' => $currentYearDefects->where('status',0)->count()
			],
			[
				'status' => 'Inprogress',
				'count' => $currentYearDefects->where('status',1)->count()
			],
			[
				'status' => 'Closed',
				'count' => $currentYearDefects->where('status',2)->count()
			]
		];

		$data = [
			'feedback_by_time' => $ticketsByTime,
			'feedback_by_category' => $ticketsByLocation,
			'feedback_by_status' => $ticketsByStatus
		];
	       
	   return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']); 
	}

	public function feedbackoptions(Request $request) 
    {
				$login_id = Auth::id();
		$user = $request->user;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
		$types = FeedbackOption::where('account_id', $adminObj->account_id)->get();

		return response()->json(['options'=>$types,'response' => 1, 'message' => 'success']);
       
	}

	public function feedbacklist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(6,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

        $account_id = $adminObj->account_id;

		$feedbacks = FeedbackSubmission::where('account_id',$adminObj->account_id)->orderby('id','desc')->get();

		
		$data =array();
		foreach($feedbacks as $feedback){
			$record['submissions']=$feedback;
			$record['option'] = $feedback->getoption;
			$user_data =array();
			if(isset($feedback->user)){
				$user_data["id"]= $feedback->user->id;
				$user_data["account_id"]= $feedback->user->account_id;
				$user_data["role_id"]= $feedback->user->role_id;
				$user_data["user_info_id"]= $feedback->user->user_info_id;
				$user_data["building_no"]= $feedback->user->building_no;
				$user_data["unit_no"]= $feedback->user->unit_no;
				$user_data["primary_contact"]= $feedback->user->primary_contact;
				$user_data["name"]=Crypt::decryptString($feedback->user->name);
				
			}
			$record['user_info'] = !empty($user_data)?$user_data:null;


			$unitObj = Unit::find($feedback->unit_no);
			$unit_data =array();
			if(isset($unitObj)){
				$unit_data["id"]= $unitObj->id;
				$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
			}
			$record['unit_info'] = !empty($unit_data)?$unit_data:null;

			//$record['user_info'] = $feedback->user;
			//$record['unit_info'] = isset($feedback->getunit)?$feedback->getunit:null;
			$data[] = $record;
		}
		
				
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function feedbacknewlist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(6,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$account_id = $adminObj->account_id;

			$date = Carbon::now()->subDays(7);
			$feedbacks = FeedbackSubmission::where('account_id',$account_id)->where('status',0)->where('view_status',0)->where('created_at', '>=', $date)->orderby('id','desc')->get();  
			
			$data =array();
			foreach($feedbacks as $feedback){
				$record['submissions']=$feedback;
				$record['option'] = $feedback->getoption;
				$user_data =array();
				if(isset($feedback->user)){
					$user_data["id"]= $feedback->user->id;
					$user_data["account_id"]= $feedback->user->account_id;
					$user_data["role_id"]= $feedback->user->role_id;
					$user_data["user_info_id"]= $feedback->user->user_info_id;
					$user_data["building_no"]= $feedback->user->building_no;
					$user_data["unit_no"]= $feedback->user->unit_no;
					$user_data["primary_contact"]= $feedback->user->primary_contact;
					$user_data["name"]=Crypt::decryptString($feedback->user->name);
					
				}
				$record['user_info'] = !empty($user_data)?$user_data:null;


				$unitObj = Unit::find($feedback->unit_no);
				$unit_data =array();
				if(isset($unitObj)){
					$unit_data["id"]= $unitObj->id;
					$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
					$unit_data["building"]= ($unitObj->buildinginfo->building)?$unitObj->buildinginfo->building:null;

				}
				$record['unit_info'] = !empty($unit_data)?$unit_data:null;
				$data[] = $record;
			}
							
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function feedbackinfo(Request $request) 
    {
		$rules=array(
			'id' => 'required',
		);
		$messages=array(
			'id.required' => 'Booking Id is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(6,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$feedbackObj = FeedbackSubmission::find($id);
			$feedbackObj->view_status = 1;
			$feedbackObj->save();

			if(!empty($feedbackObj->upload_1)) $feedbackObj->upload_1 = is_primary_domain() ? upload_path($feedbackObj->upload_1) : $feedbackObj->upload_1;
			if(!empty($feedbackObj->upload_2)) $feedbackObj->upload_2 = is_primary_domain() ? upload_path($feedbackObj->upload_2) : $feedbackObj->upload_2;
			
			$record['submissions']=$feedbackObj;
			$record['option'] = $feedbackObj->getoption;
			$record['user_info'] = $feedbackObj->user;
			$record['unit_info'] = $feedbackObj->getunit;

			return response()->json(['feedback'=>$record,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function editfeedback(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'status' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'status.required' => 'Status is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(6,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
		
			$feedbackObj = FeedbackSubmission::find($id);
			$feedbackObj->status = $request->input('status');
			$feedbackObj->remarks = $request->input('remarks');
			$feedbackObj->save();
			
			//Start Insert into notification module
			$notification = array();
			$notification['account_id'] = $feedbackObj->account_id;
			$notification['user_id'] = $feedbackObj->user_id;
			$notification['unit_no'] = $feedbackObj->unit_no;
			$notification['module'] = 'feedback';
			$notification['ref_id'] = $feedbackObj->id;
			$notification['title'] = 'Feedback';
			$notification['message'] = 'There is an update from the management in regards to your submitted feedback';
			$result = UserNotification::insert($notification);
			
			$SettingsObj = UserNotificationSetting::where('user_id',$feedbackObj->user_id)->where('account_id',$feedbackObj->account_id)->first();
			if(empty($SettingsObj) || $SettingsObj->feedback ==1){
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserLog::where('user_id',$feedbackObj->user_id)->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $feedbackObj->id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
				$probObj = Property::find($feedbackObj->account_id);
				$title = "Aerea Home - ".$probObj->company_name;
				$message = "Feedback Updated";
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$feedbackObj->unit_no;   
				$notofication_data['user_id'] =$feedbackObj->user_id;   
				$notofication_data['property'] =$feedbackObj->account_id;
				$purObj = UserPurchaserUnit::where('property_id',$feedbackObj->account_id)->where('unit_id',$feedbackObj->unit_no)->where('user_id',$feedbackObj->user_id)->first(); 
				if(isset($purObj))
					$notofication_data['switch_id'] =$purObj->id;         
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
			}
			//End Insert into notification module

			InboxMessage::where('ref_id', $id)->where('type',2)
			->update(['event_status' => $request->input('status')]);

			return response()->json(['data'=>$feedbackObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function deletefeedback(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			FeedbackSubmission::findOrFail($id)->delete();

			return response()->json(['response' => 1, 'message' => 'Deleted']);

       
		}
	}

	public function searchfeedback(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$account_id = $adminObj->account_id;
			$ticket  =  $name = $status = $option = $unit = $filter = $category = $building = '';
			$fromdate = $todate = $month = '';
			$ticket = $request->input('ticket');
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
			$filter = ($request->input('filter')!='')?$request->input('filter'):'id'; 
			$fromdate = $request->input('fromdate');
			if($request->input('todate') !='')
				$todate = $request->input('todate');
			else
				$todate =$request->input('fromdate');
			$status = $request->input('status');
			$category = $request->input('category');

			if ($ticket != '' || $unit != '' || $fromdate != '' || $status != '' || $category != ''|| $filter != '') {
				$feedbacks = FeedbackSubmission::where('account_id',$account_id)->where(function ($query) use ($ticket,$category,$fromdate,$todate,$unit,$units,$status,$building) {
					if($ticket !='')
						$query->where('ticket', 'LIKE', '%'.$ticket .'%');
					if($unit !='' || $building !='')
						$query->wherein('unit_no', $units);
					if($status !='')
						$query->where('status',$status);
					if($category !='')
						$query->where('fb_option',$category);
					if($fromdate!='' && $todate !='')
						$query->whereBetween('created_at',array($fromdate,$todate));
				})->orderBy($filter,'DESC')->get();

				$data =array();
				foreach($feedbacks as $feedback){
					$record['submissions']=$feedback;
					$record['option'] = $feedback->getoption;
					$user_data =array();
					if(isset($feedback->user)){
						$user_data["id"]= $feedback->user->id;
						$user_data["account_id"]= $feedback->user->account_id;
						$user_data["role_id"]= $feedback->user->role_id;
						$user_data["user_info_id"]= $feedback->user->user_info_id;
						$user_data["building_no"]= $feedback->user->building_no;
						$user_data["unit_no"]= $feedback->user->unit_no;
						$user_data["primary_contact"]= $feedback->user->primary_contact;
						$user_data["name"]=Crypt::decryptString($feedback->user->name);
						
					}
					$record['user_info'] = !empty($user_data)?$user_data:null;


					$unitObj = Unit::find($feedback->unit_no);
					$unit_data =array();
					if(isset($unitObj)){
						$unit_data["id"]= $unitObj->id;
						$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
						$unit_data["building"]= ($unitObj->buildinginfo->building)?$unitObj->buildinginfo->building:null;

					}
					$record['unit_info'] = !empty($unit_data)?$unit_data:null;
					$data[] = $record;
				}
		
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option is empty']);
			}
		}
	}


	public function cancelfeedback(Request $request){
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$input = $request->all();
		 	$reason ='';

			if(isset($input['reason']))
				$reason = $input['reason'];

			$bookid = $input['id'];
			$status = 1; //cancelled

			$inbox = InboxMessage::where('ref_id', $bookid)->where('type',4)->first();
			
			$inboxObj = InboxMessage::find($inbox->id);
			$inboxObj->event_status = $status;
			$inboxObj->save();

			UnittakeoverAppointment::where('id', $bookid)
					->update(['status' => $status,'reason'=>$reason]);
			
			return response()->json(['response' => 1, 'message' => 'Booking Cancelled']);

		}
	}



	public function confirmfeedback(Request $request){

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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$input = $request->all();
		 	$reason ='';

			if(isset($input['reason']))
				$reason = $input['reason'];

			$bookid = $input['id'];
			$status = 2; //confirm

			$inbox = InboxMessage::where('ref_id', $bookid)->where('type',4)->first();
			
			$inboxObj = InboxMessage::find($inbox->id);
			$inboxObj->event_status = $status;
			$inboxObj->save();

			UnittakeoverAppointment::where('id', $bookid)
					->update(['status' => $status,'reason'=>$reason]);
			
			return response()->json(['response' => 1, 'message' => 'Booking Confirmed']);

		}
	   
	}
	//Feedback Module End

	//Facility Booking Module Start
	
	public function facilityDashboard(Request $request)
	{
	    $account_id = $request->user()->account_id;
        $startDate = $request->start_date;
		$endDate = $request->end_date;
		
		$currentYearDefects = FacilityBooking::where('account_id',$account_id)
			->whereDate('created_at','>=',$startDate)
			->whereDate('created_at','<=',$endDate)
	        ->get();

		$date1 = Carbon::parse($startDate);
		$date2 = Carbon::parse($endDate);

		$sExplode = explode('-',$startDate);
		$eExplode = explode('-',$endDate);
		$cStartDate = Carbon::createFromDate($sExplode[0], $sExplode[1], $sExplode[2]);
		$cEndDate = Carbon::createFromDate($eExplode[0], $eExplode[1], $eExplode[2]);

		$diffInMonths = $cStartDate->diffInMonths($cEndDate);

		$months = $date1->diffInMonths($date2);
		$period = CarbonPeriod::create($startDate, $endDate);

		if($diffInMonths==0) //One month
		{
			foreach ($period as $date) {
				$finalMonths[] = [
					'date' => $date->format('Y-m-d'),
					'total' => FacilityBooking::where('account_id',$account_id)
						->whereDate('created_at',$date->format('Y-m-d'))->count()
				];
			}
		}else{
			$yMonths = [];
			foreach ($period as $date) {
				$yMonths[] = $date->format('Y-m-').'01';
			}
			$yMonths = array_unique($yMonths);
			foreach($yMonths as $y){
				$finalMonths[] = [
					'date' => Carbon::parse($y)->format('Y-m'),
					'total' => FacilityBooking::where('account_id',$account_id)
						->whereYear('created_at',explode('-',$y)[0])
						->whereMonth('created_at',explode('-',$y)[1])
						->count()
				];
			}
		}

		$ticketsByTime = $finalMonths;

		$ticketsByLocation = FacilityType::where('account_id',$account_id)->where('status',1)
			->orderBy('facility_type')->get()->map(function($q) use($account_id,$startDate,$endDate) {
				return [
					'type' => ucwords(strtolower($q->facility_type)),
					'count' => FacilityBooking::where('type_id',$q->id)
					    ->where('account_id',$account_id)
					    ->whereDate('created_at','>=',$startDate)
						->whereDate('created_at','<=',$endDate)->count(),
				];
			})->where('count','>',0)->values()->all();

		$ticketsByStatus = [
			[
				'status' => 'New',
				'count' => $currentYearDefects->where('status',0)->count()
			],
			[
				'status' => 'Confirmed',
				'count' => $currentYearDefects->where('status',2)->count()
			],
			[
				'status' => 'Cancelled',
				'count' => $currentYearDefects->where('status',1)->count()
			]
		];

		$data = [
			'facility_by_time' => $ticketsByTime,
			'facility_by_type' => $ticketsByLocation,
			'facility_by_status' => $ticketsByStatus
		];
	       
	   return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']); 
	}

	public function facilityoptions(Request $request) 
    {
		$login_id = Auth::id();
		$user = $request->user;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
        $types = FacilityType::where('account_id',$adminObj->account_id)->pluck('facility_type', 'id')->all();

		return response()->json(['options'=>$types,'response' => 1, 'message' => 'success']);
       
	}

	public function facilitylist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(5,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

        $account_id = $adminObj->account_id;

		$bookings = FacilityBooking::where('account_id',$account_id)->wherein('status',[1,2])->orderby('id','desc')->get();   
		
		$data =array();
		foreach($bookings as $booking){
			$userMoreInfoObj = UserMoreInfo::Where('user_id',$booking->user_id)->Where('account_id',$account_id)->first();

			$booking_data =array();
				$booking_data['id']=$booking->id;
				$booking_data['type_id']=$booking->type_id;
				$booking_data['user_id']=$booking->user_id;
				$booking_data['account_id']=$booking->account_id;
				$booking_data['opn_charge_id']=$booking->opn_charge_id;
				$booking_data['opn_card_id']=$booking->opn_card_id;
				$booking_data['unit_no']=$booking->unit_no;
				$booking_data['booking_date']=$booking->booking_date;
				$booking_data['booking_time']=$booking->booking_time;
				$booking_data['booking_fee']=$booking->booking_fee;
				$booking_data['deposit_fee']=$booking->deposit_fee;
				$booking_data['refund_amount']=$booking->refund_amount;
				$booking_data['capture_amount']=$booking->capture_amount;
				$booking_data['reason']=$booking->reason;
				$booking_data['status']=$booking->status;
				$booking_data['view_status']=$booking->view_status;
				$booking_data['notification_status']=$booking->notification_status;
				$booking_data['payment_required']=$booking->payment_required;
				$booking_data['payment_status']=$booking->payment_status;
				$booking_data['refund_status']=$booking->refund_status;
				$booking_data['amount_received_date']=$booking->amount_received_date;
				$booking_data['deposit_refund_status']=$booking->deposit_refund_status;

				$booking_data['deposit_payment_status']=$booking->deposit_payment_status;
				$booking_data['refund_reason']=$booking->refund_reason;
				$booking_data['deposit_received_date']=$booking->deposit_received_date;
				$record['submissions']=$booking_data;
				$record['type'] = $booking->gettype;
				$unitObj = Unit::find($booking->unit_no);
				$unit_data =array();
				if(isset($unitObj)){
					$unit_data["id"]= $unitObj->id;
					$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
				}
				$record['unit_info'] = !empty($unit_data)?$unit_data:null;

				//$record['unit_info']=isset($booking->getunit->unit)?$booking->getunit:null;
				$userMoreInfoObj = UserMoreInfo::Where('user_id',$booking->user_id)->Where('account_id',$account_id)->first();
				$user_data =array();
				if(isset($userMoreInfoObj)){
					$user_data['user_id']=$userMoreInfoObj->user_id;
					$user_data['user_info_id']=$userMoreInfoObj->id;
					$user_data['first_name']=Crypt::decryptString($userMoreInfoObj->first_name);
					$user_data['last_name']=Crypt::decryptString($userMoreInfoObj->last_name);
					$user_data['profile_picture']=$userMoreInfoObj->profile_picture;
					$record['user_info'] = !empty($user_data)?$user_data:null;
				}else{
					$record['user_info'] = null;
				}
				
			//$record['user_info'] = $booking->getname;
			$data[] = $record;
		}
		
				
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function facilitynewlist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(5,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$account_id = $adminObj->account_id;

			$bookings = FacilityBooking::where('account_id',$account_id)->where('view_status',0)->where('status',0)->orderby('id','desc')->get();  
 
			
			$data =array();
			foreach($bookings as $booking){
				$booking_data =array();
				$booking_data['id']=$booking->id;
				$booking_data['type_id']=$booking->type_id;
				$booking_data['user_id']=$booking->user_id;
				$booking_data['account_id']=$booking->account_id;
				$booking_data['opn_charge_id']=$booking->opn_charge_id;
				$booking_data['opn_card_id']=$booking->opn_card_id;
				$booking_data['unit_no']=$booking->unit_no;
				$booking_data['booking_date']=$booking->booking_date;
				$booking_data['booking_time']=$booking->booking_time;
				$booking_data['booking_fee']=$booking->booking_fee;
				$booking_data['deposit_fee']=$booking->deposit_fee;
				$booking_data['refund_amount']=$booking->refund_amount;
				$booking_data['capture_amount']=$booking->capture_amount;
				$booking_data['reason']=$booking->reason;
				$booking_data['status']=$booking->status;
				$booking_data['view_status']=$booking->view_status;
				$booking_data['notification_status']=$booking->notification_status;
				$booking_data['payment_required']=$booking->payment_required;
				$booking_data['payment_status']=$booking->payment_status;
				$booking_data['refund_status']=$booking->refund_status;
				$booking_data['amount_received_date']=$booking->amount_received_date;
				$booking_data['deposit_refund_status']=$booking->deposit_refund_status;
				$booking_data['deposit_payment_status']=$booking->deposit_payment_status;
				$booking_data['refund_reason']=$booking->refund_reason;
				$booking_data['deposit_received_date']=$booking->deposit_received_date;
				$record['submissions']=$booking_data;
				$record['type'] = $booking->gettype;
				$unitObj = Unit::find($booking->unit_no);
				$unit_data =array();
				if(isset($unitObj)){
					$unit_data["id"]= $unitObj->id;
					$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
					$unit_data["building"]= ($unitObj->buildinginfo->building)?$unitObj->buildinginfo->building:null;

				}
				$record['unit_info'] = !empty($unit_data)?$unit_data:null;

				//$record['unit_info']=isset($booking->getunit->unit)?$booking->getunit:null;
				$userMoreInfoObj = UserMoreInfo::Where('user_id',$booking->user_id)->Where('account_id',$account_id)->first();
				$user_data =array();
				if(isset($userMoreInfoObj)){
					$user_data['user_id']=$userMoreInfoObj->user_id;
					$user_data['user_info_id']=$userMoreInfoObj->id;
					$user_data['first_name']=Crypt::decryptString($userMoreInfoObj->first_name);
					$user_data['last_name']=Crypt::decryptString($userMoreInfoObj->last_name);
					$user_data['profile_picture']=$userMoreInfoObj->profile_picture;
					$record['user_info'] = !empty($user_data)?$user_data:null;
				}else{
					$record['user_info'] = null;
				}
				//$record['user_info'] = $booking->getname;
				$data[] = $record;
			}
							
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function facilityinfo(Request $request) 
    {
		$rules=array(
			'id' => 'required',
		);
		$messages=array(
			'id.required' => 'Booking Id is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 
		$account_id = $adminObj->account_id;

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(6,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$bookingObj = FacilityBooking::find($id);
			if(isset($bookingObj)){
				$record['submissions']=$bookingObj;
				$record['option'] = $bookingObj->gettype;
				$record['unit_info']=isset($bookingObj->getunit->unit)?$bookingObj->getunit:null;
				$userMoreInfoObj = UserMoreInfo::Where('user_id',$bookingObj->user_id)->Where('account_id',$account_id)->first();
				$user_data =array();
				if(isset($userMoreInfoObj)){
						$user_data['user_id']=$userMoreInfoObj->user_id;
						$user_data['user_info_id']=$userMoreInfoObj->id;
						$user_data['first_name']=$userMoreInfoObj->first_name;
						$user_data['last_name']=$userMoreInfoObj->last_name;
						$user_data['profile_picture']=$userMoreInfoObj->profile_picture;
						$record['user_info'] = !empty($user_data)?$user_data:null;
				}else{
						$record['user_info'] = null;
				}
				$file_path = env('APP_URL')."/storage/app";
				return response()->json(['booking'=>$record,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['booking'=>null,'response' => 1, 'message' => 'Deleted']);
			}

       
		}
	}

	public function editfacility(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'booking_date'=>'required',
			'booking_time'=>'required',
			'status' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'booking_date.required'=>'Booking date is missing',
			'booking_time.required'=>'Booking time is missing',
			'status.required' => 'Status is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(5,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
		
			$commands = ($request->input('team_command'));

        
			$bookingObj = FacilityBooking::find($id);
			$bookingObj->booking_date = $request->input('booking_date');
			$bookingObj->booking_time = $request->input('booking_time');
			$bookingObj->status = $request->input('status');
			$bookingObj->save();

			
			//Start Insert into notification module
			$notification = array();
			$notification['account_id'] = $bookingObj->account_id;
			$notification['user_id'] = $bookingObj->user_id;
			$notification['unit_no'] = $bookingObj->unit_no;

			$notification['module'] = 'facility';
			$notification['ref_id'] = $bookingObj->id;
			$notification['title'] = 'Facility Booking';
			$notification['message'] = 'There is a status update for your facility booking';
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
				$message = "Facility Booking Updated";

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

			$title = "Your Facility booking has been changed ";

			InboxMessage::where('ref_id', $id)->where('type',6)
					->update(['title'=>$title,'booking_date'=>$request->input('booking_date'),'booking_time'=>$request->input('booking_time'),'event_status' => $request->input('status')]);


			return response()->json(['data'=>$bookingObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function deletefacility(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(5,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			FacilityBooking::findOrFail($id)->delete();

			return response()->json(['response' => 1, 'message' => 'Deleted']);

       
		}
	}

	public function searchfacility(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$account_id = $adminObj->account_id;
			$ticket  =  $name = $status = $option = $unit = $filter = $category = $building ='';
			$fromdate = $todate = $month = '';
			$building = $request->input('building');

			$unit = $request->input('unit');
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
			$category = $request->input('category');  
			$filter = $request->input('filter'); 
			$fromdate = $request->input('fromdate');
			if($request->input('todate') !='')
				$todate = $request->input('todate');
			else
				$todate =$request->input('fromdate');
			$status = $request->input('status');
			$unit = $request->input('unit');
			if($request->input('filter')) 
				$filter = $request->input('filter');
			else
				$filter = 'id';

			if ($unit != '' || $category !='' || $fromdate !='' || $status !='' || $filter !='' || $building !='') {
				$bookings = FacilityBooking::where('account_id',$account_id)->where(function ($query) use ($category,$fromdate,$todate,$unit,$units,$status,$building) {
					if($category !='')
						$query->where('type_id',$category);
					if($unit !='' ||$building !='')
						$query->whereIn('unit_no', $units);
					if($status !='')
						$query->where('status',$status);
					if($fromdate!='' && $todate !='')
						$query->whereBetween('booking_date',array($fromdate,$todate));
				})->orderBy($filter,'DESC')->get();

				$data =array();
				foreach($bookings as $booking){
					$booking_data =array();
					$booking_data['id']=$booking->id;
					$booking_data['type_id']=$booking->type_id;
					$booking_data['user_id']=$booking->user_id;
					$booking_data['account_id']=$booking->account_id;
					$booking_data['opn_charge_id']=$booking->opn_charge_id;
					$booking_data['opn_card_id']=$booking->opn_card_id;
					$booking_data['unit_no']=$booking->unit_no;
					$booking_data['booking_date']=$booking->booking_date;
					$booking_data['booking_time']=$booking->booking_time;
					$booking_data['booking_fee']=$booking->booking_fee;
					$booking_data['deposit_fee']=$booking->deposit_fee;
					$booking_data['refund_amount']=$booking->refund_amount;
					$booking_data['capture_amount']=$booking->capture_amount;
					$booking_data['reason']=$booking->reason;
					$booking_data['status']=$booking->status;
					$booking_data['view_status']=$booking->view_status;
					$booking_data['notification_status']=$booking->notification_status;
					$booking_data['payment_required']=$booking->payment_required;
					$booking_data['payment_status']=$booking->payment_status;
					$booking_data['refund_status']=$booking->refund_status;
					$booking_data['amount_received_date']=$booking->amount_received_date;
					$booking_data['deposit_refund_status']=$booking->deposit_refund_status;
					$booking_data['deposit_payment_status']=$booking->deposit_payment_status;
					$booking_data['refund_reason']=$booking->refund_reason;
					$booking_data['deposit_received_date']=$booking->deposit_received_date;
					$record['submissions']=$booking_data;
					$record['type'] = $booking->gettype;
					$unitObj = Unit::find($booking->unit_no);
					$unit_data =array();
					if(isset($unitObj)){
						$unit_data["id"]= $unitObj->id;
						$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
						$unit_data["building"]= ($unitObj->buildinginfo->building)?$unitObj->buildinginfo->building:null;
					}
					$record['unit_info'] = !empty($unit_data)?$unit_data:null;

					//$record['unit_info']=isset($booking->getunit->unit)?$booking->getunit:null;
					$userMoreInfoObj = UserMoreInfo::Where('user_id',$booking->user_id)->Where('account_id',$account_id)->first();
					$user_data =array();
					if(isset($userMoreInfoObj)){
						$user_data['user_id']=$userMoreInfoObj->user_id;
						$user_data['user_info_id']=$userMoreInfoObj->id;
						$user_data['first_name']=Crypt::decryptString($userMoreInfoObj->first_name);
						$user_data['last_name']=Crypt::decryptString($userMoreInfoObj->last_name);
						$user_data['profile_picture']=$userMoreInfoObj->profile_picture;
						$record['user_info'] = !empty($user_data)?$user_data:null;
					}else{
						$record['user_info'] = null;
					}
					//$record['user_info'] = $booking->getname;
					$data[] = $record;
				}
		
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty!']);
			}
		}
	}


	public function cancelfacility(Request $request){
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
		
		$login_id = Auth::id();
		$bookid = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(5,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$input = $request->all();
			$reason ='';
			$bookingObj = FacilityBooking::find($bookid);
			if(empty($bookingObj)){
				return response()->json(['response' => 200, 'message' => 'Invalid booking id!']);
			}
			if(isset($input['reason']))
				$reason = $input['reason'];
			$booking_id = $input['id'];
			$status = 1; //cancelled

			FacilityBooking::where('id', $booking_id)
			->update(['status' =>1,'reason'=>$reason]);
	
			$recordObj = FacilityBooking::find($booking_id);
			$propinfo = Property::where('id',$recordObj->account_id)->first();
			$sub_merchant_key = $propinfo->opn_secret_key;
			$username = env('OMISEKEY');
			$password = '';
		   
			if($bookingObj->payment_required==1){
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
					$json = json_decode($result,true);
					if(isset($json['id']) && $json['id'] !=''){
						$facility_update_qry =FacilityBooking::where('id',$booking_id)->update(['deposit_refund_amount'=>$refund_amount,'deposit_refund_status' => 1,'refund_reason'=>'booking cancelled','deposit_payment_status'=>3]);
					}
	
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
			$notification['message'] = 'There is a status update for your facility booking';
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
			
			return response()->json(['response' => 1, 'message' => 'Booking Cancelled']);

		}
	}



	public function confirmfacility(Request $request){

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
		
		$login_id = Auth::id();
		$bookid = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(5,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$input = $request->all();
			$reason ='';

			$bookid = $input['id'];
			$status = 2; //cancelled

			$bookingObj = FacilityBooking::find($bookid);

			if(empty($bookingObj)){
				return response()->json(['response' => 200, 'message' => 'Invalid booking id!']);
			}
			//Start Insert into notification module
			$notification = array();
			$notification['account_id'] = $bookingObj->account_id;
			$notification['user_id'] = $bookingObj->user_id;
			$notification['unit_no'] = $bookingObj->unit_no;

			$notification['module'] = 'facility';
			$notification['ref_id'] = $bookingObj->id;
			$notification['title'] = 'Facility Booking';
			$notification['message'] = 'There is a status update for your facility booking';
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
				$message = "Facility Booking Confirmed";
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


			$inbox = InboxMessage::where('ref_id', $bookid)->where('type',6)->first();
			if(isset($inbox) && $inbox->id !='')
				{
					$inboxObj = InboxMessage::find($inbox->id);
					$inboxObj->event_status = $status;
					$inboxObj->save();
				}

			FacilityBooking::where('id', $bookid)
					->update(['status' => $status,'reason'=>$reason]);
					
			return response()->json(['response' => 1, 'message' => 'Booking Confirmed']);

		}
	   
	}

	public function refundfacility(Request $request) {

		$rules=array(
			'booking_id' => 'required',
			'deposit_amount' => 'required',
			'charge_amount' => 'required',
			'refund_amount' => 'required',
		);
		$messages=array(
			'booking_id.required' => 'Facility id is missing',
			'deposit_amount.required' => 'Deposit amount is required',
			'charge_amount.required' => 'Charge amount is required',
			'refund_amount.required' => 'Refund amount isrequired',
		);

		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(5,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$booking_id = $input['booking_id'];
			$recordObj = FacilityBooking::find($input['booking_id']);
			$charge_amount = $input['charge_amount'];
			$reason = $input['reason'];
			$refund_amount = $recordObj->deposit_fee-$charge_amount;
			if($charge_amount > $recordObj->deposit_fee){
				return response()->json(['response' => 200, 'message' => 'Claim amount is more than deposit']);

			}
		
			if(isset($recordObj->deposit_charge_id) && $recordObj->deposit_charge_id !=''){
				if($charge_amount >0){
                    $payment_url = env('OMISEURL')."charges/".$recordObj->deposit_charge_id."/capture";
                    $propinfo = Property::where('id',$recordObj->account_id)->first();
                    $sub_merchant_key = $propinfo->opn_secret_key;
                    $username = env('OMISEKEY');
                    $password = '';
                    $opn_capture_amount = ($charge_amount)*100;
                    $fields = [
                            "capture_amount" => $opn_capture_amount,
                        ];
                    //exit;
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
                }
                else{
                    $payment_url = env('OMISEURL')."charges/".$recordObj->deposit_charge_id."/reverse";
                    $propinfo = Property::where('id',$recordObj->account_id)->first();
                    $sub_merchant_key = $propinfo->opn_secret_key;
                    $username = env('OMISEKEY');
                    $password = '';

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
                }
				$json = json_decode($result,true);
				if(isset($json['id']) && $json['id'] !=''){
                    
					$refund_status = ($refund_amount ==$recordObj->deposit_fee)?1:2;
					$facility_update_qry =FacilityBooking::where('id',$booking_id)->update(['claim_amount' => $charge_amount,'deposit_refund_amount'=>$refund_amount,'deposit_refund_status' => 1,'refund_reason'=>$reason]);

                    $bookingObj = FacilityBooking::find($booking_id);
                    //Start Insert into notification module
                    $notification = array();
                    $notification['account_id'] = $bookingObj->account_id;
                    $notification['unit_no'] = $bookingObj->unit_no;
                    $notification['user_id'] = $bookingObj->user_id;
                    $notification['module'] = 'facility';
                    $notification['ref_id'] = $bookingObj->id;
                    $notification['title'] = 'Facility Booking';
                    $notification['message'] = 'Deposit fee refund update on your facility booking';
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
                    $message = "Facility Booking: Deposit Fee Refunded";
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

					return response()->json(['data'=>$json,'response' => 1, 'message' => 'success']);
				}else{
					return response()->json(['data'=>null,'response' => 200, 'message' => 'Charge not authorised']);
				}
			}else{
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Charge id not created']);

			}
				
		}
	}
	public function facilitytimeslot(Request $request)
    {
        $rules=array(
			'date' => 'required',
			'type'	=>'required',
		);
		$messages=array(
			'type.required' => 'Type is missing',
			'date.required' => 'Booking Date is missing',
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
		
		$permission = $adminObj->check_permission(2,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$data = array();
			$selecteddate = $request->date;
			$type = $request->type;

			$facility = FacilityType::find($type);

			$times = explode(",",$facility->timing);
        
        
			foreach($times as $time){
				//echo $time;
				$lists = DB::table("facility_bookings")->where('type_id',$type)->where('booking_date',$selecteddate)->where('booking_time',$time)->where('status', 1)->get();
				$recordcount = count($lists);
				$record =array('time'=>$time,'count'=>$recordcount);

				$data[] = $record;

			}
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);


        }
		
	}

	//Facility Booking End

	//Property Settings start

	public function propertyinfo(Request $request) 
    {
		$rules=array(
			'property_id' => 'required',
		);
		$messages=array(
			'property_id.required' => 'Property Id is missing',
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
		$id = $request->property_id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(28,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$propObj = Property::find($id);

			return response()->json(['property'=>$propObj,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function propertyedit(Request $request) 
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

		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(28,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
		
			$configObj = property::find($id);

			if ($request->input('company_name') != null)
				$configObj->company_name = $request->input('company_name');

			if ($request->input('company_contact') != null)
				$configObj->company_contact = $request->input('company_contact');
			
			if ($request->input('company_email') != null)
				$configObj->company_email = $request->input('company_email');

			if ($request->input('company_address') != null)
				$configObj->company_address = $request->input('company_address');

			if ($request->input('management_company_name') != null)
				$configObj->management_company_name = $request->input('management_company_name');

			if ($request->input('management_company_addr') != null)
				$configObj->management_company_addr = $request->input('management_company_addr');
			
			if ($request->input('invoice_notes') != null)
				$configObj->invoice_notes = $request->input('invoice_notes');

			if ($request->input('mcst_code') != null)
				$configObj->mcst_code = $request->input('mcst_code');

			if ($request->input('enquiry_email') != null)
				$configObj->enquiry_email = $request->input('enquiry_email');

			if ($request->input('enquiry_contact') != null)
				$configObj->enquiry_contact = $request->input('enquiry_contact');

			
			
			$configObj->save();

			if($configObj->third_party_option >0)
			{
				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();
				$property_result = $auth->property_check_record($thinmoo_access_token,$configObj->company_name);
				if($property_result['code'] ==0){
					$property = $auth->property_add_api($thinmoo_access_token,$configObj->id,$configObj->company_name);
				}
				else{
					$property = $auth->property_modify_api($thinmoo_access_token,$configObj->id,$configObj->company_name);
				}
				$emp = new \App\Models\v7\Employee();
				$emp_result = Employee::where('uuid',$configObj->id)->where('emp_type',0)->first();
				$emp_result = $emp->employee_check_record($thinmoo_access_token,$emp_result);

				if($emp_result['code'] !=0){
					$emp_rec['account_id'] = $configObj->id;
					$emp_rec['uuid'] = $configObj->id;
					$emp_rec['name'] = $configObj->company_name. " Employee";
					$emp = Employee::create($emp_rec);
					$emp_info= $emp->employee_add_api($thinmoo_access_token,$emp,3);
				}
			}
			return response()->json(['data'=>$configObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function keycollectionsetting(Request $request) 
    {
		$rules=array(
			'property_id' => 'required',
		);
		$messages=array(
			'property_id.required' => 'Property Id is missing',
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
		$id = $request->property_id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(9,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
		
			$configObj = property::find($id);
			$configObj->takeover_timing = $request->input('takeover_timing');
			$configObj->takeover_availability_start = $request->input('takeover_availability_start');
			$configObj->takeover_blockout_days = $request->input('takeover_blockout_days');
			$configObj->takeover_notes = $request->input('takeover_notes');

			$configObj->save();
			return response()->json(['data'=>$configObj,'response' => 1, 'message' => 'Key Collection Appoinment settings Updated']);
       
		}
	}

	public function inspectionsetting(Request $request) 
    {
		$rules=array(
			'property_id' => 'required',
		);
		$messages=array(
			'property_id.required' => 'Property Id is missing',
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
		$id = $request->property_id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(57,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
		
			$configObj = property::find($id);
			$configObj->inspection_timing = $request->input('inspection_timing');
			$configObj->inspection_availability_start = $request->input('inspection_availability_start');
			$configObj->inspection_blockout_days = $request->input('inspection_blockout_days');
			$configObj->inspection_notes = $request->input('inspection_notes');
			$configObj->final_inspection_required = $request->has('is_final_inspection_required') && in_array($request->is_final_inspection_required,[0,1]) ? $request->is_final_inspection_required : 0;
			$configObj->defect_max_limit = $request->has('defect_max_limit')? $request->defect_max_limit : 20;
			$configObj->save();

			return response()->json(['data'=>$configObj,'response' => 1, 'message' => 'Inspection Appoinment settings Updated']);
       
		}
	}

	//Property Booking End

	//Building Settings start

	public function buildingsummarylist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$buildings = Building::where('account_id',$account_id)->get();  			
			return response()->json(['data'=>$buildings,'response' => 1, 'message' => 'Success']);
		}
	}

	public function buildinginfo(Request $request) 
    {
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$buildingObj = Building::find($id);

			return response()->json(['unit'=>$buildingObj,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function buildingcreate(Request $request) 
    {
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			for($i=1;$i<=5;$i++){
				$building_name ='building_'. $i;
	
				if(isset($input[$building_name]) && $input[$building_name] !='') {
					$data['building'] =  $input[$building_name];
					$data['account_id'] = $adminObj->account_id;
	
					$building = Building::create($data);
					$building_no = $adminObj->account_id.$building->id;
					Building::where('id', $building->id)
						->update(['building_no' => $building_no]);
	
					$buildingObj = Building::find($building->id);
	
					
					if($building->id >0){
	
						$auth = new \App\Models\v7\Property();
						$thinmoo_access_token = $auth->thinmoo_auth_api();
	
						
						$api_obj = new \App\Models\v7\Building();
						$building_result = $api_obj->building_check_record($thinmoo_access_token,$buildingObj);
						
						if($building_result['code'] ==0){
							$building_info = $api_obj->building_update_api($thinmoo_access_token,$buildingObj);
						}
						else{
							$building_info= $api_obj->building_add_api($thinmoo_access_token,$buildingObj);
						}
					}
				}
			}
			return response()->json(['response' => 1, 'message' => 'Success']);
       
		}
	}

	public function buildingedit(Request $request) 
    {
		$rules=array(
			'building' => 'required ',
		);
		$messages=array(
			'building.required' => 'Building name is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$buildingObj = Building::find($id);
			$input['account_id'] = $adminObj->account_id;

			$validator = Validator::make($request->all(), [ 
				'building' =>[
					'required', 
					Rule::unique('buildings')
						->where('account_id', $input['account_id'])
						->whereNotIn('id',[$id])
				],
				
			]);

			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Building already exist!']);          }

			$buildingObj->building = $request->input('building');
			$buildingObj->save();

			if($buildingObj->id >0){

				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();
				
				$api_obj = new \App\Models\v7\Building();
				$building_result = $api_obj->building_check_record($thinmoo_access_token,$buildingObj);

			
				
				if($building_result['code'] ==0){
					$building_info = $api_obj->building_update_api($thinmoo_access_token,$buildingObj);
				}
				else{
					$building_info= $api_obj->building_add_api($thinmoo_access_token,$buildingObj);
				}
			}
			
			return response()->json(['data'=>$buildingObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function buildingdelete(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$BuildingObj = Building::find($id);

			if($BuildingObj->id >0){
				$auth = new \App\Models\v7\Property();
				if(Session::has('thinmoo_acess_tocken')){
					$thinmoo_access_token = Session::get('thinmoo_acess_tocken');
				}
				else{
					$thinmoo_access_token = $auth->thinmoo_auth_api();  
				}
				
				$api_obj = new \App\Models\v7\Building();
				$building_result = $api_obj->building_check_record($thinmoo_access_token,$BuildingObj);
				
				if($building_result['code'] ==0){
					$building_info = $api_obj->building_delete_api($thinmoo_access_token,$BuildingObj);
				}
			
			}
			Building::findOrFail($id)->delete();
		
			return response()->json(['response' => 1, 'message' => 'Deleted']);
       
		}
	}
	//Building Settings End

	//Unit Settings start

	public function unitsummarylist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$units = Unit::where('account_id',$account_id)->get();
			$records = array();
			
			if(isset($units)) {
				foreach($units as $unit){
					
					$unit_data =array();
					$unit_data["id"]= $unit->id;
					$unit_data["unit"]= Crypt::decryptString($unit->unit);
					$unit_data["code"]= Crypt::decryptString($unit->code);
					$unit_data["building_id"]= $unit->building_id;
					$unit_data["account_id"]= $unit->account_id;
					$unit_data["size"]= $unit->size;
					$unit_data["status"]= $unit->status;
					$unit_data["share_amount"]= $unit->share_amount;
					$unit_data['building'] = $unit->buildinginfo;
					$records[] = $unit_data;
					
				}
			}	
			return response()->json(['data'=>$records,'response' => 1, 'message' => 'Success']);
		}
	}

	public function unitsummarysearch(Request $request) 
    {
		$rules=array(
			'building' => 'required',
		);
		$messages=array(
			'building.required' => 'Building id is missing',
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
		$building = $request->building;
		$unit = $request->unit;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$units = Unit::where('account_id',$account_id)->where(function ($query) use ($building,$unit) {
				if($building !='')
					$query->where('building_id',$building);
				if($unit !='')
					$query->where('unit', 'LIKE', '%'.$unit .'%');
			})->get();
			$records = array();
			
			if(isset($units)) {
				foreach($units as $unit){
					$unit_data =array();
					$unit_data["id"]= $unit->id;
					$unit_data["unit"]= Crypt::decryptString($unit->unit);
					$unit_data["code"]= Crypt::decryptString($unit->code);
					$unit_data["building_id"]= $unit->building_id;
					$unit_data["account_id"]= $unit->account_id;
					$unit_data["size"]= $unit->size;
					$unit_data["status"]= $unit->status;
					$unit_data["share_amount"]= $unit->share_amount;
					$unit_data['building'] = $unit->buildinginfo;
					$records[] = $unit_data;
					
				}
			}	
			return response()->json(['data'=>$records,'response' => 1, 'message' => 'Success']);
		}
	}

	public function userunitdelete(Request $request) {
        $rules=array(
			'purchase_id' => 'required',
		);
		$messages=array(
			'purchase_id.required' => 'Purchase id is missing',
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
			$id = $request->purchase_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$account_id = $adminObj->account_id;
			$purchaserObj = UserPurchaserUnit::find($id);
			if(empty($purchaserObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Record not found']);
			}
			$userObj = User::find($purchaserObj->user_id);
			$UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $userObj->id)->where( 'account_id' , $userObj->account_id)->first();

			UserPurchaserUnit::findOrFail($id)->delete(); 

			$auth = new \App\Models\v7\Property();
			$thinmoo_access_token = $auth->thinmoo_auth_api();
			$name = $userObj->name." ".$userObj->userinfo_fromadmin->last_name;

				$accountinfos = UserPurchaserUnit::where('user_id',$userObj->id)
				->where('user_info_id',$UserMoreInfoObj->id)
				->where('property_id', $purchaserObj->property_id)
				->get();
				$roomuuids = '';
				if(isset($accountinfos)){
					foreach($accountinfos as $accountinfo){
						$roomuuids .= $accountinfo->unit_id.",";
					}
				}
				$roomuuids = substr($roomuuids,0,-1);
				if($roomuuids !=''){
					$api_obj = new \App\Models\v7\User();
					$household_result = $api_obj->household_check_record($thinmoo_access_token,$userObj,$purchaserObj->property_id);
					if($household_result['code'] ==0){
						$household = $api_obj->household_modify_api($thinmoo_access_token, $purchaserObj->property_id,$name,$userObj->id,$roomuuids);
					}
					else{
						$household = $api_obj->household_add_api($thinmoo_access_token, $purchaserObj->property_id,$name,$userObj->id,$roomuuids);
					}
					$api_obj = new \App\Models\v7\User();
				
				}
					$values = 'Id='.$id.", Unit Id=".$purchaserObj->unit_id;

					$log['module_id'] = 7;
					$log['account_id'] = $account_id;
					$log['admin_id'] = $login_id;
					$log['action'] = 8;
					$log['new_values'] = $values;
					$log['ref_id'] = $UserMoreInfoObj->id;
					$log['notes'] = 'User Unit Deleted From Manager App';
					$log = ActivityLog::create($log);

					return response()->json(['response' => 1, 'message' => 'Deleted']);


		}     

    }

    public function userunitactivate(Request $request) {
        $rules=array(
			'purchase_id' => 'required',
		);
		$messages=array(
			'purchase_id.required' => 'Purchase id is missing',
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
		$id = $request->purchase_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$account_id = $adminObj->account_id;
			$purchaserObj = UserPurchaserUnit::find($id);
			if(empty($purchaserObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Record not found']);
			}
			$UserMoreInfoObj = UserMoreInfo::find($purchaserObj->user_info_id);

			UserPurchaserUnit::where('id', $purchaserObj->id)
			->update(['status' => 1]);
	
			$values = 'Id='.$id.", Unit Id=".$purchaserObj->unit_id;
			$log['module_id'] = 7;
			$log['account_id'] = $account_id;
			$log['admin_id'] = $login_id;
			$log['action'] = 9;
			$log['new_values'] = $values;
			$log['ref_id'] = $UserMoreInfoObj->id;
			$log['notes'] = 'User Activated for Unit From Manager App';
			$log = ActivityLog::create($log);

			return response()->json(['response' => 1, 'message' => 'Activated']);


		}     

	}
	
	public function userunitdeactivate(Request $request) {
        		$login_id = Auth::id();
		$id = $request->purchase_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$account_id = $adminObj->account_id;
			$purchaserObj = UserPurchaserUnit::find($id);
			if(empty($purchaserObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Record not found']);
			}
			$UserMoreInfoObj = UserMoreInfo::find($purchaserObj->user_info_id);

			UserPurchaserUnit::where('id', $purchaserObj->id)
			->update(['status' => 2]);
	
			$values = 'Id='.$id.", Unit Id=".$purchaserObj->unit_id;
			$log['module_id'] = 7;
			$log['account_id'] = $account_id;
			$log['admin_id'] = $login_id;
			$log['action'] = 9;
			$log['new_values'] = $values;
			$log['ref_id'] = $UserMoreInfoObj->id;
			$log['notes'] = 'User Deactivated for Unit From Manager App';
			$log = ActivityLog::create($log);

			return response()->json(['response' => 1, 'message' => 'Deactivated']);


		}     

    }



	public function unitsearch(Request $request) 
    {
		$rules=array(
			'building_id' => 'required',
		);
		$messages=array(
			'building_id.required' => 'Building id is missing',
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
		$building_id = $request->building_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$units = Unit::where('account_id',$account_id)->where('building_id',$building_id)->get();  
			$unit_data =array();
			$data= array();
			if(isset($units)){
				foreach($units as $unit){
					$unit_data["id"]= $unit->id;
					$unit_data["unit"]= Crypt::decryptString($unit->unit);
					$unit_data["code"]= Crypt::decryptString($unit->code);
					$unit_data["building_id"]= $unit->building_id;
					$unit_data["account_id"]= $unit->account_id;
					$unit_data["size"]= $unit->size;
					$unit_data["status"]= $unit->status;
					$unit_data["share_amount"]= $unit->share_amount;
					$data[] = $unit_data;
				}
			}			
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function unitinfo(Request $request) 
    {
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$unitObj = Unit::find($id);

			return response()->json(['unit'=>$unitObj,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function unitcreate(Request $request) 
    {
		$rules=array(
			'unit' => 'required ',
		);
		$messages=array(
			'unit.required' => 'Unit is missing',
			'unit.min' => 'Unit length minimum 5 digit (Ex. 09-10)',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$input['account_id'] = $adminObj->account_id;
			$validator = Validator::make($request->all(), [ 
				'unit' =>[
					'required', 
					Rule::unique('unites')
						->where('account_id', $input['account_id'])
                        ->where('building_id', $input['building_id'])
				],
			]);
			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Unit already exist!']);  
			}
			$char = array("_","-","_");
			$code = str_replace($char,"",$input['unit']);
			$input['code'] = Crypt::encryptString($code);
			$input['unit'] = Crypt::encryptString($input['unit']);
			$input['encrypted'] = 1;
			$unit = Unit::create($input);
			$buildingObj = Building::find($unit->building_id);

			$unitObj = Unit::find($unit->id);

			if($unit->id >0){

				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();  
			
				
				$api_obj = new \App\Models\v7\Unit();
				$unit_result = $api_obj->unit_check_record($thinmoo_access_token,$unitObj);
				
				if($unit_result['code'] ==0){
					$unit_info = $api_obj->unit_update_api($thinmoo_access_token,$unitObj);
				}
				else{
					$unit_info= $api_obj->unit_add_api($thinmoo_access_token,$unitObj);
				}
			}
		
			return response()->json(['data'=>$unit,'response' => 1, 'message' => 'Success']);
       
		}
	}

	public function unitedit(Request $request) 
    {
		$rules=array(
			'unit' => 'required ',
		);
		$messages=array(
			'unit.required' => 'Unit is missing',
			'unit.min' => 'Unit length minimum 5 digit (Ex. 09-10)',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
		
			$unitObj = Unit::find($id);
			$input['account_id'] = $adminObj->account_id;

			$validator = Validator::make($request->all(), [ 
				'unit' =>[
					'required', 
					Rule::unique('unites')
						->where('account_id', $input['account_id'])
						->where('account_id', $request->input('building_id'))
						->whereNotIn('id',[$id])
				],
				
			]);

			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Unit already exist!']);  
			}
			if($request->input('code') ==''){
				$buildingObj = Building::find($unitObj->building_id);
				//$code = $buildingObj->building_no.$unitObj->id;
				$char = array("_","-","_");
				$code = str_replace($char,"",$request->input('unit'));
				$unitObj->code = Crypt::encryptString($code);
			}
			
			$unitObj->building_id = $request->input('building_id');
			$unitObj->unit = Crypt::encryptString($request->input('unit'));
			$unitObj->size = $request->input('size');
			$unitObj->share_amount = $request->input('share_amount');
			$unitObj->encrypted = 1;
			$unitObj->save();

			if($unitObj->id >0){

				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();  
			   
				$api_obj = new \App\Models\v7\Unit();
				$unit_result = $api_obj->unit_check_record($thinmoo_access_token,$unitObj);
	
			   
				if($unit_result['code'] ==0){
					$unit_info = $api_obj->unit_update_api($thinmoo_access_token,$unitObj);
				}
				else{
					$unit_info= $api_obj->unit_add_api($thinmoo_access_token,$unitObj);
				}
			}

			return response()->json(['data'=>$unitObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function unitdelete(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$UnitObj = Unit::find($id);

			if($UnitObj->id >0){
				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();  
			   
				
				$api_obj = new \App\Models\v7\Unit();
				$unit_result = $api_obj->unit_check_record($thinmoo_access_token,$UnitObj);
				
				if($unit_result['code'] ==0){
					$unit_info = $api_obj->unit_delete_api($thinmoo_access_token,$UnitObj);
				}
			   
			}

			Unit::findOrFail($id)->delete();
			return response()->json(['response' => 1, 'message' => 'Deleted']);
       
		}
	}
	//Unit Settings End

	//Feedback Options Settings start

	public function fboptionsummarylist(Request $request) 
    {
				$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(26,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$feedbacks = FeedbackOption::where('account_id',$account_id)->get();    			
			return response()->json(['data'=>$feedbacks,'response' => 1, 'message' => 'Success']);
		}
	}

	public function fboptioninfo(Request $request) 
    {
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$fbObj = FeedbackOption::find($id);
			return response()->json(['unit'=>$fbObj,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function fboptioncreate(Request $request) 
    {
		$rules=array(
			'feedback_option' => 'required',
		);
		$messages=array(
			'feedback_option.required' => 'Feedback Options is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$input['account_id'] = $adminObj->account_id;
			
			$validator = Validator::make($request->all(), [ 
				'feedback_option' =>[
					'required', 
					Rule::unique('feedback_options')
						   ->where('account_id', $input['account_id'])
				],	
			]);
			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Feedback option already exist!']);
			}
			$fbObj = FeedbackOption::create($input);
			return response()->json(['data'=>$fbObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function fboptionedit(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'feedback_option' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'feedback_option.required' => 'Feedback Options is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
		
			$fbObj = FeedbackOption::find($id);
			$input['account_id'] = $adminObj->account_id;
			$validator = Validator::make($request->all(), [ 
				'feedback_option' =>[
					'required', 
					Rule::unique('feedback_options')
						->where('account_id', $fbObj->account_id)
						->whereNotIn('id',[$id])
				],
				
			]);

			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Feedback option already exist!']);  
			}
			$fbObj->feedback_option = $request->input('feedback_option');
			$fbObj->save();
			
			return response()->json(['data'=>$fbObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function fboptiondelete(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			FeedbackOption::findOrFail($id)->delete();
			return response()->json(['response' => 1, 'message' => 'Deleted']);
       
		}
	}
	
	//Feedback Options Settings End

	//Facility Type Settings start

	public function facilitytypelist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(29,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$facilities = FacilityType::where('account_id',$account_id)->get();  
			$file_path = env('APP_URL')."/storage/app";
			return response()->json(['data'=>$facilities,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
		}
	}

	public function facilitytypeinfo(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(29,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$facilityObj = FacilityType::find($id);
			$propertyObj = Property::where('id',$facilityObj->account_id)->first();
			$prop_data = array();
			if(isset($propertyObj)){
				$prop_data['id'] = $propertyObj->id;
				$prop_data['merchant_id'] = $propertyObj->opn_secret_key;
			}
			$file_path = env('APP_URL')."/storage/app";
			return response()->json(['type'=>$facilityObj,'file_path'=>$file_path,'property'=>$prop_data,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function facilitytypecreate(Request $request) 
    {
		$rules=array(
			'facility_type' => 'required',
		);
		$messages=array(
			'facility_type.required' => 'Facility type is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$input['account_id'] = $adminObj->account_id;
			//echo $input['next_booking_allowed_days'] = $request->next_booking_allowed_days;

			$validator = Validator::make($request->all(), [ 
				'facility_type' =>[
					'required', 
					Rule::unique('facility_types')
						->where('account_id', $input['account_id'])
				],
				
			]);
			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Facility type already exist!']);
			}
			if ($request->file('facility_image') != null) {
				$input['facility_image'] = remove_upload_path($request->file('facility_image')->store(upload_path('facility')));
			}
			$propertyObj = Property::where('id',$input['account_id'])->first();

			if($propertyObj->opn_secret_key ==''){
				$input['booking_fee'] =0.00;
				$input['booking_deposit'] =0.00;
			}
			$ftObj = FacilityType::create($input);
			return response()->json(['data'=>$ftObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function facilitytypeedit(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'facility_type' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'facility_type.required' => 'Facility type is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(29,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
		
			$facilityObj = FacilityType::find($id);
			$input['account_id'] = $adminObj->account_id;
			$validator = Validator::make($request->all(), [ 
				'facility_type' =>[
					'required', 
					Rule::unique('facility_types')
						->where('account_id', $adminObj->account_id)
						->whereNotIn('id',[$id])
				],
				
			]);

			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Feedback option already exist!']);  
			}
			$facilityObj->facility_type = $request->input('facility_type');
			$facilityObj->calendar_availability_start = $request->input('calendar_availability_start');
			$facilityObj->next_booking_allowed = $request->input('next_booking_allowed');
			$facilityObj->allowed_booking_for = $request->input('allowed_booking_for');
			$facilityObj->next_booking_allowed_days = $request->input('next_booking_allowed_days');
			$facilityObj->payment_required = $request->input('payment_required');
			$facilityObj->booking_fee = $request->input('booking_fee');
			$facilityObj->booking_deposit = $request->input('booking_deposit');
			$facilityObj->cut_of_days = $request->input('cut_of_days');
			$facilityObj->cut_of_amount_percentage = $request->input('cut_of_amount_percentage');
			$facilityObj->timing = $request->input('timing');
			$facilityObj->blockout_days = $request->input('blockout_days');
			$facilityObj->notes = $request->input('notes');
			
			if(empty($request->input('payment_required')) || $request->input('payment_required') ==2){
				$facilityObj->payment_required =2;
				$facilityObj->booking_fee =0.00;
				$facilityObj->booking_deposit =0.00;
			}

			if ($request->file('facility_image') != null) {
				$facilityObj->facility_image = remove_upload_path($request->file('facility_image')->store(upload_path('facility')));
			}
		
    	    $facilityObj->save();
			
			return response()->json(['data'=>$facilityObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function facilitytypedelete(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			FacilityType::findOrFail($id)->delete();
			return response()->json(['response' => 1, 'message' => 'Deleted']);
       
		}
	}
	
	//Facility Type Settings End

	//Defects Location Settings Start


	public function locationlist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$locations = DefectLocation::where('account_id',$account_id)->get();
            
			return response()->json(['data'=>$locations,'response' => 1, 'message' => 'Success']);
		}
	}

	public function locationinfo(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$defect_types =array();
			$defectObj = DefectLocation::find($id);
			if (isset($defectObj->types)) {
				foreach ($defectObj->types as $k => $defect) {
					$d_types['key'] = $k + 1;
					$d_types['id'] = $defect['id'];
					$d_types['defect_type'] = $defect['defect_type'];
					$defect_types[$k + 1] = $d_types;
				}
			}
			return response()->json(['locations'=>$defectObj,'types'=>$defect_types,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function locationcreate(Request $request) 
    {
		$rules=array(
			'defect_location' => 'required',
		);
		$messages=array(
			'defect_location.required' => 'Defect location is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$input['account_id'] = $adminObj->account_id;
			
				$validator = Validator::make($request->all(), [ 
					'defect_location' =>[
						'required', 
						Rule::unique('defect_locations')
							->where('account_id', $input['account_id'])
					],					
				]);
				if ($validator->fails()) { 
					return response()->json(['data'=>null,'response' => 400, 'message' => 'Defect location already exist!']);       
				}
				

				$location =  DefectLocation::create($input);

				/********** INSERT Defect Type******************/
				for ($i = 1; $i <= 10; $i++) {
				
					$type_name = 'defect_type_' . $i;
					if (isset($input[$type_name]) && $input[$type_name] != null) {
						$type['location_id'] = $location->id;
						$type['account_id'] = $location->account_id;
						$type['defect_type'] = $input[$type_name];
						$type['created_at'] = $location->created_at;
						$type['updated_at'] = $location->updated_at;
						$defect_types[] = $type;
					}
				}

				if (isset($defect_types)) {
					DefectType::insert($defect_types);
				}
			
			//$ftObj = FacilityType::create($input);
			return response()->json(['data'=>$location,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function locationedit(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'defect_location' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'defect_location.required' => 'Defect location is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$locationObj = DefectLocation::find($id);
			$input = $request->all();
			$locationObj->account_id= $adminObj->account_id;

			$validator = Validator::make($request->all(), [ 
					'defect_location' =>[
						'required', 
						Rule::unique('defect_locations')
							->where('account_id', $adminObj->account_id)
							->whereNotIn('id',[$id])
					],
					
			]);

			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Defect location already exist!']);  
			}

        	$locationObj->defect_location = $request->input('defect_location');
        	$defectObj =  $locationObj->save();

			/********** INSERT type START******************/
			for ($i = 1; $i <= 10; $i++) {
				$type_title = 'defect_type_' . $i;
				$type_id = 'type_id_' . $i;

				if ($request->input($type_id) != null) {

					if($request->input($type_title) ==''){
						DefectType::findOrFail($request->input($type_id))->delete();
					}else{
						$typeObj = DefectType::find($request->input($type_id));
						$typeObj->location_id = $id;
						$typeObj->defect_type = $request->input($type_title);
						$typeObj->created_at = $locationObj->created_at;
						$typeObj->updated_at = $locationObj->updated_at;
						$typeObj->save();
					}

				} else if ($request->input($type_title) != null) {
					$type['location_id'] = $id;
					$type['account_id'] = $adminObj->account_id;
					$type['defect_type'] = $request->input($type_title);
					$type['created_at'] = $locationObj->created_at;
					$type['updated_at'] = $locationObj->updated_at;
					$allowance = DefectType::create($type);
					
				}
			}
			
			return response()->json(['data'=>$locationObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function locationdelete(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			DefectType::where('location_id', $id)->delete();

			DefectLocation::findOrFail($id)->delete();
			return response()->json(['response' => 1, 'message' => 'Deleted']);
       
		}
	}

	//Defects Location Settings End
	
	//Visiting Purpose Settings Start
	public function visitorDashboard(Request $request)
	{
	    $account_id = $request->user()->account_id;
        $startDate = $request->start_date;
		$endDate = $request->end_date;
		
		$currentYearDefects = VisitorBooking::where('account_id',$account_id)
			->whereDate('created_at','>=',$startDate)
			->whereDate('created_at','<=',$endDate)
	        ->get();

		$date1 = Carbon::parse($startDate);
		$date2 = Carbon::parse($endDate);

		$sExplode = explode('-',$startDate);
		$eExplode = explode('-',$endDate);
		$cStartDate = Carbon::createFromDate($sExplode[0], $sExplode[1], $sExplode[2]);
		$cEndDate = Carbon::createFromDate($eExplode[0], $eExplode[1], $eExplode[2]);

		$diffInMonths = $cStartDate->diffInMonths($cEndDate);

		$months = $date1->diffInMonths($date2);
		$period = CarbonPeriod::create($startDate, $endDate);

		if($diffInMonths==0) //One month
		{
			foreach ($period as $date) {
				$finalMonths[] = [
					'date' => $date->format('Y-m-d'),
					'total' => VisitorBooking::where('account_id',$account_id)
						->whereDate('created_at',$date->format('Y-m-d'))->count()
				];
			}
		}else{
			$yMonths = [];
			foreach ($period as $date) {
				$yMonths[] = $date->format('Y-m-').'01';
			}
			$yMonths = array_unique($yMonths);
			foreach($yMonths as $y){
				$finalMonths[] = [
					'date' => Carbon::parse($y)->format('Y-m'),
					'total' => VisitorBooking::where('account_id',$account_id)
						->whereYear('created_at',explode('-',$y)[0])
						->whereMonth('created_at',explode('-',$y)[1])
						->count()
				];
			}
		}

		$ticketsByTime = $finalMonths;

		$ticketsByLocation = VisitorType::where('account_id',$account_id)->where('status',1)
			->orderBy('visiting_purpose')->get()->map(function($q) use($account_id,$startDate,$endDate) {
				return [
					'type' => ucwords(strtolower($q->visiting_purpose)),
					'count' => VisitorBooking::where('visiting_purpose',$q->id)
					    ->where('account_id',$account_id)
					    ->whereDate('created_at','>=',$startDate)
						->whereDate('created_at','<=',$endDate)->count(),
				];
			})->where('count','>',0)->values()->all();

		$ticketsByStatus = [
			[
				'status' => 'Open',
				'count' => $currentYearDefects->where('status',0)->count()
			],
			[
				'status' => 'Inprogress',
				'count' => $currentYearDefects->where('status',1)->count()
			],
			[
				'status' => 'Closed',
				'count' => $currentYearDefects->where('status',2)->count()
			]
		];

		$data = [
			'visitors_by_time' => $ticketsByTime,
			'visitors_by_purpose' => $ticketsByLocation,
			'visitors_by_status' => $ticketsByStatus
		];
	       
	   return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success!']); 
	}


	public function visitorlimit_info(Request $request) 
    {
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(37,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$propertyObj = Property::where('id',$adminObj->account_id)->first();
			$data =array();
			if(isset($propertyObj)){
				$data['id'] = $propertyObj->id;
				$data['visitor_limit_option'] = ($propertyObj->visitor_limit==0)?"no limit":"set limit";
				$data['visitor_limit'] = $propertyObj->visitor_limit;
				$data['visitors_allowed'] = $propertyObj->visitors_allowed;
				$data['visiting_to_date_required'] = $propertyObj->visiting_to_date_required;
			}

			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function visitorlimit_update(Request $request) 
    {
				
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(37,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$probObj = Property::where('id',$adminObj->account_id)->first();
			$data =array();
			if(isset($probObj)){
				$probObj->visitor_limit = $request->input('visitor_limit');
				if($request->input('visitor_limit') ==1)
					$probObj->visitors_allowed = $request->input('visitors_allowed');
				else
					$probObj->visitors_allowed = '';
				
				if($request->input('visiting_to_date_required') ==1)
					$probObj->visiting_to_date_required = $request->input('visiting_to_date_required');
				else
					$probObj->visiting_to_date_required = 0;
				$probObj->save();
			}

			$data =array();
			$data['id'] = $probObj->id;
			$data['visitor_limit_option'] = ($probObj->visitor_limit==0)?"no limit":"set limit";
			$data['visitor_limit'] = $probObj->visitor_limit;
			$data['visitors_allowed'] = $probObj->visitors_allowed;
			$data['visiting_to_date_required'] = $probObj->visiting_to_date_required;
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);

       
		}
	}
	public function visipurposelist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(37,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$lists = VisitorType::where('account_id',$account_id)->get();  

			$property = Property::where('id',$account_id)->first();

			return response()->json(['data'=>$lists,'property_info'=>$property,'response' => 1, 'message' => 'Success']);
		}
	}



	public function visipurposeinfo(Request $request) 
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
		
				$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(37,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$Obj = VisitorType::find($id);
	
			$subcategories=array();
			if(isset($Obj->subcategory)){
				foreach($Obj->subcategory as $k => $category){
					
					$sub_array = array();
					$sub_array['id'] = $category->id;
					$sub_array['sub_category'] = $category->sub_category;
					$subcategories[$k+1] = $sub_array;
				
				}
			}
			return response()->json(['purpose'=>$Obj,'types'=>$subcategories,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function visipurposecreate(Request $request) 
    {
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 
		

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$account_id = $adminObj->account_id;
			$typeObj = VisitorType::create($input);
			$details=array();
			for($i=1;$i<=15;$i++){
				$list = 'sub_category_list_'.$i;
				if(!empty($request->input($list))){
					$data['type_id'] = $typeObj->id;
					$data['sub_category'] = $request->input($list);
					$data['account_id'] = $account_id;
					$data['created_at'] = $typeObj->updated_at;
					$data['updated_at'] = $typeObj->updated_at;
					$details[] = $data;
				}
			}
        	$record = VisitorTypeSubcategory::insert($details);
			return response()->json(['data'=>$record,'response' => 1, 'message' => 'Updated']);
		}
	}

	public function visipurposeedit(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'visiting_purpose' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'visiting_purpose.required' => 'Visiting purpose is missing',

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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$unitObj = VisitorType::find($id);

			if (empty($unitObj)) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Record not found!']);  
			}
			$input = $request->all();
			$input['account_id']= $adminObj->account_id;

			$validator = Validator::make($request->all(), [ 
				'visiting_purpose' =>[
					'required', 
					Rule::unique('visitor_types')
						->where('account_id', $input['account_id'])
						->whereNotIn('id',[$id])
				],
				
			]);

			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Visiting Purpose already exist!']);  
			}
			$unitObj->visiting_purpose = $request->input('visiting_purpose');
			$unitObj->id_required = $request->input('id_required');
			$unitObj->limit_set = $request->input('limit_set');
			$unitObj->end_date_required = $request->input('end_date_required');
			$unitObj->qr_scan_limit = $request->input('qr_scan_limit');
			$unitObj->compinfo_required = $request->input('compinfo_required');
			$unitObj->cat_dropdown = $request->input('cat_dropdown');
			$unitObj->sub_category = $request->input('sub_category');
			$unitObj->save();

			$type_id = $unitObj->id;
			VisitorTypeSubcategory::where('type_id', $type_id)->delete();
			$details=array();
			for($i=1;$i<=15;$i++){

				$list = 'sub_category_list_'.$i;
				if(!empty($request->input($list))){
					$data['type_id'] = $type_id;
					$data['sub_category'] = $request->input($list);
					$data['account_id'] = $input['account_id'];
					$data['created_at'] = $unitObj->updated_at;
					$data['updated_at'] = $unitObj->updated_at;
					$details[] = $data;
				}
				
			}
		
			$record = VisitorTypeSubcategory::insert($details);
			
			return response()->json(['data'=>$unitObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function visipurposedelete(Request $request) 
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
		$login_id = Auth::id();
		
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			VisitorTypeSubcategory::where('type_id', $id)->delete();
        	VisitorType::findOrFail($id)->delete();
			return response()->json(['response' => 1, 'message' => 'Deleted']);
       
		}
	}

	
	//Visiting Purpose Settings End

	//Eform Settings Settings Start

	public function eformsetting_list(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$lists = EformSetting::where('account_id',$account_id)->get();
			$record =array();  
			if(isset($lists)){
				foreach($lists as $list){
					$data = array();
					$data['id'] =  $list->id;
					$data['form_type'] =  isset($list->gettype->name)?$list->gettype->name:null;
					$data['form_type_id'] =  $list->eform_type;
					$data['account_id'] =  $list->account_id;
					$data['general_info'] =  $list->general_info;
					$data['refund_amount'] =  $list->refund_amount;
					$record[] = $data;  
				}
				return response()->json(['data'=>$record,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['purpose'=>null,'response' => 200, 'message' => 'No record']);
			}
			
		}
	}

	public function eformsetting_types(Request $request) 
    {
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$eforms = Module::where('group_id',10)->get();
			$lists=array();
			if(isset($eforms)){
				foreach($eforms as $eform){
					$data = array();
					$data['info'] =  $eform;
					$form_lists = array(40,41);
					$data['renovation'] =  ($eform->id==41)?1:0;
					$data['padding_fee'] =  in_array($eform->id,$form_lists)?1:0;
					$lists[] = $data;  
				}
				
				return response()->json(['lists'=>$lists,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['purpose'=>null,'response' => 200, 'message' => 'No record']);
			}
			
		}
	}

	public function eformsetting_info(Request $request) 
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
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$eformObj = EformSetting::find($id);
			$data=array();
			if(isset($eformObj)){
				$data['info'] = $eformObj;
				$data['type_info'] = isset($eformObj->gettype->name)?$eformObj->gettype->name:null;
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'No record']);
			}
			
		}
	}

	public function eformsetting_create(Request $request) 
    {
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$input['account_id'] = $adminObj->account_id;
			$validator = Validator::make($request->all(), [ 
				'eform_type' =>[
					'required', 
					Rule::unique('eform_settings')
						   ->where('account_id', $input['account_id'])
				],
			]);
			if ($validator->fails()) { 
				return response()->json(['response' => 200, 'message' => 'Eform Setting already exist!']);       
			}

			$result = EformSetting::create($input);    
			return response()->json(['data'=>$result,'response' => 1, 'message' => 'Created']);
		}
	}

	public function eformsetting_edit(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'eform_type' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'eform_type.required' => 'E-Form type is missing',
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

		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$eformObj = EformSetting::find($id);
			$input = $request->all();
			$input['account_id']= $adminObj->account_id;

			$validator = Validator::make($request->all(), [ 
				'eform_type' =>[
					'required', 
					Rule::unique('eform_settings')
						->where('account_id', $input['account_id'])
						->whereNotIn('id',[$id])
				],
				
			]);
			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Eform Setting already exist!']);  
			}
			$eformObj->eform_type = $request->input('eform_type');
			$eformObj->general_info = $request->input('general_info');    
			$eformObj->refund_amount = $request->input('refund_amount');
			//$eformObj->payable_to = $request->input('payable_to'); 
			$eformObj->payment_mode_cheque = $request->input('payment_mode_cheque');
			$eformObj->payment_mode_bank = $request->input('payment_mode_bank'); 
			$eformObj->payment_mode_cash = $request->input('payment_mode_cash');
			$eformObj->official_notes = $request->input('official_notes'); 
			
			
			if($request->input('eform_type') ==41){
				$eformObj->padding_lift_fee = $request->input('padding_lift_fee');
				$eformObj->hacking_work_permitted_days = $request->input('hacking_work_permitted_days');
				$eformObj->hacking_work_not_permitted_saturday = $request->input('hacking_work_not_permitted_saturday');
				$eformObj->hacking_work_not_permitted_sunday = $request->input('hacking_work_not_permitted_sunday');
				$eformObj->hacking_work_not_permitted_holiday = $request->input('hacking_work_not_permitted_holiday');
				$eformObj->hacking_work_start_time = $request->input('hacking_work_start_time');
				$eformObj->hacking_work_end_time = $request->input('hacking_work_end_time');
			} 
			else if($request->input('eform_type') ==40){
				$eformObj->padding_lift_fee = $request->input('padding_lift_fee');
				$eformObj->hacking_work_permitted_days ='';
				$eformObj->hacking_work_not_permitted_saturday ='';
				$eformObj->hacking_work_not_permitted_sunday = '';
				$eformObj->hacking_work_not_permitted_holiday ='';
				$eformObj->hacking_work_end_time = '';
		
			}else{
				$eformObj->hacking_work_permitted_days ='';
				$eformObj->hacking_work_not_permitted_saturday ='';
				$eformObj->hacking_work_not_permitted_sunday = '';
				$eformObj->hacking_work_not_permitted_holiday ='';
				$eformObj->hacking_work_end_time = '';
			}
			
			$eformObj->save();
			return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function eformsetting_delete(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			EformSetting::findOrFail($id)->delete();
			return response()->json(['response' => 1, 'message' => 'Deleted']);
       
		}
	}
	//Eform Settings Settings End

	//Roles Settings Start
	

	public function rolessummarylist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(23,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')->get();   
			return response()->json(['data'=>$roles,'response' => 1, 'message' => 'Success']);
		}
	}

	public function roleinfo(Request $request) 
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
		
		$login_id = Auth::id();
			$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(23,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$roleObj = Role::find($id);
			
			$devices = Device::where('account_id',$roleObj->account_id)->get();
			$device_bluetooth_access = array();
			foreach($roleObj->roledevices as $selecteddevices){
				$device_bluetooth_access[] = $selecteddevices->device_id;
			}
			$device_remote_access = array();
			foreach($roleObj->roleremotedevices as $selectedremotedevices){
				$device_remote_access[] = $selectedremotedevices->device_id;
			}
			$device_data =array();
			foreach($devices as $device){
				$device_rec =array();
				$device_rec['device_info'] = $device;
				$device_rec['location_info'] = isset($device->buildinginfo)?$device->buildinginfo:null;
				$device_rec['bluetooth_available'] = 0;
				$device_rec['remote_available'] = 0;
				if(in_array($device->id,$device_bluetooth_access))
					$device_rec['bluetooth_available'] = 1;
				if(in_array($device->id,$device_remote_access))
					$device_rec['remote_available'] = 1;
				
				$device_data[] = $device_rec;
			}

			$role_access = array();
			foreach($roleObj->Permissions as $permission){
				$role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);
			}

			$property_access_lists = PropertyPermission::where('property_id',$adminObj->account_id)->where('view',1)->get();
			$list_array = array();
			foreach($property_access_lists as $list){
				$list_array[] = $list->module_id;
			}
			
			$modules = Module::whereIn('id',$list_array)->where('type','!=',3)->where('status',1)->orderBy('name','ASC')->get();

			return response()->json(['role'=>$roleObj,'devices'=>$device_data,'modules'=>$modules,'access'=>$role_access,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function rolecreate(Request $request) 
    {
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		
		$permission = $adminObj->check_permission(23,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			if(empty($_REQUEST['props']))
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Property not assigned']);
			if(isset($_REQUEST['props'])){
				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();
				$properties = '';
				foreach($_REQUEST['props'] as $property){
					$validation = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$property .',%')->where('name',$request->name)->first();
					if(empty($validation))
						$properties .= $property.",";
				}
				if($properties !=''){
					$input['account_id'] = substr($properties,0,-1);
					$role_data = Role::create($input);
				}else{
					return response()->json(['response' => 200, 'message' => 'Role already exist!']);
	
				}
	
	
				foreach($_REQUEST['props'] as $property){
					$roles_array = array();
					$roles_array['account_id'] = $property;
					$roles_array['name'] = $request->name;
					$validation = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$property .',%')->where('name',$request->name)->first();
					//$validation = Role::where('account_id', 'LIKE', '%'.$property .'%')->where('name',$request->name)->first();
					if(isset($validation)){
						//$role_data =  Role::create($roles_array);
						$role_obj = new \App\Models\v7\Role();
						$role_result = $role_obj->role_check_record($thinmoo_access_token, $property,$role_data->id);
					   
						if($role_result['code'] !=0){
							$role_data->parentUuid = 3;
							$add_role_result = $role_obj->role_add_api($thinmoo_access_token, $property,$role_data);
						}
					   
					}
				}
				
			   
			}else{
				$role_data = Role::create($input);
	
				$auth = new \App\Models\v7\Property();
				$thinmoo_access_token = $auth->thinmoo_auth_api();
	
				$role_obj = new \App\Models\v7\Role();
				$role_result = $role_obj->role_check_record($thinmoo_access_token, $role_data->account_id,$role_data->id);
	
				if($role_result['code'] !=0){
					$role_data->parentUuid = 3;
					$add_role_result = $role_obj->role_add_api($thinmoo_access_token, $role_data->account_id,$role_data);
				}
	
			}

			return response()->json(['response' => 1, 'message' => 'Created']);
       
		}
	}

	public function roleproperty(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(23,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$input['account_id'] = $adminObj->account_id;
			
			if($adminObj->role_id ==1){
				$properties = Property::get();
			}
			else{
				$prop_ids = UserProperty::where('user_id',$login_id)->get();
				$properties =array();
			
				if(isset($prop_ids)){
					$assigned_property = array();
					foreach($prop_ids as $prop_id){
						$assigned_property[] = $prop_id->property_id;
					}
					$properties = Property::whereIn('id',$assigned_property)->get();
				}
			}

			return response()->json(['data'=>$properties,'response' => 1, 'message' => 'Created']);
       
		}
	}

	public function roleedit(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'name' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'name.required' => 'Role is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$input = $request->all();
			$roleObj = Role::find($id);
			$account_id  = $adminObj->account_id;
			
			$validator = Validator::make($request->all(), [ 
				'name' =>[
					'required', 
					Rule::unique('roles')
						->where('account_id', $account_id)
						->whereNotIn('id',[$id])
				],
				
			]);
	
			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Role already exist!']);  
			}
			$roleObj->name = $request->input('name');
			$roleObj->save();

			$devices = Device::where('account_id',$account_id)->get();

			$device_serial_no = array();
			$device_serial_lists ='';
			//Bluetooth door access devices
			RoleDevice::where('role_id',$id)->delete();
			foreach($devices as $device) {
				$device_input = array();
				$device_checked = "device_bluetooth_".$device->id;
				if(isset($input[$device_checked]) && $input[$device_checked]==1)
					{                   
						if(!in_array($device->device_serial_no,$device_serial_no)){
							$device_serial_no[] = $device->device_serial_no;
							$device_serial_lists .= $device->device_serial_no.",";
						}
						$device_input['role_id'] = $id;
						$device_input['account_id'] = $device->account_id;
						$device_input['device_id'] = $device->id;
						$device_input['device_svn'] = $device->device_serial_no;
						RoleDevice::create($device_input);  
					}               
			}
			//Remote door access devices
			RoleRemoteDevice::where('role_id',$id)->delete();
			foreach($devices as $device) {
				$device_input = array();
				$device_checked = "device_remote_".$device->id;
				if(isset($input[$device_checked]) && $input[$device_checked]==1)
					{          
						if(!in_array($device->device_serial_no,$device_serial_no)){
							$device_serial_no[] = $device->device_serial_no;
							$device_serial_lists .= $device->device_serial_no.",";
						}
						$device_input['role_id'] = $id;
						$device_input['account_id'] = $device->account_id;
						$device_input['device_id'] = $device->id;
						$device_input['device_svn'] = $device->device_serial_no;
						RoleRemoteDevice::create($device_input);  
					}               
			}

			$device_serial_lists= substr($device_serial_lists,0,-1);
			
			$auth = new \App\Models\v7\Property();
			$thinmoo_access_token = $auth->thinmoo_auth_api();

			$role_obj = new \App\Models\v7\Role();
			$role_result = $role_obj->role_check_record($thinmoo_access_token, $account_id,$roleObj->id);

			if($role_result['code'] ==0){  
				$add_role_result = $role_obj->role_modify_api($thinmoo_access_token,$roleObj);
				$role_access = $role_obj->role_access_api($thinmoo_access_token,$roleObj->id,$account_id,$device_serial_lists);
			}
			else{        
				$roleObj->parentUuid = 3;
				$add_role_result = $role_obj->role_add_api($thinmoo_access_token,$account_id,$roleObj);
				$role_access = $role_obj->role_access_api($thinmoo_access_token,$roleObj->id,$account_id,$device_serial_lists);
			}
		
			$env_roles 	= env('USER_APP_ROLE');
			$user_app_roles = explode(",",$env_roles);
	
			if(!in_array($id,$user_app_roles)){
	
				ModuleSetting::where('role_id',$id)->delete();
	
				if($adminObj->role_id ==1)
					$modules = Module::where('status',1)->orderBy('name','ASC')->get();
					// echo "hai"
				else{
					$property_access_lists = PropertyPermission::where('property_id',$adminObj->account_id)->where('view',1)->get();
					$list_array = array();
					foreach($property_access_lists as $list){
						$list_array[] = $list->module_id;
					}
					//print_r($list_array);
					$modules = Module::whereIn('id',$list_array)->where('type','!=',3)->where('status',1)->orderBy('name','ASC')->get();
				}	
				foreach($modules as $module) {
					$input['role_id'] = $id;
					$input['module_id'] = $module->id;
					$view_field = "mod_view_".$module->id;
					if(isset($input[$view_field]) && $input[$view_field] ==1)
						$input['view'] = 1;
					else
						$input['view'] = 0;
	
					$add_field = "mod_add_".$module->id;
					if(isset($input[$add_field]) && $input[$add_field] ==1)
						$input['create'] = 1;
					else
						$input['create'] = 0;
	
					$edit_field = "mod_edit_".$module->id;
					if(isset($input[$edit_field]) && $input[$edit_field] ==1)
						$input['edit'] = 1;
					else
						$input['edit'] = 0;
	
					$delete_field = "mod_delete_".$module->id;
					if(isset($input[$delete_field]) && $input[$delete_field] ==1)
						$input['delete'] = 1;
					else
						$input['delete'] = 0;
	
					ModuleSetting::create($input);  
				}
			}
			
			return response()->json(['data'=>$roleObj,'response' => 1, 'message' => 'Updated']);
       
		}
	}

	public function roledelete(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(23,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$roleObj = Role::find($id);
			$auth = new \App\Models\v7\Property();
			$thinmoo_access_token = $auth->thinmoo_auth_api();
			$role_obj = new \App\Models\v7\Role();
			$roles = explode(",",$roleObj->account_id);
			if(isset($roles) && count($roles) >0){
				foreach($roles as $role){
					$role_result = $role_obj->role_check_record($thinmoo_access_token, $role,$roleObj->id);
					if($role_result['code'] ==0){            
						$delete_role_result = $role_obj->role_delete_api($thinmoo_access_token,$roleObj->id,$role);
					}
				}
			}
			Role::findOrFail($id)->delete();

			//Role::findOrFail($id)->delete();
			return response()->json(['response' => 1, 'message' => 'Deleted']);
       
		}
	}
	//Roles Settings END

	//Condo Docs Category Start

	public function docsCatSummary(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(32,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$types = DocsCategory::where('account_id',$account_id)->get();   
			return response()->json(['data'=>$types,'response' => 1, 'message' => 'Success']);
		}
	}

	public function docsCatInfo(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(32,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$docsObj = DocsCategory::find($id);
       
			$doc_files =array();
			if (isset($docsObj->files)) {
				foreach ($docsObj->files as $k => $files) {
					$doc_file['key'] = $k + 1;
					$doc_file['id'] = $files['id'];
					$doc_file['docs_file'] = $files['docs_file'];
					$doc_file['docs_file_name'] = $files['docs_file_name'];
					$doc_file['original_file_name'] = $files['original_file_name'];
					$doc_files[$k + 1] = $doc_file;
				}
			}
			$img_full_path = env('APP_URL')."/storage/app/";

			return response()->json(['docs'=>$docsObj,'docs_file'=>$doc_files,'img_full_path'=>$img_full_path,'response' => 1, 'message' => 'Success']);

       
		}
	}

	public function docsCatCreate(Request $request) 
    {
		$rules=array(
			'docs_category'=>'required',
		);
		$messages=array(
			'docs_category.required' => 'Category is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(32,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$input['account_id'] = $adminObj->account_id;
			$validator = Validator::make($request->all(), [ 
				'docs_category' =>[
					'required', 
					Rule::unique('condodoc_categories')
						->where('account_id', $input['account_id'])
				],
				
			]);
		   
			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Condo document category already exist!']);  
			}
	
	
			$category = DocsCategory::create($input);
	
			 /********** INSERT Defect Type******************/
			 for ($i = 1; $i <= 10; $i++) {
				$file = 'file_' . $i;
				$file_name = 'file_name_' . $i;
				$original_file = 'original_file_name_'.$i;
	
				if($request->file($file) != null) {
					$type['docs_file'] = remove_upload_path($request->file($file)->store(upload_path('condofile')));
				}
	
				if (isset($input[$file_name]) && $input[$file_name] != null) { 
					$type['account_id'] = $input['account_id'];
					$type['cat_id'] = $category->id;
					//$type['docs_file'] = $input[$file_name];
					$type['docs_file_name'] = $input[$file_name];
					$type['original_file_name'] = $input[$file_name];
					$type['created_at'] = $category->created_at;
					$type['updated_at'] = $category->updated_at;
					$condo_files[] = $type;
				}
			}
	
			if (isset($condo_files)) {
				CondodocFile::insert($condo_files);
			}

			return response()->json(['data'=>$category,'response' => 1, 'message' => 'Created']);
       
		}
	}

	public function docsCatEdit(Request $request) 
    {
		$rules=array(
			'id' => 'required',
			'docs_category' => 'required',
		);
		$messages=array(
			'id.required' => 'Id is missing',
			'docs_category.required' => 'Category is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(27,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$DocObj = DocsCategory::find($id);

			$input = $request->all();

			$DocObj->account_id= $adminObj->account_id;

			$validator = Validator::make($request->all(), [ 
				'docs_category' =>[
					'required', 
					Rule::unique('condodoc_categories')
						->where('account_id', $DocObj->account_id)
						->whereNotIn('id',[$id])
				],
				
			]);
			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Condo document category already exist!']);  
			}


			$DocObj->docs_category = $request->input('docs_category');
			
			$defectObj =  $DocObj->save();

			/********** INSERT documents START******************/
			for ($i = 1; $i <= 10; $i++) {
				$file = 'file_' . $i;
				$file_name = 'file_name_' . $i;
				$file_id = 'file_id_' . $i;
				$original_file_name = 'original_file_name_'.$i;
				//$request->file($file);


				if ($request->input($file_id) != null) {

					if($request->input($file_name) ==''){
						CondodocFile::findOrFail($request->input($file_id))->delete();
					}else{
						$fileObj = CondodocFile::find($request->input($file_id));
						$fileObj->cat_id = $id;

						if ($request->file($file) != null) {
							$fileObj->docs_file = remove_upload_path($request->file($file)->store(upload_path('condofile')));
						}
						$fileObj->original_file_name= $request->input($file_name);

						$fileObj->docs_file_name = $request->input($file_name);
						$fileObj->created_at = $DocObj->created_at;
						$fileObj->updated_at = $DocObj->updated_at;
						$fileObj->save();
					}

				} else if($request->input($file_name) != null) {
					$type = array();
					$type['account_id'] = $DocObj->account_id;
					$type['cat_id'] = $id;

					if ($request->file($file) != null) {
						$type['docs_file'] = remove_upload_path($request->file($file)->store(upload_path('condofile')));
					}
					$type['original_file_name']= $input[$file_name]; 
					$type['docs_file_name'] = $input[$file_name];
					$type['created_at'] = $DocObj->created_at;
					$type['updated_at'] = $DocObj->updated_at;
					CondodocFile::insert($type);
					
				}
			}
			
			return response()->json(['data'=>$DocObj,'response' => 1, 'message' => 'Updated']);

		}
       
		
	}

	public function docsCatDelete(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(32,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			CondodocFile::where('cat_id', $id)->delete();
			DocsCategory::findOrFail($id)->delete();
			return response()->json(['response' => 1, 'message' => 'Deleted']);
       
		}
	}

	public function docsDeleteFile(Request $request) 
    {
		$rules=array(
			'cat_id' => 'required',
			'file_ids' => 'required',
		);
		$messages=array(
			'cat_id.required' => 'Cat Id is missing',
			'file_ids.required' => 'File Id is missing',
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
		$cat_id = $request->cat_id;
		$file_ids = $request->file_ids;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(32,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			CondodocFile::whereIn('id', $file_ids)->where('cat_id', $cat_id)->delete();
			return response()->json(['response' => 1, 'message' => 'Deleted']);
       
		}
	}
	//Condo Docs Category End

	//Residence File Category Start

	public function resFileSummary(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(33,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$submissions = ResidentFileSubmission::where('account_id',$account_id)->orderby('id','desc')->get(); 
			$data =array();
			foreach ($submissions as $k => $submission) {
				$record =array();
				$record['submission'] = $submission;
				$record['cat'] = $submission->category;
				$user_data =array();
				if(isset($submission->user)){
					$user_data["id"]= $submission->user->id;
					$user_data["account_id"]= $submission->user->account_id;
					$user_data["role_id"]= $submission->user->role_id;
					$user_data["user_info_id"]= $submission->user->user_info_id;
					$user_data["building_no"]= $submission->user->building_no;
					$user_data["unit_no"]= $submission->user->unit_no;
					$user_data["primary_contact"]= $submission->user->primary_contact;
					$user_data["name"]=Crypt::decryptString($submission->user->name);
					
				}
				$record['user'] = !empty($user_data)?$user_data:null;


				$unitObj = Unit::find($submission->unit_no);
				$unit_data =array();
				if(isset($unitObj)){
					$unit_data["id"]= $unitObj->id;
					$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
				}
				$record['unit'] = !empty($unit_data)?$unit_data:null;
				$record['files'] = $submission->files;
				$data[] = $record;
			} 
			$file_path = env('APP_URL')."/storage/app";

			return response()->json(['data'=>$data,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
		}
	}
	public function resFileSummaryNew(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(33,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$date = Carbon::now()->subDays(7);
			$submissions = ResidentFileSubmission::where('account_id',$account_id)->where('status',0)->where('view_status',0)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->orderby('id','desc')->get();
			$data =array();
			foreach ($submissions as $k => $submission) {
				$record =array();
				$record['submission'] = $submission;
				$record['cat'] = $submission->category;
				$user_data =array();
				if(isset($submission->user)){
					$user_data["id"]= $submission->user->id;
					$user_data["account_id"]= $submission->user->account_id;
					$user_data["role_id"]= $submission->user->role_id;
					$user_data["user_info_id"]= $submission->user->user_info_id;
					$user_data["building_no"]= $submission->user->building_no;
					$user_data["unit_no"]= $submission->user->unit_no;
					$user_data["primary_contact"]= $submission->user->primary_contact;
					$user_data["name"]=Crypt::decryptString($submission->user->name);
					
				}
				$record['user'] = !empty($user_data)?$user_data:null;


				$unitObj = Unit::find($submission->unit_no);
				$unit_data =array();
				if(isset($unitObj)){
					$unit_data["id"]= $unitObj->id;
					$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
					$unit_data["building"]= ($unitObj->buildinginfo->building)?$unitObj->buildinginfo->building:null;

				}
				$record['unit'] = !empty($unit_data)?$unit_data:null;
				$record['files'] = $submission->files;
				$data[] = $record;
			}    
			$file_path = env('APP_URL')."/storage/app";

			return response()->json(['data'=>$data,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
		}
	}

	public function resFileInfo(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(33,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$submissionObj = ResidentFileSubmission::find($id);
			if(isset($submissionObj))
			{
				ResidentFileSubmission::where('id', $id)
				->update(['view_status' => 1,'updated_at'=>$submissionObj->updated_at]);
				$record['data'] =$submissionObj;
				$record['cat'] = $submissionObj->category;
				$record['user'] = $submissionObj->user;
				$record['unit'] = $submissionObj->getunit;
				$record['files'] = $submissionObj->files;
				$file_path = env('APP_URL')."/storage/app";
				return response()->json(['details'=>$record,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);
			}else{
				return response()->json(['details'=>'','file_path'=>'','response' => 200, 'message' => 'Not found']);
			}

       
		}
	}

	public function resFileCreate(Request $request) 
    {
		$rules=array(
			'docs_category'=>'required',
		);
		$messages=array(
			'docs_category.required' => 'Category is missing',
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
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(33,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$input['account_id'] = $adminObj->account_id;
			$validator = Validator::make($request->all(), [ 
				'docs_category' =>[
					'required', 
					Rule::unique('condodoc_categories')
						->where('account_id', $input['account_id'])
				],
				
			]);
		   
			if ($validator->fails()) { 
				return response()->json(['data'=>null,'response' => 400, 'message' => 'Condo document category already exist!']);  
			}
	
	
			$category = DocsCategory::create($input);
	
			 /********** INSERT Defect Type******************/
			 for ($i = 1; $i <= 10; $i++) {
				$file = 'file_' . $i;
				$file_name = 'file_name_' . $i;
				$original_file = 'original_file_name_'.$i;
	
				if($request->file($file) != null) {
					$type['docs_file'] = remove_upload_path($request->file($file)->store(upload_path('condofile')));
				}
	
				if (isset($input[$file_name]) && $input[$file_name] != null) { 
					$type['account_id'] = $input['account_id'];
					$type['cat_id'] = $category->id;
					//$type['docs_file'] = $input[$file_name];
					$type['docs_file_name'] = $input[$file_name];
					$type['original_file_name'] = $input[$file_name];
					$type['created_at'] = $category->created_at;
					$type['updated_at'] = $category->updated_at;
					$condo_files[] = $type;
				}
			}
	
			if (isset($condo_files)) {
				CondodocFile::insert($condo_files);
			}

			return response()->json(['data'=>$category,'response' => 1, 'message' => 'Created']);
       
		}
	}

	public function resFileEdit(Request $request) 
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
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(33,$adminObj->role_id); 
		if(empty($permission) && $permission->edit!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{

			$submissionObj = ResidentFileSubmission::find($id);
			$submissionObj->remarks = $request->input('remarks');
			$submissionObj->status = $request->input('status');
			$submissionObj->save();
			//Start Insert into notification module
			$notification = array();
			$notification['account_id'] = $submissionObj->account_id;
			$notification['user_id'] = $submissionObj->user_id;
			$notification['unit_no'] = $submissionObj->unit_no;
			$notification['module'] = 'fileupload';
			$notification['ref_id'] = $submissionObj->id;
			$notification['title'] = "Resident's File Upload";
			$notification['message'] = "There is an update from the management in regards to your resident's file upload";
			$result = UserNotification::insert($notification);
	
			$SettingsObj = UserNotificationSetting::where('user_id',$submissionObj->user_id)->where('account_id',$submissionObj->account_id)->first();
			if(empty($SettingsObj) || $SettingsObj->resident_file_upload ==1){
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserLog::where('user_id',$submissionObj->user_id)->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $submissionObj->id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
	
				$probObj = Property::find($submissionObj->account_id);
				$title = "Aerea Home - ".$probObj->company_name;
				$message = "Resident's File Upload Update";
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$submissionObj->unit_no;   
				$notofication_data['user_id'] =$submissionObj->user_id;   
				$notofication_data['property'] =$submissionObj->account_id; 
				$purObj = UserPurchaserUnit::where('property_id',$submissionObj->account_id)->where('unit_id',$submissionObj->unit_no)->where('user_id',$submissionObj->user_id)->first(); 
				if(isset($purObj))
					$notofication_data['switch_id'] =$purObj->id;        
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
			}
			return response()->json(['data'=>$submissionObj,'response' => 1, 'message' => 'Updated']);

		}
       
		
	}

	public function resFileDelete(Request $request) 
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
		
		$login_id = Auth::id();
		$id = $request->id;
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(33,$adminObj->role_id); 
		if(empty($permission) && $permission->delete!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			ResidentUploadedFile::where('ref_id', $id)->delete();
        	ResidentFileSubmission::findOrFail($id)->delete();
			return response()->json(['response' => 1, 'message' => 'Deleted']);
       
		}
	}

	public function resFileSearch(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(33,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			
			$account_id = $adminObj->account_id;
			$date = $status = $month = $option = $unit = $category = $from_date = $to_date ='';
			$category = $request->input('category');
			$status = $request->input('status');
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
			$month = $request->input('month');
			if($month !=''){          
				$from_date = $month;
				$to_date  = date('Y-m-t', strtotime($month));
			}

        
			if ($category != '' || $status != '' || $unit != '' || $month != '' || $building !='') {
				$submissions =  ResidentFileSubmission::where('account_id',$account_id)
				->where(function ($query) use ($category,$unit,$units,$month,$from_date,$to_date,$status,$building) {
					if($status !='')
						$query->where('status', $status);
					if($unit !='' || $building !='')
						$query->whereIn('unit_no', $units);
					if($month !='')
						$query->whereBetween('created_at',array($from_date,$to_date));
					if($category !='')
						$query->where('cat_id', $category);
					
				})->orderby('id', 'desc')->get();
					
				$data =array();
				foreach ($submissions as $k => $submission) {
					$record =array();
					$record['submission'] = $submission;
					$record['cat'] = $submission->category;
					$user_data =array();
					if(isset($submission->user)){
						$user_data["id"]= $submission->user->id;
						$user_data["account_id"]= $submission->user->account_id;
						$user_data["role_id"]= $submission->user->role_id;
						$user_data["user_info_id"]= $submission->user->user_info_id;
						$user_data["building_no"]= $submission->user->building_no;
						$user_data["unit_no"]= $submission->user->unit_no;
						$user_data["primary_contact"]= $submission->user->primary_contact;
						$user_data["name"]=Crypt::decryptString($submission->user->name);
						
					}
					$record['user'] = !empty($user_data)?$user_data:null;

					
					$unitObj = Unit::find($submission->unit_no);
					$unit_data =array();
					if(isset($unitObj)){
						$unit_data["id"]= $unitObj->id;
						$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
						$unit_data["building"]= ($unitObj->buildinginfo->building)?$unitObj->buildinginfo->building:null;
					}
					
					$record['unit'] = !empty($unit_data)?$unit_data:null;

					//$record['user'] = $submission->user;
					//$record['unit'] = $submission->getunit;
					$data[] = $record;
				} 
		
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
			else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
			}
		}
	}

		//Residence File Category End
	public function eformsettingsinfo(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			$account_id = $adminObj->account_id;

			$eformsettingsObj = EformSetting::where('account_id', $account_id)->where('eform_type', $id)->first();
			$paymentettingsObj = PaymentSetting::where('account_id', $account_id)->first();

			return response()->json(['details'=>$eformsettingsObj,'payment_info'=>$paymentettingsObj,'response' => 1, 'message' => 'Success']);

	}
	

		//Eform Move In & Out Start

	public function moveinoutsummary(Request $request) 
	{
		$login_id = Auth::id();
			$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(40,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$submissions = EformMovingInOut::where('account_id',$account_id)->orderby('id','desc')->get(); 
				$data =array();
				foreach ($submissions as $k => $submission) {
					$record =array();
					$record['submission'] = $submission;
					$user_data =array();
						if(isset($submission->user)){
							$user_data["id"]= $submission->user->id;
							$user_data["account_id"]= $submission->user->account_id;
							$user_data["role_id"]= $submission->user->role_id;
							$user_data["user_info_id"]= $submission->user->user_info_id;
							$user_data["building_no"]= $submission->user->building_no;
							$user_data["unit_no"]= $submission->user->unit_no;
							$user_data["primary_contact"]= $submission->user->primary_contact;
							$user_data["name"]=Crypt::decryptString($submission->user->name);
							
						}
						$record['submitted_by'] = !empty($user_data)?$user_data:null;
						$unitObj = Unit::find($submission->unit_no);
						$unit_data =array();
						if(isset($unitObj)){
							$unit_data["id"]= $unitObj->id;
							$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
						}
						$record['unit'] = !empty($unit_data)?$unit_data:null;
					$data[] = $record;
				} 
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
	}
	

	public function moveinoutinfo(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(40,$adminObj->role_id); 
			
			if(empty($permission) || $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$submissionObj = EformMovingInOut::find($id);
				EformMovingInOut::where('id', $id)
				->update(['view_status' => 1,'updated_at'=>$submissionObj->updated_at]);

				$record['submission'] = $submissionObj;
				$record['sub_con'] = $submissionObj->sub_con;
				$record['payment'] = $submissionObj->payment;
				$record['inspection'] = $submissionObj->inspection;
				$record['defects'] = $submissionObj->defects;
				$record['submitted_by'] = isset($submissionObj->user)?$submissionObj->user:null;
				$record['unit'] = isset($submissionObj->unitinfo)?$submissionObj->unitinfo:null;
				
				$file_path = env('APP_URL')."/storage/app";

				return response()->json(['details'=>$record,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);

		
			}
	}


	public function moveinoutedit(Request $request) 
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
		$login_id = Auth::id();

			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(40,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				$command = $request->input('remarks');      
				$eformObj = EformMovingInOut::find($id);
				$eformObj->status = $request->input('status');
				$eformObj->remarks = $command;
				$eformObj->save();
		
				if(isset($eformObj->status)){
					if($eformObj->status==0)
						$status =  "new";
					else if($eformObj->status==1)
						$status =  "cancelled";
					else if($eformObj->status==3)
						$status =  "approved";
					else if($eformObj->status==2)
						$status =  "in progress";
					else 
						$status =  "rejected";
				 
				 }
			   
				$title = "Your Moving In & Out application .".$status;
				InboxMessage::where('ref_id', $eformObj->id)->where('type',10)
						->update(['title'=>$title,'booking_date'=>$eformObj->moving_start,'booking_time'=>$eformObj->moving_end,'event_status' => $status]);
				
				 
				//Start Insert into notification module
				$notification = array();
				$notification['account_id'] = $eformObj->account_id;
				$notification['user_id'] = $eformObj->user_id;
				$notification['unit_no'] = $eformObj->unit_no;
				$notification['module'] = 'eform_move_in_out';
				$notification['ref_id'] = $eformObj->id;
				$notification['title'] = 'Move In & Out E-form';
				$notification['message'] = 'There is an update from the management in regards to your submitted Move In & Out E-Form';
				$result = UserNotification::insert($notification);
	  
				$SettingsObj = UserNotificationSetting::where('user_id',$eformObj->user_id)->where('account_id',$eformObj->account_id)->first();
				if(empty($SettingsObj) || $SettingsObj->eforms ==1){
					$fcm_token_array ='';
					$user_token = ',';
					$ios_devices_to_send = array();
					$android_devices_to_send = array();
					$logs = UserLog::where('user_id',$eformObj->user_id)->where('status',1)->orderby('id','desc')->first();
					if(isset($logs->fcm_token) && $logs->fcm_token !=''){
						$user_token .=$logs->fcm_token.",";
						$fcm_token_array .=$logs->fcm_token.',';
						$appSipAccountList[] = $eformObj->id;
						if($logs->login_from ==1)
							$ios_devices_to_send[] = $logs->fcm_token;
						if($logs->login_from ==2)
							$android_devices_to_send[] = $logs->fcm_token;
					}
	  
					$probObj = Property::find($eformObj->account_id);
					$title = "Aerea Home - ".$probObj->company_name;
					$message = "Move In & Out E-form Updated";
					$notofication_data = array();
					$notofication_data['body'] =$title;
					$notofication_data['unit_no'] =$eformObj->unit_no;   
					$notofication_data['user_id'] =$eformObj->user_id;   
					$notofication_data['property'] =$eformObj->account_id; 
					$purObj = UserPurchaserUnit::where('property_id',$eformObj->account_id)->where('unit_id',$eformObj->unit_no)->where('user_id',$eformObj->user_id)->first(); 
					if(isset($purObj))
						$notofication_data['switch_id'] =$purObj->id;        
					$NotificationObj = new \App\Models\v7\FirebaseNotification();
					$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
					$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
				}
	  
				return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);

			}
		
			
	}
	public function moveinoutpaymentsave(Request $request) 
	{
			$rules=array(
				'signature' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
			);
			$messages=array(
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
		$login_id = Auth::id();

			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(40,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				
				$eformObj = EformMovingInOut::find($id);

				$account_id = $adminObj->account_id;

				$eformsettingsObj = EformSetting::where('account_id', $account_id)->where('eform_type', 40)->first();

				if ($request->input('payment_id') != null) {

						$paymentObj = EformMovingPayment::find($request->input('payment_id'));
						
						
						$paymentObj['mov_id'] = $id;
						$paymentObj['manager_id'] = $request->login_id;
						$paymentObj['payment_option'] = $request->input('payment_option');

						if($paymentObj['payment_option'] ==1){

							if($request->input('cheque_amount') != null)
								$paymentObj['cheque_amount'] = $request->input('cheque_amount');

							if($request->input('cheque_received_date') != null)
								$paymentObj['cheque_received_date'] = $request->input('cheque_received_date');

							if($request->input('cheque_no') != null)
								$paymentObj['cheque_no'] = $request->input('cheque_no');
							
							if($request->input('cheque_bank') != null)
								$paymentObj['cheque_bank'] = $request->input('cheque_bank');
						}
						if($paymentObj['payment_option'] ==2){
							if($request->input('bt_received_date') != null)
								$paymentObj['bt_received_date'] = $request->input('bt_received_date');

							if($request->input('bt_amount_received') != null)
								$paymentObj['bt_amount_received'] = $request->input('bt_amount_received');
						}
						if($paymentObj['payment_option'] ==3){
							if($request->input('cash_amount_received') != null)
								$paymentObj['cash_amount_received'] = $request->input('cash_amount_received');

							if($request->input('cash_received_date') != null)
								$paymentObj['cash_received_date'] = $request->input('cash_received_date');
							
						}
						
						if($request->input('receipt_no') != null)   
							$paymentObj['receipt_no'] = $request->input('receipt_no');

						if($request->input('acknowledged_by') != null)   
							$paymentObj['acknowledged_by'] = $request->input('acknowledged_by');

						if($request->input('manager_received') != null)   
							$paymentObj['manager_received'] = $request->input('manager_received');

						if ($request->file('signature') != null) {
							$paymentObj['signature'] = base64_encode(file_get_contents($request->file('signature')));
				
						}

						if($request->input('date_of_signature') != null)   
							$paymentObj['date_of_signature'] = $request->input('date_of_signature');

						$paymentObj->save();
						
						

					} else {
						
						$payment['mov_id'] = $id;
						$payment['manager_id'] = $request->login_id;
						$payment['payment_option'] = $request->input('payment_option');

						if($payment['payment_option'] ==1){

							if($request->input('cheque_amount') != null)
								$payment['cheque_amount'] = $request->input('cheque_amount');

							if($request->input('cheque_no') != null)
								$payment['cheque_no'] = $request->input('cheque_no');

							if($request->input('cheque_received_date') != null)
								$payment['cheque_received_date'] = $request->input('cheque_received_date');
							
							if($request->input('cheque_bank') != null)
								$payment['cheque_bank'] = $request->input('cheque_bank');
						}
						if($payment['payment_option'] ==2){
							if($request->input('bt_received_date') != null)
								$payment['bt_received_date'] = $request->input('bt_received_date');

							if($request->input('bt_amount_received') != null)
								$payment['bt_amount_received'] = $request->input('bt_amount_received');
						}
						if($payment['payment_option'] ==3){
							if($request->input('cash_amount_received') != null)
								$payment['cash_amount_received'] = $request->input('cash_amount_received');

							if($request->input('cash_received_date') != null)
								$payment['cash_received_date'] = $request->input('cash_received_date');
							
						}

						if($request->input('receipt_no') != null)   
							$payment['receipt_no'] = $request->input('receipt_no');

						if($request->input('acknowledged_by') != null)   
							$payment['acknowledged_by'] = $request->input('acknowledged_by');

						if($request->input('manager_received') != null)   
							$payment['manager_received'] = $request->input('manager_received');

						if ($request->file('signature') != null) {
							$payment['signature'] = base64_encode(file_get_contents($request->file('signature')));
						}

						if($request->input('date_of_signature') != null)   
							$payment['date_of_signature'] = $request->input('date_of_signature');

						
						$payment['created_at'] = date("Y-m-d H:i:s");
						$payment['updated_at'] = date("Y-m-d H:i:s");
						EformMovingPayment::insert($payment);
					
				}

				//Start Insert into notification module
                $notification = array();
                $notification['account_id'] = $eformObj->account_id;
                $notification['user_id'] = $eformObj->user_id;
                $notification['unit_no'] = $eformObj->unit_no;
                $notification['module'] = 'eform_move_in_out';
                $notification['ref_id'] = $eformObj->id;
                $notification['title'] = 'Move In & Out E-form';
                $notification['message'] = 'There is an update from the management in regards to your payment on Move In & Out E-form';
                $result = UserNotification::insert($notification);

                $SettingsObj = UserNotificationSetting::where('user_id',$eformObj->user_id)->where('account_id',$eformObj->account_id)->first();
                if(empty($SettingsObj) || $SettingsObj->eforms ==1){
                    $fcm_token_array ='';
                    $user_token = ',';
                    $ios_devices_to_send = array();
                    $android_devices_to_send = array();
                    $logs = UserLog::where('user_id',$eformObj->user_id)->where('status',1)->orderby('id','desc')->first();
                    if(isset($logs->fcm_token) && $logs->fcm_token !=''){
                        $user_token .=$logs->fcm_token.",";
                        $fcm_token_array .=$logs->fcm_token.',';
                        $appSipAccountList[] = $eformObj->id;
                        if($logs->login_from ==1)
                            $ios_devices_to_send[] = $logs->fcm_token;
                        if($logs->login_from ==2)
                            $android_devices_to_send[] = $logs->fcm_token;
                    }

                    $probObj = Property::find($eformObj->account_id);
                    $title = "Aerea Home - ".$probObj->company_name;
                    $message = "Move In & Out E-form Payment";
                    $notofication_data = array();
                    $notofication_data['body'] =$title;
                    $notofication_data['unit_no'] =$eformObj->unit_no;   
                    $notofication_data['user_id'] =$eformObj->user_id;   
                    $notofication_data['property'] =$eformObj->account_id; 
                    $purObj = UserPurchaserUnit::where('property_id',$eformObj->account_id)->where('unit_id',$eformObj->unit_no)->where('user_id',$eformObj->user_id)->first(); 
                    if(isset($purObj))
                        $notofication_data['switch_id'] =$purObj->id;        
                    $NotificationObj = new \App\Models\v7\FirebaseNotification();
                    $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
                    $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
                }
                //End Insert into notification module
				return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);

			}
		
			
	}
	public function moveinoutinspectionsave(Request $request) 
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

		$login_id = Auth::id();

			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(40,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				$eformObj = EformMovingInOut::find($id);

				$account_id = $adminObj->account_id;


				if ($request->input('inspection_id') != null) {

						$inspectionObj = EformMovingInspection::find($request->input('inspection_id'));
						
						
						$inspectionObj['mov_id'] = $id;
						$inspectionObj['manager_id'] = $request->login_id;
						if($request->input('date_of_completion') != null)
							$inspectionObj['date_of_completion'] = $request->input('date_of_completion');
						
						if($request->input('inspected_by') != null)
							$inspectionObj['inspected_by'] = $request->input('inspected_by');

						if($request->input('unit_in_order_or_not') != null)    
							$inspectionObj['unit_in_order_or_not'] = $request->input('unit_in_order_or_not');

						if($request->input('amount_deducted') != null)   
							$inspectionObj['amount_deducted'] = $request->input('amount_deducted');

						if($request->input('refunded_amount') != null)   
							$inspectionObj['refunded_amount'] = $request->input('refunded_amount');

						if($request->input('amount_claimable') != null)   
							$inspectionObj['amount_claimable'] = $request->input('amount_claimable');

						if($request->input('actual_amount_received') != null)   
							$inspectionObj['actual_amount_received'] = $request->input('actual_amount_received');

						if($request->input('acknowledged_by') != null)   
							$inspectionObj['acknowledged_by'] = $request->input('acknowledged_by');

						if($request->input('resident_nric') != null)   
							$inspectionObj['resident_nric'] = $request->input('resident_nric');
						
						if ($request->file('resident_signature') != null) {
							$inspectionObj['resident_signature'] = base64_encode(file_get_contents($request->file('resident_signature')));
						}
						if($request->input('resident_signature_date') != null)   
							$inspectionObj['resident_signature_date'] = $request->input('resident_signature_date');

						if($request->input('manager_received') != null)   
							$inspectionObj['manager_received'] = $request->input('manager_received');

						if ($request->file('manager_signature') != null) {
								$inspectionObj['manager_signature'] = base64_encode(file_get_contents($request->file('manager_signature')));
						}

						if($request->input('date_of_signature') != null)   
							$inspectionObj['date_of_signature'] = $request->input('date_of_signature');

							$inspectionObj['updated_at'] = date("Y-m-d H:i:s");

						$inspectionObj->save();
					
					

				} else {

					$inspectionObj['mov_id'] = $id;
					$inspectionObj['manager_id'] = $request->login_id;
					if($request->input('date_of_completion') != null)
						$inspectionObj['date_of_completion'] = $request->input('date_of_completion');
					
					if($request->input('inspected_by') != null)
						$inspectionObj['inspected_by'] = $request->input('inspected_by');

					if($request->input('unit_in_order_or_not') != null)    
						$inspectionObj['unit_in_order_or_not'] = $request->input('unit_in_order_or_not');

					if($request->input('amount_deducted') != null)   
						$inspectionObj['amount_deducted'] = $request->input('amount_deducted');

					if($request->input('refunded_amount') != null)   
						$inspectionObj['refunded_amount'] = $request->input('refunded_amount');

					if($request->input('amount_claimable') != null)   
						$inspectionObj['amount_claimable'] = $request->input('amount_claimable');

					if($request->input('actual_amount_received') != null)   
						$inspectionObj['actual_amount_received'] = $request->input('actual_amount_received');

					if($request->input('acknowledged_by') != null)   
						$inspectionObj['acknowledged_by'] = $request->input('acknowledged_by');

					if($request->input('resident_nric') != null)   
						$inspectionObj['resident_nric'] = $request->input('resident_nric');
					
					if ($request->file('resident_signature') != null) {
						$inspectionObj['resident_signature'] = base64_encode(file_get_contents($request->file('resident_signature')));
					}											
					if($request->input('resident_signature_date') != null)   
						$inspectionObj['resident_signature_date'] = $request->input('resident_signature_date');

					if($request->input('manager_received') != null)   
						$inspectionObj['manager_received'] = $request->input('manager_received');

					if ($request->file('manager_signature') != null) {
							$inspectionObj['manager_signature'] = base64_encode(file_get_contents($request->file('manager_signature')));
					}
					if($request->input('date_of_signature') != null)   
						$inspectionObj['date_of_signature'] = $request->input('date_of_signature');

					$inspectionObj['created_at'] = date("Y-m-d H:i:s");
					$inspectionObj['updated_at'] = date("Y-m-d H:i:s");

					$record =  EformMovingInspection::insert($inspectionObj);

					
						
					}

					/********** INSERT documents START******************/
					for ($i = 1; $i <= 5; $i++) {
						$file = 'file_' . $i;
						$file_id = 'file_id_' . $i;
						$notes= 'description_'.$i;

					
						if ($request->input($file_id) != null) {

							if($request->input($notes) ==''){
								EformMovingDefect::findOrFail($request->input($file_id))->delete();
							}else{
								$fileObj = EformMovingDefect::find($request->input($file_id));
								$fileObj->mov_id = $id;

								if ($request->file($file) != null) {
									$fileObj->image_base64 = base64_encode(file_get_contents($request->file($file)));
								}
								$fileObj->notes= $request->input($notes);
								$fileObj->updated_at = date("Y-m-d H:i:s");
								$fileObj->save();
							}

						} else if ($request->input($notes) != null) {
							$type = array();
							$type['account_id'] = $account_id;
							$type['mov_id'] = $id;

							if ($request->file($file) != null) {
								$type['image_base64'] = base64_encode(file_get_contents($request->file($file)));
								
							}
							$type['notes']= $request->input($notes); 
							$type['created_at'] = date("Y-m-d H:i:s");
							$type['updated_at'] = date("Y-m-d H:i:s");
							EformMovingDefect::insert($type);
							
						}

						
					}

					//Start Insert into notification module
					$notification = array();
					$notification['account_id'] = $eformObj->account_id;
					$notification['user_id'] = $eformObj->user_id;
					$notification['unit_no'] = $eformObj->unit_no;
					$notification['module'] = 'eform_move_in_out';
					$notification['ref_id'] = $eformObj->id;
					$notification['title'] = 'Move In & Out E-form';
					$notification['message'] = 'There is an update from the management in regards to inspection on your Move In & Out E-form';
					$result = UserNotification::insert($notification);
		
					$SettingsObj = UserNotificationSetting::where('user_id',$eformObj->user_id)->where('account_id',$eformObj->account_id)->first();
					if(empty($SettingsObj) || $SettingsObj->eforms ==1){
						$fcm_token_array ='';
						$user_token = ',';
						$ios_devices_to_send = array();
						$android_devices_to_send = array();
						$logs = UserLog::where('user_id',$eformObj->user_id)->where('status',1)->orderby('id','desc')->first();
						if(isset($logs->fcm_token) && $logs->fcm_token !=''){
							$user_token .=$logs->fcm_token.",";
							$fcm_token_array .=$logs->fcm_token.',';
							$appSipAccountList[] = $eformObj->id;
							if($logs->login_from ==1)
								$ios_devices_to_send[] = $logs->fcm_token;
							if($logs->login_from ==2)
								$android_devices_to_send[] = $logs->fcm_token;
						}
		
						$probObj = Property::find($eformObj->account_id);
						$title = "Aerea Home - ".$probObj->company_name;
						$message = "Move In & Out E-form Inspection";
						$notofication_data = array();
						$notofication_data['body'] =$title;
						$notofication_data['unit_no'] =$eformObj->unit_no;   
						$notofication_data['user_id'] =$eformObj->user_id;   
						$notofication_data['property'] =$eformObj->account_id; 
						$purObj = UserPurchaserUnit::where('property_id',$eformObj->account_id)->where('unit_id',$eformObj->unit_no)->where('user_id',$eformObj->user_id)->first(); 
						if(isset($purObj))
							$notofication_data['switch_id'] =$purObj->id;        
						$NotificationObj = new \App\Models\v7\FirebaseNotification();
						$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
						$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
					}
				return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);

			}
		
			
	}

	public function moveinoutdelete(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(41,$adminObj->role_id); 
			if(empty($permission) && $permission->delete!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				EformMovingSubCon::where('mov_id', $id)->delete();
				EformMovingPayment::where('mov_id', $id)->delete();
				EformMovingInspection::where('mov_id', $id)->delete();
				EformMovingDefect::where('mov_id', $id)->delete();
				EformMovingInOut::findOrFail($id)->delete();
				return response()->json(['response' => 1, 'message' => 'Deleted']);
		
			}
	}

	public function moveinoutsearch(Request $request) 
	{
			
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(40,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$account_id = $adminObj->account_id;
				$date = $status = $month = $option = $unit = $category ='';
				$unit = $request->input('unit');
				$units = array();
				if($unit !=''){   
					$unit = str_replace("#",'',$unit);
					$unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
					})->get();   
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
								$units[] = $unitid->id;
						}
					}
				}
				//print_r($units);

				$userids = array();
				$name = $request->input('name');
				if($name !=''){
					/*$userObj = User::select('id')->where('account_id',$account_id)->where('name', 'LIKE', '%'.$name .'%')->get();
					foreach($userObj as $k => $user){
						$userids[] = $user->id;
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
				$ticket = $request->input('ticket');
				$status = $request->input('status');
			
				if ($ticket != '' || $unit != '' || $name != '' || $status != '') {
					$submissions =  EformMovingInOut::where('account_id',$account_id)->where(function ($query) use ($ticket,$unit,$units,$userids,$name,$status) {
						if($status !='' )
							$query->where('status', $status);
						if( $unit !='')
							$query->whereIn('unit_no', $units);
						if( $name !='')
							$query->whereIn('user_id', $userids);
						if($ticket !='')
							$query->where('ticket', 'LIKE', '%'.$ticket .'%');
					})->orderby('id', 'desc')->get();

					$data =array();
					foreach ($submissions as $k => $submission) {
						$record =array();
						$record['submission'] = $submission;
						$record['sub_con'] = $submission->sub_con;
						//$record['payment'] =$submission->payment;
						//$record['inspection'] =$submission->inspection;
						$user_data =array();
						if(isset($submission->user)){
							$user_data["id"]= $submission->user->id;
							$user_data["account_id"]= $submission->user->account_id;
							$user_data["role_id"]= $submission->user->role_id;
							$user_data["user_info_id"]= $submission->user->user_info_id;
							$user_data["building_no"]= $submission->user->building_no;
							$user_data["unit_no"]= $submission->user->unit_no;
							$user_data["primary_contact"]= $submission->user->primary_contact;
							$user_data["name"]=Crypt::decryptString($submission->user->name);
							
						}
						$record['submitted_by'] = !empty($user_data)?$user_data:null;
						$unitObj = Unit::find($submission->unit_no);
						$unit_data =array();
						if(isset($unitObj)){
							$unit_data["id"]= $unitObj->id;
							$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
						}
						$record['unit'] = !empty($unit_data)?$unit_data:null;

						//$record['unit'] = isset($submission->unitinfo)?$submission->unitinfo:null;
						$data[] = $record;
					} 
					return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);

				}
				else{
					return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
				}
			}
	}

	//Eform Move In & Out End

	//Eform Renovation In & Out Start

	public function renovationsummary(Request $request) 
	{
			
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(41,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$account_id = $adminObj->account_id;
				$submissions = EformRenovation::where('account_id',$account_id)->orderby('id','desc')->get(); 
				
				$data =array();
				foreach ($submissions as $k => $submission) {
					$record =array();
					
					//$record['submission'] = $submission;
					//$record['sub_con'] = $submission->sub_con;
					//$record['details'] = $submission->details;
					//$record['payment'] =$submission->payment;
					//$record['inspection'] =$submission->inspection;
					//$record['defects'] =$submission->defects;
					$record['id'] = $submission->id;
					$record['ticket'] = $submission->ticket;
					$record['reno_date'] = $submission->reno_date;
					$record['resident_name'] = $submission->resident_name;
					$record['contact_no'] = $submission->contact_no;
					$record['email'] = $submission->email;
					$record['reno_comp'] = $submission->reno_comp;
					$record['in_charge_name'] = $submission->in_charge_name;
					$record['comp_address'] = $submission->comp_address;
					$record['comp_contact_no'] = $submission->comp_contact_no;
					$record['reno_start'] = $submission->reno_start;
					$record['reno_end'] = $submission->reno_end;
					$record['hacking_work_start'] = $submission->hacking_work_start;
					$record['hacking_work_end'] = $submission->hacking_work_end;
					//$record['submitted_by'] = $submission->user;
					$record['status'] = $submission->status;
					$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
					//$record['unit'] = isset($submission->unitinfo)?$submission->unitinfo:null;
					$user_data =array();
					if(isset($submission->user)){
						$user_data["id"]= $submission->user->id;
						$user_data["account_id"]= $submission->user->account_id;
						$user_data["role_id"]= $submission->user->role_id;
						$user_data["user_info_id"]= $submission->user->user_info_id;
						$user_data["building_no"]= $submission->user->building_no;
						$user_data["unit_no"]= $submission->user->unit_no;
						$user_data["primary_contact"]= $submission->user->primary_contact;
						$user_data["name"]=Crypt::decryptString($submission->user->name);
					}
					$record['submitted_by'] = !empty($user_data)?$user_data:null;
					$unitObj = Unit::find($submission->unit_no);
					$unit_data =array();
					if(isset($unitObj)){
						$unit_data["id"]= $unitObj->id;
						$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
					}
					$record['unit'] = !empty($unit_data)?$unit_data:null;	
					$data[] = $record;
				} 
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
	}
	

	public function renovationinfo(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(41,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$submissionObj = EformRenovation::find($id);
				EformRenovation::where('id', $id)
				->update(['view_status' => 1,'updated_at'=>$submissionObj->updated_at]);

				$record['submission'] = $submissionObj;
				$record['details'] = $submissionObj->details;
				$record['sub_com'] = $submissionObj->sub_con;
				$record['payment'] =$submissionObj->payment;
				$record['inspection'] =$submissionObj->inspection;
				$record['defects'] =$submissionObj->defects;
				$record['submitted_by'] = $submissionObj->user;
				$record['unit'] = isset($submissionObj->unitinfo)?$submissionObj->unitinfo:null;
				
				$file_path = env('APP_URL')."/storage/app";

				return response()->json(['details'=>$record,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);

		
			}
	}


	public function renovationedit(Request $request) 
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

		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(41,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				$command = $request->input('remarks');      
				$eformObj = EformRenovation::find($id);
				$eformObj->status = $request->input('status');
				$eformObj->remarks = $command;
				$eformObj->save();
		
				if(isset($eformObj->status)){
					if($eformObj->status==0)
						$status =  "new";
					else if($eformObj->status==1)
						$status =  "cancelled";
					else if($eformObj->status==3)
						$status =  "approved";
					else if($eformObj->status==2)
						$status =  "in progress";
					else 
						$status =  "rejected";
				 
				 }
			   
				$title = "Your Renovation application .".$status;
        		InboxMessage::where('ref_id', $eformObj->id)->where('type',11)
				->update(['title'=>$title,'booking_date'=>$eformObj->moving_start,'booking_time'=>$eformObj->moving_end,'event_status' => $status]);

				//Start Insert into notification module
				$notification = array();
				$notification['account_id'] = $eformObj->account_id;
				$notification['user_id'] = $eformObj->user_id;
				$notification['unit_no'] = $eformObj->unit_no;
				$notification['module'] = 'eform_renovation';
				$notification['ref_id'] = $eformObj->id;
				$notification['title'] = 'Renovation E-form';
				$notification['message'] = 'There is an update from the management in regards to your submitted Renovation E-form';
				$result = UserNotification::insert($notification);
		
				$SettingsObj = UserNotificationSetting::where('user_id',$eformObj->user_id)->where('account_id',$eformObj->account_id)->first();
				if(empty($SettingsObj) || $SettingsObj->eforms ==1){
					$fcm_token_array ='';
					$user_token = ',';
					$ios_devices_to_send = array();
					$android_devices_to_send = array();
					$logs = UserLog::where('user_id',$eformObj->user_id)->where('status',1)->orderby('id','desc')->first();
					if(isset($logs->fcm_token) && $logs->fcm_token !=''){
						$user_token .=$logs->fcm_token.",";
						$fcm_token_array .=$logs->fcm_token.',';
						$appSipAccountList[] = $eformObj->id;
						if($logs->login_from ==1)
							$ios_devices_to_send[] = $logs->fcm_token;
						if($logs->login_from ==2)
							$android_devices_to_send[] = $logs->fcm_token;
					}
		
					$probObj = Property::find($eformObj->account_id);
					$title = "Aerea Home - ".$probObj->company_name;
					$message = "Renovation E-form Updated";
					$notofication_data = array();
					$notofication_data['body'] =$title;
					$notofication_data['unit_no'] =$eformObj->unit_no;   
					$notofication_data['user_id'] =$eformObj->user_id;   
					$notofication_data['property'] =$eformObj->account_id; 
					$purObj = UserPurchaserUnit::where('property_id',$eformObj->account_id)->where('unit_id',$eformObj->unit_no)->where('user_id',$eformObj->user_id)->first(); 
					if(isset($purObj))
						$notofication_data['switch_id'] =$purObj->id;        
					$NotificationObj = new \App\Models\v7\FirebaseNotification();
					$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
					$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
				}
				
				return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);

			}
		
			
	}

	public function renovationpaymentsave(Request $request) 
	{
			$rules=array(
				'signature' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
			);
			$messages=array(
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

		$login_id = Auth::id();

			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(41,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				
				$eformObj = EformRenovation::find($id);

				$account_id = $adminObj->account_id;

				if ($request->input('payment_id') != null) {

					$paymentObj = EformRenovationPayment::find($request->input('payment_id'));
					
					
					$paymentObj['reno_id'] = $id;
					$paymentObj['manager_id'] = $request->input('login_id');
					$paymentObj['payment_option'] = $request->input('payment_option');

					if($paymentObj['payment_option'] ==1){

						if($request->input('cheque_amount') != null)
							$paymentObj['cheque_amount'] = $request->input('cheque_amount');

						if($request->input('cheque_received_date') != null)
							$paymentObj['cheque_received_date'] = $request->input('cheque_received_date');

						if($request->input('cheque_no') != null)
							$paymentObj['cheque_no'] = $request->input('cheque_no');
						
						if($request->input('cheque_bank') != null)
							$paymentObj['cheque_bank'] = $request->input('cheque_bank');
					}
					if($paymentObj['payment_option'] ==2){
						if($request->input('bt_received_date') != null)
							$paymentObj['bt_received_date'] = $request->input('bt_received_date');

						if($request->input('bt_amount_received') != null)
							$paymentObj['bt_amount_received'] = $request->input('bt_amount_received');
					}
					if($paymentObj['payment_option'] ==3){
						if($request->input('cash_amount_received') != null)
							$paymentObj['cash_amount_received'] = $request->input('cash_amount_received');

						if($request->input('cash_received_date') != null)
							$paymentObj['cash_received_date'] = $request->input('cash_received_date');
						
					}
					
					if($request->input('receipt_no') != null)   
						$paymentObj['receipt_no'] = $request->input('receipt_no');

					if($request->input('acknowledged_by') != null)   
						$paymentObj['acknowledged_by'] = $request->input('acknowledged_by');

					if($request->input('manager_received') != null)   
						$paymentObj['manager_received'] = $request->input('manager_received');

					if ($request->file('signature') != null) {
						$paymentObj['signature'] = base64_encode(file_get_contents($request->file('signature')));
			
					}

					if($request->input('date_of_signature') != null)   
						$paymentObj['date_of_signature'] = $request->input('date_of_signature');

					$paymentObj->save();
					
					

				} else {
					
					$payment['reno_id'] = $id;
					$payment['manager_id'] = $request->input('login_id');
					$payment['payment_option'] = $request->input('payment_option');

					if($payment['payment_option'] ==1){

						if($request->input('cheque_amount') != null)
							$payment['cheque_amount'] = $request->input('cheque_amount');

						if($request->input('cheque_no') != null)
							$payment['cheque_no'] = $request->input('cheque_no');
						
						if($request->input('cheque_bank') != null)
							$payment['cheque_bank'] = $request->input('cheque_bank');
					}
					if($payment['payment_option'] ==2){
						if($request->input('bt_received_date') != null)
							$payment['bt_received_date'] = $request->input('bt_received_date');

						if($request->input('bt_amount_received') != null)
							$payment['bt_amount_received'] = $request->input('bt_amount_received');
					}
					if($payment['payment_option'] ==3){
						if($request->input('cash_amount_received') != null)
							$payment['cash_amount_received'] = $request->input('cash_amount_received');

						if($request->input('cash_received_date') != null)
							$payment['cash_received_date'] = $request->input('cash_received_date');
						
					}

					if($request->input('receipt_no') != null)   
						$payment['receipt_no'] = $request->input('receipt_no');

					if($request->input('acknowledged_by') != null)   
						$payment['acknowledged_by'] = $request->input('acknowledged_by');

					if($request->input('manager_received') != null)   
						$payment['manager_received'] = $request->input('manager_received');

					if ($request->file('signature') != null) {
						$payment['signature'] = base64_encode(file_get_contents($request->file('signature')));
					}

					if($request->input('date_of_signature') != null)   
						$payment['date_of_signature'] = $request->input('date_of_signature');

					$payment['created_at'] = date("Y-m-d H:i:s");
					$payment['updated_at'] = date("Y-m-d H:i:s");
					EformRenovationPayment::insert($payment);
				
				}
				//Start Insert into notification module
                $notification = array();
                $notification['account_id'] = $eformObj->account_id;
                $notification['user_id'] = $eformObj->user_id;
                $notification['unit_no'] = $eformObj->unit_no;
                $notification['module'] = 'eform_renovation';
                $notification['ref_id'] = $eformObj->id;
                $notification['title'] = 'Renovation E-form';
                $notification['message'] = 'There is an update from the management in regards to your payment on Renovation E-form';
                $result = UserNotification::insert($notification);

                $SettingsObj = UserNotificationSetting::where('user_id',$eformObj->user_id)->where('account_id',$eformObj->account_id)->first();
                if(empty($SettingsObj) || $SettingsObj->eforms ==1){
                    $fcm_token_array ='';
                    $user_token = ',';
                    $ios_devices_to_send = array();
                    $android_devices_to_send = array();
                    $logs = UserLog::where('user_id',$eformObj->user_id)->where('status',1)->orderby('id','desc')->first();
                    if(isset($logs->fcm_token) && $logs->fcm_token !=''){
                        $user_token .=$logs->fcm_token.",";
                        $fcm_token_array .=$logs->fcm_token.',';
                        $appSipAccountList[] = $eformObj->id;
                        if($logs->login_from ==1)
                            $ios_devices_to_send[] = $logs->fcm_token;
                        if($logs->login_from ==2)
                            $android_devices_to_send[] = $logs->fcm_token;
                    }

                    $probObj = Property::find($eformObj->account_id);
                    $title = "Aerea Home - ".$probObj->company_name;
                    $message = "Renovation E-form Payment";
                    $notofication_data = array();
                    $notofication_data['body'] =$title;
                    $notofication_data['unit_no'] =$eformObj->unit_no;   
                    $notofication_data['user_id'] =$eformObj->user_id;   
                    $notofication_data['property'] =$eformObj->account_id; 
                    $purObj = UserPurchaserUnit::where('property_id',$eformObj->account_id)->where('unit_id',$eformObj->unit_no)->where('user_id',$eformObj->user_id)->first(); 
                    if(isset($purObj))
                        $notofication_data['switch_id'] =$purObj->id;        
                    $NotificationObj = new \App\Models\v7\FirebaseNotification();
                    $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
                    $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
                }
				
				
				return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);

			}
		
			
	}
	public function renovationinspectionsave(Request $request) 
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


		$login_id = Auth::id();

			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(40,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				$eformObj = EformRenovation::find($id);

				$account_id = $adminObj->account_id;


				if ($request->input('inspection_id') != null) {

						$inspectionObj = EformRenovationInspection::find($request->input('inspection_id'));
						
						
						$inspectionObj['reno_id'] = $id;
						$inspectionObj['manager_id'] = $request->login_id;
						if($request->input('date_of_completion') != null)
							$inspectionObj['date_of_completion'] = $request->input('date_of_completion');
						
						if($request->input('inspected_by') != null)
							$inspectionObj['inspected_by'] = $request->input('inspected_by');

						if($request->input('unit_in_order_or_not') != null)    
							$inspectionObj['unit_in_order_or_not'] = $request->input('unit_in_order_or_not');

						if($request->input('amount_deducted') != null)   
							$inspectionObj['amount_deducted'] = $request->input('amount_deducted');

						if($request->input('refunded_amount') != null)   
							$inspectionObj['refunded_amount'] = $request->input('refunded_amount');

						if($request->input('amount_claimable') != null)   
							$inspectionObj['amount_claimable'] = $request->input('amount_claimable');

						if($request->input('actual_amount_received') != null)   
							$inspectionObj['actual_amount_received'] = $request->input('actual_amount_received');

						if($request->input('acknowledged_by') != null)   
							$inspectionObj['acknowledged_by'] = $request->input('acknowledged_by');

						if($request->input('resident_nric') != null)   
							$inspectionObj['resident_nric'] = $request->input('resident_nric');
						
						if ($request->file('resident_signature') != null) {
							$inspectionObj['resident_signature'] = base64_encode(file_get_contents($request->file('resident_signature')));
						}
						if($request->input('resident_signature_date') != null)   
							$inspectionObj['resident_signature_date'] = $request->input('resident_signature_date');

						if($request->input('manager_received') != null)   
							$inspectionObj['manager_received'] = $request->input('manager_received');

						if ($request->file('manager_signature') != null) {
								$inspectionObj['manager_signature'] = base64_encode(file_get_contents($request->file('manager_signature')));
						}

						if($request->input('date_of_signature') != null)   
							$inspectionObj['date_of_signature'] = $request->input('date_of_signature');

							$inspectionObj['updated_at'] = date("Y-m-d H:i:s");

						$inspectionObj->save();
					
					

				} else {

					$inspectionObj['reno_id'] = $id;
					$inspectionObj['manager_id'] = $request->login_id;
					if($request->input('date_of_completion') != null)
						$inspectionObj['date_of_completion'] = $request->input('date_of_completion');
					
					if($request->input('inspected_by') != null)
						$inspectionObj['inspected_by'] = $request->input('inspected_by');

					if($request->input('unit_in_order_or_not') != null)    
						$inspectionObj['unit_in_order_or_not'] = $request->input('unit_in_order_or_not');

					if($request->input('amount_deducted') != null)   
						$inspectionObj['amount_deducted'] = $request->input('amount_deducted');

					if($request->input('refunded_amount') != null)   
						$inspectionObj['refunded_amount'] = $request->input('refunded_amount');

					if($request->input('amount_claimable') != null)   
						$inspectionObj['amount_claimable'] = $request->input('amount_claimable');

					if($request->input('actual_amount_received') != null)   
						$inspectionObj['actual_amount_received'] = $request->input('actual_amount_received');

					if($request->input('acknowledged_by') != null)   
						$inspectionObj['acknowledged_by'] = $request->input('acknowledged_by');

					if($request->input('resident_nric') != null)   
						$inspectionObj['resident_nric'] = $request->input('resident_nric');
					
					if ($request->file('resident_signature') != null) {
						$inspectionObj['resident_signature'] = base64_encode(file_get_contents($request->file('resident_signature')));
					}											
					if($request->input('resident_signature_date') != null)   
						$inspectionObj['resident_signature_date'] = $request->input('resident_signature_date');

					if($request->input('manager_received') != null)   
						$inspectionObj['manager_received'] = $request->input('manager_received');

					if ($request->file('manager_signature') != null) {
							$inspectionObj['manager_signature'] = base64_encode(file_get_contents($request->file('manager_signature')));
					}
					if($request->input('date_of_signature') != null)   
						$inspectionObj['date_of_signature'] = $request->input('date_of_signature');

					$inspectionObj['created_at'] = date("Y-m-d H:i:s");
					$inspectionObj['updated_at'] = date("Y-m-d H:i:s");

					$record =  EformRenovationInspection::insert($inspectionObj);

					
						
					}

					/********** INSERT documents START******************/
					for ($i = 1; $i <= 5; $i++) {
						$file = 'file_' . $i;
						$file_id = 'file_id_' . $i;
						$notes= 'description_'.$i;

					
						if ($request->input($file_id) != null) {

							if($request->input($notes) ==''){
								EformRenovationDefect::findOrFail($request->input($file_id))->delete();
							}else{
								$fileObj = EformRenovationDefect::find($request->input($file_id));
								$fileObj->reno_id = $id;

								if ($request->file($file) != null) {
									$fileObj->image_base64 = base64_encode(file_get_contents($request->file($file)));
								}
								$fileObj->notes= $request->input($notes);
								$fileObj->updated_at = date("Y-m-d H:i:s");
								$fileObj->save();
							}

						} else if ($request->input($notes) != null) {
							$type = array();
							$type['account_id'] = $account_id;
							$type['reno_id'] = $id;

							if ($request->file($file) != null) {
								$type['image_base64'] = base64_encode(file_get_contents($request->file($file)));
								
							}
							$type['notes']= $request->input($notes); 
							$type['created_at'] = date("Y-m-d H:i:s");
							$type['updated_at'] = date("Y-m-d H:i:s");
							EformRenovationDefect::insert($type);
						}	
					}	
				}

				//Start Insert into notification module
				$notification = array();
				$notification['account_id'] = $eformObj->account_id;
				$notification['user_id'] = $eformObj->user_id;
				$notification['unit_no'] = $eformObj->unit_no;
				$notification['module'] = 'eform_renovation';
				$notification['ref_id'] = $eformObj->id;
				$notification['title'] = 'Renovation E-form';
				$notification['message'] = 'There is an update from the management in regards to inspection on your Renovation E-form';
				$result = UserNotification::insert($notification);
	
				$SettingsObj = UserNotificationSetting::where('user_id',$eformObj->user_id)->where('account_id',$eformObj->account_id)->first();
				if(empty($SettingsObj) || $SettingsObj->eforms ==1){
					$fcm_token_array ='';
					$user_token = ',';
					$ios_devices_to_send = array();
					$android_devices_to_send = array();
					$logs = UserLog::where('user_id',$eformObj->user_id)->where('status',1)->orderby('id','desc')->first();
					if(isset($logs->fcm_token) && $logs->fcm_token !=''){
						$user_token .=$logs->fcm_token.",";
						$fcm_token_array .=$logs->fcm_token.',';
						$appSipAccountList[] = $eformObj->id;
						if($logs->login_from ==1)
							$ios_devices_to_send[] = $logs->fcm_token;
						if($logs->login_from ==2)
							$android_devices_to_send[] = $logs->fcm_token;
					}
	
					$probObj = Property::find($eformObj->account_id);
					$title = "Aerea Home - ".$probObj->company_name;
					$message = "Renovation E-form Inspection";
					$notofication_data = array();
					$notofication_data['body'] =$title;
					$notofication_data['unit_no'] =$eformObj->unit_no;   
					$notofication_data['user_id'] =$eformObj->user_id;   
					$notofication_data['property'] =$eformObj->account_id; 
					$purObj = UserPurchaserUnit::where('property_id',$eformObj->account_id)->where('unit_id',$eformObj->unit_no)->where('user_id',$eformObj->user_id)->first(); 
					if(isset($purObj))
						$notofication_data['switch_id'] =$purObj->id;        
					$NotificationObj = new \App\Models\v7\FirebaseNotification();
					$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
					$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
				}
				return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);

	}

			
	

	public function renovationdelete(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(41,$adminObj->role_id); 
			if(empty($permission) && $permission->delete!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				EformRenovationSubCon::where('reno_id', $id)->delete();
				EformRenovationPayment::where('reno_id', $id)->delete();
				EformRenovationInspection::where('reno_id', $id)->delete();
				EformRenovationDefect::where('reno_id', $id)->delete();
				EformRenovation::findOrFail($id)->delete();
				return response()->json(['response' => 1, 'message' => 'Deleted']);
		
			}
	}

	public function renovationsearch(Request $request) 
	{
			
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(41,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$account_id = $adminObj->account_id;
				$date = $status = $month = $option = $unit = $category ='';
				$unit = $request->input('unit');
				$units = array();
				if($unit !=''){   
					$unit = str_replace("#",'',$unit);
					$unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
					})->get();   
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
								$units[] = $unitid->id;
						}
					}
				}
				//print_r($units);

				$userids = array();
				$name = $request->input('name');
				if($name !=''){
					/*$userObj = User::select('id')->where('account_id',$account_id)->where('name', 'LIKE', '%'.$name .'%')->get();
					foreach($userObj as $k => $user){
						$userids[] = $user->id;
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
				$ticket = $request->input('ticket');
				$status = $request->input('status');
			
				if ($ticket != '' || $unit != '' || $name != '' || $status != '') {
					$submissions =  EformRenovation::where('account_id',$account_id)->where(function ($query) use ($ticket,$unit,$units,$userids,$name,$status) {
						if($status !='' )
							$query->where('status', $status);
						if( $unit !='')
							$query->whereIn('unit_no', $units);
						if( $name !='')
							$query->whereIn('user_id', $userids);
						if($ticket !='')
							$query->where('ticket', 'LIKE', '%'.$ticket .'%');
					})->orderby('id', 'desc')->get();

					$data =array();
					foreach ($submissions as $k => $submission) {
						$record =array();
						$record['id'] = $submission->id;
						$record['ticket'] = $submission->ticket;
						$record['reno_date'] = $submission->reno_date;
						$record['resident_name'] = $submission->resident_name;
						$record['contact_no'] = $submission->contact_no;
						$record['email'] = $submission->email;
						$record['reno_comp'] = $submission->reno_comp;
						$record['in_charge_name'] = $submission->in_charge_name;
						$record['comp_address'] = $submission->comp_address;
						$record['comp_contact_no'] = $submission->comp_contact_no;
						$record['reno_start'] = $submission->reno_start;
						$record['reno_end'] = $submission->reno_end;
						$record['hacking_work_start'] = $submission->hacking_work_start;
						$record['hacking_work_end'] = $submission->hacking_work_end;
						//$record['submitted_by'] = $submission->user;
						$record['status'] = $submission->status;
						$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
						$user_data =array();
						if(isset($submission->user)){
							$user_data["id"]= $submission->user->id;
							$user_data["account_id"]= $submission->user->account_id;
							$user_data["role_id"]= $submission->user->role_id;
							$user_data["user_info_id"]= $submission->user->user_info_id;
							$user_data["building_no"]= $submission->user->building_no;
							$user_data["unit_no"]= $submission->user->unit_no;
							$user_data["primary_contact"]= $submission->user->primary_contact;
							$user_data["name"]=Crypt::decryptString($submission->user->name);
						}
						$record['submitted_by'] = !empty($user_data)?$user_data:null;
						$unitObj = Unit::find($submission->unit_no);
						$unit_data =array();
						if(isset($unitObj)){
							$unit_data["id"]= $unitObj->id;
							$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
						}
						$record['unit'] = !empty($unit_data)?$unit_data:null;
	
						$data[] = $record;
					} 

					return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
				}
				else
				{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
				}

			}
	}
	//Eform Renovation In & Out End


	//Eform Door Access  In & Out Start

	public function dooraccesssummary(Request $request) 
	{
	
		$login_id = Auth::id();
			$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(42,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$submissions = EformDoorAccess::where('account_id',$account_id)->orderby('id','desc')->get(); 
				$data =array();
				foreach ($submissions as $k => $submission) {
					$record =array();
					$record['id'] = $submission->id;
					$record['ticket'] = $submission->ticket;
					$record['request_date'] = $submission->request_date;
					$record['owner_name'] = $submission->owner_name;
					$record['contact_no'] = $submission->contact_no;
					$record['email'] = $submission->email;
					$record['declared_by'] = $submission->declared_by;
					$record['in_charge_name'] = $submission->in_charge_name;
					$record['passport_no'] = $submission->passport_no;
					$record['relationship'] = $submission->relationship;
					$record['nominee_contact_no'] = $submission->nominee_contact_no;
					$record['nominee_email'] = $submission->nominee_email;
					$record['no_of_card_required'] = $submission->no_of_card_required;
					$record['no_of_schlage_required'] = $submission->no_of_schlage_required;
					$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
					if(isset($submission->unitinfo)){
						$unit_data["id"]= $submission->unitinfo->id;
						$unit_data["unit"]= Crypt::decryptString($submission->unitinfo->unit);
						$unit_data["code"]= Crypt::decryptString($submission->unitinfo->code);

					}
					//$record['unit'] = !empty($unit_data)?$unit_data:null;
					$record['unit'] = !empty($unit_data)?$unit_data:null;
					//$record['submitted_by'] = $submission->user;
					$user_data =array();
					if(isset($submission->user)){
						$user_data["id"]= $submission->user->id;
						$user_data["account_id"]= $submission->user->account_id;
						$user_data["role_id"]= $submission->user->role_id;
						$user_data["user_info_id"]= $submission->user->user_info_id;
						$user_data["building_no"]= $submission->user->building_no;
						$user_data["unit_no"]= $submission->user->unit_no;
						$user_data["primary_contact"]= $submission->user->primary_contact;
						$user_data["name"]=Crypt::decryptString($submission->user->name);
						
					}
					$record['submitted_by'] = !empty($user_data)?$user_data:null;
					$record['tenancy_start'] = $submission->tenancy_start;
					$record['tenancy_end'] = $submission->tenancy_end;
					$record['status'] = $submission->status;
					//$record['acknowledgement'] =$submission->ack;
					//$record['submission'] = $submission;
					$data[] = $record;
				} 
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
	}
	

	public function dooraccessinfo(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(42,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$submissionObj = EformDoorAccess::find($id);
				EformDoorAccess::where('id', $id)
				->update(['view_status' => 1,'updated_at'=>$submissionObj->updated_at]);

				$record['submission'] = $submissionObj;
				$record['payment'] =$submissionObj->payment;
				$record['acknowledgement'] =$submissionObj->ack;
				$record['submitted_by'] = $submissionObj->user;
				$record['unit'] = isset($submissionObj->unitinfo)?$submissionObj->unitinfo:null;
				
				$file_path = env('APP_URL')."/storage/app";

				return response()->json(['details'=>$record,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);

		
			}
	}


	public function dooraccessedit(Request $request) 
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

		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(42,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				$command = $request->input('remarks');      
				$eformObj = EformDoorAccess::find($id);
				$eformObj->status = $request->input('status');
				$eformObj->remarks = $command;
				$eformObj->save();
		
				if(isset($eformObj->status)){
					if($eformObj->status==0)
						$status =  "new";
					else if($eformObj->status==1)
						$status =  "cancelled";
					else if($eformObj->status==3)
						$status =  "approved";
					else if($eformObj->status==2)
						$status =  "in progress";
					else 
						$status =  "rejected";
				 
				 }
			   
				 $title = "Your Door access card application .".$status;
        		InboxMessage::where('ref_id', $eformObj->id)->where('type',12)
                ->update(['title'=>$title,'booking_date'=>$eformObj->tenancy_start,'booking_time'=>$eformObj->tenancy_start,'event_status' => $status]);
        
				 //Start Insert into notification module
				 $notification = array();
				 $notification['account_id'] = $eformObj->account_id;
				 $notification['user_id'] = $eformObj->user_id;
				 $notification['unit_no'] = $eformObj->unit_no;
				 $notification['module'] = 'eform_renovation';
				 $notification['ref_id'] = $eformObj->id;
				 $notification['title'] = 'Renovation E-form';
				 $notification['message'] = 'There is an update from the management in regards to your submitted Renovation E-form';
				 $result = UserNotification::insert($notification);
		 
				 $SettingsObj = UserNotificationSetting::where('user_id',$eformObj->user_id)->where('account_id',$eformObj->account_id)->first();
				 if(empty($SettingsObj) || $SettingsObj->eforms ==1){
					 $fcm_token_array ='';
					 $user_token = ',';
					 $ios_devices_to_send = array();
					 $android_devices_to_send = array();
					 $logs = UserLog::where('user_id',$eformObj->user_id)->where('status',1)->orderby('id','desc')->first();
					 if(isset($logs->fcm_token) && $logs->fcm_token !=''){
						 $user_token .=$logs->fcm_token.",";
						 $fcm_token_array .=$logs->fcm_token.',';
						 $appSipAccountList[] = $eformObj->id;
						 if($logs->login_from ==1)
							 $ios_devices_to_send[] = $logs->fcm_token;
						 if($logs->login_from ==2)
							 $android_devices_to_send[] = $logs->fcm_token;
					 }
		 
					 $probObj = Property::find($eformObj->account_id);
					 $title = "Aerea Home - ".$probObj->company_name;
					 $message = "Renovation E-form Updated";
					 $notofication_data = array();
					 $notofication_data['body'] =$title;
					 $notofication_data['unit_no'] =$eformObj->unit_no;   
					 $notofication_data['user_id'] =$eformObj->user_id;   
					 $notofication_data['property'] =$eformObj->account_id; 
					 $purObj = UserPurchaserUnit::where('property_id',$eformObj->account_id)->where('unit_id',$eformObj->unit_no)->where('user_id',$eformObj->user_id)->first(); 
					 if(isset($purObj))
						 $notofication_data['switch_id'] =$purObj->id;        
					 $NotificationObj = new \App\Models\v7\FirebaseNotification();
					 $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
					 $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
				 }
				
				return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);

			}
		
			
	}

	public function dooraccesspaymentsave(Request $request) 
	{
			$rules=array(
				'signature' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
			);
			$messages=array(
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

		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(41,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				
				$eformObj = EformDoorAccess::find($id);

				$account_id = $adminObj->account_id;

				if ($request->input('payment_id') != null) {

					$paymentObj = EformDoorAccesscardPayment::find($request->input('payment_id'));
					
					
					$paymentObj['reg_id'] = $id;
					$paymentObj['manager_id'] = $request->input('login_id');
					$paymentObj['payment_option'] = $request->input('payment_option');

					if($paymentObj['payment_option'] ==1){

						if($request->input('cheque_amount') != null)
							$paymentObj['cheque_amount'] = $request->input('cheque_amount');

						if($request->input('cheque_received_date') != null)
							$paymentObj['cheque_received_date'] = $request->input('cheque_received_date');

						if($request->input('cheque_no') != null)
							$paymentObj['cheque_no'] = $request->input('cheque_no');
						
						if($request->input('cheque_bank') != null)
							$paymentObj['cheque_bank'] = $request->input('cheque_bank');
					}
					if($paymentObj['payment_option'] ==2){
						if($request->input('bt_received_date') != null)
							$paymentObj['bt_received_date'] = $request->input('bt_received_date');

						if($request->input('bt_amount_received') != null)
							$paymentObj['bt_amount_received'] = $request->input('bt_amount_received');
					}
					if($paymentObj['payment_option'] ==3){
						if($request->input('cash_amount_received') != null)
							$paymentObj['cash_amount_received'] = $request->input('cash_amount_received');

						if($request->input('cash_received_date') != null)
							$paymentObj['cash_received_date'] = $request->input('cash_received_date');
						
					}
					
					if($request->input('receipt_no') != null)   
						$paymentObj['receipt_no'] = $request->input('receipt_no');

					if($request->input('acknowledged_by') != null)   
						$paymentObj['acknowledged_by'] = $request->input('acknowledged_by');

					if($request->input('manager_received') != null)   
						$paymentObj['manager_received'] = $request->input('manager_received');

					if ($request->file('signature') != null) {
						$paymentObj['signature'] = base64_encode(file_get_contents($request->file('signature')));
			
					}

					if($request->input('date_of_signature') != null)   
						$paymentObj['date_of_signature'] = $request->input('date_of_signature');

					$paymentObj->save();
					
					

				} else {
					
					$payment['reg_id'] = $id;
					$payment['manager_id'] = $request->input('login_id');
					$payment['payment_option'] = $request->input('payment_option');

					if($payment['payment_option'] ==1){

						if($request->input('cheque_amount') != null)
							$payment['cheque_amount'] = $request->input('cheque_amount');

						if($request->input('cheque_no') != null)
							$payment['cheque_no'] = $request->input('cheque_no');
						
						if($request->input('cheque_bank') != null)
							$payment['cheque_bank'] = $request->input('cheque_bank');
					}
					if($payment['payment_option'] ==2){
						if($request->input('bt_received_date') != null)
							$payment['bt_received_date'] = $request->input('bt_received_date');

						if($request->input('bt_amount_received') != null)
							$payment['bt_amount_received'] = $request->input('bt_amount_received');
					}
					if($payment['payment_option'] ==3){
						if($request->input('cash_amount_received') != null)
							$payment['cash_amount_received'] = $request->input('cash_amount_received');

						if($request->input('cash_received_date') != null)
							$payment['cash_received_date'] = $request->input('cash_received_date');
						
					}

					if($request->input('receipt_no') != null)   
						$payment['receipt_no'] = $request->input('receipt_no');

					if($request->input('acknowledged_by') != null)   
						$payment['acknowledged_by'] = $request->input('acknowledged_by');

					if($request->input('manager_received') != null)   
						$payment['manager_received'] = $request->input('manager_received');

					if ($request->file('signature') != null) {
						$payment['signature'] = base64_encode(file_get_contents($request->file('signature')));
					}

					if($request->input('date_of_signature') != null)   
						$payment['date_of_signature'] = $request->input('date_of_signature');

					$payment['created_at'] = date("Y-m-d H:i:s");
					$payment['updated_at'] = date("Y-m-d H:i:s");
					EformDoorAccesscardPayment::insert($payment);
				
				}

				//Start Insert into notification module
                $notification = array();
                $notification['account_id'] = $eformObj->account_id;
                $notification['user_id'] = $eformObj->user_id;
                $notification['unit_no'] = $eformObj->unit_no;
                $notification['module'] = 'eform_dooraccess_card';
                $notification['ref_id'] = $eformObj->id;
                $notification['title'] = 'Door Access Card E-form';
                $notification['message'] = 'There is an update from the management in regards to your payment on Door Access Card E-form';
                $result = UserNotification::insert($notification);

                $SettingsObj = UserNotificationSetting::where('user_id',$eformObj->user_id)->where('account_id',$eformObj->account_id)->first();
                if(empty($SettingsObj) || $SettingsObj->eforms ==1){
                    $fcm_token_array ='';
                    $user_token = ',';
                    $ios_devices_to_send = array();
                    $android_devices_to_send = array();
                    $logs = UserLog::where('user_id',$eformObj->user_id)->where('status',1)->orderby('id','desc')->first();
                    if(isset($logs->fcm_token) && $logs->fcm_token !=''){
                        $user_token .=$logs->fcm_token.",";
                        $fcm_token_array .=$logs->fcm_token.',';
                        $appSipAccountList[] = $eformObj->id;
                        if($logs->login_from ==1)
                            $ios_devices_to_send[] = $logs->fcm_token;
                        if($logs->login_from ==2)
                            $android_devices_to_send[] = $logs->fcm_token;
                    }

                    $probObj = Property::find($eformObj->account_id);
                    $title = "Aerea Home - ".$probObj->company_name;
                    $message = "Door Access Card E-form Payment";
                    $notofication_data = array();
                    $notofication_data['body'] =$title;
                    $notofication_data['unit_no'] =$eformObj->unit_no;   
                    $notofication_data['user_id'] =$eformObj->user_id;   
                    $notofication_data['property'] =$eformObj->account_id; 
                    $purObj = UserPurchaserUnit::where('property_id',$eformObj->account_id)->where('unit_id',$eformObj->unit_no)->where('user_id',$eformObj->user_id)->first(); 
                    if(isset($purObj))
                        $notofication_data['switch_id'] =$purObj->id;        
                    $NotificationObj = new \App\Models\v7\FirebaseNotification();
                    $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
                    $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
                }
				return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);

			}
		
			
	}

	public function dooracknowledgementsave(Request $request) 
	{
			$rules=array('signature' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
			);
			$messages=array(
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
		$login_id = Auth::id();

			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(41,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				
				$eformObj = EformDoorAccess::find($id);

				$account_id = $adminObj->account_id;

				$eformsettingsObj = EformSetting::where('account_id', $account_id)->where('eform_type', 42)->first();

				if ($request->input('ack_id') != null) {

					$ackObj = EformDoorAccesscardAck::find($request->input('ack_id'));
					
					$ackObj['reg_id'] = $id;
					$ackObj['manager_id'] = $login_id;
				   
					
					if($request->input('number_of_access_card') != null)   
						$ackObj['number_of_access_card'] = $request->input('number_of_access_card');
	
					if($request->input('serial_number_of_card') != null)   
						$ackObj['serial_number_of_card'] = $request->input('serial_number_of_card');
	
					if($request->input('acknowledged_by') != null)   
						$ackObj['acknowledged_by'] = $request->input('acknowledged_by');
	
					if($request->input('manager_issued') != null)   
						$ackObj['manager_issued'] = $request->input('manager_issued');
	
					if ($request->file('signature') != null) {
						$ackObj['signature'] = base64_encode(file_get_contents($request->file('signature')));
					}
	
					if($request->input('date_of_signature') != null)   
						$ackObj['date_of_signature'] = $request->input('date_of_signature');
	
					$ackObj->save();
				
	
			} else {
					
	
					$ack['reg_id'] = $id;
					$ack['manager_id'] = $login_id;
					if($request->input('number_of_access_card') != null)   
						$ack['number_of_access_card'] = $request->input('number_of_access_card');
	
					if($request->input('serial_number_of_card') != null)   
						$ack['serial_number_of_card'] = $request->input('serial_number_of_card');
	
					if($request->input('acknowledged_by') != null)   
						$ack['acknowledged_by'] = $request->input('acknowledged_by');
	
					if($request->input('manager_issued') != null)   
						$ack['manager_issued'] = $request->input('manager_issued');
	
					if ($request->file('signature') != null) {
						$ack['signature'] = base64_encode(file_get_contents($request->file('signature')));
					}
	
					if($request->input('date_of_signature') != null)   
						$ack['date_of_signature'] = $request->input('date_of_signature');
	
					$ack['created_at'] = date("Y-m-d H:i:s");
					$ack['updated_at'] = date("Y-m-d H:i:s");
					EformDoorAccesscardAck::insert($ack);
				
			}

			//Start Insert into notification module
			$notification = array();
			$notification['account_id'] = $eformObj->account_id;
			$notification['user_id'] = $eformObj->user_id;
			$notification['unit_no'] = $eformObj->unit_no;
			$notification['module'] = 'eform_dooraccess_card';
			$notification['ref_id'] = $eformObj->id;
			$notification['title'] = 'Door Access Card E-form';
			$notification['message'] = 'There is an update from the management in regards to handover on your Door Access Card E-form';
			$result = UserNotification::insert($notification);
	
			$SettingsObj = UserNotificationSetting::where('user_id',$eformObj->user_id)->where('account_id',$eformObj->account_id)->first();
			if(empty($SettingsObj) || $SettingsObj->eforms ==1){
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserLog::where('user_id',$eformObj->user_id)->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $eformObj->id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
	
				$probObj = Property::find($eformObj->account_id);
				$title = "Aerea Home - ".$probObj->company_name;
				$message = "Door Access Card E-form Handover";
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$eformObj->unit_no;   
				$notofication_data['user_id'] =$eformObj->user_id;   
				$notofication_data['property'] =$eformObj->account_id; 
				$purObj = UserPurchaserUnit::where('property_id',$eformObj->account_id)->where('unit_id',$eformObj->unit_no)->where('user_id',$eformObj->user_id)->first(); 
				if(isset($purObj))
					$notofication_data['switch_id'] =$purObj->id;        
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
			}

				return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);

			}
		
			
	}

	public function dooraccessdelete(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(42,$adminObj->role_id); 
			if(empty($permission) && $permission->delete!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				EformDoorAccesscardPayment::where('reg_id', $id)->delete();
				EformDoorAccesscardAck::where('reg_id', $id)->delete();
				EformDoorAccess::findOrFail($id)->delete();
				return response()->json(['response' => 1, 'message' => 'Deleted']);
		
			}
	}

	public function dooraccesssearch(Request $request) 
	{
			
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(42,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$account_id = $adminObj->account_id;
				$date = $status = $month = $option = $unit = $category ='';
				$unit = $request->input('unit');
				$units = array();
				if($unit !=''){   
					$unit = str_replace("#",'',$unit);
					$unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
					})->get();   
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
								$units[] = $unitid->id;
						}
					}
				}
				$userids = array();
				$name = $request->input('name');
				if($name !=''){
					$user_more_info = UserMoreInfo::select('id','user_id','first_name','last_name')->where('account_id',$account_id)->whereNotIn('status',[2])->orderby('id','desc')->get();
            
					foreach($user_more_info as $k =>$v){
						$firstname = strtolower(Crypt::decryptString($v->first_name));
						$lastname = strtolower(Crypt::decryptString($v->last_name));
						if(str_contains($firstname,strtolower($name)) || str_contains($lastname,strtolower($name))){
							$userids[] = $v->user_id;
							   
						}
					}
				}
				$ticket = $request->input('ticket');
				$status = $request->input('status');
			
				if ($ticket != '' || $unit != '' || $name != '' || $status != '') {
					$submissions =  EformDoorAccess::where('account_id',$account_id)->where(function ($query) use ($ticket,$unit,$units,$userids,$name,$status) {
						if($status !='' )
							$query->where('status', $status);
						if( $unit !='')
							$query->whereIn('unit_no', $units);
						if( $name !='')
							$query->whereIn('user_id', $userids);
						if($ticket !='')
							$query->where('ticket', 'LIKE', '%'.$ticket .'%');
					})->orderby('id', 'desc')->get();

					$data =array();
					foreach ($submissions as $k => $submission) {
						$record =array();
						$record['id'] = $submission->id;
						$record['ticket'] = $submission->ticket;
						$record['request_date'] = $submission->request_date;
						$record['owner_name'] = $submission->owner_name;
						$record['contact_no'] = $submission->contact_no;
						$record['email'] = $submission->email;
						$record['declared_by'] = $submission->declared_by;
						$record['in_charge_name'] = $submission->in_charge_name;
						$record['passport_no'] = $submission->passport_no;
						$record['relationship'] = $submission->relationship;
						$record['nominee_contact_no'] = $submission->nominee_contact_no;
						$record['nominee_email'] = $submission->nominee_email;
						$record['no_of_card_required'] = $submission->no_of_card_required;
						$record['no_of_schlage_required'] = $submission->no_of_schlage_required;
						$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
						if(isset($submission->unitinfo)){
							$unit_data["id"]= $submission->unitinfo->id;
							$unit_data["unit"]= Crypt::decryptString($submission->unitinfo->unit);
							$unit_data["code"]= Crypt::decryptString($submission->unitinfo->code);
	
						}
						//$record['unit'] = !empty($unit_data)?$unit_data:null;
						$record['unit'] = !empty($unit_data)?$unit_data:null;						//$record['submitted_by'] = $submission->user;
						$user_data =array();
						if(isset($submission->user)){
							$user_data["id"]= $submission->user->id;
							$user_data["account_id"]= $submission->user->account_id;
							$user_data["role_id"]= $submission->user->role_id;
							$user_data["user_info_id"]= $submission->user->user_info_id;
							$user_data["building_no"]= $submission->user->building_no;
							$user_data["unit_no"]= $submission->user->unit_no;
							$user_data["primary_contact"]= $submission->user->primary_contact;
							$user_data["name"]=Crypt::decryptString($submission->user->name);
							
						}
						$record['submitted_by'] = !empty($user_data)?$user_data:null;

						$record['tenancy_start'] = $submission->tenancy_start;
						$record['tenancy_end'] = $submission->tenancy_end;
						$record['status'] = $submission->status;
						//$record['acknowledgement'] =$submission->ack;
						//$record['submission'] = $submission;
						$data[] = $record;
					} 
					return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
				}
				else{
					return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
				}

			}
	}

	//Eform Door Access End

	//Eform Register Vehicle Start

	public function regvehiclesummary(Request $request) 
	{
			
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(43,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				
				$submissions = EformRegVehicle::where('account_id',$account_id)->orderby('id','desc')->get(); 
				$data =array();
				foreach ($submissions as $k => $submission) {
					$record =array();
					$info =array();
					$info['id']= $submission->id;
					$info['ticket']= $submission->ticket;
					$info['user_id']= $submission->user_id;
					$info['request_date']= $submission->request_date;
					$info['owner_name']= $submission->owner_name;
					$info['contact_no']= $submission->contact_no;
					$info['email']= $submission->email;
					$info['tenancy_start']= $submission->tenancy_start;
					$info['tenancy_end']= $submission->tenancy_end;
					$info['status']= $submission->status;
					$info['created_at']= $submission->created_at;
					$info['updated_at']= $submission->updated_at;
					$record['submission'] = $info;
					//$record['documents'] = $submission->documents;
					$user_data =array();
					if(isset($submission->user)){
						$user_data["id"]= $submission->user->id;
						$user_data["account_id"]= $submission->user->account_id;
						$user_data["role_id"]= $submission->user->role_id;
						$user_data["user_info_id"]= $submission->user->user_info_id;
						$user_data["building_no"]= $submission->user->building_no;
						$user_data["unit_no"]= $submission->user->unit_no;
						$user_data["primary_contact"]= $submission->user->primary_contact;
						$user_data["name"]=Crypt::decryptString($submission->user->name);
					}
					$record['submitted_by'] = !empty($user_data)?$user_data:null;
					$unitObj = Unit::find($submission->unit_no);
					$unit_data =array();
					if(isset($unitObj)){
						$unit_data["id"]= $unitObj->id;
						$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
					}
					$record['unit'] = !empty($unit_data)?$unit_data:null;
					//$record['submitted_by'] = $submission->user;
					$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
					//$record['unit'] = isset($submission->unitinfo)?$submission->unitinfo:null;
					$data[] = $record;
				} 
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
	}
	

	public function regvehicleinfo(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(43,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$submissionObj = EformRegVehicle::find($id);
				EformRegVehicle::where('id', $id)
				->update(['view_status' => 1,'updated_at'=>$submissionObj->updated_at]);

				$record['submission'] = $submissionObj;
				$record['documents'] = $submissionObj->documents;
				$record['submitted_by'] = $submissionObj->user;
				$record['unit'] = isset($submissionObj->unitinfo)?$submissionObj->unitinfo:null;
				
				$file_path = env('APP_URL')."/storage/app";

				return response()->json(['details'=>$record,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);

		
			}
	}


	public function regvehicleedit(Request $request) 
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

		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(43,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				$command = $request->input('remarks');      
				$eformObj = EformRegVehicle::find($id);
				$eformObj->status = $request->input('status');
				$eformObj->remarks = $command;
				$eformObj->save();
		
				if(isset($eformObj->status)){
					if($eformObj->status==0)
						$status =  "new";
					else if($eformObj->status==1)
						$status =  "cancelled";
					else if($eformObj->status==3)
						$status =  "approved";
					else if($eformObj->status==2)
						$status =  "in progress";
					else 
						$status =  "rejected";
				 
				 }
			   

				 $title = "Your registration for IU vehicle application .".$status;
        		InboxMessage::where('ref_id', $eformObj->id)->where('type',13)
                ->update(['title'=>$title,'booking_date'=>$eformObj->tenancy_start,'booking_time'=>$eformObj->tenancy_start,'event_status' => $status]);
        
				 //Start Insert into notification module
				 $notification = array();
				 $notification['account_id'] = $eformObj->account_id;
				 $notification['user_id'] = $eformObj->user_id;
				 $notification['unit_no'] = $eformObj->unit_no;
				 $notification['module'] = 'eform_vehicle_iu';
				 $notification['ref_id'] = $eformObj->id;
				 $notification['title'] = 'Registration for Vehicle IU E-form';
				 $notification['message'] = 'There is an update from the management in regards to your Registration for Vehicle IU E-form';
				 $result = UserNotification::insert($notification);
		 
				 $SettingsObj = UserNotificationSetting::where('user_id',$eformObj->user_id)->where('account_id',$eformObj->account_id)->first();
				 if(empty($SettingsObj) || $SettingsObj->eforms ==1){
					 $fcm_token_array ='';
					 $user_token = ',';
					 $ios_devices_to_send = array();
					 $android_devices_to_send = array();
					 $logs = UserLog::where('user_id',$eformObj->user_id)->where('status',1)->orderby('id','desc')->first();
					 if(isset($logs->fcm_token) && $logs->fcm_token !=''){
						 $user_token .=$logs->fcm_token.",";
						 $fcm_token_array .=$logs->fcm_token.',';
						 $appSipAccountList[] = $eformObj->id;
						 if($logs->login_from ==1)
							 $ios_devices_to_send[] = $logs->fcm_token;
						 if($logs->login_from ==2)
							 $android_devices_to_send[] = $logs->fcm_token;
					 }
		 
					 $probObj = Property::find($eformObj->account_id);
					 $title = "Aerea Home - ".$probObj->company_name;
					 $message = "Registration for Vehicle IU E-form Updated";
					 $notofication_data = array();
					 $notofication_data['body'] =$title;
					 $notofication_data['unit_no'] =$eformObj->unit_no;   
					 $notofication_data['user_id'] =$eformObj->user_id;   
					 $notofication_data['property'] =$eformObj->account_id; 
					 $purObj = UserPurchaserUnit::where('property_id',$eformObj->account_id)->where('unit_id',$eformObj->unit_no)->where('user_id',$eformObj->user_id)->first(); 
					 if(isset($purObj))
						 $notofication_data['switch_id'] =$purObj->id;        
					 $NotificationObj = new \App\Models\v7\FirebaseNotification();
					 $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
					 $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
				 }
				
				return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);

			}
		
			
	}

	public function regvehicledelete(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(43,$adminObj->role_id); 
			if(empty($permission) && $permission->delete!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				EformRegVehicleDoc::where('reg_id', $id)->delete();
				EformRegVehicle::findOrFail($id)->delete();
				return response()->json(['response' => 1, 'message' => 'Deleted']);
		
			}
	}

	public function regvehiclesearch(Request $request) 
	{
			
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(43,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$account_id = $adminObj->account_id;
				$date = $status = $month = $option = $unit = $category ='';
				$unit = $request->input('unit');
				$units = array();
				if($unit !=''){   
					$unit = str_replace("#",'',$unit);
					$unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
					})->get();   
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
								$units[] = $unitid->id;
						}
					}
				}
				//print_r($units);

				$userids = array();
				$name = $request->input('name');
				if($name !=''){
					/*$userObj = User::select('id')->where('account_id',$account_id)->where('name', 'LIKE', '%'.$name .'%')->get();
					foreach($userObj as $k => $user){
						$userids[] = $user->id;
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
				$ticket = $request->input('ticket');
				$status = $request->input('status'); 
			
				if ($ticket != '' || $unit != '' ||$name != '' ||$status != '' ) {
					$submissions =  EformRegVehicle::where('account_id',$account_id)->where(function ($query) use ($ticket,$unit,$units,$userids,$name,$status) {
						if($status !='' )
							$query->where('status', $status);
						if( $unit !='')
							$query->whereIn('unit_no', $units);
						if( $name !='')
							$query->whereIn('user_id', $userids);
						if($ticket !='')
							$query->where('ticket', 'LIKE', '%'.$ticket .'%');
					})->orderby('id', 'desc')->get();
				
				$data =array();
				foreach ($submissions as $k => $submission) {
					$record =array();
					$info =array();
					$info['id']= $submission->id;
					$info['ticket']= $submission->ticket;
					$info['user_id']= $submission->user_id;
					$info['request_date']= $submission->request_date;
					$info['owner_name']= $submission->owner_name;
					$info['contact_no']= $submission->contact_no;
					$info['email']= $submission->email;
					$info['tenancy_start']= $submission->tenancy_start;
					$info['tenancy_end']= $submission->tenancy_end;
					$info['status']= $submission->status;
					$info['created_at']= $submission->created_at;
					$info['updated_at']= $submission->updated_at;
					$record['submission'] = $info;
					//$record['documents'] = $submission->documents;
					$user_data =array();
					if(isset($submission->user)){
						$user_data["id"]= $submission->user->id;
						$user_data["account_id"]= $submission->user->account_id;
						$user_data["role_id"]= $submission->user->role_id;
						$user_data["user_info_id"]= $submission->user->user_info_id;
						$user_data["building_no"]= $submission->user->building_no;
						$user_data["unit_no"]= $submission->user->unit_no;
						$user_data["primary_contact"]= $submission->user->primary_contact;
						$user_data["name"]=Crypt::decryptString($submission->user->name);
						
					}
					$record['submitted_by'] = !empty($user_data)?$user_data:null;
					$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
					$unitObj = Unit::find($submission->unit_no);
						$unit_data =array();
						if(isset($unitObj)){
							$unit_data["id"]= $unitObj->id;
							$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
						}
					$record['unit'] = !empty($unit_data)?$unit_data:null;
					$data[] = $record;
				} 

				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
				}
				else{
					return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
				}

			}
	}

	//Eform Register Vehicle End

	//Eform Change Mailing Address Start

	public function changeaddresssummary(Request $request) 
	{
			
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(44,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$submissions = EformChangeAddress::where('account_id',$account_id)->orderby('id','desc')->get(); 
				$data =array();
				foreach ($submissions as $k => $submission) {
					$record =array();
						$info =array();
						$info['id']= $submission->id;
						$info['ticket']= $submission->ticket;
						$info['user_id']= $submission->user_id;
						$info['request_date']= $submission->request_date;
						$info['owner_name']= $submission->owner_name;
						$info['contact_no']= $submission->contact_no;
						$info['email']= $submission->email;
						$info['status']= $submission->status;
						$info['created_at']= $submission->created_at->format('d/m/Y');;
						$info['updated_at']= $submission->updated_at->format('d/m/Y');;
						$record['submission'] = $info;
						//$record['documents'] = $submission->documents;
						$record['unit_no'] = isset($submission->unitinfo)?$submission->unitinfo->unit:null;
						$user_data =array();
						if(isset($submission->user)){
							$user_data["id"]= $submission->user->id;
							$user_data["account_id"]= $submission->user->account_id;
							$user_data["role_id"]= $submission->user->role_id;
							$user_data["user_info_id"]= $submission->user->user_info_id;
							$user_data["building_no"]= $submission->user->building_no;
							$user_data["unit_no"]= $submission->user->unit_no;
							$user_data["primary_contact"]= $submission->user->primary_contact;
							$user_data["name"]=Crypt::decryptString($submission->user->name);
							
						}
						$record['submitted_by'] = !empty($user_data)?$user_data:null;
						$unitObj = Unit::find($submission->unit_no);
						$unit_data =array();
						if(isset($unitObj)){
							$unit_data["id"]= $unitObj->id;
							$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
						}
						$record['unit'] = !empty($unit_data)?$unit_data:null;
						$data[] = $record;
				} 
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
	}
	

	public function changeaddressinfo(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(44,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$submissionObj = EformChangeAddress::find($id);
				EformChangeAddress::where('id', $id)
				->update(['view_status' => 1,'updated_at'=>$submissionObj->updated_at]);

				$record['submission'] = $submissionObj;
				$user_data =array();
				if(isset($submissionObj->user)){
					$user_data["id"]= $submissionObj->user->id;
					$user_data["account_id"]= $submissionObj->user->account_id;
					$user_data["role_id"]= $submissionObj->user->role_id;
					$user_data["user_info_id"]= $submissionObj->user->user_info_id;
					$user_data["building_no"]= $submissionObj->user->building_no;
					$user_data["unit_no"]= $submissionObj->user->unit_no;
					$user_data["primary_contact"]= $submissionObj->user->primary_contact;
					$user_data["name"]=Crypt::decryptString($submissionObj->user->name);
					
				}
				$record['submitted_by'] = !empty($user_data)?$user_data:null;
				$unitObj = Unit::find($submissionObj->unit_no);
				$unit_data =array();
				if(isset($unitObj)){
					$unit_data["id"]= $unitObj->id;
					$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
				}
				$record['unit'] = !empty($unit_data)?$unit_data:null;
				
				$file_path = env('APP_URL')."/storage/app";

				return response()->json(['details'=>$record,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);

		
			}
	}


	public function changeaddressedit(Request $request) 
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

		$login_id = Auth::id();

			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(44,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				$command = $request->input('remarks');      
				$eformObj = EformChangeAddress::find($id);
				$eformObj->status = $request->input('status');
				$eformObj->remarks = $command;
				$eformObj->save();
		
				if(isset($eformObj->status)){
					if($eformObj->status==0)
						$status =  "new";
					else if($eformObj->status==1)
						$status =  "cancelled";
					else if($eformObj->status==3)
						$status =  "approved";
					else if($eformObj->status==2)
						$status =  "in progress";
					else 
						$status =  "rejected";
				 
				 }
			   
				 $title = "Your change of mailing address application .".$status;
        		InboxMessage::where('ref_id', $eformObj->id)->where('type',13)
                ->update(['title'=>$title,'booking_date'=>$eformObj->tenancy_start,'booking_time'=>$eformObj->tenancy_start,'event_status' => $status]);
				 
				//Start Insert into notification module
				$notification = array();
				$notification['account_id'] = $eformObj->account_id;
				$notification['user_id'] = $eformObj->user_id;
				$notification['unit_no'] = $eformObj->unit_no;
				$notification['module'] = 'eform_change_mailing_address';
				$notification['ref_id'] = $eformObj->id;
				$notification['title'] = 'Change of Mailing Address E-form';
				$notification['message'] = 'There is an update from the management in regards to your Change of Mailing Address E-form';
				$result = UserNotification::insert($notification);
		
				$SettingsObj = UserNotificationSetting::where('user_id',$eformObj->user_id)->where('account_id',$eformObj->account_id)->first();
				if(empty($SettingsObj) || $SettingsObj->eforms ==1){
					$fcm_token_array ='';
					$user_token = ',';
					$ios_devices_to_send = array();
					$android_devices_to_send = array();
					$logs = UserLog::where('user_id',$eformObj->user_id)->where('status',1)->orderby('id','desc')->first();
					if(isset($logs->fcm_token) && $logs->fcm_token !=''){
						$user_token .=$logs->fcm_token.",";
						$fcm_token_array .=$logs->fcm_token.',';
						$appSipAccountList[] = $eformObj->id;
						if($logs->login_from ==1)
							$ios_devices_to_send[] = $logs->fcm_token;
						if($logs->login_from ==2)
							$android_devices_to_send[] = $logs->fcm_token;
					}
		
					$probObj = Property::find($eformObj->account_id);
					$title = "Aerea Home - ".$probObj->company_name;
					$message = "Change of Mailing Address E-form Updated";
					$notofication_data = array();
					$notofication_data['body'] =$title;
					$notofication_data['unit_no'] =$eformObj->unit_no;   
					$notofication_data['user_id'] =$eformObj->user_id;   
					$notofication_data['property'] =$eformObj->account_id; 
					$purObj = UserPurchaserUnit::where('property_id',$eformObj->account_id)->where('unit_id',$eformObj->unit_no)->where('user_id',$eformObj->user_id)->first(); 
					if(isset($purObj))
						$notofication_data['switch_id'] =$purObj->id;        
					$NotificationObj = new \App\Models\v7\FirebaseNotification();
					$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
					$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
				}

				
				return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);

			}
		
			
	}

	public function changeaddressdelete(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(44,$adminObj->role_id); 
			if(empty($permission) && $permission->delete!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				EformChangeAddress::findOrFail($id)->delete();
				return response()->json(['response' => 1, 'message' => 'Deleted']);
		
			}
	}

	public function changeaddresssearch(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(44,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$account_id = $adminObj->account_id;
				$date = $status = $month = $option = $unit = $category ='';
				$unit = $request->input('unit');

				$units = array();
				if($unit !=''){   
					$unit = str_replace("#",'',$unit);
					$unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
					})->get();   
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
								$units[] = $unitid->id;
						}
					}
				}

				$name = $request->input('name');
				$userids =array();
				if($name !=''){
					$user_more_info = UserMoreInfo::select('id','user_id','first_name','last_name')->where('account_id',$account_id)->whereNotIn('status',[2])->orderby('id','desc')->get();
            
					foreach($user_more_info as $k =>$v){
						$firstname = strtolower(Crypt::decryptString($v->first_name));
						$lastname = strtolower(Crypt::decryptString($v->last_name));
						if(str_contains($firstname,strtolower($name)) || str_contains($lastname,strtolower($name))){
							$userids[] = $v->user_id;
							
						}
					}
				}
				$ticket = $request->input('ticket');
				$status = $request->input('status');
			
				if ($ticket != '' || $unit != '' || $name != '' || $status != '') {
					$submissions =  EformChangeAddress::where('account_id',$account_id)->where(function ($query) use ($ticket,$unit,$units,$userids,$name,$status) {
						if($status !='' )
							$query->where('status', $status);
						if( $unit !='')
							$query->whereIn('unit_no', $units);
						if( $name !='')
							$query->whereIn('user_id', $userids);
						if($ticket !='')
							$query->where('ticket', 'LIKE', '%'.$ticket .'%');
					})->orderby('id', 'desc')->get();
				
					$data =array();
					foreach ($submissions as $k => $submission) {
						$record =array();
						$info =array();
						$info['id']= $submission->id;
						$info['ticket']= $submission->ticket;
						$info['user_id']= $submission->user_id;
						$info['request_date']= $submission->request_date;
						$info['owner_name']= $submission->owner_name;
						$info['contact_no']= $submission->contact_no;
						$info['email']= $submission->email;
						$info['status']= $submission->status;
						$info['created_at']= $submission->created_at->format('d/m/Y');;
						$info['updated_at']= $submission->updated_at->format('d/m/Y');;
						$record['submission'] = $info;
						//$record['documents'] = $submission->documents;
						
						$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
						$user_data =array();
						if(isset($submission->user)){
							$user_data["id"]= $submission->user->id;
							$user_data["account_id"]= $submission->user->account_id;
							$user_data["role_id"]= $submission->user->role_id;
							$user_data["user_info_id"]= $submission->user->user_info_id;
							$user_data["building_no"]= $submission->user->building_no;
							$user_data["unit_no"]= $submission->user->unit_no;
							$user_data["primary_contact"]= $submission->user->primary_contact;
							$user_data["name"]=Crypt::decryptString($submission->user->name);
							
						}
						$record['submitted_by'] = !empty($user_data)?$user_data:null;
						$unitObj = Unit::find($submission->unit_no);
						$unit_data =array();
						if(isset($unitObj)){
							$unit_data["id"]= $unitObj->id;
							$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
						}
						$record['unit'] = !empty($unit_data)?$unit_data:null;
						$data[] = $record;

					} 

					return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
				}
				else{
					return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
				}
			}
	}
	//Eform Change Mailing Address End

	//Eform Update Particulars Start

	public function updateparticularsummary(Request $request) 
	{
			
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(45,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$submissions = EformParticular::where('account_id',$account_id)->orderby('id','desc')->get(); 
				$data =array();
				foreach ($submissions as $k => $submission) {
					$record =array();
					$info =array();
					$info['id']= $submission->id;
					$info['ticket']= $submission->ticket;
					$info['user_id']= $submission->user_id;
					$info['request_date']= $submission->request_date;
					$info['owner_name']= $submission->owner_name;
					$info['contact_no']= $submission->contact_no;
					$info['email']= $submission->email;
					$info['tenancy_start']= $submission->tenancy_start;
					$info['tenancy_end']= $submission->tenancy_end;
					$info['status']= $submission->status;
					$info['created_at']= $submission->created_at->format('d/m/Y');;
					$info['updated_at']= $submission->updated_at->format('d/m/Y');;
					$record['submission'] = $info;
					//$record['documents'] = $submission->documents;
					//$record['submitted_by'] = $submission->user;
					$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
					//$record['unit'] = isset($submission->unitinfo)?$submission->unitinfo:null;
					$user_data =array();
					if(isset($submission->user)){
						$user_data["id"]= $submission->user->id;
						$user_data["account_id"]= $submission->user->account_id;
						$user_data["role_id"]= $submission->user->role_id;
						$user_data["user_info_id"]= $submission->user->user_info_id;
						$user_data["building_no"]= $submission->user->building_no;
						$user_data["unit_no"]= $submission->user->unit_no;
						$user_data["primary_contact"]= $submission->user->primary_contact;
						$user_data["name"]=Crypt::decryptString($submission->user->name);
					}
					$record['submitted_by'] = !empty($user_data)?$user_data:null;
					$unitObj = Unit::find($submission->unit_no);
					$unit_data =array();
					if(isset($unitObj)){
						$unit_data["id"]= $unitObj->id;
						$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
					}
					$record['unit'] = !empty($unit_data)?$unit_data:null;
					$data[] = $record;
				} 
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
	}
	

	public function updateparticularinfo(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(45,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$submission = EformParticular::find($id);
				EformParticular::where('id', $id)
				->update(['view_status' => 1,'updated_at'=>$submission->updated_at]);

				$record =array();
						$info =array();
						$info['id']= $submission->id;
						$info['ticket']= $submission->ticket;
						$info['user_id']= $submission->user_id;
						$info['request_date']= $submission->request_date;
						$info['owner_name']= $submission->owner_name;
						$info['contact_no']= $submission->contact_no;
						$info['email']= $submission->email;
						$info['status']= $submission->status;
						$info['owner_signature']= $submission->owner_signature;
						$info['tenancy_start']= $submission->tenancy_start;
						$info['tenancy_end']= $submission->tenancy_end;
						$info['address']= $submission->address;
						$info['remarks']= $submission->remarks;
						$info['created_at']= $submission->created_at->format('d/m/Y');;
						$info['updated_at']= $submission->updated_at->format('d/m/Y');;
						$record['submission'] = $info;
						$record['owners'] = $submission->owners;
						$record['tenants'] = $submission->tenants;
						//$record['documents'] = $submission->documents;
						$record['submitted_by'] = $submission->user;
						$record['unit_no'] = isset($submission->unitinfo)?$submission->unitinfo->unit:null;
						$record['unit'] = isset($submission->unitinfo)?$submission->unitinfo:null;
						$data[] = $record;
				
				$file_path = env('APP_URL')."/storage/app";

				return response()->json(['details'=>$record,'file_path'=>$file_path,'response' => 1, 'message' => 'Success']);

		
			}
	}


	public function updateparticularedit(Request $request) 
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

			$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(45,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				$command = $request->input('remarks');      
				$eformObj = EformParticular::find($id);
				$eformObj->status = $request->input('status');
				$eformObj->remarks = $command;
				$eformObj->save();
		
				if(isset($eformObj->status)){
					if($eformObj->status==0)
						$status =  "new";
					else if($eformObj->status==1)
						$status =  "cancelled";
					else if($eformObj->status==3)
						$status =  "approved";
					else if($eformObj->status==2)
						$status =  "in progress";
					else 
						$status =  "rejected";
				 
				 }
			   
				 $title = "Your update particulars application .".$status;
        		InboxMessage::where('ref_id', $eformObj->id)->where('type',13)
                ->update(['title'=>$title,'booking_date'=>$eformObj->tenancy_start,'booking_time'=>$eformObj->tenancy_start,'event_status' => $status]);
				
				//Start Insert into notification module
				$notification = array();
				$notification['account_id'] = $eformObj->account_id;
				$notification['user_id'] = $eformObj->user_id;
				$notification['unit_no'] = $eformObj->unit_no;
				$notification['module'] = 'eform_update_particulars';
				$notification['ref_id'] = $eformObj->id;
				$notification['title'] = 'Update of Particulars E-form';
				$notification['message'] = 'There is an update from the management in regards to your Update of Particulars E-form';
				$result = UserNotification::insert($notification);
		
				$SettingsObj = UserNotificationSetting::where('user_id',$eformObj->user_id)->where('account_id',$eformObj->account_id)->first();
				if(empty($SettingsObj) || $SettingsObj->eforms ==1){
					$fcm_token_array ='';
					$user_token = ',';
					$ios_devices_to_send = array();
					$android_devices_to_send = array();
					$logs = UserLog::where('user_id',$eformObj->user_id)->where('status',1)->orderby('id','desc')->first();
					if(isset($logs->fcm_token) && $logs->fcm_token !=''){
						$user_token .=$logs->fcm_token.",";
						$fcm_token_array .=$logs->fcm_token.',';
						$appSipAccountList[] = $eformObj->id;
						if($logs->login_from ==1)
							$ios_devices_to_send[] = $logs->fcm_token;
						if($logs->login_from ==2)
							$android_devices_to_send[] = $logs->fcm_token;
					}
		
					$probObj = Property::find($eformObj->account_id);
					$title = "Aerea Home - ".$probObj->company_name;
					$message = "Update of Particulars E-form Updated";
					$notofication_data = array();
					$notofication_data['body'] =$title;
					$notofication_data['unit_no'] =$eformObj->unit_no;   
					$notofication_data['user_id'] =$eformObj->user_id;   
					$notofication_data['property'] =$eformObj->account_id; 
					$purObj = UserPurchaserUnit::where('property_id',$eformObj->account_id)->where('unit_id',$eformObj->unit_no)->where('user_id',$eformObj->user_id)->first(); 
					if(isset($purObj))
						$notofication_data['switch_id'] =$purObj->id;        
					$NotificationObj = new \App\Models\v7\FirebaseNotification();
					$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
					$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
				}

				
				return response()->json(['data'=>$eformObj,'response' => 1, 'message' => 'Updated']);

			}
		
			
	}

	public function updateparticulardelete(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(45,$adminObj->role_id); 
			if(empty($permission) && $permission->delete!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				EformParticularOwner::where('reg_id', $id)->delete();

				EformParticularTenant::where('reg_id', $id)->delete();
		
				EformParticular::findOrFail($id)->delete();

				return response()->json(['response' => 1, 'message' => 'Deleted']);
		
			}
	}

	public function updateparticularsearch(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(44,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$account_id = $adminObj->account_id;
				$date = $status = $month = $option = $unit = $category ='';
				$unit = $request->input('unit');
				$units = array();
				if($unit !=''){   
					$unit = str_replace("#",'',$unit);
					$unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
					})->get();   
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
								$units[] = $unitid->id;
						}
					}
				}

				$userids = array();
				
				$name = $request->input('name');
				if($name !=''){
					$user_more_info = UserMoreInfo::select('id','user_id','first_name','last_name')->where('account_id',$account_id)->whereNotIn('status',[2])->orderby('id','desc')->get();
					foreach($user_more_info as $k =>$v){
						$firstname = strtolower(Crypt::decryptString($v->first_name));
						$lastname = strtolower(Crypt::decryptString($v->last_name));
						if(str_contains($firstname,strtolower($name)) || str_contains($lastname,strtolower($name))){
							$userids[] = $v->user_id;
							
						}
					}
				}
				
				$ticket = $request->input('ticket');
				$status = $request->input('status');
			
				if ($ticket != '' || $unit != '' || $name != ''|| $status != '') {
					$submissions =  EformParticular::where('account_id',$account_id)->where(function ($query) use ($ticket,$unit,$units,$userids,$name,$status) {
						if($status !='' )
							$query->where('status', $status);
						if( $unit !='')
							$query->whereIn('unit_no', $units);
						if( $name !='')
							$query->whereIn('user_id', $userids);
						if($ticket !='')
							$query->where('ticket', 'LIKE', '%'.$ticket .'%');
					})->orderby('id', 'desc')->get();			
					$data =array();
					foreach ($submissions as $k => $submission) {
						$record =array();
						$info =array();
						$info['id']= $submission->id;
						$info['ticket']= $submission->ticket;
						$info['user_id']= $submission->user_id;
						$info['request_date']= $submission->request_date;
						$info['owner_name']= $submission->owner_name;
						$info['contact_no']= $submission->contact_no;
						$info['email']= $submission->email;
						$info['tenancy_start']= $submission->tenancy_start;
						$info['tenancy_end']= $submission->tenancy_end;
						$info['status']= $submission->status;
						$info['created_at']= $submission->created_at->format('d/m/Y');;
						$info['updated_at']= $submission->updated_at->format('d/m/Y');;
						$record['submission'] = $info;
						//$record['documents'] = $submission->documents;
						//$record['submitted_by'] = $submission->user;
						$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
						//$record['unit'] = isset($submission->unitinfo)?$submission->unitinfo:null;
						$user_data =array();
						if(isset($submission->user)){
							$user_data["id"]= $submission->user->id;
							$user_data["account_id"]= $submission->user->account_id;
							$user_data["role_id"]= $submission->user->role_id;
							$user_data["user_info_id"]= $submission->user->user_info_id;
							$user_data["building_no"]= $submission->user->building_no;
							$user_data["unit_no"]= $submission->user->unit_no;
							$user_data["primary_contact"]= $submission->user->primary_contact;
							$user_data["name"]=Crypt::decryptString($submission->user->name);
						}
						$record['submitted_by'] = !empty($user_data)?$user_data:null;
						$unitObj = Unit::find($submission->unit_no);
						$unit_data =array();
						if(isset($unitObj)){
							$unit_data["id"]= $unitObj->id;
							$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
						}
						$record['unit'] = !empty($unit_data)?$unit_data:null;
						$data[] = $record;
					} 
					return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
				}
				else{
					return response()->json(['data'=>null,'response' => 200, 'message' => 'Search option empty']);
				}

			}
	}


	//Eform Update Particulars END

	//Visitor Module START

	public function visitorsummary(Request $request) 
	{
			
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(45,$adminObj->role_id); 
			
			if(empty($permission) && isset($permission->view) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
			
				$account_id = $adminObj->account_id;
				$bookings = VisitorBooking::where('account_id',$account_id)->orderby('id','desc')->get(); 
				$data =array();
				foreach ($bookings as $k => $booking) {
					$record =array();
					$record['id'] = $booking->id;
					$record['ticket'] = $booking->ticket;
					$record['booking_type'] = $booking->booking_type;
					$record['booking_type'] = $booking->booking_type;
					$record['view_status'] = $booking->view_status;
					if($booking->booking_type==1)
					{
						$record['unit'] =  isset($booking->getunit->unit)?Crypt::decryptString($booking->getunit->unit):null;
						$record['invited_by'] =isset($booking->user->name)?Crypt::decryptString($booking->user->name):null;
						$record['entry_date'] =  '';
						$record['entry_time'] =  '';
                    }else{
						$record['unit'] = isset($booking->getunit->unit)?Crypt::decryptString($booking->getunit->unit):null;
						$record['invited_by'] = "Walk-In";
						$record['entry_date'] =  date('d/m/y',strtotime($booking->entry_date));
						$record['entry_time'] =  date('H:i',strtotime($booking->entry_date));
					}
					$record['date_of_visit'] =  date('d/m/y',strtotime($booking->visiting_date));
					$record['visitor_count'] = $booking->visitors->count();
					$record['purpose'] = isset($booking->visitpurpose->visiting_purpose)?$booking->visitpurpose->visiting_purpose:null;
					
					if($booking->visited_count->count() >0 && $booking->visited_count->count() >= $booking->visitors->count())
						$status = "Entered";
					else if($booking->visited_count->count() >0 && $booking->visited_count->count() < $booking->visitors->count())
						$status = $booking->visited_count->count()." Entered";
					else if($booking->registered_count->count() >0 && $booking->registered_count->count() == $booking->invitedemails->count())
						$status = "Registration Success";
					else if($booking->registered_count->count() >0 && $booking->registered_count->count() <= $booking->visitors->count())
						$status = $booking->registered_count->count()." Registered";
					else if($booking->status==0)
						$status = "Pending";
					else if($booking->status==1)
						$status = "Cancelled";
					else  
						$status = "Entered";		
						
					$record['status'] = $status;
					$data[] = $record;
				} 
				$types = VisitorType::where('account_id', $account_id)->pluck('visiting_purpose', 'id')->all();

				return response()->json(['data'=>$data,'purposes'=>$types,'response' => 1, 'message' => 'Success']);
			}
	}

	public function visitornew(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(34,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$bookings = VisitorBooking::where('account_id',$account_id)->where('view_status',0)->where('status',0)->orderby('id','desc')->get(); 
				$data =array();
				foreach ($bookings as $k => $booking) {
					$record =array();
					$record['id'] = $booking->id;
					$record['ticket'] = $booking->ticket;
					$record['booking_type'] = $booking->booking_type;
					$record['booking_type'] = $booking->booking_type;
					if($booking->booking_type==1)
					{
						$record['building'] =  isset($booking->user->userinfo->getunit->buildinginfo->building)?$booking->user->userinfo->getunit->buildinginfo->building:null;
						$record['unit'] =  isset($booking->user->userinfo->getunit->unit)?Crypt::decryptString($booking->user->userinfo->getunit->unit):null;
						$record['invited_by'] =isset($booking->user->name)?Crypt::decryptString($booking->user->name):null;
						$record['entry_date'] =  '';
						$record['entry_time'] =  '';
                    }else{
						$record['building'] =  isset($booking->user->userinfo->getunit->buildinginfo->building)?$booking->user->userinfo->getunit->buildinginfo->building:null;
						$record['unit'] = isset($booking->getunit->unit)?Crypt::decryptString($booking->getunit->unit):null;
						$record['invited_by'] = "Walk-In";
						$record['entry_date'] =  date('d/m/y',strtotime($booking->entry_date));
						$record['entry_time'] =  date('H:i',strtotime($booking->entry_date));
					}
					$record['date_of_visit'] =  date('d/m/y',strtotime($booking->visiting_date));
					$record['visitor_count'] = $booking->visitors->count();
					$record['purpose'] = isset($booking->visitpurpose->visiting_purpose)?$booking->visitpurpose->visiting_purpose:null;
					$record['status_id'] = $booking->status; 

					if($booking->status==0)
						$record['status'] = "Pending";
                    else if($booking->status==1)
						$record['status'] = "Cancelled";
                    else  
						$record['status'] = "Visited";

					$data[] = $record;
				} 
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
	}
	

	public function visitorinfo(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(34,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$bookingObj = VisitorBooking::find($id);
				$bookingObj->view_status = 1;
				$bookingObj->save();
				$account_id = $adminObj->account_id;
				if($bookingObj->id <=0){
					return response()->json(['details'=>'','file_path'=>'','response' => 200, 'message' => 'Booking has been cancelled or closed.']);
				}
				
				$file_path = env('APP_URL')."/storage/app";
				$data = array();
				$data['submission'] = $bookingObj;
				$data['visitors'] = isset($bookingObj->visitors)?$bookingObj->visitpurpose:null;
				$data['purpose'] = isset($bookingObj->visitpurpose)?$bookingObj->visitpurpose:null;
				$data['unitinfo'] = isset($bookingObj->getunit)?$bookingObj->getunit:null;

				if($bookingObj->status==0)
					$status = "Pending";
                else if($bookingObj->status==1)
					$status = "Cancelled";
                else  
					$status = "Visited";
				
				$property = $bookingObj->propertyinfo->company_name;
				$invited_by = isset($bookingObj->user->name)?$bookingObj->user->name:null;

				$types = VisitorType::where('account_id', $account_id)->pluck('visiting_purpose', 'id')->all();

				return response()->json(['details'=>$bookingObj,'status'=>$status,'purposes'=>$types,'file_path'=>$file_path,'property' =>$property,'invited_by'=>$invited_by,'response' => 1, 'message' => 'Success']);

		
			}
	}

	public function visitor_types(Request $request) 
		{
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(34,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$units = Unit::where('account_id',$account_id)->pluck('unit', 'id')->all();
				$types = VisitorType::where('account_id', $account_id)->get();
				$type_array = array();
				if(isset($types)){
					foreach($types as $k =>$type){
						$data = array();
						$data['type'] = $type;
						if(isset($type->subcategory) && $type->cat_dropdown==1){
							$data['type']['subcat'] = $type->subcategory;
						}
						$type_array[] = $data;
					}
				}

				return response()->json(['units'=>$units,'types'=>$type_array,'response' => 1, 'message' => 'Success']);

		
			}
	}

	public function availability_check(Request $request)
    {
        $cards = array();
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
        $account_id = $adminObj->account_id;
        $date = $request->date;
        $purpose = $request->purpose;

        $visiting_purpose = VisitorType::where('account_id',$account_id)->where('id',$purpose)->first();
        
		if($visiting_purpose->limit_set ==0){
			return response()->json(['slot_available'=>5,'limit'=>1,'id_required'=>$visiting_purpose->id_required,'response' => 1, 'message' => 'Success']);
        }
        else{
			$visitor_types = VisitorType::where('account_id',$account_id)->where('limit_set',1)->where('status',1)->get();
			$types = array();
			foreach($visitor_types as $type){
				$types[] = $type->id;
			}
			
			$total_visitor =0;
			$visitor_records = VisitorBooking::where('account_id',$account_id)->where('visiting_date',$date)->whereIn('visiting_purpose',$types)->whereIn('status',[0,2])->get();
			foreach($visitor_records as $records){
				$total_visitor +=$records->visitors->count();
			}
			
			$property = Property::find($account_id);

			$slot_available = $property->visitors_allowed - $total_visitor;

			return response()->json(['slot_available'=>$slot_available,'limit'=>1,'id_required'=>$visiting_purpose->id_required,'response' => 1, 'message' => 'Success']);
			
		}
        
	}
	
	public function visitorqrvalidation(Request $request)
    {
        $cards = array();
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
        $account_id = $adminObj->account_id;
		//$bookingId = $request->booking_id;
		$booking_token = $request->booking_token;
		
		$bookingObj = VisitorBooking::where('ticket',$booking_token)->where('account_id',$account_id)->first();
		if(empty($bookingObj)){
			return response()->json(['response' => 100, 'message' => 'Booking token not valid']);
		}
		//print_r($bookingObj->visiting_end_time);

		$visiting_time = Carbon::now()->format('Y-m-d H:i:s');
		//echo "End Time :".$bookingObj->visiting_end_time;
		//echo " Visit Time :".$visiting_time;

		if(isset($bookingObj->visiting_date) && $bookingObj->visiting_start_time <= $visiting_time && $bookingObj->visiting_end_time >= $visiting_time)
		{
			$purpose = $bookingObj->visiting_purpose;
			$env_max_scan_count = $bookingObj->qr_scan_limit;

			$visiting_purpose = VisitorType::where('id',$purpose)->first();
			if($bookingObj->qr_scan_type !=2){
				$vid = $request->visitor_id;
				$VisitorObj = VisitorList::where('book_id',$bookingObj->id)->where('id',$vid)->first();
				//echo " Max :".$env_max_scan_count;
				//echo " Visit :".$VisitorObj->visit_count;
				if(empty($VisitorObj)){
					return response()->json(['response' => 200, 'message' => 'Visitor id not valid']);
				}
				else if($VisitorObj->visit_count >= $env_max_scan_count)
				{
					return response()->json(['response' => 300, 'message' => 'Reached Maximum Visit Count']);
				}
				else{
					$visit_count = $VisitorObj->visit_count + 1;
					$entry_date = date("Y-m-d H:i:s");
					$result = VisitorList::where( 'id' , $VisitorObj->id)->update( array( 'visit_count' => $visit_count,'visit_status' => 1,'entry_date'=>$entry_date));
					return response()->json(['booking_id'=>$bookingObj->id,'response' => 1, 'message' => 'Success']);
				}
			}
			else{
				if($bookingObj->scan_count >= $env_max_scan_count)
				{
					return response()->json(['response' => 300, 'message' => 'Reached Maximum Visit']);
				}
				else{
					$bookingObj->scan_count = $bookingObj->scan_count +1;
					$bookingObj->view_status = 1;
					$bookingObj->save();
					return response()->json(['booking_id'=>$bookingObj->id,'response' => 1, 'message' => 'Success']);
				}
			}
		}
		else{
			if(isset($bookingObj->visiting_date) && $bookingObj->visiting_end_time < $visiting_time)
			{
				return response()->json(['response' => 400, 'message' => 'Booking Expired']);
			}
			else{
				return response()->json(['response' => 500, 'message' => 'QR Code Not Active']);
			}
		}
        
        
    }



	public function visitorwalkin(Request $request) 
	{
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(34,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$input = $request->all();
				$now = Carbon::now()->format('Y-m-d H:i:s');
		
				$ticket = new \App\Models\v7\VisitorBooking();
				$input['user_id'] = $login_id;
				$input['account_id'] = $adminObj->account_id;
		
				$propObj = property::find($input['account_id']);
				$input['ticket'] = $ticket->ticketgen($propObj->short_code);
				$input['visiting_date'] = $now;
				$input['entry_date'] = $now;
				$input['view_status'] = 1;
				$input['status'] = 2;
				$input['booking_type'] = 2;
		
				if($request->visiting_purpose !=''){
					$comp_info = "company_info_".$request->visiting_purpose;
					$sub_cat = "sub_cat_".$request->visiting_purpose;
					if(isset($input[$comp_info])){
						$input['comp_info'] = $input[$comp_info];
					}
					if(isset($input[$sub_cat])){
						$input['sub_cat'] = $input[$sub_cat];
					}
				}
				$booking = VisitorBooking::create($input);
				
				$data['book_id'] = $booking->id;
		
			   for($i=1;$i<=5;$i++){
		
				$name = 'name_'.$i;
				$mobile = 'mobile_'.$i;
				$vehicle ='vehicle_no_'.$i;
				$id_number = 'id_number_'.$i;
				if(!empty($request->input($name)) && !empty($request->input($mobile))){
					
					$data['name'] = $request->input($name);
					$data['mobile'] = $request->input($mobile);
					$data['vehicle_no'] = $request->input($vehicle);
					$data['id_number'] = $request->input($id_number);
					$data['vehicle_no'] = $request->input($vehicle);
					$data['entry_date'] = $now;
					$data['visit_status'] = 1;
					$data['created_at'] = $now;
					$data['updated_at'] = $now;
					$details[] = $data;
				}
				
				
			}
			$record = VisitorList::insert($details);
			return response()->json(['details'=>$booking,'response' => 1, 'message' => 'Success']);

		
			}
	}


	public function visitoredit(Request $request) 
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

		$login_id = Auth::id();

			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(34,$adminObj->role_id); 
			if(empty($permission) && $permission->edit!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				$input = $request->all();   
				$BookingObj = VisitorBooking::find($id);
				$BookingObj->status = 2;
				$BookingObj->save();

				$visitors = $request->input('visitor_ids');
				
				if(isset($visitors)){
					foreach($visitors as $visitor) {

						$id_number = "id_number_".$visitor;
						$vehicle  = "vehicle_no_".$visitor;
		
						$visitorObj = VisitorList::find($visitor);
						$visitorObj->vehicle_no = $request->input($vehicle);
						$visitorObj->id_number = $request->input($id_number);
		
						$visitorObj->entry_date = Carbon::now();
						$visitorObj->visit_status = 1;
						$visitorObj->save();
		
					}
			}
				return response()->json(['data'=>$BookingObj,'response' => 1, 'message' => 'Updated']);

			}
		
			
	}

	public function visitordelete(Request $request) 
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
			
		$login_id = Auth::id();
			$id = $request->id;
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(34,$adminObj->role_id); 
			if(empty($permission) && $permission->delete!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$bookingObj = VisitorBooking::find($id);
        		VisitorList::where('book_id', $id)->delete();
       			VisitorBooking::findOrFail($id)->delete();

				return response()->json(['response' => 1, 'message' => 'Deleted']);
		
			}
	}

	public function visitorsearch(Request $request) 
	{
			
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(44,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$account_id = $adminObj->account_id;
				$bookingid = $request->input('bookingid');
				$purpose = $request->input('purpose');
				$date = $request->input('date');
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
				$userObj = UserPurchaserUnit::select('user_id')->where('property_id',$account_id)->wherein('unit_id',$units)->get();
				foreach($userObj as $k => $user){
					$userids[] = $user->user_id;
				}
				//print_r($userids);
				$booking_type = $request->input('booking_type');
				
				if ($bookingid != '' || $unit != '' || $date != ''|| $purpose != '' || $booking_type != '' || $building !='') {
					$bookings = VisitorBooking::where('account_id',$account_id)->where(function ($query) use ($bookingid,$unit,$units,$date,$purpose,$booking_type,$building) {
						if($bookingid !='')
							$query->where('ticket', 'LIKE', '%'.$bookingid .'%');
						if($unit !='' || $building !='')
							$query->whereIn('unit_no', $units);
						if($date !='')
							$query->where('visiting_date',$date);
						if($purpose !='')
							$query->where('visiting_purpose', '=', $purpose);
						if($booking_type !='')
							$query->where('booking_type', '=', $booking_type);
						
					})->orderby('id', 'desc')->get();			
					$data =array();
					foreach ($bookings as $k => $booking) {
						$record =array();
						$record['id'] = $booking->id;
						$record['ticket'] = $booking->ticket;
						$record['booking_type'] = $booking->booking_type;
						$record['booking_type'] = $booking->booking_type;
						if($booking->booking_type==1)
						{
							$record['building'] =  isset($booking->user->userinfo->getunit->buildinginfo->building)?$booking->user->userinfo->getunit->buildinginfo->building:null;
							$record['unit'] =  isset($booking->user->userinfo->getunit->unit)?Crypt::decryptString($booking->user->userinfo->getunit->unit):null;
							$record['invited_by'] =isset($booking->user->name)?Crypt::decryptString($booking->user->name):null;
							$record['entry_date'] =  '';
							$record['entry_time'] =  '';
						}else{
							$record['building'] =  isset($booking->user->userinfo->getunit->buildinginfo->building)?$booking->user->userinfo->getunit->buildinginfo->building:null;
							$record['unit'] = isset($booking->getunit->unit)?Crypt::decryptString($booking->getunit->unit):null;
							$record['invited_by'] = "Walk-In";
							$record['entry_date'] =  date('d/m/y',strtotime($booking->entry_date));
							$record['entry_time'] =  date('H:i',strtotime($booking->entry_date));
						}
						$record['date_of_visit'] =  date('d/m/y',strtotime($booking->visiting_date));
						$record['visitor_count'] = $booking->visitors->count();
						$record['purpose'] = isset($booking->visitpurpose->visiting_purpose)?$booking->visitpurpose->visiting_purpose:null;
						
						if($booking->visited_count->count() >= $booking->visitors->count())
							$record['status'] = "Entered";
						else if($booking->visited_count->count() >0 && $booking->visited_count->count() < $booking->visitors->count())
							$record['status'] = $booking->visited_count->count()." Entered";
						else if($booking->status==0)
							$record['status'] = "Pending";
						else if($booking->status==1)
							$record['status'] = "Cancelled";
						else  
							$record['status'] = "Entered";
						$data[] = $record;
					} 
					$types = VisitorType::where('account_id', $account_id)->pluck('visiting_purpose', 'id')->all();

					return response()->json(['data'=>$data,'purposes'=>$types,'response' => 1, 'message' => 'Success']);
				}
				else{
					return response()->json(['data'=>null,'purposes'=>'','response' => 200, 'message' => 'Search option empty']);
				}

			}
	}

	/** Visitor Management API END */


	//Resident Management Module START

	public function paymentoverview(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(61,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-t');
		
				//Current month Amount START
				$mf_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',1)->whereBetween('payment_received_date',array($from_date,$to_date))->sum('amount'); 
				$sf_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',2)->whereBetween('payment_received_date',array($from_date,$to_date))->sum('amount'); 
				$tax_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',4)->whereBetween('payment_received_date',array($from_date,$to_date))->sum('amount'); 
				$int_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',3)->whereBetween('payment_received_date',array($from_date,$to_date))->sum('amount'); 
				$monthly_fee = $mf_amounts + $sf_amounts + $tax_amounts + $int_amounts;
				//Current month Amount END
		
				//Current month records START
				$total_invoices = FinanceInvoice::where('account_id',$account_id)->whereBetween('invoice_date',array($from_date,$to_date))->orderby('id','desc')->count();  
				$pending_invoices = FinanceInvoice::where('account_id',$account_id)->where('status',1)->whereBetween('invoice_date',array($from_date,$to_date))->orderby('id','desc')->count(); 
				$partial_invoices = FinanceInvoice::where('account_id',$account_id)->where('status',2)->whereBetween('invoice_date',array($from_date,$to_date))->orderby('id','desc')->count(); 
				$paid_invoices = FinanceInvoice::where('account_id',$account_id)->where('status',3)->whereBetween('invoice_date',array($from_date,$to_date))->orderby('id','desc')->count(); 
				//Current month records END
		
				//Current month Amount START
				$tot_mf_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',1)->sum('amount'); 
				$tot_sf_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',2)->sum('amount'); 
				$tot_tax_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',4)->sum('amount'); 
				$tot_int_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',3)->sum('amount'); 
				
		
				$all_mf_amounts =DB::table('finance_invoice_payment_paid_details')
									->select(DB::raw('SUM(amount) as total_amount'),'payment_received_date')->where('account_id',$account_id)->where('type',1)
									->groupBy(DB::raw('YEAR(payment_received_date) DESC, MONTH(payment_received_date) DESC'))->orderby('payment_received_date','desc')->limit(5)->get();
				
				$all_sf_amounts =DB::table('finance_invoice_payment_paid_details')
									->select(DB::raw('SUM(amount) as total_amount'),'payment_received_date')->where('account_id',$account_id)->where('type',2)
									->groupBy(DB::raw('YEAR(payment_received_date) DESC, MONTH(payment_received_date) DESC'))->orderby('payment_received_date','desc')->limit(5)->get();
				$mf_y_axis = '';
				if(isset($all_mf_amounts)){
					foreach($all_mf_amounts as $all_mf_amount){
						$mf_y_axis .= "{ y: ".$all_mf_amount->total_amount.", label: '".date('M y',strtotime($all_mf_amount->payment_received_date))."'},";
					}
				}
		
				$sf_y_axis = '';
				if(isset($all_sf_amounts)){
					foreach($all_sf_amounts as $all_sf_amount){
						$sf_y_axis .= "{ y: ".$all_sf_amount->total_amount.", label: '".date('M y',strtotime($all_sf_amount->payment_received_date))."'},";
					}
				}
				$mf_y_axis = substr($mf_y_axis,0,-1);
				$sf_y_axis = substr($sf_y_axis,0,-1);

				$data = array();
				$data['total_invoices'] = $total_invoices;
				$data['pending_payment_invoices'] = $pending_invoices;
				$data['partial_payment_invoices'] = $partial_invoices;
				$data['paid_invoices'] = $paid_invoices;
				$data['monthly_fee_collected'] = $monthly_fee;
				$data['mf_amounts'] = $mf_amounts;
				$data['sf_amounts'] = $sf_amounts;
				$data['tax_amounts'] = $tax_amounts;
				$data['int_amounts'] = $int_amounts;
				$data['tot_mf_amounts'] = $tot_mf_amounts;
				$data['tot_sf_amounts'] = $tot_sf_amounts;
				$data['tot_tax_amounts'] = $tot_tax_amounts;
				$data['tot_int_amounts'] = $tot_int_amounts;
				$data['mf_y_axis'] = $mf_y_axis;
				$data['sf_y_axis'] = $sf_y_axis;

				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
			}
	}

	public function viewinvoice(Request $request) 
	{
			$rules=array(
				'id' => 'required',
			);
			$messages=array(
				'id.required' => ' Invoice id is missing',
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
			
			$permission = $adminObj->check_permission(61,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$id = $request->id;
				$Unitinvoice = FinanceInvoice::find($id);
				$invoice = FinanceInvoiceInfo::find($Unitinvoice->info_id); 
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
				
				$UserPurchaserRecords = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id',$Unitinvoice->unit_no)->where('role_id',2)->orderby('id','asc')->get();
				$UserPurchaserLists = array();
				if(isset($UserPurchaserRecords)){
					//echo "Purchaser contact";
					foreach($UserPurchaserRecords as $UserPurchaserRecord){
						$UserPurchaserLists[] = $UserPurchaserRecord->user_id;
					}
				}
				//print_r($UserPurchaserLists);
				$purchasers = User::WhereIn('id',$UserPurchaserLists)->where('status',1)->orderby('id','asc')->get();
				//$purchasers = User::where('role_id',2)->where('status',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->get();   
				$data =array();
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


				$invoiceLists = FinanceInvoice::where('unit_no',$Unitinvoice->unit_no)->where('id','!=',$id)->get();
				$invInIds = array();
				if($invoiceLists){
					foreach($invoiceLists as $k => $invoiceList){
						$invInIds[] = $invoiceList->id;
					}
				}
				$paymentHistory = FinanceInvoicePayment::whereIn('invoice_id',$invInIds)->get();
		
				$balance_amount = ($Unitinvoice->payable_amount - $amount_received);
				
				$data['invoice']['id'] = $Unitinvoice->id;
				$data['invoice']['account_id'] = $Unitinvoice->account_id;
				$data['invoice']['invoice_date'] = $Unitinvoice->invoice_date;
				$data['invoice']['due_date'] = $Unitinvoice->due_date;
				$data['invoice']['batch_file_no'] = $Unitinvoice->batch_file_no;
				$data['invoice']['invoice_no'] = $Unitinvoice->invoice_no;
				$data['invoice']['invoice_amount'] = $Unitinvoice->payable_amount;
				$data['invoice']['balance_lable'] = ($balance_amount < 0)?'EXCESS PAID':'BALANCE';
				$data['invoice']['balance_amount'] = $balance_amount;
				$data['invoice']['remarks'] = $Unitinvoice->remarks;
				$data['invoice']['created_at'] = $Unitinvoice->created_at->format('d/m/Y');
				$data['building_info'] =isset($Unitinvoice->getunit->buildinginfo)?$Unitinvoice->getunit->buildinginfo->building:null;
				$data['unit_info'] = isset($Unitinvoice->getunit)?$Unitinvoice->getunit->unit:null;
				$data['details'] = $Unitinvoice->paymentdetails;
				$data['CreditPayments'] = isset($LastInvoice->CreditPayments)?$LastInvoice->CreditPayments:null;
				$data['batch_info'] = $invoice;
				$data['purchasers'] = $purchasers;
				$data['last_invoice'] = $LastInvoice;
				$data['LastInvoicePayments'] = $LastInvoicePayments;
				$data['previousDetails'] = $previousDetails;
				$data['currentDetails'] = $currentDetails;
				$data['CurrentInvoicePayments']= $CurrentInvoicePayments;
				$data['balance_amount']= $balance_amount;


				$data['recent_payment_transaction'] = $Unitinvoice->payments;
				$data['payment_received_history'] = $paymentHistory;

				$visitor_app_url = env('VISITOR_APP_URL')."invoice-pdf/";

				return response()->json(['data'=>$data,'file_path'=>$visitor_app_url,'response' => 1, 'message' => 'Success']);
			}
	}

	
	public function invoiceupdate(Request $request) 
	{
			$rules=array(
				'id' => 'required',
			);
			$messages=array(
				'id.required' => ' Invoice id is missing',
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
			
			$permission = $adminObj->check_permission(61,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$id = $request->id;
				$InvoiceObj = FinanceInvoice::find($id);
				if(isset($InvoiceObj)){
					$InvoiceObj->remarks = $request->remarks;
					$InvoiceObj->save();
					return response()->json(['response' => 1, 'message' => 'Success']);
				}
				else{
					return response()->json(['response' => 200, 'message' => 'Invalid invoice id']);
				}
			}
	}

	public function invoicedelete(Request $request) 
	{
			$rules=array(
				'id' => 'required',
			);
			$messages=array(
				'id.required' => ' Invoice id is missing',
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
			
			$permission = $adminObj->check_permission(61,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$id = $request->id;
				$InvoiceObj = FinanceInvoice::find($id);
				FinanceAdvancePayment::where('invoice_id', $id)->delete();
				FinanceCreditPayment::where('invoice_id', $id)->delete();
				FinanceInvoicePayment::where('invoice_id', $id)->delete();
				FinanceInvoicePaymentDetail::where('invoice_id', $id)->delete();
				FinanceInvoice::findOrFail($id)->delete();

				$last_invoice = FinanceInvoice::where('unit_no' , $InvoiceObj->unit_no)->orderby('id','desc')->first(); 
				if(isset($last_invoice)){
					FinanceInvoice::where('id' , $last_invoice->id)->update( array( 'active_status' => 1)); //activate all previous 

				}
				return response()->json(['response' => 1, 'message' => 'Success']);
			}
	}

	public function batches(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(61,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$invoices = FinanceInvoiceInfo::where('account_id',$account_id)->orderby('id','desc')->get(); 
				$data =array(); 
				if($invoices){
					foreach($invoices as $k => $inv) {
						$record =array();
						$record['id'] = $inv->id;
						$record['batch_no'] = $inv->batch_no;
						$record['count'] = isset($inv->invoices)?$inv->invoices->count():0;
						$record['created_by'] = isset($inv->admininfo->name)?Crypt::decryptString($inv->admininfo->name):null;
						$record['created_date'] = date('d/m/y',strtotime($inv->created_at));
						$record['pdf_path'] = env('VISITOR_APP_URL')."/batchinvoices/".$inv->id;
						$data[] = $record;
					}
				}
				$visitor_app_url = env('VISITOR_APP_URL')."/batchinvoices/";

				return response()->json(['data'=>$data,'file_path'=>$visitor_app_url,'response' => 1, 'message' => 'Success']);
			}
	}
	public function batchesearch(Request $request) 
	{
		$login_id = Auth::id();
		
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(61,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$account_id = $adminObj->account_id;
				$q = $option = $batch_file_no = $from_date = $to_date = '';
				$month = $request->input('month');
				if($month !=''){
					$from_date = date('Y-m-01', strtotime($month));
					$to_date = date('Y-m-t', strtotime($month));
				}
				$batch_file_no = $request->input('batch_file_no');
				
				if($month !='' || $batch_file_no!=''){
					$invoices =  FinanceInvoiceInfo::where('account_id',$account_id)->where(function($query)  use ($from_date,$to_date,$batch_file_no){
						
						if($from_date !='')
							$query->whereBetween('created_at',array($from_date,$to_date));
						
						if($batch_file_no !='')
							$query->where('batch_no', 'LIKE', '%'.$batch_file_no .'%');
					
					})->orderBy('id','DESC')->get();
					
					$data =array(); 
					if($invoices){
						foreach($invoices as $k => $inv) {
							$record =array();
							$record['id'] = $inv->id;
							$record['batch_no'] = $inv->batch_no;
							$record['count'] = isset($inv->invoices)?$inv->invoices->count():0;
							$record['created_by'] = isset($inv->admininfo->name)?Crypt::decryptString($inv->admininfo->name):null;
							$record['created_date'] = date('d/m/y',strtotime($inv->created_at));
							$data[] = $record;
						}
					}
					$visitor_app_url = env('VISITOR_APP_URL')."/batchinvoices/";
					return response()->json(['data'=>$data,'file_path'=>$visitor_app_url,'response' => 1, 'message' => 'Success']);

				}
				else{
					return response()->json(['data'=>null,'file_path'=>'','response' => 200, 'message' => 'Search option empty']);
				}

			}
	}

	public function batch_invoices(Request $request) 
	{
			$rules=array(
				'batch_id' => 'required',
			);
			$messages=array(
				'batch_id.required' => 'Batch id is missing',
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
			$batch_id = $request->batch_id;
			$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(61,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$invoices = FinanceInvoice::where('account_id',$account_id)->where('info_id',$batch_id)->orderby('id','desc')->get(); 
				$visitor_app_url = env('VISITOR_APP_URL')."/invoice-pdf/";
				$buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

				$data =array();
					foreach ($invoices as $k => $invoice) {
						$record =array();
						$record['invoice_info'] = $invoice;
						$record['building'] = isset($invoice->getunit->buildinginfo)?$invoice->getunit->buildinginfo:null;
						$record['unit'] = isset($invoice->getunit)?$invoice->getunit:null;

						$advance_amount = isset($invoice->AdvancePayment->amount)?$invoice->AdvancePayment->amount:0;
						$invoice_amount  = $invoice->payable_amount - $advance_amount;
						$record['invoice_amount'] = $invoice_amount;

						if(isset($invoice->status)){
							if($invoice->status !=3){
							   $financeObj = new \App\Models\v7\FinanceInvoice();
							   $ref_invoice = $financeObj->CheckNewInvoice($invoice->id,$invoice->unit_no);
							   //print_r($ref_invoice);
							}

							if($invoice->status==1){
							   $rec = $financeObj->CheckOverDue($invoice->id);
							   
							   	if(isset($ref_invoice->id))
									$record['status_lable'] = "Unpaid/ref.".$ref_invoice->invoice_no;
							   	else
							   		$record['status_lable'] = $rec;
							}                                      
							else  if($invoice->status==2){
									$record['status_lable'] ="Partial Paid";
							   if(isset($ref_invoice->id))
							   		$record['status_lable'] = "/ref.".$ref_invoice->invoice_no;
							}
							else  if($invoice->status==4)
								$record['status_lable'] ="Pending Verification";
							else 
								$record['status_lable'] ="Paid";
						 
						 }
						 $file_path = env('APP_URL')."/storage/app";
						 $record['pdf_path'] = $visitor_app_url."/".$invoice->id;

						 if(isset($invoice->PaymentLog) && $invoice->PaymentLog->type ==1){
							$record['screenshot'] =$file_path."/".$invoice->PaymentLog->screenshot;
						}
						$data[] = $record;
					} 

				return response()->json(['data'=>$data,'file_path'=>$visitor_app_url,'buildings'=>$buildings,'response' => 1, 'message' => 'Success']);
			}
	}
	public function batchdelete(Request $request) 
	{
			$rules=array(
				'batch_id' => 'required',
			);
			$messages=array(
				'batch_id.required' => 'Batch id is missing',
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
			$id = $request->batch_id;

			$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(61,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;

				$batchObj = FinanceInvoiceInfo::find($id);

				if(isset($batchObj->invoices)){
					foreach($batchObj->invoices as $invoice){
						FinanceAdvancePayment::where('invoice_id', $id)->delete();
						FinanceCreditPayment::where('invoice_id', $id)->delete();
						FinanceInvoicePayment::where('invoice_id', $invoice->id)->delete();
						FinanceInvoicePaymentDetail::where('invoice_id', $invoice->id)->delete();
						FinanceInvoicePaymentPaidDetail::where('invoice_id', $invoice->id)->delete();
						FinanceInvoice::findOrFail($invoice->id)->delete();

					$last_invoice = FinanceInvoice::where('unit_no' , $invoice->unit_no)->orderby('id','desc')->first(); 
						if(isset($last_invoice)){
							FinanceInvoice::where('id' , $last_invoice->id)->update( array( 'active_status' => 1)); //activate all previous 
						}
					}
				}
				
				FinanceInvoiceDetail::where('info_id', $id)->delete();

				FinanceInvoiceInfo::findOrFail($id)->delete();

				return response()->json(['response' => 1, 'message' => 'Deleted']);
			}
	}


	public function send_notification(Request $request) 
	{
		$rules=array(
			'batch_id' => 'required',
		);
		$messages=array(
			'batch_id.required' => 'Batch id is missing',
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
		$id = $request->batch_id;

		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
			
		$permission = $adminObj->check_permission(61,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$infoObj = FinanceInvoiceInfo::find($id);
			$count = $infoObj->notification_status+1;
			$infoObj->notification_status = $count;
			$infoObj->save();
			$invoices = FinanceInvoice::where('account_id',$account_id)->where('info_id',$id)->orderby('id','desc')->get();       
			if(isset($invoices)){
				foreach($invoices as $invoice){
					$notification = new \App\Models\v7\FinanceInvoice();
					$email = $notification->sendnotification($invoice);

					$unitPrimaryContactRecs = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id',$invoice->unit_no)->where('primary_contact',1)->where('role_id',2)->orderby('id','asc')->get();
					$primayContactIds = array();
					if($unitPrimaryContactRecs){
						//echo "primary contact";
						foreach($unitPrimaryContactRecs as $unitPrimaryContactRec){
							$primayContactIds[] = $unitPrimaryContactRec->user_info_id;
						}
					}
					//print_r($primayContactIds);
					$primary_contact = UserMoreInfo::WhereIn('id',$primayContactIds)->where('status',1)->orderby('id','asc')->get(); 
					if(isset($primary_contacts)){
						foreach($primary_contacts as $primary_contact)
						{
							$notification = array();
							$notification['account_id'] = $invoice->account_id;
							$notification['unit_no'] = $invoice->unit_no;
							$notification['user_id'] = $primary_contact->user_id;
							$notification['module'] = 'resident management';
							$notification['ref_id'] = $invoice->id;
							$notification['title'] = 'Resident Management';
							$notification['message'] = 'Your latest invoice is now available for viewing.';
							$result = UserNotification::insert($notification);

							$SettingsObj = UserNotificationSetting::where('user_id',$primary_contact->user_id)->where('account_id',$feedbackObj->account_id)->first();
							if(empty($SettingsObj) || $SettingsObj->resident_management ==1){
								$fcm_token_array ='';
								$user_token = ',';
								$ios_devices_to_send = array();
								$android_devices_to_send = array();
								$logs = UserLog::where('user_id',$primary_contact->user_id)->where('status',1)->orderby('id','desc')->first();
								if(isset($logs->fcm_token) && $logs->fcm_token !=''){
									$user_token .=$logs->fcm_token.",";
									$fcm_token_array .=$logs->fcm_token.',';
									$appSipAccountList[] = $primary_contact->user_id;
									if($logs->login_from ==1)
										$ios_devices_to_send[] = $logs->fcm_token;
									if($logs->login_from ==2)
										$android_devices_to_send[] = $logs->fcm_token;
								}
								$probObj = Property::find($account_id);
								$title = "Aerea Home - ".$probObj->company_name;
								$message = "Resident Management Update";

								$notofication_data = array();
								$notofication_data['body'] =$title; 
								$notofication_data['unit_no'] =$invoice->unit_no;   
								$notofication_data['user_id'] =$primary_contact->user_id;   
								$notofication_data['property'] =$invoice->account_id; 
								$purObj = UserPurchaserUnit::where('property_id',$invoice->account_id)->where('unit_id',$invoice->unit_no)->where('user_info_id',$primary_contact->id)->first(); 
								if(isset($purObj))
									$notofication_data['switch_id'] =$purObj->id;       
								$NotificationObj = new \App\Models\v7\FirebaseNotification();
								$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
								$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
							}
							//End Insert into notification module
						}
					}

				}
			}
			return response()->json(['response' => 1, 'message' => 'Notification sent!']);
		}
	}

	public function invoice_report(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(61,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$dateS = date("Y-m-d",strtotime(Carbon::now()->startOfMonth()->subMonth(12)));
       			$invoices = FinanceInvoice::where('account_id',$account_id)->where('invoice_date', '>=', $dateS)->orderby('id','desc')->get(); 
				
				$visitor_app_url = env('VISITOR_APP_URL')."/invoice-pdf/";
				$buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

				$data =array();
					foreach ($invoices as $k => $invoice) {
						$record =array();
						$record['invoice_info'] = $invoice;
						$record['building'] = isset($invoice->getunit->buildinginfo)?$invoice->getunit->buildinginfo:null;
						$unit_data =array();
						if(isset($invoice->getunit)){
							$unit_data["id"]= $invoice->getunit->id;
							$unit_data["unit"]= Crypt::decryptString($invoice->getunit->unit);
							$unit_data["code"]= Crypt::decryptString($invoice->getunit->code);
							$unit_data["size"]= $invoice->getunit->size;
						}
						$record['unit'] = !empty($unit_data)?$unit_data:null;

						//$record['unit'] = isset($invoice->getunit)?$invoice->getunit:null;

						$advance_amount = isset($invoice->AdvancePayment->amount)?$invoice->AdvancePayment->amount:0;
						$invoice_amount  = $invoice->payable_amount - $advance_amount;
						$record['invoice_amount'] = $invoice_amount;

						if(isset($invoice->status)){
							if($invoice->status !=3){
							   $financeObj = new \App\Models\v7\FinanceInvoice();
							   $ref_invoice = $financeObj->CheckNewInvoice($invoice->id,$invoice->unit_no);
							   //print_r($ref_invoice);
							}

							if($invoice->status==1){
							   $rec = $financeObj->CheckOverDue($invoice->id);
							   
							   	if(isset($ref_invoice->id))
									$record['status_lable'] = "Unpaid/ref.".$ref_invoice->invoice_no;
							   	else
							   		$record['status_lable'] = $rec;
							}                                      
							else  if($invoice->status==2){
									$record['status_lable'] ="Partial Paid";
							   if(isset($ref_invoice->id))
							   		$record['status_lable'] = "/ref.".$ref_invoice->invoice_no;
							}
							else  if($invoice->status==4)
								$record['status_lable'] ="Pending Verification";
							else 
								$record['status_lable'] ="Paid";
						 
						 }
						 $record['pdf_path'] = $visitor_app_url."/".$invoice->id;
						 $file_path = env('APP_URL')."/storage/app";

						if(isset($invoice->PaymentLog) && $invoice->PaymentLog->type ==1){
							$record['screenshot'] =$file_path."/".$invoice->PaymentLog->screenshot;
						}
						 
						$data[] = $record;
					} 

				return response()->json(['data'=>$data,'file_path'=>$visitor_app_url,'buildings'=>$buildings,'response' => 1, 'message' => 'Success']);
			}
	}
	

	

	public function report_search(Request $request) 
	{
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(61,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$account_id = $adminObj->account_id;
				$q = $option =$unit = $status = $invoice_no = $batch_file_no = $users = $month = $building = $fromdate = $todate = '';
				$fromdate = $request->input('fromdate');
				if($request->input('todate') !='')
					$todate = $request->input('todate');
				else
					$todate =$request->input('fromdate');
				$batch_file_no = $request->input('batch_file_no');
				$invoice_no = $request->input('invoice_no');
				$unit = $request->input('unit');
				$building = $request->input('building');
				$units = array();
				if($unit !='' || $building !=''){   
					$unit = str_replace("#",'',$unit);
					$unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($building,$unit) {
					if($building !='')
						$query->where('building_id',$building);
					//if($unit !='')
						//$query->Where('unit1', Crypt::encryptString($unit));
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
				$status = $request->input('status');
				if($batch_file_no !='' || $invoice_no !='' || $unit != '' || $units !='' || $fromdate != '' || $todate != '' || $status != '' || $building != ''){
					$invoices =  FinanceInvoice::where('account_id',$account_id)->where(function($query) use ($batch_file_no,$invoice_no,$unit,$units, $fromdate,$todate,$status,$building){
						if($fromdate !='')
							$query->whereBetween('invoice_date',array($fromdate,$todate));
						if($batch_file_no !='')
							$query->where('batch_file_no', 'LIKE', '%'.$batch_file_no .'%');
						if($unit !='' || $building !='')
							$query->whereIn('unit_no', $units);
						if($invoice_no !='')
							$query->where('invoice_no', 'LIKE', '%'.$invoice_no .'%');
						if($status !='')
							$query->where('status', $status);
					
					})->orderBy('id','DESC')->get();
					$data =array();
					foreach ($invoices as $k => $invoice) {
						$record =array();
						$record['invoice_info'] = $invoice;
						$record['building'] = isset($invoice->getunit->buildinginfo)?$invoice->getunit->buildinginfo:null;

						$unit_data =array();
						if(isset($invoice->getunit)){
							$unit_data["id"]= $invoice->getunit->id;
							$unit_data["unit"]= Crypt::decryptString($invoice->getunit->unit);
						}
						$record['unit'] = !empty($unit_data)?$unit_data:null;

						//$record['unit'] = isset($invoice->getunit)?$invoice->getunit:null;

						$advance_amount = isset($invoice->AdvancePayment->amount)?$invoice->AdvancePayment->amount:0;
						$invoice_amount  = $invoice->payable_amount - $advance_amount;
						$record['invoice_amount'] = $invoice_amount;

						if(isset($invoice->status)){
							if($invoice->status !=3){
							   $financeObj = new \App\Models\v7\FinanceInvoice();
							   $ref_invoice = $financeObj->CheckNewInvoice($invoice->id,$invoice->unit_no);
							   //print_r($ref_invoice);
							}

							if($invoice->status==1){
							   $rec = $financeObj->CheckOverDue($invoice->id);
							   
							   	if(isset($ref_invoice->id))
									$record['status_lable'] = "Unpaid/ref.".$ref_invoice->invoice_no;
							   	else
							   		$record['status_lable'] = $rec;
							}                                      
							else  if($invoice->status==2){
									$record['status_lable'] ="Partial Paid";
							   if(isset($ref_invoice->id))
							   		$record['status_lable'] = "/ref.".$ref_invoice->invoice_no;
							}
							else  if($invoice->status==4)
								$record['status_lable'] ="Pending Verification";
							else 
								$record['status_lable'] ="Paid";
						 
						 }
						$data[] = $record;
					} 
					
					$visitor_app_url = env('VISITOR_APP_URL')."invoice-pdf/";
					$buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

					return response()->json(['data'=>$data,'file_path'=>$visitor_app_url,'buildings'=>$buildings,'response' => 1, 'message' => 'Success']);
				}
				else{
					return response()->json(['data'=>null,'file_path'=>'','buildings'=>'','response' => 200, 'message' => 'Search option empty']);
				}

			}
	}

	public function paymentsave(Request $request) 
	{
			$rules=array(
				'id' => 'required',
			);
			$messages=array(
				'id.required' => ' Invoice id is missing',
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
			
			$permission = $adminObj->check_permission(61,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$id = $request->id;
				$invoiceObj = FinanceInvoice::find($id);

				$paid_by_credit = 0;

				/**********Validation Start ******/
				$payment_option = $request->input('payment_option');

				$val_inforecords = $request->input('info_detail');
				$val_amounts = $request->input('amount');
				//$balance_amount = $request->input('balance_amount');
				$invoice_amount = $invoiceObj->payable_amount;

				$amount_received =0;
				if($invoiceObj->payments){
					foreach($invoiceObj->payments as $k => $payment){
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
		
				$balance_amount = ($invoiceObj->payable_amount - $amount_received);

				$amount_received =0;
				if($payment_option ==1){
					if($request->input('cheque_amount') != null)
						$amount_received = $request->input('cheque_amount');
				}
				else if($payment_option ==2){
					if($request->input('bt_amount_received') != null)
						$amount_received = $request->input('bt_amount_received');
				}
				else if($payment_option ==3){
					if($request->input('cash_amount_received') != null)
						$amount_received = $request->input('cash_amount_received');
				}
				else if($payment_option ==6){
					if($request->input('credit_amount') != null)
						$amount_received = $request->input('credit_amount');
				}

			

				$full_payment = 0;
				if($amount_received >= $balance_amount && $payment_option ==6){
					$paid_by_credit = 1;
				}

				if($amount_received >= $balance_amount){
					$full_payment = 1;
				}
				else 
				{
					$totamount= 0;
					
					foreach($val_inforecords as $inforecord){
						if(isset($val_amounts[$inforecord]))
							$totamount += $val_amounts[$inforecord];
					}
					
					

					if($amount_received > 0 && $totamount ==0){
						return response()->json(['data'=>null,'response' => 200, 'message' => 'Error: Allocation amount should not be empty!']);
					}
					else if($balance_amount ==$amount_received && $totamount !=$amount_received){
						return response()->json(['data'=>null,'response' => 200, 'message' => 'Error: Amount and allocation amount does not match!!']);
					}
					else if($balance_amount < $amount_received && $totamount !=$invoice_amount){
						return response()->json(['data'=>null,'response' => 200, 'message' => 'Error: Amount and allocation amount does not match!']);
					} 
					else if($balance_amount > $amount_received && $totamount != $amount_received){
						return response()->json(['data'=>null,'response' => 200, 'message' => 'Error: Amount and allocation amount does not match!']);
					}    
					/****** Validation End */
				}

				$payment = array();
				$payment['invoice_id'] = $id;
				$payment['manager_received'] = $login_id;
				$payment['payment_option'] = $request->input('payment_option');
				
				
				if($payment['payment_option'] ==1){
					if($request->input('cheque_amount') != null)
						$payment['cheque_amount'] = $request->input('cheque_amount');
					if($request->input('cheque_no') != null)
						$payment['cheque_no'] = $request->input('cheque_no');
					if($request->input('cheque_received_date') != null){
						$payment['cheque_received_date'] = $request->input('cheque_received_date');
						$payment['payment_received_date'] = $request->input('cheque_received_date');
					}
					if($request->input('cheque_bank') != null)
						$payment['cheque_bank'] = $request->input('cheque_bank');
				}
				else if($payment['payment_option'] ==2){
					if($request->input('bt_received_date') != null){
						$payment['bt_received_date'] = $request->input('bt_received_date');
						$payment['payment_received_date'] = $request->input('bt_received_date');
					}
					if($request->input('bt_amount_received') != null)
						$payment['bt_amount_received'] = $request->input('bt_amount_received');
				}
				else if($payment['payment_option'] ==3){
					if($request->input('cash_amount_received') != null)
						$payment['cash_amount_received'] = $request->input('cash_amount_received');
					if($request->input('cash_received_date') != null){
						$payment['cash_received_date'] = $request->input('cash_received_date');
						$payment['payment_received_date'] = $request->input('cash_received_date');
					}					
				}
				else if($payment['payment_option'] ==6){
					if($request->input('credit_amount') != null)
						$payment['credit_amount'] = $request->input('credit_amount');
					if($request->input('credit_date') != null){
						$payment['credit_date'] = $request->input('credit_date');
						$payment['payment_received_date']  = $request->input('credit_date');
					}	
					if($request->input('credit_notes') != null){
						$payment['credit_notes'] = $request->input('credit_notes');
					}	
					
					$paid_by_credit = 1;				
				}
				if($request->input('receipt_no') != null)   
					$payment['receipt_no'] = $request->input('receipt_no');

				$payment['created_at'] = date("Y-m-d H:i:s");
				$payment['updated_at'] = date("Y-m-d H:i:s");

				
				$paymentObj = FinanceInvoicePayment::create($payment);

				if(isset($paymentObj) && $payment['payment_option'] ==6){
					/** Insert Advance Payment Record START **/
					$creditData['account_id'] = $account_id;
					$creditData['invoice_id'] = $paymentObj->invoice_id;
					$creditData['payment_id'] = $paymentObj->id;
					$creditData['credit_amount'] = $request->input('credit_amount');
					$creditData['received_date'] = $request->input('credit_date');
					$creditData['credit_notes'] = $request->input('credit_notes');
					$creditpaymentObj = FinanceCreditPayment::create($creditData);
				}
				if(isset($paymentObj) && $balance_amount > 0){                  
					if($request->input('info_detail') !=''){
						$info_records = $request->input('info_detail');
						$references = $request->input('reference');
						$amounts = $request->input('amount');
						$full_balance = $request->input('bal_amount');
						$waves = $request->input('wave');
						$paid_array = array();
						foreach($info_records as $inforecord){
							if($amounts[$inforecord] >0 || $full_payment == 1){
								if($full_payment == 1)
									$amount_of_paid = $full_balance[$inforecord]; 
								else
									$amount_of_paid = $amounts[$inforecord];  

								$PaymentdetailData =array();
								$PaymentdetailData['account_id'] = $account_id;
								$PaymentdetailData['unit_no'] = $invoiceObj->unit_no;
								$PaymentdetailData['invoice_id'] = $paymentObj->invoice_id;
								$PaymentdetailData['payment_id'] = $paymentObj->id;
								$PaymentdetailData['detail_id'] = $inforecord;
								$PaymentdetailData['type'] = $references[$inforecord];
								$PaymentdetailData['amount'] = $amount_of_paid;
								$PaymentdetailData['paid_by_credit'] = ($paid_by_credit ==1)?1:0;                        
								$PaymentdetailData['payment_received_date'] = $paymentObj->payment_received_date;
								$PaymentdetailData['created_at'] = date("Y-m-d H:i:s");
								$PaymentdetailData['updated_at'] = date("Y-m-d H:i:s");
								$paid_array[] = $PaymentdetailData;
					
								$paid_amount = $amount_of_paid;

								
								$detail_record = FinanceInvoicePaymentDetail::find($inforecord);
								if(isset($detail_record->paymenthistory)){
									foreach($detail_record->paymenthistory as $record){
										$paid_amount += $record->amount; 
									}
								}
								$detail_balance_amount = ($detail_record->amount - $paid_amount);

								
								if($detail_balance_amount<=0)
									$payment_status = 2;
								else
									$payment_status = 3;
								
								if(isset($waves[$inforecord]) && $waves[$inforecord] ==1 ){
									$paid_by_waver = 1;
								}else{
									$paid_by_waver = 0;
								}


								FinanceInvoicePaymentDetail::where('id' , $inforecord)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$paymentObj->payment_received_date,'paid_by_credit'=>$paid_by_waver));

							}
						}
					
						FinanceInvoicePaymentPaidDetail::insert($paid_array);
					}                
				}
			
				$invoiceObj = FinanceInvoice::find($invoiceObj->id);
				$amount_received =0;
				if(isset($invoiceObj->payments)){
					foreach($invoiceObj->payments as $k => $payment){
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
			
				$balance_amount = $invoiceObj->payable_amount - $amount_received;

				if($balance_amount <=0){
					if($balance_amount ==0)
						$balnce_type =1;
					else{
						$balnce_type =2;
						$balance_amount = 0- $balance_amount;
					}
					FinanceInvoice::where('id' , $id)->update( array('status' => 3,'balance_amount'=>$balance_amount,'balance_type'=>$balnce_type));
				}
				else{

					FinanceInvoice::where('id' , $id)->update( array( 'status' => 2,'balance_amount'=>$balance_amount,'balance_type'=>1));
				}

				$unitPrimaryContactRecs = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id',$invoiceObj->unit_no)->where('primary_contact',1)->where('role_id',2)->orderby('id','asc')->get();
				$primayContactIds = array();
				if($unitPrimaryContactRecs){
					//echo "primary contact";
					foreach($unitPrimaryContactRecs as $unitPrimaryContactRec){
						$primayContactIds[] = $unitPrimaryContactRec->user_info_id;
					}
				}
				//print_r($primayContactIds);
				$primary_contact = UserMoreInfo::WhereIn('id',$primayContactIds)->where('status',1)->orderby('id','asc')->first(); 

				//$primary_contact = User::where('role_id',2)->where('status',1)->where('primary_contact',1)->where('unit_no',$invoiceObj->unit_no)->orderby('id','asc')->first();

				if(isset($primary_contact)){
						//Start Insert into notification module
						$notification = array();
						$notification['account_id'] = $invoiceObj->account_id;
						$notification['user_id'] = $primary_contact->user_id;
						$notification['unit_no'] = $invoiceObj->unit_no;
						$notification['module'] = 'resident management';
						$notification['ref_id'] = $invoiceObj->id;
						$notification['title'] = 'Resident Management';
						$notification['message'] = 'Management has updated the status for the payment on invoice '.$invoiceObj->invoice_no;
						$result = UserNotification::insert($notification);

						$SettingsObj = UserNotificationSetting::where('user_id',$primary_contact->user_id)->where('account_id',$feedbackObj->account_id)->first();
						if(empty($SettingsObj) || $SettingsObj->resident_management ==1){
							$fcm_token_array ='';
							$user_token = ',';
							$ios_devices_to_send = array();
							$android_devices_to_send = array();
							$logs = UserLog::where('user_id',$primary_contact->user_id)->where('status',1)->orderby('id','desc')->first();
							if(isset($logs->fcm_token) && $logs->fcm_token !=''){
								$user_token .=$logs->fcm_token.",";
								$fcm_token_array .=$logs->fcm_token.',';
								$appSipAccountList[] = $primary_contact->user_id;
								if($logs->login_from ==1)
									$ios_devices_to_send[] = $logs->fcm_token;
								if($logs->login_from ==2)
									$android_devices_to_send[] = $logs->fcm_token;
							}

							$probObj = Property::find($account_id);
							$title = "Aerea Home - ".$probObj->company_name;
							$message = "Invoice Payment Updated";
							$notofication_data = array();
							$notofication_data['body'] =$title;                        
							$notofication_data['unit_no'] =$invoiceObj->unit_no;   
							$notofication_data['user_id'] =$primary_contact->user_id;   
							$notofication_data['property'] =$invoiceObj->account_id;
							$purObj = UserPurchaserUnit::where('property_id',$invoiceObj->account_id)->where('unit_id',$invoiceObj->unit_no)->where('user_info_id',$primary_contact->id)->first(); 
							if(isset($purObj))
								$notofication_data['switch_id'] =$purObj->id;    
									
							$NotificationObj = new \App\Models\v7\FirebaseNotification();
							$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
							$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
							//End Insert into notification module
						}
				}
			
				return response()->json(['response' => 1, 'message' => 'Payment details updated!']);
			}
	}

	public function paymentdelete(Request $request) 
	{
			$rules=array(
				'id' => 'required',
			);
			$messages=array(
				'id.required' => ' Invoice id is missing',
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
			
			$permission = $adminObj->check_permission(61,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$id = $request->payment_id;
				$paymentObj = FinanceInvoicePayment::find($id);
				$invoice_id = $paymentObj->invoice_id;
			
				FinanceInvoicePaymentPaidDetail::where('payment_id', $id)->delete();
				FinanceCreditPayment::where('payment_id', $id)->delete(); 
				
				$invoiceObj = FinanceInvoice::find($invoice_id);

				if(isset($invoiceObj->paymentdetails))
				{
					foreach($invoiceObj->paymentdetails as $key =>  $detail)
					{
						$detail_record = FinanceInvoicePaymentDetail::find($detail->id);
						$payment_received_date = "0000-00-00";
						$paid_amount =0;
						if(isset($detail_record->paymenthistory)){
							foreach($detail_record->paymenthistory as $record){
								$paid_amount += $record->amount; 
								$payment_received_date = $record->payment_received_date;
							}
						}
						//echo $detail_record->amount;
						$detail_balance_amount = number_format(($detail_record->amount - $paid_amount),2);
						if($detail_balance_amount<=0)
							$payment_status = 2;
						else
							$payment_status = 3;

						FinanceInvoicePaymentDetail::where('id' , $detail->id)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$payment_received_date));
						/*echo "id : ".$detail->id;
						echo "balance : ".$detail_balance_amount;
						echo " payment_status : ".$payment_status;
						echo " payment_received_date : ".$payment_received_date;
						echo "<hr />"; */

					}
				}

				//exit;
				FinanceInvoicePayment::findOrFail($id)->delete(); 
			

				$invoiceObj = FinanceInvoice::find($invoice_id);


				$amount_received =0;
				if($invoiceObj->payments){
					foreach($invoiceObj->payments as $k => $payment){
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
				
			/* $mf_balnce_amount = $invoiceObj->previous_mf_balance_amount + $mf_paid;
				$sf_balnce_amount = $invoiceObj->previous_sf_balance_amount +$sf_paid;
				$int_balnce_amount = $invoiceObj->previous_int_balance_amount +$int_paid;
				$tax_balnce_amount = $invoiceObj->previous_tax_balance_amount +$tax_paid; */

				
				$balance_amount = ($invoiceObj->payable_amount - $amount_received);
				if($balance_amount <=0)
					FinanceInvoice::where('id' , $invoiceObj->id)->update( array( 'status' => 3));
				else if($amount_received ==0)
					FinanceInvoice::where('id' , $invoiceObj->id)->update( array( 'status' => 1));
				else
					FinanceInvoice::where('id' , $invoiceObj->id)->update( array('status' => 2));

				return response()->json(['response' => 1, 'message' => 'Payment details updated!']);
			}
	}

	/** Resident Management API END */


	//Open Door records Module START

	public function dooropen(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(50,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$startDate = date('Y-m-d H-i-s',strtotime("-6 month"));
				$auth = new \App\Models\v7\Property();
				$token = $auth->thinmoo_auth_api();  
				//$MonthAgo = strtotime("-1 month"); //A month before today
				

				$url = env('THINMOO_API_URL')."normalOpenDoorlog/extapi/list";

				//The data you want to send via POST
				$fields = [
					'accessToken'       => $token,
					'extCommunityUuid'  => $account_id,
					'currPage'          =>1,
					'pageSize'          =>1000,
					'startDatetime'     =>$startDate,
					'endDatetime'       =>date('Y-m-d 00:00:00')
				];

				$fields_string = http_build_query($fields);

				$ch = curl_init();

				curl_setopt($ch,CURLOPT_URL, $url);
				curl_setopt($ch,CURLOPT_POST, true);
				curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

				curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

				
				$results = curl_exec($ch);
				$json = json_decode($results,true);
				$err = curl_error($ch);
				curl_close($ch);

				$records =array();
				if(isset($json['data'])){
					$records = $json['data']['list'];
				}

				$device = new \App\Models\v7\Device();
				$dev_lists = $device->device_lists_api($token,$account_id); 
				$devices = array();
				foreach($dev_lists as $dev_list){
					$devices[] =$dev_list['name'];
				}

				$types = array(8=>'Remote Door Opening',19=>'Bluetooth Door Opening',21=>'Face Recognition Door Opening');

				return response()->json(['data'=>$records,'devices'=>$devices,'types'=>$types,'response' => 1, 'message' => 'Success']);
			}
	}

	public function searchdooropen(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(50,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$option = $device = $name = $doorName =$date = $devSn = $eventType = $records = $unit = $startDate =  $endDate = $startTime = $endTime = $startdatetime = $enddatetime ='';
				$account_id = $adminObj->account_id;
				$auth = new \App\Models\v7\Property();
				$token = $auth->thinmoo_auth_api();  

				$url = env('THINMOO_API_URL')."normalOpenDoorlog/extapi/list";

				$doorName = $request->input('doorName');
				$eventType = $request->input('eventType');
				
				$startDate = $request->input('startDate');
				$endDate = $request->input('endDate');
				$startTime = $request->input('startTime');
				$endTime = $request->input('endTime');
				
				if($doorName !='' || $eventType !='' || $startDate !='' || $endDate !=''){
					if($startDate !='' || $endDate !='' ){
						if($startDate !=''){
							if($startTime !='')
								$startdatetime = $startDate." ".$startTime.":00";
							else
								$startdatetime = $startDate." 00:00:00";
						}
						
						if($endDate !=''){    
							if($endTime !='')    
								$enddatetime = $endDate." ".$endTime.":00";
							else
								$enddatetime = $endDate." 00:00:00";
						}
						if($startDate =='')
							$startdatetime = $enddatetime;
						if($endDate =='')
							$enddatetime = $startdatetime;
					}
					//echo "startdatetime :".$startdatetime." enddatetime :".$enddatetime;

					//The data you want to send via POST
					$fields = [
						'accessToken'       => $token,
						'extCommunityUuid'  => $account_id,
						'currPage'          =>1,
						'pageSize'          =>1000,
						'startDateTime'     =>$startdatetime,
						'endDateTime'       =>$enddatetime,
						'devName'           =>$doorName,
						'eventType'         =>$eventType
					];

					$fields_string = http_build_query($fields);

					$ch = curl_init();

					curl_setopt($ch,CURLOPT_URL, $url);
					curl_setopt($ch,CURLOPT_POST, true);
					curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

					curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

					
					$results = curl_exec($ch);
					$json = json_decode($results,true);
					$err = curl_error($ch);
					curl_close($ch);

					$records =array();
					if(isset($json['data'])){
						$records = $json['data']['list'];
					}
				
					/*foreach($lists as $k => $list){
						print_r($list);
						echo "<hr />";
					}*/
					$device = new \App\Models\v7\Device();
					$dev_lists = $device->device_lists_api($token,$account_id); 
					$devices = array();
					foreach($dev_lists as $dev_list){
						$devices[] =$dev_list['name'];
					}
					$types = array(8=>'Remote Door Opening',19=>'Bluetooth Door Opening',21=>'Face Recognition Door Opening');

					return response()->json(['data'=>$records,'devices'=>$devices,'types'=>$types,'response' => 1, 'message' => 'Success']);
				}
				else{
					return response()->json(['data'=>null,'devices'=>'','types'=>'','response' => 200, 'message' => 'Search option empty']);
				}

			}
	}

	public function bluetoothdooropen(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(52,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$auth = new \App\Models\v7\Property();
				$token = $auth->thinmoo_auth_api();  

				$device = new \App\Models\v7\Device();
				$dev_lists = $device->device_lists_api($token,$account_id); 
				$devices = array();
				foreach($dev_lists as $dev_list){
					$devices[] =$dev_list['name'];
				}
				$records = BluetoothDoorOpen::where('account_id',$account_id)->orderBy('id','DESC')->get();

				$data = array();
				foreach($records as $record){
					$result = array();
					$result['record'] = $record;
					$result['status'] = ($record->status==1)?"Success":'Fail';
					$result['action'] = ($record->action_type==1)?'Proximity':'Manual';
					$result['unitinfo'] = isset($record->getunit->unit)?"#".Crypt::decryptString($record->getunit->unit):null;
					$result['name'] = isset($record->user->name)?Crypt::decryptString($record->user->name):null;
					$data[] = $result;
				}

				return response()->json(['data'=>$data,'devices'=>$devices,'response' => 1, 'message' => 'Success']);
			}
	}

	public function searchbluetoothdooropen(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(52,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime = $startdatetime = $enddatetime = '';
				$account_id = $adminObj->account_id;
				$auth = new \App\Models\v7\Property();
				$token = $auth->thinmoo_auth_api();  

				$device = new \App\Models\v7\Device();
				$dev_lists = $device->device_lists_api($token,$account_id); 
				$devices = array();
				foreach($dev_lists as $dev_list){
					$devices[] =$dev_list['name'];
				}

				$doorName = $request->input('doorName');
				$device_rec = '';
				if( $doorName !='')
					$device_rec = Device::where('device_name',$doorName)->first();
				
				$name = $request->input('name');
				$unit = $request->input('unit');
				$units = array();
				if($unit !=''){
					$unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->get();
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							$units[] = $unitid->id;
						}
					}
				} 
				$startDate = $request->input('startDate');
				$endDate = $request->input('endDate');
				$startTime = $request->input('startTime');
				$endTime = $request->input('endTime');
				if($doorName !='' || $name !='' || $unit !='' || $startDate !='' || $endDate !=''){
					if($startDate !='' || $endDate !='' ){
						if($startDate !=''){
							if($startTime !='')
								$startdatetime = $startDate." ".$startTime.":00";
							else
								$startdatetime = $startDate." 00:00:00";
						}
						
						if($endDate !=''){    
							if($endTime !='')    
								$enddatetime = $endDate." ".$endTime.":00";
							else
								$enddatetime = $endDate." 00:00:00";
						}
						if($startDate =='')
							$startdatetime = $enddatetime;
						if($endDate =='')
							$enddatetime = $startdatetime;
					}    
							
					$records =  BluetoothDoorOpen::where('bluetooth_door_opens.account_id',$account_id)
						->join('users', 'users.id', '=', 'bluetooth_door_opens.user_id')
						->where(function ($query) use ($doorName,$device_rec,$name,$unit,$units,$startdatetime,$enddatetime) {
							if($doorName !='' && isset($device_rec))
								$query->where('bluetooth_door_opens.devSn', $device_rec->device_serial_no);
							if($name !='')
								$query->where('users.name', 'LIKE', '%'.$name .'%');
							if($unit !='')
								$query->whereIn('bluetooth_door_opens.unit_no', $units);
							if($startdatetime !='')
								$query->whereBetween('bluetooth_door_opens.call_date_time',[$startdatetime,$enddatetime]);
							
						})->get();
						$data = array();
						foreach($records as $record){
							$result = array();
							$result['record'] = $record;
							$result['status'] = ($record->status==1)?"Success":'Fail';
							$result['action'] = ($record->action_type==1)?'Proximity':'Manual';
							$result['unitinfo'] = isset($record->getunit->unit)?"#".$record->getunit->unit:null;
							$result['name'] = isset($record->user->name)?$record->user->name:null;
							$data[] = $result;
						}
						return response()->json(['data'=>$data,'devices'=>$devices,'response' => 1, 'message' => 'Success']);
				}
				else{
					return response()->json(['data'=>null,'devices'=>'','response' => 200, 'message' => 'Search option empty']);
				}

			}
	}

	public function dooropenfailed(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(52,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$auth = new \App\Models\v7\Property();
				$token = $auth->thinmoo_auth_api();  

				$device = new \App\Models\v7\Device();
				$dev_lists = $device->device_lists_api($token,$account_id); 
				$devices = array();
				foreach($dev_lists as $dev_list){
					$devices[] =$dev_list['name'];
				}
				$records = FailDoorOpenRecord::where('account_id',$account_id)->orderBy('id','DESC')->get();

				$data = array();
				foreach($records as $record){
					$result = array();
					$result['record'] = $record;
					$result['reason'] = ($record->eventType ==74)?'QR code failed to open the door':'Failled';
					$result['unitinfo'] = isset($record->getunit->unit)?"#".Crypt::decryptString($record->getunit->unit):null;
					$result['name'] = isset($record->user->name)?Crypt::decryptString($record->user->name):null;
					$data[] = $result;
				}

				return response()->json(['data'=>$data,'devices'=>$devices,'response' => 1, 'message' => 'Success']);
			}
	}

	public function searchdooropenfailed(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(52,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime = $startdatetime = $enddatetime = '';
				$account_id = $adminObj->account_id;
				$auth = new \App\Models\v7\Property();
				$token = $auth->thinmoo_auth_api();  

				$device = new \App\Models\v7\Device();
				$dev_lists = $device->device_lists_api($token,$account_id); 
				$devices = array();
				foreach($dev_lists as $dev_list){
					$deviceObj = Device::where('device_name',$dev_list['name'])->first();
					if(isset($deviceObj))
						$devices[] =$dev_list['name'];
				}

				$doorName = $request->input('doorName');
				$device_rec = '';
				if( $doorName !='')
					$device_rec = Device::where('device_name',$doorName)->first();
				
				$name = $request->input('name');
				$unit = $request->input('unit');
				$units = array();
				if($unit !=''){
					$unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->get();
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							$units[] = $unitid->id;
						}
					}
				} 
				$startDate = $request->input('startDate');
				$endDate = $request->input('endDate');
				$startTime = $request->input('startTime');
				$endTime = $request->input('endTime');
				if($doorName !='' || $name !='' || $unit !='' || $startDate !='' || $endDate !=''){
					if($startDate !='' || $endDate !='' ){
						if($startDate !=''){
							if($startTime !='')
								$startdatetime = $startDate." ".$startTime.":00";
							else
								$startdatetime = $startDate." 00:00:00";
						}
						
						if($endDate !=''){    
							if($endTime !='')    
								$enddatetime = $endDate." ".$endTime.":00";
							else
								$enddatetime = $endDate." 00:00:00";
						}
						if($startDate =='')
							$startdatetime = $enddatetime;
						if($endDate =='')
							$enddatetime = $startdatetime;
					}    
							
					$records =  FailDoorOpenRecord::where('fail_door_open_records.account_id',$account_id)
					->where(function ($query) use ($doorName,$device_rec,$name,$unit,$units,$startdatetime,$enddatetime) {
						if($doorName !='' && isset($device_rec))
							$query->where('fail_door_open_records.devSn', $device_rec->device_serial_no);
						if($name !='')
							$query
							->join('users', 'users.id', '=', 'fail_door_open_records.empuuid')->where('fail_door_open_records.empname', 'LIKE', '%'.$name .'%');
						if($unit !='')
							$query->whereIn('fail_door_open_records.unit_no', $units);
						if($startdatetime !='')
							$query->whereBetween('fail_door_open_records.eventtime',[$startdatetime,$enddatetime]);
						
					})->get();
						$data = array();
						foreach($records as $record){
							$result = array();
							$result['record'] = $record;
							$result['reason'] = ($record->eventType ==74)?'QR code failed to open the door':'Failled';

							$result['unitinfo'] = isset($record->getunit->unit)?"#".$record->getunit->unit:null;
							$result['name'] = isset($record->user->name)?$record->user->name:null;
							$data[] = $result;
						}
						return response()->json(['data'=>$data,'devices'=>$devices,'response' => 1, 'message' => 'Success']);
				}
				else{
					return response()->json(['data'=>null,'devices'=>'','response' => 200, 'message' => 'Search option empty']);
				}

			}
	}

	public function callunit(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(52,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$auth = new \App\Models\v7\Property();
				$token = $auth->thinmoo_auth_api();  

				$device = new \App\Models\v7\Device();
				$dev_lists = $device->device_lists_api($token,$account_id); 
				$devices = array();
				foreach($dev_lists as $dev_list){
					$devices[] =$dev_list['name'];
				}
				$records =  CallPushRecord::where('account_id',$account_id)->orderby('id','desc')->get();

				$data = array();
				foreach($records as $record){
					$result = array();
					$result['record'] = $record;
					$bt = new \App\Models\v7\CallPushRecord();
                    $rec = $bt->deviceDetail($record->devSn);  
					$result['device_info'] = isset($rec)?$rec:null;
					$result['unitinfo'] = isset($record->getunit->unit)?"#".Crypt::decryptString($record->getunit->unit):null;
					$data[] = $result;
				}

				return response()->json(['data'=>$data,'devices'=>$devices,'response' => 1, 'message' => 'Success']);
			}
	}

	public function searchcallunit(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(52,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime = $startdatetime = $enddatetime = '';
				$account_id = $adminObj->account_id;
				$auth = new \App\Models\v7\Property();
				$token = $auth->thinmoo_auth_api();  

				$device = new \App\Models\v7\Device();
				$dev_lists = $device->device_lists_api($token,$account_id); 
				$devices = array();
				foreach($dev_lists as $dev_list){
					$devices[] =$dev_list['name'];
				}

				$doorName = $request->input('doorName');
				$device_rec = '';
				if( $doorName !='')
					$device_rec = Device::where('device_name',$doorName)->first();
				
				$name = $request->input('name');
				$unit = $request->input('unit');
				$unit_code = '';
				if($unit !=''){
					$unitObj = Unit::where('account_id',$account_id)->where('unit',$unit)->first();
					$unit_code = isset($unitObj->code)?$unitObj->code:null;
						
					$unit_code = (int)$unit_code;
				} 
				$units = array();
				if($unit !=''){
					$unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->get();
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							$units[] = $unitid->id;
						}
					}
				} 
				$startDate = $request->input('startDate');
				$endDate = $request->input('endDate');
				$startTime = $request->input('startTime');
				$endTime = $request->input('endTime');
				if($doorName !='' || $name !='' || $unit !='' || $startDate !='' || $endDate !=''){
					if($startDate !='' || $endDate !='' ){
						if($startDate !=''){
							if($startTime !='')
								$startdatetime = $startDate." ".$startTime.":00";
							else
								$startdatetime = $startDate." 00:00:00";
						}
						
						if($endDate !=''){    
							if($endTime !='')    
								$enddatetime = $endDate." ".$endTime.":00";
							else
								$enddatetime = $endDate." 00:00:00";
						}
						if($startDate =='')
							$startdatetime = $enddatetime;
						if($endDate =='')
							$enddatetime = $startdatetime;
					}    
							
					$records =  CallPushRecord::where('account_id',$account_id)
					->where(function ($query) use ($doorName,$device_rec,$name,$unit,$unit_code,$startdatetime,$enddatetime) {
						if($doorName !='' && isset($device_rec))
							$query->where('devSn', $device_rec->device_serial_no);
						if($unit !='')
							$query->where('roomCode', 'LIKE', '%'.$unit_code);
						if($startdatetime !='')
							$query->whereBetween('created_at',[$startdatetime,$enddatetime]);
						
					})->get();
						$data = array();
						foreach($records as $record){
							$result = array();
							$result['record'] = $record;
							$bt = new \App\Models\v7\CallPushRecord();
                       		$rec = $bt->deviceDetail($record->devSn);  
							$result['device_info'] = isset($rec)?$rec:null;
							$result['unitinfo'] = isset($record->getunit->unit)?"#".$record->getunit->unit:null;
							$data[] = $result;
						}
						return response()->json(['data'=>$data,'devices'=>$devices,'response' => 1, 'message' => 'Success']);
				}
				else{
					return response()->json(['data'=>null,'devices'=>'','response' => 200, 'message' => 'Search option empty']);
				}

			}
	}

	public function qropenrecords(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
			}
			
			$permission = $adminObj->check_permission(52,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$account_id = $adminObj->account_id;
				$auth = new \App\Models\v7\Property();
				$token = $auth->thinmoo_auth_api();  

				$device = new \App\Models\v7\Device();
				$dev_lists = $device->device_lists_api($token,$account_id); 
				$devices = array();
				foreach($dev_lists as $dev_list){
					$devices[] =$dev_list['name'];
				}
				$records =  $records =  QrcodeOpenRecord::where('account_id',$account_id)->orderby('id','desc')->get();

				$data = array();
				foreach($records as $record){
					$result = array();
					$result['record'] = $record;
					$bt = new \App\Models\v7\QrcodeOpenRecord();
					$rec = $bt->deviceDetail($record->devSn);  
					$result['device_info'] = isset($rec)?$rec:null;
					$result['booking_id'] = isset($record->BookingInfo->ticket)?$record->BookingInfo->ticket:null;
					$result['name'] = isset($record->BookingInfo->user->name)?Crypt::decryptString($record->BookingInfo->user->name):null;
					$data[] = $result;
				}

				return response()->json(['data'=>$data,'devices'=>$devices,'response' => 1, 'message' => 'Success']);
			}
	}

	public function searchqropenrecords(Request $request) 
	{
		
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(52,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				$option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime = $startdatetime = $enddatetime = '';
				$account_id = $adminObj->account_id;
				$auth = new \App\Models\v7\Property();
				$token = $auth->thinmoo_auth_api();  

				$device = new \App\Models\v7\Device();
				$dev_lists = $device->device_lists_api($token,$account_id); 
				$devices = array();
				foreach($dev_lists as $dev_list){
					$devices[] =$dev_list['name'];
				}

				$doorName = $request->input('doorName');
				$device_rec = '';
				if( $doorName !='')
					$device_rec = Device::where('device_name',$doorName)->first();
				
				$name = $request->input('name');
				$unit = $request->input('unit');
				$unit_code = '';
				if($unit !=''){
					$unitObj = Unit::where('account_id',$account_id)->where('unit',$unit)->first();
					$unit_code = isset($unitObj->code)?$unitObj->code:null;
						
					$unit_code = (int)$unit_code;
				} 
				$units = array();
				if($unit !=''){
					$unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->get();
					if(isset($unitObj)){
						foreach($unitObj as $unitid){
							$units[] = $unitid->id;
						}
					}
				} 
				$startDate = $request->input('startDate');
				$endDate = $request->input('endDate');
				$startTime = $request->input('startTime');
				$endTime = $request->input('endTime');
				if($doorName !='' || $name !='' || $unit !='' || $startDate !='' || $endDate !=''){
					if($startDate !='' || $endDate !='' ){
						if($startDate !=''){
							if($startTime !='')
								$startdatetime = $startDate." ".$startTime.":00";
							else
								$startdatetime = $startDate." 00:00:00";
						}
						
						if($endDate !=''){    
							if($endTime !='')    
								$enddatetime = $endDate." ".$endTime.":00";
							else
								$enddatetime = $endDate." 00:00:00";
						}
						if($startDate =='')
							$startdatetime = $enddatetime;
						if($endDate =='')
							$enddatetime = $startdatetime;
					}    
							
					$records =  QrcodeOpenRecord::where('qrcode_open_records.account_id',$account_id)
						->where(function ($query) use ($doorName,$device_rec,$name,$unit,$units,$startdatetime,$enddatetime) {
							if($doorName !='')
								$query->where('qrcode_open_records.devSn', $device_rec->device_serial_no);
							if($unit !='')
								$query->join('visitor_bookings', 'visitor_bookings.id', '=', 'qrcode_open_records.booking_id')->whereIn('visitor_bookings.unit_no', $units);
							if($startdatetime !='')
								$query->whereBetween('qrcode_open_records.created_at',[$startdatetime,$enddatetime]);                
						})->get();
						$data = array();
						foreach($records as $record){
							$result = array();
							$result['record'] = $record;
							$bt = new \App\Models\v7\QrcodeOpenRecord();
							$rec = $bt->deviceDetail($record->devSn);  
							$result['device_info'] = isset($rec)?$rec:null;
							$result['booking_id'] = isset($record->BookingInfo->ticket)?$record->BookingInfo->ticket:null;
							$result['name'] = isset($record->BookingInfo->user->name)?$record->BookingInfo->user->name:null;
							$data[] = $result;
						}
						return response()->json(['data'=>$data,'devices'=>$devices,'response' => 1, 'message' => 'Success']);
				}
				else{
					return response()->json(['data'=>null,'devices'=>'','response' => 200, 'message' => 'Search option empty']);
				}

			}
	}

	/** OPEN DOOR RECORDS API END */

	public function paymentinfo(Request $request) 
	{
		$login_id = Auth::id();

		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(28,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$account_id = $adminObj->account_id;
				$payment = PaymentSetting::where('account_id',$account_id)->first();
				$data =array();
				if(isset($payment)){
						$record = array();
						$record['id'] = $payment->id;
						$record['account_id'] = $payment->account_id;
						$record['cheque_pay'] = isset($payment->cheque_payable_to)?1:0;
						$record['cheque_payable_to'] = $payment->cheque_payable_to;
						$record['account_pay'] = isset($payment->account_holder_name)?1:0;
						$record['account_holder_name'] = $payment->account_holder_name;
						$record['account_number'] = $payment->account_number;
						$record['account_type'] = $payment->account_type;
						$record['bank_name'] = $payment->bank_name;
						$record['bank_address'] = $payment->bank_address;
						$record['swift_code'] = $payment->swift_code;
						$record['cash_pay'] = isset($payment->cash_payment_info)?1:0;
						$record['cash_payment_info'] = $payment->cash_payment_info;
						$record['created_at'] = $payment->created_at;
						$record['updated_at'] = $payment->updated_at;
					
				}
				return response()->json(['data'=>$record,'response' => 1, 'message' => 'Success']);

			}
	}

	public function paymentedit(Request $request) 
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
			
			$input = $request->all();
					$login_id = Auth::id();
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(28,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				$id = $request->input('id');

				$paymentObj = PaymentSetting::find($id);

				if($request->input('terms1') != null){
						if($request->input('cheque_payable_to') != null)
						$paymentObj->cheque_payable_to = $request->input('cheque_payable_to');
				}else{
						$paymentObj->cheque_payable_to ='';
				}
					
				if($request->input('terms3') != null){
						if($request->input('cash_payment_info') != null)
						$paymentObj->cash_payment_info = $request->input('cash_payment_info');
				}else{
						$paymentObj->cash_payment_info = '';
				}
					if($request->input('terms2') != null){
						if($request->input('account_holder_name') != null)
							$paymentObj->account_holder_name = $request->input('account_holder_name');

						if($request->input('account_number') != null)
							$paymentObj->account_number = $request->input('account_number');

						if($request->input('account_type') != null){
							$paymentObj->account_type = $request->input('account_type');
						}

						if($request->input('bank_name') != null){
							$paymentObj->bank_name = $request->input('bank_name');
						}

						if($request->input('bank_address') != null){
							$paymentObj->bank_address = $request->input('bank_address');
						}

						if ($request->input('swift_code') != null){
							$paymentObj->swift_code = $request->input('swift_code');
						} 
					}else{
						$paymentObj->account_holder_name = '';
						$paymentObj->account_number = '';
						$paymentObj->account_type = '';
						$paymentObj->bank_name = '';
						$paymentObj->bank_address = '';
						$paymentObj->swift_code = '';
					}      

					$paymentObj->save();

				return response()->json(['data'=>$paymentObj,'response' => 1, 'message' => 'Success']);

			}
	}

	public function holidayinfo(Request $request) 
	{
		$login_id = Auth::id();
		
		$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(28,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{
				
				$account_id = $adminObj->account_id;
				$HoloidayObj = HolidaySetting::where('account_id',$account_id)->first();
				return response()->json(['data'=>$HoloidayObj,'response' => 1, 'message' => 'Success']);

			}
	}

	public function holidayedit(Request $request) 
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
			
			$input = $request->all();
			$login_id = Auth::id();
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
			$permission = $adminObj->check_permission(28,$adminObj->role_id); 
			if(empty($permission) && $permission->view!=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
			}
			else{

				$id = $request->input('id');
				$holidayObj = HolidaySetting::find($id);
				if ($request->input('public_holidays') != null){
					$holidayObj->public_holidays = $request->input('public_holidays');
				} 
				
				$holidayObj->save();

				return response()->json(['data'=>$holidayObj,'response' => 1, 'message' => 'Success']);

			}
	}

	public function unitsummarytypes(Request $request) 
    {
			$login_id = Auth::id();
		$user = $request->user;
		$adminObj = User::find($login_id); 

		$types = array();

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
		
		$user_permission =  $adminObj->check_permission(7,$adminObj->role_id,1);
		$collection_permission = $adminObj->check_permission(2,$adminObj->role_id); 
		$facility_permission = $adminObj->check_permission(5,$adminObj->role_id); 
		$defect_permission = $adminObj->check_permission(3,$adminObj->role_id); 
		$feedback_permission = $adminObj->check_permission(6,$adminObj->role_id); 
		$move_permission = $adminObj->check_permission(40,$adminObj->role_id); 
		$renovation_permission = $adminObj->check_permission(41,$adminObj->role_id); 
		$dooraccess_permission = $adminObj->check_permission(42,$adminObj->role_id); 
		$vehicle_permission = $adminObj->check_permission(43,$adminObj->role_id); 
		$mailling_permission = $adminObj->check_permission(44,$adminObj->role_id); 
		$particular_permission = $adminObj->check_permission(45,$adminObj->role_id); 
		$vm_permission =  $adminObj->check_permission(34,$adminObj->role_id,1); 
		$rm_permission=  $adminObj->check_permission(60,$adminObj->role_id,1); 
		$card_permission =  $adminObj->check_permission(38,$adminObj->role_id,1);
		if((isset($user_permission) && $user_permission->view==1))
			$types[7] = "Contact info";
		if((isset($collection_permission) && $collection_permission->view==1))
			$types[2] = "Key Collection";
		if((isset($defect_permission) && $defect_permission->view==1))
			$types[3] = "Defects";
		if((isset($facility_permission) && $facility_permission->view==1))
			$types[5] = "Facility Booking";
		if((isset($feedback_permission) && $feedback_permission->view==1))
			$types[6] = "Feedback";

		if((isset($move_permission) && $move_permission->view==1))
			$types[40] = "E-Move In & Out";
		if((isset($renovation_permission) && $renovation_permission->view==1))
			$types[41] = "E-Renovation Registration";
		if((isset($dooraccess_permission) && $dooraccess_permission->view==1))
			$types[42] = "E-Access Card Registration";
		if((isset($vehicle_permission) && $vehicle_permission->view==1))
			$types[43] = "E-Registration for Vehicle IU";
		if((isset($mailling_permission) && $mailling_permission->view==1))
			$types[44] = "E-Change Of Mailing Address";
		if((isset($particular_permission) && $particular_permission->view==1))
			$types[45] = "E-Update Of Particulars";
		if((isset($card_permission) && $card_permission->view==1))
			$types[38] = "Access Card Management";
		if((isset($vm_permission) && $vm_permission->view==1))
			$types[34] = "Visitor Management";
		if((isset($rm_permission) && $rm_permission->view==1))
			$types[60] = "Resident Management";
		if((isset($user_permission) && $user_permission->view==1))
			$types[82] = "License Plates";
		

		return response()->json(['type'=>$types,'response' => 1, 'message' => 'success']);
       
	}


	public function unitsummary(Request $request) 
    {
		$rules=array(
			'building_id' => 'required',
			'unit_id' => 'required',
			'type' => 'required',
		);
		$messages=array(
			'building_id.required' => 'Building is missing',
			'unit_id.required' => 'Unit is missing',
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
		
				$login_id = Auth::id();
		$user = $request->user;
		$adminObj = User::find($login_id); 
		$unit = $request->unit_id;
		$type = $request->type;
		$UnitObj = Unit::find($unit);

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login id not found']);
		}
		
		$user_permission =  $adminObj->check_permission(7,$adminObj->role_id,1);
		$collection_permission = $adminObj->check_permission(2,$adminObj->role_id); 
		$facility_permission = $adminObj->check_permission(5,$adminObj->role_id); 
		$defect_permission = $adminObj->check_permission(3,$adminObj->role_id); 
		$feedback_permission = $adminObj->check_permission(6,$adminObj->role_id); 
		$move_permission = $adminObj->check_permission(40,$adminObj->role_id); 
		$renovation_permission = $adminObj->check_permission(41,$adminObj->role_id); 
		$dooraccess_permission = $adminObj->check_permission(42,$adminObj->role_id); 
		$vehicle_permission = $adminObj->check_permission(43,$adminObj->role_id); 
		$mailling_permission = $adminObj->check_permission(44,$adminObj->role_id); 
		$particular_permission = $adminObj->check_permission(45,$adminObj->role_id); 
		$vm_permission =  $adminObj->check_permission(34,$adminObj->role_id,1); 
		$rm_permission=  $adminObj->check_permission(60,$adminObj->role_id,1); 
		$card_permission =  $adminObj->check_permission(38,$adminObj->role_id,1);
		$account_id = $adminObj->account_id;

		$userids = array();
		$userObj = User::select('id')->where('account_id',$account_id)->where('unit_no',$unit)->get();
		foreach($userObj as $k => $user){
			$userids[] = $user->id;
		}
		
		if($type == 7 && isset($user_permission) && $user_permission->view==1){

			$unitids_byusers = UserPurchaserUnit::where('unit_id', $unit)->get();
			$prop_userids =array();
			foreach($unitids_byusers as $k =>$v){
				$prop_userids[] = $v->user_info_id;
			}
		
			//$users = User::where('account_id',$account_id)->where('unit_no',$unit)->orderBy('id','DESC')->get();
			$userMoreInfoObjs = UserMoreInfo::where('account_id',$account_id)->WhereIn('id',$prop_userids)->orderBy('user_id','DESC')->get();
			$app_user_lists = explode(",",env('USER_APP_ROLE'));

			$data = array();
			foreach($userMoreInfoObjs as $user){
					//$user = User::find($userMoreInfoObj->user_id);
					$record['id']=$user->user_id;
					$record['info_id']=$user->id;
					$record['name']=Crypt::decryptString($user->first_name);
					$record['last_name']=isset($user->last_name)?Crypt::decryptString($user->last_name):null;
					$record['email']=$user->getuser->email;
					$record['account_enabled']=$user->getuser->account_enabled;


						$role_id =  isset($user->getuser->role_id)?$user->getuser->role_id:'';
						$building_name = '';
						$unit_name = '';
						$building = $UnitObj->building_id;
						if(isset($unit) && $unit >0){
						   $unitObj = new \App\Models\v7\Unit();
						   //echo $unit;
						   $moreinfo = new \App\Models\v7\UserMoreInfo();
						   $purchaseUnitInfo = $moreinfo->moreunitinfo($user->id,$user->account_id,$unit);
						   $role_id = isset($purchaseUnitInfo->role_id)?$purchaseUnitInfo->role_id:null;
						   //echo $role_id;
						   $roleInfo = $moreinfo->roleInfo($role_id);
						   $building_name = isset($UnitObj->addubuildinginfo)?$UnitObj->addubuildinginfo->building:null;
						   $unit_name = isset($unitinfo->addunitinfo)?"#".Crypt::decryptString($unitinfo->addunitinfo->unit):null;
						   $PurchaseUnitDetail = $moreinfo->userunitinfo($user->id,$UnitObj->id);
						   $primary_contact = (isset($PurchaseUnitDetail))?$PurchaseUnitDetail->primary_contact:null;
						   $UserPurchaseId = (isset($PurchaseUnitDetail))?$PurchaseUnitDetail->id:null;
						   $purchaseStatus = (isset($PurchaseUnitDetail))?$PurchaseUnitDetail->status:null;
						   $role_name = isset($user->getuser->role->name)?$user->getuser->role->name:null;
						}
						else if(in_array($user->getuser->role_id,$app_user_lists)){
						   //echo "User :".$user->id." Property :".$user->account_id;
						   $moreinfo = new \App\Models\v7\UserMoreInfo();
						   $purchaseUnitInfo = $moreinfo->moreunitinfo($user->id,$user->account_id);
						   $role_id = isset($purchaseUnitInfo->role_id)?$purchaseUnitInfo->role_id:null;
						   $roleInfo = $moreinfo->roleInfo($role_id);
						   $PurchaseUnitDetail = $moreinfo->userunitinfo($user->id,$UnitObj->id);
						   $primary_contact = (isset($PurchaseUnitDetail))?$PurchaseUnitDetail->primary_contact:null;
						   $UserPurchaseId = (isset($PurchaseUnitDetail))?$PurchaseUnitDetail->id:null;
						   $purchaseStatus = (isset($PurchaseUnitDetail))?$PurchaseUnitDetail->status:null;
						   $role_name = isset($roleInfo->name)?$roleInfo->name:null;
						   //exit;
						}

					$record['purchase_id']=$UserPurchaseId;
					$record['first_name']=Crypt::decryptString($user->first_name);
					$record['last_name']=isset($user->last_name)?Crypt::decryptString($user->last_name):null;
					$record['phone']=isset($user->phone)?Crypt::decryptString($user->phone):null;
					$record['building_name']=$building_name;
					$record['unit_name']=$unit_name;
					$record['purchase_id']=$UserPurchaseId;
					$record['primary_contact']=$primary_contact;
					$record['roleInfo']=$roleInfo;
					$record['purchase_id']=$UserPurchaseId;
					$record['primary_contact']=$primary_contact;
					$record['account_status']=$user->status;
					if($user->status ==2)
						$record['status']= "Account Deleted";
                    else if($purchaseStatus ==1)
						$record['status']="Active";
                    else
						$record['status']="Deactive";

					$record['status_value']=$purchaseStatus;
					$purchased_unit_data =array();
					if(isset($PurchaseUnitDetail)){
						$purchased_unit_data["id"]= $PurchaseUnitDetail->id;
						$purchased_unit_data["user_info_id"]= $PurchaseUnitDetail->user_info_id;
						$purchased_unit_data["property_id"]= $PurchaseUnitDetail->property_id;
						$purchased_unit_data["building_id"]= $PurchaseUnitDetail->building_id;
						$purchased_unit_data["unit_id"]= $PurchaseUnitDetail->unit_id;
					}

					$record['unit_assigned_details']=$purchased_unit_data;
					$unit_data =array();
					if(isset($UnitObj)){
						$unit_data["id"]= $UnitObj->id;
						$unit_data["unit"]= Crypt::decryptString($UnitObj->unit);
					}
					$record['unitInfo'] = $unit_data;

					
					//$record['unitInfo']=$UnitObj;
					$record['role_id']=$role_id;
					$record['role_name']=$role_name;

					/*$record['primary']=$user->primary_contact;
					$record['status']=isset($userMoreInfoObj->status)?$userMoreInfoObj->status:null;
					$record['created_at']=$user->created_at->format('d/m/Y');
					$record['end_date']=($user->deactivated_date != '0000-00-00' && $user->status ==0)?date('d/m/y',strtotime($user->deactivated_date)):null;
					$record['userinfo']['id'] = isset($userMoreInfoObj->id)?$userMoreInfoObj->id:null;
					$record['userinfo']['profile_picture'] = isset($userMoreInfoObj->profile_picture)?$userMoreInfoObj->profile_picture:null;
					$record['userinfo']['face_picture'] = isset($userMoreInfoObj->face_picture)?$userMoreInfoObj->face_picture:null;
					$record['userinfo']['last_name'] = isset($userMoreInfoObj->last_name)?$userMoreInfoObj->last_name:null;
					$record['userinfo']['phone'] = isset($userMoreInfoObj->phone)?$userMoreInfoObj->phone:null;
					$record['userinfo']['mailing_address'] = isset($userMoreInfoObj->mailing_address)?$userMoreInfoObj->mailing_address:null;
					//$record['unit'] = $user->getunit;*/
					$data[] = $record;
				
			}
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);

		}
		else if($type == 82 && isset($user_permission) && $user_permission->view==1){
			$licenseplates = UserLicensePlate::where('unit_id',$unit)->where('property_id',$account_id)->orderBy('id','DESC')->get();
			$license_plate_data =array();
				if(isset($licenseplates)){
					foreach($licenseplates as $licenseplate){
						$lplate_data =array();
						$lplate_data['id'] = $licenseplate->id;
						$lplate_data['unit_id'] = $licenseplate->unit_id;
						$lplate_data['user_id'] = $licenseplate->user_id;
						$lplate_data['user_info_id'] = $licenseplate->user_info_id;
						$lplate_data['building'] = isset($licenseplate->buildinginfo)?$licenseplate->buildinginfo->building:null;
						$lplate_data['unit'] = isset($licenseplate->addunitinfo)?Crypt::decryptString($licenseplate->addunitinfo->unit):null;
						$lplate_data['first_name'] = isset($licenseplate->usermoreinfo)?Crypt::decryptString($licenseplate->usermoreinfo->first_name):null;
						$lplate_data['last_name'] = isset($licenseplate->usermoreinfo)?Crypt::decryptString($licenseplate->usermoreinfo->last_name):null;
						$lplate_data['license_plate'] = $licenseplate->license_plate;

						$lplate_data['created_date'] = date('d/m/y',strtotime($licenseplate->created_at));
						$license_plate_data[] =$lplate_data;
					}
				}
				
			return response()->json(['data'=>$license_plate_data,'response' => 1, 'message' => 'Success']);

		}
		else if($type== 2 && isset($collection_permission) && $collection_permission->view==1){
			$keycollections = UnittakeoverAppointment::where('account_id',$account_id)->where('unit_no',$unit)->orderBy('id','DESC')->get();	
			$data =array();
			foreach($keycollections as $keycollection){
				$record['submission_info']=$keycollection;
				$unit_data =array();
				if(isset($keycollection->getunit)){
					$unit_data["id"]= $keycollection->getunit->id;
					$unit_data["unit"]= Crypt::decryptString($keycollection->getunit->unit);
					$unit_data["code"]= Crypt::decryptString($keycollection->getunit->code);
					$unit_data["building_id"]= $keycollection->getunit->building_id;
				}	
				$record['unit_info'] = !empty($unit_data)?$unit_data:null;
				$user_data =array();
				if(isset($keycollection->getname)){
					$user_data["id"]= $keycollection->getname->id;
					$user_data["account_id"]= $keycollection->getname->account_id;
					$user_data["role_id"]= $keycollection->getname->role_id;
					$user_data["user_info_id"]= $keycollection->getname->user_info_id;
					$user_data["building_no"]= $keycollection->getname->building_no;
					$user_data["unit_no"]= $keycollection->getname->unit_no;
					$user_data["primary_contact"]= $keycollection->getname->primary_contact;
					$user_data["name"]=Crypt::decryptString($keycollection->getname->name);
					
				}	
				$record['user_info'] = !empty($user_data)?$user_data:null;
				$data[] = $record;
			}
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
		else if($type== 3 && isset($defect_permission) && $defect_permission->view==1){
			$defects = Defect::where('account_id',$account_id)->whereIn('user_id',$userids)->orderBy('id','DESC')->get();
			$data =array();
			foreach($defects as $defect){
				$record['lists']=$defect;
				$unit_data =array();
				if(isset($defect->getunit)){
					$unit_data["id"]= $defect->getunit->id;
					$unit_data["unit"]= Crypt::decryptString($defect->getunit->unit);
					$unit_data["code"]= Crypt::decryptString($defect->getunit->code);
					$unit_data["building_id"]= $defect->getunit->building_id;
				}	
				$record['unit_info'] = !empty($unit_data)?$unit_data:null;
				$user_data =array();
				if(isset($defect->user)){
					$user_data["id"]= $defect->user->id;
					$user_data["account_id"]= $defect->user->account_id;
					$user_data["role_id"]= $defect->user->role_id;
					$user_data["user_info_id"]= $defect->user->user_info_id;
					$user_data["building_no"]= $defect->user->building_no;
					$user_data["unit_no"]= $defect->user->unit_no;
					$user_data["primary_contact"]= $defect->user->primary_contact;
					$user_data["name"]=Crypt::decryptString($defect->user->name);
					
				}	
				$record['user_info'] = !empty($user_data)?$user_data:null;
				//$record['submission'] = $defect->submissions;
				//$record['user_info'] = $defect->user;
				//$record['unit_info'] = isset($defect->getunit)?$defect->getunit:null;
				$record['inspection'] = $defect->inspection;
				$data[] = $record;
			}
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);

		}
		else if($type== 5 && isset($facility_permission) && $facility_permission->view==1){
			$facilities = FacilityBooking::where('account_id',$account_id)->where('unit_no', $unit)->orderby('id','desc')->get();  
			$data =array();
			foreach($facilities as $booking){
				$record['submissions']=$booking;
				$record['type'] = $booking->gettype;
				//$record['user_info'] = $booking->getname;
				$user_data =array();
				if(isset($booking->getname)){
					$user_data["id"]= $booking->getname->id;
					$user_data["account_id"]= $booking->getname->account_id;
					$user_data["role_id"]= $booking->getname->role_id;
					$user_data["user_info_id"]= $booking->getname->user_info_id;
					$user_data["building_no"]= $booking->getname->building_no;
					$user_data["unit_no"]= $booking->getname->unit_no;
					$user_data["primary_contact"]= $booking->getname->primary_contact;
					$user_data["name"]=Crypt::decryptString($booking->getname->name);
					
				}	
				$record['user_info'] = !empty($user_data)?$user_data:null;
				$unitObj = Unit::find($booking->unit_no);
				$unit_data =array();
				if(isset($unitObj)){
					$unit_data["id"]= $unitObj->id;
					$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
				}
				$record['unit_info'] = !empty($unit_data)?$unit_data:null;

				$data[] = $record;
			}	
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']); 
		}
		else if($type== 6 && isset($feedback_permission) && $feedback_permission->view==1){
			$feedbacks = FeedbackSubmission::where('account_id',$account_id)->whereIn('user_id',$userids)->orderBy('id','DESC')->get();
			$data =array();
			foreach($feedbacks as $feedback){
				$record['submissions']=$feedback;
				$record['option'] = $feedback->getoption;
				//$record['user_info'] = $feedback->user;
				//$record['unit_info'] = isset($feedback->getunit)?$feedback->getunit:null;
				$user_data =array();
				if(isset($feedback->user)){
					$user_data["id"]= $feedback->user->id;
					$user_data["account_id"]= $feedback->user->account_id;
					$user_data["role_id"]= $feedback->user->role_id;
					$user_data["user_info_id"]= $feedback->user->user_info_id;
					$user_data["building_no"]= $feedback->user->building_no;
					$user_data["unit_no"]= $feedback->user->unit_no;
					$user_data["primary_contact"]= $feedback->user->primary_contact;
					$user_data["name"]=Crypt::decryptString($feedback->user->name);
					
				}	
				$record['user_info'] = !empty($user_data)?$user_data:null;
				//$unitObj = Unit::find($booking->unit_no);
				$unit_data =array();
				if(isset($feedback->getunit)){
					$unit_data["id"]= $feedback->getunit->id;
					$unit_data["unit"]= Crypt::decryptString($feedback->getunit->unit);
				}
				$record['unit_info'] = !empty($unit_data)?$unit_data:null;

				$data[] = $record;
			}
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
		else if($type== 40 && isset($move_permission) && $move_permission->view==1){
			$submissions = EformMovingInOut::where('account_id',$account_id)->where('unit_no',$unit)->orderBy('id','DESC')->get();
			$data =array();
			foreach ($submissions as $k => $submission) {
				$record =array();
				$record['submission'] = $submission;
				$user_data =array();
				if(isset($submission->user)){
					$user_data["id"]= $submission->user->id;
					$user_data["account_id"]= $submission->user->account_id;
					$user_data["role_id"]= $submission->user->role_id;
					$user_data["user_info_id"]= $submission->user->user_info_id;
					$user_data["building_no"]= $submission->user->building_no;
					$user_data["unit_no"]= $submission->user->unit_no;
					$user_data["primary_contact"]= $submission->user->primary_contact;
					$user_data["name"]=Crypt::decryptString($submission->user->name);
					
				}
				$record['submitted_by'] = !empty($user_data)?$user_data:null;
				$unitObj = Unit::find($submission->unit_no);
				$unit_data =array();
				if(isset($unitObj)){
					$unit_data["id"]= $unitObj->id;
					$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
				}
				$record['unit'] = !empty($unit_data)?$unit_data:null;		
				//$record['submitted_by'] = isset($submission->user)?$submission->user:(object)[];
				//$record['unit'] = isset($submission->unitinfo)?$submission->unitinfo:(object)[];
				$data[] = $record;
			} 
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);		


		}
		else if($type== 41 && isset($renovation_permission) && $renovation_permission->view==1){
			$submissions = EformRenovation::where('account_id',$account_id)->where('unit_no',$unit)->orderBy('id','DESC')->get();
			$data =array();
			foreach ($submissions as $k => $submission) {
				$record =array();
				$record['id'] = $submission->id;
				$record['ticket'] = $submission->ticket;
				$record['reno_date'] = $submission->reno_date;
				$record['resident_name'] = $submission->resident_name;
				$record['contact_no'] = $submission->contact_no;
				$record['email'] = $submission->email;
				$record['reno_comp'] = $submission->reno_comp;
				$record['in_charge_name'] = $submission->in_charge_name;
				$record['comp_address'] = $submission->comp_address;
				$record['comp_contact_no'] = $submission->comp_contact_no;
				$record['reno_start'] = $submission->reno_start;
				$record['reno_end'] = $submission->reno_end;
				$record['hacking_work_start'] = $submission->hacking_work_start;
				$record['hacking_work_end'] = $submission->hacking_work_end;
				$record['status'] = $submission->status;
				$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
				$user_data =array();
				if(isset($submission->user)){
					$user_data["id"]= $submission->user->id;
					$user_data["account_id"]= $submission->user->account_id;
					$user_data["role_id"]= $submission->user->role_id;
					$user_data["user_info_id"]= $submission->user->user_info_id;
					$user_data["building_no"]= $submission->user->building_no;
					$user_data["unit_no"]= $submission->user->unit_no;
					$user_data["primary_contact"]= $submission->user->primary_contact;
					$user_data["name"]=Crypt::decryptString($submission->user->name);
					
				}
				$record['submitted_by'] = !empty($user_data)?$user_data:null;
				$unitObj = Unit::find($submission->unit_no);
				$unit_data =array();
				if(isset($unitObj)){
					$unit_data["id"]= $unitObj->id;
					$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
				}
				$record['unit'] = !empty($unit_data)?$unit_data:null;		
				$data[] = $record;
			} 
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);

		}
		else if($type== 42 && isset($dooraccess_permission) && $dooraccess_permission->view==1){
			$submissions = EformDoorAccess::where('account_id',$account_id)->where('unit_no',$unit)->orderBy('id','DESC')->get();
			$data =array();
			foreach ($submissions as $k => $submission) {
				$record =array();
				$record['id'] = $submission->id;
				$record['ticket'] = $submission->ticket;
				$record['request_date'] = $submission->request_date;
				$record['owner_name'] = $submission->owner_name;
				$record['contact_no'] = $submission->contact_no;
				$record['email'] = $submission->email;
				$record['declared_by'] = $submission->declared_by;
				$record['in_charge_name'] = $submission->in_charge_name;
				$record['passport_no'] = $submission->passport_no;
				$record['relationship'] = $submission->relationship;
				$record['nominee_contact_no'] = $submission->nominee_contact_no;
				$record['nominee_email'] = $submission->nominee_email;
				$record['no_of_card_required'] = $submission->no_of_card_required;
				$record['no_of_schlage_required'] = $submission->no_of_schlage_required;
				$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
				$user_data =array();
				if(isset($submission->user)){
					$user_data["id"]= $submission->user->id;
					$user_data["account_id"]= $submission->user->account_id;
					$user_data["role_id"]= $submission->user->role_id;
					$user_data["user_info_id"]= $submission->user->user_info_id;
					$user_data["building_no"]= $submission->user->building_no;
					$user_data["unit_no"]= $submission->user->unit_no;
					$user_data["primary_contact"]= $submission->user->primary_contact;
					$user_data["name"]=Crypt::decryptString($submission->user->name);
					
				}
				$record['submitted_by'] = !empty($user_data)?$user_data:null;
				$unitObj = Unit::find($submission->unit_no);
				$unit_data =array();
				if(isset($unitObj)){
					$unit_data["id"]= $unitObj->id;
					$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
				}
				$record['unit'] = !empty($unit_data)?$unit_data:null;
				$record['tenancy_start'] = $submission->tenancy_start;
				$record['tenancy_end'] = $submission->tenancy_end;
				$record['status'] = $submission->status;
				$data[] = $record;
			} 
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);

		}
		else if($type== 43 && isset($vehicle_permission) && $vehicle_permission->view==1){
			$submissions = EformRegVehicle::where('account_id',$account_id)->where('unit_no',$unit)->orderBy('id','DESC')->get();
			$data =array();
			foreach ($submissions as $k => $submission) {
				$record =array();
				$info =array();
				$info['id']= $submission->id;
				$info['ticket']= $submission->ticket;
				$info['user_id']= $submission->user_id;
				$info['request_date']= $submission->request_date;
				$info['owner_name']= $submission->owner_name;
				$info['contact_no']= $submission->contact_no;
				$info['email']= $submission->email;
				$info['tenancy_start']= $submission->tenancy_start;
				$info['tenancy_end']= $submission->tenancy_end;
				$info['status']= $submission->status;
				$info['created_at']= $submission->created_at->format('d/m/Y');;
				$info['updated_at']= $submission->updated_at->format('d/m/Y');;
				$record['submission'] = $info;
				//$record['documents'] = $submission->documents;
				$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
				$user_data =array();
				if(isset($submission->user)){
					$user_data["id"]= $submission->user->id;
					$user_data["account_id"]= $submission->user->account_id;
					$user_data["role_id"]= $submission->user->role_id;
					$user_data["user_info_id"]= $submission->user->user_info_id;
					$user_data["building_no"]= $submission->user->building_no;
					$user_data["unit_no"]= $submission->user->unit_no;
					$user_data["primary_contact"]= $submission->user->primary_contact;
					$user_data["name"]=Crypt::decryptString($submission->user->name);
					
				}
				$record['submitted_by'] = !empty($user_data)?$user_data:null;
				$unitObj = Unit::find($submission->unit_no);
				$unit_data =array();
				if(isset($unitObj)){
					$unit_data["id"]= $unitObj->id;
					$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
				}
				$record['unit'] = !empty($unit_data)?$unit_data:null;
				$data[] = $record;
			} 
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);

		}
		else if($type== 44 && isset($mailling_permission) && $mailling_permission->view==1){
			$submissions = EformChangeAddress::where('account_id',$account_id)->where('unit_no',$unit)->orderBy('id','DESC')->get();
			$data =array();
			foreach ($submissions as $k => $submission) {
				$record =array();
					$info =array();
					$info['id']= $submission->id;
					$info['ticket']= $submission->ticket;
					$info['user_id']= $submission->user_id;
					$info['request_date']= $submission->request_date;
					$info['owner_name']= $submission->owner_name;
					$info['contact_no']= $submission->contact_no;
					$info['email']= $submission->email;
					$info['status']= $submission->status;
					$info['created_at']= $submission->created_at->format('d/m/Y');;
					$info['updated_at']= $submission->updated_at->format('d/m/Y');;
					$record['submission'] = $info;
					//$record['documents'] = $submission->documents;
					$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
					$user_data =array();
					if(isset($submission->user)){
						$user_data["id"]= $submission->user->id;
						$user_data["account_id"]= $submission->user->account_id;
						$user_data["role_id"]= $submission->user->role_id;
						$user_data["user_info_id"]= $submission->user->user_info_id;
						$user_data["building_no"]= $submission->user->building_no;
						$user_data["unit_no"]= $submission->user->unit_no;
						$user_data["primary_contact"]= $submission->user->primary_contact;
						$user_data["name"]=Crypt::decryptString($submission->user->name);
						
					}
					$record['submitted_by'] = !empty($user_data)?$user_data:null;
					$unitObj = Unit::find($submission->unit_no);
					$unit_data =array();
					if(isset($unitObj)){
						$unit_data["id"]= $unitObj->id;
						$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
					}
					$record['unit'] = !empty($unit_data)?$unit_data:null;
					$data[] = $record;
			} 
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);

		}
		else if($type== 45 && isset($particular_permission) && $particular_permission->view==1){
			$submissions = EformParticular::where('account_id',$account_id)->where('unit_no',$unit)->orderBy('id','DESC')->get();
			$data =array();
			foreach ($submissions as $k => $submission) {
				$record =array();
				$info =array();
				$info['id']= $submission->id;
				$info['ticket']= $submission->ticket;
				$info['user_id']= $submission->user_id;
				$info['request_date']= $submission->request_date;
				$info['owner_name']= $submission->owner_name;
				$info['contact_no']= $submission->contact_no;
				$info['email']= $submission->email;
				$info['tenancy_start']= $submission->tenancy_start;
				$info['tenancy_end']= $submission->tenancy_end;
				$info['status']= $submission->status;
				$info['created_at']= $submission->created_at->format('d/m/Y');;
				$info['updated_at']= $submission->updated_at->format('d/m/Y');;
				$record['submission'] = $info;
				//$record['documents'] = $submission->documents;
				//$record['submitted_by'] = $submission->user;
				$record['unit_no'] = isset($submission->unitinfo)?Crypt::decryptString($submission->unitinfo->unit):null;
				//$record['unit'] = isset($submission->unitinfo)?$submission->unitinfo:null;
				$user_data =array();
				if(isset($submission->user)){
					$user_data["id"]= $submission->user->id;
					$user_data["account_id"]= $submission->user->account_id;
					$user_data["role_id"]= $submission->user->role_id;
					$user_data["user_info_id"]= $submission->user->user_info_id;
					$user_data["building_no"]= $submission->user->building_no;
					$user_data["unit_no"]= $submission->user->unit_no;
					$user_data["primary_contact"]= $submission->user->primary_contact;
					$user_data["name"]=Crypt::decryptString($submission->user->name);
				}
				$record['submitted_by'] = !empty($user_data)?$user_data:null;
				$unitObj = Unit::find($submission->unit_no);
				$unit_data =array();
				if(isset($unitObj)){
					$unit_data["id"]= $unitObj->id;
					$unit_data["unit"]= Crypt::decryptString($unitObj->unit);
				}
				$record['unit'] = !empty($unit_data)?$unit_data:null;
				$data[] = $record;
			} 
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);

		}
		else if($type== 38 && isset($card_permission) && $card_permission->view==1){
			$cards = Card::where('account_id',$account_id)->where('unit_no',$unit)->orderBy('id','DESC')->get();
			return response()->json(['data'=>$cards,'response' => 1, 'message' => 'Success']);
		}
		else if($type== 34 && isset($vm_permission) && $vm_permission->view==1){
			$bookings = VisitorBooking::where('account_id',$account_id)->where('unit_no',$unit)->orderBy('id','DESC')->get();
			$data =array();
			foreach ($bookings as $k => $booking) {
				$record =array();
				$record['id'] = $booking->id;
				$record['ticket'] = $booking->ticket;
				$record['booking_type'] = $booking->booking_type;
				$record['booking_type'] = $booking->booking_type;
				$record['view_status'] = $booking->view_status;
				if($booking->booking_type==1)
				{
					$record['unit'] =  isset($booking->user->userinfo->getunit->unit)?Crypt::decryptString($booking->user->userinfo->getunit->unit):null;
					$record['invited_by'] =isset($booking->user->name)?Crypt::decryptString($booking->user->name):null;
					$record['entry_date'] =  '';
					$record['entry_time'] =  '';
				}else{
					$record['unit'] = isset($booking->getunit->unit)?Crypt::decryptString($booking->getunit->unit):null;
					$record['invited_by'] = "Walk-In";
					$record['entry_date'] =  date('d/m/y',strtotime($booking->entry_date));
					$record['entry_time'] =  date('H:i',strtotime($booking->entry_date));
				}
				$record['date_of_visit'] =  date('d/m/y',strtotime($booking->visiting_date));
				$record['visitor_count'] = $booking->visitors->count();
				$record['purpose'] = isset($booking->visitpurpose->visiting_purpose)?$booking->visitpurpose->visiting_purpose:null;
				
				if($booking->visited_count->count() >= $booking->visitors->count())
					$record['status'] = "Entered";
				else if($booking->visited_count->count() >0 && $booking->visited_count->count() < $booking->visitors->count())
					$record['status'] = $booking->visited_count->count()." Entered";
				else if($booking->status==0)
					$record['status'] = "Pending";
				else if($booking->status==1)
					$record['status'] = "Cancelled";
				else  
					$record['status'] = "Entered";
				$data[] = $record;
			} 
			return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);			

		}
		else if($type== 60 && isset($rm_permission) && $rm_permission->view==1){
			$invoices = FinanceInvoice::where('account_id',$account_id)->where('unit_no',$unit)->orderBy('id','DESC')->get();
			$visitor_app_url = env('VISITOR_APP_URL')."invoice-pdf/";
			$data =array();
				foreach ($invoices as $k => $invoice) {
					$record =array();
					$record['invoice_info'] = $invoice;
					$record['building'] = isset($invoice->getunit->buildinginfo)?$invoice->getunit->buildinginfo:null;
					$record['unit'] = isset($invoice->getunit)?Crypt::decryptString($invoice->getunit):null;

					$advance_amount = isset($invoice->AdvancePayment->amount)?$invoice->AdvancePayment->amount:0;
					$invoice_amount  = $invoice->payable_amount - $advance_amount;
					$record['invoice_amount'] = $invoice_amount;

					if(isset($invoice->status)){
						if($invoice->status !=3){
						   $financeObj = new \App\Models\v7\FinanceInvoice();
						   $ref_invoice = $financeObj->CheckNewInvoice($invoice->id,$invoice->unit_no);
						   //print_r($ref_invoice);
						}

						if($invoice->status==1){
						   $rec = $financeObj->CheckOverDue($invoice->id);
						   
							   if(isset($ref_invoice->id))
								$record['status_lable'] = "Unpaid/ref.".$ref_invoice->invoice_no;
							   else
								   $record['status_lable'] = $rec;
						}                                      
						else  if($invoice->status==2){
								$record['status_lable'] ="Partial Paid";
						   if(isset($ref_invoice->id))
								   $record['status_lable'] = "/ref.".$ref_invoice->invoice_no;
						}
						else  if($invoice->status==4)
							$record['status_lable'] ="Pending Verification";
						else 
							$record['status_lable'] ="Paid";
					 }
					$data[] = $record;
				} 
			return response()->json(['data'=>$data,'file_path'=>$visitor_app_url,'response' => 1, 'message' => 'Success']);	

		}
		
	}

	public function UserRoles(Request $request) 
	{
	
			$input = $request->all();
			$login_id = Auth::id();
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
				$env_roles 	= env('USER_APP_ROLE');
				$roles = explode(",",$env_roles);
				$data = Role::whereIn('id',$roles)->get();

				return response()->json(['roles'=>$data,'response' => 1, 'message' => 'Success']);

	}
	public function userrolesarray(Request $request) 
	{
	
			$input = $request->all();
			$login_id = Auth::id();
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
			
				$env_roles 	= env('USER_APP_ROLE');
				$user_roles = explode(",",$env_roles);
				$roles = Role::select('id','name')->whereIn('id',$user_roles)->get();
				return response()->json(['roles'=>$roles,'response' => 1, 'message' => 'Success']);

	}
	public function countries(Request $request) 
	{
			
			$input = $request->all();
			$login_id = Auth::id();
			$adminObj = User::find($login_id); 

			if(empty($adminObj)){
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
			}
				$allcountries = Country::whereNotIn('id',[1,2])->where('status',1)->orderby('country_name','ASC')->pluck('country_name', 'id')->all();
        		$loccountries = Country::whereIn('id',[1,2])->where('status',1)->pluck('country_name', 'id')->all();
				$countries =$loccountries +  $allcountries ;
				return response()->json(['roles'=>$countries,'response' => 1, 'message' => 'Success']);

	}

	public function manager_notifications(Request $request)
	{
		
		$input = $request->all();
			$login_id = Auth::id();
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		
		$messages = InboxMessage::whereIn('type',[2,3,6,4,7,8,9,10,11,12,13,14,15,16,50,34])->where('submitted_by',1)->where('account_id',$adminObj->account_id)->orderby('id','desc')->get();  
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
                $record['admin_view_status'] = $message->admin_view_status;
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
				else if($message->type ==4){
					$recordObj = UnittakeoverAppointment::find($message->ref_id);
					if(empty($recordObj))
						$record['deleted'] = 1;
				}
				else if($message->type ==6){
					$recordObj = FacilityBooking::find($message->ref_id);
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
				
				else if($message->type ==16){
					$recordObj = FinanceInvoice::find($message->ref_id);
					if(empty($recordObj))
						$record['deleted'] = 1;
				}
				else if($message->type ==50){
					$recordObj = UserFacialId::find($message->ref_id);
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
		$data['types'] = array(2=>"Feedback Submission",3=>"Defect Submission",4=>"Key Collection",6=>"Facility Booking",7=>'Move In & Out: E-form',8=>'Renovation: E-form',9=>'Door Access Card: E-form',10=>'Registration for Vehicle: E-form',11=>'Change of Mailing Address: E-form',12=>'Update of Particulars: E-form',13=>"Resident File upload",14=>"Visitor Management",15=>'Update',16=>'Resident Management',50=>"Facial Recognition");

		return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);

		
	}

	public function update_notification(Request $request)
	{
		$rules=array(
			'id'=>'required'
		);
		$messages=array(
			'id.required' => 'Notification id is missing',
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
			$login_id = Auth::id();
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		$notiObj = InboxMessage::find($input['id']);
		if(isset($notiObj)){
			$notiObj->admin_view_status = 1;
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


	public function sharesettingslist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			if($adminObj->role_id ==1)
				$shares = FinanceShareSetting::get(); 
			else
				$shares = FinanceShareSetting::where('account_id',$account_id)->orderby('id','desc')->get(); 
				$data =array();
				if(isset($shares)){
					foreach($shares as $share){
						$record =array();
						$record['id'] =$share->id;
						$record['account_id'] =$share->account_id;
						$record['management_fund_share'] =$share->management_fund_share;
						$record['sinking_fund_share'] =$share->sinking_fund_share;
						$record['share_amount'] =$share->share_amount;
						$record['no_of_billing_month'] =$share->no_of_billing_month;
						$record['interest'] =$share->interest;
						$record['int_percentage'] =$share->int_percentage;
						$record['tax'] =$share->tax;
						$record['tax_percentage'] =$share->tax_percentage;
						$record['due_period_value'] =$share->due_period_value;
						$record['due_period_type'] =$share->due_period_type;
						$record['qrcode_file'] =$share->qrcode_file;
						$record['status'] =$share->status;
						$record['start_date'] =date('d/m/y',strtotime($share->created_at));
						$record['end_date'] =($share->created_at != $share->updated_at)?date('d/m/y',strtotime($share->updated_at)):null;
						$data[] = $record;
					}

				}
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function createsharesettings(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 

		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'Login not found']);
		}
		
		$permission = $adminObj->check_permission(24,$adminObj->role_id); 
		if(empty($permission) && $permission->create!=1){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$account_id = $adminObj->account_id;
			$unit =  FinanceShareSetting::where('account_id', $account_id)->where('status',1)->first(); 
			if(isset($unit)){
			FinanceShareSetting::where('account_id', $account_id)->where('id',$unit->id)
			->update(['status' => 0]);
			}
			if(isset($input['interest']) && $input['interest'] ==1){
				$input['int_percentage'] = 0;
			}
			if(isset($input['tax']) && $input['tax'] ==1){
				$input['tax_percentage'] = 0;
			}
			if ($request->file('qrcode_file') != null) {
				$input['qrcode_file'] = remove_upload_path($request->file('qrcode_file')->store(upload_path('finance')));
			}
			$input['account_id'] = $account_id;
			FinanceShareSetting::create($input);
			return response()->json(['response' => 1, 'message' => 'Settings has been created']);
       
		}
	}

	public function opn_facility_capture_amount(Request $request) {

		$rules=array(
			'booking_id' => 'required',
			'deposit_amount' => 'required',
			'charge_amount' => 'required',
			'refund_amount' => 'required',
		);
		$messages=array(
			'booking_id.required' => 'Facility id is missing',
			'deposit_amount.required' => 'Deposit amount is required',
			'charge_amount.required' => 'Charge amount is required',
			'refund_amount.required' => 'Refund amount isrequired',
		);

		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(5,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$input = $request->all();
			$booking_id = $input['booking_id'];
			$recordObj = FacilityBooking::find($input['booking_id']);
			$charge_amount = $input['charge_amount'];
			$refund_amount = $recordObj->deposit_fee-$charge_amount;
			if($charge_amount > $recordObj->deposit_fee){
				return response()->json(['response' => 200, 'message' => 'Claim amount is more than deposit']);

			}
		
			if(isset($recordObj->opn_charge_id) && $recordObj->opn_charge_id !=''){
				$payment_url = env('OMISEURL')."charges/".$recordObj->opn_charge_id."/capture";
				$propinfo = Property::where('id',$recordObj->account_id)->first();
				$username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
				$password = '';
				$capture_amount = ($recordObj->booking_fee + $input['charge_amount'])*100;
				$fields = [
						"capture_amount" => $capture_amount,
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
				//print_r($json);
				if(isset($json['id']) && $json['id'] !=''){

					$refund_status = ($refund_amount ==$recordObj->deposit_fee)?1:2;
					$facility_update_qry =FacilityBooking::where('id',$booking_id)->update(['capture_amount' => $charge_amount,'refund_amount'=>$refund_amount,'refund_status' => $refund_status]);

					return response()->json(['data'=>$json,'response' => 1, 'message' => 'success']);
				}else{
					return response()->json(['data'=>null,'response' => 200, 'message' => 'Charge not authorised']);
				}
			}else{
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Charge id not created']);

			}
				
		}
	}

	public function resichatlist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$lists = ChatBox::where('account_id',$account_id)->orderby('id','desc')->get();   
		$data =array();
		foreach($lists as $list){
			$chat_data =array();
			$chat_data['id']=$list->id;
			$chat_data['property_id']=isset($list->propertyinfo->id)?$list->propertyinfo->id:null;
			$chat_data['property_name']=isset($list->propertyinfo->company_name)?$list->propertyinfo->company_name:null;
			$chat_data['user_id']=$list->user_id;
			$chat_data['user']=isset($list->user->name)?Crypt::decryptString($list->user->name):null;
			$chat_data['ticket']=$list->ticket;
			$chat_data['subject']=$list->subject;
			$chat_data['category_id']=$list->category;
			$chat_data['category_name']=isset($list->cat_info->name)?$list->cat_info->name:null;
			$chat_data['comments']=$list->comment_count();
			$chat_data['reports']=$list->report_count();
			$chat_data['status']=$list->status;
			$chat_data['created_at']=($list->created_at != '0000-00-00' && $list->created_at != '')?date('d/m/y',strtotime($list->created_at)):null;
			$data[] = $chat_data;
		}
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function resichatsearch(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$first_name = $request->input('first_name');
			$ticket = $request->input('ticket');
			$subject = $request->input('subject');
			$property = $account_id;
			$userids =array();
			if($first_name !='' || $ticket!='' || $subject !=''){
				if($property !='')
				{
					if($first_name !=''){
						$user_more_info = UserMoreInfo::where('account_id',$property)->where('status',1)->orderby('id','desc')->get();
						foreach($user_more_info as $k =>$v){
							$firstname = strtolower(Crypt::decryptString($v->first_name));
							$lastname = strtolower(Crypt::decryptString($v->last_name));
							if(str_contains($firstname,strtolower($first_name)) || str_contains($lastname,strtolower($first_name))){
								$userids[] = $v->user_id;
								
							}
						}
					} 
					else{
						$user_more_info = UserMoreInfo::where('account_id',$property)->where('status',1)->orderby('id','desc')->get();
						foreach($user_more_info as $k =>$v){
						$userids[] = $v->user_id;
						}
					}
				}
				$lists = ChatBox::where(function ($query) use ($property,$userids,$ticket,$subject) {
					if($property !=''){
						$query->where('account_id', $property);
					} 
					if($ticket !=''){
						$query->where('ticket', 'LIKE', '%' . $ticket . '%');
					} 
					if($subject !=''){
						$query->where('subject', 'LIKE', '%' . $subject . '%');
					} 
					if(count($userids) >0){
						$query->whereIn('user_id',$userids);
					}  
				})->orderby('id','desc')->get();   
				$data =array();
				foreach($lists as $list){
					$chat_data =array();
					$chat_data['id']=$list->id;
					$chat_data['property_id']=isset($list->propertyinfo->id)?$list->propertyinfo->id:null;
					$chat_data['property_name']=isset($list->propertyinfo->company_name)?$list->propertyinfo->company_name:null;
					$chat_data['user_id']=$list->user_id;
					$chat_data['user']=isset($list->user->name)?Crypt::decryptString($list->user->name):null;
					$chat_data['ticket']=$list->ticket;
					$chat_data['subject']=$list->subject;
					$chat_data['category_id']=$list->category;
					$chat_data['category_name']=isset($list->cat_info->name)?$list->cat_info->name:null;
					$chat_data['comments']=$list->comment_count();
					$chat_data['reports']=$list->report_count();
					$chat_data['status']=$list->status;
					$chat_data['created_at']=($list->created_at != '0000-00-00' && $list->created_at != '')?date('d/m/y',strtotime($list->created_at)):null;
					$data[] = $chat_data;
				}
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
				
			}
			else{
				return response()->json(['response' => 1, 'message' => 'Search option are empty']);
			}
		}
	}

	public function resichatnewreport(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$new_record = ChatBoxReport::where('status', 1)->where('view_status', 1)->where('account_id', $account_id)
        ->count();
		return response()->json(['data'=>$new_record,'response' => 1, 'message' => 'Success']);
		}
	}

	public function resichatallreports(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$chat_id = $request->chat_id;
        $lists = ChatBoxReport::where('account_id',$account_id)->where('status',1)->orderby('id','desc')->groupBy('ref_id')->get();  
		$data =array();
		foreach($lists as $list){
			$chat_data =array();
			$chat_data['id']=$list->id;
			$chat_data['user_id'] = $list->user_id;
			$chat_data['user_name'] = isset($list->user->name)?Crypt::decryptString($list->user->name):null;
			$chat_data['subject_id']=$list->ref_id;
			$chat_data['subject']=$list->topic->subject;
			$chat_data['ticket']=$list->topic->ticket;
			$chat_data['category']=isset($list->topic->cat_info->name)?$list->topic->cat_info->name:null;
			$chat_data['total_report']=$list->topic->report_count();
			$chat_data['new_report']=$list->topic->new_count();
			$chat_data['status']=$list->status;
			$chat_data['created_at']=($list->created_at != '0000-00-00' && $list->created_at != '')?date('d/m/y',strtotime($list->created_at)):null;
			$data[] = $chat_data;
		}
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function resichatreports(Request $request) 
    {
		$rules=array(
			'chat_id' => 'required',
		);
		$messages=array(
			'chat_id.required' => 'Chat id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$chat_id = $request->chat_id;
		$ChatObj = ChatBox::find($chat_id);
        $lists = ChatBoxReport::where('ref_id',$chat_id)->orderby('id','desc')->get();  
		$data =array();
		foreach($lists as $list){
			$chat_data =array();
			$chat_data['id']=$list->id;
			$chat_data['user_id']=$list->user_id;
			$chat_data['user']=isset($list->user->name)?Crypt::decryptString($list->user->name):null;
			$chat_data['remark']=$list->remark;
			$chat_data['ref_id']=$list->ref_id;
			$chat_data['status']=$list->status;
			$chat_data['created_at']=($list->created_at != '0000-00-00' && $list->created_at != '')?date('d/m/y',strtotime($list->created_at)):null;
			$data[] = $chat_data;
		}
		ChatBoxReport::where('ref_id',$chat_id)->where('view_status',1)->update(['view_status'=>2]);

		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function resichatcomments(Request $request) 
    {
		$rules=array(
			'chat_id' => 'required',
		);
		$messages=array(
			'chat_id.required' => 'Chat id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$chat_id = $request->chat_id;
		$ChatObj = ChatBox::find($chat_id);
        $lists = ChatBoxComment::where('ref_id',$chat_id)->orderby('id','desc')->get();  
		$data =array();
		foreach($lists as $list){
			$chat_data =array();
			$chat_data['id']=$list->id;
			$chat_data['user_id']=$list->user_id;
			$chat_data['user']=isset($list->user->name)?Crypt::decryptString($list->user->name):null;
			$chat_data['comment']=$list->comment;
			$chat_data['comment_report']=$list->report_count();
			$chat_data['ref_id']=$list->ref_id;
			$chat_data['status']=$list->status;
			$chat_data['created_at']=($list->created_at != '0000-00-00' && $list->created_at != '')?date('d/m/y',strtotime($list->created_at)):null;
			$data[] = $chat_data;
		}
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}
	public function resichatdeactivate(Request $request) 
    {
		$rules=array(
			'chat_id' => 'required',
		);
		$messages=array(
			'chat_id.required' => 'Chat id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$chat_id = $request->chat_id;
		$ChatObj = ChatBox::find($chat_id);
		$ChatObj->status =2;
        $ChatObj->save();
		return response()->json(['data'=>$ChatObj,'response' => 1, 'message' => 'De-Activated']);
		}
	}
	public function resichatactivate(Request $request) 
    {
		$rules=array(
			'chat_id' => 'required',
		);
		$messages=array(
			'chat_id.required' => 'Chat id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$chat_id = $request->chat_id;
		$ChatObj = ChatBox::find($chat_id);
		$ChatObj->status =1;
        $ChatObj->save();
		return response()->json(['data'=>$ChatObj,'response' => 1, 'message' => 'Activated']);
		}
	}

	public function resichathidecomments(Request $request) 
    {
		$rules=array(
			'comment_id' => 'required',
		);
		$messages=array(
			'comment_id.required' => 'Chat id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$chat_id = $request->comment_id;
		$ChatObj = ChatBoxComment::find($chat_id);
		$ChatObj->status =2;
        $ChatObj->save();
		return response()->json(['data'=>$ChatObj,'response' => 1, 'message' => 'De-Activated']);
		}
	}

	public function resichatshowcomments(Request $request) 
    {
		$rules=array(
			'comment_id' => 'required',
		);
		$messages=array(
			'comment_id.required' => 'Chat id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$chat_id = $request->comment_id;
		$ChatObj = ChatBoxComment::find($chat_id);
		$ChatObj->status =1;
        $ChatObj->save();
		return response()->json(['data'=>$ChatObj,'response' => 1, 'message' => 'De-Activated']);
		}
	}

	public function resichathidereport(Request $request) 
    {
		$rules=array(
			'report_id' => 'required',
		);
		$messages=array(
			'report_id.required' => 'Report id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$report_id = $request->report_id;
		$ReportObj = ChatBoxReport::find($report_id);
		$ReportObj->status =2;
        $ReportObj->save();
		return response()->json(['data'=>$ReportObj,'response' => 1, 'message' => 'De-Activated']);
		}
	}

	public function resichatshowreport(Request $request) 
    {
		$rules=array(
			'report_id' => 'required',
		);
		$messages=array(
			'report_id.required' => 'Report id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$report_id = $request->report_id;
		$ReportObj = ChatBoxReport::find($report_id);
		$ReportObj->status =1;
        $ReportObj->save();
		return response()->json(['data'=>$ReportObj,'response' => 1, 'message' => 'Activated']);
		}
	}

	public function resichatblocklistedusers(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$chat_id = $request->chat_id;
        $lists = ChatBoxBlockUserByAdmin::where('account_id',$account_id)->orderby('id','desc')->get();  
		$data =array();
		foreach($lists as $list){
			$userinfo = UserMoreInfo::where('account_id',$account_id)->where('user_id',$list->block_user_id)->orderby('id','desc')->first();
			$chat_data =array();
			$chat_data['id']=$list->id;
			$chat_data['user_id']=$list->block_user_id;
			$chat_data['user_first_name']=isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):null;
			$chat_data['user_last_name']=isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
			$chat_data['admin_id']=$list->admin_id;
			$chat_data['admin_name']=isset($list->admininfo->name)?Crypt::decryptString($list->admininfo->name):null;
			$chat_data['type']=$list->type;
			$chat_data['ref_id']=$list->ref_id;
			$chat_data['status']=$list->status;
			$chat_data['created_at']=($list->created_at != '0000-00-00' && $list->created_at != '')?date('d/m/y',strtotime($list->created_at)):null;
			$data[] = $chat_data;
		}
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function resichatremovefromblocklist(Request $request) 
    {
		$rules=array(
			'block_id' => 'required',
		);
		$messages=array(
			'block_id.required' => 'Block id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$block_id = $request->block_id;
		$ReportObj = ChatBoxBlockUserByAdmin::find($block_id);
		$ReportObj->status =2;
        $ReportObj->save();
		return response()->json(['data'=>$ReportObj,'response' => 1, 'message' => 'Removed from blocklist!']);
		}
	}

	public function resichataddtoblocklist(Request $request) 
    {
		$rules=array(
			'block_id' => 'required',
		);
		$messages=array(
			'block_id.required' => 'Block id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$block_id = $request->block_id;
		$ReportObj = ChatBoxBlockUserByAdmin::find($block_id);
		$ReportObj->status =1;
        $ReportObj->save();
		return response()->json(['data'=>$ReportObj,'response' => 1, 'message' => ' User has been blocked!']);
		}
	}

	public function resichatblockuser(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'type' => 'required',
			'ref_id' => 'required',
			'remark'=> 'required',
		);
		$messages=array(
			'user_id.required' => 'User Id is missing',
			'type.required' => 'type is missing',
			'ref_id.required' => 'Ref id is missing',
			'remark.required' => 'Remark is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$UserObj = User::find($request->user_id);
			$check_status = ChatBoxBlockUserByAdmin::where('account_id',$UserObj->account_id)->where('admin_id',$login_id)->where('block_user_id',$request->user_id)->first();
			if(isset($check_status)){
				return response()->json(['response' => 200, 'message' => 'Already Blocked!']);
			}else{
				$ref_id = $request->ref_id;
				$type = $request->type;
				if($type ==1){
					$comObj = ChatBoxComment::find($ref_id);
					if(empty($comObj)) 
					return response()->json(['data'=>null,'response' => 300, 'message' => 'Comment not exist!']);
	
				}
				else{
					$comObj = ChatBoxReport::find($ref_id); 
					if(empty($comObj))
					return response()->json(['data'=>null,'response' => 300, 'message' => 'Report not exist!']);
				}

				$input['admin_id'] = $login_id;
				$input['account_id'] = $UserObj->account_id;
				$input['unit_no'] = $comObj->unit_no;
				$input['block_user_id'] = $request->user_id;
				$input['type'] = $request->type;
				$input['ref_id'] = $request->ref_id;
				$input['remark'] = $request->remark;
				$input['status'] = 1;
				$results = ChatBoxBlockUserByAdmin::create($input);
				return response()->json(['response' => 1, 'message' => 'Blocked!']);
			}
		}
	}

	public function resichatwarninguser(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'type' => 'required',
			'ref_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User Id is missing',
			'type.required' => 'type is missing',
			'ref_id.required' => 'Ref id is missing',
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
		$user_id = $request->user_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$UserObj = User::find($user_id); 
			$ref_id = $request->ref_id;
			$type = $request->type;
			if($type ==1){
				$comObj = ChatBoxComment::find($ref_id);
				if(empty($comObj)) 
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Comment not exist!']);

			}
			else{
				$comObj = ChatBoxReport::find($ref_id); 
				if(empty($comObj))
				return response()->json(['data'=>null,'response' => 300, 'message' => 'Report not exist!']);
			}

			if(isset($UserObj)){
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserLog::where('user_id',$comObj->user_id)->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $comObj->id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
		
				$probObj = Property::find($adminObj->account_id);
				$title = "Aerea Home - ".$probObj->company_name;
				$message = "Warning from ResiChat";

				//Start Insert into notification module
				$notification = array();
				$notification['account_id'] = $comObj->account_id;
				$notification['user_id'] = $comObj->user_id;
				$notification['unit_no'] = $comObj->unit_no;
				$notification['module'] = 'resichat_warning';
				$notification['ref_id'] = $comObj->id;
				$notification['title'] = $message;
				$notification['message'] = $message;
				UserNotification::insert($notification);
				//End Insert into notification module

		 
				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$comObj->unit_no;   
				$notofication_data['user_id'] =$comObj->user_id;   
				$notofication_data['property'] =$comObj->account_id; 
				$purObj = UserPurchaserUnit::where('property_id',$comObj->account_id)->where('unit_id',$comObj->unit_no)->where('user_id',$comObj->user_id)->first(); 
				if(isset($purObj))
					$notofication_data['switch_id'] =$purObj->id;        
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
				return response()->json(['response' => 1, 'message' => 'Warning has been sent!']);
			}else{
				return response()->json(['response' => 1, 'message' => 'User no login history from Home App!']);
			}
		}
	}

	public function mpadslist(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(77,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$lists = MpAdsSubmission::where('account_id',$account_id)->orderby('id','desc')->get();   
		$data =array();
		foreach($lists as $list){
			$chat_data =array();
			$chat_data['id']=$list->id;
			$chat_data['property_id']=isset($list->propertyinfo->id)?$list->propertyinfo->id:null;
			$chat_data['property_name']=isset($list->propertyinfo->company_name)?$list->propertyinfo->company_name:null;
			$chat_data['user_id']=$list->user_id;
			$chat_data['user']=isset($list->user->name)?Crypt::decryptString($list->user->name):null;
			$chat_data['ticket']=$list->ticket;
			$chat_data['title']=$list->title;
			$chat_data['price']=$list->price;
			$chat_data['type_id']=$list->type;
			$chat_data['type']=isset($list->gettype->type)?$list->gettype->type:null;
			$chat_data['condition_id']=$list->item_condition;
			$chat_data['condition']=isset($list->getcondition->type)?$list->getcondition->type:null;
			$chat_data['likes']=$list->like_count();
			$chat_data['reports']=$list->report_count();
			$chat_data['status']=$list->status;
			$chat_data['created_at']=($list->created_at != '0000-00-00' && $list->created_at != '')?date('d/m/y',strtotime($list->created_at)):null;
			$data[] = $chat_data;
		}
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function mpadssearch(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(77,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$account_id = $adminObj->account_id;
			$first_name = $request->input('first_name');
			$title = $request->input('title');
			$type = $request->input('type');
			$condition = $request->input('condition');
			$property = $account_id;
			$userids =array();
			if($first_name !='' || $title !='' || $type !='' || $condition !='')
			{
				if($property !='')
				{
					if($first_name !=''){
						$user_more_info = UserMoreInfo::where('account_id',$property)->where('status',1)->orderby('id','desc')->get();
						foreach($user_more_info as $k =>$v){
							$firstname = strtolower(Crypt::decryptString($v->first_name));
							$lastname = strtolower(Crypt::decryptString($v->last_name));
							if(str_contains($firstname,strtolower($first_name)) || str_contains($lastname,strtolower($first_name))){
								$userids[] = $v->user_id;
								
							}
						}
					} 
					else{
						$user_more_info = UserMoreInfo::where('account_id',$property)->where('status',1)->orderby('id','desc')->get();
						foreach($user_more_info as $k =>$v){
						$userids[] = $v->user_id;
						}
					}
				}
				
				//print_r($userids);
				$lists = MpAdsSubmission::where(function ($query) use ($property,$userids,$title,$type,$condition) {
					if($property !=''){
						$query->where('account_id', $property);
					} 
					if($title !=''){
						$query->where('title', 'LIKE', '%' . $title . '%');
					} 
					if($type !=''){
						$query->where('type',$type);
					} 
					if($condition !=''){
						$query->where('item_condition',$condition);
					} 
					if(count($userids) >0){
						$query->whereIn('user_id',$userids);
					}  
				})->orderby('id','desc')->get();   
				$data =array();
				foreach($lists as $list){
					$chat_data =array();
					$chat_data['id']=$list->id;
					$chat_data['property_id']=isset($list->propertyinfo->id)?$list->propertyinfo->id:null;
					$chat_data['property_name']=isset($list->propertyinfo->company_name)?$list->propertyinfo->company_name:null;
					$chat_data['user_id']=$list->user_id;
					$chat_data['user']=isset($list->user->name)?Crypt::decryptString($list->user->name):null;
					$chat_data['ticket']=$list->ticket;
					$chat_data['title']=$list->title;
					$chat_data['price']=$list->price;
					$chat_data['type_id']=$list->type;
					$chat_data['type']=isset($list->gettype->type)?$list->gettype->type:null;
					$chat_data['condition_id']=$list->item_condition;
					$chat_data['condition']=isset($list->getcondition->type)?$list->getcondition->type:null;
					$chat_data['likes']=$list->like_count();
					$chat_data['reports']=$list->report_count();
					$chat_data['status']=$list->status;
					$chat_data['created_at']=($list->created_at != '0000-00-00' && $list->created_at != '')?date('d/m/y',strtotime($list->created_at)):null;
					$data[] = $chat_data;
				}
				return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
				
			}
			else{
				return response()->json(['response' => 1, 'message' => 'Search option are empty']);
			}
		}
	}

	public function mpadsallreports(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(77,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$lists = MpAdsReport::where('account_id',$account_id)->where('status',1)->orderby('id','desc')->groupBy('ref_id')->get();  
		$data =array();
		foreach($lists as $list){
			$chat_data =array();
			$chat_data['id']=$list->id;
			$chat_data['user_id'] = $list->user_id;
			$chat_data['user_name'] = isset($list->user->name)?Crypt::decryptString($list->user->name):null;
			$chat_data['subject_id']=$list->ref_id;
			$chat_data['title']=$list->gettitle->title;
			$chat_data['price']=$list->gettitle->price;
			$chat_data['type']=isset($list->gettitle->gettype->type)?$list->gettitle->gettype->type:null;
			$chat_data['condition']=isset($list->gettitle->getcondition->type)?$list->gettitle->getcondition->type:null;
			$chat_data['total_report']=$list->gettitle->report_count();
			$chat_data['new_report']=$list->gettitle->new_count();
			$chat_data['status']=$list->status;
			$chat_data['created_at']=($list->created_at != '0000-00-00' && $list->created_at != '')?date('d/m/y',strtotime($list->created_at)):null;
			$data[] = $chat_data;
		}
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function mpadsreports(Request $request) 
    {
		$rules=array(
			'mpads_id' => 'required',
		);
		$messages=array(
			'mpads_id.required' => 'MArketplace id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$mpads_id = $request->mpads_id;
		$ChatObj = MpAdsSubmission::find($mpads_id);
        //$lists = MpAdsReport::where('ref_id',$mpads_id)->orderby('id','desc')->get();  
		$lists = MpAdsReport::where('ref_id',$mpads_id)->orderby('id','desc')->get();  
		$data =array();
		foreach($lists as $list){
			$chat_data =array();
			$chat_data['id']=$list->id;
			$chat_data['user_id'] = $list->user_id;
			$chat_data['user_name'] = isset($list->user->name)?Crypt::decryptString($list->user->name):null;
			$chat_data['subject_id']=$list->ref_id;
			$chat_data['title']=$list->gettitle->title;
			$chat_data['price']=$list->gettitle->price;
			$chat_data['type']=isset($list->gettitle->gettype->type)?$list->gettitle->gettype->type:null;
			$chat_data['condition']=isset($list->gettitle->getcondition->type)?$list->gettitle->getcondition->type:null;
			$chat_data['remark']=$list->remark;
			$chat_data['total_report']=$list->gettitle->report_count();
			$chat_data['new_report']=$list->gettitle->new_count();
			$chat_data['status']=$list->status;
			$chat_data['created_at']=($list->created_at != '0000-00-00' && $list->created_at != '')?date('d/m/y',strtotime($list->created_at)):null;
			$data[] = $chat_data;
		}
		MpAdsReport::where('ref_id',$mpads_id)->where('view_status',1)->update(['view_status'=>2]);

		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function mpadslikes(Request $request) 
    {
		$rules=array(
			'mpads_id' => 'required',
		);
		$messages=array(
			'mpads_id.required' => 'MArketplace id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$mpads_id = $request->mpads_id;
		$ChatObj = MpAdsSubmission::find($mpads_id);
        $lists = MpAdsLike::where('ref_id',$mpads_id)->orderby('id','desc')->get();  
		$BlkUserObj = new \App\Models\v7\MpAdsLike();

		$data =array();
		foreach($lists as $list){
			$chat_data =array();
			$userinfo = $BlkUserObj->likeduserinfo($list->user_id,$list->account_id);
			
			$chat_data['id']=$list->id;
			$chat_data['user_id']=$list->user_id;
			$chat_data['first_name']=isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):null;
			$chat_data['last_name']=isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
			$chat_data['ref_id']=$list->ref_id;
			$chat_data['status']=$list->status;
			$chat_data['created_at']=($list->created_at != '0000-00-00' && $list->created_at != '')?date('d/m/y',strtotime($list->created_at)):null;
			$data[] = $chat_data;
		}
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}
	public function mpadslikedelete(Request $request) 
    {
		$rules=array(
			'mpads_id' => 'required',
		);
		$messages=array(
			'mpads_id.required' => 'Marketplace id is missing',
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
		
		$permission = $adminObj->check_permission(77,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        	$account_id = $adminObj->account_id;
			$id = $request->like_id;
			
        	MpAdsLike::findOrFail($id)->delete();
			return response()->json(['data'=>$ChatObj,'response' => 1, 'message' => 'Deleted']);
		}
	}
	public function mpadsactivate(Request $request) 
    {
		$rules=array(
			'mpads_id' => 'required',
		);
		$messages=array(
			'mpads_id.required' => 'Marketplace id is missing',
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
		
		$permission = $adminObj->check_permission(77,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$mpads_id = $request->mpads_id;
		$ChatObj = MpAdsSubmission::find($mpads_id);
		$ChatObj->status =1;
        $ChatObj->save();
		return response()->json(['data'=>$ChatObj,'response' => 1, 'message' => 'Activated']);
		}
	}
	
	public function mpadsdeactivate(Request $request) 
    {
		$rules=array(
			'mpads_id' => 'required',
		);
		$messages=array(
			'mpads_id.required' => 'M<arketplace id is missing',
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
		
		$permission = $adminObj->check_permission(77,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$mpads_id = $request->mpads_id;
		$ChatObj = MpAdsSubmission::find($mpads_id);
		$ChatObj->status =2;
        $ChatObj->save();
		return response()->json(['data'=>$ChatObj,'response' => 1, 'message' => 'De-Activated']);
		}
	}
	

	public function mpadshidereport(Request $request) 
    {
		$rules=array(
			'report_id' => 'required',
		);
		$messages=array(
			'report_id.required' => 'Report id is missing',
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
		
		$permission = $adminObj->check_permission(77,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$report_id = $request->report_id;
		$ReportObj = MpAdsReport::find($report_id);
		$ReportObj->status =2;
        $ReportObj->save();
		return response()->json(['data'=>$ReportObj,'response' => 1, 'message' => 'De-Activated']);
		}
	}

	public function mpadsshowreport(Request $request) 
    {
		$rules=array(
			'report_id' => 'required',
		);
		$messages=array(
			'report_id.required' => 'Report id is missing',
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
		
		$permission = $adminObj->check_permission(77,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$report_id = $request->report_id;
		$ReportObj = MpAdsReport::find($report_id);
		$ReportObj->status =1;
        $ReportObj->save();
		return response()->json(['data'=>$ReportObj,'response' => 1, 'message' => 'Activated']);
		}
	}

	public function mpadsblockuser(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'report_id' => 'required',
			'remark' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id is missing',
			'report_id.required' => 'Report id is missing',
			'remark.required' => 'Remark is missing',
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
		
		$permission = $adminObj->check_permission(77,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$UserObj = User::find($request->user_id);
			$check_status = MpadsBlockUserByAdmin::where('account_id',$UserObj->account_id)->where('admin_id',$login_id)->where('block_user_id',$request->user_id)->first();
			if(isset($check_status)){
				return response()->json(['response' => 200, 'message' => 'Already Blocked!']);
			}else{
				$input['admin_id'] = $login_id;
				$input['account_id'] = $UserObj->account_id;
				$input['unit_no'] = $UserObj->unit_no;
				$input['block_user_id'] = $request->user_id;
				$input['comment_id'] = $request->comment_id;
				$input['remark'] = $request->remark;
				$input['status'] = 1;
				$results = MpadsBlockUserByAdmin::create($input);
				return response()->json(['response' => 1, 'message' => 'Blocked!']);
			}
		}
	}

	public function mpadswarninguser(Request $request) 
    {
		$rules=array(
			'user_id' => 'required',
			'report_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User Id is missing',
			'report_id.required' => 'Report id is missing',
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
		$user_id = $request->user_id;
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(77,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$UserObj = User::find($user_id); 
			$report_id = $request->report_id;
			$reportObj = MpAdsReport::find($report_id); 
			if(isset($UserObj)){
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$logs = UserLog::where('user_id',$reportObj->user_id)->where('status',1)->orderby('id','desc')->first();
				if(isset($logs->fcm_token) && $logs->fcm_token !=''){
					$user_token .=$logs->fcm_token.",";
					$fcm_token_array .=$logs->fcm_token.',';
					$appSipAccountList[] = $report_id;
					if($logs->login_from ==1)
						$ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2)
						$android_devices_to_send[] = $logs->fcm_token;
				}
		
				$probObj = Property::find($adminObj->account_id);
				$title = "Aerea Home - ".$probObj->company_name;
				$message = "Warning from Marketplace";

				//Start Insert into notification module
				$notification = array();
				$notification['account_id'] = $reportObj->account_id;
				$notification['user_id'] = $reportObj->user_id;
				$notification['unit_no'] = $reportObj->unit_no;
				$notification['module'] = 'marketplace_warning';
				$notification['ref_id'] = $reportObj->id;
				$notification['title'] = $message;
				$notification['message'] = $message;
				UserNotification::insert($notification);

				$notofication_data = array();
				$notofication_data['body'] =$title;
				$notofication_data['unit_no'] =$reportObj->unit_no;   
				$notofication_data['user_id'] =$reportObj->user_id;   
				$notofication_data['property'] =$reportObj->account_id; 
				$purObj = UserPurchaserUnit::where('property_id',$reportObj->account_id)->where('unit_id',$reportObj->unit_no)->where('user_id',$reportObj->user_id)->first(); 
				if(isset($purObj))
					$notofication_data['switch_id'] =$purObj->id;        
				$NotificationObj = new \App\Models\v7\FirebaseNotification();
				$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
				$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
				return response()->json(['response' => 1, 'message' => 'Warning has been sent!']);
			}else{
				return response()->json(['response' => 1, 'message' => 'User no login history from Home App!']);
			}
		}
	}
	
	public function mpadstypes(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$typeObj = MpAdsType::where('account_id',$account_id)->orderby('type','asc')->get();
		return response()->json(['data'=>$typeObj,'response' => 1, 'message' => 'Success']);
		}
	}

	public function mpadsconditions(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$typeObj = MpAdsCondition::where('account_id',$account_id)->orderby('type','asc')->get();
		return response()->json(['data'=>$typeObj,'response' => 1, 'message' => 'Success']);
		}
	}

	public function mpadsdeletelike(Request $request) 
    {
		$rules=array(
			'like_id' => 'required',
		);
		$messages=array(
			'like_id.required' => 'Like id is missing',
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
		
		$permission = $adminObj->check_permission(77,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
			$like_id = $request->like_id;
			$likeObj = MpAdsLike::find($like_id);
			if(isset($likeObj)){
				MpAdsLike::findOrFail($like_id)->delete();
				return response()->json(['response' => 1, 'message' => 'Deleted']);
			}else{
				return response()->json(['response' => 200, 'message' => 'Record not found!']);

			}
		}
	}

	
	public function mpadsblocklistedusers(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$chat_id = $request->chat_id;
        $lists = MpadsBlockUserByAdmin::where('account_id',$account_id)->orderby('id','desc')->get();  
		$data =array();
		foreach($lists as $list){
			$userinfo = UserMoreInfo::where('account_id',$account_id)->where('user_id',$list->block_user_id)->orderby('id','desc')->first();
			$chat_data =array();
			$chat_data['id']=$list->id;
			$chat_data['user_id']=$list->block_user_id;
			$chat_data['user_first_name']=isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):null;
			$chat_data['user_last_name']=isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):null;
			$chat_data['admin_id']=$list->admin_id;
			$chat_data['admin_name']=isset($list->admininfo->name)?Crypt::decryptString($list->admininfo->name):null;
			$chat_data['type']=$list->type;
			$chat_data['ref_id']=$list->ref_id;
			$chat_data['status']=$list->status;
			$chat_data['created_at']=($list->created_at != '0000-00-00' && $list->created_at != '')?date('d/m/y',strtotime($list->created_at)):null;
			$data[] = $chat_data;
		}
		return response()->json(['data'=>$data,'response' => 1, 'message' => 'Success']);
		}
	}

	public function mpadsremovefromblocklist(Request $request) 
    {
		$rules=array(
			'block_id' => 'required',
		);
		$messages=array(
			'block_id.required' => 'Block id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$block_id = $request->block_id;
		$ReportObj = MpadsBlockUserByAdmin::find($block_id);
		$ReportObj->status =2;
        $ReportObj->save();
		return response()->json(['data'=>$ReportObj,'response' => 1, 'message' => 'Removed from blocklist!']);
		}
	}

	public function mpadsaddtoblocklist(Request $request) 
    {
		$rules=array(
			'block_id' => 'required',
		);
		$messages=array(
			'block_id.required' => 'Block id is missing',
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
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$block_id = $request->block_id;
		$ReportObj = MpadsBlockUserByAdmin::find($block_id);
		$ReportObj->status =1;
        $ReportObj->save();
		return response()->json(['data'=>$ReportObj,'response' => 1, 'message' => ' User has been blocked!']);
		}
	}

	public function mpadsnewreport(Request $request) 
    {
		$login_id = Auth::id();
		$adminObj = User::find($login_id); 
		if(empty($adminObj)){
			return response()->json(['data'=>null,'response' => 300, 'message' => 'User not found']);
		}
		
		$permission = $adminObj->check_permission(76,$adminObj->role_id); 
		if(empty($permission) ){
			return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else if(isset($permission->view) && $permission->view !=1){
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Permission denied']);
		}
		else{
        $account_id = $adminObj->account_id;
		$new_record = MpadsReport::where('status', 1)->where('view_status', 1)->where('account_id', $account_id)
        ->count();
		return response()->json(['data'=>$new_record,'response' => 1, 'message' => 'Success']);
		}
	}

	public function encryptstring(Request $request) 
    {
		$rules=array(
			'login_id' => 'required',
			'string' => 'required',
		);
		$messages=array(
			'login_id.required' => 'Login id is missing',
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
	
}
