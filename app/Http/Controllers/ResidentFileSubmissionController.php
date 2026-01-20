<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\ResidentUploadedFile;
use App\Models\v7\ResidentFileSubmission;
use App\Models\v7\DocsCategory;


use App\Models\v7\UserLog;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserNotification;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\Property;
use App\Models\v7\UserNotificationSetting;

use App\Models\v7\Unit;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use App\Models\v7\Building;

use DB;
use Auth;
use Session;

use Carbon\Carbon;
use App\Models\v7\InboxMessage;

class ResidentFileSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
    {
        $date = $status = $month = $option = $unit = $category = $building= '';
        $account_id = Auth::user()->account_id;
        $types = DocsCategory::where('account_id',$account_id)->pluck('docs_category', 'id')->all();
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

        $submissions = ResidentFileSubmission::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
        return view('admin.filesubmission.index', compact('submissions','types','unit','date','status','month','option','category','buildings','building'));
    }

    public function new()
    {
       
        $date = $status = $month = $option = $unit ='';
        $date = Carbon::now()->subDays(7);
        $account_id = Auth::user()->account_id;
        //$submissions = ResidentFileSubmission::where('account_id',$account_id)->where('status',0)->where('view_status',0)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        $submissions = ResidentFileSubmission::where('account_id',$account_id)->where('status',0)->where('view_status',0)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        return view('admin.filesubmission.new', compact('submissions','unit','date','status','month','option'));
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
       $submissionObj = ResidentFileSubmission::find($id);
         /*$submissionObj->view_status = 1;
        $submissionObj->save();*/
        ResidentFileSubmission::where('id', $id)
        ->update(['view_status' => 1,'updated_at'=>$submissionObj->updated_at]);

        $file_path = env('APP_URL')."/storage/app";
        return view('admin.filesubmission.edit', compact('submissionObj','file_path'));
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
       
        $submissionObj = ResidentFileSubmission::find($id);
        $submissionObj->remarks = $request->input('remarks');
        $submissionObj->status = $request->input('status');
        $submissionObj->save();

        //Start Insert into notification module
        $notification = array();
        $notification['account_id'] = $submissionObj->account_id;
        $notification['user_id'] = $submissionObj->user_id;
        $notification['unit_no'] = $submissionObj->unit_no;
        $notification['module'] = 'fileupload';
        $notification['ref_id'] = $submissionObj->id;
        $notification['title'] = "Resident's File Upload";
        $notification['message'] = "There is an update from the management in regards to your resident's file upload";
        $result = UserNotification::insert($notification);

        $SettingsObj = UserNotificationSetting::where('user_id',$submissionObj->user_id)->where('account_id',$submissionObj->account_id)->first();
		if(empty($SettingsObj) || $SettingsObj->resident_file_upload ==1){
            $fcm_token_array ='';
            $user_token = ',';
            $ios_devices_to_send = array();
            $android_devices_to_send = array();
            $logs = UserLog::where('user_id',$submissionObj->user_id)->where('status',1)->orderby('id','desc')->first();
            if(isset($logs->fcm_token) && $logs->fcm_token !=''){
                $user_token .=$logs->fcm_token.",";
                $fcm_token_array .=$logs->fcm_token.',';
                $appSipAccountList[] = $submissionObj->id;
                if($logs->login_from ==1)
                    $ios_devices_to_send[] = $logs->fcm_token;
                if($logs->login_from ==2)
                    $android_devices_to_send[] = $logs->fcm_token;
            }

            $probObj = Property::find($submissionObj->account_id);
			$title = "Aerea Home - ".$probObj->company_name;
			$message = "Resident's File Upload Update";
            $notofication_data = array();
            $notofication_data['body'] =$title;
            $notofication_data['unit_no'] =$submissionObj->unit_no;   
            $notofication_data['user_id'] =$submissionObj->user_id;   
            $notofication_data['property'] =$submissionObj->account_id; 
            $purObj = UserPurchaserUnit::where('property_id',$submissionObj->account_id)->where('unit_id',$submissionObj->unit_no)->where('user_id',$submissionObj->user_id)->first(); 
            if(isset($purObj))
                $notofication_data['switch_id'] =$purObj->id;        
            $NotificationObj = new \App\Models\v7\FirebaseNotification();
            $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
            $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
        }

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$submissionObj->unit_no.'/15';
            return redirect($return_url)->with('status', 'Record deleted successfully!');
        }
        else if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/residents-uploads?page=$page#fb")->with('status', 'Record deleted successfully!');}
        else
            return redirect('opslogin/residents-uploads')->with('status', 'Record has been updated!');
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

        ResidentUploadedFile::where('ref_id', $id)->delete();

        ResidentFileSubmission::findOrFail($id)->delete();
        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$submissionObj->unit_no.'/15';
            return redirect($return_url)->with('status', 'Record deleted successfully!');
        }
        else if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/residents-uploads?page=$page#fb")->with('status', 'Record deleted successfully!');}
        else
         return redirect('opslogin/residents-uploads')->with('status', 'Record deleted successfully!');
    }

    public function search(Request $request)
    {
        $date = $status = $month = $option = $unit = $category = $from_date = $to_date = '';

        $account_id = Auth::user()->account_id;
        $option = $request->input('option'); 
        
        $category = $request->input('category');
        $status = $request->input('status');
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
        /*$userids = array();
        $userObj = User::select('id')->where('account_id',$account_id)->wherein('unit_no',$units)->get();
        foreach($userObj as $k => $user){
            $userids[] = $user->id;
        } */
        $month = $request->input('month');
        if($month !=''){          
            $from_date = $month;
            $to_date  = date('Y-m-t', strtotime($month));
        }

        $submissions =  ResidentFileSubmission::where('account_id',$account_id)
        ->where(function ($query) use ($category,$unit,$units,$month,$from_date,$to_date,$status,$building) {
            if($status !='')
                $query->where('status', $status);
            if($unit !='' || $building !='')
                $query->whereIn('unit_no', $units);
            if($month !='')
                $query->whereBetween('created_at',array($from_date,$to_date));
            if($category !='')
                $query->where('cat_id', $category);
            
        })->orderby('id', 'desc')->paginate(env('PAGINATION_ROWS'));
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

        $types = DocsCategory::where('account_id',$account_id)->pluck('docs_category', 'id')->all();

        return view('admin.filesubmission.index', compact('submissions','types','unit','date','status','month','option','category','buildings','building'));

       
    }


}
