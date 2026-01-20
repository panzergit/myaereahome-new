<?php

namespace App\Models\v2;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
     protected $fillable = [
        'account_id','unit_no','user_id','user_info_id', 'announcement', 'key_collection','defect','feedback','facility','resident_management','visitor_management','face_id_upload','resident_file_upload','eforms'
    ];

    public function userinfo(){
        return $this->belongsTo('App\Models\v2\UserMoreInfo','user_info_id');
      }

    public function addpropinfo(){
        return $this->belongsTo('App\Models\v2\Property','account_id');
      }
  
      public function addunitinfo(){
        return $this->belongsTo('App\Models\v2\Unit','unit_no');
      }

}
