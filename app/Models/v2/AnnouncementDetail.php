<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class AnnouncementDetail extends Model
{
     protected $fillable = [
        'a_id','account_id', 'user_id','name','email', 'status','view_status'
    ];

   
    public function announcement(){
        return $this->belongsTo('App\Models\v2\Announcement','a_id');
    }
}
