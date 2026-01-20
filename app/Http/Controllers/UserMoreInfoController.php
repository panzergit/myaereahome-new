<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;


use App\Models\v7\Role;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\Unit;

use App\Models\v7\Employee;
use App\Models\v7\UserLog;
use App\Models\v7\Device;
use App\Models\v7\UserDevice;
use App\Models\v7\UserFacialId;
use App\Models\v7\UserRemoteDevice;
use App\Models\v7\Building;
use App\Models\v7\Setting;
use App\Models\v7\Module;
use App\Models\v7\UserPermission;
use App\Models\v7\Property;
use App\Models\v7\UserProperty;
use App\Models\v7\Country;
use App\Models\v7\Card;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\DefectSubmission;
use App\Models\v7\FeedbackSubmission;
use App\Models\v7\UnittakeoverAppointment;
use App\Models\v7\JoininspectionAppointment;
use App\Models\v7\FacilityBooking;
use App\Models\v7\ResidentFileSubmission;
use App\Models\v7\UserCard;
use App\Models\v7\ActivityLog;
use App\Models\v7\UserLicensePlate;


use Redirect;
use Auth;
use DB;
use File;
use Validator;
use Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class UserMoreInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $accountId =Auth::user()->account_id;
        
        $chartOne = $chartTwo = $chartThree = $chartFour = [];
        if(!$request->filled('view') && Auth::user()->role_id !=1)
        {
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
    		$totalHomeUsers = UserMoreInfo::where([
                    'status' => 1,
                    'account_id' => $accountId
                ])->whereIn('id',$totalHomeUsersLists->pluck('user_info_id'))->count();
            
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
                ->groupBy('user_id')->orderby('id','desc')->get();

           
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
    
    		/*$one_car = UserMoreInfo::has('licenseplates','=',1)->count();
            $two_cars = UserMoreInfo::has('licenseplates','=',2)->count();
    		$no_cars = $totalPropUsers - ($one_car+ $two_cars);
            */
            $totalUnits = Unit::where('account_id',$accountId)->where('status',1)->get()->count();
		    $one_car = Unit::where('account_id',$accountId)->has('licenseplates','=',1)->count();
            $two_cars = unit::where('account_id',$accountId)->has('licenseplates','=',2)->count();
		    $no_cars = $totalUnits - ($one_car+ $two_cars);
            
            $chartOne = [
                ['y' => $ownerUsers, 'name' => 'Owners', 'exploded' => true],
                ['y' => $familyMemberUsers, 'name' => 'Family Members', 'exploded' => false],
                ['y' => $occupantUsers, 'name' => 'Occupants', 'exploded' => false],
                ['y' => $domesticHelperUsers, 'name' => 'Domestic Helpers', 'exploded' => false],
                ['y' => $propertyAgentUsers, 'name' => 'Property Agents', 'exploded' => false],
                ['y' => $tenantUsers, 'name' => 'Tenants', 'exploded' => false],
                ['y' => $staffsCount, 'name' => 'Staffs', 'exploded' => false]
            ];
            
            $chartTwo = [
                'app_using' => [
                        'percentage' => number_format((($app_using_count/$totalHomeUsers)*100),2),
                        'numbers' => $app_using_count
                    ],
                'app_not_using' => [
                        'percentage' => number_format((($app_notusing_count/$totalHomeUsers)*100),2),
                        'numbers' => $app_notusing_count
                    ],
            ];
            
            $chartThree = [
                'android_usage' => [
                    'percentage' => $totalUsers->isNotEmpty() ? number_format((($androidUsageNumbers/$totalHomeUsers)*100),2) : 0,
                    'numbers' => $androidUsageNumbers
                ],
                'ios_usage' => [
                    'percentage' => $totalUsers->isNotEmpty() ? number_format((($iOsUsageNumbers/$totalHomeUsers)*100),2) : 0,
                    'numbers' => $iOsUsageNumbers
                ],
            ];
            
            $chartFour = [
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
            ];
            
        }
        
        
        $q= $option = $role = $name = $email = $last_name = $users = $building = $unit = $properties = $property = $property_name = $login_from = '';

        if(Auth::user()->role_id ==1){
            $userids = User::where('role_id',3)->orderby('id','desc')->get();
            foreach($userids as $k =>$v){
                $prop_userids[] = $v->id;
            }
            //print_r($prop_userids);
            $users = UserMoreInfo::where(function ($query) use ($prop_userids) {
                if($prop_userids !='')
                    $query->whereIn('user_id', $prop_userids);
               
            })->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
            
            $roles = Role::orderby('name','asc')->pluck('name', 'id')->all(); 
            $properties = Property::pluck('company_name', 'id')->all();
            $file_path = env('APP_URL')."/storage/app";
            return view('admin.users.employee', compact('users', 'q','roles','role','name','last_name','option','unit','file_path','property','properties','property_name','login_from', 'chartOne',
                'chartTwo', 'chartThree', 'chartFour'));
        } 
        else
        {
            $account_id = Auth::user()->account_id;
            $prop_userids = array();
            $userids = UserProperty::where('property_id',$account_id)->orderby('id','desc')->get();        
            foreach($userids as $k =>$v){
                $prop_userids[] = $v->user_id;
            }
            /*$user_prop_userids = array();
            $userids = UserPurchaserUnit::where('property_id',$account_id)->orderby('id','desc')->get();        
            foreach($userids as $k =>$v){
                $user_prop_userids[] = $v->user_id;
            }*/
            if(Auth::user()->id ==2946 || 1==1){#
                $allUnits = UserPurchaserUnit::select("id","user_info_id")->where('property_id',Auth::user()->account_id)->orderby('unit_id','DEsC')->get();
                $userinfoids = '';
                foreach($allUnits as $unitlist){
                    $userinfoids .= $unitlist->user_info_id.",";
                }   
                $userinfoids = substr($userinfoids,0,-1);
            
                $users = UserMoreInfo::whereNotIn('status',[2])->where(function ($query) use ($account_id,$prop_userids,$userinfoids) {
                if($account_id !='')
                    $query->where('account_id',$account_id);
                if($prop_userids !='')
                    $query->orwhereIn('user_id', $prop_userids);
                if(strlen($userinfoids)> 0) {
                    $query->orderByRaw(DB::raw("FIELD(id, $userinfoids) DESC"));
                }
                })->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
            }
            else{
                $users = UserMoreInfo::whereNotIn('status',[2])->where(function ($query) use ($account_id,$prop_userids) {
                if($account_id !='')
                    $query->where('account_id',$account_id);
                if($prop_userids !='')
                    $query->orwhereIn('user_id', $prop_userids);
                })->orderby('id','DESC')->paginate(env('PAGINATION_ROWS'));
            }
            //$users = User::Where('account_id', $account_id)->orwhereIn('id',$userids)->paginate(env('PAGINATION_ROWS'));
            $roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')->orWhere('id',3)->orWhere('type',1)->orderby('name','asc')->pluck('name', 'id')->all();
            $file_path = env('APP_URL')."/storage/app";
            session()->forget('searchpage');
            session()->forget('search_url');
            session()->forget('current_page');
            $currentURL = url()->full();
            $page = explode("=",$currentURL);
            if(isset($page[1]) && $page[1]>0){
                    session()->put('page', $page[1]);
            }else{
                    session()->forget('page');
            }
            $app_user_lists = explode(",",env('USER_APP_ROLE'));
            $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

            return view('admin.users.index', compact('users', 'q','roles','role','name','email','last_name','option','building','unit','file_path','account_id','app_user_lists','buildings','login_from', 'chartOne',
                'chartTwo', 'chartThree', 'chartFour'));
        }
    }

    

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $account_id = Auth::user()->account_id;
        $properties = Property::pluck('company_name', 'id')->all();
        $roles = Role::select('name', 'id','type')->WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')->orWhere('id',3)->orWhere('type',1)->orderby('type','asc')->orderby('name','asc')->get();
        $unites = Unit::where('account_id',$account_id)->pluck('unit', 'id')->all();
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        $allcountries = Country::whereNotIn('id',[1,2])->where('status',1)->orderby('country_name','ASC')->pluck('country_name', 'id')->all();
        $loccountries = Country::whereIn('id',[1,2])->where('status',1)->pluck('country_name', 'id')->all();
		$countries =$loccountries +  $allcountries ;
        $devices = UserDevice::where('account_id',$account_id)->get();
        return view('admin.users.create', compact('roles', 'unites','properties','buildings','devices','countries'));
    }

    public function password()
    {
    
        $account_id = Auth::user()->account_id;
        return view('admin.users.updatepassword', compact('account_id'));
    }

    public function savepassword(Request $request) 
    {
    
        //$user = Auth::user();
        $UserObj = Auth::user();

        $user = $UserObj->id;
		$old_password = $request->old_password;
		$password = $request->password;
        $confirmpassword = $request->confirmpassword;
        
		if($password == $confirmpassword) {	
	 
		   //$users =User::find($user);
		   $hashedPassword = $UserObj->password;
	 
		   if (\Hash::check($old_password , $hashedPassword )) {
	 
			 if (!\Hash::check($request->newpassword , $hashedPassword)) {
	 

				  $UserObj->password = bcrypt($password);
                  $result = User::where( 'id' , $UserObj->id)->update( array( 'password' =>  $UserObj->password));
                  return redirect("opslogin/configuration/updatepassword")->with('status', 'Password updated successfully');  

				}
	 
				else{
                    return redirect("opslogin/configuration/updatepassword")->with('status', 'New password can not be the old password!');  
					}
	 
			   }
	 
			  else{
                return redirect("opslogin/configuration/updatepassword")->with('status', 'Old password doesnt matched!');  
				 }
	 
		   }	
		   else {
            return redirect("opslogin/configuration/updatepassword")->with('status', 'Password and Confirm Password Mismatch!');  
					
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [ 
            'email' =>'email'
        ]);
        if ($validator->fails()) { 
            return redirect("opslogin/user/create")->withInput()->with('status', 'Invalid email format');         
       }
       $input = $request->all();
       $input['name'] = Crypt::encryptString($request->name);
       $input['last_name'] = Crypt::encryptString($request->last_name);
       $input['phone'] = Crypt::encryptString($request->phone);
       $input['encrypted']  =1;
       $user_roles = explode(",",env('USER_APP_ROLE'));
       
       if(in_array($input['role_id'],$user_roles))
       {
            if ($input['building_no'] =='') { 
                return redirect("opslogin/user/create")->withInput()->with('status', 'Building not selected');         
            }

            if (empty($input['unit_no'])) { 
                return redirect("opslogin/user/create")->withInput()->with('status', 'Unit not selected');         
            }

            $check_email_account = User::where('email',$input['email'])->whereIn('role_id',$user_roles)->first();
            if(isset($check_email_account)){
                $check_userinfo_data = UserMoreInfo::where('user_id',$check_email_account->id)->where('account_id',$input['account_id'])->whereNotIn('status',[2])->first();

                if(isset($check_userinfo_data)){
                    return redirect('opslogin/user/create')->withInput()->with('status', 'Email already register for this property!');
                }
            }
           /* $validator = Validator::make($request->all(), [ 
                'email' =>[
                    'required', 
                    Rule::unique('users')
                    ->where('account_id',$input['account_id'])
                    ->where('building_no',$input['building_no'])
                    ->where('unit_no',$input['unit_no'])
                    ->where('status',1)
                ],
                
            ]);
           
            if ($validator->fails()) { 
                return redirect("opslogin/user/create")->withInput()->with('status', 'Email already exist!');         
            } */

        }else{
            $check_email_account = User::where('email',$input['email'])->whereIn('role_id',$user_roles)->first();
            if(isset($check_email_account)){
                $check_userinfo_data = UserMoreInfo::where('user_id',$check_email_account->id)->whereNotIn('status',[2])->first();
                if(isset($check_userinfo_data)){
                    return redirect("opslogin/user/create")->withInput()->with('status', 'Email already registered as user!'); 
                }        
            }
            /*$validator = Validator::make($request->all(), [ 
                'email' =>[
                    'required', 
                    Rule::unique('users')
                    ->where('status',1)
                ],
            ]);
            if ($validator->fails()) { 
                return redirect("opslogin/user/create")->withInput()->with('status', 'Email already exist!');         
            }
            */
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
            return redirect('opslogin/user/create')->withInput()->with('status', 'Email already register as a employee!');
        }

        if(in_array($input['role_id'],$user_roles))
        {

            $check_email_account = User::where('email',$input['email'])->whereIn('role_id',$user_roles)->first();

            if(empty($check_email_account)){
                $input['password'] =null;
                $user = User::create($input);

                //$moreinfo = new \App\Models\v7\UserMoreInfo();
                //$opn_record = $moreinfo->opnaccount_creation($user->id,$user->account_id);

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
                $purchaser['created_at'] = date("Y-m-d H:i:s");
                $purchaser['updated_at'] = date("Y-m-d H:i:s");
                $purchaserunit = UserPurchaserUnit::create($purchaser);
            }
            else{
                //$moreinfo = new \App\Models\v7\UserMoreInfo();
                //$opn_record = $moreinfo->opnaccount_creation($check_email_account->id,$input['account_id']);

                $input['user_id'] = $check_email_account->id;
                $input['first_name'] = $input['name'];
                $check_userinfo_data = UserMoreInfo::where('user_id',$check_email_account->id)->where('account_id',$input['account_id'])->whereNotIn('status',[2])->first();

                if(isset($check_userinfo_data)){
                    /*$UserMoreObj = UserMoreInfo::find($check_userinfo_data->id);
                    $UserMoreObj->first_name = $input['name'];
                    $UserMoreObj->last_name = $input['last_name'];
                    $UserMoreObj->account_id = $input['account_id'];
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
                    $UserMoreObj->save();*/
                    $userinfo = UserMoreInfo::find($check_userinfo_data->id);
                    $userinfoid = $UserMoreObj->id;

                }else{
                    $userinfo = UserMoreInfo::create($input);
                    $userinfoid = $userinfo->id;
                }
                

                $user = $check_email_account;
                $purchaser['user_id'] = $check_email_account->id;
                $purchaser['user_info_id'] = $userinfoid;
                $purchaser['property_id'] = $input['account_id'];
                $purchaser['building_id'] = $input['building_no'];
                $purchaser['unit_id'] = $input['unit_no'];
                $purchaser['primary_contact'] = $input['primary_contact'];
                $purchaser['role_id'] = $input['role_id'];
                $purchaser['created_at'] = date("Y-m-d H:i:s");
                $purchaser['updated_at'] = date("Y-m-d H:i:s");
                $purchaserunit = UserPurchaserUnit::create($purchaser);
            }
        }
        else{
           
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
            $userinfo = UserMoreInfo::create($input);

            User::where('id', $user->id)
                ->update(['user_info_id' => $userinfo->id,'role_id'=>$input['role_id']]);
            
            $user = User::find($user->id);    
            
        }
        
        $auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();
        $name = Crypt::decryptString($userinfo->first_name)." ".Crypt::decryptString($userinfo->last_name);

        
        if(in_array($user->role_id,$user_roles)){
            
            $roomuuids = $purchaserunit->unit_id;
           
            if($roomuuids !=''){
                $api_obj = new \App\Models\v7\User();
                $household = $api_obj->household_add_api($thinmoo_access_token, $purchaserunit->property_id,$name,$purchaserunit->user_id,$roomuuids);
            }

        }else{

            $role_info =  $user->role;
            $property_input['user_id'] = $user->id;
            $property_input['property_id'] = $user->account_id;
            UserProperty::create($property_input);

            $emp_rec['account_id'] = $user->account_id;
            $emp_rec['name'] =  $name;
            $emp_rec['uuid'] =  $user->id; //
            $result = Employee::create($emp_rec);

            //print_r($result);
            //exit;
            $emp = new \App\Models\v7\Employee();
            $employee = $emp->employee_add_api($thinmoo_access_token,$result,$user->role_id);

            $role_obj = new \App\Models\v7\Role();
            $role_result = $role_obj->role_check_record($thinmoo_access_token,$user->account_id,$user->role_id);

            if($role_result['code'] !=0){
                $role_data = Role::where('id',$user->role_id)->first();
                //$parent_result = $role_obj->role_check_record($thinmoo_access_token,$user->account_id,3);
                //print_r($parent_result);
                $role_data->account_id = $user->account_id;
                $role_data->uuid = $role_data->id;
                $role_data->name = $role_data->name;
                $role_data->parentUuid = 3;

                $add_role_result = $role_obj->role_add_api($thinmoo_access_token,$user->account_id,$role_data);

                //print_r($add_role_result);
            }
           
        }
        //print_r($household);
       
            $new_values = "Id:". $user->id. ", Name:".$input['name'].", Last Name:".$input['last_name'].", Email:".$input['email'].", Phone:".$input['phone'];
            $log['module_id'] = 7;
            $log['account_id'] = $user->account_id;
            $log['admin_id'] = Auth::user()->id;
            $log['action'] = 1;
            $log['new_values'] = $new_values;
            $log['ref_id'] = $userinfo->id;
            $log['notes'] = 'User Created';
            $log = ActivityLog::create($log);

        //exit;
        //$redirect_url = 'opslogin/user?page='3
        return redirect('opslogin/user')->with('status', 'Record has been added!');
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
    public function edit($user_info_id)
    {
        //$user = Category::pluck('category','id')->all();
        $account_id = Auth::user()->account_id;
        $UserMoreInfoObj = UserMoreInfo::find($user_info_id);
        $id = $UserMoreInfoObj->user_id;
        $UserObj = User::find($id);
        $user_roles = explode(",",env('USER_APP_ROLE'));
        if(in_array($UserObj->role_id,$user_roles))
            $UserMoreInfoObj = UserMoreInfo::where('id', $user_info_id)->orderby('id','desc')->first();
        else
            $UserMoreInfoObj = UserMoreInfo::where('id', $user_info_id)->orderby('id','desc')->first();

        $properties = Property::orderby('company_name', 'asc')->pluck('company_name', 'id')->all();

        $roles = Role::select('name','id','type')->WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')->orWhere('id',3)->orWhere('type',1)->orderby('type','asc')->orderby('name','asc')->get();
        $unites = Unit::where('account_id',$account_id)->pluck('unit', 'id')->all();

        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        $devices = Device::where('account_id',$account_id)->get();
        $allcountries = Country::whereNotIn('id',[1,2])->where('status',1)->orderby('country_name','ASC')->pluck('country_name', 'id')->all();
        $loccountries = Country::whereIn('id',[1,2])->where('status',1)->pluck('country_name', 'id')->all();
		$countries =$loccountries +  $allcountries ;
        $userPurchaseRec = UserPurchaserUnit::where('user_info_id', $user_info_id)->orderby('id','desc')->first();


        if(@Auth::user()->role_id ==1)
            $agent_properties = Property::where('status',1)->get();
        else{
            $login_id = Auth::user()->id;
            $prop_ids = UserProperty::where('user_id',$login_id)->get();
            $agent_properties =array();
           
            if(isset($prop_ids)){
                $assigned_property = array();
                foreach($prop_ids as $prop_id){
                    $assigned_property[] = $prop_id->property_id;
                }
                $agent_properties = Property::whereIn('id',$assigned_property)->orderby('company_name','asc')->get();
            }
        }
        
        $role_access=array();
        foreach($UserObj->permissions as $permission){
            $role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);
        }
        //print_r($role_access);
        $device_access = array();
        foreach($UserObj->userdevices as $selecteddevices){
            $device_access[] = $selecteddevices->device_id;
           
        }
        $device_remote_access = array();
        foreach($UserObj->userremotedevices as $selectedremotedevices){
            $device_remote_access[] = $selectedremotedevices->device_id;
           
        }


        //print_r($UserObj->permissions);
        $file_path = env('APP_URL')."/storage/app";
        $all_modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();
       
        $modules =array();
        $property = new \App\Models\v7\Property();

        foreach($all_modules as $module){
            $permission =  $property->check_property_permission($module->id,$account_id,1);
            if(isset($permission) &&  $permission->view==1)
                $modules[] = $module;

        }

        $assigned_property = array();
        foreach($UserObj->userproperties as $userproperty){
                $assigned_property[] = $userproperty->property_id;

        }
        
        $app_user_lists = explode(",",env('USER_APP_ROLE'));

        if(Session::get('page') >0){
            $page = Session::get('page');
            $return_url = "opslogin/user?view=summary&page=$page";
        }
        else if(Session::get('searchpage') ==1){
            $return_url = Session::get('search_url');
        }
        else if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$UserObj->unit_no.'/1';
        }
        else
            $return_url = "opslogin/user?view=summary";

        
        return view('admin.users.edit', compact('UserObj','UserMoreInfoObj', 'roles','unites','modules','role_access','properties','file_path','buildings','devices','device_access','device_remote_access','agent_properties','assigned_property','app_user_lists','countries','return_url','userPurchaseRec'));
    }


    public function info($user_info_id)
    {
        //$user = Category::pluck('category','id')->all();
        session()->forget('current_page');

        $account_id = Auth::user()->account_id;
        $UserMoreInfoObj = UserMoreInfo::find($user_info_id);
        $id = $UserMoreInfoObj->user_id;
        $UserObj = User::find($id);
        $properties = Property::orderby('company_name', 'asc')->pluck('company_name', 'id')->all();

        $roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')->orWhere('type',1)->orderby('name','asc')->pluck('name', 'id')->all();
        $unites = Unit::where('account_id',$account_id)->pluck('unit', 'id')->all();

        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        $devices = Device::where('account_id',$account_id)->get();
        $countries = Country::where('status',1)->pluck('country_name', 'id')->all();

        $userPurchaseRec = UserPurchaserUnit::where('user_info_id', $user_info_id)->where('status',1)->orderby('id','desc')->first();

        $PurchaserUnits = UserPurchaserUnit::where('user_info_id', $user_info_id)->where('status',1)->where('property_id',$account_id)->get();

        if(@Auth::user()->role_id ==1)
            $agent_properties = Property::where('status',1)->get();
        else{
            $login_id = Auth::user()->id;
            $prop_ids = UserProperty::where('user_id',$login_id)->get();
            $agent_properties =array();
           
            if(isset($prop_ids)){
                $assigned_property = array();
                foreach($prop_ids as $prop_id){
                    $assigned_property[] = $prop_id->property_id;
                }
                $agent_properties = Property::whereIn('id',$assigned_property)->get();
            }
        }
        
        $role_access=array();
        foreach($UserObj->permissions as $permission){
            $role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);
        }
        //print_r($role_access);
        $device_access = array();
        foreach($UserObj->userdevices as $selecteddevices){
            $device_access[] = $selecteddevices->device_id;
           
        }
        $device_remote_access = array();
        foreach($UserObj->userremotedevices as $selectedremotedevices){
            $device_remote_access[] = $selectedremotedevices->device_id;
           
        }

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
							$user_bluetooth_device = UserDevice::where('account_id',$account_id)->where('unit_no',$EmpBuilding->building_id)->where('user_id',$id)->where('device_id',$device->id)->first();
								$devices_array['user_bluethooth_checked_status'] = isset($user_bluetooth_device)?1:0;
							
							$user_remote_device = UserRemoteDevice::where('account_id',$account_id)->where('unit_no',$EmpBuilding->building_id)->where('user_id',$id)->where('device_id',$device->id)->first();
								$devices_array['user_remote_checked_status'] = isset($user_remote_device)?1:0;
							$available_devices[] = $devices_array;
							
						}
					}
                    $record['devices'] = $available_devices;
					$data[] = $record;

                }
            }
            if(Session::get('searchpage') ==1){
                $search_url = Session::get('search_url');
                $return_url = $search_url;
                if(Session::get('page') >0){
                    $page = Session::get('page');
                    $return_url = $search_url."page=$page";
                }
            }
            else if(Session::get('page') >0){
                $page = Session::get('page');
                $return_url = "opslogin/user?view=summary&page=$page";
            }else{
                $return_url = 'opslogin/user?view=summary';
            }
            return view('admin.users.assignempdevices', compact('UserObj','UserMoreInfoObj','data','return_url'));

        }else{ //for user roles
            $userPurchaseRec = UserPurchaserUnit::where('user_info_id', $user_info_id)->where('status',1)->orderby('id','desc')->first();

            $PurchasedUnits = UserPurchaserUnit::where('user_info_id', $user_info_id)->where('status',1)->where('property_id',$account_id)->get();
			$data =array();
			if(isset($PurchasedUnits)){
				foreach($PurchasedUnits as $PurchasedUnit){
					$record = array();
					$record['id'] = $PurchasedUnit->id;
					$record['building_no'] = $PurchasedUnit->building_id;
					$record['building'] = isset($PurchasedUnit->addubuildinginfo->building)?$PurchasedUnit->addubuildinginfo->building:null;
					$record['unit_no'] = $PurchasedUnit->unit_id;
					$record['unit'] = isset($PurchasedUnit->addunitinfo->unit)?$PurchasedUnit->addunitinfo->unit:'';
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

							$user_bluetooth_device = UserDevice::where('account_id',$account_id)->where('unit_no',$PurchasedUnit->unit_id)->where('user_id',$id)->where('device_id',$device->id)->first();
								$devices_array['user_bluethooth_checked_status'] = isset($user_bluetooth_device)?1:0;
							
							$user_remote_device = UserRemoteDevice::where('account_id',$account_id)->where('unit_no',$PurchasedUnit->unit_id)->where('user_id',$id)->where('device_id',$device->id)->first();
								$devices_array['user_remote_checked_status'] = isset($user_remote_device)?1:0;
							$available_devices[] = $devices_array;
							
						}
					}
                    $record['devices'] = $available_devices;
                    $record['receive_call'] = $PurchasedUnit->receive_call;
					$data[] = $record;
				}
            }
        }

        //print_r($UserObj->permissions);
        $file_path = env('APP_URL')."/storage/app";
        $all_modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();
       
        $modules =array();
        $property = new \App\Models\v7\Property();

        foreach($all_modules as $module){
            $permission =  $property->check_property_permission($module->id,$account_id,1);
            if(isset($permission) &&  $permission->view==1)
                $modules[] = $module;

        }

        $assigned_property = array();
        foreach($UserObj->userproperties as $userproperty){
                $assigned_property[] = $userproperty->property_id;

        }
        
        $users = UserPurchaserUnit::where('property_id',$account_id)->where('user_info_id',$UserMoreInfoObj->id)->where('user_id',$id)->WhereIn('role_id',$user_roles)->where('status',1)->orderby('user_id','asc')->get();
					
			$userlists = array();
			$user_access=array();
			foreach($users as $user){
				$userdetail = array();
				$userdetail['id'] = $user->id;
				$userdetail['first_name'] = isset($user->usermoreinfo->first_name)?$user->usermoreinfo->first_name:null;
				$userdetail['last_name'] = isset($user->usermoreinfo->last_name)?$user->usermoreinfo->last_name:null;
				$userdetail['role'] = isset($user->role->name)?$user->role->name:null;
				$userdetail['user_id'] = $user->user_id;
				$userdetail['user_info_id'] = isset($user->usermoreinfo->id)?$user->usermoreinfo->id:null;
				$userdetail['building_id'] = $user->building_id;
				$userdetail['unit_id'] = $user->unit_id;
				$userdetail['building'] = isset($user->addubuildinginfo->building)?$user->addubuildinginfo->building:null;
				$userdetail['unit'] = isset($user->addunitinfo->unit)?"#".$user->addunitinfo->unit:null;
				

				$permissions = UserPermission::where('account_id',$account_id)->where('user_id',$user->user_id)->where('unit_no',$user->unit_id)->orderby('user_id','asc')->get();

				$role_access=array();
				foreach($permissions as $permission){
					$role_access[$permission->module_id] = $permission->view;      
				}
				$userdetail['access'] = !empty($role_access)?$role_access:null;
				$userlists[] = $userdetail;
				//$user_access[$user->id][$user->unit_id] = !empty($role_access)?$role_access:null;
			}
			
			//print_r($user_access);
			$all_modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();
       
			$modules =array();

			foreach($all_modules as $module){
				$permission =  $property->check_property_permission($module->id,$account_id,1);
				if(isset($permission) &&  $permission->view==1)
					$modules[] = !empty($module)?$module:null;

			}
            $license_plates = UserLicensePlate::where('user_info_id',$UserMoreInfoObj->id)->orderBy('id','DESC')->get();


        $app_user_lists = explode(",",env('USER_APP_ROLE'));

        
        return view('admin.users.info', compact('UserObj','UserMoreInfoObj', 'roles','unites','modules','role_access','properties','file_path','buildings','devices','device_access','device_remote_access','agent_properties','assigned_property','app_user_lists','countries','userPurchaseRec','PurchaserUnits','data','userlists','license_plates'));
    }

   


     public function rights($id)
    {
        //$user = Category::pluck('category','id')->all();

        $UserObj = User::find($id);

        foreach($UserObj->permissions as $permission){
            $role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);
           
        }

        //print_r($UserObj->permissions);
        $modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();
        return view('admin.users.rights', compact('UserObj','modules','role_access'));
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

        $login_id = Auth::user()->id;
        $UserObj = User::find($id);
        $env_roles 	= env('USER_APP_ROLE');
        $roles = explode(",",$env_roles);
        $account_id = Auth::user()->account_id;

        $old_values = "Id:". $UserObj->id. ", Name:".$UserObj->name.", Email:".$UserObj->email.", Phone:".$UserObj->phone;


        if(in_array($UserObj->role_id,$roles))
            $UserMoreObj = UserMoreInfo::where('account_id',$account_id)->where('user_id',$UserObj->id)->orderby('id','desc')->first();
        else
            $UserMoreObj = UserMoreInfo::find($UserObj->empinfo->id);

        $validator = Validator::make($request->all(), [ 
            'email' =>'email'
        ]);
        if ($validator->fails()) { 
            return redirect("opslogin/user/$UserMoreObj->id/edit")->withInput()->with('status', 'Invalid email format');         
       }
        
        $validator = Validator::make($request->all(), [ 
            'email' =>[
                'required', 
                Rule::unique('users_new')
                    ->whereNotIn('id',[$id])
            ],
            
        ]);

        if ($validator->fails()) { 
             return redirect("opslogin/user/$UserMoreObj->id/edit")->with('status', 'Account already exist!');         
        }

        
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
        
        
        /*if(isset($input['unit_no']) && in_array($input['role_id'],$roles))
            $UserObj->unit_no = $input['unit_no'];
        else
            $UserObj->unit_no = '';
        
        if(isset($input['building_no']) && in_array($input['role_id'],$roles))
            $UserObj->building_no = $input['building_no'];
        else
            $UserObj->building_no = '';

        if(isset($input['account_id']))
            $UserObj->account_id = $input['account_id'];
        else
            $UserObj->account_id = Auth::user()->account_id;
        
        $UserObj->primary_contact = isset($input['primary_contact'])?$input['primary_contact']:0;
        */
       
        $UserObj->save();
        
        if(isset($input['faceid_access_permission'])){
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

        $old_values = "Id:". $UserMoreObj->id. ", Name:".$UserMoreObj->first_name.", Last Name:".$UserMoreObj->last_name.", Email:".$UserObj->email.", Phone:".$UserMoreObj->phone;


       
            $userPurchaseId = UserPurchaserUnit::where('property_id', $account_id)->where('user_id',$UserObj->id)->orderby('id','desc')->first();
            $primary_contact =0;
            if(isset($input['primary_contact']))
                $primary_contact =1;

            if(isset($userPurchaseId->id))
                UserPurchaserUnit::where('id', $userPurchaseId->id)->update(['primary_contact' => $primary_contact,'role_id'=>$input['role_id']]);
           
        //for assign property to user agent
        $env_roles 	= explode(",",env('USER_APP_ROLE'));

        if(@Auth::user()->role_id ==1){ //by Superadmin
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
        else if(!in_array($UserObj->role_id,$env_roles)){ //by Agent login
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
          
            //UserProperty::where('user_id',$id)->delete();
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

        $user_roles = explode(",",env('USER_APP_ROLE'));

        if(!in_array($UserObj->role_id,$user_roles)){
            if($login_id ==1){
                if(isset($UserObj->userproperties)){
                    Employee::where('uuid', $UserObj->id)
                    ->update(['status' => 0]);

                    foreach($UserObj->userproperties as $property){
                        $emp_result = Employee::where('account_id',$property->property_id)->where('uuid',$UserObj->id)->orderby('id','desc')->first();
                        
                        $name = Crypt::decryptString($UserMoreObj->first_name)." ".Crypt::decryptString($UserMoreObj->last_name);
                       // exit;
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
                        /*echo "Property : ".$property->property_id."<br/>";
                        echo "Emp Id :".$EmpObj."<br/>";
                        echo "Check Status<br/>";
                        print_r($employee_result);
                        echo "Result Status<br/>";
                        print_r($employee);
                        echo "<hr />";*/
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

        $new_values = "Id:". $UserObj->id. ", Name:".$input['name'].", Last Name:".$input['last_name'].", Email:".$input['email'].", Phone:".$input['phone'];
        $log['module_id'] = 7;
        $log['account_id'] = $UserObj->account_id;
        $log['admin_id'] = Auth::user()->id;
        $log['action'] = 2;
        $log['old_values'] = $old_values;
        $log['new_values'] = $new_values;
        $log['ref_id'] = $UserMoreObj->id;
        $log['notes'] = 'User Updated';
        $log = ActivityLog::create($log);  
        //exit;      
       
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/user?page=$page")->with('status', 'Record has been updated!');
        }
        else if(Session::get('searchpage') ==1){
            $return_url = Session::get('search_url');
            return redirect($return_url)->with('status', 'Record has been updated!');
        }
        else if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$UserObj->unit_no.'/1';
            return redirect($return_url)->with('status', 'Record has been updated!');
        }
        else
            return redirect('opslogin/user')->with('status', 'Record has been updated!');
    }

    public function access()
    {
        $q= $option = $role = $name = $last_name = $users = $unit ='';
        $user_lists = explode(",",env('USER_APP_ROLE'));

        $account_id = Auth::user()->account_id;
        //$users = User::where('account_id',$account_id)->WhereIn('role_id',$user_lists)->get();
        //$roles = Role::where('account_id', 'LIKE', '%'.$account_id .'%')->orWhere('type',1)->pluck('name', 'id')->all();
        $users = UserPurchaserUnit::where('property_id',$account_id)->orderby('user_id','asc')->get();
        
        $user_access=array();
        foreach($users as $user){
            $role_access=array();
            $permissions = UserPermission::where('account_id',$account_id)->where('user_id',$user->user_id)->where('unit_no',$user->unit_id)->orderby('user_id','asc')->get();
            foreach($permissions as $permission){
                $role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);          
            }
            $user_access[$user->id] = $role_access;
        }
        $file_path = env('APP_URL')."/storage/app";
        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }

        $all_modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();
        $modules =array();
        $property = new \App\Models\v7\Property();
        foreach($all_modules as $module){
            $permission =  $property->check_property_permission($module->id,$account_id,1);
            if(isset($permission) &&  $permission->view==1)
                $modules[] = $module;

        }

        $roles = Role::WhereIn('id',$user_lists)->orderby('name','asc')->pluck('name', 'id')->all(); 
        return view('admin.users.access', compact('users', 'roles','file_path','modules','user_access','role'));
    }

    public function accesssearch(Request $request)
    {
        $q= $option = $role = $name = $last_name = $users = $unit = $building = '';
        $user_lists = explode(",",env('USER_APP_ROLE'));

        $account_id = Auth::user()->account_id;
        $role = $request->input('role');
        $user_lists = explode(",",env('USER_APP_ROLE'));

        $users = UserPurchaserUnit::where('property_id',$account_id)->where(function ($query) use ($account_id,$role,$building,$unit,$user_lists) {
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
        })->orderby('user_id','asc')->get();
        
        $user_access=array();
        foreach($users as $user){
            $role_access=array();
            $permissions = UserPermission::where('account_id',$account_id)->where('user_id',$user->user_id)->where('unit_no',$user->unit_id)->orderby('user_id','asc')->get();
            foreach($permissions as $permission){
                $role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);          
            }
            $user_access[$user->id] = $role_access;
        }
        $file_path = env('APP_URL')."/storage/app";
        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }

        $all_modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();
        $modules =array();
        $property = new \App\Models\v7\Property();
        foreach($all_modules as $module){
            $permission =  $property->check_property_permission($module->id,$account_id,1);
            if(isset($permission) &&  $permission->view==1)
                $modules[] = $module;

        }

        $roles = Role::WhereIn('id',$user_lists)->orderby('name','asc')->pluck('name', 'id')->all(); 
        return view('admin.users.access', compact('users', 'roles','file_path','modules','user_access','role'));
    }

    public function accessupdate(Request $request)
    {

        $user_lists = explode(",",env('USER_APP_ROLE'));
        $input = $request->all();
        $account_id = Auth::user()->account_id;
        $role = isset($input['role'])?$input['role']:'';
        //$users = User::where('account_id',$account_id)->WhereIn('role_id',$user_lists)->get();
        if($role !=''){
            $user_lists = array();
            $user_lists[] = $role;
        }
        $users = UserPurchaserUnit::where('property_id',$account_id)->WhereIn('role_id',$user_lists)->orderby('user_id','asc')->get();
        foreach($users as $puser){
            $purchaseUnitid = UserPurchaserUnit::where('id',$puser->id)->first();
            UserPermission::where('user_id',$purchaseUnitid->user_id)->where('unit_no',$purchaseUnitid->unit_id)->delete();
            $input['user_id'] = $purchaseUnitid->user_id;
            $modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();    
            foreach($modules as $module) {
                $data['account_id'] = $purchaseUnitid->property_id;
				$data['user_id'] = $purchaseUnitid->user_id;
				$data['unit_no'] = $purchaseUnitid->unit_id;
				$data['module_id'] = $module->id;
				$view_field = "mod_".$module->id."_pid_".$puser->id;
                if(isset($input[$view_field]))
                    {
                        $data['view'] = $data['create'] = $data['edit'] = $data['delete'] = 1;
                    }
                else
                    {
                        $data['view'] =   $data['create'] =  $data['edit'] =   $data['delete'] = 0;
                    }  
               UserPermission::create($data);  
            }
        }

        
        $log['module_id'] = 7;
        $log['account_id'] = $account_id;
        $log['admin_id'] = Auth::user()->id;
        $log['action'] = 6;
        $log['new_values'] = '';
        $log['ref_id'] = '';
        $log['notes'] = 'Bulk User Access Updated';
        $log = ActivityLog::create($log);
        
        if($role !=''){
            return redirect("opslogin/user/accesssearch?role=$role")->with('status', 'User access has been updated!');
        }
        else if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/user/access")->with('status', 'User access has been updated!');}
        else
            return redirect('opslogin/user/access')->with('status', 'User access has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\UserMoreInfo  $userMoreInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_info_id)
    {
            $UserMoreInfoObj = UserMoreInfo::find($user_info_id);
            $UserObj = User::find($UserMoreInfoObj->user_id);
            $account_id = Auth::user()->account_id;
			$user_roles = explode(",",env('USER_APP_ROLE'));
            $id = $UserMoreInfoObj->user_id;
			$deactivated_date = date("Y-m-d");
			if(in_array($UserObj->role_id,$user_roles)){
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

			}
			else{
                $UserObj = User::find($id);
                $result = UserMoreInfo::where( 'id' , $user_info_id)->delete();
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

        /*UserMoreInfo::where('user_id', $id)->where('account_id',$account_id)->delete();

        UserPermission::where('user_id', $id)->where('account_id',$account_id)->delete();

        UserLog::where('user_id', $id)->where('account_id',$account_id)->delete();

        JoininspectionAppointment::where('user_id', $id)->where('account_id',$account_id)->delete();

        UnittakeoverAppointment::where('user_id', $id)->where('account_id',$account_id)->delete();

        FeedbackSubmission::where('user_id', $id)->where('account_id',$account_id)->delete();

        FacilityBooking::where('user_id', $id)->where('account_id',$account_id)->delete();

        ResidentFileSubmission::where('user_id', $id)->where('account_id',$account_id)->delete();

        DefectSubmission::where('user_id', $id)->where('account_id',$account_id)->delete();

        $auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();
        
        $api_obj = new \App\Models\v7\User();
        $UserObj = User::find($id);

        $household_result = $api_obj->household_check_record($thinmoo_access_token,$UserObj);

        if($household_result['code'] ==0){
            $household = $api_obj->household_delete_api($thinmoo_access_token,$UserObj);
        }

        //$EmpObj = Employee::where('uuid', $id)->first();

        $emp_obj = new \App\Models\v7\Employee();

        if(isset($UserObj->userproperties)){

            foreach($UserObj->userproperties as $property){

                $emp_result = Employee::where('account_id',$property->property_id)->where('uuid',$UserObj->id)->orderby('id','desc')->first();

                if(isset($emp_result)){
                    $thinmoo_result = $emp_obj->employee_check_record($thinmoo_access_token,$emp_result);

                    if($thinmoo_result['code'] ==0){
                        $empresult = $emp_obj->employee_delete_record($thinmoo_access_token,$emp_result->account_id,$emp_result->id);;
                    
                    }
                }

            }
        }

       

      
        Employee::where('uuid', $id)->delete();

        User::findOrFail($id)->delete();*/


        $log['module_id'] = 7;
        $log['account_id'] = $account_id;
        $log['admin_id'] = Auth::user()->id;
        $log['action'] = 3;
        $log['new_values'] = '';
        $log['ref_id'] = $UserMoreInfoObj->id;
        $log['notes'] = 'User Deleted';
        $log = ActivityLog::create($log);

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$UserObj->unit_no.'/1';
            return redirect($return_url)->with('status', 'Record deleted successfully!');
        }
        else{
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/user?page=$page")->with('status', 'Record has been updated!');
            }
            else
                return redirect('opslogin/user')->with('status', 'Record deleted successfully!');
        }
        
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

    public function adminsearch(Request $request)
    {
        $q= $option = $role = $name = $email = $last_name = $users = $building = $unit = $property = $properties =$property_name ='';
        $option = $request->input('option'); 
        $name = $request->input('name');
        $email = $request->input('email');

        $last_name = $request->input('last_name');
        $property = $request->input('property');
        $app_user_lists = explode(",",env('USER_APP_ROLE'));

        //$userids =array();
       
            
        $rol_userids = array();
        $admin_ids = array();
        $userids = User::where('role_id',3)->orderby('id','desc')->get();
        foreach($userids as $k =>$v){
            $rol_userids[] = $v->id;
            $admin_ids[] = $v->id;
        }
        if($property !='')
        {   
            $admin_ids = array();
            $prop_userids = array();
            $prop_userids = UserProperty::where('property_id',$property)->orderby('id','desc')->get();        
            foreach($prop_userids as $k =>$v1){
                if(in_array($v1->user_id,$rol_userids))
                    $admin_ids[] = $v1->user_id;
            }
        }
        $email_userids =array();
        if($email !=''){
            $user_emailids = User::where('email', 'LIKE', '%' . $email . '%')->orderby('id','desc')->get();        
            foreach($user_emailids as $v3){
                $email_userids[] = $v3->id;
            }
        }

       
        $users = UserMoreInfo::where(function ($query) use ($name,$last_name,$email,$property,$userids,$admin_ids,$email_userids) {
            if($name !='')
                $query->where('first_name', 'LIKE', '%' . $name . '%');
            if($last_name !='')
                $query->where('last_name', 'LIKE', '%' . $last_name . '%');
            if(count($admin_ids) >0)
                $query->whereIn('user_id', $admin_ids);
            if($email !=''){
                    $query->whereIn('user_id', $email_userids);
            }
            

        })
        ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        $roles = Role::orderby('name','asc')->pluck('name', 'id')->all(); 
        $properties = Property::orderby('company_name', 'asc')->pluck('company_name', 'id')->all();
        $file_path = env('APP_URL')."/storage/app";
        if($property >0){
            $property_result = Property::select('company_name')->where('id',$property)->first();
            $property_name = $property_result->company_name;
        }

        return view('admin.users.employee', compact('users', 'q','roles','role','name','email','last_name','option','unit','file_path','property','properties','property_name'));
    
    }

    public function search(Request $request)
    {
        $q= $option = $role = $name = $email = $last_name = $users = $building = $unit = $login_from ='';
        $option = $request->input('option'); 
        $name = $request->input('name');
        $email = $request->input('email');
        $last_name = $request->input('last_name');
        $role = $request->input('role');
        $building = $request->input('building');
        $unit = $request->input('unit');
        $login_from = $request->input('login_from');
        $app_user_lists = explode(",",env('USER_APP_ROLE'));
        $account_id = Auth::user()->account_id;

        $units = array();
        if($unit !='' || $building !=''){   
            //$UnitClassObj = new Unit();
           /* $searchResults = Unit::where('account_id',$account_id)->get()
            ->filter(function ($unit) use ($request) {
                return $unit->getDecryptedUnit() === $request->input('unit');
            });*/


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
            
        })->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS')); 

        

        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        $roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')->orWhere('type',1)->orderby('name','asc')->pluck('name', 'id')->all();
        $file_path = env('APP_URL')."/storage/app";
        //echo Request::url();

        session()->forget('current_page');
        $currentURL = request()->getRequestUri();
        $page = explode("page=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
            session()->put('page', $page[1]);
        }else{
            session()->forget('page');
        }
        session()->put('searchpage', 1);
        session()->put('search_url', $page[0]);
        //$request()->getRequestUri();
        return view('admin.users.index', compact('users', 'q','roles','role','name','email','last_name','option','building','unit','file_path','app_user_lists','buildings','login_from'));
    }

    public function testsearch(Request $request)
    {
        $q= $option = $role = $name = $email = $last_name = $users = $building = $unit = '';
        $option = $request->input('option'); 
        $name = $request->input('name');
        $email = $request->input('email');
        $last_name = $request->input('last_name');
        $role = $request->input('role');
        $building = $request->input('building');
        $unit = $request->input('unit');
        $app_user_lists = explode(",",env('USER_APP_ROLE'));
        $account_id = Auth::user()->account_id;

        $units = array();
        if($unit !=''){     
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id')->where('account_id',$account_id)->where(function ($query) use ($building,$unit) {
            if($building !='')
                $query->where('building_id',$building);
            if($unit !='')
                $query->Where('unit', $unit);
            })->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    $units[] = $unitid->id;
                }
            }
        }
        //print_r($units);

        $userids =array();
        if($last_name !=''){
            $user_more_info = UserMoreInfo::where('last_name', 'LIKE', '%'.$last_name .'%')->orderby('id','desc')->get();
               foreach($user_more_info as $k =>$v){
                $userids[] = $v->user_id;
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
        $user_emailids = User::where('email', 'LIKE', '%' . $email . '%')->where('account_id',$account_id)->orderby('id','desc')->get();        
        foreach($user_emailids as $v3){
            $email_userids[] = $v3->id;
        }
        

        $users = UserMoreInfo::whereNotIn('status',[2])->where(function ($query) use ($name,$email,$email_userids,$last_name,$userids,$role,$unit,$units,$app_user_lists,$account_id,$building) {
            
            if($name !=''){
                $query->where('first_name', 'LIKE', '%' . $name . '%');
            }
            if($last_name !=''){
                $query->where('last_name', 'LIKE', '%' . $last_name . '%');
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
                        if(count($units)>0)
                            $subquery->WhereIn('unit_id', $units);
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
                }else{
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
                
            
        })->paginate(env('PAGINATION_ROWS')); 

        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        $roles = Role::orderby('name','asc')->pluck('name', 'id')->all();
        $file_path = env('APP_URL')."/storage/app";
        //echo Request::url();

        session()->forget('current_page');
        $currentURL = request()->getRequestUri();
        $page = explode("page=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
            session()->put('page', $page[1]);
        }else{
            session()->forget('page');
        }
        session()->put('searchpage', 1);
        session()->put('search_url', $page[0]);
        //$request()->getRequestUri();
        return view('admin.users.index', compact('users', 'q','roles','role','name','email','last_name','option','building','unit','file_path','app_user_lists','buildings'));
    }

    public function autocomplete(Request $request)
    {

        $term = $request->term;
        $employees = User::where('name', 'LIKE', "%" . $term . "%")->take(10)->get();

        $data = [];

        foreach ($employees as $key => $value) {
            $empname = $value->name;
            $data[] = ['id' => $value->id, 'value' => $empname];
        }
        return response()->json($data);
        $data = [];
    }



    public function autocompleteid(Request $request)
    {

        $term = $request->term;


       
        
        $employees =   DB::table('users')
                        ->select('users.id','users.name','user_more_infos.emp_id')
                        ->join('user_more_infos','user_more_infos.user_id','=','users.id')
                        ->where('user_more_infos.emp_id','like', "%" . $term . "%")
                        ->orwhere('users.name', 'LIKE', "%" . $term . "%")
                        ->take(10)
                        ->get();
       // $employees = User::where('name', 'LIKE', "%" . $term . "%")->take(10)->get();

        $data = [];

        foreach ($employees as $key => $value) {
            $empname = $value->name.'('.$value->emp_id.')';
            $data[] = ['id' => $value->id, 'value' => $empname];
        }
        return response()->json($data);
        $data = [];
    }

    public function getuser(Request $request)
    {
        
        $users = array();
        $role = $request->role;
        
        if($role=='a')
            $users = DB::table("users")->where("status",1)->orderby('name','asc')->pluck("name","id");
        else
            $users = DB::table("users")->where("status",1)->where("role_id",$role)->orderby('name','asc')->pluck("name","id");

        if(isset($users))
            return json_encode($users);
        else
             return json_encode($users);

       /*

        $employees =   DB::table('users')->select('id','name')->where('role_id', $role)->orderBy('name','asc')->get();
       // $employees = User::where('name', 'LIKE', "%" . $term . "%")->take(10)->get();

        $data = [];

        foreach ($employees as $key => $value) {
            $empname = $value->name;
            $data[] = ['id' => $value->id, 'value' => $empname];
        }
        return response()->json($data);
        $data = []; 
        */
    }

    public function getbuildingunitlists(Request $request)
    {
        
        $users = array();
        $building = $request->building;
        $units = Unit::select("unit","id")->where("building_id",$building)->orderby('id','asc')->get();

        if(isset($units)){
            $unit_data = array();
            foreach($units as $unit){
                $unit_data[$unit->id] = \Crypt::decryptString($unit->unit);
            }
            return json_encode($unit_data);
        }
        else
             return json_encode($units);

       
    }

    public function getunituserlists(Request $request)
    {
        
        $users = array();
        $unit = $request->unit;

        $unitids_byusers = UserPurchaserUnit::where('unit_id', $unit)->where('status',1)->get();
        $prop_userids =array();
        foreach($unitids_byusers as $k =>$v){
            $prop_userids[] = $v->user_id;
        }
        //print_r($prop_userids);

        $users = UserMoreInfo::whereNotIn('status',[2])->WhereIn('user_id',$prop_userids)->orderBy('user_id','DESC')->pluck("first_name","user_id");

        //$users = DB::table("users")->where("unit_no",$unit)->where("status",1)->orderby('name','asc')->pluck("name","id");

        if(isset($users))
            return json_encode($users);
        else
             return json_encode($users);

       
    }

    public function getunitusernewlists(Request $request)
    {
        
        $users = array();
        $unit = $request->unit;
        $role = $request->role;
        $unitids_byusers = UserPurchaserUnit::where('role_id', $role)->where('unit_id', $unit)->where('status',1)->get();
        $prop_userids =array();
        foreach($unitids_byusers as $k =>$v){
            $prop_userids[] = $v->user_info_id;
        }
        //print_r($prop_userids);

        $users = UserMoreInfo::whereNotIn('status',[2])->WhereIn('id',$prop_userids)->orderBy('first_name','ASC')->get();

        //$users = DB::table("users")->where("unit_no",$unit)->where("status",1)->orderby('name','asc')->pluck("name","id");
        $data = array();
		if(isset($users)){
			foreach($users as $user){
                    $result = array();
                    $result['id'] = $user->user_id;
                    $result['name'] = Crypt::decryptString($user->first_name)." ".Crypt::decryptString($user->last_name);
                    $data[] = $result;
			}
		}
        if(isset($data))
            return json_encode($data);
        else
             return json_encode($data);
       
    }

    public function getmanagerlists(Request $request)
    {
        
        $users = array();
        $account_id = Auth::user()->account_id;
        $role = $request->role;
        $app_user_lists = explode(",",env('USER_APP_ROLE'));
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


        if(isset($data))
            return json_encode($data);
        else
             return json_encode($data);

       
    }

    public function getroles(Request $request)
    {
        
        $users = array();
        $property = $request->property;

        $roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$property .',%')->orWhere('type',1)->orderBy('name','desc')->pluck('name', 'id')->all();
        if(isset($roles))
            return json_encode($roles);
        else
             return json_encode($roles);
    }

    public function getunits(Request $request)
    {
        
        $users = array();
        $property = $request->property;
        $building = $request->building;
        if($building !='')
            $unites = Unit::select('unit', 'id')->where('account_id',$property)->where('building_id',$building)->get();
        else    
            $unites = Unit::select('unit', 'id')->where('account_id',$property)->get();

        if(isset($unites) && $building !=''){
            $unit_data = array();
            foreach($unites as $unit){
                $unit_data[$unit->id] = Crypt::decryptString($unit->unit);
            }
            return json_encode($unit_data);
        }
            
        else
             return json_encode(null);
    }

    public function getblockunits(Request $request)
    {
        
        $users = array();
        $property = $request->property;
        $building = $request->building;
        $info_id = $request->info_id;

        $userunits = UserPurchaserUnit::where('property_id',$property)->where('user_info_id',$info_id)->get();
        $user_units = array();
        foreach($userunits as $userunit){
            $user_units[] = $userunit->unit_id;
        }
        $unites = Unit::select('unit', 'id')->whereIn('id',$user_units)->get();

        if(isset($unites) && $building !=''){
            $unit_data = array();
            foreach($unites as $unit){
                $unit_data[$unit->id] = Crypt::decryptString($unit->unit);
            }
            return json_encode($unit_data);
        }
            
        else
             return json_encode(null);
    }

    public function myprofile($name)
    {
        //$user = Category::pluck('category','id')->all();

        $UserObj = Auth::user();

        //$UserObj = User::find($id);

        $departments = Department::pluck('department', 'id')->all();

        $employment = EmploymentType::pluck('type', 'id')->all();

        $img_full_path = env('APP_URL') . "/storage/app/";

        $allowance_types = AllowanceType::all();
        if (isset($UserObj->allowances)) {
            foreach ($UserObj->allowances as $val) {
                $user_allowanzes[$val['type_id']] = $val['amount'];

            }
        }
        $leave_types = LeaveType::all();
        if (isset($UserObj->leaves)) {
            foreach ($UserObj->leaves as $val) {
                $taken = $val['total_days'] - $val['balance_days'];

                $user_total_leaves[$val['type_id']] = $val['total_days'];
                if ($taken <= 0) {
                    $user_taken_leaves[$val['type_id']] = 0;
                } else {
                    $user_taken_leaves[$val['type_id']] = $taken;
                }

                $user_balance_leaves[$val['type_id']] = $val['balance_days'];

            }
        }

        if (isset($UserObj->documents)) {
            foreach ($UserObj->documents as $k => $document) {
                $documents['id'] = $document['id'];
                $documents['file_title'] = $document['file_title'];
                $documents['file_name'] = $document['file_name'];

                $user_documents[$k + 1] = $documents;
            }
        }

        return view('admin.users.view', compact('UserObj', 'img_full_path', 'departments', 'employment', 'allowance_types', 'user_allowanzes', 'leave_types', 'user_total_leaves', 'user_taken_leaves', 'user_balance_leaves', 'user_documents'));
    }

    public function editprofile($name)
    {
        //$user = Category::pluck('category','id')->all();

        $UserObj = Auth::user();

        $img_full_path = env('APP_URL') . "/storage/app/";

        return view('admin.users.editprofile', compact('UserObj', 'img_full_path'));
    }

    public function switchproperty(Request $request)
    {

        $UserObj = Auth::user();
        $UserObj->account_id = $request->input('ag_prop');
        $UserObj->save();

        $emp_result = Employee::where('account_id',$UserObj->account_id)->where('uuid',$UserObj->id)->where('emp_type',1)->orderby('id','desc')->first();
        
        $name = $UserObj->name." ".$UserObj->empinfo->last_name;
        if(empty($emp_result)){
            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();

            $emp_rec['account_id'] = $UserObj->account_id;
            $emp_rec['name'] =  $name;
            $emp_rec['emp_type'] =  1;
            $emp_rec['status'] =  1;
            $emp_rec['uuid'] =  $UserObj->id; //
            $result = Employee::create($emp_rec);

            $emp = new \App\Models\v7\Employee();
            $employee = $emp->employee_add_api($thinmoo_access_token,$result,$UserObj->role_id);
        }

        return redirect("opslogin/home");

    }

    public function updateprofile(Request $request)
    {
        $UserObj = Auth::user();

        $UserObj->name = $request->input('name');
        $UserObj->save();
        $UserMoreObj = UserMoreInfo::find($UserObj->userinfo_fromadmin->id);
        $UserMoreObj->last_name = $request->input('last_name');
        $UserMoreObj->date_of_birth = date('Y-m-d', strtotime($request->input('date_of_birth')));
        $UserMoreObj->gender = $request->input('gender');
        $UserMoreObj->phone = $request->input('phone');
        $UserMoreObj->local_address = $request->input('local_address');
        $UserMoreObj->permanent_address = $request->input('permanent_address');

        $UserMoreObj->nationality = $request->input('nationality');
        $UserMoreObj->passport = $request->input('passport');
        $UserMoreObj->homephone = $request->input('homephone');
        $UserMoreObj->emergency_contact = $request->input('emergency_contact');
        $UserMoreObj->personal_email = $request->input('personal_email');
        $UserMoreObj->identification_no = $request->input('identification_no');

        if ($request->file('profile_picture') != null) {
            $UserMoreObj->profile_picture = $request->file('profile_picture')->store('documents');
        }

        $UserMoreObj->save();

        $log['module_id'] = 7;
        $log['account_id'] = $account_id;
        $log['admin_id'] = Auth::user()->id;
        $log['action'] = 2;
        $log['new_values'] = '';
        $log['ref_id'] = $UserMoreInfoObj->id;
        $log['notes'] = 'User Updated';
        $log = ActivityLog::create($log);

        return redirect("opslogin/myprofile/{$UserObj->name}")->with('status', 'Profile information has been updated!');
    }

    public function saveSalaryIncreament(Request $request)
    {
        $result = UserSalaryLogsModel::firstOrCreate([
            'user_id' => $request->user,
            'current_salary' => trim($request->curSalary),
            'new_salary' => trim($request->newSalary),
            'increament_amount' => trim($request->inc_salary),
            'reason' => trim($request->salReason) != '' ? trim($request->salReason) : '',
            'increament_date' => date('Y-m-d', strtotime($request->inc_salary_date)),
        ]);

        return response()->json([
            'status' => $result ? 1 : 0,
        ]);
    }

    public function listSalaryIncreament(Request $request)
    {
        return response()->json([
            'data' => UserSalaryLogsModel::select([DB::raw('current_salary as lastSalary,new_salary as newSalary,increament_amount as increaAmt,
                DATE_FORMAT(increament_date,"%d-%m-%Y") as increaDate'), 'reason'])
                ->where('user_id', $request->user)->latest()->get(),
        ]);
    }


    function csvToArray($filename = '', $delimiter = ',')
    {
            if (!file_exists($filename) || !is_readable($filename))
                return false;

            $header = null;
            $data = array();
            if (($handle = fopen($filename, 'r')) !== false)
            {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
                {
                    if (!$header)
                        $header = $row;
                    else{
                        $records = array();
                        foreach($row as $k => $rec){
                           

                            if($k ==4){
                                $unit_no = str_replace("#",'',$rec);
                                $unit = Unit::where("unit",$unit_no)->where("account_id",Auth::user()->account_id)->first();
                                if(isset($unit) && $unit->id >0)
                                    $records[$header[$k]] = $unit->id;
                                else
                                    $records[$header[$k]] = '';
                            }
                            else{
                                $records[$header[$k]] = $rec;
                            }

                           
                        }
                        $records['role_id'] = 2;
                        $records['account_id'] = Auth::user()->account_id;
                        $data[] = $records;
                        
                    }
                        
                }
                fclose($handle);
            }

            return $data;
    }

    public function importcsv_old()
    {
        
        $file = public_path('import/'.Auth::user()->account_id.'/customer.csv');

        $customerArr = $this->csvToArray($file);

        for ($i = 0; $i < count($customerArr); $i ++)
        {
           
            $user = User::create($customerArr[$i]);
        
            $customerArr[$i]['user_id'] = $user->id;

            $userinfo = UserMoreInfo::create($customerArr[$i]);
        }

        return redirect('opslogin/user')->with('status', 'Record has been imported from CSV!');
    }


    public function logout(){
        auth()->logout();
        return redirect()->intended('opslogin');
     }

     public function logs()
    {
        $q= $option = $role = $name = $last_name = $users = $unit = $property ='';

       if(Auth::user()->role_id ==1){
        $logs = UserLog::paginate(env('PAGINATION_ROWS'));
        $roles = Role::orderby('name','asc')->pluck('name', 'id')->all(); 
        $properties = Property::orderby('company_name', 'asc')->pluck('company_name', 'id')->all(); 
       } else{
        return redirect("opslogin/user")->with('status', 'Access denied!');         


       }
        
         return view('admin.users.logs', compact('logs','properties', 'q','roles','role','name','last_name','option','unit','property'));
    }


     public function logsearch(Request $request)
    {
       $q= $option = $role = $name = $last_name = $users ='';
       $option = $request->input('option'); 

       $property = $request->input('property');
       $name = $request->input('name');
       $role = $request->input('role');
       $unit = $request->input('unit');

       $account_id = Auth::user()->account_id;

        if ($option != '') {
            if($option == 'property') {
                $logs = UserLog::where('account_id',$property)->paginate(env('PAGINATION_ROWS'));
            }

            if($option == 'name') {
                $users = User::where(function ($query) use ($name) {
                        $query->where('name', 'LIKE', '%' . $name . '%');
                        //$query->orWhere('last_name', 'LIKE', '%' . $name . '%');
                    })->orderby('id','desc')->get();
                
                foreach($users as $k =>$v){
                        $userids[] = $v->id;
                }

                $logs = UserLog::whereIn('user_id', $userids)->paginate(env('PAGINATION_ROWS'));
            }
            

            if($option == 'role') {
                $users = User::where(function ($query) use ($role) {
                        $query->where('role_id', '=', $role);
                    })->get();
                
                foreach($users as $k =>$v){
                        $userids[] = $v->id;
                }

                $logs = UserLog::whereIn('user_id', $userids)->paginate(env('PAGINATION_ROWS'));
            }

            if($option == 'unit' ) { 
                $units= Unit::select('id')->where('unit',$unit)->get();
                $unitids =array();
                foreach($units as $k1 =>$v1){
                    $unitids[] = $v1->id;
                }
                
                $users = User::whereIn('unit_no',$unitids)->get();               
                $userids =array();
                foreach($users as $k =>$v){
                        $userids[] = $v->id;
                }
                
                $logs = UserLog::whereIn('user_id', $userids)->paginate(env('PAGINATION_ROWS'));
            }
           

            $roles = Role::orderby('name','asc')->pluck('name', 'id')->all(); 
            $properties = Property::orderby('company_name', 'asc')->pluck('company_name', 'id')->all(); 
            
            return view('admin.users.logs', compact('logs','properties', 'q','roles','role','name','last_name','option','unit','property'));

        } else {
            return redirect('opslogin/logsearch');
        }
    }



    public function activate($user_more_info_id) //
    {
        $account_id = Auth::user()->account_id;
        $UserMoreInfoObj = UserMoreInfo::find($user_more_info_id);
        $UserObj = User::find($UserMoreInfoObj->user_id);
        $user_roles = explode(",",env('USER_APP_ROLE'));
        $id =  $UserObj->id;
        $deactivated_date = '0000-00-00';
        if(in_array($UserObj->role_id,$user_roles)){
            $result = UserMoreInfo::where( 'id' , $user_more_info_id)->update( array( 'status' => 1,'deactivated_date'=>$deactivated_date));
            $userinfo =  UserMoreInfo::where( 'id' , $user_more_info_id)->orderby('id','desc')->first();
            $name = Crypt::decryptString($userinfo->first_name)." ".Crypt::decryptString($userinfo->last_name);
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
        $log['admin_id'] = Auth::user()->id;
        $log['action'] = 4;
        $log['new_values'] = '';
        $log['ref_id'] = $UserMoreInfoObj->id;
        $log['notes'] = 'User Activated';
        $log = ActivityLog::create($log);

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$UserObj->unit_no.'/1';
            return redirect($return_url)->with('status', 'User account activated!');
        }
        else{
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/user?page=$page")->with('status', 'User account activated!');
            }
            else if(Session::get('searchpage') ==1){
                $return_url = Session::get('search_url');
                return redirect($return_url)->with('status', 'User account activated!');
            }
            else
                return redirect('opslogin/user')->with('status', 'User account activated!');
        }

    }

    

    public function deactivate($user_more_info_id)
    {
        $UserMoreInfoObj = UserMoreInfo::find($user_more_info_id);
        $UserObj = User::find($UserMoreInfoObj->user_id);
        $account_id = Auth::user()->account_id;
        $user_roles = explode(",",env('USER_APP_ROLE'));
        $id = $UserObj->id;
        $deactivated_date = date("Y-m-d");
        if(in_array($UserObj->role_id,$user_roles)){
            $result = UserMoreInfo::where( 'id' , $user_more_info_id)->whereNotIn( 'status' , [2])->update( array( 'status' => 0,'deactivated_date'=>$deactivated_date));
            $userinfo =  UserMoreInfo::where( 'id' , $user_more_info_id)->orderby('id','desc')->first();
            $name = Crypt::decryptString($userinfo->first_name)." ".Crypt::decryptString($userinfo->last_name);
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
        $log['admin_id'] = Auth::user()->id;
        $log['action'] = 5;
        $log['new_values'] = '';
        $log['ref_id'] = $UserMoreInfoObj->id;
        $log['notes'] = 'User Deactivated';
        $log = ActivityLog::create($log);

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$UserObj->unit_no.'/1';
            return redirect($return_url)->with('status', 'User account de-activated!');
        }
        else{
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/user?page=$page")->with('status', 'User account de-activated!');
            }
            else if(Session::get('searchpage') ==1){
                $return_url = Session::get('search_url');
                return redirect($return_url)->with('status', 'User account de-activated!');
            }
            else
                return redirect('opslogin/user')->with('status', 'User account de-activated!');
        }

    }

    public function userunits($user_info_id)
    {
        $account_id = Auth::user()->account_id;
        $UserMoreInfoObj = UserMoreInfo::find($user_info_id);
        if($UserMoreInfoObj->status !=1){
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/user?page=$page")->with('status', 'Sorry, Account is not Active!');
            }
            else if(Session::get('searchpage') ==1){
                $return_url = Session::get('search_url');
                return redirect($return_url)->with('status', 'Sorry, Account is not Active!');
            }
            else
                return redirect("opslogin/user")->with('status', 'Sorry, Account is not Active!');         

        }
        $id = $UserMoreInfoObj->user_id;
        $UserObj = User::find($id);
        //$UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $id)->where( 'account_id' , $account_id)->orderby('id','desc')->first();
        $PurchaserUnits = UserPurchaserUnit::where('user_info_id',$UserMoreInfoObj->id)->where('property_id',$account_id)->where('status',1)->get();
        $unites = Unit::where('account_id',$account_id)->pluck('unit', 'id')->all();
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        $app_user_lists = explode(",",env('USER_APP_ROLE'));
        $roles = Role::WhereIn('id',$app_user_lists)->orderby('name','asc')->pluck('name', 'id')->all();

        if(Session::get('searchpage') ==1){
            $search_url = Session::get('search_url');
            $return_url = $search_url;
            if(Session::get('page') >0){
                $page = str_replace("&view","",Session::get('page'));
                $return_url = $search_url."page=$page";
            }
        }
        else if(Session::get('page') >0){
            $page = str_replace("&view","",Session::get('page'));
            $return_url = "opslogin/user?view=summary&page=$page";
        }else{
            $return_url = 'opslogin/user?view=summary';
        }
        return view('admin.users.assignedunits', compact('UserObj','UserMoreInfoObj','PurchaserUnits','buildings','unites','return_url','roles'));
    }

    public function assignunit(Request $request){

        $input = $request->all();
        $account_id = Auth::user()->account_id;

        $UserMoreInfoObj = UserMoreInfo::find($input['user_more_info_id']);
        $userObj = User::find($UserMoreInfoObj->user_id);
        //$UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $userObj->id)->where( 'account_id' , $userObj->account_id)->first();
        $user_roles = explode(",",env('USER_APP_ROLE'));
        if(in_array($input['role_id'],$user_roles))
        {
            if ($input['building_no'] =='') { 
                return redirect("opslogin/user/userunits/".$input['user_more_info_id'])->withInput()->with('status', 'Building not selected');         
            }

            if (empty($input['unit_no'])) { 
                return redirect("opslogin/user/userunits/".$input['user_more_info_id'])->withInput()->with('status', 'Unit not selected');         
            }

            $validator = Validator::make($request->all(), [ 
                'user_id' =>[
                    'required', 
                    Rule::unique('user_purchaser_units')
                    ->where('user_info_id',$UserMoreInfoObj->id)
                    ->where('property_id',$input['account_id'])
                    ->where('building_id',$input['building_no'])
                    ->where('unit_id',$input['unit_no'])
                ],
                
            ]);
            
            if ($validator->fails()) { 
                return redirect("opslogin/user/userunits/".$input['user_more_info_id'])->withInput()->with('status', 'Unit assigned already!');         
            }

            
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
            //end
            $purchaser['user_id'] = $userObj->id;
            $purchaser['user_info_id'] = $UserMoreInfoObj->id;
            $purchaser['property_id'] = $input['account_id'];
            $purchaser['building_id'] = $input['building_no'];
            $purchaser['unit_id'] = $input['unit_no'];
            $purchaser['card_no'] = isset($input['card_no'])?$input['card_no']:null;
            $purchaser['role_id'] = $input['role_id'];
            $purchaser['primary_contact'] = isset($input['primary_contact'])?$input['primary_contact']:0;

            UserPurchaserUnit::create($purchaser);
            //$cards =  isset($input['card_no'])?$input['card_no']:null;
           
            $name = Crypt::decryptString($UserMoreInfoObj->first_name)." ".Crypt::decryptString($UserMoreInfoObj->last_name);
    
                $accountinfos = UserPurchaserUnit::where('user_id',$userObj->id)
                ->where('user_info_id',$UserMoreInfoObj->id)
                ->where('property_id',$input['account_id'])
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
                    $household_result = $api_obj->household_check_record($thinmoo_access_token,$userObj,$input['account_id']);
                    
                    if($household_result['code'] ==0){
                        $household = $api_obj->household_modify_api($thinmoo_access_token, $input['account_id'],$name,$userObj->id,$roomuuids);
                    }
                    else{
                        $household = $api_obj->household_add_api($thinmoo_access_token, $input['account_id'],$name,$userObj->id,$roomuuids);
                    }
                    
                }

                $log['module_id'] = 7;
                $log['account_id'] = $account_id;
                $log['admin_id'] = Auth::user()->id;
                $log['action'] = 7;
                $log['new_values'] = '';
                $log['ref_id'] = $UserMoreInfoObj->id;
                $log['notes'] = 'User Unit Assigned';
                $log = ActivityLog::create($log);

               // exit;
            return redirect("opslogin/user/userunits/".$input['user_more_info_id'])->withInput()->with('status', 'Unit assigned!');         
        }

    }

    public function assignunitupdate($id){

        $account_id = Auth::user()->account_id;
        $purchaseObj = UserPurchaserUnit::find($id);

        $UserMoreInfoObj = UserMoreInfo::find($purchaseObj->user_info_id);
        $userObj = User::find($purchaseObj->user_id);
        //$UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $userObj->id)->where( 'account_id' , $userObj->account_id)->first();
    
        $user_roles = explode(",",env('USER_APP_ROLE'));
        if(in_array($purchaseObj->role_id,$user_roles))
        {
            

            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();

            //checking units avbailablility in thinmoo start
            $unitObj = Unit::find($purchaseObj->unit_id);
            $api_obj = new \App\Models\v7\Unit();
            $unit_result = $api_obj->unit_check_record($thinmoo_access_token,$unitObj);
            if($unit_result['code'] !=0){
                $unit_info= $api_obj->unit_add_api($thinmoo_access_token,$unitObj);
            }
            //end
           /* $purchaser['user_id'] = $purchaseObj->user_id;
            $purchaser['user_info_id'] = $purchaseObj->user_info_id;
            $purchaser['property_id'] = $purchaseObj->property_id;
            $purchaser['building_id'] = $purchaseObj->building_no;
            $purchaser['unit_id'] = $purchaseObj->unit_id;
            $purchaser['role_id'] = $purchaseObj->role_id;
            $purchaser['primary_contact'] = $purchaseObj->primary_contact;

            UserPurchaserUnit::create($purchaser);*/
            //$cards =  isset($input['card_no'])?$input['card_no']:null;
           
            $name = Crypt::decryptString($UserMoreInfoObj->first_name)." ".Crypt::decryptString($UserMoreInfoObj->last_name);
    
                $accountinfos = UserPurchaserUnit::where('user_id',$userObj->id)
                ->where('user_info_id',$UserMoreInfoObj->id)
                ->where('property_id',$purchaseObj->property_id)
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
                    $household_result = $api_obj->household_check_record($thinmoo_access_token,$userObj,$purchaseObj->property_id);
                    
                    if($household_result['code'] ==0){
                        $household = $api_obj->household_modify_api($thinmoo_access_token, $purchaseObj->property_id,$name,$userObj->id,$roomuuids);
                    }
                    else{
                        /*echo $purchaseObj->property_id. "<br >";
                        echo $name. "<br >";
                        echo $userObj->id. "<br >";
                        print_r($roomuuids). "<br >";*/
                        $household = $api_obj->household_add_api($thinmoo_access_token, $purchaseObj->property_id,$name,$userObj->id,$roomuuids);
                    }
                    
                }

             
                $log['module_id'] = 7;
                $log['account_id'] = $account_id;
                $log['admin_id'] = Auth::user()->id;
                $log['action'] = 7;
                $log['new_values'] = '';
                $log['ref_id'] = $UserMoreInfoObj->id;
                $log['notes'] = 'User Unit Assigned';
                $log = ActivityLog::create($log);

               // exit;
            return redirect("opslogin/user/userunits/".$purchaseObj->user_info_id)->withInput()->with('status', 'Unit assigned!');         
        }

    }

    public function deleteunit($id){
        $account_id = Auth::user()->account_id;

        $purchaserObj = UserPurchaserUnit::find($id);
        $userObj = User::find($purchaserObj->user_id);
        $UserMoreInfoObj = UserMoreInfo::where('id' , $purchaserObj->user_info_id)->first();
        //echo $UserMoreInfoObj->id;
        //exit;
        UserPurchaserUnit::findOrFail($id)->delete(); 
        //Update User Current Unit -START
        if(!empty($userObj)){
            $currentUnitObj = UserPurchaserUnit::where('user_id',$userObj->id)
            ->where('user_info_id',$UserMoreInfoObj->id)->orderby('id', 'asc')->first();
            if($userObj->unit_no == $purchaserObj->unit_id){
                User::where('id',$purchaserObj->user_id)->update(array('unit_no'=>$currentUnitObj->unit_id,"building_no"=>$currentUnitObj->building_id,"role_id"=>$currentUnitObj->role_id,"primary_contact"=>$purchaserObj->primary_contact));
            }
        }
        //Update User Current Unit -END
        $auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();
        $name = Crypt::decryptString($userObj->name)." ".Crypt::decryptString($userObj->userinfo_fromadmin->last_name);

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
                //$api_obj = new \App\Models\v7\User();
               
            }

            $log['module_id'] = 7;
            $log['account_id'] = $account_id;
            $log['admin_id'] = Auth::user()->id;
            $log['action'] = 8;
            $log['new_values'] = '';
            $log['ref_id'] = $UserMoreInfoObj->id;
            $log['notes'] = 'User Unit Deleted';
            $log = ActivityLog::create($log);

        return redirect("opslogin/user/userunits/".$UserMoreInfoObj->id)->withInput()->with('status', 'Unit has been deleted!');         


    }
    public function deleteuserunit($id){
        $account_id = Auth::user()->account_id;

        $purchaserObj = UserPurchaserUnit::find($id);
        $userObj = User::find($purchaserObj->user_id);
        $UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $userObj->id)->where( 'account_id' , $userObj->account_id)->first();

        UserPurchaserUnit::findOrFail($id)->delete(); 

        $auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();
        $name = Crypt::decryptString($userObj->name)." ".Crypt::decryptString($userObj->userinfo_fromadmin->last_name);

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
               // $api_obj = new \App\Models\v7\User();
               
            }
                $values = 'Id='.$id.", Unit Id=".$purchaserObj->unit_id;

                $log['module_id'] = 7;
                $log['account_id'] = $account_id;
                $log['admin_id'] = Auth::user()->id;
                $log['action'] = 8;
                $log['new_values'] = $values;
                $log['ref_id'] = $UserMoreInfoObj->id;
                $log['notes'] = 'User Unit Deleted';
                $log = ActivityLog::create($log);

        return redirect("opslogin/unit_summary/".$purchaserObj->unit_id/1)->with('status', 'Assigned unit has been deleted!');         

    }

    public function activateuserunit($id){
        $account_id = Auth::user()->account_id;

        $purchaserObj = UserPurchaserUnit::find($id);
        $UserMoreInfoObj = UserMoreInfo::find($purchaserObj->user_info_id);

        UserPurchaserUnit::where('id', $purchaserObj->id)
        ->update(['status' => 1]);

        $values = 'Id='.$id.", Unit Id=".$purchaserObj->unit_id;
        $log['module_id'] = 7;
        $log['account_id'] = $account_id;
        $log['admin_id'] = Auth::user()->id;
        $log['action'] = 9;
        $log['new_values'] = $values;
        $log['ref_id'] = $UserMoreInfoObj->id;
        $log['notes'] = 'User Activated for Unit';
        $log = ActivityLog::create($log);

        return redirect("opslogin/unit_summary/".$purchaserObj->unit_id/1)->with('status', 'Activated!');         

    }

    public function deactivateuserunit($id){
        $account_id = Auth::user()->account_id;

        $purchaserObj = UserPurchaserUnit::find($id);
        $UserMoreInfoObj = UserMoreInfo::find($purchaserObj->user_info_id);

        UserPurchaserUnit::where('id', $purchaserObj->id)
        ->update(['status' => 0]);

        $values = 'Id='.$id.", Unit Id=".$purchaserObj->unit_id;
        $log['module_id'] = 7;
        $log['account_id'] = $account_id;
        $log['admin_id'] = Auth::user()->id;
        $log['action'] = 9;
        $log['new_values'] = $values;
        $log['ref_id'] = $UserMoreInfoObj->id;
        $log['notes'] = 'User Deactivated for Unit';
        $log = ActivityLog::create($log);

        return redirect("opslogin/unit_summary/".$purchaserObj->unit_id/1)->with('status', 'De-Activated!');         

    }


    public function userdevices($user_info_id)
    {
        $account_id = Auth::user()->account_id;
        $UserMoreInfoObj = UserMoreInfo::find($user_info_id);
        if($UserMoreInfoObj->status !=1){
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/user?page=$page")->with('status', 'Sorry, Account is not Active!');
            }
            else if(Session::get('searchpage') ==1){
                $return_url = Session::get('search_url');
                return redirect($return_url)->with('status', 'Sorry, Account is not Active!');
            }
            else
                return redirect("opslogin/user")->with('status', 'Sorry, Account is not Active!');          

        }
        $id = $UserMoreInfoObj->user_id;
        $UserObj = User::find($id);
        $user_roles = explode(",",env('USER_APP_ROLE'));
        if(!in_array($UserObj->role_id,$user_roles)){ //for employee roles
            $EmpBuildings = Building::where('account_id',$account_id)->get();
            $data =array();
            if(isset($EmpBuildings)){
				foreach($EmpBuildings as $EmpBuilding){
                    $record = array();
					$record['id'] = $EmpBuilding->id;
					$record['building'] = $EmpBuilding->building;
					//$devices = Device::where('locations',$EmpBuilding->id)->where('account_id',$account_id)->get();
                    $devices = Device::WhereRaw("CONCAT(',',locations,',') LIKE ?", '%,'.$EmpBuilding->id .',%')->where('account_id',$account_id)->get();
					$available_devices = array();
					if(isset($devices)){
						foreach($devices as $device){
							$devices_array = array();
							$devices_array['id'] = $device->id;
							$devices_array['device_name'] = $device->device_name;
							$devices_array['device_serial_no'] = $device->device_serial_no;
							$devices_array['model'] = $device->model;
							$devices_array['location_id'] = $device->locations;
                            $deviceClsObj = new \App\Models\v7\Device();
                            $locations = $deviceClsObj->getLocations($device->locations); 
                            $devices_array['location'] = '';
                            $loc_str = '';
                            if($locations !=''){
                                foreach($locations as $loc){
                                    $loc_str .= $loc->building."<br/> ";
                                }
                             }
                            //$loc_str = substr($loc_str,0,-2);
                            $devices_array['location'] = $loc_str;
							$user_bluetooth_device = UserDevice::where('account_id',$account_id)->where('unit_no',$EmpBuilding->building_id)->where('user_id',$id)->where('device_id',$device->id)->first();
								$devices_array['user_bluethooth_checked_status'] = isset($user_bluetooth_device)?1:0;
							
							$user_remote_device = UserRemoteDevice::where('account_id',$account_id)->where('unit_no',$EmpBuilding->building_id)->where('user_id',$id)->where('device_id',$device->id)->first();
								$devices_array['user_remote_checked_status'] = isset($user_remote_device)?1:0;
							$available_devices[] = $devices_array;
							
						}
					}
                    $record['devices'] = $available_devices;
					$data[] = $record;

                }
            }
         
            if(Session::get('searchpage') ==1){
                $search_url = Session::get('search_url');
                $return_url = $search_url;
                if(Session::get('page') >0){
                    $page = str_replace("&view","",Session::get('page'));
                    $return_url = $search_url."page=$page";
                }
            }
            else if(Session::get('page') >0){
                $page = str_replace("&view","",Session::get('page'));
                $return_url = "opslogin/user?view=summary&page=$page";
            }else{
                $return_url = 'opslogin/user?view=summary';
            }
            return view('admin.users.assignempdevices', compact('UserObj','UserMoreInfoObj','data','return_url'));

        }else{ //for user roles
            $UserMoreInfoObj = UserMoreInfo::where( 'id' , $user_info_id)->where( 'account_id' , $account_id)->first();
            $data =array();
            if(empty($UserMoreInfoObj)){
                return redirect("opslogin/user")->with('status', 'User not available');         

            }

                $PurchasedUnits = UserPurchaserUnit::where('user_id',$id)->where('user_info_id', $UserMoreInfoObj->id)->where('property_id',$account_id)->get();
                if(isset($PurchasedUnits)){
                    foreach($PurchasedUnits as $PurchasedUnit){
                        $record = array();
                        $record['id'] = $PurchasedUnit->id;
                        $record['building_no'] = $PurchasedUnit->building_id;
                        $record['building'] = isset($PurchasedUnit->addubuildinginfo->building)?$PurchasedUnit->addubuildinginfo->building:null;
                        $record['unit_no'] = $PurchasedUnit->unit_id;
                        $record['unit'] = $PurchasedUnit->addunitinfo->unit;
                        //$devices = Device::where('locations',$PurchasedUnit->building_id)->where('account_id',$account_id)->get();
                        //echo $PurchasedUnit->building_id . " , ";
                        $devices = Device::WhereRaw("CONCAT(',',locations,',') LIKE ?", '%,'.$PurchasedUnit->building_id .',%')->where('account_id',$account_id)->get();

                        $available_devices = array();
                        if(isset($devices)){
                            foreach($devices as $device){
                                $devices_array = array();
                                $devices_array['id'] = $device->id;
                                $devices_array['device_name'] = $device->device_name;
                                $devices_array['device_serial_no'] = $device->device_serial_no;
                                $devices_array['model'] = $device->model;
                                $devices_array['location_id'] = $device->locations;
                                $deviceClsObj = new \App\Models\v7\Device();
                                $locations = $deviceClsObj->getLocations($device->locations); 
                                $devices_array['location'] = '';
                                $loc_str = '';
                                if($locations !=''){
                                    foreach($locations as $loc){
                                        $loc_str .= $loc->building."<br/> ";
                                    }
                                 }
                                //$loc_str = substr($loc_str,0,-2);
                                $devices_array['location'] = $loc_str;

                                $user_bluetooth_device = UserDevice::where('account_id',$account_id)->where('unit_no',$PurchasedUnit->unit_id)->where('user_id',$id)->where('device_id',$device->id)->first();
                                    $devices_array['user_bluethooth_checked_status'] = isset($user_bluetooth_device)?1:0;
                                
                                $user_remote_device = UserRemoteDevice::where('account_id',$account_id)->where('unit_no',$PurchasedUnit->unit_id)->where('user_id',$id)->where('device_id',$device->id)->first();
                                    $devices_array['user_remote_checked_status'] = isset($user_remote_device)?1:0;
                                $available_devices[] = $devices_array;
                                
                            }
                        }
                        $record['devices'] = $available_devices;
                        $record['receive_call'] = $PurchasedUnit->receive_call;
                        $data[] = $record;
                    }
                }
            
            if(Session::get('searchpage') ==1){
                $search_url = Session::get('search_url');
                $return_url = $search_url;
                if(Session::get('page') >0){
                    $page = str_replace("&view","",Session::get('page'));
                    $return_url = $search_url."page=$page";
                }
            }
            else if(Session::get('page') >0){
                $page = str_replace("&view","",Session::get('page'));
                $return_url = "opslogin/user?view=summary&page=$page";
            }else{
                $return_url = 'opslogin/user?view=summary';
            }
            return view('admin.users.assigndevices', compact('UserObj','UserMoreInfoObj','data','return_url'));
        }
        
    }

    public function assigndevice(Request $request){
        $input = $request->all();
        $account_id = Auth::user()->account_id;
        $user = $input['user_id'];
		$UserObj = User::find($user);
		$user_roles = explode(",",env('USER_APP_ROLE'));
		UserDevice::where('account_id',$account_id)->where('user_id',$user)->delete();
        UserRemoteDevice::where('account_id',$account_id)->where('user_id',$user)->delete();
        
        $UserMoreInfoObj = UserMoreInfo::find($input['user_info_id']);

		$PurchasedUnits = UserPurchaserUnit::where('user_info_id', $UserMoreInfoObj->id)->where('property_id',$account_id)->get();
       
       if(in_array($UserObj->role_id,$user_roles))
       { // userapp devices
            $data =array();
            if(isset($PurchasedUnits)){
                foreach($PurchasedUnits as $PurchasedUnit){

                    //$devices = Device::where('locations',$PurchasedUnit->building_id)->where('account_id',$account_id)->get();
                    $devices = Device::WhereRaw("CONCAT(',',locations,',') LIKE ?", '%,'.$PurchasedUnit->building_id .',%')->where('account_id',$account_id)->get();
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
                    if(isset($input[$receive_device_call]) && $input[$receive_device_call] ==1)
                    {
                       
                        UserPurchaserUnit::where('id', $PurchasedUnit->id)
                        ->update(['receive_call' => 1]);
                    }
                    else{
                        UserPurchaserUnit::where('id', $PurchasedUnit->id)
                        ->update(['receive_call' => 0]);
                    }
                }
            }
           
            $log['module_id'] = 7;
            $log['account_id'] = $account_id;
            $log['admin_id'] = Auth::user()->id;
            $log['action'] = 10;
            $log['new_values'] = '';
            $log['ref_id'] = $UserMoreInfoObj->id;
            $log['notes'] = 'User Device Updated';
            $log = ActivityLog::create($log);

            
            return redirect("opslogin/user/userdevices/".$UserMoreInfoObj->id)->withInput()->with('status', 'Device(s) assigned!');         
        }else{ //employee devices
            $EmpBuildings = Building::where('account_id',$account_id)->get();
            $data =array();
            if(isset($EmpBuildings)){
                foreach($EmpBuildings as $EmpBuilding){

                    //$devices = Device::where('locations',$EmpBuilding->id)->where('account_id',$account_id)->get();
                    $devices = Device::WhereRaw("CONCAT(',',locations,',') LIKE ?", '%,'.$EmpBuilding->id .',%')->where('account_id',$account_id)->get();
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
            $log['admin_id'] = Auth::user()->id;
            $log['action'] = 10;
            $log['new_values'] = '';
            $log['ref_id'] = $UserMoreInfoObj->id;
            $log['notes'] = 'Employee Device Updated';
            $log = ActivityLog::create($log);
            return redirect("opslogin/user/userdevices/".$UserMoreInfoObj->id)->withInput()->with('status', 'Device(s) assigned.!');  
        }

    }

    public function useraccess($user_info_id)
    {
        $account_id = Auth::user()->account_id;
        $UserMoreInfoObj = UserMoreInfo::find($user_info_id);
        $id = $UserMoreInfoObj->user_id;
        $UserObj = User::find($id);
        $user_lists = explode(",",env('USER_APP_ROLE'));
        //$UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $id)->where( 'account_id' , $account_id)->first();
        //$UserMoreInfoObj = UserMoreInfo::where( 'id1' , $user)->where('status',1)->first();
        if($UserMoreInfoObj->status !=1){
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/user?page=$page")->with('status', 'Sorry, Account is not Active!');
            }
            else if(Session::get('searchpage') ==1){
                $return_url = Session::get('search_url');
                return redirect($return_url)->with('status', 'Sorry, Account is not Active!');
            }
            else
                return redirect("opslogin/user")->with('status', 'Sorry, Account is not Active!');         

        }
       
        $users = UserPurchaserUnit::where('property_id',$account_id)->where('user_info_id',$UserMoreInfoObj->id)->where('user_id',$id)->WhereIn('role_id',$user_lists)->orderby('user_id','asc')->get();
					
			$userlists = array();
			$user_access=array();
			foreach($users as $user){
				$userdetail = array();
				$userdetail['id'] = $user->id;
				$userdetail['first_name'] = isset($user->usermoreinfo->first_name)?$user->usermoreinfo->first_name:null;
				$userdetail['last_name'] = isset($user->usermoreinfo->last_name)?$user->usermoreinfo->last_name:null;
				$userdetail['role'] = isset($user->role->name)?$user->role->name:null;
				$userdetail['user_id'] = $user->user_id;
				$userdetail['user_info_id'] = isset($user->usermoreinfo->id)?$user->usermoreinfo->id:null;
				$userdetail['building_id'] = $user->building_id;
				$userdetail['unit_id'] = $user->unit_id;
				$userdetail['building'] = isset($user->addubuildinginfo->building)?$user->addubuildinginfo->building:null;
				$userdetail['unit'] = isset($user->addunitinfo->unit)?"#".$user->addunitinfo->unit:null;
				

				$permissions = UserPermission::where('account_id',$account_id)->where('user_id',$user->user_id)->where('unit_no',$user->unit_id)->orderby('user_id','asc')->get();

				$role_access=array();
				foreach($permissions as $permission){
					$role_access[$permission->module_id] = $permission->view;      
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
            if(Session::get('searchpage') ==1){
                $search_url = Session::get('search_url');
                $return_url = $search_url;
                if(Session::get('page') >0){
                    $page = str_replace("&view","",Session::get('page'));
                    $return_url = $search_url."page=$page";
                }
            }
            else if(Session::get('page') >0){
                $page = str_replace("&view","",Session::get('page'));
                $return_url = "opslogin/user?view=summary&page=$page";
            }else{
                $return_url = 'opslogin/user?view=summary';
            }
        return view('admin.users.assignaccess', compact('UserObj','UserMoreInfoObj','userlists','modules','return_url'));
    }

    public function assignaccess(Request $request){

        $input = $request->all();
        $user = $input['user_id'];
        $account_id = Auth::user()->account_id;
        $UserObj = User::find($user);
        $UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $user)->where( 'account_id' , $account_id)->where('status',1)->orderby('id','desc')->first();
        if(empty($UserMoreInfoObj)){
            return redirect("opslogin/user")->with('status', 'Account has been de-activated');         

        }
       // $UserMoreInfoObj = UserMoreInfo::where( 'id1' , $user)->where('status',1)->first();
        //echo $UserMoreInfoObj->id;
        //exit;
        $user_lists = explode(",",env('USER_APP_ROLE'));
        $punitids = UserPurchaserUnit::where('property_id',$account_id)->where('user_info_id',$UserMoreInfoObj->id)->where('user_id',$user)->WhereIn('role_id',$user_lists)->orderby('user_id','asc')->get();
        
        if(in_array($UserObj->role_id,$user_lists))
        {
            foreach($punitids as $unitid)
            {
                UserPermission::where('user_id',$unitid->user_id)->where('unit_no',$unitid->unit_id)->delete();
                $input['user_id'] = $unitid->user_id;
                $modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();    
                foreach($modules as $module) {
                    $data['account_id'] = $unitid->property_id;
                    $data['user_id'] = $unitid->user_id;
                    $data['unit_no'] = $unitid->unit_id;
                    $data['module_id'] = $module->id;
                    $view_field = "mod_".$module->id."_pid_".$unitid->id;
                    if(isset($input[$view_field]) && $input[$view_field] ==1){
                        $data['view'] = $data['create'] = $data['edit'] = $data['delete'] = 1;
                    }
                    else{
                        $data['view'] = $data['create'] = $data['edit'] = $data['delete'] = 0;
                    }  
                    UserPermission::create($data);  
                }
            }
            $log['module_id'] = 7;
            $log['account_id'] = $account_id;
            $log['admin_id'] = Auth::user()->id;
            $log['action'] = 11;
            $log['new_values'] = '';
            $log['ref_id'] = $UserMoreInfoObj->id;
            $log['notes'] = 'User Access Updated';
            $log = ActivityLog::create($log);
            return redirect("opslogin/user/useraccess/". $UserMoreInfoObj->id)->withInput()->with('status', 'System access has been updated!');         
        }
        return redirect("opslogin/user/useraccess/".$UserMoreInfoObj->id)->withInput()->with('status', 'System access not updated!');   
    }

    public function queryupdate()
    {
        $users = User::get();  
        if(isset($users)){
            foreach($users as $user){
                UserMoreInfo::where( 'user_id' , $user->id)->update( array( 'account_id' => $user->account_id));
            }
        } 
        return redirect('opslogin/user')->with('status', 'Query updated!');
    }
    public function unitqueryupdate()
    {
        $user_roles = explode(",",env('USER_APP_ROLE'));

        $users = User::whereIn('role_id',$user_roles)->get();  
        if(isset($users)){
            foreach($users as $user){
                
                $purchaser['user_id'] = $user->id;
                $purchaser['user_info_id'] = $user->empinfo->id;
                $purchaser['property_id'] = $user->account_id;
                $purchaser['building_id'] = $user->building_no;
                $purchaser['unit_id'] = $user->unit_no;
                $purchaser['card_no'] = $user->empinfo->card_no;
                $purchaser['role_id'] = $user->role_id;
                $purchaser['primary_contact'] = $user->primary_contact;
                $purchaser['created_at'] = $user->created_at;
                $purchaser['updated_at'] = $user->updated_at;
                UserPurchaserUnit::create($purchaser);
            }
        } 
        return redirect('opslogin/user')->with('status', 'Query updated!');
    }

    public function bluetoothdevice_query()
    {
       
        $userdevices = UserDevice::get();  
        if(isset($userdevices)){
            foreach($userdevices as $userdevice){
                $user = User::find($userdevice->user_id);  
                $unit_no = isset($user->unit_no)?$user->unit_no:null;
                if($unit_no >0)
                    UserDevice::where( 'id' , $userdevice->id)->update( array( 'unit_no' => $user->unit_no));
            }
        }   
        return redirect('opslogin/user')->with('status', 'Query updated!');
    }

    public function remotedevice_query()
    {
        $userdevices = UserRemoteDevice::get();  
        if(isset($userdevices)){
            foreach($userdevices as $userdevice){
                $user = User::find($userdevice->user_id);  
                $unit_no = isset($user->unit_no)?$user->unit_no:null;
                if($unit_no >0)
                    UserRemoteDevice::where( 'id' , $userdevice->id)->update( array( 'unit_no' => $user->unit_no));
            }
        } 
        return redirect('opslogin/user')->with('status', 'Query updated!');
    }

    public function serviceaccess_updatequery()
    {
        $userdevices = UserPermission::get();  
        if(isset($userdevices)){
            foreach($userdevices as $userdevice){
                $user = User::find($userdevice->user_id);  
                $unit_no = isset($user->unit_no)?$user->unit_no:null;
                $purchaseUnit = UserPurchaserUnit::where('property_id',$user->account_id)->where('user_id',$user->user_id)->where('unit_id',$user->unit_no)->first();
                $purchaseUnitId = isset($purchaseUnit)?$purchaseUnit->id:null;
                if($unit_no >0)
                    UserPermission::where( 'id' , $userdevice->id)->update( array('account_id' => $user->account_id,'unit_no' => $user->unit_no,'purchase_unit_id'=>$purchaseUnitId));
            }
        } 
        return redirect('opslogin/user')->with('status', 'Query updated!');
    }

    public function encrypt(Request $request){
        $infosObj = UserMoreInfo::where('encrypted',0)->get();
       
        if(isset($infosObj)){
            foreach($infosObj as $k => $infoObj){
                $firstname =Crypt::encryptString($infoObj->first_name);
                $lastname =Crypt::encryptString($infoObj->last_name);
                $phone =Crypt::encryptString($infoObj->phone); 
                UserMoreInfo::where('id',$infoObj->id)->update(['first_name'=>$firstname,'last_name'=>$lastname,'phone'=>$phone,'encrypted'=>1]);
            }
            return redirect('opslogin/user')->with('status', 'Data has been encrypted!');
        }
        else{
            return redirect('opslogin/user')->with('status', 'All data encrypted already!');
        }
    }

    public function userencrypt(Request $request){
        $userObj = User::where('encrypted',0)->get();
       
        if(isset($userObj)){
            foreach($userObj as $k => $user){
                $name =Crypt::encryptString($user->name);
                User::where('id',$user->id)->update(['name'=>$name,'encrypted'=>1]);
            }
            return redirect('opslogin/user')->with('status', 'Data has been encrypted!');
        }
        else{
            return redirect('opslogin/user')->with('status', 'All data encrypted already!');
        }
    }

    public function usercards($user_info_id)
    {
        $account_id = Auth::user()->account_id;
        $UserMoreInfoObj = UserMoreInfo::find($user_info_id);
        if($UserMoreInfoObj->status !=1){
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/user?page=$page")->with('status', 'Sorry, Account is not Active!');
            }
            else if(Session::get('searchpage') ==1){
                $return_url = Session::get('search_url');
                return redirect($return_url)->with('status', 'Sorry, Account is not Active!');
            }
            else
                return redirect("opslogin/user")->with('status', 'Sorry, Account is not Active!');         

        }
        $id = $UserMoreInfoObj->user_id;
        $UserObj = User::find($id);
        //$UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $id)->where( 'account_id' , $account_id)->orderby('id','desc')->first();
        $assignedCards = UserCard::where('user_info_id',$UserMoreInfoObj->id)->where('property_id',$account_id)->where('status',1)->get();
        $unites = Unit::where('account_id',$account_id)->pluck('unit', 'id')->all();
        $assignedBlocks = UserPurchaserUnit::select('building_id')->where('user_info_id',$UserMoreInfoObj->id)->where('property_id',$account_id)->where('status',1)->get()->toArray();
        //print_r($assignedUnits );
        $buildings = Building::whereIn('id',$assignedBlocks)->pluck('building', 'id')->all();
        $app_user_lists = explode(",",env('USER_APP_ROLE'));
        $roles = Role::WhereIn('id',$app_user_lists)->orderby('name','asc')->pluck('name', 'id')->all();

        if(Session::get('searchpage') ==1){
            $search_url = Session::get('search_url');
            $return_url = $search_url;
            if(Session::get('page') >0){
                $page = Session::get('page');
                $return_url = $search_url."page=$page";
            }
        }
        else if(Session::get('page') >0){
            $page = str_replace("&view","",Session::get('page'));
            $return_url = "opslogin/user?view=summary&page=$page";
        }else{
            $return_url = 'opslogin/user?view=summary';
        }
        return view('admin.users.assignedcards', compact('UserObj','UserMoreInfoObj','assignedCards','buildings','unites','return_url','roles'));
    }

    public function assigncard(Request $request){

        $input = $request->all();
        $account_id = Auth::user()->account_id;

        $UserMoreInfoObj = UserMoreInfo::find($input['user_more_info_id']);
        $userObj = User::find($UserMoreInfoObj->user_id);
        //$UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $userObj->id)->where( 'account_id' , $userObj->account_id)->first();
       

        
        $user_roles = explode(",",env('USER_APP_ROLE'));
        if(in_array($userObj->role_id,$user_roles))
        {
            if ($input['building_no'] =='') { 
                return redirect("opslogin/user/usercards/".$input['user_more_info_id'])->withInput()->with('status', 'Building not selected');         
            }

            if (empty($input['unit_no'])) { 
                return redirect("opslogin/user/usercards/".$input['user_more_info_id'])->withInput()->with('status', 'Unit not selected');         
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
            //end
            $card_nos = explode(",",$input['card_no']);
            $cards_exist = '';
            foreach($card_nos as $card_no){
                $cardObj = UserCard::where('card_no',$card_no)->first();
                if(!isset( $cardObj)){
                    $card_input['user_id'] = $userObj->id;
                    $card_input['user_info_id'] = $UserMoreInfoObj->id;
                    $card_input['property_id'] = $input['account_id'];
                    $card_input['building_id'] = $input['building_no'];
                    $card_input['unit_id'] = $input['unit_no'];
                    $card_input['card_no'] = $card_no;
                    UserCard::create($card_input);
                }else{
                    $cards_exist = $card_no.",";
                }
            }

            //$cards =  isset($input['card_no'])?$input['card_no']:null;
           
            $name = Crypt::decryptString($UserMoreInfoObj->first_name)." ".Crypt::decryptString($UserMoreInfoObj->last_name);
    
                $accountinfos = UserPurchaserUnit::where('user_id',$userObj->id)
                ->where('user_info_id',$UserMoreInfoObj->id)
                ->where('property_id',$input['account_id'])
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
                ->where('property_id',$input['account_id'])
                ->get();
                $card_numbers = '';
                if(isset($cardinfos)){
                    foreach($cardinfos as $cardinfo){
                        $card_numbers .= $cardinfo->card_no.",";
                    }
                }
                $cards = substr($card_numbers,0,-1);
                $cards_exist = substr($cards_exist,0,-1);

                if($roomuuids !=''){
                    $api_obj = new \App\Models\v7\User();
                    $household_result = $api_obj->household_check_record($thinmoo_access_token,$userObj,$input['account_id']);
                    
                    if($household_result['code'] ==0){
                        $household = $api_obj->household_modify_api($thinmoo_access_token, $input['account_id'],$name,$userObj->id,$roomuuids,$cards);
                    }
                    else{
                        $household = $api_obj->household_add_api($thinmoo_access_token, $input['account_id'],$name,$userObj->id,$roomuuids,$cards);
                    }
                    
                }

                $log['module_id'] = 7;
                $log['account_id'] = $account_id;
                $log['admin_id'] = Auth::user()->id;
                $log['action'] = 7;
                $log['new_values'] = '';
                $log['ref_id'] = $UserMoreInfoObj->id;
                $log['notes'] = 'User Card Assigned';
                $log = ActivityLog::create($log);

               // exit;
               if($cards_exist !='')
                    return redirect("opslogin/user/usercards/".$input['user_more_info_id'])->withInput()->with('status', "Card assigned!. ($cards_exist card(s) already assigned)");   
               else
                    return redirect("opslogin/user/usercards/".$input['user_more_info_id'])->withInput()->with('status', 'Card assigned!');   

        }else{
            return redirect("opslogin/user/usercards/".$input['user_more_info_id'])->withInput()->with('status', 'Card not assigned!');   
        }

    }

    public function deletecard($id){
        $account_id = Auth::user()->account_id;
        $cardObj = UserCard::find($id);
        $userObj = User::find($cardObj->user_id);
        $UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $userObj->id)->where( 'account_id' , $userObj->account_id)->first();
        UserCard::findOrFail($id)->delete(); 

        $auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();
        $name = Crypt::decryptString($userObj->name)." ".Crypt::decryptString($userObj->userinfo_fromadmin->last_name);

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
            //echo $cards;
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
            $log['admin_id'] = Auth::user()->id;
            $log['action'] = 8;
            $log['new_values'] = '';
            $log['ref_id'] = $UserMoreInfoObj->id;
            $log['notes'] = 'User Card Deleted';
            $log = ActivityLog::create($log);

        return redirect("opslogin/user/usercards/".$UserMoreInfoObj->id)->withInput()->with('status', 'Assigned card has been deleted!');         


    }

    public function uploadcsv()
    {
        //$properties = Property::pluck('company_name', 'id')->all();
        return view('admin.users.uploadcsv');
    }

    public function importcsv(Request $request){
        $input = $request->all();
        $account_id =  Auth::user()->account_id;

        if ($request->file('csv_file') != null) {
            $extension = $request->file('csv_file')->getClientOriginalName();
            $filename = uniqid().'.'.$extension; 
            $csv_file_path = $request->file('csv_file')->storeAs("usercsv/$account_id",$filename);
        }
        $file_path = "app/".$csv_file_path;
        $filename =  base_path() .\Storage::url($file_path);
        $userArr = $this->csvToArray($filename);
        //print_r($buildingArr);
        $json_data = array();
        $auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();

        if(isset($userArr)){
            foreach($userArr as $k => $user){
                //echo $user['Role'];
                //print_r($user);
                $user_data = array();
                $accounts = array(0,$account_id);
                $roleObj = Role::where('name',$user['Role'])->wherein('account_id',$accounts)->first();
                //echo $roleObj->id;
                $buildingObj = Building::where('building',$user['Building'])->where('account_id',$account_id)->first();
                if(empty($buildingObj)){
                    return redirect('opslogin/user/uploadcsv')->with('status', $user['Building'].' Building not exists!');
                }
                
                $UnitsObj = Unit::where('building_id',$buildingObj->id)->get();
                $unit = str_replace("#","",trim($user['Unit']));
                $unit_id ='';
                if(isset($UnitsObj)){
                    //echo "here";
                    foreach($UnitsObj as $unitid){
                        if(trim(Crypt::decryptString($unitid->unit)) == trim($unit)){
                            $unit_id = $unitid->id;
                            break;
                        }
                    }
                }
               
                if($unit_id ==''){
                    return redirect('opslogin/user/uploadcsv')->with('status', $unit.' Unit not exists!');
                }
                $usercheck = User::where('email',$user['Email'])->first();
                if(empty($usercheck)){
                    $input['role_id'] = $roleObj->id;
                    $input['name'] = Crypt::encryptString($user['First Name']);
                    $input['last_name'] = Crypt::encryptString($user['Last Name']);
                    $input['phone'] = Crypt::encryptString($user['Phone']);
                    $input['email'] = $user['Email'];
                    $input['building_no'] = $buildingObj->id;
                    $input['account_id'] = $account_id;
                    $input['unit_no'] = $unit_id;
                    $input['encrypted']  =1;
                    $input['primary_contact'] =1;
                    $input['created_at'] = date("Y-m-d H:i:s");
                    $input['updated_at'] = date("Y-m-d H:i:s");
                    $userObj = User::create($input);
                    $country = Country::where('country_name',$user['Country'])->first();

                    $input['user_id'] = $userObj->id;
                    $input['first_name'] = $input['name'];
                    $input['mailing_address'] = $user['Address'];
                    $input['company_name'] = $user['Company'];
                    $input['country'] = $country->id;
                    $input['postal_code'] = $user['Postal Code'];
                    $userinfo = UserMoreInfo::create($input);

                    User::where('id', $userObj->id)->update(['user_info_id' => $userinfo->id]);

                    $purchaser['user_id'] = $userObj->id;
                    $purchaser['user_info_id'] = $userinfo->id;
                    $purchaser['property_id'] = $account_id;
                    $purchaser['building_id'] = $buildingObj->id;
                    $purchaser['unit_id'] = $unit_id;
                    $purchaser['primary_contact'] = $userObj->primary_contact;
                    $purchaser['role_id'] = $userObj->role_id;
                    $purchaser['created_at'] = date("Y-m-d H:i:s");
                    $purchaser['updated_at'] = date("Y-m-d H:i:s");
                    $purchaserunit = UserPurchaserUnit::create($purchaser);

                
                    $name = Crypt::decryptString($userinfo->first_name)." ".Crypt::decryptString($userinfo->last_name);
                    $roomuuids = $userObj->unit_no;
                    if($roomuuids !=''){
                        $api_obj = new \App\Models\v7\User();
                        $household = $api_obj->household_add_api($thinmoo_access_token, $account_id,$name,$userObj->id,$roomuuids);
                    }
                }
            }
        }
        
        //exit;
        return redirect('opslogin/user')->with('status', 'User(s) has been imported!');


    }

    public function userproperties($user_info_id)
    {
        $account_id = Auth::user()->account_id;
        $UserMoreInfoObj = UserMoreInfo::find($user_info_id);
        if($UserMoreInfoObj->status !=1){
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/user?page=$page")->with('status', 'Sorry, Account is not Active!');
            }
            else if(Session::get('searchpage') ==1){
                $return_url = Session::get('search_url');
                return redirect($return_url)->with('status', 'Sorry, Account is not Active!');
            }
            else
                return redirect("opslogin/user")->with('status', 'Sorry, Account is not Active!');         

        }
        $id = $UserMoreInfoObj->user_id;
        $UserObj = User::find($id);
        //$UserMoreInfoObj = UserMoreInfo::where( 'user_id' , $id)->where( 'account_id' , $account_id)->orderby('id','desc')->first();
        
        $agent_properties = Property::where('status',1)->get();
        $assigned_props = UserProperty::where('user_id',$UserObj->id)->get();
        $agent_properties =array();
           
            if(isset($assigned_props)){
                $assigned_property = array();
                foreach($assigned_props as $prop_id){
                    $assigned_property[] = $prop_id->property_id;
                }
                $not_assigned_properties = Property::whereNotIn('id',$assigned_property)->orderby('company_name','asc')->pluck('company_name', 'id')->all();
            }
        
        
        if(Session::get('searchpage') ==1){
            $search_url = Session::get('search_url');
            $return_url = $search_url;
            if(Session::get('page') >0){
                $page = Session::get('page');
                $return_url = $search_url."page=$page";
            }
        }
        else if(Session::get('page') >0){
            $page = Session::get('page');
            $return_url = "opslogin/user?view=summary&page=$page";
        }else{
            $return_url = 'opslogin/user?view=summary';
        }
        return view('admin.users.assignedprop', compact('UserObj','UserMoreInfoObj','not_assigned_properties','assigned_props','return_url'));
    }

    public function assignproperty(Request $request){
        $input = $request->all();
        $account_id = Auth::user()->account_id;

        $UserMoreObj = UserMoreInfo::find($input['user_more_info_id']);
        $UserObj = User::find($UserMoreObj->user_id);

        $auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();
        
        $emp = new \App\Models\v7\Employee();
        $name = Crypt::decryptString($UserMoreObj->first_name)." ".Crypt::decryptString($UserMoreObj->last_name);
        foreach($input['properties'] as $property){
            $emp_result = Employee::where('account_id',$property)->where('uuid',$UserObj->id)->orderby('id','desc')->first();
           //echo "ID ". $emp_result->id;
            if(empty($emp_result)){
                
                $property_input['user_id'] = $UserObj->id;
                $property_input['property_id'] = $property;
                UserProperty::create($property_input);  

                $emp_rec['account_id'] = $property;
                $emp_rec['name'] =  $name;
                $emp_rec['emp_type'] =  1;
                $emp_rec['status'] =  1;
                $emp_rec['uuid'] =  $UserObj->id; //
                $result = Employee::create($emp_rec);
                $employee = $emp->employee_add_api($thinmoo_access_token,$result,$UserObj->role_id);
            }
        }
        return redirect("opslogin/user/userproperties/".$input['user_more_info_id'])->withInput()->with('status', 'Property assigned!');   
    }

   
    public function deleteproperty($id){
        //$account_id = Auth::user()->account_id;
        $userPropObj = UserProperty::find($id);
        $emp = new \App\Models\v7\Employee();
        $UserMoreObj = UserMoreInfo::where("user_id",$userPropObj->user_id)->where('status',1)->orderby('id','desc')->first();

        $auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();
        $emp_results = Employee::where('account_id',$userPropObj->property_id)->where('uuid',$userPropObj->user_id)->orderby('id','desc')->get();
        if(isset($emp_results)){
            foreach($emp_results as $emp){
                //echo "hi";
                $employee = $emp->employee_delete_record($thinmoo_access_token,$emp->account_id,$emp->id);
                //print_r( $employee);
                Employee::findOrFail($emp->id)->delete(); 
            }
        }
        UserProperty::findOrFail($id)->delete(); 
        return redirect("opslogin/user/userproperties/".$UserMoreObj->id)->withInput()->with('status', 'Property has been removed');  
    }

}
