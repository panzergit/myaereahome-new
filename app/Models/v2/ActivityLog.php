<?php

namespace App\Models\v2;
use Session;
use App\Models\Module;
use App\Models\User;
use Auth;


use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ActivityLog extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id','admin_id','module_id','action', 'ref_id', 'old_values','new_values','notes'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

   
    public function propertyinfo(){
        return $this->belongsTo('App\Models\v2\Property','account_id');
    }
    public function adminInfo(){
        return $this->belongsTo('App\Models\v2\User','admin_id');
    }
    public function userInfo(){
        return $this->belongsTo('App\Models\v2\UserMoreInfo','ref_id');
    }

   
   

}
