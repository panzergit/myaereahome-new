<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class ContactBook extends Model
{
    protected $table = 'contact_book';	

    protected $fillable = [
      'account_id','user_id','unit_no','name','mobile','vehicle_no','id_number','email'
    ];


}
