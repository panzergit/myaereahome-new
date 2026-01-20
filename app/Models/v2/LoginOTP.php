<?php

namespace App\Models\v2;
//use Illuminate\Support\Facades\Mail;
use Log;
use Mail;
use App\Models\v2\User;
use App\Models\v2\UserMoreInfo;

use Illuminate\Database\Eloquent\Model;



class LoginOTP extends Model
{
   
    public function sendotp($customername = '', $email = '') {
		$otp = rand(00000, 99999);					
		
		$user = User::where('email', $email)->update(['otp' => $otp]);	
		$userObj = User::where('email', $email)->first();
		//echo $userObj->propertyinfo->otp_option;
	
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
				$phonenumber = $usermoreinfo->phone;
			}
			else
				$phonenumber = $userObj->empinfo->phone;

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
				
				$message 	= "Welcome+to+Aerea+Home:+Your+account+verification+code+is+".$otp;
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
				$err = curl_error($curl);

				curl_close($curl);
			}


		}
		else{
		
			$logo = url('/').'/public/assets/admin/img/aerea-logo.png';		
					
			$companyname = 'Aerea Home';
			$adminemail = 'otp@myaereahome.com';
			$replyto = 'no-reply@myaereahome.com';
			
			$emailcontent = file_get_contents(public_path().'/emails/verifyotp.php');
			$emailcontent = str_replace('#logo#', $logo, $emailcontent);
			$emailcontent = str_replace('#companyname#', $companyname, $emailcontent);
			$emailcontent = str_replace('#otp#', $otp, $emailcontent);
			$emailcontent = str_replace('#customername#', $customername, $emailcontent);
			
			$subject = $companyname.' Login OTP Code';
			
			/*$headers = 'From: '.$companyname.' <'.$adminemail.'/> ' . "\r\n" ;
			$headers .='Reply-To: '. $replyto . "\r\n" ;
			$headers .='X-Mailer: PHP/' . phpversion();
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
			*/
			$headers  = "From: " . $adminemail . "\r\n";
			$headers .= "Reply-To: " . $adminemail . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
			if(mail($email, $subject, $emailcontent, $headers)){
				//echo "The email message was sent.";
			} else {
			 //echo "The email message was not sent.";
			}
			//exit;

		}

		return $otp;
	}

	public function forgotpwdotp($customername = '', $email = '') {
		$otp = rand(00000, 99999);					
		
		User::where('email', $email)->update(['otp' => $otp]);	
		
		$userObj = User::where('email', $email)->first();
		$special_char = array("+", " ", "(", ")","-","_");

		$user_roles = explode(",",env('USER_APP_ROLE'));
			if(in_array($userObj->role_id,$user_roles))
				$phonenumber = $userObj->userinfo->phone;
			else
				$phonenumber = $userObj->empinfo->phone;

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

				$message 	= "Welcome+to+Aerea+Home:+Your+account+verification+code+is+".$otp;
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


		}
		else{
		
			$logo = url('/').'/public/assets/admin/img/aerea-logo.png';		
					
			$companyname = 'Aerea Home';
			$adminemail = 'otp@myaereahome.com';
			$replyto = 'no-reply@myaereahome.com';
			
			$emailcontent = file_get_contents(public_path().'/emails/verifyotp.php');
			$emailcontent = str_replace('#logo#', $logo, $emailcontent);
			$emailcontent = str_replace('#companyname#', $companyname, $emailcontent);
			$emailcontent = str_replace('#otp#', $otp, $emailcontent);
			$emailcontent = str_replace('#customername#', $customername, $emailcontent);
			
			$subject = $companyname.' Forgot password OTP Code';
			
			$headers = 'From: '.$companyname.' <'.$adminemail.'/>' . "\r\n" ;
			$headers .='Reply-To: '. $replyto . "\r\n" ;
			$headers .='X-Mailer: PHP/' . phpversion();
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
					
			@mail($email, $subject, $emailcontent, $headers);
		}

		return $otp;
	}

	public function testemail(){

	}

}
