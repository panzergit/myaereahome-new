<?php

namespace App\Models\v7;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformParticular extends Model
{   	
    protected $table = 'eform_particulars';	
    
    protected $fillable = [
        'account_id','unit_no','ticket','user_id','request_date','owner_name','address','email','tenancy_start','tenancy_end','owner_signature','date_of_sign','status','view_status'];

        public function ticketgen($customername = '', $email = '') {
            $date = new DateTime('now');
            $autonumver = rand(00000, 99999);	
            $ticket = $date->format('ymd') .$autonumver;
            return $ticket;        
        }

        public function owners(){
            return $this->hasMany('App\Models\v7\EformParticularOwner','reg_id');
        }

        public function tenants(){
            return $this->hasMany('App\Models\v7\EformParticularTenant','reg_id');
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


