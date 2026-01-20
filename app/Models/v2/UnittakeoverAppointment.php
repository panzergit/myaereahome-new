<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class UnittakeoverAppointment extends Model
{
     protected $fillable = [
      'user_id','account_id','unit_no','appt_date','appt_time','nricid_1','nricid_2','reason', 'status','notification_status'
    ];

    public function getunit(){
        return $this->belongsTo('App\Models\v2\Unit','unit_no');
    }

    public function getname(){
        return $this->belongsTo('App\Models\v2\User','user_id');
    }

    public function perperty_info(){
        return $this->belongsTo('App\Models\v2\Property','account_id');
    }

    public function timeslots($account){
        //echo $account;
        $result = array();
        $records   = Property::select('takeover_timing')->where('id', $account)->first();
       if(isset($records->takeover_timing)){
            $timings = explode(",",$records->takeover_timing);
            foreach($timings as $k =>$val){
                $result[$val] = $val;
            }
        }
        return $result;
    }



    

}
