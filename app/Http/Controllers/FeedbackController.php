<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\FeedbackOption;
use App\Models\v7\FeedbackSubmission;
use App\Models\v7\UserLog;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\Property;
use App\Models\v7\UserNotificationSetting;

use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use App\Models\v7\Building;

use App\Models\v7\Unit;
use Auth;
use DB;
use Session;
use Carbon\Carbon;
use App\Models\v7\InboxMessage;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function summary(Request $request)
    {
        session()->forget('current_page');

        $account_id = Auth::user()->account_id;
        $ticket  =  $name = $status = $option = $unit = $filter = $category = $month = $building = '';
        $feedbacks = FeedbackSubmission::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        $types = FeedbackOption::where('account_id', $account_id)->pluck('feedback_option', 'id')->all();

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
            $feedbacks = FeedbackSubmission::where('account_id',$account_id)->whereYear('created_at',$year)
                ->when((trim($request->status)!=''), fn($q) => $q->where('status',$request->status))
                ->when((trim($request->category)!=''), fn($q) => $q->where('fb_option',$request->category))
                ->when((trim($request->unit)!=''), fn($q) => $q->where('unit_no',$request->unit))
                ->get();
            $totalFeedbacks = $feedbacks->count();
            $totalFeedbacksOpen = $feedbacks->where('status','0')->values()->count();
            $totalFeedbacksInprogress = $feedbacks->where('status','2')->values()->count();
            $totalFeedbacksClosed = $feedbacks->where('status','1')->values()->count();
            $totalBookingsFacilities = $feedbacks->where('status','0')->values()->count();
            
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
                    'defects' => FeedbackSubmission::where('account_id',$account_id)->whereYear('created_at',$start->copy()->format('Y'))->whereMonth('created_at',$start->copy()->format('m'))->count()
                ];
                $start->addMonth();
            }
            
            $defactsByCategory = FeedbackOption::where('account_id',$account_id)->where('status',1)
                ->when((trim($request->category)!=''),fn($q) => $q->where('id', $request->category))
                ->orderBy('feedback_option')->get()->map(function($q) use($account_id,$year,$units) {
                return [
                    'name' => ucwords(strtolower($q->feedback_option)),
                    'defects' => FeedbackSubmission::where('fb_option',$q->id)
                        ->where('account_id',$account_id)
                        ->when($units,fn($q) => $q->where('unit_no', $units))
                        ->whereYear('created_at', $year)
                        ->count(),
                ];
            });
            
            $compactData = compact('totalFeedbacks', 'totalFeedbacksOpen', 'totalFeedbacksInprogress', 'totalFeedbacksClosed', 'totalBookingsFacilities', 'dates', 'defactsByCategory', 'ticket','unit','name','status','option','filter','types','category','month','buildings','building');
        }else{

            $compactData = compact('feedbacks','ticket','unit','name','status','option','filter','types','category','month','buildings','building');
        }

        return view('admin.feedback.summary', $compactData);
    }

    public function new()
    {
        session()->forget('current_page');

        $account_id = Auth::user()->account_id;
        $date = Carbon::now()->subDays(7);
        $ticket  =  $name = $status = $option = $unit = $filter = '';
        $feedbacks = FeedbackSubmission::where('account_id',$account_id)->where('status',0)->where('view_status',0)->where('created_at', '>=', $date)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        
        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }

        return view('admin.feedback.new', compact('feedbacks','ticket','unit','name','status','option','filter'));
    }


     public function index()
    {
        $q ='';
        $feedbacks = FeedbackOption::paginate(150);   
        return view('admin.feedback.index', compact('feedbacks','q'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::pluck('name','id')->all();
        return view('admin.feedback.create', compact('users'));
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
            'feedback_option' => 'required|unique:feedback_options' 
        ]);
        if ($validator->fails()) { 

             return redirect('opslogin/configuration/feedback/create#settings')->with('status', 'Feedback already exist!');         
        }

        FeedbackOption::create($request->all());
        return redirect('opslogin/configuration/feedback#settings');
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

        $feedbackObj = FeedbackSubmission::find($id);
        $feedbackObj->view_status = 1;
        $feedbackObj->save();
        $file_path = image_storage_domain();
        //$feedbackObj = FeedbackSubmission::find($id);
        return view('admin.feedback.edit', compact('feedbackObj','file_path'));
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

        $feedbackObj = FeedbackSubmission::find($id);
        $feedbackObj->status = $request->input('status');
        $feedbackObj->remarks = $request->input('remarks');
        $feedbackObj->save();

        //Start Insert into notification module
        $notification = array();
        $notification['account_id'] = $feedbackObj->account_id;
        $notification['user_id'] = $feedbackObj->user_id;
        $notification['unit_no'] = $feedbackObj->unit_no;
        $notification['module'] = 'feedback';
        $notification['ref_id'] = $feedbackObj->id;
        $notification['title'] = 'Feedback';
        $notification['message'] = 'There is an update from the management in regards to your submitted feedback';
        $result = UserNotification::insert($notification);

        $SettingsObj = UserNotificationSetting::where('user_id',$feedbackObj->user_id)->where('account_id',$feedbackObj->account_id)->first();
		if(empty($SettingsObj) || $SettingsObj->feedback ==1){
            $fcm_token_array ='';
            $user_token = ',';
            $ios_devices_to_send = array();
            $android_devices_to_send = array();
            $logs = UserLog::where('user_id',$feedbackObj->user_id)->where('status',1)->orderby('id','desc')->first();
            if(isset($logs->fcm_token) && $logs->fcm_token !=''){
                $user_token .=$logs->fcm_token.",";
                $fcm_token_array .=$logs->fcm_token.',';
                $appSipAccountList[] = $feedbackObj->id;
                if($logs->login_from ==1)
                    $ios_devices_to_send[] = $logs->fcm_token;
                if($logs->login_from ==2)
                    $android_devices_to_send[] = $logs->fcm_token;
            }

            $probObj = Property::find($feedbackObj->account_id);
			$title = "Aerea Home - ".$probObj->company_name;
			$message = "Feedback Updated";
            $notofication_data = array();
            $notofication_data['body'] =$title;
            $notofication_data['unit_no'] =$feedbackObj->unit_no;   
            $notofication_data['user_id'] =$feedbackObj->user_id;   
            $notofication_data['property'] =$feedbackObj->account_id; 
            $purObj = UserPurchaserUnit::where('property_id',$feedbackObj->account_id)->where('unit_id',$feedbackObj->unit_no)->where('user_id',$feedbackObj->user_id)->first(); 
            if(isset($purObj))
                $notofication_data['switch_id'] =$purObj->id;        
            $NotificationObj = new \App\Models\v7\FirebaseNotification();
            $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
            $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
        }
        //End Insert into notification module

      

        InboxMessage::where('ref_id', $id)->where('type',2)
        ->update(['event_status' => $request->input('status')]);

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$feedbackObj->unit_no.'/4';
            return redirect($return_url)->with('status', 'Feedback has been updated!');
        }
        else if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/feedbacks/summary?page=$page#fb")->with('status', 'Feedback has been updated!');}
        else
            return redirect('opslogin/feedbacks/summary#fb')->with('status', 'Feedback has been updated!');
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
        $feedbackObj = FeedbackSubmission::find($id);
        FeedbackSubmission::findOrFail($id)->delete();
        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$feedbackObj->unit_no.'/4';
            return redirect($return_url)->with('status', 'Feedback deleted successfully!');
        }
        else if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/feedbacks/summary?page=$page#fb")->with('status', 'Feedback deleted successfully!');}
        else
             return redirect('opslogin/feedbacks/summary#fb')->with('status', 'Feedback deleted successfully!');
    }


   public function submit()
   {
       $q ='';
       $account_id = Auth::user()->account_id;
       $feedbacks = FeedbackOption::where('account_id', $account_id)->pluck('feedback_option', 'id')->all();
       //$feedbacks = FeedbackOption::paginate(150);   
       return view('user.feedbacksubmit', compact('feedbacks','q'));
   }

   public function save(Request $request)
   {
      

        $input = $request->all();

        $ticket = new \App\Models\v7\FeedbackSubmission();
        $input['user_id'] = Auth::user()->id;
        $input['account_id'] = Auth::user()->account_id;
        $input['ticket'] = $ticket->ticketgen();
        
        if ($request->file('upload') != null) {
            $input['upload'] = remove_upload_path($request->file('upload')->store(upload_path('feedback')));
        }
        $input['user_id'] = Auth::user()->id;
        $input['status'] = 0;
        FeedbackSubmission::create($input);

        return redirect('opslogin/feedback/lists#settings')->with('status', 'Feedback has been sent!');
   }

   public function submitlists()
   {
       $user = Auth::user()->id;
       $feedbacks = FeedbackSubmission::where('user_id',$user)->orderby('id','desc')->paginate(50);  

       return view('user.feedbacklists', compact('feedbacks'));
   }

   public function search(Request $request)
    {
        
        $account_id = Auth::user()->account_id;
        $ticket  =  $name = $status = $option = $unit = $filter = $category = $building = '';
        $fromdate = $todate = $month = '';
        $ticket = $request->input('ticket');
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
        $filter = $request->input('filter'); 
        $fromdate = $request->input('fromdate');
        if($request->input('todate') !='')
            $todate = $request->input('todate');
        else
            $todate =$request->input('fromdate');
        $status = $request->input('status');
        $category = $request->input('category');
        $feedbacks = FeedbackSubmission::where('account_id',$account_id)->where(function ($query) use ($ticket,$category,$fromdate,$todate,$unit,$units,$status,$building) {
            if($ticket !='')
                $query->where('ticket', 'LIKE', '%'.$ticket .'%');
            if($unit !='' || $building !='')
                $query->wherein('unit_no', $units);
            if($status !='')
                $query->where('status',$status);
            if($category !='')
                $query->where('fb_option',$category);
            if($fromdate!='' && $todate !='')
                $query->whereBetween('created_at',array($fromdate,$todate));
        })->orderBy($filter,'DESC')->paginate(env('PAGINATION_ROWS'));

            $types = FeedbackOption::where('account_id', $account_id)->pluck('feedback_option', 'id')->all();
            $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

            return view('admin.feedback.summary', compact('feedbacks','ticket','unit','name','status','option','filter','fromdate','todate','month','category','types','buildings','building'));

    }

}
