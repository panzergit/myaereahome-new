<?php

namespace App\Models\v7;
use Session;

use Illuminate\Database\Eloquent\Model;

class FinanceAdvancePayment extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'finance_advance_payments';

    protected $fillable = [
        'account_id','unit_no','invoice_id','payment_id','amount','payment_received_date'
    ];

   

}
