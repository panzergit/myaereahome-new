<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class DefectSubmission extends Model
{
     protected $fillable = [
      'ticket','defect_location','defect_type','upload','notes', 'user_id','type','status','defect_status','handover_message','owner_status','remarks'
    ];

    public function getlocation(){
        return $this->belongsTo('App\Models\v2\DefectLocation','defect_location');
    }

    public function gettype(){
        return $this->belongsTo('App\Models\v2\DefectType','defect_type');
    }

    public function user(){
        return $this->belongsTo('App\Models\v2\User','user_id');
    }

    public function ticketgen($customername = '', $email = '') {
        $autonumver = rand(00000, 99999);	
        $ticket = $date->format('ymd') .$autonumver;
        return $otp;
        
    }

    public function getreviews(){
        return $this->hasMany('App\Models\v2\DefectSubmissionReview','def_submission_id');
    }

   

}
