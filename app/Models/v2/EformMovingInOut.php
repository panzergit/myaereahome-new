<?php

namespace App\Models\v2;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformMovingInOut extends Model
{   	
    protected $table = 'eform_moving_in_out';	
    
    protected $fillable = [
        'account_id','unit_no','ticket','user_id','moving_date','resident_name','contact_no','email','mover_comp','in_charge_name','comp_address','comp_contact_no','moving_start','moving_end','status','view_status'];


        public function ticketgen($customername = '', $email = '') {
            $date = new DateTime('now');
            $autonumver = rand(00000, 99999);	
            $ticket = $date->format('ymd') .$autonumver;
            return $ticket;
            
        }

        public function sub_con(){
            return $this->hasMany('App\Models\v2\EformMovingSubCon','mov_id');
        }

        public function payment(){
            return $this->hasOne('App\Models\v2\EformMovingPayment','mov_id');
        }

        public function inspection(){
            return $this->hasOne('App\Models\v2\EformMovingInspection','mov_id');
        }

        public function defects(){
            return $this->hasMany('App\Models\v2\EformMovingDefect','mov_id');
        }

        public function user(){
            return $this->belongsTo('App\Models\v2\User','user_id');
        }

        public function unitinfo(){
            return $this->belongsTo('App\Models\v2\Unit','unit_no');
        }
}


