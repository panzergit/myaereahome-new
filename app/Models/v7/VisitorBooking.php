<?php

namespace App\Models\v7;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use App\Services\PHPMailerService;

class VisitorBooking extends Model
{
     protected $fillable = [
     'account_id','qr_scan_type','booking_type', 'ticket', 'user_id','unit_no', 'visiting_date','visiting_start_time','visiting_end_time', 'qr_scan_limit','scan_count','visiting_purpose','qrcode_file','status','view_status','remarks'
    ];

    public function visitpurpose(){
        return $this->belongsTo('App\Models\v7\VisitorType','visiting_purpose');
    }

    public function getunit(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
    }

    public function user(){
        return $this->belongsTo('App\Models\v7\User','user_id');
    }

    public function propertyinfo(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }


    public function visitors(){
        return $this->hasMany('App\Models\v7\VisitorList','book_id');
    }

    public function invitedemails(){
        return $this->hasMany('App\Models\v7\VisitorInviteEmailList','book_id');
    }

    public function ticketgen($code) {
        $date = new DateTime('now');
        $autonumver = rand(00000, 99999);	
        $ticket = $code.$date->format('ymd') .$autonumver;
        return $ticket;
        
    }

    public function invite_email($bookId,$userId,$accountId,$email,$name){

        $boking_rec   = VisitorBooking::find($bookId);
        $user_rec   = User::find($userId);
        $prop_rec   = Property::find($accountId);

      

        $logo = env('MAIN_URL')."/storage/app/". $prop_rec->company_logo;
        $companyname = $prop_rec->company_name;
        $companyemail = $prop_rec->company_email;
        $message = $boking_rec->email_message;
        $inviteurl = env('APP_URL')."/pre-registration/".$boking_rec->ticket;
        $invited_by = $user_rec->name;
        $date = date('d-M-y',strtotime($boking_rec->visiting_date));
        		
			$companyname = 'Aerea Home';
			$adminemail = 'hello@myaereahome.com';
            $replyto = 'no-reply@myaereahome.com';
           
			
            $emailcontent = file_get_contents(public_path().'/emails/visitorinvite.php');
            
            $emailcontent = str_replace('#logo#', $logo, $emailcontent);
            //$emailcontent = str_replace('#visitor#', $name, $emailcontent);
            $emailcontent = str_replace('#name#', $user_rec->name, $emailcontent);
            $emailcontent = str_replace('#date#', $date, $emailcontent);
			$emailcontent = str_replace('#companyname#', $companyname, $emailcontent);
            $emailcontent = str_replace('#message#', $message, $emailcontent);
            $emailcontent = str_replace('#companyemail#', $companyemail, $emailcontent);
            $emailcontent = str_replace('#url#', $inviteurl, $emailcontent);
            
           
			$subject = $companyname .': Visitor Pre Registration';
			
			$headers = 'From: '.$companyname.' <'.$adminemail.'/> ' . "\r\n" ;
			$headers .='Reply-To: '. $replyto . "\r\n" ;
			$headers .='X-Mailer: PHP/' . phpversion();
			$headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

            @mail($email, $subject, $emailcontent, $headers);
           
            
    }

    public static function invite_emailnew($bookId,$userId,$accountId,$email,$name){

        $boking_rec   = VisitorBooking::find($bookId);
        $user_rec   = User::find($userId);
        $prop_rec   = Property::find($accountId);

      

        $logo = env('MAIN_URL')."/storage/app/". $prop_rec->company_logo;
        $companyname = $prop_rec->company_name;
        $companyemail = $prop_rec->company_email;
        $message = $boking_rec->email_message;
        $inviteurl = env('VISITOR_APP_URL')."/pre-registration/".$boking_rec->ticket;
        $invited_by = Crypt::decryptString($user_rec->name);
        $date = date('d-M-y',strtotime($boking_rec->visiting_date));
        		
			$companyname = 'Aerea Home';
        /*
			$adminemail = 'hello@myaereahome.com';
            $replyto = 'no-reply@myaereahome.com';
           
			
            $emailcontent = file_get_contents(public_path().'/emails/visitorinvite.php');
            
            $emailcontent = str_replace('#logo#', $logo, $emailcontent);
            //$emailcontent = str_replace('#visitor#', $name, $emailcontent);
            $emailcontent = str_replace('#name#', $user_rec->name, $emailcontent);
            $emailcontent = str_replace('#date#', $date, $emailcontent);
			$emailcontent = str_replace('#companyname#', $companyname, $emailcontent);
            $emailcontent = str_replace('#message#', $message, $emailcontent);
            $emailcontent = str_replace('#companyemail#', $companyemail, $emailcontent);
            $emailcontent = str_replace('#url#', $inviteurl, $emailcontent);
			$headers = 'From: '.$companyname.' <'.$adminemail.'/> ' . "\r\n" ;
			$headers .='Reply-To: '. $replyto . "\r\n" ;
			$headers .='X-Mailer: PHP/' . phpversion();
			$headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

            @mail($email, $subject, $emailcontent, $headers);*/
           
			$subject = $companyname .': Visitor Pre Registration';

            $service = app(PHPMailerService::class);
			$returnMailData = $service->sendMail(
                trim($email),
                $subject,
                'emails.visitorinvite', 
                [
                    'logo' => $logo,
					'companyname' => $companyname,
					'name' => $invited_by,
                    'date' => $date,
					'message' => $message,
                    'companyemail' => $companyemail,
					'url' => $inviteurl,

                ]
            );
			
           
            
    }

    public function qrcode_email($bookId,$userId,$name,$email,$mobile,$vehicle_no,$qr_code,$id_number){

       
        $boking_rec   = VisitorBooking::find($bookId);
        $user_rec   = User::find($userId);
        $prop_rec   = Property::find($boking_rec->account_id);
      

        $logo = env('MAIN_URL')."/storage/app/". $prop_rec->company_logo;
        $companyname = $prop_rec->company_name;
        $companyemail = $prop_rec->company_email;
        $qrcode_eurl = env('APP_URL')."/assets/visitorqr/".$qr_code;
        $ticket = $boking_rec->ticket;
        $property = $boking_rec->propertyinfo->company_name;
        $invited_by = $user_rec->name;
        $date_of_visit = date('d/m/y',strtotime($boking_rec->visiting_date));
        $unit = isset($boking_rec->user->userinfo->getunit->unit)?$boking_rec->user->userinfo->getunit->unit:'';
        $purpose = isset($boking_rec->visitpurpose->visiting_purpose)?$boking_rec->visitpurpose->visiting_purpose:'';



        		
			$companyname = 'Aerea Home';
			$adminemail = 'hello@myaereahome.com';
            $replyto = 'no-reply@myaereahome.com';
           
			
            $emailcontent = file_get_contents(public_path().'/emails/visitorqrcode.php');
            
            $emailcontent = str_replace('#logo#', $logo, $emailcontent);
            $emailcontent = str_replace('#companyname#', $companyname, $emailcontent);
            
            $emailcontent = str_replace('#ticket#', $ticket, $emailcontent);
            $emailcontent = str_replace('#property#', $property, $emailcontent);
            $emailcontent = str_replace('#invited_by#', $invited_by, $emailcontent);
            $emailcontent = str_replace('#date_of_visit#', $date_of_visit, $emailcontent);
            $emailcontent = str_replace('#unit#', $unit, $emailcontent);
            $emailcontent = str_replace('#purpose#', $purpose, $emailcontent);

            $emailcontent = str_replace('#name#', $name, $emailcontent);
            $emailcontent = str_replace('#email#', $email, $emailcontent);
            $emailcontent = str_replace('#mobile#', $mobile, $emailcontent);
            $emailcontent = str_replace('#vehicle_no#', $vehicle_no, $emailcontent);

            if($id_number !=''){
            $id_number_content ='<tr>
                <td style="font-size:14px;color:#454545; width:40%;">
                    ID Number : 
                </td>
                <td style="font-size:14px;color:#454545;">'.$id_number.'</td>
            </tr>';
            }
            else{
                $id_number_content='';
            }
            $emailcontent = str_replace('#id_number#', $id_number_content, $emailcontent);

            //$emailcontent = str_replace('#id_numner#', $name, $emailcontent);

            $emailcontent = str_replace('#qrcode_eurl#', $qrcode_eurl, $emailcontent);

           //echo $emailcontent;

			$subject = $companyname .': Visitor Pre Registration QR Code';
			
			$headers = 'From: '.$companyname.' <'.$adminemail.'/> ' . "\r\n" ;
			$headers .='Reply-To: '. $replyto . "\r\n" ;
			$headers .='X-Mailer: PHP/' . phpversion();
			$headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

            @mail($email, $subject, $emailcontent, $headers);
           
            
    }

    public function visited_count(){
        return $this->visitors()->where('visit_status', 1);
    }
    public function registered_count(){
        return $this->visitors()->where('visit_status', 0);
    }
    public function visitor_entry_date($bookId){
        $visitorObj = VisitorList::where('book_id',$bookId)->orderby('id','asc')->first();
        if($visitorObj){
          return date('d/m/y',strtotime($visitorObj->entry_date));
        }
        return '';
    }
    public function visitor_entry_time($bookId){
        $visitorObj = VisitorList::where('book_id',$bookId)->orderby('id','asc')->first();
        if($visitorObj){
          return date('H:i',strtotime($visitorObj->entry_date));
        }
        return '';
    }

    public function count_total_scan($bookId){
        $visitorObj = VisitorList::selectRaw("SUM(visit_count) as visit_count")->where('book_id',$bookId)->first();
        if($visitorObj){
            return  $visitorObj->visit_count;
        }
        return 0;
    }

}
