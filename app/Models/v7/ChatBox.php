<?php

namespace App\Models\v7;;
use DateTime;
use Auth;

use Illuminate\Database\Eloquent\Model;

class ChatBox extends Model
{
    protected $table = "chat_box_lists";
     protected $fillable = [
        'account_id','user_id','unit_no','ticket', 'category','subject','notes','upload_1','status'
    ];

    public function user(){
        return $this->belongsTo('App\Models\v7\User','user_id');
    }

    public function getunit(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
    }
    public function cat_info(){
        return $this->belongsTo('App\Models\v7\ChatBoxCategory','category');
    }
    public function comments(){
        return $this->hasMany('App\Models\v7\ChatBoxComment','ref_id')->where('status',1);
    }
    public function reports(){
        return $this->hasMany('App\Models\v7\ChatBoxReport','ref_id')->where('status',1);
    }
    public function comment_count(){
        return $this->hasMany('App\Models\v7\ChatBoxComment','ref_id')->where('status',1)->count();
    }

    public function report_count(){
        return $this->hasMany('App\Models\v7\ChatBoxReport','ref_id')->where('status',1)->count();
    }
    public function new_count(){
        return $this->hasMany('App\Models\v7\ChatBoxReport','ref_id')->where('status', 1)->where('view_status',1)->count();
    }
    public function report_new_count(){
        $account_id = Auth::user()->account_id;
        $records = ChatBoxReport::where('status', 1)->where('view_status', 1)->where('account_id', $account_id)
        ->count();
        return $records;
    }

    public function report_allnew_count(){
        //$account_id = Auth::user()->account_id;
        $records = ChatBoxReport::where('status', 1)->where('view_status', 1)
        ->count();
        return $records;
    }

    public function propertyinfo(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }
    
    public function ticketgen($customername = '', $email = '') {
        $date = new DateTime('now');
        $autonumver = rand(00000, 99999);	
        $ticket = $date->format('ymd') .$autonumver;
        return $ticket;
        
    }

}
