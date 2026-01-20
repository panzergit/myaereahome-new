<?php

namespace App\Http\Controllers;
use Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\EformSetting;
use App\Models\v7\EformChangeAddress;
use App\Models\v7\EformMovingSubCon;

use App\Models\v7\UserLog;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\Property;
use App\Models\v7\UserNotificationSetting;

use App\Models\v7\Unit;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\v7\InboxMessage;

class EformChangeAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
    {
        session()->forget('current_page');
        $ticket  =  $name = $status = $option = $unit ='';
        $account_id = Auth::user()->account_id;
        $visitor_app_url = env('VISITOR_APP_URL');

        $forms = EformChangeAddress::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
        return view('admin.eform_address.index', compact('forms','ticket','unit','name','status','option','visitor_app_url'));
    }

    public function new()
    {
        session()->forget('current_page');
        $ticket  =  $name = $status = $option = $unit ='';
        $date = Carbon::now()->subDays(7);
        $account_id = Auth::user()->account_id;
        $forms = EformChangeAddress::where('account_id',$account_id)->where('status',0)->where('view_status',0)->where('created_at', '>=', $date)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        return view('admin.eform_address.new', compact('forms','ticket','unit','name','status','option'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::pluck('name','id')->all();
        return view('admin.eform_address.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // print_r($request->unit);
        $validator = Validator::make($request->all(), [ 
            'defect_location' => 'required|unique:defect_locations' 
        ]);
        if ($validator->fails()) { 

             return redirect('opslogin/eform/changeaddress/create')->with('status', 'Defect Location already exist!');         
        }
        $input = $request->all();

        
        DefectLocation::create($input);
        return redirect('opslogin/eform/changeaddress');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Respons
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $eformObj = EformChangeAddress::find($id);
        $eformObj->view_status = 1;
        $eformObj->save();

        $file_path = env('APP_URL')."/storage/app";
        return view('admin.eform_address.edit', compact('eformObj','file_path'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
       
        $title = "Your Changing Mailing Address application .".$status;
        InboxMessage::where('ref_id', $eformObj->id)->where('type',14)
                ->update(['title'=>$title,'event_status' => $status]);
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
                if(Session::get('current_page') =='unit_summary'){
                    $return_url = 'opslogin/configuration/unit_summary/'.$eformObj->unit_no.'/9';
                    return redirect($return_url)->with('status', 'Changing Mailing Address application status has been updated!');
                }
                else{
                    return redirect('opslogin/eform/changeaddress')->with('status', 'Changing Mailing Address application status has been updated!');
                }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $eformObj = EformChangeAddress::find($id);
        EformChangeAddress::findOrFail($id)->delete();

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$eformObj->unit_no.'/9';
            return redirect($return_url)->with('status', 'Record deleted successfully!');
        }
        else{
            return redirect('opslogin/eform/changeaddress')->with('status', 'Record deleted successfully!');
        }
    }

  


   


    public function search(Request $request)
    {
        $ticket  =  $name = $status = $option = $unit ='';

        $account_id = Auth::user()->account_id;
        $option = $request->input('option'); 
        $visitor_app_url = env('VISITOR_APP_URL');

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
        $forms =  EformChangeAddress::where('account_id',$account_id)->where(function ($query) use ($ticket,$unit,$units,$userids,$name,$status) {
            if($status !='' )
                $query->where('status', $status);
            if( $unit !='')
                $query->whereIn('unit_no', $units);
            if( $name !='')
                $query->whereIn('user_id', $userids);
            if($ticket !='')
                $query->where('ticket', 'LIKE', '%'.$ticket .'%');
        })->orderby('id', 'desc')->paginate(env('PAGINATION_ROWS'));
        

        /*if ($option != '') {
            if($option == 'name') {
                $name = $request->input('name');
                $forms =  EformChangeAddress::where('eform_address_changes.account_id',$account_id)->join('users', 'users.id', '=', 'eform_address_changes.user_id')->where('users.name', 'LIKE', '%'.$name .'%')
                    ->orderby('eform_address_changes.id','desc')->paginate(env('PAGINATION_ROWS'));
            }
            if($option == 'ticket') {
                $ticket = $request->input('ticket');
                $forms =  EformChangeAddress::where('account_id',$account_id)->where('ticket', 'LIKE', '%'.$ticket .'%')
                    ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
            }

            if($option == 'unit' ) { 
                $unit = $request->input('unit');
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->get();
                $units = array();
                if(isset($unitObj)){
                    foreach($unitObj as $unitid){
                        $units[] = $unitid->id;
                    }
                }

                $forms =  EformChangeAddress::where('eform_address_changes.account_id',$account_id)->whereIn('eform_address_changes.status', ['0'])
                    ->join('users', 'users.id', '=', 'eform_address_changes.user_id')->whereIn('users.unit_no', $units)
                    ->orderby('eform_address_changes.id','desc')->paginate(env('PAGINATION_ROWS'));
                
            }
            if($option == 'status') {
                $status = $request->input('status');
                if($request->input('status') =='a'){
                    $forms =  EformChangeAddress::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
                }
                else
                    {
                        
                        $forms =  EformChangeAddress::where('account_id',$account_id)->where('status', $status)
                    ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
                }
            }
           
        }*/
            return view('admin.eform_address.index', compact('forms','ticket','unit','name','status','option','visitor_app_url'));

        
    }

    public function updatecancelstatus(Request $request){

        $input = $request->all();
         $reason ='';

       if(isset($input['reason']))
           $reason = $input['reason'];

       $bookid = $input['bookId'];
       $status = $input['status']; //cancelled

       $eformObj = EformChangeAddress::find($bookid);


      $inbox = InboxMessage::where('ref_id', $bookid)->where('type',5)->first();
      
      if(isset($inbox) && $inbox->id !=''){
       $inboxObj = InboxMessage::find($inbox->id);
       $inboxObj->event_status = $status;
       $inboxObj->save();
       }

       
        JoininspectionAppointment::where('id', $bookid)
               ->update(['status' => $status,'reason'=>$reason]);

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$eformObj->unit_no.'/9';
            return redirect($return_url)->with('status', 'Appointment Cancelled!');
        }
        else if(isset($input['return_url']))
           return redirect('opslogin/defect/new')->with('status', "Appointment Cancelled!!");
        else
           return redirect('opslogin/defect')->with('status', "Appointment Cancelled!!");
    }


}
