<?php

namespace App\Models\v7;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformDoorAccesscardPayment extends Model
{   	
    protected $table = 'eform_door_accesscard_payments';	
    
    protected $fillable = [
        'reg_id','manager_id','payment_option','cheque_amount','cheque_received_date','cheque_bank','cheque_no','bt_received_date','bt_amount_received','cash_amount_received','cash_received_date','receipt_no','acknowledged_by','manager_received','signature','date_of_signature'];


}


