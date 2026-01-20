<?php

namespace App\Http\Controllers;


use Session;
use Validator;
use App\Models\v7\Role;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\FacilityType;
use App\Models\v7\Property;
use App\Models\v7\FacilityBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Mail;
use Hash;
use DB;
use Log;


class OpnController extends Controller
{
    
	public function opn_account_creation(Request $request) {

		$rules=array(
			'user_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User id missing',
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
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($input['user_id']);
		$env_roles 	= env('USER_APP_ROLE');

		$userinfo = UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('status',1)->first();
		if(isset($userinfo) && isset($userinfo)){
			$payment_url = env('OMISEURL')."customers";
			$propinfo = Property::where('id',$UserObj->account_id)->first();
			//$username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
			$username = env('OMISEKEY');
			$password = '';
			$fields = [
				"description"           => "aerea customer (".$input['user_id'].") from ".$propinfo->company_name,
				"email"          		=> $userinfo->getuser->email,
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
			//print_r($result);
			$json = json_decode($result,true);
			if(isset($json['id']) && $json['id'] !=''){
				UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->update(['opn_id' => $json['id']]);
				$userinfo  = UserMoreInfo::where('user_id',$input['user_id'])->where('account_id',$UserObj->account_id)->where('status',1)->first();
					$userdata['userinfo'] = array();
					$userdata['userinfo']['id'] = isset($userinfo->id)?$userinfo->id:null;
					$userdata['userinfo']['last_name'] = isset($userinfo->last_name)?$userinfo->last_name:null;
					$userdata['userinfo']['profile_picture'] = isset($userinfo->profile_picture)?$userinfo->profile_picture:null;
					$userdata['userinfo']['phone'] =  isset($userinfo->phone)?$userinfo->phone:null;
					$userdata['userinfo']['mailing_address'] =  isset($userinfo->mailing_address)?$userinfo->mailing_address:null;
					$userdata['userinfo']['postal_code'] =  isset($userinfo->postal_code)?$userinfo->postal_code:null;
					$userdata['userinfo']['company_name'] =  isset($userinfo->company_name)?$userinfo->company_name:null;
					$userdata['userinfo']['face_picture'] =  isset($userinfo->face_picture)?$userinfo->face_picture:null;
					$userdata['userinfo']['status'] = isset($userinfo->status)?$userinfo->status:null;
					$userdata['userinfo']['deactivated_date'] = isset($userinfo->status)?$userinfo->deactivated_date:null;
					$userdata['userinfo']['opn_id'] = isset($userinfo->opn_id)?$userinfo->opn_id:null;

				return response()->json(['data'=>$userdata,'file_path'=>$file_path,'response' => 1, 'message' => 'success']);
			}else{
				return response()->json(['data'=>null,'file_path'=>$file_path,'response' => 200, 'message' => 'OPN record not created']);
			}
			
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'Record not found!'
			]);
		}

	}
	public function opn_facility_deposit_capture(Request $request) {
		$rules=array(
			'booking_id' => 'required',
			'charge_id' => 'required'
		);
		$messages=array(
			'booking_id.required' => 'Booking Id missing',
			'charge_id.required' => 'Charge Id missing'
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
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($input['user_id']);
		$env_roles 	= env('USER_APP_ROLE');
		$userinfo = UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('status',1)->first();
		if(isset($userinfo) && isset($userinfo)){
			echo $payment_url = env('OMISEURL')."charges/".$input['charge_id']."/capture";
			$propinfo = Property::where('id',$UserObj->account_id)->first();
			
			if($propinfo->opn_secret_key ==''){
				return response()->json([
					'data'=>null,
					'response' => 300,
					'status'=>'OPN Merchant Id not found!'
				]);
			}
			$cust_key = explode("_",$input['cust_opn_id']);

			if(in_array("test",$cust_key) && env('OPNMODE')=='live'){
				return response()->json([
					'data'=>null,
					'response' => 300,
					'status'=>'Customer found!'
				]);
			}
			else if(!in_array("test",$cust_key) && env('OPNMODE')=='test'){
				return response()->json([
					'data'=>null,
					'response' => 300,
					'status'=>'OPN Merchant Id not found!'
				]);
			}
			$sub_merchant_key = $propinfo->opn_secret_key;
			$username = env('OMISEKEY');
			$password = '';

			$fields = [
				"capture_amount"           	=> $input['amount'],
				"metadata[facility]"  		=> "facility_deposite",
				"metadata[user_id]"   		=> $input['user_id'],
				"metadata[property]"  		=> $input['account_id'],
				"metadata[customer]"  		=> $input['cust_opn_id'],
				"metadata[description]"		=> $input['description'],
				"metadata[charge_id]"			=> $input['charge_id'],
				"metadata[capture_amount]"	=> $input['amount'],

			];
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
			print_r($result);
			$json = json_decode($result,true);
			if(isset($json['id']) && $json['id'] !=''){

				return response()->json(['data'=>$json,'response' => 1, 'message' => 'success']);
			}else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Charge not created']);
			}
			
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'User not found!'
			]);
		}


	}
	public function opn_facility_deposit(Request $request) {
		$rules=array(
			'user_id' => 'required',
			'account_id' => 'required',
			'cust_opn_id' => 'required',
			'card_token' => 'required',
			'description' => 'required',
			'amount' => 'required',
			'facility_type'=> 'required',
		);
		$messages=array(
			'user_id.required' => 'User missing',
			'account_id.required' => 'Property missing',
			'cust_opn_id.required' => 'Customer OPN id code missing',
			'card_token.required' => 'Token missing',
			'description.required' => 'Description missing',
			'amount.required' => 'Amount missing',
			'facility_type.required' => 'Facility type missing'
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
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($input['user_id']);
		$env_roles 	= env('USER_APP_ROLE');

		$userinfo = UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('status',1)->first();
		if(isset($userinfo) && isset($userinfo)){
			$payment_url = env('OMISEURL')."charges";
			$propinfo = Property::where('id',$UserObj->account_id)->first();
			
			if($propinfo->opn_secret_key ==''){
				return response()->json([
					'data'=>null,
					'response' => 300,
					'status'=>'OPN Merchant Id not found!'
				]);
			}
			$cust_key = explode("_",$input['cust_opn_id']);

			if(in_array("test",$cust_key) && env('OPNMODE')=='live'){
				return response()->json([
					'data'=>null,
					'response' => 300,
					'status'=>'Customer found!'
				]);
			}
			else if(!in_array("test",$cust_key) && env('OPNMODE')=='test'){
				return response()->json([
					'data'=>null,
					'response' => 300,
					'status'=>'OPN Merchant Id not found!'
				]);
			}
			$sub_merchant_key = $propinfo->opn_secret_key;
			$username = env('OMISEKEY');

			$password = '';
			/*$fields = [
				"customer"				=> $input['cust_opn_id'],
				"card"       			=> $input['card_token'],
				"description"           => $input['description'],
				"amount"           		=> $input['amount'],
				"currency"         		=> 'SGD',
				"return_uri"           	=> env('APP_URL')."/opn_payment_status_update",
				"capture"           	=> 'false',
				"metadata['facility']"  => $input['facility_type'],
				"metadata['user_id']"   => $input['user_id'],
				"metadata['property']"  => $input['account_id'],
			];*/
			$fields = [
				"customer"       			=> $input['cust_opn_id'],
				"description"				=> $input['description'],
				"amount"           			=> $input['amount'],
				"currency"         			=> 'SGD',
				"capture"           		=> 'false',
				"authorization_type"    	=> 'pre_auth',
				"return_uri"           		=> env('APP_URL')."/payment/status",
				"metadata[facility]"  		=> $input['facility_type'],
				"metadata[user_id]"   		=> $input['user_id'],
				"metadata[property]"  		=> $input['account_id'],
				"metadata[customer]"  		=> $input['cust_opn_id'],
				"metadata[description]"		=> $input['description'],
				"metadata[token]"			=> $input['card_token'],
			];
			print_r($fields);

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
			print_r($result);
			$json = json_decode($result,true);
			if(isset($json['id']) && $json['id'] !=''){

				return response()->json(['data'=>$json,'response' => 1, 'message' => 'success']);
			}else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Charge not created']);
			}
			
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'User not found!'
			]);
		}

	}


	public function opn_create_charge(Request $request) {

		$rules=array(
			'user_id' => 'required',
			'account_id' => 'required',
			'cust_opn_id' => 'required',
			'card_token' => 'required',
			'description' => 'required',
			'amount' => 'required',
			'facility_type'=> 'required',
		);
		$messages=array(
			'user_id.required' => 'User missing',
			'account_id.required' => 'Property missing',
			'cust_opn_id.required' => 'Customer OPN id code missing',
			'card_token.required' => 'Token missing',
			'description.required' => 'Description missing',
			'amount.required' => 'Amount missing',
			'facility_type.required' => 'Facility type missing'
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
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($input['user_id']);
		$env_roles 	= env('USER_APP_ROLE');

		
		$userinfo = UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('status',1)->first();
		if(isset($userinfo) && isset($userinfo)){
			$payment_url = env('OMISEURL')."charges";
			$propinfo = Property::where('id',$UserObj->account_id)->first();
			
			if($propinfo->opn_secret_key ==''){
				return response()->json([
					'data'=>null,
					'response' => 300,
					'status'=>'OPN Merchant Id not found!'
				]);
			}
			$cust_key = explode("_",$input['cust_opn_id']);

			if(in_array("test",$cust_key) && env('OPNMODE')=='live'){
				return response()->json([
					'data'=>null,
					'response' => 300,
					'status'=>'Customer found!'
				]);
			}
			else if(!in_array("test",$cust_key) && env('OPNMODE')=='test'){
				return response()->json([
					'data'=>null,
					'response' => 300,
					'status'=>'OPN Merchant Id not found!'
				]);
			}
			$sub_merchant_key = $propinfo->opn_secret_key;
			$username = env('OMISEKEY');

			$password = '';
			/*$fields = [
				"customer"				=> $input['cust_opn_id'],
				"card"       			=> $input['card_token'],
				"description"           => $input['description'],
				"amount"           		=> $input['amount'],
				"currency"         		=> 'SGD',
				"return_uri"           	=> env('APP_URL')."/opn_payment_status_update",
				"capture"           	=> 'false',
				"metadata['facility']"  => $input['facility_type'],
				"metadata['user_id']"   => $input['user_id'],
				"metadata['property']"  => $input['account_id'],
			];*/
			$fields = [
				"customer"       			=> $input['cust_opn_id'],
				"description"				=> $input['description'],
				"amount"           			=> $input['amount'],
				"currency"         			=> 'SGD',
				"return_uri"           		=> env('OPNRETUNURI'),
				"metadata[facility]"  	=> $input['facility_type'],
				"metadata[user_id]"   	=> $input['user_id'],
				"metadata[property]"  	=> $input['account_id'],
				"metadata[customer]"  	=> $input['cust_opn_id'],
				"metadata[description]"	=> $input['description'],
				"metadata[token]"		=> $input['card_token'],
			];
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
			print_r($result);
			$json = json_decode($result,true);
			if(isset($json['id']) && $json['id'] !=''){

				return response()->json(['data'=>$json,'response' => 1, 'message' => 'success']);
			}else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Charge not created']);
			}
			
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'User not found!'
			]);
		}


	}

	public function non_3ds_create_charge(Request $request) {

		$rules=array(
			'user_id' => 'required',
			'account_id' => 'required',
			'cust_opn_id' => 'required',
			'card_token' => 'required',
			'description' => 'required',
			'amount' => 'required',
			'facility_type'=> 'required',
		);
		$messages=array(
			'user_id.required' => 'User missing',
			'account_id.required' => 'Property missing',
			'cust_opn_id.required' => 'Customer OPN id code missing',
			'card_token.required' => 'Token missing',
			'description.required' => 'Description missing',
			'amount.required' => 'Amount missing',
			'facility_type.required' => 'Facility type missing'
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
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($input['user_id']);
		$env_roles 	= env('USER_APP_ROLE');

		$record = FacilityBooking::find($input['booking_id']);
		if(empty($record) || $record->id ==''){
			return response()->json([
				'data'=>null,
				'response' => 300,
				'status'=>'Booking Id Id not found!'
			]);
		}


		$userinfo = UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('status',1)->first();
		if(isset($userinfo) && isset($userinfo)){
			$payment_url = env('OMISEURL')."charges";
			$propinfo = Property::where('id',$UserObj->account_id)->first();
			
			if($propinfo->opn_secret_key ==''){
				return response()->json([
					'data'=>null,
					'response' => 300,
					'status'=>'OPN Merchant Id not found!'
				]);
			}
			$sub_merchant_key = $propinfo->opn_secret_key;
			$username = env('OMISEKEY');

			$password = '';
			$fields = [
				"customer"				=> $input['cust_opn_id'],
				"description"           => $input['description'],
				"amount"           		=> $input['amount'],
				"currency"         		=> 'SGD',
				"authorization_type"    => 'pre_auth',
				"capture"           	=> 'false',
				"metadata[type]" 		=> 'facility_deposit',
				"metadata[facility_booking_id]" => $record->id,
				"metadata[user_id]"   	=> $input['user_id'],
				"metadata[property]"  	=> $input['account_id'],
				"metadata[customer]"  	=> $input['cust_opn_id'],
				"metadata[description]"	=> $input['description'],
				"metadata[token]"		=> $input['card_token'],
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
			//print_r($result);
			$json = json_decode($result,true);
			if(isset($json['id']) && $json['id'] !=''){

				return response()->json(['data'=>$json,'response' => 1, 'message' => 'success']);
			}else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'Charge not created']);
			}
			
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'User not found!'
			]);
		}


	}

	public function opn_retrive_customer_info(Request $request) {

		$rules=array(
			'user_id' => 'required',
			'user_opn_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User missing',
			'user_opn_id.required' => 'Customer OPN id code missing',
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
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($input['user_id']);
		$env_roles 	= env('USER_APP_ROLE');

		$userinfo = UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('status',1)->first();
		if(isset($userinfo) && isset($userinfo)){
			$payment_url = env('OMISEURL')."customers/".$input['user_opn_id'];
			$propinfo = Property::where('id',$UserObj->account_id)->first();
			//$username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
			$username = env('OMISEKEY');
			$password = '';
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $payment_url);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
			$result = curl_exec($ch);
			$json = json_decode($result,true);
			
			if(isset($json['id']) && $json['id'] !=''){
				return response()->json(['data'=>$json,'response' => 1, 'message' => 'success']);
			}else{
				return response()->json(['data'=>null,'response' => 200, 'message' => 'OPN Customer id not created']);
			}
			
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'User not found!'
			]);
		}


	}

	public function opn_create_token(Request $request) {

		$rules=array(
			'user_id' => 'required',
			'card_number' => 'required',
			'card_security_code' => 'required',
			'card_expiration_month' => 'required',
			'card_expiration_year' => 'required',
			'card_name' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User missing',
			'card_number.required' => 'Card number missing',
			'card_security_code.required' => 'Security code missing',
			'card_expiration_month.required' => 'Expiration month missing',
			'card_expiration_year.required' => 'Expiration year missing',
			'card_name.required' => 'Name missing'
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
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($input['user_id']);
		$env_roles 	= env('USER_APP_ROLE');

		$userinfo = UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('status',1)->first();
		if(isset($userinfo) && isset($userinfo)){
			$payment_url = env('VAULTURL')."tokens";
			$propinfo = Property::where('id',$UserObj->account_id)->first();
			//$username = ($propinfo->vault_secret_key !='')?$propinfo->vault_secret_key:env('VAULTKEY');
			if($propinfo->opn_secret_key ==''){
				return response()->json([
					'data'=>null,
					'response' => 300,
					'status'=>'OPN Merchant Id not found!'
				]);
			}
			$sub_merchant_key = $propinfo->opn_secret_key;
			$username = env('VAULTKEY');
			$password = '';
			$fields = [
				"card[expiration_month]"	=> $input['card_expiration_month'],
				"card[expiration_year]"     => $input['card_expiration_year'],
				"card[name]"           		=> $input['card_name'],
				"card[number]"           	=> $input['card_number'],
				"card[security_code]"       => $input['card_security_code'],
				"card[city]"           		=> (isset($input['card_city']))?$input['card_city']:'',
				"card[country]"           	=> (isset($input['card_country']))?$input['card_country']:'',
				"card[postal_code]"         => (isset($input['card_postal_code']))?$input['card_postal_code']:'',
				"card[state]"           	=> (isset($input['card_state']))?$input['card_state']:'',
				"card[phone_number]"        => (isset($input['card_phone_number']))?$input['card_phone_number']:'',
			];
			
			$fields_string = http_build_query($fields);
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $payment_url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("SUB_MERCHANT_ID:$sub_merchant_key,Content-type: application/x-www-form-urlencoded"));
			curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
			$result = curl_exec($ch);
			$json = json_decode($result,true);
			if(isset($json['id']) && $json['id'] !=''){
				return response()->json(['data'=>$json,'file_path'=>$file_path,'response' => 1, 'message' => 'success']);
			}else{
				return response()->json(['data'=>null,'file_path'=>$file_path,'response' => 200, 'message' => 'OPN record not created']);
			}
			
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'Record not found!'
			]);
		}


	}

	public function opn_attach_card_customer(Request $request) {

		$rules=array(
			'user_id' => 'required',
			'user_opn_id' => 'required',
			'card_opn_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User missing',
			'user_opn_id.required' => 'Customer OPN id  missing',
			'card_opn_id.required' => 'Card token id  missing',
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
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($input['user_id']);
		$env_roles 	= env('USER_APP_ROLE');

		$userinfo = UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('status',1)->first();
		if(isset($userinfo) && isset($userinfo)){
			$payment_url = env('OMISEURL')."customers/".$input['user_opn_id'];
			$propinfo = Property::where('id',$UserObj->account_id)->first();
			//$username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
			$username =env('OMISEKEY');
			$password = '';
			$fields = [
				"card"        => $input['card_opn_id'],
			];
			
			$fields_string = http_build_query($fields);
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $payment_url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
			$result = curl_exec($ch);
			$json = json_decode($result,true);
			//print_r($result);

			if(isset($json['id']) && $json['id'] !=''){
				return response()->json(['data'=>$json,'response' => 1, 'message' => 'success']);
			}else{
				return response()->json(['data'=>$result,'response' => 200, 'message' => 'Not assigned']);
			}
			
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'User not found!'
			]);
		}


	}

	public function opn_update_default_card(Request $request) {

		$rules=array(
			'user_id' => 'required',
			'user_opn_id' => 'required',
			'card_opn_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User missing',
			'user_opn_id.required' => 'Customer OPN id missing',
			'card_opn_id.required' => 'Card token id missing',
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
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($input['user_id']);
		$env_roles 	= env('USER_APP_ROLE');

		$userinfo = UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('status',1)->first();
		if(isset($userinfo) && isset($userinfo)){
			$payment_url = env('OMISEURL')."customers/".$input['user_opn_id'];
			$propinfo = Property::where('id',$UserObj->account_id)->first();
			//$username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
			$username =env('OMISEKEY');
			$password = '';
			$fields = [
				"default_card"        => $input['card_opn_id'],
			];
			
			$fields_string = http_build_query($fields);
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $payment_url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
			$result = curl_exec($ch);
			$json = json_decode($result,true);
			if(isset($json['id']) && $json['id'] !=''){
				return response()->json(['data'=>$json,'response' => 1, 'message' => 'success']);
			}else{
				return response()->json(['data'=>$result,'response' => 200, 'message' => 'Not updated']);
			}
			
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'User not found!'
			]);
		}


	}

	public function opn_destroy_card(Request $request) {

		$rules=array(
			'user_id' => 'required',
			'user_opn_id' => 'required',
			'card_opn_id' => 'required',
		);
		$messages=array(
			'user_id.required' => 'User missing',
			'user_opn_id.required' => 'Customer OPN id  missing',
			'card_opn_id.required' => 'Card OPN id missing',
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
		$file_path = env('APP_URL')."/storage/app";
		$UserObj = User::find($input['user_id']);
		$env_roles 	= env('USER_APP_ROLE');

		$userinfo = UserMoreInfo::where('account_id',$UserObj->account_id)->where('user_id',$input['user_id'])->where('status',1)->first();
		if(isset($userinfo) && isset($userinfo)){
			$payment_url = env('OMISEURL')."customers/".$input['user_opn_id']."/cards/".$input['card_opn_id'];
			$propinfo = Property::where('id',$UserObj->account_id)->first();
			//$username = ($propinfo->opn_secret_key !='')?$propinfo->opn_secret_key:env('OMISEKEY');
			$username =env('OMISEKEY');
			$password = '';
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $payment_url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $username . ":".$password);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
			$result = curl_exec($ch);
			$json = json_decode($result,true);
			
			if(isset($json['id']) && $json['id'] !=''){
				return response()->json(['data'=>$json,'response' => 1, 'message' => 'success']);
			}else{
				return response()->json(['data'=>result,'response' => 200, 'message' => 'Not Destroyed']);
			}
			
		}else{
			return response()->json([
				'data'=>null,
				'response' => 200,
				'file_path'=>$file_path,
				'status'=>'User not found!'
			]);
		}


		
	}
	public function opn_payment_webhook(Request $request) {
		Log::info('OPN UPDATE from END PINT WebHook --START--');
		$json = json_decode($request,true); 
		Log::info(print_r($json)); 
		Log::info('OPN UPDATE from END PINT --END--'); 
		return response()->json([
			'data'=>null,
			'response' => 200,
			'status'=>'User not found!'
		]);
	}

	public function opn_payment_status_update(Request $request) {
		Log::info('OPN UPDATE from END PINT --START--');
		$json = json_decode($request,true); 
		Log::info(print_r($json)); 
		Log::info('OPN UPDATE from END PINT --END--'); 
	}
}
