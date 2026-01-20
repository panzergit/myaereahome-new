<?php

namespace App\Models\v2;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
     protected $fillable = [
     'account_id', 'ticket','ref_id','notes', 'user_id','unit_no','status','view_status','remarks','signature','inspection_owner_signature','inspection_team_signature','handover_owner_signature','handover_team_signature','inspection_status','handover_status','completion_date'
    ];

    public function property(){
        return $this->belongsTo('App\Models\v2\Property','account_id');
    }

    public function getunit(){
        return $this->belongsTo('App\Models\v2\Unit','unit_no');
    }
    
    public function user(){
        return $this->belongsTo('App\Models\v2\User','user_id');
    }

    public function submissions(){
        return $this->hasMany('App\Models\v2\DefectSubmission','def_id');
    }

    public function inspection(){
        return $this->hasOne('App\Models\v2\JoininspectionAppointment','def_id');
    }

    public function ticketgen($customername = '', $email = '') {
        $date = new DateTime('now');
        $autonumver = rand(00000, 99999);	
        $ticket = $date->format('ymd') .$autonumver;
        return $ticket;
        
    }

   

}
