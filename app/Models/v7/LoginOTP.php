<?php

namespace App\Models\v7;

use Illuminate\Support\Facades\Crypt;
use Log, Mail, Throwable;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use App\Mail\OTPEmail;
use Illuminate\Database\Eloquent\Model;
use App\Services\PHPMailerService;

class LoginOTP extends Model
{

    public function sendotp($customername = '', $email = '', $from ='') {
		$otp = rand(00000, 99999);					
		
		if($from ==2)
			$source ='Home';
		else if($from ==3)
			$source ='Manager';
		else 
			$source ='OPS+Portal';
		
		$user = User::where('email', $email)->update(['otp' => $otp]);	
		$userObj = User::where('email', $email)->first();
		//echo $userObj->propertyinfo->otp_option;
		$customername = Crypt::decryptString($customername);
		if(isset($userObj->propertyinfo->otp_option) && $userObj->propertyinfo->otp_option ==2){
			//$userObj = User::where('email', $email)->first();
			$phone_validation = 0;
			$username 	= env('SMS_USER');
			$password 	= env('SMS_PASS');

			$special_char = array("+", " ", "(", ")","-","_");
			$phone_number = '';

			$user_roles = explode(",",env('USER_APP_ROLE'));

			if(in_array($userObj->role_id,$user_roles)){
				$usermoreinfo = UserMoreInfo::where('status',1)->where('account_id',$userObj->propertyinfo->id)->where('user_id',$userObj->id)->orderby('id','desc')->first();
				$phonenumber = Crypt::decryptString($usermoreinfo->phone);
			}
			else
				$phonenumber = Crypt::decryptString($userObj->empinfo->phone);

			if(isset($phonenumber))
				$phone_number 	= str_replace($special_char,"",$phonenumber);

			if(isset($userObj->propertyinfo->sms_username) && $userObj->propertyinfo->sms_username !='')
				$username 	= $userObj->propertyinfo->sms_username;
			if(isset($userObj->propertyinfo->sms_password) && $userObj->propertyinfo->sms_password !='')
				$password 	= $userObj->propertyinfo->sms_password;

			//echo $phone_number;
			//echo strlen($phone_number);
			if(strlen($phone_number) >=10){
				
				$to_number 	= "%2b".$phone_number ;
				//$to_number 	= str_replace(" ","",$to_number) ;
				$phone_validation = 1;
			}else if(strlen($phone_number) ==8){
				
				$to_number 	= "65".$phone_number;
				$phone_validation = 1;
			}else{
				return response()->json([
					'message' => "phone number not valid",
				], 400);
			}
			//echo "phonr :".$to_number;
			if($phone_validation ==1){
				
				$message 	= "Welcome+to+Aerea+".$source.":+Your+account+verification+code+is+".$otp;
				$sender 	= "Aerea";

			$sentotpotp = "https://mx.fortdigital.net/http/send-message?username=".$username."&password=".$password."&to=".$to_number."&from=".$sender."&message=".$message;
			//Log::info($sentotpotp);
			
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $sentotpotp,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "get",
					CURLOPT_POSTFIELDS => "",
					CURLOPT_SSL_VERIFYHOST => 0,
					CURLOPT_SSL_VERIFYPEER => 0,
				));
				$response = curl_exec($curl);
				//echo $to_number;
				
				$err = curl_error($curl);
				//print_r($response);
				//exit;
				curl_close($curl);
			}


		}
		else{
		
			/*$logo = url('/').'/public/assets/admin/img/aerea-logo.png';		
					
			$companyname = 'Aerea Home';
			$adminemail = 'otp@myaereahome.com';
			$replyto = 'no-reply@myaereahome.com';
			
			$emailcontent = file_get_contents(public_path().'/emails/verifyotp.php');
			$emailcontent = str_replace('#logo#', $logo, $emailcontent);
			$emailcontent = str_replace('#companyname#', $companyname, $emailcontent);
			$emailcontent = str_replace('#otp#', $otp, $emailcontent);
			$emailcontent = str_replace('#customername#', $customername, $emailcontent);
			
			$subject = $companyname.' Login OTP Code';
			
			$headers = 'From: '.$companyname.' <'.$adminemail.'/> ' . "\r\n" ;
			$headers .='Reply-To: '. $replyto . "\r\n" ;
			//$headers .='X-Mailer: PHP/' . phpversion();
			$headers = "MIME-Version: 1.0" . "\r\n"; 
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
			//		echo $headers ;
			$email = @mail($email, $subject, $emailcontent, $headers);
			/*if($email) {
				echo "<p>Mail Sent.</p>"; 
			  }
			  else {
				echo "<p>Mail Fault.</p>"; 
			  }
			  exit;*/
			//echo "here";

			$companyname = 'Aerea '.$source;

			try{
				Mail::to($email)->send(new OTPEmail([
					'subject' => $companyname.' Login OTP Code',
					'otp' => $otp,
					'companyname' => $companyname,
					'customername' => $customername
				]));				
			} catch (Exception $e) {
				Log::error('Email sending failed: ' . $e->getMessage());
			}
			
		}

		return $otp;
	}

	public static function sendotpnew($customername = '', $email = '', $from ='')
	{
		$otp = rand(00000, 99999);
		$source ='OPS+Portal';
		$otpappcode = '';

		if($from ==2)
		{
			$source ='Home';
			$otpappcode = "+-+".env('HOMEOTPCODE');
		}

		if($from ==3)
		{
			$source ='Manager';
			$otpappcode = "+-+".env('MANAGEROTPCODE');
		}
		
		User::where('email', $email)->update(['otp' => $otp]);	
		$userObj = User::where('email', $email)->first();
		$customername = Crypt::decryptString($customername);

		$otp_option = 1;
		if($from ==2 ){
			if(isset($userObj->propertyinfo->otp_option) && $userObj->propertyinfo->otp_option ==2)
				$otp_option =2;
		}else{
			if(isset($userObj->propertyinfo->manager_otp_option) && $userObj->propertyinfo->manager_otp_option ==2)
				$otp_option =2;
		}
		
		if($otp_option==2)
		{
			$phone_validation = 0;
			$username = env('SMS_USER');
			$password = env('SMS_PASS');

			$special_char = ["+", " ", "(", ")","-","_"];
			$phone_number = '';

			$user_roles = explode(",",env('USER_APP_ROLE'));

			if(in_array($userObj->role_id,$user_roles)){
				$usermoreinfo = UserMoreInfo::where('status',1)->where('account_id',$userObj->propertyinfo->id)->where('user_id',$userObj->id)->orderby('id','desc')->first();
				$phonenumber = Crypt::decryptString($usermoreinfo->phone);
			}
			else
				$phonenumber = Crypt::decryptString($userObj->empinfo->phone);

			if(isset($phonenumber))
				$phone_number 	= str_replace($special_char,"",$phonenumber);

			if(isset($userObj->propertyinfo->sms_username) && $userObj->propertyinfo->sms_username !='')
				$username 	= $userObj->propertyinfo->sms_username;
			if(isset($userObj->propertyinfo->sms_password) && $userObj->propertyinfo->sms_password !='')
				$password 	= $userObj->propertyinfo->sms_password;

			if(strlen($phone_number) >=10)
			{
				$to_number 	= "%2b".$phone_number ;
				$phone_validation = 1;
			}else if(strlen($phone_number) ==8)
			{	
				$to_number 	= "65".$phone_number;
				$phone_validation = 1;
			}else{
				return response()->json([
					'message' => "phone number not valid",
				], 400);
			}

			if($phone_validation ==1)
			{
				$message 	= "Welcome+to+Aerea+".$source.":+Your+account+verification+code+is+".$otp.$otpappcode;
				$sender 	= "Aerea";
				$sentotpotp = "https://mx.fortdigital.net/http/send-message?username=".$username."&password=".$password."&to=".$to_number."&from=".$sender."&message=".$message;
			
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $sentotpotp,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "get",
					CURLOPT_POSTFIELDS => "",
					CURLOPT_SSL_VERIFYHOST => 0,
					CURLOPT_SSL_VERIFYPEER => 0,
				));
				$response = curl_exec($curl);				
				$err = curl_error($curl);
				curl_close($curl);
			}
		}else{
			$source = str_replace("+"," ",$source);
			$companyname = 'Aerea '.$source;
			try{
    			Mail::to($email)->send(new OTPEmail([
    				'subject' => $companyname.' Login OTP Code',
    				'otp' => $otp,
    				'companyname' => $companyname,
    				'customername' => $customername
    			]));
			} catch (Throwable $e) {
                Log::error('Mail failed', [
                    'error' => $e->getMessage(),
                    'email' => $email,
                ]);
			}
		}
		return $otp;
	}

	public function resendotp($customername = '', $email = '',$from ='') {
		$otp = rand(00000, 99999);					
		if($from ==2)
			$source ='Home';
		else if($from ==3)
			$source ='Manager';
		else 
			$source ='OPS+Portal';
		
		$user = User::where('email', $email)->update(['otp' => $otp]);	
		$userObj = User::where('email', $email)->first();
		//echo $userObj->propertyinfo->otp_option;
		$customername = Crypt::decryptString($customername);
		if(isset($userObj->propertyinfo->otp_option) && $userObj->propertyinfo->otp_option ==2){
			//$userObj = User::where('email', $email)->first();
			$phone_validation = 0;
			$username 	= env('SMS_USER');
			$password 	= env('SMS_PASS');

			$special_char = array("+", " ", "(", ")","-","_");
			$phone_number = '';

			$user_roles = explode(",",env('USER_APP_ROLE'));

			if(in_array($userObj->role_id,$user_roles)){
				$usermoreinfo = UserMoreInfo::where('status',1)->where('account_id',$userObj->propertyinfo->id)->where('user_id',$userObj->id)->orderby('id','desc')->first();
				$phonenumber = Crypt::decryptString($usermoreinfo->phone);
			}
			else
				$phonenumber = Crypt::decryptString($userObj->empinfo->phone);

			if(isset($phonenumber))
				$phone_number 	= str_replace($special_char,"",$phonenumber);

			if(isset($userObj->propertyinfo->sms_username) && $userObj->propertyinfo->sms_username !='')
				$username 	= $userObj->propertyinfo->sms_username;
			if(isset($userObj->propertyinfo->sms_password) && $userObj->propertyinfo->sms_password !='')
				$password 	= $userObj->propertyinfo->sms_password;

			//echo $phone_number;
			//echo strlen($phone_number);
			if(strlen($phone_number) >=10){
				
				$to_number 	= "%2b".$phone_number ;
				//$to_number 	= str_replace(" ","",$to_number) ;
				$phone_validation = 1;
			}else if(strlen($phone_number) ==8){
				
				$to_number 	= "65".$phone_number;
				$phone_validation = 1;
			}else{
				return response()->json([
					'message' => "phone number not valid",
				], 400);
			}
			//echo "phonr :".$to_number;
			if($phone_validation ==1){
				
				$message 	= "Welcome+to+Aerea+".$source.":+Your+account+verification+code+is+".$otp;
				$sender 	= "Aerea";

			$sentotpotp = "https://mx.fortdigital.net/http/send-message?username=".$username."&password=".$password."&to=".$to_number."&from=".$sender."&message=".$message;
			//Log::info($sentotpotp);
			
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $sentotpotp,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "get",
					CURLOPT_POSTFIELDS => "",
					CURLOPT_SSL_VERIFYHOST => 0,
					CURLOPT_SSL_VERIFYPEER => 0,
				));

				$response = curl_exec($curl);
				//echo $to_number;
				//print_r($response);

				$err = curl_error($curl);

				curl_close($curl);
			}


		}
		else{
		
			$logo = url('/').'/public/assets/admin/img/aerea-logo.png';		
			$companyname = 'Aerea '.$source;
	
			try{
				Mail::to($email)->send(new OTPEmail([
					'subject' => $companyname.' Login OTP Code',
					'otp' => $otp,
					'companyname' => $companyname,
					'customername' => $customername
				]));				
			} catch (Exception $e) {
				Log::error('Email sending failed: ' . $e->getMessage());
			}
		}

		return $otp;
	}
	public static function resendotpnew($customername = '', $email = '',$from ='') {
		$otp = rand(00000, 99999);					
		if($from ==2){
			$source ='Home';
			$otpappcode = "+-+".env('HOMEOTPCODE');
		}
		else if($from ==3){
			$source ='Manager';
			$otpappcode = "+-+".env('MANAGEROTPCODE');
		}
		else {
			$source ='OPS+Portal';
			$otpappcode = '';
		}
		
		$user = User::where('email', $email)->update(['otp' => $otp]);	
		$userObj = User::where('email', $email)->first();
		//echo $userObj->propertyinfo->otp_option;
		$customername = Crypt::decryptString($customername);
		if(isset($userObj->propertyinfo->otp_option) && $userObj->propertyinfo->otp_option ==2){
			//$userObj = User::where('email', $email)->first();
			$phone_validation = 0;
			$username 	= env('SMS_USER');
			$password 	= env('SMS_PASS');

			$special_char = array("+", " ", "(", ")","-","_");
			$phone_number = '';

			$user_roles = explode(",",env('USER_APP_ROLE'));

			if(in_array($userObj->role_id,$user_roles)){
				$usermoreinfo = UserMoreInfo::where('status',1)->where('account_id',$userObj->propertyinfo->id)->where('user_id',$userObj->id)->orderby('id','desc')->first();
				$phonenumber = Crypt::decryptString($usermoreinfo->phone);
			}
			else
				$phonenumber = Crypt::decryptString($userObj->empinfo->phone);

			if(isset($phonenumber))
				$phone_number 	= str_replace($special_char,"",$phonenumber);

			if(isset($userObj->propertyinfo->sms_username) && $userObj->propertyinfo->sms_username !='')
				$username 	= $userObj->propertyinfo->sms_username;
			if(isset($userObj->propertyinfo->sms_password) && $userObj->propertyinfo->sms_password !='')
				$password 	= $userObj->propertyinfo->sms_password;

			//echo $phone_number;
			//echo strlen($phone_number);
			if(strlen($phone_number) >=10){
				
				$to_number 	= "%2b".$phone_number ;
				//$to_number 	= str_replace(" ","",$to_number) ;
				$phone_validation = 1;
			}else if(strlen($phone_number) ==8){
				
				$to_number 	= "65".$phone_number;
				$phone_validation = 1;
			}else{
				return response()->json([
					'message' => "phone number not valid",
				], 400);
			}
			//echo "phonr :".$to_number;
			if($phone_validation ==1){
				
				$message 	= "Welcome+to+Aerea+".$source.":+Your+account+verification+code+is+".$otp.$otpappcode;
				$sender 	= "Aerea";

			$sentotpotp = "https://mx.fortdigital.net/http/send-message?username=".$username."&password=".$password."&to=".$to_number."&from=".$sender."&message=".$message;
			//Log::info($sentotpotp);
			
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $sentotpotp,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "get",
					CURLOPT_POSTFIELDS => "",
					CURLOPT_SSL_VERIFYHOST => 0,
					CURLOPT_SSL_VERIFYPEER => 0,
				));

				$response = curl_exec($curl);
				//echo $to_number;
				//print_r($response);

				$err = curl_error($curl);

				curl_close($curl);
			}


		}
		else{
		
			$source = str_replace("+"," ",$source);
			$companyname = 'Aerea '.$source;
			$service = app(PHPMailerService::class);
			$returnMailData = $service->sendMail(
                trim($email),
                $companyname.' Login OTP Code',
                'emails.verifyotp', 
                [
                    'otp' => $otp,
					'companyname' => $companyname,
					'customername' => $customername
                ]

            );
		}

		return $otp;
	}

	public function forgotpwdotp($customername = '', $email = '',$from ='') {
		$otp = rand(00000, 99999);					
		if($from ==2)
			$source ='Home';
		else if($from ==3)
			$source ='Manager';
		else 
			$source ='OPS Portal';
		User::where('email', $email)->update(['otp' => $otp]);	
		$customername = Crypt::decryptString($customername);
		$userObj = User::where('email', $email)->first();
		$special_char = array("+", " ", "(", ")","-","_");

		$user_roles = explode(",",env('USER_APP_ROLE'));
			if(in_array($userObj->role_id,$user_roles))
				$phonenumber = Crypt::decryptString($userObj->userinfo->phone);
			else
				$phonenumber = Crypt::decryptString($userObj->empinfo->phone);

			if(isset($phonenumber))
				$phone_number 	= str_replace($special_char,"",$phonenumber);
		
		
		if(isset($userObj->propertyinfo->otp_option) && $userObj->propertyinfo->otp_option ==2){
			//$userObj = User::where('email', $email)->first();
			$phone_validation = 0;
			$username 	= env('SMS_USER');
			$password 	= env('SMS_PASS');
			if(isset($userObj->propertyinfo->sms_username) && $userObj->propertyinfo->sms_username !='')
				$username 	= $userObj->propertyinfo->sms_username;
			if(isset($userObj->propertyinfo->sms_password) && $userObj->propertyinfo->sms_password !='')
				$password 	= $userObj->propertyinfo->sms_password;

				
				
			if(strlen($phone_number) >10){
				$to_number 	= $phone_number ;
				$phone_validation = 1;
			}else if(strlen($phone_number) ==8){
				$to_number 	= "65".$phone_number;
				$phone_validation = 1;
			}else{
				return response()->json([
					'message' => "phone number not valid",
				], 400);
			}
			if($phone_validation ==1){

				$message 	= "Welcome+to+Aerea+".$source.":+Your+account+verification+code+is+".$otp;
				$sender 	= "Aerea";

				$sentotpotp = "https://mx.fortdigital.net/http/send-message?username=".$username."&password=".$password."&to=".$to_number."&from=".$sender."&message=".$message;
				
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $sentotpotp,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "get",
					CURLOPT_POSTFIELDS => "",
					CURLOPT_SSL_VERIFYHOST => 0,
					CURLOPT_SSL_VERIFYPEER => 0,
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);
				//print_r($response);
				curl_close($curl);
			}


		}
		else{
		
			$companyname = 'Aerea '.$source;
			try{
				Mail::to($email)->send(new OTPEmail([
					'subject' => $companyname.' Forgot password OTP Code',
					'otp' => $otp,
					'companyname' => $companyname,
					'customername' => $customername
				]));				
			} catch (Exception $e) {
				Log::error('Email sending failed: ' . $e->getMessage());
			}
		}

		return $otp;
	}

	public static function forgotpwdotpnew($customername = '', $email = '',$from ='') {
		$otp = rand(00000, 99999);					
		if($from ==2){
			$source ='Home';
			$otpappcode = "+-+".env('HOMEOTPCODE');
		}
		else if($from ==3){
			$source ='Manager';
			$otpappcode = "+-+".env('MANAGEROTPCODE');
		}
		else {
			$source ='OPS+Portal';
			$otpappcode = '';
		}


		User::where('email', $email)->update(['otp' => $otp]);	
		$customername = Crypt::decryptString($customername);
		$userObj = User::where('email', $email)->first();
		$special_char = array("+", " ", "(", ")","-","_");

		$user_roles = explode(",",env('USER_APP_ROLE'));
			if(in_array($userObj->role_id,$user_roles))
				$phonenumber = Crypt::decryptString($userObj->userinfo->phone);
			else
				$phonenumber = Crypt::decryptString($userObj->empinfo->phone);

			if(isset($phonenumber))
				$phone_number 	= str_replace($special_char,"",$phonenumber);
		
		$otp_option =1;
		if($from ==2 ){
			if(isset($userObj->propertyinfo->otp_option) && $userObj->propertyinfo->otp_option ==2)
				$otp_option =2;

		}else{
			if(isset($userObj->propertyinfo->manager_otp_option) && $userObj->propertyinfo->manager_otp_option ==2)
				$otp_option =2;
		}
		
		if($otp_option ==2){
			//$userObj = User::where('email', $email)->first();
			$phone_validation = 0;
			$username 	= env('SMS_USER');
			$password 	= env('SMS_PASS');
			if(isset($userObj->propertyinfo->sms_username) && $userObj->propertyinfo->sms_username !='')
				$username 	= $userObj->propertyinfo->sms_username;
			if(isset($userObj->propertyinfo->sms_password) && $userObj->propertyinfo->sms_password !='')
				$password 	= $userObj->propertyinfo->sms_password;

				
				
			if(strlen($phone_number) >10){
				$to_number 	= $phone_number ;
				$phone_validation = 1;
			}else if(strlen($phone_number) ==8){
				$to_number 	= "65".$phone_number;
				$phone_validation = 1;
			}else{
				return response()->json([
					'message' => "phone number not valid",
				], 400);
			}
			if($phone_validation ==1){

				$message 	= "Welcome+to+Aerea+".$source.":+Your+account+verification+code+is+".$otp.$otpappcode;
				$sender 	= "Aerea";

				$sentotpotp = "https://mx.fortdigital.net/http/send-message?username=".$username."&password=".$password."&to=".$to_number."&from=".$sender."&message=".$message;
				
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $sentotpotp,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "get",
					CURLOPT_POSTFIELDS => "",
					CURLOPT_SSL_VERIFYHOST => 0,
					CURLOPT_SSL_VERIFYPEER => 0,
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);
				//print_r($response);
				//exit;
				curl_close($curl);
			}


		}
		else{
			$source = str_replace("+"," ",$source);
			$companyname = 'Aerea '.$source;
			$service = app(PHPMailerService::class);
			$returnMailData = $service->sendMail(
                trim($email),
                $companyname.' Forgot password OTP Code',
                'emails.verifyotp', 
                [
                    'otp' => $otp,
					'companyname' => $companyname,
					'customername' => $customername
                ]

            );
		}

		return $otp;
	}

	public function testemail(){

	}

}
