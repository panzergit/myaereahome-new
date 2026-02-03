<?php

namespace App\Http\Controllers;
use Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\EformSetting;
use App\Models\v7\EformDoorAccess;
use App\Models\v7\EformDoorAccesscardPayment;
use App\Models\v7\EformDoorAccesscardAck;

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

class EformDoorAccessController extends Controller
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
        $visitor_app_url = url('visitors');

        $forms = EformDoorAccess::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
        return view('admin.eform_door.index', compact('forms','ticket','unit','name','status','option','visitor_app_url'));
    }

    public function new()
    {
        session()->forget('current_page');

        $ticket  =  $name = $status = $option = $unit ='';
        $date = Carbon::now()->subDays(7);
        $account_id = Auth::user()->account_id;
        $forms = EformDoorAccess::where('account_id',$account_id)->where('status',0)->where('view_status',0)->where('created_at', '>=', $date)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        return view('admin.eform_door.new', compact('forms','ticket','unit','name','status','option'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::pluck('name','id')->all();
        return view('admin.eform_door.create', compact('users'));
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

             return redirect('opslogin/eform/dooraccess/create')->with('status', 'Defect Location already exist!');         
        }
        $input = $request->all();

        
        DefectLocation::create($input);
        return redirect('opslogin/eform/dooraccess');
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
        $eformObj = EformDoorAccess::find($id);
        $eformObj->view_status = 1;
        $eformObj->save();

        $file_path = image_storage_domain();
        return view('admin.eform_door.edit', compact('eformObj','file_path'));
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
        $notification['module'] = 'eform_dooraccess_card';
        $notification['ref_id'] = $eformObj->id;
        $notification['title'] = 'Door Access Card E-form';
        $notification['message'] = 'There is an update from the management in regards to your submitted Door Access Card E-form';
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
            $message = "Door Access Card E-form Updated";
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
            $return_url = 'opslogin/configuration/unit_summary/'.$eformObj->unit_no.'/7';
            return redirect($return_url)->with('status', 'Door access card application status has been updated!');
        }
        else{
            return redirect('opslogin/eform/dooraccess')->with('status', 'Door access card application status has been updated!');
        }
    }


    public function payment($id)
    {
        //
        $eformObj = EformDoorAccess::find($id);
        $eformObj->view_status = 1;
        $eformObj->save();
        $account_id = Auth::user()->account_id;

        $eformsettingsObj = EformSetting::where('account_id', $account_id)->where('eform_type', 42)->first();
        $file_path = image_storage_domain();
        return view('admin.eform_door.payment', compact('eformObj','file_path','eformsettingsObj'));
    }
    public function paymentsave(Request $request,$id)
    {
        //
      
        $eformObj = EformDoorAccess::find($id);

        $account_id = Auth::user()->account_id;

        $login_id = Auth::user()->id;

        if ($request->input('payment_id') != null) {

            $paymentObj = EformDoorAccesscardPayment::find($request->input('payment_id'));
            
            
            $paymentObj['reg_id'] = $id;
            $paymentObj['manager_id'] = $login_id;
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
            $payment['manager_id'] = $login_id;
            $payment['payment_option'] = $request->input('payment_option');

            if($payment['payment_option'] ==1){

                if($request->input('cheque_amount') != null)
                    $payment['cheque_amount'] = $request->input('cheque_amount');
                
                if($request->input('cheque_received_date') != null)
                    $payment['cheque_received_date'] = $request->input('cheque_received_date');

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

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$eformObj->unit_no.'/7';
            return redirect($return_url)->with('status', 'Payment details updated!');
        }
        else{
            return redirect('opslogin/eform/dooraccess')->with('status', 'Payment details updated!');
        }         
    }

    public function acknowledgement($id)
    {
        //
        $eformObj = EformDoorAccess::find($id);
        $eformObj->view_status = 1;
        $eformObj->save();
        $account_id = Auth::user()->account_id;

        $eformsettingsObj = EformSetting::where('account_id', $account_id)->where('eform_type', 42)->first();
        $file_path = image_storage_domain();
        return view('admin.eform_door.ack', compact('eformObj','file_path','eformsettingsObj'));
    }
    public function acknowledgementsave(Request $request,$id)
    {
        //
      
        $eformObj = EformDoorAccess::find($id);

        $account_id = Auth::user()->account_id;

        $login_id = Auth::user()->id;

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

                if($request->input('signature') != null)   
                    $ackObj['signature'] = $request->input('signature');

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

                if($request->input('signature') != null)   
                    $ack['signature'] = $request->input('signature');

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
        $notification['module'] = 'eform_renovation';
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

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$eformObj->unit_no.'/7';
            return redirect($return_url)->with('status', 'Acknowledgement details updated!');
        }
        else{
            return redirect('opslogin/eform/dooraccess')->with('status', 'Acknowledgement details updated!');  
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
        $eformObj = EformDoorAccess::find($id);

        EformDoorAccesscardPayment::where('reno_id', $id)->delete();
		EformDoorAccesscardAck::where('reno_id', $id)->delete();
        EformDoorAccess::findOrFail($id)->delete();

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$eformObj->unit_no.'/7';
            return redirect($return_url)->with('status', 'Record deleted successfully!');
        }
        else{
            return redirect('opslogin/eform/dooraccess')->with('status', 'Record deleted successfully!');
        }
    }

  


   


    public function search(Request $request)
    {
        $ticket  =  $name = $status = $option = $unit ='';

        $account_id = Auth::user()->account_id;
        $option = $request->input('option'); 
        $visitor_app_url = url('visitors');
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
        $forms =  EformDoorAccess::where('account_id',$account_id)->where(function ($query) use ($ticket,$unit,$units,$userids,$name,$status) {
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
                $forms =  EformDoorAccess::where('eform_door_accesses.account_id',$account_id)->join('users', 'users.id', '=', 'eform_door_accesses.user_id')->select('eform_door_accesses.*','users.name')->where('users.name', 'LIKE', '%'.$name .'%')
                    ->orderby('eform_door_accesses.id','desc')->paginate(env('PAGINATION_ROWS'));
            }
            if($option == 'ticket') {
                $ticket = $request->input('ticket');
                $forms =  EformDoorAccess::where('account_id',$account_id)->where('ticket', 'LIKE', '%'.$ticket .'%')
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

                $forms =  EformDoorAccess::where('eform_door_accesses.account_id',$account_id)
                    ->join('users', 'users.id', '=', 'eform_door_accesses.user_id')->whereIn('users.unit_no', $units)
                    ->orderby('eform_door_accesses.id','desc')->paginate(env('PAGINATION_ROWS'));
                
            }
            if($option == 'status') {
                $status = $request->input('status');
                if($request->input('status') =='a'){
                    $forms =  EformDoorAccess::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
                }
                else
                    {
                        
                        $forms =  EformDoorAccess::where('account_id',$account_id)->where('status', $status)
                    ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
                }
            }
        }
            */
            return view('admin.eform_door.index', compact('forms','ticket','unit','name','status','option','visitor_app_url'));

       
    }

    public function updatecancelstatus(Request $request){

        $input = $request->all();
         $reason ='';

       if(isset($input['reason']))
           $reason = $input['reason'];

       $bookid = $input['bookId'];
       $status = $input['status']; //cancelled

       $eformObj = EformDoorAccess::find($bookid);


      $inbox = InboxMessage::where('ref_id', $bookid)->where('type',5)->first();
      
      if(isset($inbox) && $inbox->id !=''){
       $inboxObj = InboxMessage::find($inbox->id);
       $inboxObj->event_status = $status;
       $inboxObj->save();
       }

       
        JoininspectionAppointment::where('id', $bookid)
               ->update(['status' => $status,'reason'=>$reason]);
               
        if(Session::get('current_page') =='unit_summary'){
                $return_url = 'opslogin/configuration/unit_summary/'.$eformObj->unit_no.'/7';
                return redirect($return_url)->with('status', 'Appointment Cancelled!!');
        }
        else if(isset($input['return_url']))
           return redirect('opslogin/defect/new')->with('status', "Appointment Cancelled!!");
       else
           return redirect('opslogin/defect')->with('status', "Appointment Cancelled!!");
    }


}
