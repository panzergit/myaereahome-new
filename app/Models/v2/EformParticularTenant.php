<?php

namespace App\Models\v2;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformParticularTenant extends Model
{   	
    
    protected $fillable = [
        'reg_id','tenant_name','tenant_nric','tenant_contact_no','tenant_vehicle_no','tenant_photo'];


}


