<?php

namespace App\Models\v2;
use Session;

use Illuminate\Database\Eloquent\Model;

class FinanceInvoicePayment extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'finance_invoice_payments';

    protected $fillable = [
        'invoice_id','payment_option','cheque_amount','cheque_received_date','cheque_bank','cheque_no','bt_received_date','bt_amount_received','cash_amount_received','cash_received_date','transaction_id','online_amount_received','credit_amount','credit_date','credit_notes','manager_received','receipt_no','signature','date_of_signature','status','remarks','payment_received_date','add_amt_received_by','add_amt_received','add_amt_date','add_amt_notes','bounced_cheque_date'
    ];

    public function paymentdetails(){
        return $this->hasMany('App\Models\v2\FinanceInvoicePaymentPaidDetail','payment_id')->orderBy('id','asc');;
    }
    

    public function invoicerecord(){
        return $this->belongsTo('App\Models\v2\FinanceInvoice','invoice_id')->orderBy('id','asc');;
    }

}
