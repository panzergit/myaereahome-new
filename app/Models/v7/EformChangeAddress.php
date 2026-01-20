<?php

namespace App\Models\v7;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformChangeAddress extends Model
{   	
    protected $table = 'eform_address_changes';	
    
    protected $fillable = [
        'account_id','unit_no','ticket','user_id','request_date','owner_name','address','contact_no','email','declared_by','in_charge_name','owner_signature','nominee_signature','date_of_sign','status','view_status'];

        public function ticketgen($customername = '', $email = '') {
            $date = new DateTime('now');
            $autonumver = rand(00000, 99999);	
            $ticket = $date->format('ymd') .$autonumver;
            return $ticket;        
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


