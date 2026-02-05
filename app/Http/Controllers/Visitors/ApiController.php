<?php

namespace App\Http\Controllers\Visitors;

use App\Http\Controllers\Controller;
use App\Models\v7\User;
use App\Models\v7\VisitorType;
use App\Models\v7\VisitorBooking;
use App\Models\v7\VisitorList;
use App\Models\v7\Property;
use App\Models\v7\VisitorInviteEmailList;

use Illuminate\Http\Request;
use QrCode;
use Validator;

class ApiController extends Controller
{
   
	public function visitingPurpose(Request $request)
   {
       
		$rules=array(
			'property' => 'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
		);

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$messages = $validator->messages();
			$errors = $messages->all();
			return response()->json([
				'message' => $errors,
			], 400);
		}

		$input = $request->all();

		$account_id = $input['property'];

	   $data = VisitorType::where('account_id', $account_id)->get();
	   
       return response()->json(['data'=>$data,'response' => 1, 'message' => 'success!']);
   }



	public function visitorRegisitration(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'visiting_date'=>'required',
			'visiting_purpose'=>'required',
			'name_1'=>'required',
			'mobile_1'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'visiting_date.required'=>'Date is missing',
			'visiting_purpose.required'=>'Purpose of visit is missing',
			'name_1.required'=>'Name is missing',
			'mobile_1.required'=>'Mobile is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();
        
        $details = array();
		$UserObj = User::find($input['user_id']);

        $ticket = new \App\VisitorBooking();
		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;

        $propObj = property::find($UserObj->account_id);
		$input['ticket'] = $ticket->ticketgen($propObj->short_code);

		

		$booking = VisitorBooking::create($input);
		$qr_code = '';
		if($propObj->security_option == 2){
			//$inviteurl = env('APP_URL')."/visitor-summary/".$input['ticket'];
			$inviteurl = env('MAIN_URL')."/opslogin/visitor-summary/".$booking->id."/edit";

			$qrsize = 300;
			$img =  base64_encode(QrCode::format('png')->size($qrsize)->errorCorrection('H')->margin(0)->generate($inviteurl));
			$qrdata = base64_decode($img);
			$randnum = rand(10000,99999).'-'.date('YmdHis');
			$file = $_SERVER['DOCUMENT_ROOT'].'/assets/visitorqr/'.$randnum.'.png';	
			file_put_contents($file, $qrdata);  
			$qr_code = $randnum.'.png';
			
		}


		$bookingObj = VisitorBooking::find($booking->id);
		$bookingObj->qrcode_file =$qr_code;
		$bookingObj->save();
		


		$data['book_id'] = $booking->id;
		
		$visiting_purpose = VisitorType::where('account_id',$bookingObj->account_id)->where('id',$bookingObj->visiting_purpose)->first();
        
		if($visiting_purpose->limit_set ==0){
			$slot_available = 5;
        }
        else{
			$visitor_types = VisitorType::where('account_id',$bookingObj->account_id)->where('limit_set',1)->where('status',1)->get();
			$types = array();
			foreach($visitor_types as $type){
				$types[] = $type->id;
			}
			
			$total_visitor =0;
			$visitor_records = VisitorBooking::where('account_id',$bookingObj->account_id)->where('visiting_date',$bookingObj->visiting_date)->whereIn('visiting_purpose',$types)->whereIn('status',[0,2])->get();
			foreach($visitor_records as $records){
				$total_visitor +=$records->visitors->count();
			}
			$count = $propObj->visitors_allowed - $total_visitor;

			$slot_available = ($count >=0)?$count:0;
        }
		
		

        for($i=1;$i<=$slot_available;$i++){

            $name = 'name_'.$i;
            $mobile = 'mobile_'.$i;
            $vehicle ='vehicle_no_'.$i;
			$id_number = 'id_number_'.$i;
			$email = 'email_'.$i;
			$qrcode_file = 'qrcode_file_'.$i;

            //print_r($input);

            if(!empty($request->input($name)) && !empty($request->input($mobile))){
                
                $data['name'] = $request->input($name);
                $data['mobile'] = $request->input($mobile);
				$data['vehicle_no'] = $request->input($vehicle);
				//$data['id_number'] = $request->input($id_number);
				$data['email'] = $request->input($email);
				
				if($request->input($id_number) !='')
                	$data['id_number'] = $request->input($id_number);
            	else
                	$data['id_number'] = '';

                    
                if($propObj->security_option == 1){
					//$inviteurl = env('APP_URL')."/visitor-summary/".$booking->ticket;
					$inviteurl = env('MAIN_URL')."/opslogin/visitor-summary/".$booking->id."/edit";

					$qrsize = 300;
					$img =  base64_encode(QrCode::format('png')->size($qrsize)->errorCorrection('H')->margin(0)->generate($inviteurl));
					$qrdata = base64_decode($img);
					$randnum = rand(10000,99999).'-'.date('YmdHis');
					$file = $_SERVER['DOCUMENT_ROOT'].'/assets/visitorqr/'.$randnum.'.png';	
					file_put_contents($file, $qrdata);   
					
					$data['qrcode_file']= $randnum.'.png';
					
				}else{
					$data['qrcode_file']='';
				}

				$data['created_at'] = $booking->created_at;
				$data['updated_at'] = $booking->updated_at;

				$details[] = $data;
				
				if($propObj->security_option == 1)
                	$qrcodefile_email =  $data['qrcode_file'];
            	else
					$qrcodefile_email =   $bookingObj->qrcode_file;

					$emailObj = new \App\VisitorBooking();
					$emailObj->qrcode_email($bookingObj->id, $bookingObj->user_id, $data['name'],$data['email'],$data['mobile'],$data['vehicle_no'],$qrcodefile_email,$data['id_number']);
            }
            
            
        }

		$record = VisitorList::insert($details);

		
		

		return response()->json(['result'=>$record,'response' => 1, 'message' => 'Visitor has been submitted!']);

        
	}

	public function visitorRegSummary(Request $request){

		$userid = $request->user;
		$UserObj = User::find($userid);

		$file_path =  env('APP_URL').'/assets/visitorqr/';

		$records = VisitorBooking::where('user_id',$userid)->orderby('id','desc')->get();   
		$data = array();
		foreach($records as $k => $record){
			$data[$k] = $record;
			if(isset($record->visitors)){
				$data[$k]['visitors'] = $record->visitors;
			
			}

		}
		$type = VisitorType::where('account_id', $UserObj->account_id)->get();


		return response()->json([
			'booking'=>$data,
			'purpose_lists'=>$type,
			'file_path'=>$file_path,
			'status'=>'success'
		]);

	}

	public function visitorBookingInfo(Request $request){

		$rules=array(
			'user_id' => 'required',
			'book_id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'book_id.required'=>'Booking id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

		$bookid = $request->book_id;
		$userid = $request->user_id;
		$UserObj = User::find($userid);

		//$file_path = env('APP_URL')."/storage/app";
		$file_path =  env('APP_URL').'/assets/visitorqr/';

		$record = VisitorBooking::where('id',$bookid)->first();  
		
		$data['bookinf_info'] =$record;
		
		$type = VisitorType::where('account_id', $UserObj->account_id)->get();

		$total_visitor =0;
		$visitor_records = VisitorBooking::where('account_id',$record->account_id)->where('visiting_date',$record->visiting_date)->whereIn('status',[0,2])->get();
		foreach($visitor_records as $records){
			$total_visitor +=$records->visitors->count();
		}
		
		$property = Property::find($record->account_id);

		$slot_available = $property->visitors_allowed - $total_visitor;


		return response()->json([
			'booking'=>$data,
			'purpose_lists'=>$type,
			'file_path'=>$file_path,
			'available_slots'=>$slot_available,
			'status'=>'success'
		]);

	}

	public function visitorBookingCancel(Request $request){

		$rules=array(
			'user_id' => 'required',
			'book_id'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'book_id.required'=>'Booking id is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

		$bookid = $request->book_id;
		$userid = $request->user_id;
		if($request->reason !='')
			$reason = $request->reason;
		else	
			$reason = '';
		
		VisitorBooking::where('id', $bookid)->where('user_id',$userid)
		->update(['status' => 1,'view_status'=>1,'remarks'=>$reason]);

		return response()->json(['response' => 1, 'message' => 'Cancelled']);


	}

	public function visitorSendInvite(Request $request)
    {
		$rules=array(
			'user_id' => 'required',
			'visiting_date'=>'required',
			'visiting_purpose'=>'required',
			'email_1'=>'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
			'visiting_date.required'=>'Date is missing',
			'visiting_purpose.required'=>'Purpose of visit is missing',
			'email_1.required'=>'Email is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
        $input = $request->all();
        
        $details = array();
		$UserObj = User::find($input['user_id']);

		$ticket = new \App\VisitorBooking();
		
		$propObj = property::find($UserObj->account_id);
		$input['ticket'] = $ticket->ticketgen($propObj->short_code);

		$input['user_id'] = $input['user_id'];
		$input['account_id'] = $UserObj->account_id;
       
        $booking = VisitorBooking::create($input);


        $data['book_id'] = $booking->id;

        for($i=1;$i<=5;$i++){
			$email = 'email_'.$i;
			$name = 'name_'.$i;
            if(!empty($request->input($email)) && !empty($request->input($email))){

				$bookingObj = new \App\VisitorBooking();
				$bookingObj->invite_email($booking->id, $UserObj->id,$UserObj->account_id,$request->input($email),$request->input($name));
				$data['name'] = $request->input($name); 
                $data['email'] = $request->input($email);                    
				$data['created_at'] = $booking->created_at;
				$data['updated_at'] = $booking->updated_at;

                $details[] = $data;
			}
            
		}
		

		$record = VisitorInviteEmailList::insert($details);

		$qr_code ='';
		if($propObj->security_option == 2){
			$inviteurl = env('MAIN_URL')."/opslogin/visitor-summary/".$booking->id."/edit";

			//$inviteurl = env('APP_URL')."/visitor-summary/".$input['ticket'];
	
			$qrsize = 300;
			$img =  base64_encode(QrCode::format('png')->size($qrsize)->errorCorrection('H')->margin(0)->generate($inviteurl));
			$qrdata = base64_decode($img);
			$randnum = rand(10000,99999).'-'.date('YmdHis');
			$file = $_SERVER['DOCUMENT_ROOT'].'/assets/visitorqr/'.$randnum.'.png';	
			file_put_contents($file, $qrdata);  
			$qr_code = $randnum.'.png';
			
		}

		$BookingObj = VisitorBooking::find($booking->id);
		$BookingObj->qrcode_file =$qr_code;
        $BookingObj->save();
		

		return response()->json(['result'=>$record,'response' => 1, 'message' => 'Invitation has been sent!']);

        
	}

	public function visitorRegValidation(Request $request)
    {
		$rules=array(
			'property' => 'required',
			'purpose' => 'required',
			'visiting_date'=>'required',
		);
		$messages=array(
			'property.required' => 'Property id missing',
			'purpose.required' => 'Visiting purpose missing',
			'visiting_date.required'=>'Date is missing',
		);

	   
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }
		
		$input = $request->all();
		
		$visiting_purpose = VisitorType::where('account_id',$input['property'])->where('id',$input['purpose'])->first();
		if($visiting_purpose->limit_set ==0){
			return response()->json(['slot_available'=>5,'response' => 1, 'message' => 'Validation']);
		}
		else{
			$visitor_types = VisitorType::where('account_id',$input['property'])->where('limit_set',1)->where('status',1)->get();
			$types = array();
			foreach($visitor_types as $type){
				$types[] = $type->id;
			}
			
			$total_visitor =0;
			$visitor_records = VisitorBooking::where('account_id',$input['property'])->where('visiting_date',$input['visiting_date'])->whereIn('visiting_purpose',$types)->whereIn('status',[0,2])->get();
			foreach($visitor_records as $records){
				$total_visitor +=$records->visitors->count();
			}
			
			$property = Property::find($input['property']);

			$count = $property->visitors_allowed - $total_visitor;

			$slot_available = ($count >=0)?$count:0;

			return response()->json(['slot_available'=>$slot_available,'response' => 1, 'message' => 'Validation']);
		}
		

        
	}
	
}
