<?php

namespace App\Http\Controllers;
use Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\EformSetting;
use App\Models\v7\EformMovingInOut;
use App\Models\v7\EformMovingSubCon;
use App\Models\v7\EformMovingPayment;
use App\Models\v7\EformMovingInspection;
use App\Models\v7\EformMovingDefect;

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

class EformMovingInOutController extends Controller
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
        $visitor_app_url = env('VISITOR_APP_URL');

        $forms = EformMovingInOut::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 
        return view('admin.eform_move.index', compact('forms','ticket','unit','name','status','option','visitor_app_url'));
    }

    public function new()
    {
        session()->forget('current_page');

        $ticket  =  $name = $status = $option = $unit ='';
        $date = Carbon::now()->subDays(7);
        $account_id = Auth::user()->account_id;
        $forms = EformMovingInOut::where('account_id',$account_id)->where('status',0)->where('view_status',0)->where('created_at', '>=', $date)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        return view('admin.eform_move.new', compact('forms','ticket','unit','name','status','option'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::pluck('name','id')->all();
        return view('admin.eform_move.create', compact('users'));
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

             return redirect('opslogin/eform/moveinout/create')->with('status', 'Defect Location already exist!');         
        }
        $input = $request->all();

        
        DefectLocation::create($input);
        return redirect('opslogin/eform/moveinout');
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
        $eformObj = EformMovingInOut::find($id);
        $eformObj->view_status = 1;
        $eformObj->save();

        $file_path = env('APP_URL')."/storage/app";
        return view('admin.eform_move.edit', compact('eformObj','file_path'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */

    public function payment($id)
    {
        //
        $eformObj = EformMovingInOut::find($id);
        $eformObj->view_status = 1;
        $eformObj->save();
        $account_id = Auth::user()->account_id;

        $eformsettingsObj = EformSetting::where('account_id', $account_id)->where('eform_type', 40)->first();
        $file_path = env('APP_URL')."/storage/app";
        return view('admin.eform_move.payment', compact('eformObj','file_path','eformsettingsObj'));
    }
    public function paymentsave(Request $request,$id)
    {
        //
      
        $eformObj = EformMovingInOut::find($id);

        $account_id = Auth::user()->account_id;
        $login_id = Auth::user()->id;
        
        //$eformsettingsObj = EformSetting::where('account_id', $account_id)->where('eform_type', 40)->first();

				if ($request->input('payment_id') != null) {

						$paymentObj = EformMovingPayment::find($request->input('payment_id'));
						
						
						$paymentObj['mov_id'] = $id;
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
                            
                         
                        $paymentObj['lift_payment_option'] = $request->input('lift_payment_option');
                        

						if($paymentObj['lift_payment_option'] ==1){

							if($request->input('lift_cheque_amount') != null)
								$paymentObj['lift_cheque_amount'] = $request->input('lift_cheque_amount');

							if($request->input('lift_cheque_no') != null)
                                $paymentObj['lift_cheque_no'] = $request->input('lift_cheque_no');
                            
                            if($request->input('lift_cheque_received_date') != null)
								$paymentObj['lift_cheque_received_date'] = $request->input('lift_cheque_received_date');
							
							if($request->input('lift_cheque_bank') != null)
								$paymentObj['lift_cheque_bank'] = $request->input('lift_cheque_bank');
						}
						if($paymentObj['lift_payment_option'] ==2){
							if($request->input('lift_bt_received_date') != null)
								$paymentObj['lift_bt_received_date'] = $request->input('lift_bt_received_date');

							if($request->input('lift_bt_amount_received') != null)
								$paymentObj['lift_bt_amount_received'] = $request->input('lift_bt_amount_received');
						}
						if($paymentObj['lift_payment_option'] ==3){
							if($request->input('lift_cash_amount_received') != null)
								$paymentObj['lift_cash_amount_received'] = $request->input('lift_cash_amount_received');

							if($request->input('lift_cash_received_date') != null)
								$paymentObj['lift_cash_received_date'] = $request->input('lift_cash_received_date');
							
						}
						
						if($request->input('lift_receipt_no') != null)   
							$paymentObj['lift_receipt_no'] = $request->input('lift_receipt_no');

						if($request->input('lift_acknowledged_by') != null)   
							$paymentObj['lift_acknowledged_by'] = $request->input('lift_acknowledged_by');

						if($request->input('lift_manager_received') != null)   
							$paymentObj['lift_manager_received'] = $request->input('lift_manager_received');

						if ($request->file('lift_signature') != null) {
							$paymentObj['lift_signature'] = base64_encode(file_get_contents($request->file('lift_signature')));
				
						}

						if($request->input('lift_date_of_signature') != null)   
                            $paymentObj['lift_date_of_signature'] = $request->input('lift_date_of_signature');
                            
                           

						$paymentObj->save();
						
						

					} else {
						
						$payment['mov_id'] = $id;
						$payment['manager_id'] = $login_id;
						$payment['payment_option'] = $request->input('payment_option');

						if($payment['payment_option'] ==1){

							if($request->input('cheque_amount') != null)
								$payment['cheque_amount'] = $request->input('cheque_amount');

							if($request->input('cheque_no') != null)
                                $payment['cheque_no'] = $request->input('cheque_no');
                            
                            if($request->input('cheque_received_date') != null)
								$payment['cheque_received_date'] = $request->input('cheque_received_date');
							
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
                            
                        $payment['lift_payment_option'] = $request->input('lift_payment_option');
                        

                            if($payment['lift_payment_option'] ==1){
    
                                if($request->input('lift_cheque_amount') != null)
                                    $payment['lift_cheque_amount'] = $request->input('lift_cheque_amount');
                            
                                if($request->input('lift_cheque_received_date') != null)
                                    $payment['lift_cheque_received_date'] = $request->input('lift_cheque_received_date');
    
                                if($request->input('lift_cheque_no') != null)
                                    $payment['lift_cheque_no'] = $request->input('lift_cheque_no');
                                
                                if($request->input('lift_cheque_bank') != null)
                                    $payment['lift_cheque_bank'] = $request->input('lift_cheque_bank');
                            }
                            if($payment['lift_payment_option'] ==2){
                                if($request->input('lift_bt_received_date') != null)
                                    $payment['lift_bt_received_date'] = $request->input('lift_bt_received_date');
    
                                if($request->input('lift_bt_amount_received') != null)
                                    $payment['lift_bt_amount_received'] = $request->input('lift_bt_amount_received');
                            }
                            if($payment['lift_payment_option'] ==3){
                                if($request->input('lift_cash_amount_received') != null)
                                    $payment['lift_cash_amount_received'] = $request->input('lift_cash_amount_received');
    
                                if($request->input('lift_cash_received_date') != null)
                                    $payment['lift_cash_received_date'] = $request->input('lift_cash_received_date');
                                
                            }
                            
                            if($request->input('lift_receipt_no') != null)   
                                $payment['lift_receipt_no'] = $request->input('lift_receipt_no');
    
                            if($request->input('lift_acknowledged_by') != null)   
                                $payment['lift_acknowledged_by'] = $request->input('lift_acknowledged_by');
    
                            if($request->input('lift_manager_received') != null)   
                                $payment['lift_manager_received'] = $request->input('lift_manager_received');
    
                            if ($request->file('lift_signature') != null) {
                                $payment['lift_signature'] = base64_encode(file_get_contents($request->file('lift_signature')));
                    
                            }
    
                            if($request->input('lift_date_of_signature') != null)   
                                $payment['lift_date_of_signature'] = $request->input('lift_date_of_signature');
                                
                               

						$payment['created_at'] = date("Y-m-d H:i:s");
						$payment['updated_at'] = date("Y-m-d H:i:s");
						EformMovingPayment::insert($payment);
					
                }

                //Start Insert into notification module
                $notification = array();
                $notification['account_id'] = $eformObj->account_id;
                $notification['user_id'] = $eformObj->user_id;
                $notification['unit_no'] = $eformObj->unit_no;
                $notification['module'] = 'eform_move_in_out';
                $notification['ref_id'] = $eformObj->id;
                $notification['title'] = 'Move In & Out E-form';
                $notification['message'] = 'There is an update from the management in regards to your payment on Move In & Out E-form';
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
                    $message = "Move In & Out E-form Payment";
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
                //End Insert into notification module
                
                if(Session::get('current_page') =='unit_summary'){
                    $return_url = 'opslogin/configuration/unit_summary/'.$eformObj->unit_no.'/5';
                    return redirect($return_url)->with('status', 'Payment details updated!');
                }
                else{
                    return redirect('opslogin/eform/moveinout')->with('status', 'Payment details updated!');   
                }
                      
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */

    public function inspection($id)
    {
        //
        $eformObj = EformMovingInOut::find($id);
        $eformObj->view_status = 1;
        $eformObj->save();
        $account_id = Auth::user()->account_id;

        $defect_files =array();
        if (isset($eformObj->defects)) {
            foreach ($eformObj->defects as $k => $defect) {
                $defect_file['key'] = $k + 1;
                $defect_file['id'] = $defect['id'];
                $defect_file['image_file'] = $defect['image_base64'];
                $defect_file['notes'] = $defect['notes'];
                $defect_files[$k + 1] = $defect_file;
            }
        }

        

        $eformsettingsObj = EformSetting::where('account_id', $account_id)->where('eform_type', 40)->first();
        $file_path = env('APP_URL')."/storage/app";
        return view('admin.eform_move.inspection', compact('eformObj','file_path','eformsettingsObj','defect_files'));
    }

    public function inspectionsave(Request $request,$id)
    {
        //
      
        $eformObj = EformMovingInOut::find($id);

        $account_id = Auth::user()->account_id;
        $login_id = Auth::user()->id;

        if ($request->input('inspection_id') != null) {

                $inspectionObj = EformMovingInspection::find($request->input('inspection_id'));
                
                
                $inspectionObj['mov_id'] = $id;
                $inspectionObj['manager_id'] = $login_id;
                if($request->input('date_of_completion') != null)
                    $inspectionObj['date_of_completion'] = $request->input('date_of_completion');
                
                if($request->input('inspected_by') != null)
                    $inspectionObj['inspected_by'] = $request->input('inspected_by');

                if($request->input('unit_in_order_or_not') != null)    
                    $inspectionObj['unit_in_order_or_not'] = $request->input('unit_in_order_or_not');

                if($request->input('amount_deducted') != null)   
                    $inspectionObj['amount_deducted'] = $request->input('amount_deducted');

                if($request->input('refunded_amount') != null)   
                    $inspectionObj['refunded_amount'] = $request->input('refunded_amount');

                if($request->input('amount_claimable') != null)   
                    $inspectionObj['amount_claimable'] = $request->input('amount_claimable');

                if($request->input('actual_amount_received') != null)   
                    $inspectionObj['actual_amount_received'] = $request->input('actual_amount_received');

                if($request->input('acknowledged_by') != null)   
                    $inspectionObj['acknowledged_by'] = $request->input('acknowledged_by');

                if($request->input('resident_nric') != null)   
                    $inspectionObj['resident_nric'] = $request->input('resident_nric');

                if($request->input('resident_signature_date') != null)   
                    $inspectionObj['resident_signature_date'] = $request->input('resident_signature_date');

                if($request->input('manager_received') != null)   
                    $inspectionObj['manager_received'] = $request->input('manager_received');

                if($request->input('date_of_signature') != null)   
                    $inspectionObj['date_of_signature'] = $request->input('date_of_signature');

                    $inspectionObj['updated_at'] = date("Y-m-d H:i:s");

                $inspectionObj->save();
            
            

        } else {

            $inspectionObj['mov_id'] = $id;
            $inspectionObj['manager_id'] = $login_id;
            if($request->input('date_of_completion') != null)
                $inspectionObj['date_of_completion'] = $request->input('date_of_completion');
            
            if($request->input('inspected_by') != null)
                $inspectionObj['inspected_by'] = $request->input('inspected_by');

            if($request->input('unit_in_order_or_not') != null)    
                $inspectionObj['unit_in_order_or_not'] = $request->input('unit_in_order_or_not');

            if($request->input('amount_deducted') != null)   
                $inspectionObj['amount_deducted'] = $request->input('amount_deducted');

            if($request->input('refunded_amount') != null)   
                $inspectionObj['refunded_amount'] = $request->input('refunded_amount');

            if($request->input('amount_claimable') != null)   
                $inspectionObj['amount_claimable'] = $request->input('amount_claimable');

            if($request->input('actual_amount_received') != null)   
                $inspectionObj['actual_amount_received'] = $request->input('actual_amount_received');

            if($request->input('acknowledged_by') != null)   
                $inspectionObj['acknowledged_by'] = $request->input('acknowledged_by');

            if($request->input('resident_nric') != null)   
                $inspectionObj['resident_nric'] = $request->input('resident_nric');

            if($request->input('resident_signature_date') != null)   
                $inspectionObj['resident_signature_date'] = $request->input('resident_signature_date');

            if($request->input('manager_received') != null)   
                $inspectionObj['manager_received'] = $request->input('manager_received');

            if($request->input('date_of_signature') != null)   
                $inspectionObj['date_of_signature'] = $request->input('date_of_signature');

            $inspectionObj['created_at'] = date("Y-m-d H:i:s");
            $inspectionObj['updated_at'] = date("Y-m-d H:i:s");

            $record =  EformMovingInspection::insert($inspectionObj);

            
                
            }

            /********** INSERT documents START******************/
            for ($i = 1; $i <= 5; $i++) {
                $file = 'file_' . $i;
                $file_id = 'file_id_' . $i;
                $notes= 'description_'.$i;

               
                if ($request->input($file_id) != null) {

                    if($request->input($notes) ==''){
                        EformMovingDefect::findOrFail($request->input($file_id))->delete();
                    }else{
                        $fileObj = EformMovingDefect::find($request->input($file_id));
                        $fileObj->mov_id = $id;

                        if ($request->file($file) != null) {
                            $fileObj->image_base64 = base64_encode(file_get_contents($request->file($file)));
                        }
                        $fileObj->notes= $request->input($notes);
                        $fileObj->updated_at = date("Y-m-d H:i:s");
                        $fileObj->save();
                    }

                } else if ($request->input($notes) != null) {
                    $type = array();
                    $type['account_id'] = $account_id;
                    $type['mov_id'] = $id;

                    if ($request->file($file) != null) {
                        $type['image_base64'] = base64_encode(file_get_contents($request->file($file)));
                        
                    }
                    $type['notes']= $request->input($notes); 
                    $type['created_at'] = date("Y-m-d H:i:s");
                    $type['updated_at'] = date("Y-m-d H:i:s");
                    EformMovingDefect::insert($type);
                    
                }

                
            }

            //Start Insert into notification module
            $notification = array();
            $notification['account_id'] = $eformObj->account_id;
            $notification['user_id'] = $eformObj->user_id;
            $notification['unit_no'] = $eformObj->unit_no;
            $notification['module'] = 'eform_move_in_out';
            $notification['ref_id'] = $eformObj->id;
            $notification['title'] = 'Move In & Out E-form';
            $notification['message'] = 'There is an update from the management in regards to inspection on your Move In & Out E-form';
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
                $message = "Move In & Out E-form Inspection";
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
                $return_url = 'opslogin/configuration/unit_summary/'.$eformObj->unit_no.'/5';
                return redirect($return_url)->with('status', 'Inspection details updated!');
            }
            else{
                return redirect('opslogin/eform/moveinout')->with('status', 'Inspection details updated!');    
            }
                 
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
        $eformObj = EformMovingInOut::find($id);
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
       
          //Start Insert into notification module
          $notification = array();
          $notification['account_id'] = $eformObj->account_id;
          $notification['user_id'] = $eformObj->user_id;
          $notification['unit_no'] = $eformObj->unit_no;
          $notification['module'] = 'eform_move_in_out';
          $notification['ref_id'] = $eformObj->id;
          $notification['title'] = 'Move In & Out E-form';
          $notification['message'] = 'There is an update from the management in regards to your submitted Move In & Out E-Form';
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
              $message = "Move In & Out E-form Updated";
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

        $title = "Your Moving In & Out application .".$status;
        InboxMessage::where('ref_id', $eformObj->id)->where('type',10)
                ->update(['title'=>$title,'booking_date'=>$eformObj->moving_start,'booking_time'=>$eformObj->moving_end,'event_status' => $status]);
        
        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$eformObj->unit_no.'/5';
            return redirect($return_url)->with('status', 'Moving In & Out application status has been updated!');
        }
        else{
            return redirect('opslogin/eform/moveinout')->with('status', 'Moving In & Out application status has been updated!');
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
        //
        $eformObj = EformMovingInOut::find($id);

        EformMovingSubCon::where('mov_id', $id)->delete();
        EformMovingPayment::where('mov_id', $id)->delete();
        EformMovingInspection::where('mov_id', $id)->delete();
        EformMovingDefect::where('mov_id', $id)->delete();
        EformMovingInOut::findOrFail($id)->delete();

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$eformObj->unit_no.'/5';
            return redirect($return_url)->with('status', 'Moving In & Out application status has been updated!');
        }
        else{
            return redirect('opslogin/eform/moveinout')->with('status', 'Record deleted successfully!');
        }
        
    }

  


   


    public function search(Request $request)
    {
        $ticket  =  $name = $status = $option = $unit ='';

        $account_id = Auth::user()->account_id;
        $option = $request->input('option'); 
        $visitor_app_url = env('VISITOR_APP_URL');

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
        $forms =  EformMovingInOut::where('account_id',$account_id)->where(function ($query) use ($ticket,$unit,$units,$userids,$name,$status) {
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
                $forms =  EformMovingInOut::where('eform_moving_in_out.account_id',$account_id)->join('users', 'users.id', '=', 'eform_moving_in_out.user_id')->select('eform_moving_in_out.*','users.name')->where('users.name', 'LIKE', '%'.$name .'%')
                    ->orderby('eform_moving_in_out.id','desc')->paginate(env('PAGINATION_ROWS'));
            }
            if($option == 'ticket') {
                $ticket = $request->input('ticket');
                $forms =  EformMovingInOut::where('account_id',$account_id)->where('ticket', 'LIKE', '%'.$ticket .'%')
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

                $forms =  EformMovingInOut::where('eform_moving_in_out.account_id',$account_id)
                    ->join('users', 'users.id', '=', 'eform_moving_in_out.user_id')->whereIn('users.unit_no', $units)
                    ->orderby('eform_moving_in_out.id','desc')->paginate(env('PAGINATION_ROWS'));
                
            }
            if($option == 'status') {
                $status = $request->input('status');
                if($request->input('status') =='a'){
                    $forms =  EformMovingInOut::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
                }
                else
                    {
                        
                        $forms =  EformMovingInOut::where('account_id',$account_id)->where('status', $status)
                    ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
                }
            }
           
        }*/
            return view('admin.eform_move.index', compact('forms','ticket','unit','name','status','option','visitor_app_url'));

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

       if(isset($input['return_url']))
           return redirect('opslogin/defect/new')->with('status', "Appointment Cancelled!!");
       else
           return redirect('opslogin/defect')->with('status', "Appointment Cancelled!!");
    }


}
