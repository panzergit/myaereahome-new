<?php

namespace App\Models\v7;
use DateTime;
use Auth;
use Illuminate\Database\Eloquent\Model;

class MpAdsSubmission extends Model
{
    protected $table = 'mp_ads_submissions';
     protected $fillable = [
        'account_id','unit_no','ticket','title','price','upload','notes', 'user_id','item_condition','type','members_count','status'
    ];

    public function gettype(){
        return $this->belongsTo('App\Models\v7\MpAdsType','type');
    }
    public function getcondition(){
        return $this->belongsTo('App\Models\v7\MpAdsCondition','item_condition');
    }


    public function user(){
        return $this->belongsTo('App\Models\v7\User','user_id');
    }
    public function report_count(){
        return $this->hasMany('App\Models\v7\MpAdsReport','ref_id')->where('status',1)->count();;
    }
    public function like_count(){
        return $this->hasMany('App\Models\v7\MpAdsLike','ref_id')->where('status',1)->count();;
    }
    public function ticketgen($customername = '', $email = '') {
        $date = new DateTime('now');
        $autonumver = rand(00000, 99999);	
        $ticket = $date->format('ymd') .$autonumver;
        return $ticket;  
    }
    public function new_count(){
        return $this->hasMany('App\Models\v7\MpAdsReport','ref_id')->where('status', 1)->where('view_status',1)->count();
    }
    public function report_new_count(){
        $account_id = Auth::user()->account_id;
        $records = MpAdsReport::where('status', 1)->where('view_status', 1)->where('account_id', $account_id)
        ->count();
        return $records;
    }
    public function report_allnew_count(){
        
        $records = MpAdsReport::where('status', 1)->where('view_status', 1)
        ->count();
        return $records;
    }
    public function propertyinfo(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }

    public function images(){
        return $this->hasMany('App\Models\v7\MpAdsImage','ref_id');
    }

    public function getlikes(){
        return $this->hasMany('App\Models\v7\MpAdsLike','ref_id');
    }
   

}
