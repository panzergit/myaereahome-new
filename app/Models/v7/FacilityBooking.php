<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class FacilityBooking extends Model
{
     protected $fillable = [
      'type_id','user_id','account_id','opn_charge_id','opn_card_id','unit_no','booking_date','booking_time','booking_fee','deposit_fee','refund_amount','capture_amount','reason', 'status','view_status','notification_status','payment_required','payment_status','refund_status','cron_job_count','amount_received_date','deposit_payment_status','card_last_digits','card_brand','deposit_refund_amount','claim_amount','deposit_refund_status','deposit_received_date','booking_charge_id','deposit_charge_id', 'refund_reason','ack_status'
    ];

    public function gettype(){
        return $this->belongsTo('App\Models\v7\FacilityType','type_id');
    }

    public function getproperty(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }
    public function getname(){
        return $this->belongsTo('App\Models\v7\User','user_id');
    }

    public function getunit(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
    }

    public function timeslots($type){
        //echo $account;
        $result = array();
        $records   = FacilityType::select('timing')->where('id', $type)->first();
       if(isset($records->timing)){
            $timings = explode(",",$records->timing);
            foreach($timings as $k =>$val){
                $result[$val] = $val;
            }
        }
        return $result;
    }



    

}
