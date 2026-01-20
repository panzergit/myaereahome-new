<?php

namespace App\Models\v7;
use App\Models\v7\Role;

use Illuminate\Database\Eloquent\Model;

class AnnouncementCron extends Model
{
     protected $fillable = [
      'account_id', 'roles','a_id','cron_status'
    ];
    public function role(){
     
        return $this->belongsTo('App\Models\v7\Role')->whereIn('id',['roles']);
    }

    public function roles($ids){
      $role_id = explode(",",$ids);
      $roles = Role::Select('name')->WhereIn('id',$role_id)->get();
      return  $roles;
  }


}
