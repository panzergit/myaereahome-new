<?php

namespace App\Models\v2;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformMovingSubCon extends Model
{   	
    protected $table = 'eform_moving_sub_con';	
    
    protected $fillable = [
        'mov_id','workman','id_type','id_number','permit_expiry','status'];


}


