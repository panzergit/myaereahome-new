<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class AnnouncementtestDetail extends Model
{
     protected $fillable = [
        'a_id','account_id','unit_no', 'user_id','name','email', 'status','view_status','notification_status'
    ];

   
    public function announcement(){
        return $this->belongsTo('App\Models\v7\Announcement','a_id');
    }
}
