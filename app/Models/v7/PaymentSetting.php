<?php

namespace App\Models\v7;
use Session;


use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $table = "payment_settings";
    
    protected $fillable = [
        'account_id','cheque_payable_to','cash_payment_info','account_holder_name','account_number','account_type','bank_name','bank_address','swift_code'];

    
 

}
