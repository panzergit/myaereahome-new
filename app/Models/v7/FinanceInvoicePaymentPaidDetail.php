<?php

namespace App\Models\v7;
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
        'account_id','unit_no','invoice_id','payment_id','detail_id','type','paid_by_credit','amount','payment_received_date'
    ];
    public function detail(){
        return $this->belongsTo('App\Models\v7\FinanceInvoicePaymentDetail','detail_id');
    }
    public function paidtype(){
        return $this->belongsTo('App\Models\v7\FinanceReferenceType','type');
    }
    public function unitinfo(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
    }
    public function invoiceinfo(){
        return $this->belongsTo('App\Models\v7\FinanceInvoice','invoice_id');
    }

}
