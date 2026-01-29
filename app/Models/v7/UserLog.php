<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model 
{
    protected $fillable = [
     'account_id','unit_no', 'user_id','login_from','device_info','device_token','fcm_token','version','call_device_token'
    ];

    public function user(){
        return $this->belongsTo('App\Models\v7\User','user_id');
    }

    public function property(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }


}
