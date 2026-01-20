<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\ChatBox;
use App\Models\v7\ChatBoxReport;
use App\Models\v7\ChatBoxCategory;
use App\Models\v7\ChatBoxCommentReport;
use App\Models\v7\ChatBoxComment;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\Property;
use App\Models\v7\User;
use App\Models\v7\Role;
use App\Models\v7\ChatBoxBlockUserByAdmin;
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

class ChatterBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $q = $property = $first_name = $ticket = $subject = $category = '';
        $categories = ChatBoxCategory::orderby('name','asc')->pluck('name', 'id')->all();
        session()->forget('current_page');

        if(Auth::user()->role_id ==1){
            $lists = ChatBox::orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
            $properties = Property::orderby('company_name','asc')->pluck('company_name', 'id')->all();
            $currentURL = url()->full();

            $page = explode("=",$currentURL);
            if(isset($page[1]) && $page[1]>0){
                    session()->put('page', $page[1]);
            }else{
                    session()->forget('page');
            }
            return view('admin.chatterbox.adminindex', compact('lists','q','properties','property','first_name','ticket','subject','category','categories'));
        }
        else{
            $account_id = Auth::user()->account_id;
            $lists = ChatBox::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
            $properties = Property::where('id',$account_id)->orderby('company_name','asc')->pluck('company_name', 'id')->all();

            $currentURL = url()->full();

            $page = explode("=",$currentURL);
            if(isset($page[1]) && $page[1]>0){
                    session()->put('page', $page[1]);
            }else{
                    session()->forget('page');
            }
            return view('admin.chatterbox.index', compact('lists','q','properties','property','first_name','ticket','subject','categories','category'));
        }
        

    }

    public function allreports()
    {
        $q = $property = $first_name = $ticket = $subject = $category = '';
        $categories = ChatBoxCategory::orderby('name','asc')->pluck('name', 'id')->all();
        session()->put('current_page', 'allreports');

        if(Auth::user()->role_id ==1){
            $lists = ChatBoxReport::where('status',1)->orderby('id','desc')->groupBy('ref_id')->paginate(env('PAGINATION_ROWS')); 
            $properties = Property::orderby('company_name','asc')->pluck('company_name', 'id')->all();
            $currentURL = url()->full();

            $page = explode("=",$currentURL);
            if(isset($page[1]) && $page[1]>0){
                    session()->put('page', $page[1]);
            }else{
                    session()->forget('page');
            }
            return view('admin.chatterbox.adminallreports', compact('lists','q','properties','property','first_name','ticket','subject','category','categories'));
        }
        else{
            $account_id = Auth::user()->account_id;
            $lists = ChatBoxReport::where('account_id',$account_id)->where('status',1)->orderby('id','desc')->groupBy('ref_id')->paginate(env('PAGINATION_ROWS')); 
            $currentURL = url()->full();

            $page = explode("=",$currentURL);
            if(isset($page[1]) && $page[1]>0){
                    session()->put('page', $page[1]);
            }else{
                    session()->forget('page');
            }
            return view('admin.chatterbox.allreports', compact('lists','q','first_name','ticket','subject','categories','category'));
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
        ChatBoxComment::where('ref_id',$id)->delete();
        ChatBoxCommentReport::where('ref_id',$id)->delete();
        ChatBoxReport::where('ref_id',$id)->delete();
        ChatBox::findOrFail($id)->delete();

        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/resichat?page=$page")->with('status', 'Record has been deleted!');
        }
        else
            return redirect('opslogin/resichat')->with('status', 'Record has been deleted!');
    }

    public function search(Request $request)
    {
        $q = $property = $first_name = $ticket = $subject = $category = '';
        $first_name = $request->input('first_name');
        $ticket = $request->input('ticket');
        $subject = $request->input('subject');
        if(Auth::user()->role_id ==1){
            $property = $request->input('property');
        }
        else{
            $category = $request->input('category');
            $property = Auth::user()->account_id;
        }
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
        $lists = ChatBox::where(function ($query) use ($property,$userids,$ticket,$subject,$category) {
            if($property !=''){
                $query->where('account_id', $property);
            } 
            if($ticket !=''){
                $query->where('ticket', 'LIKE', '%' . $ticket . '%');
            } 
            if($category !=''){
                $query->where('category', $category);
            } 
            if($subject !=''){
                $query->where('subject', 'LIKE', '%' . $subject . '%');
            } 
            if(count($userids) >0){
                $query->whereIn('user_id',$userids);
            }  
        })->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 

        $properties = Property::orderby('company_name','asc')->pluck('company_name', 'id')->all();
		$types = ChatBoxCategory::get();
        $categories = ChatBoxCategory::orderby('name','asc')->pluck('name', 'id')->all();
        if(Auth::user()->role_id ==1){
            return view('admin.chatterbox.adminindex', compact('lists','q','properties','types','property','first_name','ticket','subject','category','categories'));

        }
        else{
           
            return view('admin.chatterbox.index', compact('lists','q','properties','types','property','first_name','ticket','subject','category','categories'));

        }
    }

    public function replies($id)
    {
        $q = $property = $first_name = $ticket = $subject = '';
        $ChatObj = ChatBox::find($id);
        $lists = ChatBoxComment::where('ref_id',$id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        $currentURL = url()->full();

        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        } 
        return view('admin.chatterbox.replies', compact('ChatObj','lists','q','property','first_name','subject'));

    }
    public function repliesdestroy($id)
    {
        $CommentObj = ChatBoxComment::find($id);
        ChatBoxCommentReport::where('comment_id',$id)->delete();
        ChatBoxComment::findOrFail($id)->delete();
        //echo $CommentObj->ref_id;
        //exit;
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/resichat/replies/$CommentObj->ref_id?page=$page")->with('status', 'Record has been deleted!');
        }
        else
            return redirect("opslogin/resichat/replies/$CommentObj->ref_id")->with('status', 'Record has been deleted!');
    }

    public function reports($id)
    {
        $q = $property = $first_name = $ticket = $subject = '';
        $ChatObj = ChatBox::find($id);
        $lists = ChatBoxReport::where('ref_id',$id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
        $currentURL = url()->full();
        ChatBoxReport::where('ref_id',$id)->where('view_status',1)->update(['view_status'=>2]);

        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }
        return view('admin.chatterbox.reports', compact('ChatObj','lists','q','property','first_name','subject'));

    }
    /*public function viewreports($id)
    {
        $q = $property = $first_name = $ticket = $subject = '';
        $ChatObj = ChatBox::find($id);
        $lists = ChatBoxReport::where('ref_id',$id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
        $currentURL = url()->full();
        ChatBoxReport::where('ref_id',$id)->where('view_status',1)->update(['status'=>2]);
        
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }
        return view('admin.chatterbox.viewreports', compact('ChatObj','lists','q','property','first_name','subject'));

    }*/

    public function reportsdestroy($id)
    {   
       $reportObj =  ChatBoxReport::find($id);
        ChatBoxReport::findOrFail($id)->delete();

        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/resichat/reports/$reportObj->ref_id?page=$page")->with('status', 'Record has been deleted!');
        }
        else
            return redirect("opslogin/resichat/reports/$reportObj->ref_id")->with('status', 'Record has been deleted!');
    }
    public function deactivate($id)
    {   
        $ChatObj = ChatBox::find($id);
		$ChatObj->status =2;
        $ChatObj->save();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/resichat/$ChatObj->ref_id?page=$page")->with('status', 'ResiChat has been de-activated!');
        }
        else
            return redirect("opslogin/resichat/$ChatObj->ref_id")->with('status', 'ResiChat has been de-activated!');
    }
    public function activate($id)
    {   
        $ChatObj = ChatBox::find($id);
		$ChatObj->status =1;
        $ChatObj->save();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/resichat/$ChatObj->ref_id?page=$page")->with('status', 'ResiChat has been activated!');
        }
        else
            return redirect("opslogin/resichat/$ChatObj->ref_id")->with('status', 'ResiChat has been activated!');
    }

    public function hidereport($id)
    {   
        $ReportObj = ChatBoxReport::find($id);
		$ReportObj->status =2;
        $ReportObj->save();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/resichat/reports/$reportObj->ref_id?page=$page")->with('status', 'Report has been hided!');
        }
        else
            return redirect("opslogin/resichat/reports/$reportObj->ref_id")->with('status', 'Report has been hided!');
    }
    public function showreport($id)
    {   
        $ReportObj = ChatBoxReport::find($id);
		$ReportObj->status =1;
        $ReportObj->save();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/resichat/reports/$reportObj->ref_id?page=$page")->with('status', 'Record has been activated!');
        }
        else
            return redirect("opslogin/resichat/reports/$reportObj->ref_id")->with('status', 'Record has been activated!');
    }

    public function blockuser($id)
    {   
       $reportObj =  ChatBoxReport::find($id);
       $UserObj = User::find($reportObj->user_id);
       $admin_id= Auth::user()->id;
       $check_status = ChatBoxBlockUserByAdmin::where('account_id',$reportObj->account_id)->where('block_user_id',$reportObj->user_id)->first();
       if(isset($check_status)){
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/resichat/reports/$reportObj->ref_id?page=$page")->with('status', 'User already blocked!');
        }
        else
            return redirect("opslogin/resichat/reports/$reportObj->ref_id")->with('status', 'User already blocked!');
       }else{
           $input['admin_id'] = $admin_id;
           $input['account_id'] = $reportObj->account_id;
           $input['unit_no'] = $reportObj->unit_no;
           $input['block_user_id'] = $reportObj->user_id;
           $input['type'] = 2;
           $input['ref_id'] = $reportObj->id;
           //$input['remark'] = $request->remark;
           $input['status'] = 1;
           $results = ChatBoxBlockUserByAdmin::create($input);

            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/resichat/reports/$reportObj->ref_id?page=$page")->with('status', 'User has been blocked!');
            }
            else
                return redirect("opslogin/resichat/reports/$reportObj->ref_id")->with('status', 'User has been blocked!');
       }
    }

    public function warninguser($id)
    {   
       $reportObj =  ChatBoxReport::find($id);
       $UserObj = User::find($reportObj->user_id); 
       $account_id= Auth::user()->account_id;
       
       $probObj = Property::find($account_id);
       $title = "Aerea Home - ".$probObj->company_name;
       $message = "Warning from ResiChat";
       //Start Insert into notification module
       $notification = array();
       $notification['account_id'] = $reportObj->account_id;
       $notification['user_id'] = $reportObj->user_id;
       $notification['unit_no'] = $reportObj->unit_id;
       $notification['module'] = 'ResiChat';
       $notification['ref_id'] = $reportObj->id;
       $notification['title'] = $title;
       $notification['message'] = $message;
   
       UserNotification::insert($notification);
   //End Insert into notification module

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
                return redirect("opslogin/resichat/reports/$reportObj->ref_id?page=$page")->with('status', 'Warning message has been sent!');
            }
            else
                return redirect("opslogin/resichat/reports/$reportObj->ref_id")->with('status', 'Warning message has been sent!');
        }
    }
    public function blockedusers()
    {
        $q = $property = $first_name = $ticket = $subject = $category = '';
        if(Auth::user()->role_id ==1){
            $lists = ChatBoxBlockUserByAdmin::orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
            $currentURL = url()->full();
    
            $page = explode("=",$currentURL);
            if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
            }else{
                session()->forget('page');
            }
            return view('admin.chatterbox.adminblockedusers', compact('lists','q','first_name','ticket','subject','category'));
        }
        $account_id= Auth::user()->account_id;
        $lists = ChatBoxBlockUserByAdmin::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
        $currentURL = url()->full();

        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
            session()->put('page', $page[1]);
        }else{
            session()->forget('page');
        }
        return view('admin.chatterbox.blockedusers', compact('lists','q','first_name','ticket','subject','category'));

    }

    public function unblockuser($id)
    {   
        $ReportObj = ChatBoxBlockUserByAdmin::find($id);
		$ReportObj->status =2;
        $ReportObj->save();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/resichat/blockedusers?page=$page")->with('status', 'User unblocked!');
        }
        else
            return redirect("opslogin/resichat/blockedusers")->with('status', 'User unblocked!');
    }
    public function blockagainuser($id)
    {   
        $ReportObj = ChatBoxBlockUserByAdmin::find($id);
		$ReportObj->status =1;
        $ReportObj->save();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/resichat/blockedusers?page=$page")->with('status', 'User blocked!');
        }
        else
            return redirect("opslogin/resichat/blockedusers")->with('status', 'User blocked!');
    }

}
