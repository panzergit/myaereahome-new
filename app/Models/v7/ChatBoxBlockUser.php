<?php

namespace App\Models\v7;;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class ChatBoxBlockUser extends Model
{
     protected $fillable = [
        'account_id','user_id','unit_no','block_user_id','spam','remark','status'
    ];

    public function user(){
        return $this->belongsTo('App\Models\v7\User','user_id');
    }

    public function getunit(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
    }
    
    public function ticketgen($customername = '', $email = '') {
        $date = new DateTime('now');
        $autonumver = rand(00000, 99999);	
        $ticket = $date->format('ymd') .$autonumver;
        return $ticket;
        
    }

}
