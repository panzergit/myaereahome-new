<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class VisitorList extends Model
{
     protected $fillable = [
      'book_id','name','mobile','vehicle_no','id_number', 'email','qrcode_file','visit_status','visit_count','entry_date','entry_time'
    ];


}
