<?php

namespace App\Models\v7;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformRegVehicle extends Model
{   	
    //protected $table = 'eform_moving_in_out';	
    
    protected $fillable = [
        'account_id','unit_no','ticket','user_id','request_date','owner_name','contact_no','email','declared_by','owner_of_vehicle','licence_no','iu_number','in_charge_name','passport_no','relationship','nominee_contact_no','nominee_email','owner_of_nominee_vehicle','nominee_vehicle_licence_no','nominee_vehicle_iu_number','tenancy_start','tenancy_end','owner_signature','nominee_signature','date_of_sign','status','view_status'];

        public function ticketgen($customername = '', $email = '') {
            $date = new DateTime('now');
            $autonumver = rand(00000, 99999);	
            $ticket = $date->format('ymd') .$autonumver;
            return $ticket;        
        }

        public function documents(){
            return $this->hasMany('App\Models\v7\EformRegVehicleDoc','reg_id');
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


