<?php

namespace App\Models\v7;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformDoorAccess extends Model
{   	
    //protected $table = 'eform_moving_in_out';	
    
    protected $fillable = [
        'account_id','unit_no','ticket','user_id','request_date','owner_name','contact_no','email','declared_by','in_charge_name','passport_no','relationship','nominee_contact_no','nominee_email','no_of_card_required','no_of_schlage_required','tenancy_start','tenancy_end','owner_signature','nominee_signature','date_of_sign','status','view_status'];

        public function ticketgen($customername = '', $email = '') {
            $date = new DateTime('now');
            $autonumver = rand(00000, 99999);	
            $ticket = $date->format('ymd') .$autonumver;
            return $ticket;        
        }

        public function payment(){
            return $this->hasOne('App\Models\v7\EformDoorAccesscardPayment','reg_id');
        }

        public function ack(){
            return $this->hasOne('App\Models\v7\EformDoorAccesscardAck','reg_id');
        }

        public function user(){
            return $this->belongsTo('App\Models\v7\User','user_id');
        }

        public function unitinfo(){
            return $this->belongsTo('App\Models\v7\Unit','unit_no');
        }

        public function getunit(){
            return $this->belongsTo('App\Models\v7\Unit','unit_no');
        }
      
}


