<?php

namespace App\Models\v2;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformRenovationDefect extends Model
{   	
    protected $table = 'eform_renovation_defects';	
    
    protected $fillable = ['reno_id','notes','image_base64','view_status','remarks'];


}


