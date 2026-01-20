<?php

namespace App\Models\v2;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformParticularOwner extends Model
{   	
    
    protected $fillable = [
        'reg_id','owner_name','owner_nric','owner_contact_no','owner_vehicle_no','owner_photo'];


}


