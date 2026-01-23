<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session, Auth, Mail, Hash, DB, Validator;
use App\Models\v7\Role;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\Unit;
use App\Services\PHPMailerService;
use App\Models\v7\Property;
use App\Models\v7\Building;
use App\Models\v7\Country;
use App\Models\v7\UserRegistrationRequest;
use App\Models\v7\Mail\LoginOtp;

class StaticController extends Controller
{
    // protected $mailService;

    // public function __construct(PHPMailerService $mailService)
    public function __construct()
    {
        // $this->mailService = $mailService;
    }
    
    public function testMail(Request $request)
    {
        if($request->has('email'))
        {
            Mail::raw('This is a test email', fn ($m) => $m->to(trim($request->email))->subject('Test Mail'));
            
            // $returnMailData = $this->mailService->sendMail(
            //     trim($request->email),
            //     'Test Mail Subject',
            //     'emails.test_content', 
            //     [
            //         'name' => trim($request->email),
            //         'message' => 'Your test message'
            //     ]
            // );
    
            // if($returnMailData) echo "Success";
            // if(!$returnMailData) echo "Failure";
        }else{
            echo "Pass 'email' param in url";
        }
    }
    
    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

	public function landing()
    {
        if(Auth::check()){
            $user = Auth::user();
            if($user->otp!=0){
                $user->otp = 0;
                $user->save();
                Auth::logout();
                return redirect('/');
            }
            if($user->otp==0) return redirect('opslogin/home');
        }

		return view('user.landing');
	}
	
	public function privacypolicy()
    {
		return view('user.privacypolicy');
	}
	
	public function termsconditions()
    {
		return view('user.termsconditions');
	}

	public function faqprofile()
    {
		return view('faq.profile');
	}

	public function faqunittakeover()
    {
		return view('faq.takeover');
	}

	public function faqdefects()
    {
		return view('faq.defect');
	}

	public function faqjointinspection()
    {
		return view('faq.inspection');
	}

	public function faqfeedback()
    {
		return view('faq.feedback');
	}

	public function faqfacilities()
    {
		return view('faq.facility');
	}
	
	public function contactus()
    {
		return view('faq.contactus');
	}
	
	public function enquiry(Request $request)
    {
		$input = $request->all();
		$name = $input['first_name']." ".$input['last_name'];
		$phone = $input['phone'];
		$email = $input['email'];
		$enquiry = $input['enquiry'];
        $logo = url('public/assets/admin/img/aerea-logo.png');

		$companyname = 'Aerea Home';
		$adminemail = 'help@myaereahome.com';
		$replyto = 'no-reply@myaereahome.com';
			
        $emailcontent = file_get_contents(public_path().'/emails/enquiry.php');
        $emailcontent = str_replace('#logo#', $logo, $emailcontent);
        $emailcontent = str_replace('#companyname#', $companyname, $emailcontent);
        $emailcontent = str_replace('#name#', $name, $emailcontent);
        $emailcontent = str_replace('#phone#', $phone, $emailcontent);
        $emailcontent = str_replace('#email#', $email, $emailcontent);
        $emailcontent = str_replace('#enquiry#', $enquiry, $emailcontent);
        
        $subject = "New enquiry request";
        
        $headers = 'From: '.$companyname.' <'.$adminemail.'/>' . "\r\n" ;
        $headers .='Reply-To: '. $replyto . "\r\n" ;
        $headers .='X-Mailer: PHP/' . phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
                
        @mail($email, $subject, $emailcontent, $headers);
        return redirect('/contact-us')->with('status', 'Your enquiry has been submitted. We will revert back to you shortly.');
    }

	public function user_registration()
    {
		$user_roles = explode(",",env('USER_APP_ROLE'));
		$properties = Property::where('open_for_registration',1)->pluck('company_name', 'id')->all();
		$roles = Role::whereIn('id',$user_roles)->pluck('name', 'id')->all();
        $allcountries = Country::whereNotIn('id',[1,2])->where('status',1)->orderby('country_name','ASC')->pluck('country_name', 'id')->all();
        $loccountries = Country::whereIn('id',[1,2])->where('status',1)->pluck('country_name', 'id')->all();
		$countries =$loccountries +  $allcountries ;
        return view('user.registration', compact('properties','roles','countries'));
	}

	public function submit_registration(Request $request)
    {
		if($request){
			$input = $request->all();
			$check_email_account = User::where('email',$input['email'])->first();
			
            if($check_email_account) return redirect('/message')->with('status', '1');

            $check_email_account = UserRegistrationRequest::where('status','!=',3)->where('email',$input['email'])->first();
            
            if($check_email_account) return redirect('/reg-message')->with('status', '1');
                
            $total_vehicle = 0;
            $license_records = UserRegistrationRequest::where('unit_no',$input['unit_no'])->get();
            if($license_records){
                foreach($license_records as $license_record){
                    if($license_record->first_vehicle !='') $total_vehicle++;
                    if($license_record->second_vehicle !='') $total_vehicle++;
                }
            }

            if(($total_vehicle >=2 && $license_record->first_vehicle !='') || 
                ($total_vehicle >=2 && $license_record->second_vehicle !='')) return redirect('/license-message')->with('status', '1');
            
            if ($request->file('profile') != null) $input['profile_picture'] = $request->file('profile')->store(upload_path('user_request'));
            if ($request->file('contract') != null && $input['role_id']==29) $input['contract_file'] = $request->file('contract')->store(upload_path('user_request'));
            
            return redirect('/thankyou')->with('status', '1');
		}
	}

	public function thankyou(Request $request)
    {	
		$message = 1;
		return view('user.reg_success',compact('message'));
	}

	public function message(Request $request)
    {	
		$message = 2;
		return view('user.reg_success',compact('message'));
	}

	public function reg_message(Request $request)
    {	
		$message = 3;
		return view('user.reg_success',compact('message'));
	}

	public function license_message(Request $request)
    {	
		$message = 4;
		return view('user.reg_success',compact('message'));
	}
}