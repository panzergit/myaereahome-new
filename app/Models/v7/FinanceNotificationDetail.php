<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class FinanceNotificationDetail extends Model
{
     protected $fillable = [
        'info_id','invoice_id','account_id','unit_no', 'user_id','name','email', 'status','view_status','notification_status'
    ];

   
}
