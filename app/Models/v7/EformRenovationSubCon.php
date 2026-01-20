<?php

namespace App\Models\v7;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformRenovationSubCon extends Model
{   	
    
    protected $fillable = [
        'reno_id','workman','id_type','id_number','permit_expiry','status'];


}


