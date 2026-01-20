<?php

namespace App\Models\v2;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformDoorAccesscardAck extends Model
{   	
    protected $table = 'eform_door_accesscard_acknowledgements';	
    
    protected $fillable = [
        'reg_id','manager_id','number_of_access_card','serial_number_of_card','acknowledged_by','manager_issued','signature','date_of_signature'];


}


