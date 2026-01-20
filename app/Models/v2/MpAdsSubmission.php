<?php

namespace App\Models\v2;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class MpAdsSubmission extends Model
{
    protected $table = 'mp_ads_submissions';
     protected $fillable = [
        'account_id','unit_no','ticket','title','price','upload','notes', 'user_id','item_condition','type','members_count','status'
    ];

    public function gettype(){
        return $this->belongsTo('App\Models\v2\MpAdsType','type');
    }
    public function getcondition(){
        return $this->belongsTo('App\Models\v2\MpAdsCondition','item_condition');
    }

    public function user(){
        return $this->belongsTo('App\Models\v2\User','user_id');
    }

    public function ticketgen($customername = '', $email = '') {
        $date = new DateTime('now');
        $autonumver = rand(00000, 99999);	
        $ticket = $date->format('ymd') .$autonumver;
        return $ticket;  
    }

    public function images(){
        return $this->hasMany('App\Models\v2\MpAdsImage','ref_id');
    }

    public function getlikes(){
        return $this->hasMany('App\Models\v2\MpAdsLike','ref_id');
    }
   

}
