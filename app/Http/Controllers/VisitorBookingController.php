<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\v7\VisitorType;
use App\Models\v7\VisitorBooking;
use App\Models\v7\VisitorList;
use App\Models\v7\Property;
use App\Models\v7\Unit;
use App\Models\v7\User;
use App\Models\v7\Building;

use App\Models\v7\UserMoreInfo;
use Carbon\Carbon;

use App\Models\v7\FirebaseNotification;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserLog;
use App\Models\v7\UserNotificationSetting;

use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Session;

class VisitorBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

   

     public function index(Request $request)
    {
        session()->forget('current_page');

        $bookingid  =  $unit = $date = $purpose = $option = $booking_type = $building = '';

        $account_id = Auth::user()->account_id;
        $bookings = VisitorBooking::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));  
        $types = VisitorType::where('account_id', $account_id)->pluck('visiting_purpose', 'id')->all();

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
            $bookings = VisitorBooking::where('account_id',$account_id)->whereYear('created_at',$year)
                ->when((trim($request->status)!=''), fn($q) => $q->where('status',$request->status))
                ->when((trim($request->category)!=''), fn($q) => $q->where('booking_type',$request->category))
                ->when((trim($request->unit)!=''), fn($q) => $q->where('unit_no',$request->unit))
                ->get();
            $totalBookings = $bookings->count();
            $totalBookingsPre = $bookings->where('booking_type','1')->values()->count();
            $totalBookingsWalkIn = $bookings->where('booking_type','2')->values()->count();
            $totalBookingsPending = $bookings->where('status','0')->values()->count();
            $totalBookingsCancelled = $bookings->where('status','1')->values()->count();
            
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
                    'defects' => VisitorBooking::where('account_id',$account_id)->whereYear('created_at',$start->copy()->format('Y'))->whereMonth('created_at',$start->copy()->format('m'))->count()
                ];
                $start->addMonth();
            }
            
            $defactsByPurpose = VisitorType::where('account_id',$account_id)->where('status',1)
                ->when((trim($request->category)!=''),fn($q) => $q->where('id', $request->category))
                ->orderBy('visiting_purpose')->get()->map(function($q) use($account_id,$year,$units) {
                return [
                    'name' => ucwords(strtolower($q->visiting_purpose)),
                    'defects' => VisitorBooking::where('visiting_purpose',$q->id)
                        ->where('account_id',$account_id)
                        ->when($units,fn($q) => $q->where('unit_no', $units))
                        ->whereYear('created_at', $year)
                        ->count(),
                ];
            });
            
            $compactData = compact('totalBookings', 'totalBookingsPre', 'totalBookingsWalkIn', 'totalBookingsPending', 'totalBookingsCancelled', 'dates', 'defactsByPurpose', 'bookings','bookingid','unit','option','types','purpose','date','booking_type','buildings','building');
        }else{
            $compactData = compact('bookings','bookingid','unit','option','types','purpose','date','booking_type','buildings','building');
        }

        return view('admin.visitor_booking.index', $compactData);
    }

    public function new()
    {
        session()->forget('current_page');

        $bookingid  =  $unit = $date = $purpose = $option = $booking_type = '';
        $account_id = Auth::user()->account_id;
        $bookings = VisitorBooking::where('account_id',$account_id)->where('view_status',0)->where('status',0)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));  
        $types = VisitorType::where('account_id', $account_id)->pluck('visiting_purpose', 'id')->all();

        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }

        return view('admin.visitor_booking.new', compact('bookings','bookingid','unit','option','types','purpose','date','booking_type'));
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$users = User::pluck('name','id')->all();
        $account_id = Auth::user()->account_id;
        $property = Property::where('id',$account_id)->first();
        $units = Unit::select('unit', 'id')->where('account_id',$account_id)->get();
        if(isset($units)){
            $unites = array();
            foreach($units as $unit){
                $unites[$unit->id] = Crypt::decryptString($unit->unit);
            }
        }
        $types = VisitorType::where('account_id', $account_id)->pluck('visiting_purpose', 'id')->all();
        $visiting_types = VisitorType::where('account_id', $account_id)->get();

        return view('admin.visitor_booking.create', compact('property','unites','types','visiting_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $input = $request->all();
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $ticket = new \App\Models\v7\VisitorBooking();
		$input['user_id'] = Auth::user()->id;
		$input['account_id'] = Auth::user()->account_id;

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

        //print_r($input);

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
    
    if(Session::get('page') >0){
        $page = Session::get('page');
        return redirect("opslogin/visitor-summary?page=$page#vm")->with('status', 'Walk-In info has been added');}
    else
        return redirect('opslogin/visitor-summary#vm')->with('status', 'Walk-In info has been added'); 
       
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
    public function manualscan(Request $request,$id)
    {
        //
        $today = Carbon::now()->format('Y-m-d');
        $visiting_time = Carbon::now()->format('Y-m-d H:i:s');

        $input = $request->all();
        $bookingObj = VisitorBooking::find($id);
        if($bookingObj->scan_count >= $bookingObj->qr_scan_limit){
            return redirect('opslogin/visitor-summary')->with('status', 'QR scan reached maximum limit.'); 
        }
        $bookingObj->scan_count = $bookingObj->scan_count +1;
        $bookingObj->view_status = 1;
        $bookingObj->save();
        $account_id = Auth::user()->account_id;

        if($bookingObj->id <=0){
            return redirect('opslogin/visitor-summary')->with('status', 'Booking has been cancelled or closed.'); 
        }
        if(isset($bookingObj->visiting_date) && $bookingObj->visiting_start_time <= $visiting_time && $bookingObj->visiting_end_time >= $visiting_time)
		{
            return redirect("opslogin/visitor-summary/$id/edit")->with('status', 'QR code scan successful.'); 
        }
        else{
            return redirect('opslogin/visitor-summary')->with('status', 'QR code not active.'); 
        }

    }
    public function facialscan($id,$data)
    {
        //
        //$input = $request->all();
        $qr_data = explode("&",$data);
        $visiting_time = Carbon::now()->format('Y-m-d H:i:s');
		foreach($qr_data as $data){
			$values = explode("=",$data);
			$input[$values[0]] = $values[1];
        }
      
        $bookingObj = VisitorBooking::find($id);
        $VisitorObj = VisitorList::where('book_id',$bookingObj->id)->where('id',$input['vid'])->first();
        if($VisitorObj->visit_count >= $bookingObj->qr_scan_limit){
            return redirect('opslogin/visitor-summary')->with('status', 'QR scan reached maximum limit.'); 
        }
        $bookingObj->view_status = 1;
        $bookingObj->save();
        $visit_count = $VisitorObj->visit_count + 1;

        $entry_date = date("Y-m-d H:i:s");
        $result = VisitorList::where( 'id' , $VisitorObj->id)->update( array( 'visit_count' => $visit_count,'visit_status' => 1,'entry_date'=>$entry_date));

        $account_id = Auth::user()->account_id;

        if($bookingObj->id <=0){
            return redirect('opslogin/visitor-summary')->with('status', 'Booking has been cancelled or closed.'); 
        }
        if(isset($bookingObj->visiting_date) && $bookingObj->visiting_start_time <= $visiting_time && $bookingObj->visiting_end_time >= $visiting_time)
		{
            return redirect("opslogin/visitor-summary/$id/edit")->with('status', 'QR code scan successful.'); 
        }
        else{
            return redirect('opslogin/visitor-summary')->with('status', 'QR code not active.'); 
        }
    }

    public function edit($id)
    {
        //
        $bookingObj = VisitorBooking::find($id);
        $bookingObj->view_status = 1;
        $bookingObj->save();
        $account_id = Auth::user()->account_id;

        if($bookingObj->id <=0){
            return redirect('opslogin/visitor-summary')->with('status', 'Booking has been cancelled or closed.'); 
        }
        $propObj = property::find(Auth::user()->account_id);
        $img_full_path = env('APP_URL')."/storage/app/";
        $visiting_types = VisitorType::where('account_id', $account_id)->get();

        return view('admin.visitor_booking.edit', compact('bookingObj','img_full_path','propObj','visiting_types'));
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

        $BookingObj = VisitorBooking::find($id);

        $input = $request->all();

        if($request->input('account_id') !='')
            $BookingObj->account_id = $request->input('account_id');
        else
            $BookingObj->account_id= Auth::user()->account_id;

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

        //Start Insert into notification module
        $notification = array();
        $notification['account_id'] = $BookingObj->account_id;
        $notification['user_id'] = $BookingObj->user_id;
        $notification['unit_no'] = $BookingObj->unit_no;
        $notification['module'] = 'vistor management';
        $notification['ref_id'] = $BookingObj->id;
        $notification['title'] = 'Visitor Update';
        $notification['message'] = 'Visitor Entering Premise Update';
        $notification['created_at'] = date('Y-m-d H:i:s');
        $notification['updated_at'] = date('Y-m-d H:i:s');
        $result = UserNotification::insert($notification);

        $SettingsObj = UserNotificationSetting::where('user_id',$BookingObj->user_id)->where('account_id',$BookingObj->account_id)->first();
        //print_r($SettingsObj);
        if(empty($SettingsObj) || $SettingsObj->visitor_management ==1){
            $fcm_token_array ='';
            $user_token = ',';
            $ios_devices_to_send = array();
            $android_devices_to_send = array();
            $logs = UserLog::where('user_id',$BookingObj->user_id)->where('status',1)->orderby('id','desc')->first();
            if(isset($logs->fcm_token) && $logs->fcm_token !=''){
                $user_token .=$logs->fcm_token.",";
                $fcm_token_array .=$logs->fcm_token.',';
                $appSipAccountList[] = $BookingObj->id;
                if($logs->login_from ==1)
                    $ios_devices_to_send[] = $logs->fcm_token;
                if($logs->login_from ==2)
                    $android_devices_to_send[] = $logs->fcm_token;
            }
            $property = Property::find($BookingObj->account_id);
            $title = "Aerea Home - ".$property->company_name;
            $message = $notification['message'];
            $notofication_data = array();
            $notofication_data['body'] =$title;
            $notofication_data['unit_no'] =$BookingObj->unit_no;   
            $notofication_data['user_id'] =$BookingObj->user_id;   
            $notofication_data['property'] =$BookingObj->account_id;
            $purObj = UserPurchaserUnit::where('property_id',$BookingObj->account_id)->where('unit_id',$BookingObj->unit_no)->where('user_id',$BookingObj->user_id)->first(); 
            if(isset($purObj))
                $notofication_data['switch_id'] =$purObj->id;     

            $NotificationObj = new \App\Models\v7\FirebaseNotification();
            $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
            $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
            //End Insert into notification module
        }


        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$visitorObj->unit_no.'/12';
            return redirect($return_url)->with('status', 'Booking has been updated!');
        }
        else if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/visitor-summary?page=$page#vm")->with('status', 'Booking updated');}
        else
            return redirect('opslogin/visitor-summary#vm')->with('status', 'Booking updated'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

       /* $account_id = Auth::user()->account_id;
        $bookings = VisitorBooking::where('account_id',$account_id)->get();
        foreach($bookings as $booking){
            echo $booking->id."<br/>";
            VisitorList::where('book_id', $booking->id)->delete();
            VisitorBooking::findOrFail($id)->delete();

        }

        exit;*/
        $bookingObj = VisitorBooking::find($id);

        VisitorList::where('book_id', $id)->delete();
        VisitorBooking::findOrFail($id)->delete();

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$bookingObj->unit_no.'/12';
            return redirect($return_url)->with('status', 'Booking deleted');
        }
        else if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/visitor-summary?page=$page#vm")->with('status', 'Booking deleted');}
        else
            return redirect('opslogin/visitor-summary#vm')->with('status', 'Booking deleted'); 
    }


    public function search(Request $request)
    {
        $bookingid  =  $unit = $date = $purpose = $option =  $booking_type = $building ='';
        $option = $request->input('option'); 

       $account_id = Auth::user()->account_id;

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

        //print_r($units);
        $booking_type = $request->input('booking_type');

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
            
        })->orderby('id', 'desc')->paginate(env('PAGINATION_ROWS'));

            $types = VisitorType::where('account_id', $account_id)->pluck('visiting_purpose', 'id')->all();
            $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

            return view('admin.visitor_booking.index', compact('bookings','bookingid','unit','option','types','purpose','date','booking_type','buildings','building'));

    }

    public function availability_check(Request $request)
    {
        
        $cards = array();

        $account_id = Auth::user()->account_id;
        $date = $request->date;
        $purpose = $request->purpose;

        $visiting_purpose = VisitorType::where('account_id',$account_id)->where('id',$purpose)->first();
        
		if($visiting_purpose->limit_set ==0){
			return json_encode(['slot_available'=>5, 'limit'=>'0','id_required'=>$visiting_purpose->id_required]);
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
            return json_encode(['slot_available'=>$slot_available,'limit'=>'1','id_required'=>$visiting_purpose->id_required]);
		
			
		}
        
    }

   
}
