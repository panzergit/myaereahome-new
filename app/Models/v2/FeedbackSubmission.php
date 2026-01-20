<?php

namespace App\Models\v2;;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class FeedbackSubmission extends Model
{
     protected $fillable = [
        'account_id', 'ticket', 'fb_option','upload_1','upload_2','subject','notes', 'user_id','unit_no','status','view_status','remarks'
    ];

    public function getoption(){
        return $this->belongsTo('App\Models\v2\FeedbackOption','fb_option');
    }

    public function user(){
        return $this->belongsTo('App\Models\v2\User','user_id');
    }

    public function getunit(){
        return $this->belongsTo('App\Models\v2\Unit','unit_no');
    }
    

    public function ticketgen($customername = '', $email = '') {
        $date = new DateTime('now');
        $autonumver = rand(00000, 99999);	
        $ticket = $date->format('ymd') .$autonumver;
        return $ticket;
        
    }

}
