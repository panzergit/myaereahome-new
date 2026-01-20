<?php

namespace App\Models\v7;;
use DateTime;

use Auth;
use Illuminate\Database\Eloquent\Model;

class ChatBoxBlockUserByAdmin extends Model
{
    protected $table  ="chat_box_block_user_by_admin";
     protected $fillable = [
        'account_id','admin_id','unit_no','type','ref_id','block_user_id','spam','remark','status'
    ];

    public function blockeduserinfo($userid,$account_id){
        //$account_id = Auth::user()->account_id;
        $userinfo = UserMoreInfo::where('user_id',$userid)->where('account_id',$account_id)->orderby('id','desc')->first();
        return $userinfo;
    }

    public function admininfo(){
        return $this->belongsTo('App\Models\v7\User','admin_id');
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
