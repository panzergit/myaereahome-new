<?php

namespace App\Models\v7;;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class MpAdsLike extends Model
{
     protected $fillable = [
        'account_id','user_id','unit_no','ref_id'
    ];
    public function likeduserinfo($userid,$account_id){
        //$account_id = Auth::user()->account_id;
        $userinfo = UserMoreInfo::where('user_id',$userid)->where('account_id',$account_id)->orderby('id','desc')->first();
        return $userinfo;
    }
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
