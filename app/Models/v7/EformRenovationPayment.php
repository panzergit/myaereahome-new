<?php

namespace App\Models\v7;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformRenovationPayment extends Model
{   	
    protected $table = 'eform_renovation_payments';	
    
    protected $fillable = [
        'reno_id','manager_id','payment_option','cheque_amount','cheque_received_date','cheque_bank','cheque_no','bt_received_date','bt_amount_received','cash_amount_received','cash_received_date','receipt_no','acknowledged_by','manager_received','signature','date_of_signature','lift_payment_option','lift_cheque_amount','lift_cheque_received_date','lift_cheque_bank','lift_cheque_no','lift_bt_received_date','lift_bt_amount_received','lift_cash_amount_received','lift_cash_received_date','lift_receipt_no','lift_acknowledged_by','lift_manager_received','lift_signature','lift_date_of_signature'];
}


