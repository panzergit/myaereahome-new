<?php

namespace App\Models\v7;
use App\User;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
     protected $fillable = [
        'account_id','unit_no','user_id', 'module', 'status','ref_id','title','message'
    ];

    public function userinfo(){
        return $this->belongsTo('App\Models\v7\User','user_id');
      }

    public function addpropinfo(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
      }
  
      public function addunitinfo(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
      }

}
