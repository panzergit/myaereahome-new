<?php

namespace App\Models\v2;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformRenovation extends Model
{   	
    //protected $table = 'eform_moving_in_out';	
    
    protected $fillable = [
        'account_id','unit_no','ticket','user_id','reno_date','resident_name','contact_no','email','reno_comp','in_charge_name','comp_address','comp_contact_no','reno_start','reno_end','hacking_work_start','hacking_work_end','owner_name','owner_signature','nominee_name','nominee_signature','date_of_sign','nominee_contact_no','letter_of_authorization','status','view_status'];

        public function ticketgen($customername = '', $email = '') {
            $date = new DateTime('now');
            $autonumver = rand(00000, 99999);	
            $ticket = $date->format('ymd') .$autonumver;
            return $ticket;        
        }

        public function sub_con(){
            return $this->hasMany('App\Models\v2\EformRenovationSubCon','reno_id');
        }

        public function details(){
            return $this->hasMany('App\Models\v2\EformRenovationDetail','reno_id');
        }

        public function payment(){
            return $this->hasOne('App\Models\v2\EformRenovationPayment','reno_id');
        }

        public function inspection(){
            return $this->hasOne('App\Models\v2\EformRenovationInspection','reno_id');
        }

        public function defects(){
            return $this->hasMany('App\Models\v2\EformRenovationDefect','reno_id');
        }

        public function user(){
            return $this->belongsTo('App\Models\v2\User','user_id');
        }
}


