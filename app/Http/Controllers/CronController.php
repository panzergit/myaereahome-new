<?php

namespace App\Http\Controllers;


use Session;
use Validator;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Mail;
use Hash;
use DB;

use Illuminate\Support\Facades\Log;
use App\Models\v7\FinanceShareSetting;
use App\Models\v7\FinanceInvoice;
use App\Models\v7\FinanceInvoiceInfo;
use App\Models\v7\FinanceInvoiceDetail;
use App\Models\v7\FinanceInvoicePayment;
use App\Models\v7\FinanceReferenceType;
use App\Models\v7\FinanceInvoicePaymentDetail;
use App\Models\v7\FinanceInvoicePaymentPaidDetail;
use App\Models\v7\FinancePaymentLog;
use App\Models\v7\FacilityBooking;
use App\Models\v7\Property;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\Announcement;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\AnnouncementDetail;
use App\Models\v7\UserNotificationSetting;
use App\Models\v7\InboxMessage;
use App\Models\v7\UserLog;


class CronController extends Controller
{

	public function facility_deposit_charges(Request $request) {
		$cron_date = date('Y-m-d', strtotime(Carbon::now()->addDays(3)));
		$booking_records = FacilityBooking::where('status',2)->where('payment_status',2)->where('deposit_payment_status',0)->where('booking_date','<=',$cron_date)->get();
		
		if(isset($booking_records)){
			foreach($booking_records as $booking_record){
				$facility_booking_id = $booking_record->id;
				$record = FacilityBooking::find($facility_booking_id);
				if(isset($record->opn_charge_id) && $record->opn_charge_id !=''){
					$userinfo = UserMoreInfo::where('account_id',$record->account_id)->where('user_id',$record->user_id)->where('status',1)->first();
					if(isset($userinfo) && isset($userinfo)){
						$payment_url = env('OMISEURL')."charges";
						$propinfo = Property::where('id',$userinfo->account_id)->first();
						$sub_merchant_key = $propinfo->opn_secret_key;
						$username = env('OMISEKEY');
						$password = '';
						echo $record->deposit_fee;
						if($record->deposit_fee >0){
							$depost_amount = $record->deposit_fee *100;
							$fields = [
								"customer"				=> $userinfo->opn_id,
								"description"           => "Deposit Collection : ".$record->id,
								"amount"           		=> $depost_amount,
								"currency"         		=> 'SGD',
								"authorization_type"    => 'pre_auth',
								"capture"           	=> 'false',
								"metadata[type]" 		=> 'facility_deposit',
								"metadata[facility_booking_id]" => $record->id,
								"metadata[user_id]"   	=> $userinfo->user_id,
								"metadata[property]"  	=> $userinfo->account_id,
								"metadata[customer]"  	=> $userinfo->opn_id,
								"metadata[description]"	=> "deposit collection",
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
							print_r($result);
							$json = json_decode($result,true);
						}

					}
				}

			}
		}
	}

	public function facility_pre_auth_charges(Request $request) {

		$input = $request->all();
		$cron_date = date('Y-m-d', strtotime(Carbon::now()->subDays(12)));
		
		$booking_records = FacilityBooking::where('status',2)->where('payment_status',2)->where('refund_status',0)->where('amount_received_date',$cron_date)->where('booking_date','>=',$cron_date)->get();
		
		if(isset($booking_records) && count($booking_records) >0){
			foreach($booking_records as $booking_record){
				$facility_booking_id = $booking_record->id;
				$record = FacilityBooking::find($facility_booking_id);
				if(isset($record->opn_charge_id) && $record->opn_charge_id !=''){
					$userinfo = UserMoreInfo::where('account_id',$record->account_id)->where('user_id',$record->user_id)->where('status',1)->first();
					if(isset($userinfo) && isset($userinfo)){
						//Reverse payment start
						
						$propinfo = Property::where('id',$record->account_id)->first();
						//$username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
						$username =env('OMISEKEY');
						$password = '';
						$ch = curl_init();

						$reverse_url = env('OMISEURL')."charges/".$record->opn_charge_id."/reverse";
						curl_setopt($ch,CURLOPT_URL, $reverse_url);
						curl_setopt($ch,CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
						curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
						curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
						$result = curl_exec($ch);
						$reverse_data = json_decode($result,true);
						if(isset($reverse_data['id']) && $reverse_data['id'] !=''){
							FacilityBooking::where('id',$facility_booking_id)->update(['payment_status'=>'4']);

						}

						//Reverse Payment End

						//Create charge payment start
						$payment_url = env('OMISEURL')."charges";
						$amount = ($record->booking_fee + $record->deposit_fee)*100;
						$fields = [
							"customer"				=> $userinfo->opn_id,
							"card"       			=>	$record->opn_card_id,
							"description"           => "pre auth cron job run",
							"amount"           		=> $amount,
							"currency"         		=> 'SGD',
							"capture"           	=> 'false',
							"authorization_type"    => 'pre_auth',
							"metadata[facility_booking_id]"  => $record->id,
							"metadata[type]"  => 'facility booking',
							"metadata[facility]"  => $record->type_id,
							"metadata[user_id]"   => $record->user_id,
							"metadata[property]"  => $record->account_id,
							"metadata[method]"  => 'cron',
						];
						//print_r($fields);
						$fields_string = http_build_query($fields);
						$headers =array();
						//$headers[] = 'Content-type: application/x-www-form-urlencoded';
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
							$booking_id = isset($json['metadata']['facility_booking_id'])?$json['metadata']['facility_booking_id']:'';
							$status = $json['status'];
							$authorized = $json['authorized'];
							$capturable = $json['capturable'];

							$payment_status = 2;
							if($status =='pending' && $authorized ==1 && $capturable ==1){
								FacilityBooking::where('id',$booking_id)->update(['opn_charge_id' => $json['id'],'opn_card_id' => $json['card']['id'],'payment_status'=>'2','amount_received_date'=>date("Y-m-d")]);
								return response()->json(['status'=>200,'response' => 1, 'message' => 'Payment updated as paid!']);
							}
							else if($status =='pending' && empty($authorized) && empty($capturable)){
								FacilityBooking::where('id',$booking_id)->update(['payment_status'=>'1']);
								return response()->json(['status'=>200,'response' => 1, 'message' => 'Payment updated as paid!']);
							}
							
						}
					}
				}
			}	
		}
		else{
			return response()->json(['response' => 200, 'message' => '
			No records']);
		}

	}

	public function facility_refund_charges(Request $request) {

		$input = $request->all();
		$cron_date = date('Y-m-d', strtotime(Carbon::now()->subDays(12)));
		
		$booking_records = FacilityBooking::where('status',2)->where('payment_status',2)->where('refund_status',0)->where('amount_received_date',$cron_date)->where('booking_date','<',$cron_date)->get();
		//print_r($booking_records);
		if(isset($booking_records) && count($booking_records) >0){
			echo "here";
			foreach($booking_records as $booking_record){
				$facility_booking_id = $booking_record->id;
				$recordObj = FacilityBooking::find($facility_booking_id);
				$refund_amount = $recordObj->deposit_fee;

				if(isset($recordObj->opn_charge_id) && $recordObj->opn_charge_id !=''){
						$payment_url = env('OMISEURL')."charges/".$recordObj->opn_charge_id."/capture";
						$propinfo = Property::where('id',$recordObj->account_id)->first();
						//$username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
						$username =env('OMISEKEY');
						$password = '';
						$opn_capture_amount = 0.00;
						$fields = [
								"capture_amount" => $opn_capture_amount,
							];
							
						$fields_string = http_build_query($fields);
						$ch = curl_init();
						curl_setopt($ch,CURLOPT_URL, $payment_url);
						curl_setopt($ch,CURLOPT_POST, true);
						curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
						curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
						curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
						$result = curl_exec($ch);
						$json = json_decode($result,true);
						if(isset($json['id']) && $json['id'] !=''){
							
							$refund_status = ($refund_amount ==$recordObj->deposit_fee)?1:2;
							$facility_update_qry =FacilityBooking::where('id',$booking_id)->update(['capture_amount' => 0.00,'refund_amount'=>$refund_amount,'refund_status' => $refund_status]);

							$loginotp = new \App\Models\v7\LoginOTP();
							$otp = $loginotp->facilitybooking_refunded($booking_id);	
		
							return response()->json(['response' => 1, 'message' => 'success']);
						}else{
							return response()->json(['response' => 200, 'message' => 'Charge not created']);
						}
				}
				else{
					return response()->json(['response' => 200, 'message' => 'No approved transactions']);
				}
			}
			
		}
		else{
			return response()->json(['response' => 200, 'message' => 'No records']);
		}

	}

	public function announcement_send(Request $request) {
		$records = AnnouncementDetail::where('notification_status',0)->orderby('id','asc')->limit(50)->get();
        if(count($records) >0){
			foreach($records as $record){
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();
				$user = User::find($record->user_id);
				
				$announcement = Announcement::find($record->a_id);

				if(isset($user) && !empty($announcement)){
					$inbox['account_id'] = $record->account_id;
					$inbox['unit_no'] = $record->unit_no;
					$inbox['user_id'] = $record->user_id;
					$inbox['type'] = 1;
					$inbox['ref_id'] = $record->id;
					$inbox['title'] = $announcement->title;
					$inbox['message'] = $announcement->notes;
					$inbox['status'] =  0; 
					$inbox['view_status'] =  0;   
					$inbox['created_at'] =  $announcement->created_at;     
					$inboxs[] = $inbox;
					$email = $user->email;
					InboxMessage::insert($inboxs);

					//Start Insert into notification module
					$notification = array();
					$notification['account_id'] = $record->account_id;
					$notification['user_id'] = $user->id;
					$notification['unit_no'] = $record->unit_id;
					$notification['module'] = 'announcement';
					$notification['ref_id'] = $record->id;
					$notification['title'] = $announcement->title;
					$notification['message'] = $announcement->notes;
					UserNotification::insert($notification);
					//End Insert into notification module
					$SettingsObj = UserNotificationSetting::where('user_id',$record->user_id)->where('account_id',$record->account_id)->first();
					if(empty($SettingsObj) || $SettingsObj->announcement ==1){
						//Firebase Notification - Preparing ids START
						$logs = UserLog::where('user_id',$user->id)->where('status',1)->orderby('id','desc')->first();
						if(isset($logs->fcm_token) && $logs->fcm_token !=''){
							$user_token .=$logs->fcm_token.",";
							$fcm_token_array .=$logs->fcm_token.',';
							$appSipAccountList[] = $user->id;
							if($logs->login_from ==1)
								$ios_devices_to_send[] = $logs->fcm_token;
							if($logs->login_from ==2)
								$android_devices_to_send[] = $logs->fcm_token;
						}
						//echo $logs->fcm_token;
						//Firebase Notification  - Preparing ids END
						//Push notification to Mobile app for IOS
						$probObj = Property::find($record->account_id);
						$title = "Aerea Home - ".$probObj->company_name;
						$message = "New Announcement";
						$notofication_data = array();
						$notofication_data['body'] =$announcement->title;
						$notofication_data['unit_no'] =$record->unit_no;   
						$notofication_data['user_id'] =$record->user_id;   
						$notofication_data['property'] =$record->account_id;
						$purObj = UserPurchaserUnit::where('property_id',$record->account_id)->where('unit_id',$record->unit_id)->where('user_id',$record->user_id)->first(); 
						if(isset($purObj))
							$notofication_data['switch_id'] =$record->id;
							
						$NotificationObj = new \App\Models\v7\FirebaseNotification();
						$NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
						$NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
					}	

					/*$subject = 'Aerea : '.$announcement->title;                        
					if(env('MAIL_SEND')== 1){
						$admin = Setting::findOrFail(1);
						//Mail::to($admin->company_email)->cc($emails)->send(new AnnouncementNotification($announcement,$subject,'Admin'));
						Mail::to($email)->send(new AnnouncementNotification($announcement,$subject,'Admin'));
					}*/
	
				}
				AnnouncementDetail::where('id',$record->id)->update(['notification_status'=>'1']);
			}
		}
	}
}
