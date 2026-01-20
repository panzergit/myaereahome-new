<?php

namespace App\Models\v2;
use Session;

use Illuminate\Database\Eloquent\Model;

class FinanceInvoicePaymentPaidDetail extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'finance_invoice_payment_paid_details';

    protected $fillable = [
        'account_id','unit_no','invoice_id','payment_id','detail_id','type','amount','payment_received_date'
    ];
    public function detail(){
        return $this->belongsTo('App\Models\v2\FinanceInvoicePaymentDetail','detail_id');
    }
    public function paidtype(){
        return $this->belongsTo('App\Models\v2\FinanceReferenceType','type');
    }

}
