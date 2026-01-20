<?php

namespace App\Models\v7;
use Session;

use Illuminate\Database\Eloquent\Model;

class FinancePaymentLog extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'finance_payment_logs';

    protected $fillable = [
        'invoice_id','status','type','screenshot','remarks'
    ];

    

}
