<?php

namespace App\Models\v7;
use Mail;

/*use App\User;
use App\UserMoreInfo;
use App\Property;
*/

use Illuminate\Database\Eloquent\Model;

class FinalInspectionAppointment extends Model
{
     protected $fillable = [
      'user_id','def_id','account_id','unit_no','appt_date','appt_time','nricid_1','nricid_2','reason', 'status','progress_date','reminder_in_days','reminder_emails','email_message','reminder_email_status','reminder_email_send_on','notification_status'
    ];

    public function getunit(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
    }

     public function getname(){
        return $this->belongsTo('App\Models\v7\User','user_id');
    }

    public function perperty_info(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }


    public function timeslots($account){
        //echo $account;
        $result = array();
        $records   = Property::select('inspection_timing')->where('id', $account)->first();
        
        if(isset($records->inspection_timing)){
            $timings = explode(",",$records->inspection_timing);
            foreach($timings as $k =>$val){
                $result[$val] = $val;
            }
        }
        return $result;
    }

    public function opstimeslots($account){
        //echo $account;
        $result = array();
        $records   = Property::select('inspection_timing')->where('id', $account)->first();
        
        if(isset($records->inspection_timing)){
            $timings = explode(",",$records->inspection_timing);
        }
        return $timings;
    }

    public function progress_reminder_email($bookId,$userId,$accountId){

        $boking_rec   = JoininspectionAppointment::find($bookId);
        $user_rec   = User::find($userId);
        $prop_rec   = Property::find($accountId);

        $logo = image_storage_domain(). $prop_rec->company_logo;
        $companyname = $prop_rec->company_name;
        $companyemail = $prop_rec->company_email;
        $message = $boking_rec->email_message;
        $first_name = $user_rec->name;
        if($user_rec->userinfo->last_name !='')
            $last_name = " ".$user_rec->last_name;
        else
            $last_name = '';
        
        $name = $first_name.$last_name;

        $emails = explode(",",$boking_rec->reminder_emails);
        	
					
			$companyname = 'Aerea Home';
			$adminemail = 'hello@myaereahome.com';
            $replyto = 'no-reply@myaereahome.com';
           
			
            $emailcontent = file_get_contents(public_path().'/emails/inspectionreminder.php');
            
			$emailcontent = str_replace('#logo#', $logo, $emailcontent);
			$emailcontent = str_replace('#companyname#', $companyname, $emailcontent);
            $emailcontent = str_replace('#message#', $message, $emailcontent);
            $emailcontent = str_replace('#companyemail#', $companyemail, $emailcontent);
            $emailcontent = str_replace('#customername#', $name, $emailcontent);
            
           
			$subject = $companyname .' Joint Inspection Reminder ';
			
			$headers = 'From: '.$companyname.' <'.$adminemail.'/> ' . "\r\n" ;
			$headers .='Reply-To: '. $replyto . "\r\n" ;
			$headers .='X-Mailer: PHP/' . phpversion();
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
            
            foreach($emails as $k =>$email){
                @mail($email, $subject, $emailcontent, $headers);
            }
            
    }

}
