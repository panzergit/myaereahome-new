<?php

namespace App\Http\Controllers;

use App\Models\v7\JoininspectionAppointment;
use Illuminate\Http\Request;
use App\Models\v7\Unit;
use Validator;
use App\Models\v7\User;
use Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DB;
use App\Models\v7\InboxMessage;

class JoininspectionAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected   $timeslots = array('9:00 AM'=>'9:00 AM','10:00 AM'=>'10:00 AM','11:00 AM'=>'11:00 AM','1:00 PM'=>'1:00 PM','2:00 PM'=>'2:00 PM','3:00 PM'=>'3:00 PM','4:00 PM'=>'4:00 PM');
     public function index()
    {
        $q ='';
        $account_id = Auth::user()->account_id;
      
         $units = JoininspectionAppointment::where('account_id',$account_id)->where('status', '0')
                ->whereDate('appt_date', '>=', Carbon::now('Asia/Singapore')) 
                ->paginate(env('PAGINATION_ROWS'));     
               
        return view('admin.inspection.index', compact('units','q'));
    }

    public function lists()
    {

        

        $q = $option =$unit = $status = $name = $users = $month ='';

        $account_id = Auth::user()->account_id;

        $units = JoininspectionAppointment::where('account_id',$account_id)->whereNotIn('status', ['0'])
                ->orderby('appt_date','asc')
                ->paginate(env('PAGINATION_ROWS'));   

        return view('admin.inspection.summary' , compact('units','q','status','name','option','unit','month'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   

        $user = Auth::user();

        $times = $this->timeslots;
      
        $users = User::pluck('name','id')->all();
        if($user->role_id ==6)
            return view('admin.inspection.create', compact('users','times'));
        else
            return view('user.inspectionbook', compact('users','times'));
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

        $record = JoininspectionAppointment::create($input);

        if($user->role_id ==6)
            return redirect('opslogin/inspection_appt');
        else {
             
            return redirect('opslogin/inspection_thankyou')->with('status', $record->id);
        }

        
    }

       public function thankyou()
    {

        $q ='';
        $user = Auth::user();

        if(session('status')) {
            $id = session('status');
            $units = JoininspectionAppointment::find($id);   
            return view('user.inspectionthankyou', compact('units'));
        }
        else{
            return redirect('opslogin/book_inspection');
        }
       
    }


     public function message()
    {

        $q ='';
        
        $user = Auth::user();

        $units = JoininspectionAppointment::where('user_id',$user->id)->orderby("id",'desc')->first();

        return view('user.inspectionmessage', compact('units'));
       
       
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


        $inspectionObj = JoininspectionAppointment::find($id);
        $users = User::pluck('name','id')->all();
        $user = Auth::user();
        $obj = new JoininspectionAppointment();
        $times = $obj->timeslots($user->account_id);	
        
        //$times = $this->timeslots;

        return view('admin.inspection.edit', compact('inspectionObj','users','times'));
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


       /* $data = [
            'appt_date' =>  $request->input('appt_date'),
            'appt_time' =>  $request->input('appt_time'),
            'id' => $id,
        ];
          $validator = Validator::make($data, [
            'appt_time' => [
                'required',
                Rule::unique('joininspection_appointments')->where(function ($query) use($data) {
                    return $query->where('appt_date1', $data['appt_date'])
                        ->where('appt_time', $data['appt_time'])
                        ->whereNotIn('id', [$data['id']]);
                }),
            ],
        ]);*/

        $inspectionObj = JoininspectionAppointment::find($id);


        $result = DB::table('joininspection_appointments')->where('appt_date', $request->input('appt_date'))
                        ->where('appt_time',$request->input('appt_date'))
                        ->whereNotIn('id', [$id])->first();


      
        if (isset($result)) { 
             return redirect("opslogin/inspection_appt/$id/edit")->with('status', 'Date & Time of the Appointment already taken !');         
        }


        $inspectionObj->appt_date = $request->input('appt_date');
        $inspectionObj->appt_time = $request->input('appt_time');
        $inspectionObj->status = $request->input('status');

        if($request->input('status') ==4){

            $inspectionObj->progress_date = $request->input('progress_date');
            $inspectionObj->reminder_in_days = $request->input('reminder_in_days');

            if($request->input('progress_date') !=''){
                $date = Carbon::createFromFormat('Y-m-d', $request->input('progress_date'));
                if($request->input('reminder_in_days') !='')
                    $booking_allowed  = $date->addDays($request->input('reminder_in_days'));
                else
                    $booking_allowed  = $date->addDays(0);
                
                $inspectionObj->reminder_email_send_on = $booking_allowed;
            }

            $inspectionObj->reminder_emails = $request->input('reminder_emails');
            $inspectionObj->email_message = $request->input('email_message');
            $inspectionObj->reminder_email_status = 0;
        }
        
        
        $inspectionObj->save();

        $title = "Your Facility booking has been changed ";

        InboxMessage::where('ref_id', $id)->where('type',5)
                ->update(['title'=>$title,'booking_date'=>$request->input('appt_date'),'booking_time'=>$request->input('appt_time'),'event_status' => $request->input('status')]);



        return redirect('opslogin/inspection_appt/lists')->with('status', 'Appointment has been updated!');

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

        Unit::findOrFail($id)->delete();
        return redirect('opslogin/configuration/unit')->with('status', 'Unit deleted successfully!');
    }

  

    public function gettimeslots(Request $request)
    {
        
        $data = array();
        //$times = $this->timeslots;
        $selecteddate = $request->date;
        $obj = new JoininspectionAppointment();
        $times = $obj->timeslots($request->property);

        foreach($times as $time){
            //echo $time;
            $lists = DB::table("joininspection_appointments")->where('appt_date',$selecteddate)->where('appt_time',$time)->whereNotIn('status', [1])->get();
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

     

       $inbox = InboxMessage::where('ref_id', $bookid)->where('type',5)->first();
       
       if(isset($inbox) && $inbox->id !=''){
        $inboxObj = InboxMessage::find($inbox->id);
        $inboxObj->event_status = $status;
        $inboxObj->save();
        }

        
         JoininspectionAppointment::where('id', $bookid)
                ->update(['status' => $status,'reason'=>$reason]);
        
       

        return redirect('opslogin/defects#defect')->with('status', "Booking Cancelled!!");
     }



     public function updateconfirmstatus(Request $request){


         $input = $request->all();
          $reason ='';

        $bookid = $input['Id'];
        $status = $input['status']; //cancelled

        $inbox = InboxMessage::where('ref_id', $bookid)->where('type',5)->first();
        
        if(isset($inbox) && $inbox->id !=''){
            $inboxObj = InboxMessage::find($inbox->id);
            $inboxObj->event_status = $status;
            $inboxObj->save();
        }

         JoininspectionAppointment::where('id', $bookid)
                ->update(['status' => $status,'reason'=>$reason]);

        
            return redirect('opslogin/defects')->with('status', "Booking Confirmed!!");
        
     }




     public function search(Request $request)
    {

        $q = $option =$unit = $status = $name = $users = $month ='';
        $account_id = Auth::user()->account_id;

        $option = $request->input('option'); 
        $name = $request->input('name');
        $status = $request->input('status');
        $unit = $request->input('unit');

        if ($option != '') {
            if($option == 'name') {

                $units =  JoininspectionAppointment::where('account_id',$account_id)->whereNotIn('joininspection_appointments.status', ['0','1'])
                    ->join('users', 'users.id', '=', 'joininspection_appointments.user_id')->where('users.name', 'LIKE', '%'.$name .'%')
->orderby('joininspection_appointments.appt_date','asc')->paginate(50);

               
            }
            if($option == 'month' ) { 
                $month = $request->input('month');
                $from_date = $month."-1";
                $to_date  = $month."-31";
               

                $units =  JoininspectionAppointment::where('account_id',$account_id)->whereNotIn('status', ['0'])->where(function($query) use ($from_date,$to_date){
                    
                    if($from_date !=''){
                        $query->whereBetween('appt_date',array($from_date,$to_date));
                    }
                    
                })->orderBy('appt_date','DESC')->paginate(env('PAGINATION_ROWS'));   
                
            }

            if($option == 'status') {
                 $units = JoininspectionAppointment::where('account_id',$account_id)->where('status', $status)
                ->orderby('appt_date','asc')
                ->paginate(env('PAGINATION_ROWS'));   
            }

            if($option == 'unit' ) { 

                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';
                
                $units = JoininspectionAppointment::where('account_id',$account_id)->where('unit_no', $unit_id)->whereNotIn('status', ['0'])->orderby('appt_date','asc')->paginate(env('PAGINATION_ROWS'));   
            }
  
            
            return view('admin.inspection.summary' , compact('units','q','status','name','option','unit','month'));

        } else {
            return redirect('opslogin/inspection_appt/lists');
        }
    }

    public function cron_reminderemail()
    {
        $start_date = date('Y-m-d 00:00:00');
        $end_date = date('Y-m-d 23:59:59');

        $results = JoininspectionAppointment::where('status', 4)->where('reminder_email_status', 0)->whereBetween('reminder_email_send_on',array($start_date,$end_date))->get();

       
        foreach($results as $key =>$result){

            $inspectionObj = new \App\Models\v7\JoininspectionAppointment();
            $reminder_email = $inspectionObj->progress_reminder_email($result->id, $result->user_id,$result->account_id);
            
            JoininspectionAppointment::where('id', $result->id)
            ->update(['reminder_email_status' => 1]);

        }


    }

}
