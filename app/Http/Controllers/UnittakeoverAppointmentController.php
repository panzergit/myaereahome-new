<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\v7\UnittakeoverAppointment;
use App\Models\v7\UserLog;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserNotificationSetting;
use App\Models\v7\Property;

use Illuminate\Http\Request;
use App\Models\v7\Unit;
use Validator;
use App\Models\v7\User;
use App\Models\v7\Role;
use Auth;
use Carbon\Carbon;
use DB;
use Session;
use App\Models\v7\InboxMessage;

class UnittakeoverAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

   private   $timeslots = array('9:00 AM'=>'9:00 AM','9:30 AM'=>'9:30 AM','10:00 AM'=>'10:00 AM','10:30 AM'=>'10:30 AM','11:00 AM'=>'11:00 AM','11:30 AM'=>'11:30 AM','1:30 PM'=>'1:30 PM','2:00 PM'=>'2:00 PM','2:30 PM'=>'2:30 PM','3:00 PM'=>'3:00 PM','3:30 PM'=>'3:30 PM','4:00 PM'=>'4:00 PM');

     public function index()
    {
        session()->forget('current_page');

        $q = $option =$unit = $status = $name = $users = $month ='';

        $account_id = Auth::user()->account_id;
      
         $units = UnittakeoverAppointment::where('account_id',$account_id)->where('status', '0')
                ->whereDate('appt_date', '>=', Carbon::now('Asia/Singapore')) 
                ->orderby('id','desc')
                ->paginate(env('PAGINATION_ROWS')); 

                $currentURL = url()->full();
                $page = explode("=",$currentURL);
                if(isset($page[1]) && $page[1]>0){
                    session()->put('page', $page[1]);
                }else{
                    session()->forget('page');
                }  

        return view('admin.takeover.index', compact('units','q'));
    }

    public function lists(Request $request)
    {

        session()->forget('current_page');

       $q = $option =$unit = $status = $name = $users = $month ='';
       // $roles = Role::pluck('name', 'id')->all();
       $account_id = Auth::user()->account_id;
        $units = UnittakeoverAppointment::where('account_id',$account_id)
                ->orderby('id','desc')
                ->paginate(env('PAGINATION_ROWS'));   

        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
            session()->put('page', $page[1]);
        }else{
            session()->forget('page');
        }
        
        //Dashboard code - start
        $totalUnits = UnittakeoverAppointment::where('account_id',$account_id)->count();   
        $app_by_status = [
            [
                'id' => '0',
                'name' => 'New',
                'value' => 0
            ],
            [
                'id' => '1',
                'name' => 'Cancelled',
                'value' => 0
            ],
            [
                'id' => '2',
                'name' => 'On Schedule',
                'value' => 0
            ],
            [
                'id' => '3',
                'name' => 'Done',
                'value' => 0
            ]
        ];
        $app_by_status_count = [
            [
                'id' => '0',
                'name' => 'New',
                'value' => 0
            ],
            [
                'id' => '1',
                'name' => 'Cancelled',
                'value' => 0
            ],
            [
                'id' => '2',
                'name' => 'On Schedule',
                'value' => 0
            ],
            [
                'id' => '3',
                'name' => 'Done',
                'value' => 0
            ]
        ];
        foreach($app_by_status as $sk => $s) $app_by_status[$sk]['value'] = number_format(((UnittakeoverAppointment::where([['account_id','=',$account_id],['status','=',$s['id']]])
            ->count()/$totalUnits)*100),2,'.','');
        foreach($app_by_status_count as $sk => $s) $app_by_status_count[$sk]['value'] = UnittakeoverAppointment::where([['account_id','=',$account_id],['status','=',$s['id']]])->count();
        
        $keyCollections = UnittakeoverAppointment::where([
            'account_id' => $account_id
            ])->get();
            
        $totalUnits = Unit::where('account_id',$account_id)->where('status',1)->get()->count();

        $chartThree = [
            ['y' => $totalUnits, 'label' => 'Total Units'],
            ['y' => $keyCollections->where('status',3)->count(), 'label' => 'Collected Keys'],
            ['y' => $keyCollections->where('status',2)->count(), 'label' => 'Scheduled Appointments'],
            ['y' => $keyCollections->where('status',0)->count(), 'label' => 'No actions yet'],
            ['y' => $keyCollections->where('status',1)->count(), 'label' => 'Cancelled']
        ];
            
        //Dashboard code - end

        return view('admin.takeover.summary', compact('units','q','status','name','option','unit','month','app_by_status','app_by_status_count', 'chartThree'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   

        $user = Auth::user();

        $obj = new UnittakeoverAppointment();
        $times = $obj->timeslots($user->account_id);

        $users = User::pluck('name','id')->all();
        if($user->role_id ==6)
            return view('admin.takeover.create', compact('users','times'));
        else
            return view('user.takeoverbook', compact('users','times'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       //$request->validate(['email' => 'required|unique:users','name'=>'min:2|max:25','last_name'=>'min:2|max:25','password' =>'min:6||max:25']);

        $user = Auth::user();
        $input = $request->all();

        //Auth::user()->userinfo->unit_no;


        $input['user_id'] = Auth::user()->id;
        if(isset(Auth::user()->userinfo->unit_no))
            $input['unit_no'] = Auth::user()->userinfo->unit_no;;

        $record = UnittakeoverAppointment::create($input);


        if($user->role_id ==6)
            return redirect('opslogin/takeover_appt');
        else {
             
            return redirect('opslogin/appt_thankyou')->with('status', $record->id);
        }

        
    }

    public function thankyou()
    {

        $q ='';
        $user = Auth::user();

        if(session('status')) {

            $id = session('status');
            
            $units = UnittakeoverAppointment::find($id);

            return view('user.takeoverthankyou', compact('units'));
        }
        else{
            return redirect('opslogin/book_appt');
        }
       
    }

    public function message()
    {

        $q ='';
        
        $user = Auth::user();

        $units = UnittakeoverAppointment::where('user_id',$user->id)->orderby("id",'desc')->first();

        return view('user.takeovermessage', compact('units'));
       
       
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

        $takeoverObj = UnittakeoverAppointment::find($id);
        
        $users = User::pluck('name','id')->all();
        
        $obj = new UnittakeoverAppointment();
        $user = Auth::user();
        $times = $obj->timeslots($user->account_id);	

        return view('admin.takeover.edit', compact('takeoverObj','users','times'));
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


        $takeoverObj = UnittakeoverAppointment::find($id);


        $result = DB::table('unittakeover_appointments')->where('appt_date', $request->input('appt_date'))
                        ->where('appt_time',$request->input('appt_date'))
                        ->whereNotIn('id', [$id])->first();


      
        if (isset($result)) { 
             return redirect("opslogin/takeover_appt/$id/edit")->with('status', 'Date & Time of the Appointment already taken !');         
        }


        $takeoverObj->appt_date = $request->input('appt_date');
        $takeoverObj->appt_time = $request->input('appt_time');
        $takeoverObj->status = $request->input('status');
        $takeoverObj->save();

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
            //$message = $notification['message'];
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

        $title = "Your appointment for Unit Take Over has been changed ";

        InboxMessage::where('ref_id', $id)->where('type',4)
                ->update(['title'=>$title,'booking_date'=>$request->input('appt_date'),'booking_time'=>$request->input('appt_time'),'event_status' => $request->input('status')]);



                if(Session::get('current_page') =='unit_summary'){
                    $return_url = 'opslogin/configuration/unit_summary/'.$takeoverObj->unit_no.'/14';
                    return redirect($return_url)->with('status', 'Appointment has been updated!');
                }
                else
                    return redirect('opslogin/takeover_appt/lists')->with('status', 'Appointment has been updated!');

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
        $takeoverObj = UnittakeoverAppointment::find($id);

        //UnittakeoverAppointment::findOrFail($id)->delete();

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$takeoverObj->unit_no.'/14';
            return redirect($return_url)->with('status', 'Appointment has been updated!');
        }
        else
            return redirect('opslogin/configuration/unit')->with('status', 'Unit deleted successfully!');
    }


    public function gettimeslots(Request $request)
    {
        
        $data = array();
        $obj = new UnittakeoverAppointment();
        $times = $obj->timeslots($user->account_id);
        
        $selecteddate = $request->date;

        foreach($times as $time){
            //echo $time;
            $lists = DB::table("unittakeover_appointments")->where('appt_date',$selecteddate)->where('appt_time',$time)->whereNotIn('status', [1])->get();
            $recordcount = count($lists);
            $record =array('time'=>$time,'count'=>$recordcount);

            $data[] = $record;

        }
        

        if(isset($data))
            return json_encode($data);
        else
             return json_encode($data);

    }


     public function updatecancelstatus(Request $request){

         $input = $request->all();
          $reason ='';

        if(isset($input['reason']))
            $reason = $input['reason'];
        $bookid = $input['bookId'];
        $status = $input['status']; //cancelled

        $takeoverObj = UnittakeoverAppointment::find($bookid);

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
        
        
            if(Session::get('current_page') =='unit_summary'){
                $return_url = 'opslogin/configuration/unit_summary/'.$takeoverObj->unit_no.'/14';
                return redirect($return_url)->with('status', 'Booking Cancelled!');
            }
            else if(isset($input['return_url']))
                return redirect('opslogin/takeover_appt/lists')->with('status', "Booking Cancelled!!");
            else
                return redirect('opslogin/takeover_appt')->with('status', "Booking Cancelled!!");
     }



     public function updateconfirmstatus(Request $request){


         $input = $request->all();
          $reason ='';

        $bookid = $input['Id'];
        $status = $input['status']; //cancelled

        $takeoverObj = UnittakeoverAppointment::find($bookid);

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

            if(Session::get('current_page') =='unit_summary'){
                $return_url = 'opslogin/configuration/unit_summary/'.$takeoverObj->unit_no.'/14';
                return redirect($return_url)->with('status', 'Booking Confirmed!');
            }
            else
                return redirect('opslogin/takeover_appt')->with('status', "Booking Confirmed!!");
        
     }

    public function search(Request $request)
    {

        $q = $option = $status = $name = $users = $month = $from_date = $to_date = '';
        $account_id = Auth::user()->account_id;
        $option = $request->input('option'); 
        $name = $request->input('name');
        $status = $request->input('status');
        $unit = $request->input('unit');
        //$unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->get();
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
        if($month !=''){          
            $from_date = $month;
            $to_date  = date('Y-m-t', strtotime($month));
        }

       /* $units =  UnittakeoverAppointment::where('unittakeover_appointments.account_id',$account_id)->whereNotIn('unittakeover_appointments.status', ['0'])->join('users', 'users.id', '=', 'unittakeover_appointments.user_id')
        ->where(function ($query) use ($name,$unit,$units,$month,$from_date,$to_date,$status) {
            if($status !='')
                $query->where('unittakeover_appointments.status',$status);
            if($unit !='')
                $query->whereIn('unittakeover_appointments.unit_no', $units);
            if($month !='')
                $query->whereBetween('unittakeover_appointments.appt_date',array($from_date,$to_date));
            
        })->whereNotIn('unittakeover_appointments.status', ['0'])->paginate(env('PAGINATION_ROWS'));*/

        $units =  UnittakeoverAppointment::where('account_id',$account_id)
        ->where(function ($query) use ($name,$unit,$units,$month,$from_date,$to_date,$status) {
            if($status !='')
                $query->where('status',$status);
            if($unit !='')
                $query->whereIn('unit_no', $units);
            if($month !='')
                $query->whereBetween('appt_date',array($from_date,$to_date));
            
        })->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
       

        /*

        if ($option != '') {
            if($option == 'name') {

                $units =  UnittakeoverAppointment::where('account_id',$account_id)->whereNotIn('unittakeover_appointments.status', ['0'])
                    ->join('users', 'users.id', '=', 'unittakeover_appointments.user_id')->where('users.name', 'LIKE', '%'.$name .'%')
->orderby('unittakeover_appointments.appt_date','asc')->get();

               
            }
            if($option == 'month' ) { 
                $month = $request->input('month');
                $from_date = $month."-1";
                $to_date  = $month."-31";
               

                $units =  UnittakeoverAppointment::where('account_id',$account_id)->whereNotIn('status', ['0'])->where(function($query) use ($from_date,$to_date){
                    
                    if($from_date !=''){
                        $query->whereBetween('appt_date',array($from_date,$to_date));
                    }
                    
                })->orderBy('appt_date','DESC')->paginate(env('PAGINATION_ROWS'));   
                
            }

            if($option == 'status') {
                 $units = UnittakeoverAppointment::where('account_id',$account_id)->where('status', $status)
                ->orderby('appt_date','desc')
                ->paginate(env('PAGINATION_ROWS'));   
            }

            if($option == 'unit' ) { 

                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->get();
                $units = array();
                if(isset($unitObj)){
                    foreach($unitObj as $unitid){
                        $units[] = $unitid->id;
                    }
                }
                
                $units = UnittakeoverAppointment::where('account_id',$account_id)->whereIn('unit_no', $units)->whereNotIn('status', ['0'])->orderby('appt_date','asc')->paginate(env('PAGINATION_ROWS'));   
            }
        }
        */
            
            return view('admin.takeover.summary', compact('units','q','status','name','option','unit','month'));

    }

}
