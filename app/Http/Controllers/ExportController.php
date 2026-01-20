<?php

namespace App\Http\Controllers;
use Auth;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use DB;

use App\Models\v7\Unit;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use App\Models\v7\Card;
use App\Models\v7\Device;
use App\Models\v7\Defect;
use App\Models\v7\ResidentUploadedFile;
use App\Models\v7\ResidentFileSubmission;
use App\Models\v7\EformMovingInOut;
use App\Models\v7\EformRenovation;
use App\Models\v7\EformDoorAccess;
use App\Models\v7\EformRegVehicle;
use App\Models\v7\EformChangeAddress;
use App\Models\v7\EformParticular;

use App\Models\v7\DocsCategory;
use App\Models\v7\VisitorBooking;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\UserPermission;
use App\Models\v7\Role;
use App\Models\v7\JoininspectionAppointment;
use App\Models\v7\UnittakeoverAppointment;
use App\Models\v7\FeedbackOption;
use App\Models\v7\FeedbackSubmission;
use App\Models\v7\FacilityType;
use App\Models\v7\FacilityBooking;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserLicensePlate;

class ExportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
		
		$account_id = Auth::user()->account_id;
		

		$option = $request->input('option'); 
		$name = $request->input('name');
		$role = $request->input('role');
		$unit = $request->input('unit');
		
		
		$filename = 'users_'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['NAME', 'EMAIL', 'PHONE', 'ROLE', 'UNIT', 'MAILING ADDRESS', 'COMPANY'];
		
		fputcsv($fp, $header);
		
		if($option) {
			if($option == 'name' && !empty($name)) {
				$users = User::where('account_id',$account_id)->where('name', 'LIKE', '%' . $name . '%')->get();
			} elseif($option == 'role' && !empty($role)) {
				$users = User::where('account_id',$account_id)->where('role_id', '=', $role)->get();
			} elseif($option == 'unit' && !empty($unit_id)) { 
				$unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit', '=', $unit)->first();
                $unit_id = isset($unitObj->id) ? $unitObj->id : '';
				$users = User::where('account_id',$account_id)->where('unit_no', '=', $unit_id)->get();
			} else {
				$users = User::where('account_id',$account_id)->get();
			}
		} else {
			$users = User::where('account_id',$account_id)->get();
		}

		
		
		foreach($users as $user) {
			$data = [];	
			
			$name = $user->name;
			$email = $user->email;
			
			$phone = $role = $unit = $address = $company = '';

			$moreinfo = UserMoreInfo::where('user_id', $user->id)->first();
			if($moreinfo) {
				$phone = $moreinfo->phone;
				$units = Unit::where('id', $moreinfo->unit_no)->first();
				if($units) {
					$unit = '#'.$units->unit;
				}
				$address = $moreinfo->mailing_address;
				$company = $moreinfo->company_name;				
			}

			$roles = Role::where('id', $user->role_id)->first();
			if($roles) {
				$role = $roles->name;
			}				
						
			$data = [$name, $email, $phone, $role, $unit, $address, $company];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}
	
	public function userExport(Request $request){
	    
	    $fileName = "users_".date('Ymd_His').".csv";
		
		/*$account_id = Auth::user()->account_id;
            $prop_userids = array();
            $userids = UserProperty::where('property_id',$account_id)->orderby('id','desc')->get();        
            foreach($userids as $k =>$v){
                $prop_userids[] = $v->user_id;
            }

			 $allUnits = UserPurchaserUnit::select("id","user_info_id")->where('property_id',Auth::user()->account_id)->orderby('unit_id','DEsC')->get();
                $userinfoids = '';
                foreach($allUnits as $unitlist){
                    $userinfoids .= $unitlist->user_info_id.",";
                }   
                $userinfoids = substr($userinfoids,0,-1);
            
                $users = UserMoreInfo::whereNotIn('status',[2])->where(function ($query) use ($account_id,$prop_userids,$userinfoids) {
                if($account_id !='')
                    $query->where('account_id',$account_id);
                if($prop_userids !='')
                    $query->orwhereIn('user_id', $prop_userids);
                if(strlen($userinfoids)> 0) {
                    $query->orderByRaw(DB::raw("FIELD(id, $userinfoids) DESC"));
                }
                })->orderBy('id','DESC')->get();
				*/

        $users = UserMoreInfo::where([
            ['account_id','=',$request->user()->account_id],
            ['status','=',1]
        ])->get();
    
        $app_user_lists = explode(",",env('USER_APP_ROLE'));
        
       $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
    
        $columns = ['S/NO','PROPERTY', 'BLOCK', 'UNIT','UNIT SHARE', 'FIRST_NAME', 'LAST_NAME','EMAIL', 'ROLE', 'CONTACT','MAILING ADDRESS','POSTAL CODE','LICENSE PLATES'];
    
        $callback = function() use ($users, $columns, $app_user_lists)
        {
           $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
			$rows = 1;
            foreach ($users as $uk => $user)
            {
                $property = $block = $email = $unit_name = $firstName = $lastName = $role = $address = $postal_code = $license_plates ='';
                $role_id = $user->getuser->role_id ?? '';
                $firstName = Crypt::decryptString($user->first_name);
                $lastName = Crypt::decryptString($user->last_name);
				$purchase_units = UserPurchaserUnit::where('user_info_id',$user->id)->orderby('id','ASC')->get();

				if($purchase_units){
					foreach($purchase_units as $purchase_unit){
						if($purchase_unit->unit_id !=''){
						$property = ($purchase_unit->addpropinfo->company_name)?$purchase_unit->addpropinfo->company_name:'';
						$block = ($purchase_unit->addubuildinginfo->building)?$purchase_unit->addubuildinginfo->building:'';
						$unitObj = Unit::where('id',$purchase_unit->unit_id)->first();
						$unit = ($unitObj->unit)?"#".Crypt::decryptString($unitObj->unit):'';
						$unit_share = ($unitObj->share_amount)?$unitObj->share_amount:'';
						$role = isset($purchase_unit->role->name)?$purchase_unit->role->name:'';

						$license_records = UserLicensePlate::where('user_info_id',$user->id)->where('unit_id',$purchase_unit->unit_id)->orderby('id','ASC')->get();
						if($license_records){
							foreach($license_records as $plate){
								$license_plates .=$plate->license_plate.", ";
							}
							$license_plates = substr(trim($license_plates),0,-1);
						}

						fputcsv($file, [$rows,$property,$block, $unit,$unit_share, $firstName, $lastName,$user->getuser->email, $role, (isset($user->phone)?Crypt::decryptString($user->phone):''),$user->mailing_address,($user->postal_code??''),$license_plates]);
						$rows++;}
					}
				}

            }
            fclose($file);
        };
    
        return response()->stream($callback, 200, $headers);
	}
	

	public function inspection(Request $request) {


						
	   $option = $request->input('option'); 
       $name = $request->input('name');
       $status = $request->input('status');
       $unit = $request->input('unit');
		
		$filename = 'booking_joint_inspection__'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['UNIT No','BOOKED BY', 'APPOINTMENT DATE', 'APPOINTMENT TIME', 'STATUS'];
		
		fputcsv($fp, $header);
		
		if($option) {
			if($option == 'name' && !empty($name)) {

				$bookings = JoininspectionAppointment::whereNotIn('joininspection_appointments.status', ['0','1'])
                    ->join('users', 'users.id', '=', 'joininspection_appointments.user_id')->where('users.name', 'LIKE', '%'.$name .'%')
->orderby('joininspection_appointments.appt_date','asc')->get();

			} elseif($option == 'status' && !empty($status)) {

				$bookings = JoininspectionAppointment::where('status', $status)
                ->orderby('appt_date','asc')
                ->get();

			} elseif($option == 'unit' && !empty($unit_id)) { 

				 $unitObj = Unit::select('id')->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';
                
                $bookings = JoininspectionAppointment::where('unit_no', $unit_id)->whereNotIn('status', ['0','1'])->orderby('appt_date','asc')->get();
			} else {
				$bookings = JoininspectionAppointment::all();
			}
		} else {
			$bookings = JoininspectionAppointment::all();
		}
		


		foreach($bookings as $appt) {
			$data = [];	

			if($appt->status==0)
                $status_val= "New";
            else  if($appt->status==1)
                 $status_val= "Cancelled";
            else  if($appt->status==2)
                 $status_val= "On Schedule";
            else  if($appt->status==3)
                 $status_val= "Done";

			
			$unit = isset($appt->getunit->unit)?'#'.$appt->getunit->unit:'';
			$name = isset($appt->getname->name)?$appt->getname->name:'';
			$date = date('d/m/y',strtotime($appt->appt_date));
			$time = $appt->appt_time;
			$status = $status_val;

						
			$data = [$unit, $name, $date, $time, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}

	public function takeover(Request $request) {
						
		
		$account_id = Auth::user()->account_id;
				
		$option = $request->input('option'); 
		$name = $request->input('name');
		$status = $request->input('status');
		$unit = $request->input('unit');
		
		$filename = 'keycollection_'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['UNIT NO','BOOKED BY', 'APPOINTMENT DATE', 'APPOINTMENT TIME', 'STATUS'];
		
		fputcsv($fp, $header);
		
		if($option) {
			if($option == 'month' ) { 
                $month = $request->input('month');
                $from_date = $month."-1";
                $to_date  = $month."-31";
               

                $bookings =  UnittakeoverAppointment::where('account_id',$account_id)->whereNotIn('status', ['0'])->where(function($query) use ($from_date,$to_date){
                    
                    if($from_date !=''){
                        $query->whereBetween('appt_date',array($from_date,$to_date));
                    }
                    
                })->orderBy('appt_date','DESC')->get();   
                
            }

           else if($option == 'status') {
                 $bookings = UnittakeoverAppointment::where('account_id',$account_id)->where('status', $status)
                ->orderby('appt_date','desc')
                ->get();   
            }

           else if($option == 'unit' ) { 

                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';
                
                $bookings = UnittakeoverAppointment::where('account_id',$account_id)->where('unit_no', $unit_id)->whereNotIn('status', ['0'])->orderby('appt_date','asc')->get();   
			}
			else{
				$bookings = UnittakeoverAppointment::where('account_id',$account_id)->whereNotIn('status', ['0'])->get();
			}
					
		} 
		else {
			$bookings = UnittakeoverAppointment::where('account_id',$account_id)->whereNotIn('status', ['0'])->get();
		}
		


		foreach($bookings as $appt) {
			$data = [];	

			if($appt->status==0)
                $status_val= "New";
            else  if($appt->status==1)
                 $status_val= "Cancelled";
            else  if($appt->status==2)
                 $status_val= "On Schedule";
            else  if($appt->status==3)
                 $status_val= "Done";

			
			$unit = isset($appt->getunit->unit)?'#'.$appt->getunit->unit:'';
			$name = isset($appt->getname->name)?$appt->getname->name:'';
			$date = date('d/m/y',strtotime($appt->appt_date));
			$time = $appt->appt_time;
			$status = $status_val;

						
			$data = [$unit, $name, $date, $time, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}

	public function exportdefects(Request $request) {
						
		
		$account_id = Auth::user()->account_id;
				
		$name = $request->input('name');
        $ticket = $request->input('ticket');
        $fromdate = $request->input('fromdate');
        if($request->input('todate') !='')
            $todate = $request->input('todate');
        else
            $todate =$request->input('fromdate');
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
        $userids =array();
        if($name !=''){$user_more_info = UserMoreInfo::select('id','user_id','first_name','last_name')->where('account_id',$account_id)->whereNotIn('status',[2])->orderby('id','desc')->get();
            
            foreach($user_more_info as $k =>$v){
                $firstname = strtolower(Crypt::decryptString($v->first_name));
                $lastname = strtolower(Crypt::decryptString($v->last_name));
                if(str_contains($firstname,strtolower($name)) || str_contains($lastname,strtolower($name))){
                    $userids[] = $v->user_id;
                       
                }
            }
        }  
        $status = $request->input('status');

        $defects =  Defect::where('account_id',$account_id)->where(function ($query) use ($name,$userids,$ticket,$fromdate,$todate,$unit,$units,$status) {
            if($ticket !='')
                $query->where('ticket', 'LIKE', '%'.$ticket .'%');
            if($name !='')
                $query->whereIn('user_id', $userids);
            if($unit !='')
                $query->wherein('unit_no', $units);
            if($status !='')
                $query->where('status', $status);
            if($fromdate!='' && $todate !='')
                $query->whereBetween('created_at',array($fromdate,$todate));
        })->orderby('id','desc')->get();
		
		$filename = 'defects_'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['TICKET NO', 'UNIT NO','SUBMITTED BY','SUBMITTED DATE', 'APPOINTMENT DATE', 'APPOINTMENT TIME', 'STATUS'];
		
		fputcsv($fp, $header); 
		
		foreach($defects as $defect) {
			$data = [];	

			if(isset($defect->status)){
				if($defect->status==0)
				 $status = "OPEN";
				 else if($defect->status==1)
				 $status = "CLOSED";
				else if($defect->status==3)
				$status = "ON SCHEDULE";
				else
				$status = "IN PROGRESS";
			  }

			$ticket = $defect->ticket;
			$unit = isset($defect->getunit->unit)?"#".Crypt::decryptString($defect->getunit->unit):'';
			$fname = isset($defect->user->name)?Crypt::decryptString($defect->user->name):'';
			$lname = isset($defect->user->userinfo_fromadmin->last_name)?Crypt::decryptString($defect->user->userinfo_fromadmin->last_name):'';
			$submitted_by = $fname." ".$lname;
			$date = date('d/m/y',strtotime($defect->created_at));
			$apt_date = isset($defect->inspection->appt_date)?date('d/m/y',strtotime($defect->inspection->appt_date)):'';
			$apt_time = isset($defect->inspection->appt_time)?$defect->inspection->appt_time:'';
			

						
			$data = [$ticket,$unit, $submitted_by, $date, $apt_date,$apt_time, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}

	public function exportfileupload(Request $request) {
						
		
		$account_id = Auth::user()->account_id;
				
		$option = $request->input('option'); 
		
		
		$filename = 'fileuploads'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['UNIT NO','UPLOAD BY','UPLOAD DATE', 'CATEGORY', 'STATUS','UPDATED ON'];
		
		fputcsv($fp, $header); 
		
		if($option) {
			if($option == 'month') {
                $month = $request->input('month');
                $from_date = $month."-1";
                $to_date  = $month."-31";
               

                $submissions = ResidentFileSubmission::where('account_id',$account_id)->where(function($query) use ($from_date,$to_date){
                    
                    if($from_date !=''){
                        $query->whereBetween('created_at',array($from_date,$to_date));
                    }
                    
                })->orderBy('id','DESC')->get();   
            }
           

            else if($option == 'unit' ) { 
                $unit = $request->input('unit');
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';
                $userids = array();
                $userObj = User::select('id')->where('account_id',$account_id)->where('unit_no',$unit_id)->get();
                foreach($userObj as $k => $user){
                    $userids[] = $user->id;
                }
               
                $submissions = ResidentFileSubmission::where('account_id',$account_id)->whereIn('user_id', $userids)
                ->orderby('id','desc')->get(); 

                
            }

            else if($option == 'category') {
                $category = $request->input('category');
                $submissions = ResidentFileSubmission::where('account_id',$account_id)->where('cat_id', $category)
                ->orderby('id','desc')->get();

            }

            else if($option == 'status') {
                $status = $request->input('status');
                $submissions = ResidentFileSubmission::where('account_id',$account_id)->where('status', $status)
                ->orderby('id','desc')->get();

            }
			else{
				$submissions = ResidentFileSubmission::where('account_id',$account_id)->whereNotIn('status', ['0'])->get();
			}
					
		} 
		else {
			$submissions = ResidentFileSubmission::where('account_id',$account_id)->whereNotIn('status', ['0'])->get();
		}
		

		$status = '';
		foreach($submissions as $submission) {
			if(isset($submission->status)){
				if($submission->status==0)
				  	$status = "NEW";
				else if($submission->status==1)
				 	$status = "PROCESSING";
				else
					$status = "PROCESSED";
			  }

			$unit = isset($submission->user->userinfo->getunit->unit)?"#".$submission->user->userinfo->getunit->unit:'';
			$submitted_by = isset($submission->user->name)?$submission->user->name:'';
			$date = date('d/m/y',strtotime($submission->created_at));
			$category = isset($submission->category->docs_category)?$submission->category->docs_category:'';
			$apt_time = isset($defect->inspection->appt_time)?$defect->inspection->appt_time:'';
			$updated_on = ($submission->created_at !=$submission->updated_at)?date('d/m/y',strtotime($submission->updated_at)):'';

						
			$data = [$unit, $submitted_by, $date, $category, $status,$updated_on];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}

	public function exportvisitors(Request $request) {
						
		
		$account_id = Auth::user()->account_id;
				
		$option = $request->input('option'); 
		
		
		$filename = 'visitors'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['BOOKING ID','UNIT NO','INVITED BY','DATE OF VISIT', 'ENTRY DATE', 'ENTRY TIME','VISITORS','PURPOSE', 'STATUS'];
		
		fputcsv($fp, $header); 
		
		if($option) {
			if($option == 'bookingid') {
                $bookingid = $request->input('bookingid');
                $submissions =  VisitorBooking::where('account_id',$account_id)->where('ticket', 'LIKE', '%'.$bookingid .'%')
                    ->orderby('id','desc')->get();
            }

            else if($option == 'purpose') {
                $purpose = $request->input('purpose');
                $submissions = VisitorBooking::where('account_id',$account_id)->where(function ($query) use ($purpose) {
                        $query->where('visiting_purpose', '=', $purpose);
                    })->orderby('id','desc')->get();
            }

            else if($option == 'date') {
                $date = $request->input('date');
                $submissions =  VisitorBooking::where('account_id',$account_id)->where(function($query) use ($date){
                    if($date !=''){
                        $query->where('visiting_date',$date);
                    }
                    

                })->orderby('id','desc')->get();
                
            }
           
            else if($option == 'unit' ) { 
                $unit = $request->input('unit');
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';

                $submissions =  VisitorBooking::where('visitor_bookings.account_id',$account_id)
                    ->join('users', 'users.id', '=', 'visitor_bookings.user_id')->where('users.unit_no', $unit_id)
                    ->orderby('visitor_bookings.id','desc')->get();
                
            }

            else if($option == 'type') {
                $booking_type = $request->input('booking_type');
                $submissions = VisitorBooking::where('account_id',$account_id)->where(function ($query) use ($booking_type) {
                        $query->where('booking_type', '=', $booking_type);
                    })->orderby('id','desc')->get();
            }
			else{
				$submissions = VisitorBooking::where('account_id',$account_id)->get();
			}
					
		} 
		else {
			$submissions = VisitorBooking::where('account_id',$account_id)->get();
		}
		

		$status = '';
		foreach($submissions as $booking) {
			$ticket = $booking->ticket;
			if($booking->booking_type==1)
            	$unit = isset($booking->user->userinfo->getunit->unit)?"#".\Crypt::decryptString($booking->user->userinfo->getunit->unit):'';
            else
				$unit = isset($booking->getunit->unit)?"#".\Crypt::decryptString($booking->getunit->unit):'';
			
			if($booking->booking_type ==1)
               $invited_by = isset($booking->user->name)?\Crypt::decryptString($booking->user->name):'';
            else
				$invited_by = "Walk-In";
			$date_of_visit =date('d/m/y',strtotime($booking->visiting_date));
			
			$entry_date = ($booking->booking_type ==2)?date('d/m/y',strtotime($booking->entry_date)):'';

			$entry_time = ($booking->booking_type ==2)?date('H:i',strtotime($booking->entry_date)):'';

			$visitors = $booking->visitors->count();

			$purpose = isset($booking->visitpurpose->visiting_purpose)?$booking->visitpurpose->visiting_purpose:'';
				 

			if($booking->status==0)
				$status = "Pending";
		 	else if($booking->status==1)
		 		$status = "Cancelled";
		 	else  
			 	$status = "Visited";
						
			$data = [$ticket, $unit, $invited_by, $date_of_visit, $entry_date, $entry_time, $visitors, $purpose, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}

	public function exportmoveinout(Request $request) {
						
		
		$account_id = Auth::user()->account_id;
				
		$option = $request->input('option'); 
		
		
		$filename = 'Eform_moveingin&out_'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['TICKET ID','UNIT NO','SUBMITTED BY','SUBMITTED DATE', 'MOVING START', 'MOVING END','STATUS'];
		
		fputcsv($fp, $header); 
		
		if($option) {
			if($option == 'name') {
                $name = $request->input('name');
                $forms =  EformMovingInOut::where('eform_moving_in_out.account_id',$account_id)->join('users', 'users.id', '=', 'eform_moving_in_out.user_id')->select('eform_moving_in_out.*','users.name')->where('users.name', 'LIKE', '%'.$name .'%')
                    ->orderby('eform_moving_in_out.id','desc')->get();
            }
            else if($option == 'ticket') {
                $ticket = $request->input('ticket');
                $forms =  EformMovingInOut::where('account_id',$account_id)->where('ticket', 'LIKE', '%'.$ticket .'%')
                    ->orderby('id','desc')->get();
            }

            else if($option == 'unit' ) { 
                $unit = $request->input('unit');
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';

                $forms =  EformMovingInOut::where('eform_moving_in_out.account_id',$account_id)
                    ->join('users', 'users.id', '=', 'eform_moving_in_out.user_id')->where('users.unit_no', $unit_id)
                    ->orderby('eform_moving_in_out.id','desc')->get();
                
            }
            else if($option == 'status') {
                $status = $request->input('status');
                if($request->input('status') =='a'){
                    $forms =  EformMovingInOut::where('account_id',$account_id)->orderby('id','desc')->get();
                }
                else
                    {
                        
                        $forms =  EformMovingInOut::where('account_id',$account_id)->where('status', $status)
                    ->orderby('id','desc')->get();
                }
            }
			else{
				$forms = EformMovingInOut::where('account_id',$account_id)->get();
			}
					
		} 
		else {
			$forms = EformMovingInOut::where('account_id',$account_id)->get();
		}
		

		$status = '';
		foreach($forms as $form) {
			$ticket = $form->ticket;
			$unit = isset($form->user->userinfo->getunit->unit)?"#".$form->user->userinfo->getunit->unit:'';
			$submitted_by = isset($form->user->name)?$form->user->name:'';
			$submitted_date = date('d/m/y',strtotime($form->created_at));	
			$moving_start = date('d/m/y',strtotime($form->moving_start));
			$moving_end = date('d/m/y',strtotime($form->moving_end));		
			
			if(isset($form->status)){
				if($form->status==0)
					$status =  "NEW";
				else if($form->status==1)
					$status =  "CANCELLED";
				else if($form->status==3)
					$status =  "APPROVED";
				else if($form->status==2)
					$status =  "IN PROGRESS";
				else if($form->status==5)
					$status =  "PAYMENT PENDING";
				else if($form->status==6)
					$status =  "REFUNDED";
				else 
					$status =  "REJECTED";
			 
			 }

			$data = [$ticket, $unit, $submitted_by, $submitted_date, $moving_start, $moving_end, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}

	public function exportrenovation(Request $request) {
						
		
		$account_id = Auth::user()->account_id;
				
		$option = $request->input('option'); 
		
		
		$filename = 'Eform_renovation_'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['TICKET ID','UNIT NO','SUBMITTED BY','SUBMITTED DATE', 'WORK START', 'WORK END','STATUS'];
		
		fputcsv($fp, $header); 
		
		if($option) {
			if($option == 'name') {
                $name = $request->input('name');
                $forms =  EformRenovation::where('eform_renovations.account_id',$account_id)->join('users', 'users.id', '=', 'eform_renovations.user_id')->select('eform_renovations.*','users.name')->where('users.name', 'LIKE', '%'.$name .'%')
                    ->orderby('eform_renovations.id','desc')->get();
            }
            else if($option == 'ticket') {
                $ticket = $request->input('ticket');
                $forms =  EformRenovation::where('account_id',$account_id)->where('ticket', 'LIKE', '%'.$ticket .'%')
                    ->orderby('id','desc')->get();
            }

            else if($option == 'unit' ) { 
                $unit = $request->input('unit');
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';

                $forms =  EformRenovation::where('eform_renovations.account_id',$account_id)
                    ->join('users', 'users.id', '=', 'eform_renovations.user_id')->where('users.unit_no', $unit_id)
                    ->orderby('eform_renovations.id','desc')->get();
                
            }
            else if($option == 'status') {
                $status = $request->input('status');
                if($request->input('status') =='a'){
                    $forms =  EformRenovation::where('account_id',$account_id)->orderby('id','desc')->get();
                }
                else
                    {
                        
                        $forms =  EformRenovation::where('account_id',$account_id)->where('status', $status)
                    ->orderby('id','desc')->get();
                }
            }
			else{
				$forms = EformRenovation::where('account_id',$account_id)->get();
			}
					
		} 
		else {
			$forms = EformRenovation::where('account_id',$account_id)->get();
		}
		

		$status = '';
		foreach($forms as $form) {
			$ticket = $form->ticket;
			$unit = isset($form->user->userinfo->getunit->unit)?"#".$form->user->userinfo->getunit->unit:'';
			$submitted_by = isset($form->user->name)?$form->user->name:'';
			$submitted_date = date('d/m/y',strtotime($form->created_at));	
			$work_start = date('d/m/y',strtotime($form->reno_start));
			$work_end = date('d/m/y',strtotime($form->reno_end));		
			
			if(isset($form->status)){
				if($form->status==0)
					$status =  "NEW";
				else if($form->status==1)
					$status =  "CANCELLED";
				else if($form->status==3)
					$status =  "APPROVED";
				else if($form->status==2)
					$status =  "IN PROGRESS";
				else if($form->status==5)
					$status =  "PAYMENT PENDING";
				else if($form->status==6)
					$status =  "REFUNDED";
				else 
					$status =  "REJECTED";
			 
			 }

			$data = [$ticket, $unit, $submitted_by, $submitted_date, $work_start, $work_end, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}

	public function exportdooraccess(Request $request) {
						
		
		$account_id = Auth::user()->account_id;
				
		$option = $request->input('option'); 
		
		
		$filename = 'Eform_door_access_'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['TICKET ID','UNIT NO','SUBMITTED BY','SUBMITTED DATE', 'TENANCY START', 'TENANCY END','STATUS'];
		
		fputcsv($fp, $header); 
		
		if($option) {
			if($option == 'name') {
                $name = $request->input('name');
                $forms =  EformDoorAccess::where('eform_door_accesses.account_id',$account_id)->join('users', 'users.id', '=', 'eform_door_accesses.user_id')->select('eform_door_accesses.*','users.name')->where('users.name', 'LIKE', '%'.$name .'%')
                    ->orderby('eform_door_accesses.id','desc')->get();
            }
            else if($option == 'ticket') {
                $ticket = $request->input('ticket');
                $forms =  EformDoorAccess::where('account_id',$account_id)->where('ticket', 'LIKE', '%'.$ticket .'%')
                    ->orderby('id','desc')->get();
            }

            else if($option == 'unit' ) { 
                $unit = $request->input('unit');
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';

                $forms =  EformDoorAccess::where('eform_door_accesses.account_id',$account_id)
                    ->join('users', 'users.id', '=', 'eform_door_accesses.user_id')->where('users.unit_no', $unit_id)
                    ->orderby('eform_door_accesses.id','desc')->get();
                
            }
            else if($option == 'status') {
                $status = $request->input('status');
                if($request->input('status') =='a'){
                    $forms =  EformDoorAccess::where('account_id',$account_id)->orderby('id','desc')->get();
                }
                else
                    {
                        
                        $forms =  EformDoorAccess::where('account_id',$account_id)->where('status', $status)
                    ->orderby('id','desc')->get();
                }
            }
			else{
				$forms = EformDoorAccess::where('account_id',$account_id)->get();
			}
					
		} 
		else {
			$forms = EformDoorAccess::where('account_id',$account_id)->get();
		}
		

		$status = '';
		foreach($forms as $form) {
			$ticket = $form->ticket;
			$unit = isset($form->user->userinfo->getunit->unit)?"#".$form->user->userinfo->getunit->unit:'';
			$submitted_by = isset($form->user->name)?$form->user->name:'';
			$submitted_date = date('d/m/y',strtotime($form->created_at));	
			$tenancy_start = date('d/m/y',strtotime($form->tenancy_start));
			$tenancy_end = date('d/m/y',strtotime($form->tenancy_end));		
			
			if(isset($form->status)){
				if($form->status==0)
					$status =  "NEW";
				else if($form->status==1)
					$status =  "CANCELLED";
				else if($form->status==3)
					$status =  "APPROVED";
				else if($form->status==2)
					$status =  "IN PROGRESS";
				else if($form->status==5)
					$status =  "PAYMENT PENDING";
				else if($form->status==6)
					$status =  "REFUNDED";
				else 
					$status =  "REJECTED";
			 
			 }

			$data = [$ticket, $unit, $submitted_by, $submitted_date, $tenancy_start, $tenancy_end, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}

	public function exportregvehicleiu(Request $request) {
						
		
		$account_id = Auth::user()->account_id;
				
		$option = $request->input('option'); 
		
		
		$filename = 'Eform_reg_vehicle_iu_'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['TICKET ID','UNIT NO','SUBMITTED BY','SUBMITTED DATE', 'TENANCY START', 'TENANCY END','STATUS'];
		
		fputcsv($fp, $header); 
		
		if($option) {
			if($option == 'name') {
                $name = $request->input('name');
                $forms =  EformRegVehicle::where('eform_reg_vehicles.account_id',$account_id)->join('users', 'users.id', '=', 'eform_reg_vehicles.user_id')->select('eform_reg_vehicles.*','users.name')->where('users.name', 'LIKE', '%'.$name .'%')
                    ->orderby('eform_reg_vehicles.id','desc')->get();
            }
            else if($option == 'ticket') {
                $ticket = $request->input('ticket');
                $forms =  EformRegVehicle::where('account_id',$account_id)->where('ticket', 'LIKE', '%'.$ticket .'%')
                    ->orderby('id','desc')->get();
            }

            else if($option == 'unit' ) { 
                $unit = $request->input('unit');
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';

                $forms =  EformRegVehicle::where('eform_reg_vehicles.account_id',$account_id)->whereIn('eform_reg_vehicles.status', ['0'])
                    ->join('users', 'users.id', '=', 'eform_reg_vehicles.user_id')->where('users.unit_no', $unit_id)
                    ->orderby('eform_reg_vehicles.id','desc')->get();
                
            }
            else if($option == 'status') {
                $status = $request->input('status');
                if($request->input('status') =='a'){
                    $forms =  EformRegVehicle::where('account_id',$account_id)->orderby('id','desc')->get();
                }
                else
                    {
                        
                        $forms =  EformRegVehicle::where('account_id',$account_id)->where('status', $status)
                    ->orderby('id','desc')->get();
				}
			}
			else{
				$forms = EformRegVehicle::where('account_id',$account_id)->get();
			}
					
		} 
		else {
			$forms = EformRegVehicle::where('account_id',$account_id)->get();
		}
		

		$status = '';
		foreach($forms as $form) {
			$ticket = $form->ticket;
			$unit = isset($form->user->userinfo->getunit->unit)?"#".$form->user->userinfo->getunit->unit:'';
			$submitted_by = isset($form->user->name)?$form->user->name:'';
			$submitted_date = date('d/m/y',strtotime($form->created_at));	
			$tenancy_start = date('d/m/y',strtotime($form->tenancy_start));
			$tenancy_end = date('d/m/y',strtotime($form->tenancy_end));		
			
			if(isset($form->status)){
				if($form->status==0)
					$status =  "NEW";
				else if($form->status==1)
					$status =  "CANCELLED";
				else if($form->status==3)
					$status =  "APPROVED";
				else if($form->status==2)
					$status =  "IN PROGRESS";
				else if($form->status==5)
					$status =  "PAYMENT PENDING";
				else if($form->status==6)
					$status =  "REFUNDED";
				else 
					$status =  "REJECTED";
			 
			 }

			$data = [$ticket, $unit, $submitted_by, $submitted_date, $tenancy_start, $tenancy_end, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}


	public function exportmailingaddress(Request $request) {
						
		
		$account_id = Auth::user()->account_id;
				
		$option = $request->input('option'); 
		
		
		$filename = 'Eform_mailling_address_'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['TICKET ID','UNIT NO','SUBMITTED BY','SUBMITTED DATE','STATUS'];
		
		fputcsv($fp, $header); 
		
		if($option) {
			if($option == 'name') {
                $name = $request->input('name');
                $forms =  EformChangeAddress::where('eform_address_changes.account_id',$account_id)->join('users', 'users.id', '=', 'eform_address_changes.user_id')->where('users.name', 'LIKE', '%'.$name .'%')
                    ->orderby('eform_address_changes.id','desc')->get();
            }
            else if($option == 'ticket') {
                $ticket = $request->input('ticket');
                $forms =  EformChangeAddress::where('account_id',$account_id)->where('ticket', 'LIKE', '%'.$ticket .'%')
                    ->orderby('id','desc')->get();
            }

            else if($option == 'unit' ) { 
                $unit = $request->input('unit');
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';

                $forms =  EformChangeAddress::where('eform_address_changes.account_id',$account_id)->whereIn('eform_address_changes.status', ['0'])
                    ->join('users', 'users.id', '=', 'eform_address_changes.user_id')->where('users.unit_no', $unit_id)
                    ->orderby('eform_address_changes.id','desc')->get();
                
            }
            else if($option == 'status') {
                $status = $request->input('status');
                if($request->input('status') =='a'){
                    $forms =  EformChangeAddress::where('account_id',$account_id)->orderby('id','desc')->get();
                }
                else
                    {
                        
                        $forms =  EformChangeAddress::where('account_id',$account_id)->where('status', $status)
                    ->orderby('id','desc')->get();
                }
            }
			else{
				$forms = EformChangeAddress::where('account_id',$account_id)->get();
			}
					
		} 
		else {
			$forms = EformChangeAddress::where('account_id',$account_id)->get();
		}
		

		$status = '';
		foreach($forms as $form) {
			$ticket = $form->ticket;
			$unit = isset($form->user->userinfo->getunit->unit)?"#".$form->user->userinfo->getunit->unit:'';
			$submitted_by = isset($form->user->name)?$form->user->name:'';
			$submitted_date = date('d/m/y',strtotime($form->created_at));	
			
			if(isset($form->status)){
				if($form->status==0)
					$status =  "NEW";
				else if($form->status==1)
					$status =  "CANCELLED";
				else if($form->status==3)
					$status =  "APPROVED";
				else if($form->status==2)
					$status =  "IN PROGRESS";
				else 
					$status =  "REJECTED";
			 
			 }

			$data = [$ticket, $unit, $submitted_by, $submitted_date, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}


	public function exportparticular(Request $request) {
						
		
		$account_id = Auth::user()->account_id;
				
		$option = $request->input('option'); 
		
		
		$filename = 'Eform_update_particulars_'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['TICKET ID','UNIT NO','SUBMITTED BY','SUBMITTED DATE','STATUS'];
		
		fputcsv($fp, $header); 
		
		if($option) {
			if($option == 'name') {
                $name = $request->input('name');
                $forms =  EformParticular::where('eform_particulars.account_id',$account_id)->join('users', 'users.id', '=', 'eform_particulars.user_id')->where('users.name', 'LIKE', '%'.$name .'%')
                    ->orderby('eform_particulars.id','desc')->get();
            }
            else if($option == 'ticket') {
                $ticket = $request->input('ticket');
                $forms =  EformParticular::where('account_id',$account_id)->where('ticket', 'LIKE', '%'.$ticket .'%')
                    ->orderby('id','desc')->get();
            }

            else if($option == 'unit' ) { 
                $unit = $request->input('unit');
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';

                $forms =  EformParticular::where('eform_particulars.account_id',$account_id)->whereIn('eform_particulars.status', ['0'])
                    ->join('users', 'users.id', '=', 'eform_particulars.user_id')->where('users.unit_no', $unit_id)
                    ->orderby('eform_particulars.id','desc')->get();
                
            }
            else if($option == 'status') {
                $status = $request->input('status');
                if($request->input('status') =='a'){
                    $forms =  EformParticular::where('account_id',$account_id)->orderby('id','desc')->get();
                }
                else
                    {
                        
                        $forms =  EformParticular::where('account_id',$account_id)->where('status', $status)
                    ->orderby('id','desc')->get();
                }
            }
			else{
				$forms = EformParticular::where('account_id',$account_id)->get();
			}
					
		} 
		else {
			$forms = EformParticular::where('account_id',$account_id)->get();
		}
		

		$status = '';
		foreach($forms as $form) {
			$ticket = $form->ticket;
			$unit = isset($form->user->userinfo->getunit->unit)?"#".$form->user->userinfo->getunit->unit:'';
			$submitted_by = isset($form->user->name)?$form->user->name:'';
			$submitted_date = date('d/m/y',strtotime($form->created_at));	
			
			if(isset($form->status)){
				if($form->status==0)
					$status =  "NEW";
				else if($form->status==1)
					$status =  "CANCELLED";
				else if($form->status==3)
					$status =  "APPROVED";
				else if($form->status==2)
					$status =  "IN PROGRESS";
				else 
					$status =  "REJECTED";
			 
			 }

			$data = [$ticket, $unit, $submitted_by, $submitted_date, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}



	public function printdefect(Request $request) {
						
		$option = $request->input('option'); 
		$name = $request->input('name');
		$role = $request->input('role');
		$unit = $request->input('unit');
		
		$filename = 'users_'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['NAME', 'EMAIL', 'PHONE', 'ROLE', 'UNIT', 'MAILING ADDRESS', 'COMPANY'];
		
		fputcsv($fp, $header);
		
		if($option) {
			if($option == 'name' && !empty($name)) {
				$users = User::where('name', 'LIKE', '%' . $name . '%')->get();
			} elseif($option == 'role' && !empty($role)) {
				$users = User::where('role_id', '=', $role)->get();
			} elseif($option == 'unit' && !empty($unit_id)) { 
				$unitObj = Unit::select('id')->where('unit', '=', $unit)->first();
                $unit_id = isset($unitObj->id) ? $unitObj->id : '';
				$users = User::where('unit_no', '=', $unit_id)->get();
			} else {
				$users = User::all();
			}
		} else {
			$users = User::all();
		}
		
		foreach($users as $user) {
			$data = [];	
			
			$name = $user->name;
			$email = $user->email;
			
			$phone = $role = $unit = $address = $company = '';

			$moreinfo = UserMoreInfo::where('user_id', $user->id)->first();
			if($moreinfo) {
				$phone = $moreinfo->phone;
				$units = Unit::where('id', $moreinfo->unit_no)->first();
				if($units) {
					$unit = '#'.$units->unit;
				}
				$address = $moreinfo->mailing_address;
				$company = $moreinfo->company_name;				
			}

			$roles = Role::where('id', $user->role_id)->first();
			if($roles) {
				$role = $roles->name;
			}				
						
			$data = [$name, $email, $phone, $role, $unit, $address, $company];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}

	public function exportcard(Request $request) {
						
		$q= $option = $card = $status  = $unit ='';
        $option = $request->input('option'); 
        $card = $request->input('card');
        $unit = $request->input('unit');
		$status = $request->input('status');
		
		
		$filename = 'accesscards_'.date("YmdHis").'.csv';

		$account_id = Auth::user()->account_id;
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['CARD NO', 'UNIT NO', 'STATUS'];
		
		fputcsv($fp, $header);
		
		
		if($option) {
			if($option == 'card') {
				
                $cards = Card::where('account_id',$account_id)->where(function ($query) use ($card) {
                        $query->where('card', 'LIKE', '%' . $card . '%');
                    })->get();
            }
            
            else if($option == 'status') {
                $cards = Card::where('account_id',$account_id)->where('status',$status)->get();
            }

            else if($option == 'unit' ) { 
				$units =array();
                $unitObj = Unit::select('id','unit')->where('account_id',$account_id)->get();
				if(isset($unitObj)){
					foreach($unitObj as $unitid){
						if($unit !='' && Crypt::decryptString($unitid->unit) ===$unit)
							$units[] = $unitid->id;
					}
				}
                $cards = Card::where('account_id',$account_id)->where(function ($query) use ($unit_id,$units) {
                        $query->whereIn('unit_no',$units); 
                    })->get();
			}
			else{
				$cards = Card::where('account_id',$account_id)->get();
			}
		} else {
			
			$cards = Card::where('account_id',$account_id)->get();
		}
		
	//exit;
		foreach($cards as $card) {
			$data = [];	
			$card_no = $unit = $status = '';
			$card_no = $card->card;
			$unit = '';
			$units = Unit::where('account_id',$account_id)->where('id', $card->unit_no)->first();
			if(isset($units)) {
				$unit = '#'.\Crypt::decryptString($units->unit);
			}
			if($card->status ==1)
				$status = "Active";
            else if($card->status ==2)
				$status = "Inactive";
        	else if($card->status ==3)
				$status = "Faulty";
            else if($card->status ==4)
				$status = "Loss";
            else if($card->status ==5)
				$status = "Stolen";
						
			$data = [$card_no, $unit, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}

	public function exportdevice(Request $request) {
						
		$q= $option = $card = $status  = $unit ='';
        $option = $request->input('option'); 
		$name = $request->input('name');
        $serial_no = $request->input('serial_no');
		$status = $request->input('status');
		
		
		$filename = 'device_'.date("YmdHis").'.csv';

		$account_id = Auth::user()->account_id;
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['DEVICE NAME', 'SERIAL NO','MODEL','LOCATION', 'STATUS'];
		
		fputcsv($fp, $header); 
		
		if($option) {
			if($option == 'name') {
				
                $devices = Device::where('account_id',$account_id)->where(function ($query) use ($name) {
					$query->where('device_name', 'LIKE', '%' . $name . '%');
				})->get();
            }
            else if($option == 'serial_no'){
				
				$devices = Device::where('account_id',$account_id)->where(function ($query) use ($serial_no) {
                    $query->where('device_serial_no', 'LIKE', '%' . $serial_no . '%');
                })->get();
			
			}
            else if($option == 'status') {
                $devices = Device::where('account_id',$account_id)->where('status',$status)->get();
            }

			else{
				$devices = Device::where('account_id',$account_id)->get();
			}
		} else {
			
			$devices = Device::where('account_id',$account_id)->get();
		}
		
	
		foreach($devices as $device) {
			$data = [];	
			$name = $serial_no= $model = $building = $status = '';
			$name = $device->device_name;
			$serial_no = $device->device_serial_no;
			$model = $device->model;
			if(isset($device->buildinginfo->building)) {
				$building = $device->buildinginfo->building;
			}
			if($device->status ==1)
				$status = "Online";
			else
				$status = "Offline";
						
			$data = [$name, $serial_no,$model,$building, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}

	public function exportfeedback(Request $request) {
						
		
		$account_id = Auth::user()->account_id;
				
		$ticket  =  $name = $status = $option = $unit = $filter = $category ='';
        $fromdate = $todate = $month = '';
        $unit = $request->input('unit');
        $option = $request->input('option');   
        $filter = $request->input('filter'); 
		
		$filename = 'feedbacks_'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['TICKET NO', 'SUBMITTED DATE','CATEGORY', 'UNIT NO', 'SUBMITTED By', 'STATUS','UPDATED ON'];
		
		fputcsv($fp, $header); 
		
		if($option) {
			if($option == 'date') {
                
                $fromdate = $request->input('fromdate');
                if($request->input('todate') !='')
                    $todate = $request->input('todate');
                else
                    $todate =$request->input('fromdate');

                $feedbacks =  FeedbackSubmission::where('account_id',$account_id)->where(function($query) use ($fromdate,$todate,$filter){
                    if($fromdate !=''){
                        $query->whereBetween('created_at',array($fromdate,$todate));
                    }
                   
                })->orderBy($filter,'DESC')->get();
                
            }
            else if($option == 'ticket') {
                $ticket = $request->input('ticket');
                $feedbacks =  FeedbackSubmission::where('account_id',$account_id)->where(function($query) use ($ticket,$filter){
                    if($ticket !=''){
                        $query->where('ticket', 'LIKE', '%'.$ticket .'%');
                    }
                    
                })->orderBy($filter,'DESC')->get();
                
            }

            else if($option == 'unit' ) { 
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';

                $feedbacks =  FeedbackSubmission::where('feedback_submissions.account_id',$account_id)->join('users', 'users.id', '=', 'feedback_submissions.user_id')->where('users.unit_no',$unit_id)->orderBy("feedback_submissions.".$filter,'DESC')->get();

                
               
            }
            else if($option == 'category') {
                $category = $request->input('category');
 
                 $feedbacks =  FeedbackSubmission::where('account_id',$account_id)->where(function($query) use ($category,$filter){
                     if($category !=''){
                         $query->where('fb_option',$category);
                     }
                     
 
                 })->orderBy($filter,'DESC')->get();
 
             }

            else if($option == 'month' ) { 
                $month = $request->input('month');
                $from_date = $month."-1";
                $to_date  = $month."-31";
               

                $feedbacks =  FeedbackSubmission::where('account_id',$account_id)->where(function($query) use ($from_date,$to_date,$filter){
                    
                    if($from_date !=''){
                        $query->whereBetween('created_at',array($from_date,$to_date));
                    }
                    
                })->orderBy($filter,'DESC')->get();
                
            }
            else if($option == 'status') {
                $status = $request->input('status');
                $feedbacks =  FeedbackSubmission::where('account_id',$account_id)->where('status', $status)
                    ->orderby('status','desc')->paginate(env('PAGINATION_ROWS'));
            }
			else{
				$feedbacks = FeedbackSubmission::where('account_id',$account_id)->get();
			}
					
		} 
		else {
			$feedbacks = FeedbackSubmission::where('account_id',$account_id)->get();
		}
		
		

		foreach($feedbacks as $feedback) {
			$data = [];	

			
				if($feedback->status==0)
                    $status =  "OPEN";
            	else if($feedback->status==1)
					$status = "CLOSED";
                else
					$status = "IN PROGRESS";
                
		

			$ticket = $feedback->ticket;
			$submitted_date = date('d/m/y',strtotime($feedback->created_at));
			$category = isset($feedback->getoption->feedback_option)?$feedback->getoption->feedback_option:'';
			$unit = isset($feedback->user->userinfo->getunit->unit)?"#".\Crypt::decryptString($feedback->user->userinfo->getunit->unit):'';
			$submitted_by = isset($feedback->user->name)?$feedback->user->name:'';
			$updated_at = date('d/m/y',strtotime($feedback->updated_at));

			$data = [$ticket,$submitted_date,$category,$unit, $submitted_by, $status,$updated_at];
			

			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}


	public function exportfacility(Request $request) {
						
		
		$account_id = Auth::user()->account_id;
				
		$unit = $request->input('unit');
        $option = $request->input('option');   
        $filter = $request->input('filter'); 
		
		$filename = 'facility_booking_'.date("YmdHis").'.csv';
			
		$fp = fopen('php://output', 'w');
		
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$header = ['FACILITY','BOOKED BY','UNIT NO','BOOKING DATE','BOOKING TIME', 'STATUS'];
		
		fputcsv($fp, $header); 
		
		if($option) {
			if($option == 'date') {
                
                $fromdate = $request->input('fromdate');
                if($request->input('todate') !='')
                    $todate = $request->input('todate');
                else
                    $todate =$request->input('fromdate');

                $bookings =  FacilityBooking::where('account_id',$account_id)->where(function($query) use ($fromdate,$todate,$filter){
                    if($fromdate !=''){
                        $query->whereBetween('booking_date',array($fromdate,$todate));
                    }
                    

                })->orderBy($filter,'DESC')->get();
                
            }
            else if($option == 'unit' ) { 

                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';
                
                $bookings = FacilityBooking::where('account_id',$account_id)->whereNotIn('status', ['0'])->where(function($query) use ($unit_id){
                    if($unit_id !=''){
                        $query->where('unit_no',$unit_id);
                    }
                   
                })->orderBy($filter,'DESC')->get();
                
               
            }

            else if($option == 'month' ) { 
                $month = $request->input('month');
                $from_date = $month."-1";
                $to_date  = $month."-31";

                $bookings =  FacilityBooking::where('account_id',$account_id)->where(function($query) use ($from_date,$to_date,$filter){
                    if($from_date !=''){
                        $query->whereBetween('created_at',array($from_date,$to_date));
                    }
                   
                })->orderBy($filter,'DESC')->get();
                
            }
            else if($option == 'category') {
                $category = $request->input('category');

                $bookings =  FacilityBooking::where('account_id',$account_id)->where(function($query) use ($category,$filter){
                    if($category !=''){
                        $query->where('type_id',$category);
                    }
                    

                })->orderBy($filter,'DESC')->get();

            }

            else if($option == 'status') {
                $status = $request->input('status');

                $bookings =  FacilityBooking::where('account_id',$account_id)->where(function($query) use ($status,$filter){
                    if($status !=''){
                        $query->where('status',$status);
                    }
                    

                })->orderBy($filter,'DESC')->get();

            }
			else{
				$bookings = FacilityBooking::where('account_id',$account_id)->wherein('status',[1,2])->get();
			}
					
		} 
		else {
			$bookings = FacilityBooking::where('account_id',$account_id)->wherein('status',[1,2])->get();
		}
		
		

		foreach($bookings as $booking) {
			$data = [];	

			$type = isset($booking->gettype->facility_type)?$booking->gettype->facility_type:'';
			$booked_by = isset($booking->getname->name)?$booking->getname->name:'';	
			$unit = isset($booking->getname->getunit->unit)?"#".$booking->getname->getunit->unit:'';
			$booking_date = date('d/m/y',strtotime($booking->booking_date));
			$booking_time = $booking->booking_time;
			if(isset($booking->status)){
				if($booking->status==0)
				  	$status =  "New";
				else if($booking->status==1)
					$status =  "Cancelled";
				else
					$status = "Confirmed";
			  }
			
			$data = [$type,$booked_by, $unit, $booking_date, $booking_time, $status];
			
			fputcsv($fp, $data);
		}
		
		fclose($fp);
		
		exit;
	}

	public function exportmultiunituser() {
		$filename = 'User_Lists'.date("YmdHis").'.csv';
		$fp = fopen('php://output', 'w');
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
		$header = ['USER ID','FIRST NAME','LAST NAME','EMAIL','TOTAL UNIT',"PROPERTIES"];
		
		fputcsv($fp, $header); 
		$records = DB::select("SELECT user_id,user_info_id, count(user_id) as total_units FROM user_purchaser_units GROUP BY user_id HAVING COUNT(user_id) >1");
		foreach($records as $record) {
			$data = [];	
			$UserRec= User::where('id',$record->user_id)->first();
			$purchaseLists = DB::select("SELECT p.company_name,u.property_id from  user_purchaser_units as u, properties as p WHERE u.property_id = p.id and u.user_id = $record->user_id");
			$properties ='';
			foreach($purchaseLists as $purchaseList) {
				$properties .=$purchaseList->company_name.", ";
			}

			$userObj = UserMoreInfo::where('id',$record->user_info_id)->first();
			if(!empty($userObj)){
				$fname = Crypt::decryptString($userObj->first_name);
				$lname = Crypt::decryptString($userObj->last_name);
				$data = [$record->user_id,$fname,$lname,$UserRec->email,$record->total_units,$properties];
				//echo $record->user_id. " ". $fname. " ". $lname. " ". $record->total_units;
				fputcsv($fp, $data);
			//echo $record->user_id. "<br />";
			}
		}
		
		fclose($fp);
		
		exit;
	}
}
