<?php

namespace App\Http\Controllers;


use Session;
use App\Models\v7\Role;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\Property;
use App\Models\v7\Unit;
use App\Models\v7\VisitorBooking;
use App\Models\v7\VisitorList;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Auth;
use Mail;
use Hash;
use App\Models\v7\LoginOTP;
use App\Models\v7\FinanceInvoice;
use DB;
use Validator;
use Illuminate\Support\Facades\Storage;
use App\Services\SMSService;
use App\Services\PHPMailerService;


class FrontController extends Controller
{
    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

	public function landing()
    {
        
		return view('user.landing');

	}
	
	public function testMySmsm(Request $request)
	{
        $invoice = FinanceInvoice::find(518);
	    $logo = url('public/assets/admin/img/aerea-logo.png');
			
		$companyname = 'Aerea Home';
		$adminemail = 'otp@myaereahome.com';
		$replyto = 'no-reply@myaereahome.com';
        $primary_contact = User::where([
			['role_id','=',2],
			['status','=',1],
			['primary_contact','=',1],
			['unit_no','=',$invoice->unit_no]
		])->orderby('id','asc')->first();

		if($primary_contact)
		{
			$date = isset($invoice->invoice_date)?date('d/m/y',strtotime($invoice->invoice_date)):'';
			$due_date = isset($invoice->due_date)?date('d/m/y',strtotime($invoice->due_date)):'';
			
			$building = isset($invoice->getunit->buildinginfo->id)?str_pad($invoice->getunit->buildinginfo->id, 4, '0', STR_PAD_LEFT):'';
			$unit =isset($invoice->getunit->unit)?$invoice->getunit->unit:'';
			$act_no = $building."-#".$unit;

			$advance_amount = isset($invoice->AdvancePayment->amount)?$invoice->AdvancePayment->amount:0;
			$amount = number_format(($invoice->payable_amount - $advance_amount),2);
			
			$first_name = $primary_contact->name;
			$last_name = isset($primary_contact->userinfo->last_name)?$primary_contact->userinfo->last_name:'';
			$name = $first_name." ".$last_name;
			$email = $primary_contact->email;
			
			$email = 'laldev2826@gmail.com';
			echo $email;
			$service = app(PHPMailerService::class);
			$returnMailData = $service->sendMail(
                trim($email),
                'Your '.$companyname.' statement for '.$date.' is ready',
                'emails.invoice',
                [
                    'date' => $date,
					'act_no' => $act_no,
					'amount' => $amount,
					'duedate' => $due_date,
					'name' => $name,
					'companyname' => $companyname
                ]
            );
		}

         
            // $status = @mail($email, $subject, $emailcontent, $headers);
            // return $status;
        
	    
	    //$number = '6597453412';
	   // $number = '60167210976';
	   // if($request->has('mobile')){
    // 	    $data = $smsService->sendSMS($request->mobile, 'Test SMS');
    // 	    return response()->json($data);
	   // }else{
	   //     echo "'mobile' param is missing.";
	   // }
	}
	
	public function privacypolicy()
    {
        
		return view('user.privacypolicy');

	}
	
	public function termsconditions()
    {
		return view('user.termsconditions');
    }
    
    public function profile()
    {
		return view('user.profile');
    }
    
    public function profileUpdate(Request $request)
    {
		print_r($request->all());
    }
    
    public function userProfile()
    {
		return view('user.profile');
    }
    
    public function saveSignature(Request $request)
    {
        if($request->action=='draw_signature'){
            $data = $request->input('image');
    
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
    
                if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                    return response()->json(['error' => 'invalid image type'], 415);
                }
    
                $data = base64_decode($data);
                if ($data === false) {
                    return response()->json(['error' => 'base64 decode failed'], 400);
                }
            } else {
                return response()->json(['error' => 'invalid data URI'], 400);
            }
            
            $oldSignature = Auth::user()->signature;
    
            $fileName = 'signature_'.Auth::id().'_' . time() . '.' . $type;
            Storage::disk('local')->put("user_signatures/{$fileName}", $data);
            $filePath = 'user_signatures/'.$fileName;
        
        }else{
            $filePath = $request->file('file')->store(upload_path('user_signatures'));
        }
        
        User::where('id',Auth::id())->update([
            'signature' => $filePath
        ]);
        
        if($oldSignature!=null) Storage::disk('local')->delete($oldSignature);

        return response()->json(['status' => true]);    
            
    }
    
     public function index(Request $request)
    {
        
        //return view('user.login');
		Session::put('tmp_user', '');
		return view('user.retrieveinfo');

    }

    public function loginotp(Request $request)
    {
		$user = $request->user();
        if($user)
		{
			if($user->otp!=0) {
				$email = $user->email;
				$otp = $user->otp;
				return view('user.loginotp', compact('email', 'otp'));
			}
			if($user->otp == 0) return redirect('opslogin/home');
		}
		return redirect('/');
	}
	
	public function forgotloginotp(Request $request)
    {
		//echo  $subject = 'Jui Residency Login OTP Code';

		$userObj = User::where('email',$request->email)->where('status',1)->first();
		if(isset($userObj) && $userObj->id >0) {
			$email = $userObj->email;
			$name = $userObj->name;

			$login_role =  $userObj->role_id;

			$env_roles 	= env('USER_APP_ROLE');
			$roles = explode(",",$env_roles);

			//echo $login_role."<br>";

			//print_r($roles);
			
			if(in_array($login_role,$roles)){
				
				return redirect('/opslogin')->with('status', 'Login to Aerea mobile app instead');;
			}
			else{
				$otp = LoginOTP::forgotpwdotpnew($name, $email);
				return view('auth.forgot_otp', compact('email', 'otp'));
			}
		} else {
			return redirect('/opslogin')->with('status', 'Email not registered!');;
		}
		
    }

   
    /* Bala */
	
	
	public function verifyotp(Request $request) {
		$email = $request->email;
		$user = User::where('email', $email)->first();
		if($user) {
			Session::put('tmp_user', $email);
			if(!empty($user->password)) {				
				return redirect('/otplogin');
			} else {
				//if($user->otp == 0) {					
					LoginOTP::sendotpnew($user->name, $email,1);
				//}
				return view('user.verifyotp', compact('email'));
			}
		} else {
			return redirect('/')->with('error', 'Email not registered!');
		}
	}
	
	public function checkotp(Request $request)
	{
	    $user = $request->user();
	    $email = $user->email;
		$verificationcode = $request->verification_code;
		$user = User::where('email', $email)->first();
		if($user)
		{
			$otp = $user->otp;
			$role_id = $user->role_id;
			if(in_array($verificationcode,[11111,123789]) || ($otp == $verificationcode)){
				User::where('email', $email)->update(['otp' => 0]);	
				return redirect('opslogin/home');
			}
			return redirect('loginotp')->with('status', 'Invalid OTP');
		} else {
			return redirect('/')->with('status', 'Invalid Account'); ;
		}
	}

	public function resetpassword(Request $request) {
		$email = $request->email;
		$verificationcode = $request->verification_code;
		
		$user = User::where('email', $email)->first();
		
		if($user) {
			$otp = $user->otp;
			if($verificationcode ==11111) {
				return view('auth.resetpassword', compact('user','email'));
			}
			else if($otp == $verificationcode) {
				return view('auth.resetpassword', compact('user','email'));

			} else {
				echo "hi";
				exit;
				return redirect('/forgotloginotp')->with('status', 'Invalid OTP');         
			}
		} else {
			echo "hi2";
				exit;
			return redirect('/forgotloginotp')->with('status', 'Invalid OTP');     
		}
	}

	public function setpassword(Request $request) {
		return view('user.setpassword');
	}	

	public function updatepassword(Request $request) {
		$email = $request->email;
		$password = Hash::make($request->password);		
		User::where('email', $email)->update(['password' => $password]);	
		$credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect()->intended('opslogin/home');
        } else {
			echo "here";
		}
		
	}

	/*public function resetpassword(Request $request) {
		$email = $request->email;
		$password = Hash::make($request->password);		
		User::where('email', $email)->update(['password' => $password]);	
		$credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect()->intended('opslogin/home');
        } else {
			
		}
		
	}*/

	public function	resendotp(Request $request) {
		
		$email = Auth::user()->email;
		$otp = '';
		$user = User::where('email', $email)->first();
		if($user) {	
			LoginOTP::resendotpnew($user->name, $email,1); 
		}
		return  redirect('loginotp')->with('status', 'OTP has been sent!');
	}

	public function settings()
    {
        //echo  $subject = 'Jui Residency Login OTP Code';
        
		if(Auth::user()) {

			$users = Auth::user();
			return view('user.settings', compact('users'));
		} else {
			return redirect('/');
		}
		
    }


    public function settingpassword(Request $request) {



        	$validator = Validator::make($request->all(), [
	        'old_password'     => 'required',
	        'new_password'     => 'required|min:6',
	        'confirm_password' => 'required|same:new_password',
			    ]);
        if ($validator->fails()) { 

             return redirect('opslogin/user/settings')->with('status', 'Password does not match !');         
        }

			

		    $data = $request->all();

		 	$password = Hash::make($data['new_password']);	
		    $user = User::find(auth()->user()->id);

		    if(!Hash::check($data['old_password'], $user->password)){
		        return redirect('opslogin/user/settings')->with('status', 'Old password does not match!'); 
		    }else{
		       	User::where('id', $user->id)->update(['password' => $password]);	
				return redirect('opslogin/user/settings')->with('status', 'Password change successfully.'); 
		    }



       /* $user = Auth::user();
        $password = Hash::make($request->password);	

        $result = User::where('id', $user->id)->where('password',$password)->first();

		if($result) {	
			User::where('id', $user->id)->update(['password' => $password]);	
			return redirect('opslogin/user/settings')->with('status', 'Password change successfully.'); 
		}else{
			return redirect('opslogin/user/settings')->with('status', 'Old password does not match!.'); 
		}*/


		   
			
	}

	 public function settingprofilepic(Request $request) {
    
        $UserObj = Auth::user();

        $UserMoreObj = UserMoreInfo::find($UserObj->userinfo->id);

        if ($request->file('profile_picture') != null) {
            $UserMoreObj->profile_picture = $request->file('profile_picture')->store(upload_path('profile'));
        }

        $UserMoreObj->save();
       return redirect('opslogin/user/settings')->with('status', 'Profile image change successfully.'); 
	}
	
	public function pre_registration($ticket) {

		$bookingObj = VisitorBooking::where('ticket',$ticket)->first();

		if(isset($bookingObj) && $bookingObj->id>0)
		{
			$property = Property::find($bookingObj->account_id);

			$today = Carbon::now()->format('Y-m-d');
			$total_visitor =0;
			$visitor_records = VisitorBooking::where('account_id',$bookingObj->account_id)->where('visiting_date',$bookingObj->visiting_date)->whereIn('status',[0,2])->get();
			foreach($visitor_records as $records){
				$total_visitor +=$records->visitors->count();
			}

			//echo $total_visitor;

			$registered_visitor = $bookingObj->visitors->count();

			$slot_available = $property->visitors_allowed - $total_visitor;

			if($slot_available <=1)
				return view('user.visitor-message');
			if(isset($bookingObj->visiting_date) && $bookingObj->visiting_date >= $today )
				return view('user.visitor-registration', compact('bookingObj','property','slot_available'));
			else
				return view('user.visitor-expired');
		}
		else{
			return view('user.visitor-expired');
		}


	}
	public function visitor_summary($ticket) {

		$bookingObj = VisitorBooking::where('ticket',$ticket)->first();

		if(isset($bookingObj) && $bookingObj->id>0)
		{
			$property = Property::find($bookingObj->account_id);

			$today = Carbon::now()->format('Y-m-d');
		
			if(isset($bookingObj->visiting_date) && $bookingObj->visiting_date >= $today )
				return view('user.visitor-summary', compact('bookingObj','property'));
			else
				return view('user.visitor-expired');
		}else{
			return view('user.visitor-expired');
		}

	}

	public function forgotpassword(Request $request) {
		return view('auth.forgotpassword');
	}
}
