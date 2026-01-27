<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\FacilityType;
use App\Models\v7\FacilityBooking;
use App\Models\v7\UserLog;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\Property;
use App\Models\v7\Unit;
use App\Models\v7\UserNotificationSetting;

use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use App\Models\v7\Building;

use DB;
use Auth;
use Session;
use Carbon\Carbon;


use App\Models\v7\InboxMessage;

class FacilityBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
    {
        session()->forget('current_page');

        $ticket  =  $name = $status = $option = $unit = $filter = $category = $fromdate = $todate = $month = $building = '';
        $account_id = Auth::user()->account_id;
        $types = FacilityType::where('account_id',$account_id)->pluck('facility_type', 'id')->all();
        $bookings = FacilityBooking::where('account_id',$account_id)->wherein('status',[1,2])->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));   
        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

        if($request->has('view') && $request->view=='dashboard')
        {
            $units = ($request->has('unit') && trim($request->unit)!='' ) ? $request->unit : '';
            $year = $request->has('year') ? $request->year : date('Y');
            $bookings = FacilityBooking::where('account_id',$account_id)->whereYear('created_at',$year)
                ->when((trim($request->status)!=''), fn($q) => $q->where('status',$request->status))
                ->when((trim($request->category)!=''), fn($q) => $q->where('type_id',$request->category))
                ->when((trim($request->unit)!=''), fn($q) => $q->where('unit_no',$request->unit))
                ->get();
            $totalBookings = $bookings->count();
            $totalBookingsNew = $bookings->where('status','0')->values()->count();
            $totalBookingsConfirmed = $bookings->where('status','2')->values()->count();
            $totalBookingsCancelled = $bookings->where('status','1')->values()->count();
            $totalBookingsFacilities = $bookings->where('status','0')->values()->count();
            
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->startOfMonth();
            
            if($request->has('year')){
                if($request->year!=date('Y')){
                    $start = Carbon::parse($request->year.'-01-01')->startOfMonth();
                    $end = Carbon::parse($request->year.'-12-01')->endOfMonth();
                }
            }
            
            $dates = [];
            
            while ($start->lte($end)) {
                $dates[] = [
                    'date' => $start->format('Y-n-j'),
                    'defects' => FacilityBooking::where('account_id',$account_id)->whereYear('created_at',$start->copy()->format('Y'))->whereMonth('created_at',$start->copy()->format('m'))->count()
                ];
                $start->addMonth();
            }
            
            $defactsByFacility = FacilityType::where('account_id',$account_id)->where('status',1)
                ->when((trim($request->category)!=''),fn($q) => $q->where('id', $request->category))
                ->orderBy('facility_type')->get()->map(function($q) use($account_id,$year,$units) {
                return [
                    'name' => ucwords(strtolower($q->facility_type)),
                    'defects' => FacilityBooking::where('type_id',$q->id)
                        ->where('account_id',$account_id)
                        ->when($units,fn($q) => $q->where('unit_no', $units))
                        ->whereYear('created_at', $year)
                        ->count(),
                ];
            });
            
            $compactData = compact('totalBookings', 'totalBookingsNew', 'totalBookingsConfirmed', 'totalBookingsCancelled', 'totalBookingsFacilities', 'dates', 'defactsByFacility', 'types', 'category', 'status', 'option', 'fromdate', 'todate', 'ticket', 'unit', 'month', 'filter','buildings','building');
        }else{
            $compactData = compact('bookings','ticket','unit','name','status','option','filter','category','types','month','fromdate','todate','buildings','building');
        }

        return view('admin.facilitybooking.index', $compactData);
    }

    public function new()
    {
        session()->forget('current_page');

        $ticket  =  $name = $status = $option = $unit = $filter = '';
        $account_id = Auth::user()->account_id;
        $bookings = FacilityBooking::where('account_id',$account_id)->where('view_status',0)->where('status',0)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }

        return view('admin.facilitybooking.new', compact('bookings','ticket','unit','name','status','option','filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::pluck('name','id')->all();
        return view('admin.defect.create', compact('users'));
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

             return redirect('opslogin/configuration/defect/create')->with('status', 'Defect Location already exist!');         
        }
        $input = $request->all();

        
        DefectLocation::create($input);
        return redirect('opslogin/configuration/defect');
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
        $bookingObj = FacilityBooking::find($id);
        $bookingObj->view_status = 1;
        $bookingObj->save();

        $slots = explode(",",$bookingObj->gettype->timing);
       // print_r($slots);

        $file_path = env('APP_URL')."/storage/app";
        return view('admin.facilitybooking.edit', compact('bookingObj','file_path','slots'));
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
        $commands = ($request->input('team_command'));

        
        $bookingObj = FacilityBooking::find($id);
        $bookingObj->booking_date = $request->input('booking_date');
        $bookingObj->booking_time = $request->input('booking_time');
        $bookingObj->status = $request->input('status');
        $bookingObj->save();

        
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
            $message = "Facility Booking Updated";
            
            //$title = "Facility Booking";
            //$message = $notification['message'];
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

        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/facility?page=$page#fb")->with('status', 'Facility booking has been updated!');
        }
        else if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$bookingObj->unit_no.'/3';
            return redirect($return_url)->with('status', 'Facility booking has been updated!');
        }
        else
            return redirect('opslogin/facility#fb')->with('status', 'Facility booking has been updated!');
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
        $bookingObj = FacilityBooking::find($id);
        FacilityBooking::findOrFail($id)->delete();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/facility/new?page=$page#fb")->with('status', 'Facility booking deleted successfully!');
        }
        else if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$bookingObj->unit_no.'/3';
            return redirect($return_url)->with('status', 'Facility booking deleted successfully!');
        }
        else
            return redirect('opslogin/facility/new')->with('status', 'Facility booking deleted successfully!');
    }

  


   public function submit()
    {
        
        $locations = DefectLocation::pluck('defect_location', 'id')->all();  
        $types = DefectType::pluck('defect_type', 'id')->all();  
        return view('user.defectsubmit', compact('locations','types'));
    }

    public function save(Request $request)
    {
        $input = $request->all();
        
        $details = array();

        $ticket = new \App\Models\v7\Defect();
        $input['user_id'] = Auth::user()->id;
        $input['account_id'] = Auth::user()->account_id;
        $input['ticket'] = $ticket->ticketgen();
        $defect = Defect::create($input);


        $data['user_id'] = Auth::user()->id;
        $data['def_id'] = $defect->id;

        for($i=1;$i<=10;$i++){

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
                        $data['upload'] = remove_upload_path($request->file($attachement)->store(upload_path('defect')));
                }

                
                $data['status'] = 0;
                $details[] = $data;
            }
            
            
        }

        
        DefectSubmission::insert($details);

        return redirect('opslogin/defect/lists')->with('status', 'Defect(s) has been sent!');
    }

    public function submitlists()
    {
        $file_path = env('APP_URL')."/storage/app";
        $user = Auth::user()->id;
        $defects = DefectSubmission::where('user_id',$user)->orderby('id','desc')->paginate(50); 
        return view('user.defectlists', compact('defects','file_path'));
    }


    public function search(Request $request)
    {
        $account_id = Auth::user()->account_id;

        $ticket  =  $name = $status = $option = $unit = $filter = $category = $building = '';
        $fromdate = $todate = $month = '';
        
        $building = $request->input('building');
        $unit = $request->input('unit');
        //$unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->get();
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
        if($request->input('filter')) 
            $filter = $request->input('filter');
        else
            $filter = 'id';
        $fromdate = $request->input('fromdate');
        if($request->input('todate') !='')
            $todate = $request->input('todate');
        else
            $todate =$request->input('fromdate');
        $status = $request->input('status');

       
        $bookings = FacilityBooking::where('account_id',$account_id)->where(function ($query) use ($category,$fromdate,$todate,$unit,$units,$status,$building) {
            if($category !='')
                $query->where('type_id',$category);
            if($unit !=''|| $building !='')
                $query->whereIn('unit_no', $units);
            if($status !='')
                $query->where('status',$status);
            if($fromdate!='' && $todate !='')
                $query->whereBetween('booking_date',array($fromdate,$todate));
        })->orderBy($filter,'DESC')->paginate(env('PAGINATION_ROWS'));

       
            $types = FacilityType::where('account_id',$account_id)->pluck('facility_type', 'id')->all();
            $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

        
            return view('admin.facilitybooking.index', compact('bookings','ticket','unit','name','status','option','filter','fromdate','todate','month','types','category','buildings','building'));

    }


    public function updatecancelstatus(Request $request){

        $input = $request->all();
        $reason ='';
       
       if(isset($input['reason']))
           $reason = $input['reason'];
        $bookid = $input['bookId'];
        $status = $input['status']; //cancelled
        $bookingObj = FacilityBooking::find($bookid);

        //refund amount start
        if($bookingObj->payment_required==1 && $bookingObj->payment_status==2 && isset($bookingObj->opn_charge_id) && $bookingObj->opn_charge_id !=''){
            $booking_id = $bookid;

            $payment_url = env('OMISEURL')."charges/".$bookingObj->opn_charge_id."/capture";
            $propinfo = Property::where('id',$bookingObj->account_id)->first();
            $username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
            $password = '';
            if(isset($bookingObj->gettype)){
                $cut_of_date = date('Y-m-d', strtotime(Carbon::now()->subDays($bookingObj->gettype->cut_of_days)));
                if($bookingObj->booking_date < $cut_of_date){
                    $deduct_percentage = $bookingObj->gettype->cut_of_amount_percentage;
                    $deduct_amt= ($bookingObj->booking_fee /100) * $deduct_percentage;
                    $opn_capture_amount= $deduct_amt ;
                }else{
                    $opn_capture_amount= 0.00 ;
                }
            }else{
                $opn_capture_amount = 0.00;
            }
            $ch = curl_init();
			if($opn_capture_amount >0){
				$amount = $opn_capture_amount *100;
				$payment_url = env('OMISEURL')."charges/".$bookingObj->opn_charge_id."/capture";
				$fields = [
					"capture_amount" => $amount,
				];
				$fields_string = http_build_query($fields);
				curl_setopt($ch,CURLOPT_URL, $payment_url);
				curl_setopt($ch,CURLOPT_POST, true);
				curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			}
			else{
				$amount= ($bookingObj->booking_fee + $bookingObj->deposit_fee)*100;
				$payment_url = env('OMISEURL')."charges/".$bookingObj->opn_charge_id."/reverse";
				curl_setopt($ch,CURLOPT_URL, $payment_url);
				curl_setopt($ch,CURLOPT_POST, true);
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
			$result = curl_exec($ch);
			$json = json_decode($result,true);
				//print_r($json);
			if(isset($json['id']) && $json['id'] !=''){
				$collected_amount = ($bookingObj->booking_fee + $bookingObj->deposit_fee);
				$refund_amount = $collected_amount - $opn_capture_amount;
				$refund_status = ($refund_amount ==$collected_amount)?1:2;
				$facility_update_qry =FacilityBooking::where('id',$bookid)->update(['capture_amount' => $opn_capture_amount,'refund_amount'=>$refund_amount,'refund_status' => $refund_status]);
				$loginotp = new \App\Models\v7\LoginOTP();
				$otp = $loginotp->facilitybooking_refunded($booking_id);	
					//return response()->json(['response' => 1, 'message' => 'success']);
			}
        }
        //refund amount end

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
            $message = "Facility Booking Cancelled";
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
       //print_r($inboxObj);
       //->update(['event_status' => $status]);

        FacilityBooking::where('id', $bookid)
               ->update(['status' => $status,'reason'=>$reason]);
        
      
        //exit;
        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$bookingObj->unit_no.'/3';
            return redirect($return_url)->with('status', 'Booking Cancelled!');
        }
       else if(isset($input['return_url']))
        {
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/facility?page=$page#fb")->with('status', 'Booking Cancelled !');
            }
            else
                return redirect('opslogin/facility#fb')->with('status', 'Booking Cancelled !');
        }
        
       else{
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/facility/new?page=$page#fb")->with('status', 'Booking Cancelled!');
            }
            else
                return redirect('opslogin/facility/new#fb')->with('status', 'Booking Cancelled!');
       }
           
    }



    public function updateconfirmstatus(Request $request){


        $input = $request->all();
        $reason ='';

       $bookid = $input['Id'];
       $status = $input['status']; //cancelled

       $bookingObj = FacilityBooking::find($bookid);

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

        //$userObj = UserMoreInfo::where($bookingObj->user_id);

        //$loginotp = new \App\Models\v7\LoginOTP();
	    //$otp = $loginotp->facilitybooking_confirmation($bookid);	

       $inbox = InboxMessage::where('ref_id', $bookid)->where('type',6)->first();
       if(isset($inbox) && $inbox->id !='')
        {
            $inboxObj = InboxMessage::find($inbox->id);
            $inboxObj->event_status = $status;
            $inboxObj->save();
        }

       FacilityBooking::where('id', $bookid)
               ->update(['status' => $status,'reason'=>$reason]);

        if(Session::get('current_page') =='unit_summary'){
                $return_url = 'opslogin/configuration/unit_summary/'.$bookingObj->unit_no.'/3';
                return redirect($return_url)->with('status', 'Booking Confirmed!');
        }
        else if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/facility/new?page=$page#fb")->with('status', 'Booking Confirmed!');
        }       
        else
            return redirect('opslogin/facility/new#fb')->with('status', 'Booking Confirmed!');

       
    }

    public function gettimeslots(Request $request)
    {
        
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
        

        if(isset($data))
            return json_encode($data);
        else
             return json_encode($data);

    }

    public function refunddeposit(Request $request)
    {
        //return response()->json(['response' => 1, 'message' => 'success']);

        $input = $request->all();

        $booking_id = $request->refund_booking;
        $charge_amount = $request->charge_amount;
        $refund_amount = $request->ramount;
        $reason = $request->reason;

        //return response()->json(['booking_id' => $booking_id,'charge_amount' => $charge_amount, 'refund_amount' => $refund_amount]);
        $recordObj = FacilityBooking::find($booking_id);

        $refund_amount = $recordObj->deposit_fee-$charge_amount;
        if($charge_amount > $recordObj->deposit_fee){
            if(Session::get('current_page') =='unit_summary'){
                $return_url = 'opslogin/configuration/unit_summary/'.$bookingObj->unit_no.'/3';
                return redirect($return_url)->with('status', 'Claim amount is more than deposit!');
            }
            else if(isset($input['return_url']))
            {
                if(Session::get('page') >0){
                    $page = Session::get('page');
                    return redirect("opslogin/facility?page=$page#fb")->with('status', 'Claim amount is more than deposit!');
                }
                else
                    return redirect('opslogin/facility#fb')->with('status', 'Claim amount is more than deposit!');
            }
            
            else{
                if(Session::get('page') >0){
                    $page = Session::get('page');
                    return redirect("opslogin/facility?page=$page#fb")->with('status', 'Claim amount is more than deposit!');
                }
                else
                    return redirect('opslogin/facility#fb')->with('status', 'Claim amount is more than deposit!');
            }

        }

			if(isset($recordObj->deposit_charge_id) && $recordObj->deposit_charge_id !=''){
                if($charge_amount >0){
                    $payment_url = env('OMISEURL')."charges/".$recordObj->deposit_charge_id."/capture";
                    $propinfo = Property::where('id',$recordObj->account_id)->first();
                    $sub_merchant_key = $propinfo->opn_secret_key;
                    $username = env('OMISEKEY');
                    $password = '';

                    //$propinfo = Property::where('id',$recordObj->account_id)->first();
                    //$username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
                    //$password = '';
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
					//$refund_status = ($refund_amount ==$recordObj->deposit_fee)?1:2;
					$facility_update_qry =FacilityBooking::where('id',$booking_id)->update(['claim_amount' => $charge_amount,'deposit_refund_amount'=>$refund_amount,'deposit_refund_status' => 1,'refund_reason'=>$reason,'deposit_payment_status'=>3]);
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
                  
					if(Session::get('current_page') =='unit_summary'){
                        $return_url = 'opslogin/configuration/unit_summary/'.$bookingObj->unit_no.'/3';
                        return redirect($return_url)->with('status', 'Refund successfull!');
                    }
                    else if(isset($input['return_url']))
                    {
                        if(Session::get('page') >0){
                            $page = Session::get('page');
                            return redirect("opslogin/facility?page=$page#fb")->with('status', 'Refund successfull!');
                        }
                        else
                            return redirect('opslogin/facility#fb')->with('status', 'Refund successfull!');
                    }
                    
                    else{
                        if(Session::get('page') >0){
                            $page = Session::get('page');
                            return redirect("opslogin/facility?page=$page#fb")->with('status', 'Refund successfull!');
                        }
                        else
                            return redirect('opslogin/facility#fb')->with('status', 'Refund successfull!');
                    }
        
				}else{
					if(Session::get('current_page') =='unit_summary'){
                        $return_url = 'opslogin/configuration/unit_summary/'.$bookingObj->unit_no.'/3';
                        return redirect($return_url)->with('status', 'Charge not created!');
                    }
                    else if(isset($input['return_url']))
                    {
                        if(Session::get('page') >0){
                            $page = Session::get('page');
                            return redirect("opslogin/facility?page=$page#fb")->with('status', 'Charge not created!');
                        }
                        else
                            return redirect('opslogin/facility#fb')->with('status', 'Charge not created!');
                    }
                    
                    else{
                        if(Session::get('page') >0){
                            $page = Session::get('page');
                            return redirect("opslogin/facility?page=$page#fb")->with('status', 'Charge not created!');
                        }
                        else
                            return redirect('opslogin/facility#fb')->with('status', 'Charge not created!');
                    }
				}
            }
            
    }

    public function cancellationrefund(Request $request){

        $input = $request->all();
        $reason ='';
       
       if(isset($input['reason']))
           $reason = $input['reason'];
        $booking_id = $input['bookId'];

        FacilityBooking::where('id', $booking_id)
        ->update(['status' =>1,'reason'=>$reason]);

        $recordObj = FacilityBooking::find($booking_id);
        $propinfo = Property::where('id',$recordObj->account_id)->first();
	    $sub_merchant_key = $propinfo->opn_secret_key;
		$username = env('OMISEKEY');
		$password = '';
       
        if($recordObj->payment_required==1){
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
                /*$payment_url = env('OMISEURL')."charges/".$recordObj->deposit_charge_id."/refunds";
                $return_amount = ($recordObj->deposit_fee)*100;
                $fields = [
                        "amount" => $return_amount,
                    ];
                    
                $fields_string = http_build_query($fields);*/
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

        
		if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$bookingObj->unit_no.'/3';
            return redirect($return_url)->with('status', 'Booking cancelled!');
        }
        else if(isset($input['return_url']))
        {
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/facility?page=$page#fb")->with('status', 'Booking cancelled!');
            }
            else
                return redirect('opslogin/facility#fb')->with('status', 'Booking cancelled!');
        }         
        else{
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/facility?page=$page#fb")->with('status', 'Booking cancelled!');
            }
            else
                return redirect('opslogin/facility#fb')->with('status', 'Booking cancelled!');
        } 
    }


}
