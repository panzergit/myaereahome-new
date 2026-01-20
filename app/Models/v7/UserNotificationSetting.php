<?php

namespace App\Models\v7;
use App\User;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
     protected $fillable = [
        'account_id','unit_no','user_id','user_info_id', 'announcement', 'key_collection','defect','feedback','facility','resident_management','visitor_management','face_id_upload','resident_file_upload','eforms','resichat','marketplace'
    ];

    public function userinfo(){
        return $this->belongsTo('App\Models\v7\UserMoreInfo','user_info_id');
      }

    public function addpropinfo(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
      }
  
      public function addunitinfo(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
      }

}
