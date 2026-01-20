<?php

namespace App\Models\v7;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
     protected $fillable = [
     'account_id', 'ticket','ref_id','notes', 'user_id','unit_no','status','view_status','remarks','signature','inspection_owner_signature','inspection_team_signature','handover_owner_signature','handover_team_signature','inspection_status','handover_status','completion_date','block_no'
    ];

    public function property(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }

    public function getbuilding(){
        return $this->belongsTo('App\Models\v7\Building','block_no');
    }
    
    public function getunit(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
    }
    
    public function user(){
        return $this->belongsTo('App\Models\v7\User','user_id');
    }

    public function submissions(){
        return $this->hasMany('App\Models\v7\DefectSubmission','def_id');
    }

    public function inspection(){
        return $this->hasOne('App\Models\v7\JoininspectionAppointment','def_id');
    }

    public function finalInspection(){
        return $this->hasOne('App\Models\v7\FinalInspectionAppointment','def_id');
    }

    public function ticketgen($customername = '', $email = '') {
        $date = new DateTime('now');
        $autonumver = rand(00000, 99999);	
        $ticket = $date->format('ymd') .$autonumver;
        return $ticket;
        
    }

   

}
