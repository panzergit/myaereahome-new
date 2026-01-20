<?php

namespace App\Models\v2;
use Session;

use Illuminate\Database\Eloquent\Model;

class FinanceCreditPayment extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'finance_credit_payments';

    protected $fillable = [
        'account_id','invoice_id','payment_id','credit_amount','credit_notes','received_date'
    ];

   

}
