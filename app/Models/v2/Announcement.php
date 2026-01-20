<?php

namespace App\Models\v2;
use App\Models\v2\Role;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
     protected $fillable = [
      'account_id','title','document','notes', 'roles','upload','upload_2','upload_3','upload_4','upload_5'
    ];

      public function uploadedto(){
        return $this->belongsTo('App\Models\v2\Department','announcement_to');
    }

    public function role(){
     
        return $this->belongsTo('App\Models\v2\Role')->whereIn('id',['roles']);
    }

    public function roles($ids){
      $role_id = explode(",",$ids);
      $roles = Role::Select('name')->WhereIn('id',$role_id)->get();
      return  $roles;
  }

    public function details(){
        $instance =$this->hasMany('App\Models\v2\AnnouncementDetail','a_id');
        $instance->orderby('name','asc');
        return $instance;
        
    }

}
