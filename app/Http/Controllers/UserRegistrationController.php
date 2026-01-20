<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use Session;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\Unit;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\UserRegistrationRequest;
use App\Models\v7\UserLicensePlate;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserFacialId;
use App\Models\v7\FacialRecoOption;
use App\Models\v7\Building;
use App\Models\v7\Role;

use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Storage;

class UserRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $q = $fromdate = $todate = $name = $last_name = $building = $buildings = $unit = $email = $status= $role = $roles ='' ;
        $app_user_lists = explode(",",env('USER_APP_ROLE'));

        $account_id = Auth::user()->account_id;
        $registrations = UserRegistrationRequest::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));  
        $buildings = Building::where("status",1)->where('account_id',$account_id)->orderby('building','asc')->pluck("building","id")->all();
        $roles = Role::whereIn("id",$app_user_lists)->orderby('name','asc')->pluck('name', 'id')->all();

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
        

        return view('admin.registration.index', compact('registrations','q','fromdate','todate','name','last_name','building','buildings','unit','email','status','roles','role'));
    }

    public function view($id)
    {
        $q ='';
        $account_id = Auth::user()->account_id;
        $regObj = UserRegistrationRequest::find($id);
        $ownersObj = UserRegistrationRequest::where('role_id',2)->where('unit_no',$regObj->unit_no)->where('status',2)->get();
        $img_full_path = env('APP_URL') . "/storage/app/";
        return view('admin.registration.view', compact('regObj','ownersObj','img_full_path'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        $q ='';
        $img_full_path = env('APP_URL') . "/storage/app/";
        $account_id = Auth::user()->account_id;
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
           //exit;
            $status_message = 'Registration has been approved!';
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/registrations?page=$page")->with('status', $status_message);
            }
            else if(Session::get('searchpage') ==1){
                $return_url = Session::get('search_url');
                return redirect($return_url)->with('status', $status_message);
            }
            else
                return redirect('opslogin/registrations')->with('status', $status_message);
            }
        else{
            $status_message = 'Email already registered for this property!';

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
                $status_message = 'Registration has been approved!';
            }
            
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/registrations?page=$page")->with('status', $status_message);
        }
        else if(Session::get('searchpage') ==1){
            $return_url = Session::get('search_url');
            return redirect($return_url)->with('status', $status_message);
        }
        else
            return redirect('opslogin/registrations')->with('status', $status_message);
           
        }
        
    }

    public function cancelregistration(Request $request)
    {
        $q ='';
        $input = $request->all();
        $id = $input['bookId'];
        $account_id = Auth::user()->account_id;
        $regObj = UserRegistrationRequest::find($id);
        $regObj->approved_date = date("Y-m-d H:i:s");
         $regObj->status = 3;
        $regObj->reason = $input['reason'];
        $regObj->save();
        
        $name = $regObj->first_name. " ".$regObj->last_name;
        $email = $regObj->email;
        $reason = $regObj->reason;
        UserRegistrationRequest::cancelemail($name, $email,$reason);

        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/registrations?page=$page")->with('status', 'Registration has been rejected!');
        }
        else if(Session::get('searchpage') ==1){
            $return_url = Session::get('search_url');
            return redirect($return_url)->with('status', 'Registration has been rejected!');
        }
        else
            return redirect('opslogin/registrations')->with('status', 'Registration has been rejected!');

        
    }

   public function delete($id)
    {
        //
        UserRegistrationRequest::findOrFail($id)->delete();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/registrations?page=$page")->with('status', 'Registration deleted successfully!');
        }
        else if(Session::get('searchpage') ==1){
            $return_url = Session::get('search_url');
            return redirect($return_url)->with('status', 'Registration deleted successfully!');
        }
        else
            return redirect('opslogin/registrations')->with('status', 'Registration deleted successfully!');
    }

    public function search(Request $request)
    {
        $q = $fromdate = $todate = $name = $last_name = $building = $buildings = $unit = $email = $status= $role = $roles ='' ;
        $app_user_lists = explode(",",env('USER_APP_ROLE'));

        $name = $request->input('name');
        $email = $request->input('email');
        $last_name = $request->input('last_name');
        $building = $request->input('building');
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

        if($unit !=''){
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($building,$unit) {
            if($building !='')
                $query->where('building_id',$building);
            //if($unit !='')
                //$query->Where('unit1', Crypt::encryptString($unit));
            })->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    if(Crypt::decryptString($unitid->unit) ===$unit)
                        $units[] = $unitid->id;
                    else if ($request->input('unit') =='')
                        $units[] = $unitid->id;
                }
            }
        }
        


        $registrations = UserRegistrationRequest::where('account_id',$account_id)->where(function ($query) use ($fromdate,$todate,$name,$last_name,$email,$building,$unit,$status,$role,$units) {
            if($name !='')
                $query->where('first_name', 'LIKE', '%' . $name . '%');
            if($last_name !='')
                $query->where('last_name', 'LIKE', '%' . $last_name . '%');
            if($email !='')
                $query->where('email', 'LIKE', '%' . $email . '%');
            if($building !='')
                $query->where('building_no', $building);
            if($unit !=''){
                $query->whereIn('unit_no', $units);
            }
            if($role !='')
                $query->where('role_id', $role);
            if($status !='')
                $query->where('status',$status);
            if($fromdate!='' && $todate !='')
                $query->whereBetween('created_at',array($fromdate,$todate));
        })->orderBy('unit_no','ASC')->paginate(env('PAGINATION_ROWS'));  

        $buildings = Building::where("status",1)->where('account_id',$account_id)->orderby('building','asc')->pluck("building","id")->all();
        $roles = Role::whereIn("id",$app_user_lists)->orderby('name','asc')->pluck('name', 'id')->all(); 
        
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

        return view('admin.registration.index', compact('registrations','q','fromdate','todate','name','last_name','building','buildings','unit','email','status','roles','role'));


    }

   
}
