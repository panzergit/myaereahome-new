<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\Defect;
use App\Models\v7\DefectLocation;
use App\Models\v7\DefectType;
use App\Models\v7\DefectSubmission;
use App\Models\v7\JoininspectionAppointment;
use App\Models\v7\UserLog;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserNotificationSetting;
use App\Models\v7\Unit;
use App\Models\v7\Property;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\FinalInspectionAppointment;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use DB;
use Auth;
use Carbon\Carbon;
use App\Models\v7\InboxMessage;
use Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\v7\Building;

class DefectController extends Controller
{
    protected $defectStatus = ['' => '--ALL--',0=>'OPEN','3'=>'ON SCHEDULE',2=>'IN PROGRESS',1=>"CLOSED",5=> 'COMPLETED - PENDING RESIDENT UPDATE',6 => 'COMPLETED - FINAL INSPECTION SCHEDULED'];
    protected $inspectionStatus = ['0' => 'New', '1' => 'Cancelled', '2'=>'On Schedule','4'=>'In Progress','3'=>'Done'];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
    {
        session()->forget('current_page');

        $ticket  =  $name = $status = $option = $unit = $fromdate = $todate = $indays = '';
        $account_id = Auth::user()->account_id;
        $defects = Defect::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
        $visitor_app_url = env('VISITOR_APP_URL');
        $file_path = env('APP_URL')."/storage/app";
        
        $defectBlocks = array_filter(array_unique(Defect::where('account_id',$account_id)->pluck('block_no')->toArray()));
        $blocks = !empty($defectBlocks) ? Building::select('id','building')->where('account_id',$account_id)->whereIn('id',$defectBlocks)->orderBy('building')->get()
            ->map(function($r) use($account_id){
                $r['defects'] = Defect::where(['view_status' => 0,'account_id' => $account_id,'status' => 0, 'block_no' => $r->id])->count();
                return $r;
            })->toArray() : [];
        
        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
            session()->put('page', $page[1]);
        }else{
            session()->forget('page');
        }
        
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
                'defects' => Defect::where('account_id',$account_id)->whereYear('created_at',$start->copy()->format('Y'))->whereMonth('created_at',$start->copy()->format('m'))->count()
            ];
            $start->addMonth();
        }
        
        $year = $request->has('year') ? $request->year : date('Y');
        
        $units = array();
        if($request->has('units') && !empty($request->units)) {
            $unit = str_replace("#",'',$request->has('units'));
            $unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
            })->get();   
            if(isset($unitObj)){
                //echo "hi";
                foreach($unitObj as $unitid){
                    //echo Crypt::decryptString($unitid->unit) . " ".$request->units." <br/>";
                    if(Crypt::decryptString($unitid->unit) ===$request->units)
                        $units[] = $unitid->id;
                }
            }
        }
        
        $locations = null;
        if($request->has('locations') && !empty($request->locations)){
            $locations = $request->locations;
        } 
        
        $defactsByLocations = DefectLocation::where('account_id',$account_id)->where('status',1)
            ->when($locations,fn($q) => $q->where('id', $locations))
            ->orderBy('defect_location')->get()->map(function($q) use($account_id,$year,$units) {
            return [
                'name' => ucwords(strtolower($q->defect_location)),
                'defects' => DefectSubmission::where('defect_location',$q->id)
                    ->whereHas('defect', function (Builder $query) use($account_id,$year,$units) {
                        $query->where('account_id',$account_id)
                        ->when($units,fn($q) => $q->where('unit_no', $units))
                        ->whereYear('created_at', $year);
                    })->count(),
            ];
        });
        
        $types = null;
        if($request->has('types') && !empty($request->types)) $types = $request->types;
            
        $defactsByTypes = DefectType::where('account_id',$account_id)->where('status',1)
            ->when($types,fn($q) => $q->where('id', $types))
            ->orderBy('defect_type')->get()->map(function($q) use($account_id,$year) {
                return [
                    'name' => ucwords(strtolower($q->defect_type)),
                    'defects' => DefectSubmission::where('defect_type',$q->id)
                        ->whereHas('defect', function (Builder $query) use($account_id,$year) {
                            $query->where('account_id',$account_id)->whereYear('created_at', $year);
                        })->count(),
                ];
            });
        
            
        $defactsByUnits = Unit::where('account_id',$account_id)->where('status',1)
            ->when($units,fn($q) => $q->where('id', $units))
            ->get()->map(function($q) use($account_id,$year) {
                return [
                    'id' => $q->id,
                    'name' => \Crypt::decryptString($q->unit),
                    'defects' => Defect::where('unit_no',$q->id)->where('account_id',$account_id)->whereYear('created_at', $year)->count(),
                ];
            });
           // print_r($units);
        $currentYearDefects = Defect::where('account_id',$account_id)->whereYear('created_at',$year)
        ->when($units,fn($q) => $q->whereIn('unit_no', $units))
        ->get();
        $totalDefects = $currentYearDefects->count();
        $totalNewDefects = $currentYearDefects->where('status',0)->count();
        $totalInprogressDefects = $currentYearDefects->where('status',4)->count();
        $totalScheduledDefects = $currentYearDefects->where('status',3)->count();
        $totalCompletedDefects = $currentYearDefects->where('status',1)->count();
        $units = Unit::where('account_id',$account_id)->get()->map(function($v){
            $v->unit = \Crypt::decryptString($v->unit);
            return $v;
        })->sortBy('unit')
        ->values();
        
        $locations = DefectLocation::where('account_id',$account_id)->orderBy('defect_location')->get();
        if($request->has('locations') && !empty($request->locations) ){
            $types = DefectType::where('location_id',$request->locations)->orderBy('defect_type')->get();
        }else{
            $types = array();
        }
        
        $defectStatus = $this->defectStatus;
        
        return view('admin.defect.index', compact('defects', 'blocks', 'dates','defectStatus','defactsByLocations', 'totalDefects', 'totalNewDefects', 'totalInprogressDefects', 'totalScheduledDefects', 'totalCompletedDefects', 'ticket','unit','name','units','locations','types', 'defactsByTypes', 'defactsByUnits', 'status','option','fromdate','todate','visitor_app_url','file_path','indays'));
    }

    public function new()
    {
        session()->forget('current_page');

        $ticket  =  $name = $status = $option = $unit ='';
        $date = Carbon::now()->subDays(7);
        $account_id = Auth::user()->account_id;
        $defects = Defect::where('account_id',$account_id)->where('status',0)->where('view_status',0)->where('created_at', '>=', $date)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        $visitor_app_url = env('VISITOR_APP_URL');
        $file_path = env('APP_URL')."/storage/app";

        return view('admin.defect.index', compact('defects','ticket','unit','name','status','option','visitor_app_url','file_path'));
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
        $user = Auth::user();
        $defectObj = Defect::find($id);
        $inspectionObj = JoininspectionAppointment::where('def_id', $id)->orderby('id','desc')->first();
        
        $obj = new JoininspectionAppointment();
        $times = $obj->timeslots($user->account_id);	
		if($defectObj->property->final_inspection_required==1)
            $ticket_status = array('a' => '--ALL--',0=>'OPEN','3'=>'ON SCHEDULE',4=>'IN PROGRESS','5'=>'COMPLETED-PENDING RESIDENT UPDATE',1=>"CLOSED");
        else
            $ticket_status = array('a' => '--ALL--',0=>'OPEN','3'=>'ON SCHEDULE',4=>'IN PROGRESS',1=>"CLOSED");

        $file_path = env('APP_URL')."/storage/app";
        
        $defectStatus = $this->defectStatus;
        $inspectionStatus = $this->inspectionStatus;
        
        $signatureUserName = User::find($defectObj->user_id)->name ?? null;
        $signatureUserName = $signatureUserName!=null ? Crypt::decryptString($signatureUserName) : null;
        $signatureUserTime = \Carbon\Carbon::parse($defectObj->created_at)->format('d/m/y h:i:s A');
        
        $inspectedOwnerName = User::find($defectObj->inspection_owner_user)->name ?? null;
        $inspectedOwnerName = $inspectedOwnerName!=null ? Crypt::decryptString($inspectedOwnerName) : null;
        $inspectedOwnerTime = \Carbon\Carbon::parse($defectObj->inspection_owner_timestamp)->format('d/m/y h:i:s A');
        
        $inspectedTeamName = User::find($defectObj->inspection_team_user)->name ?? null;
        $inspectedTeamName = $inspectedTeamName!=null ? Crypt::decryptString($inspectedTeamName) : null;
        $inspectedTeamTime = \Carbon\Carbon::parse($defectObj->inspection_team_timestamp)->format('d/m/y h:i:s A');
        
        $handOverOwnerName = User::find($defectObj->handover_owner_user)->name ?? null;
        $handOverOwnerName = $handOverOwnerName!=null ? Crypt::decryptString($handOverOwnerName) : null;
        $handOverOwnerTime = \Carbon\Carbon::parse($defectObj->handover_owner_timestamp)->format('d/m/y h:i:s A');
        
        $handOverTeamName = User::find($defectObj->handover_team_user)->name ?? null;
        $handOverTeamName = $handOverTeamName!=null ? Crypt::decryptString($handOverTeamName) : null;
        $handOverTeamTime = \Carbon\Carbon::parse($defectObj->handover_team_timestamp)->format('d/m/y h:i:s A');
        
        return view('admin.defect.show', compact('defectObj', 'inspectionStatus', 'defectStatus','file_path','inspectionObj','times','ticket_status' , 'inspectedOwnerName', 'inspectedOwnerTime', 'inspectedTeamName', 
            'inspectedTeamTime', 'handOverOwnerName', 'handOverOwnerTime', 'handOverTeamName', 'handOverTeamTime', 'signatureUserName', 'signatureUserTime'));
    }
    
    public function viewSubmission($id)
    {
        $user = Auth::user();
        $defectObj = Defect::find($id);
        $file_path = env('APP_URL')."/storage/app";
        $defectStatus = $this->defectStatus;
        $isUserSignatureEmpty = is_null($user->signature);
        return view('admin.defect.defect_submission_update', compact('defectObj','defectStatus','file_path','isUserSignatureEmpty'));
    }
    
    public function submissionUpdate(Request $request, $id)
    {
        $defectObj = Defect::find($id);
        $allowed_status = array(0,3);
        if(!in_array($defectObj->status,$allowed_status)){
            return redirect()->back()->with('status', 'Sorry, this ticket cannot be updated as it is already in progress.');
        }
        $defectObj->status = $request->status;
        $defectObj->save();
        
        $d_status = $request->defect_status;
        $inspection = 0;
        if($defectObj->submissions){
            foreach($defectObj->submissions as $k => $defect){
                $defectSubmissionObj = DefectSubmission::find($defect->id);
                $defect_status = $d_status[$defect->id];
                $defectSubmissionObj->status = $defect_status;
                if($defect_status ==2) $inspection  =1;
                $defectSubmissionObj->save();
            }
        }
        

        $inspectionOwnerSignature = User::find(Auth::id())->signature;
        
        $updateData = [
            'inspection_status' => 1,
            'status' => 3,
            'inspection_team_signature' => $inspectionOwnerSignature,
            'inspection_team_user' => Auth::id(), 
            'inspection_team_timestamp' => now()
        ];
        
        if($inspection!=1) $updateData = array_merge($updateData, ['handover_status' => 0, 'status' => 0]);
        
        Defect::where('id', $defectObj->id)->update($updateData);
        
        //print_r($updateData);
        //exit;
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/defects?view=summary&page=$page")->with('status', 'Defects has been updated!');
        }else {
            return redirect()->back()->with('status', 'Defects has been updated!');
        }
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
        $user = Auth::user();
        
        $defectObj = Defect::find($id);
        $inspectionObj = JoininspectionAppointment::where('def_id', $id)->orderby('id','desc')->first();

        $defectObj->view_status = 1;
        $defectObj->save();

        $obj = new JoininspectionAppointment();
        $times = $obj->timeslots($user->account_id);	
		if($defectObj->property->final_inspection_required==1)
            $ticket_status = array('a' => '--ALL--',0=>'OPEN','3'=>'ON SCHEDULE',4=>'IN PROGRESS','5'=>'COMPLETED-PENDING RESIDENT UPDATE',1=>"CLOSED");
        else
            $ticket_status = array('a' => '--ALL--',0=>'OPEN','3'=>'ON SCHEDULE',4=>'IN PROGRESS',1=>"CLOSED");

        $file_path = env('APP_URL')."/storage/app";
        
        $defectStatus = $this->defectStatus;
        $isUserSignatureEmpty = is_null($user->signature);

        //default loading for reminder fields
        $reminderData = JoininspectionAppointment::where('account_id', $defectObj->account_id)->orderby('id','desc')->first();

        $reminder_in_days = '';
        $reminder_emails = '';
        $email_message = '';
        if(!empty($inspectionObj->reminder_in_days))
            $reminder_in_days = $inspectionObj->reminder_in_days;
        else if(!empty($reminderData->reminder_in_days))
            $reminder_in_days = $reminderData->reminder_in_days;
        
        if(!empty($inspectionObj->reminder_emails))
            $reminder_emails = $inspectionObj->reminder_emails;
        else if(!empty($reminderData->reminder_emails))
            $reminder_emails = $reminderData->reminder_emails;

        if(!empty($inspectionObj->email_message))
            $email_message = $inspectionObj->email_message;
        else if(!empty($reminderData->email_message))
            $email_message = $reminderData->email_message;

        return view('admin.defect.edit', compact('defectObj','defectStatus','file_path','inspectionObj','times','ticket_status','isUserSignatureEmpty','reminderData','reminder_in_days','reminder_emails','email_message'));
    }
    
    public function finalInspection($id)
    {
        //
        $user = Auth::user();
        
        $defectObj = Defect::find($id);
        if($defectObj->status!=6) return redirect()->back()->with('status','Permission denied.');
        
        $inspectionObj = FinalInspectionAppointment::where('def_id', $id)->orderby('id','desc')->first();
        
        if(empty($inspectionObj)) return redirect()->back()->with('status', 'Final inspection is not yet scheduled');

        $defectObj->view_status = 1;
        $defectObj->save();

        $obj = new FinalInspectionAppointment();
        $times = $obj->timeslots($user->account_id);	
		if($defectObj->property->final_inspection_required==1)
            $ticket_status = array('a' => '--ALL--',0=>'OPEN','3'=>'ON SCHEDULE',4=>'IN PROGRESS','5'=>'COMPLETED-PENDING RESIDENT UPDATE',1=>"CLOSED");
        else
            $ticket_status = array('a' => '--ALL--',0=>'OPEN','3'=>'ON SCHEDULE',4=>'IN PROGRESS',1=>"CLOSED");

        $file_path = env('APP_URL')."/storage/app";
        
        $defectStatus = $this->defectStatus;
        
        return view('admin.defect.final_inspection', compact('defectObj','defectStatus','file_path','inspectionObj','times','ticket_status'));
    }
    
    public function finalInspectionUpdate(Request $request, $id)
    {
        $defectObj = Defect::find($id);
        $status = $request->input('inspection_status');
        //Final Inspection Start
        $finalInspectionObj = FinalInspectionAppointment::where('def_id', $id)->orderby('id','desc')->first();
        
        if(!empty($finalInspectionObj)){

            $inspectionObj = FinalInspectionAppointment::find($finalInspectionObj->id);
            $inspectionObj->status = $status;
            $inspectionObj->appt_date = $request->input('appt_date');
            $inspectionObj->appt_time = $request->input('appt_time');

            if($request->input('inspection_status')==4)
            { 
                $inspectionObj->progress_date = $request->input('progress_date');
                $inspectionObj->reminder_in_days = $request->input('reminder_in_days');
                $inspectionObj->reminder_emails = $request->input('reminder_emails');
                $inspectionObj->email_message = $request->input('email_message');
            }
            
            $inspectionObj->save();
           
            $title = "Your defect final inspection status changed ";
        
            InboxMessage::where('ref_id', $finalInspectionObj->id)->where('type',5)
                    ->update(['title'=>$title,'booking_date'=> $request->input('appt_date'), 'booking_time'=> $request->input('appt_time'),'event_status' => $status]);
            InboxMessage::where('ref_id', $id)->where('type',3)->update(['event_status' => $request->input('status')]);
        }
        
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
            $notofication_data['property'] =$defectObj->account_id;   ; 
            $purObj = UserPurchaserUnit::where('property_id',$defectObj->account_id)->where('unit_id',$defectObj->unit_no)->where('user_id',$defectObj->user_id)->first(); 
            if(isset($purObj))
                $notofication_data['switch_id'] =$purObj->id;
            
            $NotificationObj = new \App\Models\v7\FirebaseNotification();
            $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
            $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
            //End Insert into notification module
        }

        return redirect()->back()->with('status', 'Defects has been updated!');
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
        $defectObj = Defect::find($id);
        $defectObj->status = $request->input('status');
        $defectObj->save();
        
        //Inspection update Start
        $jointObj = JoininspectionAppointment::where('def_id', $id)->orderby('id','desc')->first();
        
        if($jointObj)
        {
            $status = $request->input('inspection_status');
            if($status!=4){ //defect New , joint inspection new
    
                JoininspectionAppointment::where('id', $jointObj->id)
                    ->update([
                        'status' => $request->input('inspection_status'),
                        'appt_date'=>$request->input('appt_date'),
                        'appt_time'=>$request->input('appt_time')
                    ]);
                    
            }else { ///defect on in progress, , joint inspection closed
                
                $inspectionObj = JoininspectionAppointment::find($jointObj->id);
                $inspectionObj->status = $status;
            
                $inspectionObj->appt_date = $request->input('appt_date');
                $inspectionObj->appt_time = $request->input('appt_time');

                $inspectionObj->progress_date = $request->input('progress_date');
                $inspectionObj->reminder_in_days = $request->input('reminder_in_days');
            
                if($request->input('progress_date') !=''){
                    $date = Carbon::createFromFormat('Y-m-d', $request->input('progress_date'));
                    $booking_allowed = $request->input('reminder_in_days') !='' ? $date->addDays($request->input('reminder_in_days')) : $date->addDays(0);
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

            InboxMessage::where('ref_id', $id)->where('type',3)->update(['event_status' => $request->input('status')]);
        }

        //Final Inspection Start

        /*
        if($defectObj->status ==6)
        $finalInspectionObj = FinalInspectionAppointment::where('def_id', $id)->orderby('id','desc')->first();
        
        if(!empty($finalInspectionObj)){

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
                $inspectionObj->save();    
            
            }

           
            //$request->input('inspection_status');
            $title = "Your defect final inspection status changed ";
            
            InboxMessage::where('ref_id', $jointObj->id)->where('type',5)
                    ->update(['title'=>$title,'booking_date'=>$jointObj->appt_date,'booking_time'=>$jointObj->appt_time,'event_status' => $status]);
            //inspection update End

            InboxMessage::where('ref_id', $id)->where('type',3)
            ->update(['event_status' => $request->input('status')]);
        }*/
        
       
        //Start Insert into notification module
        $notification = [];
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
            $ios_devices_to_send = [];
            $android_devices_to_send = [];
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
            $notofication_data['property'] =$defectObj->account_id;   ; 
            $purObj = UserPurchaserUnit::where('property_id',$defectObj->account_id)->where('unit_id',$defectObj->unit_no)->where('user_id',$defectObj->user_id)->first(); 
            if(isset($purObj)) $notofication_data['switch_id'] =$purObj->id;
            
            $NotificationObj = new \App\Models\v7\FirebaseNotification();
            $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
            $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
            //End Insert into notification module
        }

        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/defects?view=summary&page=$page")->with('status', 'Defects has been updated!');
        }else {
            return redirect()->back()->with('status', 'Defects has been updated!');
        }
    }

    public function handover($id)
    {
        //
        $user = Auth::user();

        $defectObj = Defect::find($id);
        $inspectionObj = JoininspectionAppointment::where('def_id', $id)->orderby('id','desc')->first();

        $defectObj->view_status = 1;
        $defectObj->save();

        $obj = new JoininspectionAppointment();
        $times = $obj->timeslots($user->account_id);	
        $defectStatus = $this->defectStatus;
        $file_path = env('APP_URL')."/storage/app";
        $defectStatus = $this->defectStatus;
        $isUserSignatureEmpty = is_null($user->signature);
        
        return view('admin.defect.handover', compact('defectObj','file_path','inspectionObj','times','isUserSignatureEmpty','defectStatus'));
    }

    public function handoverupdate(Request $request)
    {
        //
        $id = $request->input('id');
        $defectObj = Defect::find($id);
        
        if($defectObj->status ==1){
            //$page = Session::get('page');
            return redirect("opslogin/defects")->with('status', 'Sorry, this ticket has already been closed!');
        }
        $defect_status = ($request->input('defect_status'));
        $handover_message = $request->input('handover_message');
       
        $handover = 1;
        
        $inspectionOwnerSignature = User::find(Auth::id())->signature;
        $defectObj->handover_team_signature = $inspectionOwnerSignature;
        $defectObj->handover_team_user = Auth::id();
	    $defectObj->handover_team_timestamp = now();
        $ticket_status = 5;// completed pending Resident update     
        if($defectObj->submissions){
            foreach($defectObj->submissions as $k => $submission){
                if($submission->status ==2)
                {
                   
                    //echo $defect_status[$submission->id];
                    $rectified_image = "rectieid_image_".$submission->id;
                    $defectSubmissionObj = DefectSubmission::find($submission->id);
                    $defectSubmissionObj->defect_status = $defect_status[$submission->id];
                    if($defect_status[$submission->id] ==2)
                        $defectSubmissionObj->handover_message = $handover_message[$submission->id];  
                    if($request->file($rectified_image) != null) {
                        $defectSubmissionObj->rectified_image = $request->file($rectified_image)->store('defect');
                    }    
                    if($defect_status[$submission->id] ==0)  { 
                        $handover = 0;
                        $ticket_status = 2; // in progress status 
                    }          
                    $defectSubmissionObj->save();
                }
            }
        }
        $defectObj->handover_status = $handover;
        $defectObj->status = $ticket_status;
        $defectObj->save();
        //exit;
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

        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/defects?page=$page")->with('status', 'Defects handover status has been updated!');
        }
        else if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$defectObj->unit_no.'/2';
            return redirect($return_url)->with('status', 'Defects handover status has been updated!');
        }
        else
            return redirect('opslogin/defects')->with('status', 'Defects handover status has been updated!');
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
        $defectObj = Defect::find($id);

        JoininspectionAppointment::where('def_id', $id)->delete();

        DefectSubmission::where('def_id', $id)->delete();

        Defect::findOrFail($id)->delete();

        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/defects?page=$page")->with('status', 'Record deleted successfully!');
        }
        else if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$defectObj->unit_no.'/2';
            return redirect($return_url)->with('status', 'Defects handover status has been updated!');
        }
        else
            return redirect('opslogin/defects')->with('status', 'Record deleted successfully!!');
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
                        $data['upload'] = $request->file($attachement)->store('defect');
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
        $ticket  =  $name = $status = $option = $unit = $fromdate = $todate = $location = $type = '';
        $dates = [];

        //print_r($request);
        $account_id = $request->user()->account_id;
        $option = $request->input('option'); 
        $name = $request->input('name');
        $ticket = $request->input('ticket');
        $fromdate = $request->input('fromdate');
        $location = $request->input('locations');
        $type = $request->input('types');
        $todate = $request->filled('todate') ? $request->todate : '';
        $block = $request->has('block') ? $request->block : 'all'; 
        
        $defectBlocks = array_filter(array_unique(Defect::where('account_id',$account_id)->pluck('block_no')->toArray()));
        $blocks = !empty($defectBlocks) ? Building::select('id','building')->where('account_id',$account_id)->whereIn('id',$defectBlocks)->orderBy('building')->get()
            ->map(function($r) use($account_id){
                $r['defects'] = Defect::where(['view_status' => 0,'account_id' => $account_id,'status' => 0, 'block_no' => $r->id])->count();
                return $r;
            })->toArray() : [];
        
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
        
        $indays = $request->filled('indays') ? $request->indays : ''; 
        
        //print_r($units);
        $userids =array();
        if($name !=''){
            /*$user_more_info = UserMoreInfo::where('last_name', 'LIKE', '%'.$last_name .'%')->where('first_name', 'LIKE', '%'.$last_name .'%')->orderby('id','desc')->get();
                
               foreach($user_more_info as $k =>$v){
                $userids[] = $v->user_id;
               }
            */
            $user_more_info = UserMoreInfo::select('id','user_id','first_name','last_name')->where('account_id',$account_id)->whereNotIn('status',[2])->orderby('id','desc')->get();
            
            foreach($user_more_info as $k =>$v){
                $firstname = strtolower(Crypt::decryptString($v->first_name));
                $lastname = strtolower(Crypt::decryptString($v->last_name));
                if(str_contains($firstname,strtolower($name)) || str_contains($lastname,strtolower($name))){
                    $userids[] = $v->user_id;
                }
            }
        }

        $defectsids = [];
        if(!empty($location)){
            $defectsids = DefectSubmission::select('def_id')->where(function ($subquery) use ($location,$type){
                if($location !='')
                    $subquery->where('defect_location', $location);
                if($type !='')
                    $subquery->where('defect_type', $type);
            })->orderby('id','desc')->get()->toArray();
        }
        
        $sortedDefects =  Defect::where('account_id',$account_id)->where(function ($query) use ($name,$userids,$ticket,$fromdate,$todate,$unit,$units,$defectsids,$location) {
            if($ticket !='')
                $query->where('ticket', 'LIKE', '%'.$ticket .'%');
            if($name !='')
                $query->whereIn('user_id', $userids);
            if($unit !='')
                $query->wherein('unit_no', $units);
            if(!empty($defectsids) && !empty($location))
                $query->wherein('id', $defectsids);
        })
        ->when($block!='all', fn($q) => $q->where('block_no',$block))
        ->when((trim($request->status)!=''),fn($q) => $q->where('status', $request->status))
        ->when((trim($fromdate)!='' && trim($todate)==''),fn($q) => $q->whereDate('created_at','>=',$fromdate))
        ->when((trim($fromdate)=='' && trim($todate)!=''),fn($q) => $q->whereDate('created_at','<=',$todate))
        ->when((trim($fromdate)!='' && trim($todate)!=''),fn($q) => $q->whereBetween('created_at',array($fromdate,$todate)))
        ->when((trim($indays)!=''),fn($q) => $q->whereDate('inspection_owner_timestamp','<=',Carbon::now()->subDays($indays)->format('Y-m-d')))
        ->get()
        ->map(function($r){
            if(trim($r->unit_no)=='') $r->unit_no = 0;
            return $r;
        })
        ->sortBy(fn($i) => (!isset($i->getunit->unit) ? '0' : Crypt::decryptString($i->getunit->unit)))
        ->values();
        
        // id desc after unit sort asc
        $unitNoGrouped = $sortedDefects->groupBy('unit_no');
        $finalResults = collect([]);
        foreach ($unitNoGrouped as $uk => $rows){
            $idSorted = $rows->sortByDesc(fn($i) => $i->id)->values();
            $unitNoGrouped[$uk] = $idSorted;
            foreach ($idSorted as $row) $finalResults->push($row);
        }
        
        // manual pagination buildinginfo
        $page = request('page', 1);
        $perPage = env('PAGINATION_ROWS');
        
        $defects = new LengthAwarePaginator(
            $finalResults->forPage($page, $perPage),
            $finalResults->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $locations = DefectLocation::where('account_id',$account_id)->orderBy('defect_location')->get();
        $types = DefectType::where('account_id',$account_id)->orderBy('defect_type')->get();
        $defectStatus = $this->defectStatus;
        $defactsByLocations = $totalDefects = $totalNewDefects = $totalInprogressDefects = $totalScheduledDefects = $totalCompletedDefects ='';
            $visitor_app_url = env('VISITOR_APP_URL');
            return view('admin.defect.index', compact('defects', 'blocks', 'dates','defectStatus','defactsByLocations', 'totalDefects', 'totalNewDefects', 'totalInprogressDefects', 'totalScheduledDefects', 'totalCompletedDefects','ticket','unit','name','units','status','option','fromdate','todate','visitor_app_url','locations','types','defectStatus','indays')); 
    }

    public function cancelFinalInspection(Request $request)
    {
        $input = $request->all();
        $reason = $input['reason'] ?? '';
        $bookid = $input['bookId'];
        $status = $input['status']; //cancelled
      
        $inbox = InboxMessage::where('ref_id', $bookid)->where('type',5)->first();
      
          if(isset($inbox) && $inbox->id !=''){
           $inboxObj = InboxMessage::find($inbox->id);
           $inboxObj->event_status = $status;
           $inboxObj->save();
           }

       
        FinalInspectionAppointment::where('id', $bookid)->update(['status' => $status,'reason'=>$reason]);
               
        return redirect()->back()->with('status', "Appointment Cancelled!");
    }

    public function updatecancelstatus(Request $request){

        $input = $request->all();
         $reason ='';

       if(isset($input['reason']))
           $reason = $input['reason'];

       $bookid = $input['bookId'];
       $status = $input['status']; //cancelled

    

      $inbox = InboxMessage::where('ref_id', $bookid)->where('type',5)->first();
      
      if(isset($inbox) && $inbox->id !=''){
       $inboxObj = InboxMessage::find($inbox->id);
       $inboxObj->event_status = $status;
       $inboxObj->save();
       }

       
        JoininspectionAppointment::where('id', $bookid)
               ->update(['status' => $status,'reason'=>$reason]);

        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/defect?page=$page")->with('status', 'Appointment Cancelled!');
        }
        
        else
           return redirect('opslogin/defect')->with('status', "Appointment Cancelled!");
    }

    public function getlocationtypes(Request $request)
    {
        
        $users = array();
        $location = $request->location;
        $types = DefectType::select('defect_type', 'id')->where('location_id',$location)->get();

        if(isset($types) ){
            $type_data = array();
            foreach($types as $type){
                $type_data[$type->id] = $type->defect_type;
            }
            return json_encode($type_data);
        }
            
        else
             return json_encode(null);
    }
}
