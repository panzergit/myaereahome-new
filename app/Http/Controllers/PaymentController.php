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
use App\Models\v7\UserNotification;
use App\Models\v7\UserNotificationSetting;
use App\Models\v7\UserLog;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\FacilityBookingTempRequest;



class PaymentController extends Controller
{

	public function charges(Request $request) {
		$payment_url = env('OMISEURL')."charges";
		$invoiceObj = FinanceInvoice::find($request->invoice_id);
		if(empty($invoiceObj)){
			return response()->json([
				'code' =>200,
				'msg'=>'Invalid invoice number!'
			]);
		}
		$propinfo = Property::where('id',$invoiceObj->account_id)->first();
		if($propinfo->opn_secret_key ==''){
			return response()->json([
				'data'=>null,
				'response' => 300,
				'status'=>'OPN Merchant Id not found!'
			]);
		}
		$sub_merchant_key = $propinfo->opn_secret_key;
		$username = $sub_merchant_key;
		$username = env('OMISEKEY');

		$password = '';
		$input = $request->all();
		$amount = $input['amount']*100;
		
        $fields = [
            "amount"            		=> $amount,
            "currency"          		=> 'SGD',
			"source[type]"    			=> 'paynow',
			"metadata[invoice_id]"    	=> $invoiceObj->id,
			"metadata[property]"    	=> $invoiceObj->account_id

        ];
		$fields_string = http_build_query($fields);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $payment_url);
        curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('SUB_MERCHANT_ID:team_5z7m4xovufke11twp0z'));
		curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        $result = curl_exec($ch);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        return $json;

	}

    public function TestWebhook(Request $request) {

		Log::info('After Parse Str');
		
		$response = $request->all();
		$remarks = '';
		$input =array();
		$values = isset($response['data'])?$response['data']:null;
		//Log::info($values);
		//Log::info($values['metadata']);
		//return response()->json(['status'=>200,'response' => 1, 'message' => 'Payment updated as paid!']);
		
		
		if(empty($values))
			return response()->json(['status'=>200,'response' => 101, 'message' => 'no data']);

		$module_type = isset($values['metadata']['type'])?$values['metadata']['type']:'';
		//Log::info('type :'.$module_type);

		if($module_type =='facility_booking'){ //facility payment
			Log::info("Facility Webhook");

			$booking_id = isset($values['metadata']['facility_booking_id'])?$values['metadata']['facility_booking_id']:'';
			$status = $values['status'];
			$authorized = $values['authorized'];
			$capturable = $values['capturable'];

			Log::info('type :'.$module_type);
			Log::info('booking_id :'.$booking_id);
			Log::info('status :'.$status);
			Log::info('authorized :'.$authorized);
			Log::info('capturable :'.$capturable);

			if($status =='successful' && $authorized ==1 ){
				FacilityBooking::where('id',$booking_id)->update(['card_last_digits'=>$values['card']['last_digits'],'card_brand'=>$values['card']['brand'],'payment_status'=>'2','amount_received_date'=>date("Y-m-d"),'status'=>2,'booking_charge_id'=>$values['id']]);
				$bookingObj = FacilityBooking::find($booking_id);
				FacilityBookingTempRequest::where('user_id', $bookingObj->account_id)->where('user_id', $bookingObj->user_id)->delete();
				$loginotp = new \App\Models\v7\LoginOTP();
				$otp = $loginotp->facilitybooking_confirmation($booking_id);
				return response()->json(['status'=>200,'response' => 1, 'message' => 'Payment updated as paid!']);
			}
			else if($status =='pending' && empty($authorized) && empty($capturable)){
				FacilityBooking::where('id',$booking_id)->update(['payment_status'=>'1']);
				return response()->json(['status'=>200,'response' => 1, 'message' => 'Payment updated as failed!']);
			}
			else{
				return response()->json(['status'=>200,'response' => 101, 'message' => 'Payment updated as failed!']);
			}

			Log::info('End');
		}
		else if($module_type =='facility_deposit'){ //facility payment
			Log::info("Facility Debosit Webhook");

			$booking_id = isset($values['metadata']['facility_booking_id'])?$values['metadata']['facility_booking_id']:'';
			$status = $values['status'];
			$authorized = $values['authorized'];
			$capturable = $values['capturable'];

			Log::info('type :'.$module_type);
			Log::info('booking_id :'.$booking_id);
			Log::info('status :'.$status);
			Log::info('authorized :'.$authorized);
			Log::info('capturable :'.$capturable);

			if($status =='pending' && $authorized ==1 && $capturable ==1){
				FacilityBooking::where('id',$booking_id)->update(['deposit_payment_status'=>'2','deposit_received_date'=>date("Y-m-d"),'deposit_charge_id'=>$values['id']]);
				$bookingObj = FacilityBooking::find($booking_id);
				//FacilityBookingTempRequest::where('user_id', $FacilityObj->account_id)->where('user_id', $FacilityObj->user_id)->delete();
				//Start Insert into notification module
				$notification = array();
				$notification['account_id'] = $bookingObj->account_id;
				$notification['unit_no'] = $bookingObj->unit_no;
				$notification['user_id'] = $bookingObj->user_id;
				$notification['module'] = 'facility';
				$notification['ref_id'] = $bookingObj->id;
				$notification['title'] = 'Facility Booking';
				$notification['message'] = 'Deposit fee update on your facility booking';
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
				   $message = "Facility Booking: Deposit Fee Collected";
				   
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
					if(count($ios_devices_to_send) >0){
						$ios_result =  $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
						//$ios_result = (isset($ios_result))?implode(",",$ios_result):'';
						Log::info($ios_result);
					}
					if(count($android_devices_to_send) >0){
						Log::info('Android Title :'.$title);
						Log::info('Android Body :'.$message);
						/*$additional_data = array();
						$additional_data['title'] = $title;
						$additional_data['body'] = $message;
						$additional_data['additional'] = $notofication_data;
						Log::info($additional_data);*/

						$android_result = $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
						//$android_result = (isset($android_result))?implode(",",$android_result):'';
						Log::info($android_result);
					}


				   //Log::info('capturable :'.$capturable);

				//End Insert into notification module
			   }
				//$loginotp = new \App\Models\v7\LoginOTP();
				//$otp = $loginotp->facilitydeposit_confirmation($booking_id);
				return response()->json(['status'=>200,'response' => 1, 'message' => 'Payment updated as paid!']);
			}
			else if($status =='pending' && empty($authorized) && empty($capturable)){
				FacilityBooking::where('id',$booking_id)->update(['payment_status'=>'1']);
				return response()->json(['status'=>200,'response' => 1, 'message' => 'Payment updated as failed!']);
			}
			else{
				return response()->json(['status'=>200,'response' => 101, 'message' => 'Payment updated as failed!']);
			}

			Log::info('End');
		}
		else { //invoice payment

			$input['invoice_id'] = isset($values['metadata']['id'])?$values['metadata']['id']:'';
			$status = isset($values['status'])?$values['status']:'';
			$transaction_id = isset($values['id'])?$values['id']:'';
			if($status=='successful')
				$input['status'] = 3;
			else if($status=='failed')
				$input['status'] = 4;

			$input['type'] = 2;
			$input['remarks'] = isset($values['failure_message'])?$values['failure_message']:'';
			$result = FinancePaymentLog::create($input); 
			if($input['status'] ==3){
				$id = $input['invoice_id'];
				$paid_at = isset($values['paid_at'])?date("Y-m-d",strtotime($values['paid_at'])):'';
				$amount = isset($values['amount'])?$values['amount']:'';
				$paid_amount = number_format(($amount /100),2);
				$payment['invoice_id'] = $id;
				$payment['payment_option'] = 5;
				$payment['transaction_id'] = $transaction_id;
				$payment['online_amount_received'] = $paid_amount;
				$payment['payment_received_date'] = $paid_at;
				$payment['created_at'] = date("Y-m-d H:i:s");
				$payment['updated_at'] = date("Y-m-d H:i:s");
				$paymentObj = FinanceInvoicePayment::create($payment);

				if(isset($paymentObj)){   
					$invoiceObj = FinanceInvoice::find($id);
					$paid_array = array();
					foreach($invoiceObj->paymentdetails as $key =>  $detail){
						$PaymentdetailData =array();
						$PaymentdetailData['account_id'] = $detail->account_id;
						$PaymentdetailData['unit_no'] = $invoiceObj->unit_no;
						$PaymentdetailData['invoice_id'] = $paymentObj->invoice_id;
						$PaymentdetailData['payment_id'] = $paymentObj->id;
						$PaymentdetailData['detail_id'] = $detail->id;
						$PaymentdetailData['type'] = $detail->reference_type;
						$PaymentdetailData['amount'] = $detail->balance;                        
						$PaymentdetailData['payment_received_date'] = $paymentObj->payment_received_date;
						$PaymentdetailData['created_at'] = date("Y-m-d H:i:s");
						$PaymentdetailData['updated_at'] = date("Y-m-d H:i:s");
						$paid_array[] = $PaymentdetailData;

						$paid_amount = $detail->balance;
						$detail_record = FinanceInvoicePaymentDetail::find($detail->id);
						if(isset($detail_record->paymenthistory)){
							foreach($detail_record->paymenthistory as $record){
								$paid_amount += $record->amount; 
							}
						}
						$detail_balance_amount = number_format(($detail_record->amount - $paid_amount),2);
						if($detail_balance_amount<=0)
							$payment_status = 2;
						else
							$payment_status = 3;
						FinanceInvoicePaymentDetail::where('id' , $detail->id)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$paymentObj->payment_received_date));
					}
					FinanceInvoicePaymentPaidDetail::insert($paid_array);
					$invoiceObj = FinanceInvoice::find($invoiceObj->id);
					$amount_received =0;
					if(isset($invoiceObj->payments)){
						foreach($invoiceObj->payments as $k => $payment){
							if($payment->payment_option ==1)
								$amount_received += $payment->cheque_amount; 
							else if($payment->payment_option ==2)
								$amount_received += $payment->bt_amount_received;
							else if($payment->payment_option ==5)
								$amount_received += $payment->online_amount_received;
							else
								$amount_received += $payment->cash_amount_received;
						}
					}
					$balance_amount = $invoiceObj->payable_amount - $amount_received;
					if($balance_amount <=0){
						if($balance_amount ==0)
							$balnce_type =1;
						else{
							$balnce_type =2;
							$balance_amount = 0- $balance_amount;
						}
						FinanceInvoice::where('id' , $id)->update( array('status' => 3,'balance_amount'=>$balance_amount,'balance_type'=>$balnce_type));
					}
					else{
						FinanceInvoice::where('id' , $id)->update( array( 'status' => 2,'balance_amount'=>$balance_amount));
					}
				}
				return response()->json(['response' => 1, 'message' => 'Payment updated as paid!']);
			}
			else{
				return response()->json(['response' => 101, 'message' => 'Payment updated as failed!']);
			}
		}

		
		
	}
	
	public function LiveWebhook(Request $request) {
		return response()->json(['data'=>$request,'response' => 1, 'message' => 'Live Success']);
		
	}
	
	
	
}
