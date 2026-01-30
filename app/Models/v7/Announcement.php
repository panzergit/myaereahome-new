<?php

namespace App\Models\v7;
use App\Models\v7\Role;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
     protected $fillable = [
      'account_id','title','document','notes', 'roles','upload','upload_2','upload_3','upload_4','upload_5'
    ];

    public function uploadedto(){
        return $this->belongsTo('App\Models\v7\Department','announcement_to');
    }

    // public function getUploadAttribute($value)
    // {
    //   if(empty($value)) return $value;
    //   return is_primary_domain() ? upload_path($value) : $value;
    // }

    public function role(){
     
        return $this->belongsTo('App\Models\v7\Role')->whereIn('id',['roles']);
    }

    public function roles($ids){
      $role_id = explode(",",$ids);
      $roles = Role::Select('name')->WhereIn('id',$role_id)->get();
      return  $roles;
  }

    public function details(){
        $instance =$this->hasMany('App\Models\v7\AnnouncementDetail','a_id');
        $instance->orderby('name','asc');
        return $instance;
        
    }

}
