<?php

namespace App\Models\v7;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformRenovationInspection extends Model
{   	
    protected $table = 'eform_renovation_inspection';	
    
    protected $fillable = [
        'reno_id','manager_id','manager_received','manager_signature','date_of_signature','date_of_completion','inspected_by','unit_in_order_or_not','amount_received_by','amount_deducted','refunded_amount','resident_nric','resident_signature','receipt_no','acknowledged_by','manager_received','resident_signature_date','amount_claimable','actual_amount_received'];


}


