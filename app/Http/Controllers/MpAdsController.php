<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\MpAdsBlockUser;
use App\Models\v7\MpAdsCondition;
use App\Models\v7\MpAdsImage;
use App\Models\v7\MpAdsLike;
use App\Models\v7\MpAdsReport;
use App\Models\v7\MpAdsSubmission;
use App\Models\v7\MpAdsType;
use App\Models\v7\MpGroupRegister;
use App\Models\v7\MpadsBlockUserByAdmin;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\Property;
use App\Models\v7\User;
use App\Models\v7\Role;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserLog;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\UserNotification;
use Auth;
use Mail;
use App\Models\v7\Mail\AnnouncementNotification;
use App\Models\v7\Setting;
use DB;
use Session;


use Illuminate\Http\Request;

class MpAdsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $q = $property = $first_name  = $title = $category = $types = $type = $conditions = $condition = '';
        if(Auth::user()->role_id ==1){
            $lists = MpAdsSubmission::orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
            $properties = Property::orderby('company_name','asc')->pluck('company_name', 'id')->all();
            $types = MpAdstype::orderby('type','asc')->pluck('type', 'id')->all();
            $conditions = MpAdsCondition::orderby('type','asc')->pluck('type', 'id')->all();
            $currentURL = url()->full();

            $page = explode("=",$currentURL);
            if(isset($page[1]) && $page[1]>0){
                    session()->put('page', $page[1]);
            }else{
                    session()->forget('page');
            }
            return view('admin.marketplace.adminindex', compact('lists','q','properties','property','types','type','conditions','condition','first_name','title'));
        }else{
            $account_id = Auth::user()->account_id;
            $lists = MpAdsSubmission::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
            $properties = Property::where('id',$account_id)->orderby('company_name','asc')->pluck('company_name', 'id')->all();
            $types = MpAdstype::orderby('type','asc')->pluck('type', 'id')->all();
            $conditions = MpAdsCondition::orderby('type','asc')->pluck('type', 'id')->all();
            $currentURL = url()->full();

            $page = explode("=",$currentURL);
            if(isset($page[1]) && $page[1]>0){
                    session()->put('page', $page[1]);
            }else{
                    session()->forget('page');
            }
            return view('admin.marketplace.index', compact('lists','q','properties','property','types','type','conditions','condition','first_name','title'));
        }

    }

    public function allreports()
    {
        $q = $property = $first_name  = $title = $category = $types = $type = $conditions = $condition = '';
        if(Auth::user()->role_id ==1){
            $lists = MpAdsReport::where('status',1)->orderby('id','desc')->groupBy('ref_id')->paginate(env('PAGINATION_ROWS')); 
            $properties = Property::orderby('company_name','asc')->pluck('company_name', 'id')->all();
            $types = MpAdstype::orderby('type','asc')->pluck('type', 'id')->all();
            $conditions = MpAdsCondition::orderby('type','asc')->pluck('type', 'id')->all();
            $currentURL = url()->full();

            $page = explode("=",$currentURL);
            if(isset($page[1]) && $page[1]>0){
                    session()->put('page', $page[1]);
            }else{
                    session()->forget('page');
            }
            return view('admin.marketplace.adminallreports', compact('lists','q','properties','property','types','type','conditions','condition','first_name','title'));
        }else{
            $account_id = Auth::user()->account_id;
            $lists = MpAdsReport::where('account_id',$account_id)->where('status',1)->orderby('id','desc')->groupBy('ref_id')->paginate(env('PAGINATION_ROWS')); 
            $properties = Property::where('id',$account_id)->orderby('company_name','asc')->pluck('company_name', 'id')->all();
            $types = MpAdstype::orderby('type','asc')->pluck('type', 'id')->all();
            $conditions = MpAdsCondition::orderby('type','asc')->pluck('type', 'id')->all();
            $currentURL = url()->full();

            $page = explode("=",$currentURL);
            if(isset($page[1]) && $page[1]>0){
                    session()->put('page', $page[1]);
            }else{
                    session()->forget('page');
            }
            return view('admin.marketplace.allreports', compact('lists','q','properties','property','types','type','conditions','condition','first_name','title'));
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Announcement  $sharingAnnouncement
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        MpAdsReport::where('ref_id',$id)->delete();
        MpAdsLike::where('ref_id',$id)->delete();
        MpAdsSubmission::findOrFail($id)->delete();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/marketplace?page=$page")->with('status', 'Remarks has been deleted!');
        }
        else
            return redirect('opslogin/marketplace')->with('status', 'Record has been deleted!');
    }

    public function search(Request $request)
    {
        $q = $property = $first_name  = $title = $category = $types = $type = $conditions = $condition = '';
        $first_name = $request->input('first_name');
        $title = $request->input('title');
        $type = $request->input('type');
        $condition = $request->input('condition');
        $property = $request->input('property');
        $userids =array();
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
            else{
                if($first_name !=''){
                    $user_more_info = UserMoreInfo::where('status',1)->orderby('id','desc')->get();
                    foreach($user_more_info as $k =>$v){
                        $firstname = strtolower(Crypt::decryptString($v->first_name));
                        $lastname = strtolower(Crypt::decryptString($v->last_name));
                        if(str_contains($firstname,strtolower($first_name)) || str_contains($lastname,strtolower($first_name))){
                            $userids[] = $v->user_id;
                                
                        }
                    }
                } 
                else{
                    $user_more_info = UserMoreInfo::where('status',1)->orderby('id','desc')->get();
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
        })->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 

        $properties = Property::orderby('company_name','asc')->pluck('company_name', 'id')->all();
        $types = MpAdstype::orderby('type','asc')->pluck('type', 'id')->all();
        $conditions = MpAdsCondition::orderby('type','asc')->pluck('type', 'id')->all();
        if(Auth::user()->role_id ==1){
            return view('admin.marketplace.adminindex', compact('lists','q','properties','property','types','type','conditions','condition','first_name','title'));
        }
        else{
            return view('admin.marketplace.index', compact('lists','q','properties','property','types','type','conditions','condition','first_name','title'));
        }
    }

    public function likes($id)
    {
        $q = $property = $first_name = $ticket = $subject = '';
        $AdsObj = MpAdsSubmission::find($id);
        $lists = MpAdsLike::where('ref_id',$id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        $currentURL = url()->full();

        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        } 
        return view('admin.marketplace.likes', compact('AdsObj','lists','q','property','first_name','subject'));

    }
    public function likesdestroy($id)
    {
        $CommentObj = MpAdsLike::find($id);
        MpAdsLike::findOrFail($id)->delete();
        //echo $CommentObj->ref_id;
        //exit;
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/marketplace/likes/$CommentObj->ref_id?page=$page")->with('status', 'Record has been deleted!');
        }
        else
            return redirect("opslogin/marketplace/likes/$CommentObj->ref_id")->with('status', 'Record has been deleted!');
    }

    public function reports($id)
    {
        $q = $property = $first_name = $ticket = $subject = '';
        $AdsObj = MpAdsSubmission::find($id);
        $lists = MpAdsReport::where('ref_id',$id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
        $currentURL = url()->full();
        MpAdsReport::where('ref_id',$id)->where('view_status',1)->update(['view_status'=>2]);

        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }
        return view('admin.marketplace.reports', compact('AdsObj','lists','q','property','first_name','subject'));

    }

    public function reportsdestroy($id)
    {   
        $reportObj =  MpAdsReport::find($id);
        MpAdsReport::findOrFail($id)->delete();

        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/marketplace/reports/$reportObj->ref_id?page=$page")->with('status', 'Record has been deleted!');
        }
        else
            return redirect("opslogin/marketplace/reports/$reportObj->ref_id")->with('status', 'Record has been deleted!');
    }

    public function deactivate($id)
    {   
        $ChatObj = MpAdsSubmission::find($id);
		$ChatObj->status =2;
        $ChatObj->save();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/marketplace/$ChatObj->ref_id?page=$page")->with('status', 'marketplace has been de-activated!');
        }
        else
            return redirect("opslogin/marketplace/$ChatObj->ref_id")->with('status', 'marketplace has been de-activated!');
    }
    public function activate($id)
    {   
        $ChatObj = MpAdsSubmission::find($id);
		$ChatObj->status =1;
        $ChatObj->save();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/marketplace/$ChatObj->ref_id?page=$page")->with('status', 'marketplace has been activated!');
        }
        else
            return redirect("opslogin/marketplace/$ChatObj->ref_id")->with('status', 'marketplace has been activated!');
    }

    public function hidereport($id)
    {   
        $ReportObj = MpAdsReport::find($id);
		$ReportObj->status =2;
        $ReportObj->save();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/marketplace/reports/$reportObj->ref_id?page=$page")->with('status', 'Report has been hided!');
        }
        else
            return redirect("opslogin/marketplace/reports/$reportObj->ref_id")->with('status', 'Report has been hided!');
    }
    public function showreport($id)
    {   
        $ReportObj = MpAdsReport::find($id);
		$ReportObj->status =1;
        $ReportObj->save();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/marketplace/reports/$reportObj->ref_id?page=$page")->with('status', 'Record has been activated!');
        }
        else
            return redirect("opslogin/marketplace/reports/$reportObj->ref_id")->with('status', 'Record has been activated!');
    }

    public function blockuser($id)
    {   
       $reportObj =  MpAdsReport::find($id);
       $UserObj = User::find($reportObj->user_id);
       $admin_id= Auth::user()->id;
       $check_status = MpAdsBlockUserByAdmin::where('account_id',$reportObj->account_id)->where('block_user_id',$reportObj->user_id)->first();
       if(isset($check_status)){
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/marketplace/reports/$reportObj->ref_id?page=$page")->with('status', 'User already blocked!');
        }
        else
            return redirect("opslogin/marketplace/reports/$reportObj->ref_id")->with('status', 'User already blocked!');
       }else{
           $input['admin_id'] = $admin_id;
           $input['account_id'] = $reportObj->account_id;
           $input['unit_no'] = $reportObj->unit_no;
           $input['block_user_id'] = $reportObj->user_id;
           $input['type'] = 2;
           $input['ref_id'] = $reportObj->id;
           //$input['remark'] = $request->remark;
           $input['status'] = 1;
           $results = MpAdsBlockUserByAdmin::create($input);

            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/marketplace/reports/$reportObj->ref_id?page=$page")->with('status', 'User has been blocked!');
            }
            else
                return redirect("opslogin/marketplace/reports/$reportObj->ref_id")->with('status', 'User has been blocked!');
       }
    }

    public function warninguser($id)
    {   
       $reportObj =  MpAdsReport::find($id);
       $UserObj = User::find($reportObj->user_id); 
       $account_id= Auth::user()->account_id;

       if(isset($UserObj)){
           $fcm_token_array ='';
           $user_token = ',';
           $ios_devices_to_send = array();
           $android_devices_to_send = array();
           $logs = UserLog::where('user_id',$UserObj->id)->where('status',1)->orderby('id','desc')->first();
           if(isset($logs->fcm_token) && $logs->fcm_token !=''){
               $user_token .=$logs->fcm_token.",";
               $fcm_token_array .=$logs->fcm_token.',';
               $appSipAccountList[] = $reportObj->id;
               if($logs->login_from ==1)
                   $ios_devices_to_send[] = $logs->fcm_token;
               if($logs->login_from ==2)
                   $android_devices_to_send[] = $logs->fcm_token;
           }
   
           $probObj = Property::find($account_id);
           $title = "Aerea Home - ".$probObj->company_name;
           $message = "Warning from marketplace";
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
           

            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/marketplace/reports/$reportObj->ref_id?page=$page")->with('status', 'Warning message has been sent!');
            }
            else
                return redirect("opslogin/marketplace/reports/$reportObj->ref_id")->with('status', 'Warning message has been sent!');
        }
    }

  
    public function blockedusers()
    {
        $q = $property = $first_name = $ticket = $subject = $category = '';
        if(Auth::user()->role_id ==1){
            $lists = MpAdsBlockUserByAdmin::orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
        }
        else{
            $account_id= Auth::user()->account_id;
            $lists = MpAdsBlockUserByAdmin::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
        }
        $currentURL = url()->full();

        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
            session()->put('page', $page[1]);
        }else{
            session()->forget('page');
        }
        return view('admin.marketplace.blockedusers', compact('lists','q','first_name','ticket','subject','category'));

    }
    public function unblockuser($id)
    {   
        $ReportObj = MpAdsBlockUserByAdmin::find($id);
		$ReportObj->status =2;
        $ReportObj->save();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/marketplace/blockedusers?page=$page")->with('status', 'User unblocked!');
        }
        else
            return redirect("opslogin/marketplace/blockedusers")->with('status', 'User unblocked!');
    }
    public function blockagainuser($id)
    {   
        $ReportObj = MpAdsBlockUserByAdmin::find($id);
		$ReportObj->status =1;
        $ReportObj->save();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/marketplace/blockedusers?page=$page")->with('status', 'User blocked!');
        }
        else
            return redirect("opslogin/marketplace/blockedusers")->with('status', 'User blocked!');
    }
}
