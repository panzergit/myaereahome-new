<?php

namespace App\Models\v7;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformMovingDefect extends Model
{   	
    protected $table = 'eform_moving_defects';	
    
    protected $fillable = ['mov_id','notes','image_base64','view_status','remarks'];


}


