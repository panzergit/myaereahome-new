<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class InboxMessage extends Model
{
     protected $fillable = [
      'account_id','ref_id','user_id','unit_no','type','title', 'message','booking_date','booking_time','status','view_status','submitted_by','admin_view_status','event_status'
    ];


}
