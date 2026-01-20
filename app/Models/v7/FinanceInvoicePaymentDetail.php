<?php

namespace App\Models\v7;
use Session;

use Illuminate\Database\Eloquent\Model;

class FinanceInvoicePaymentDetail extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'finance_invoice_payment_details';

    protected $fillable = [
        'account_id','unit_no','invoice_id','payment_id','reference_type','reference_no','reference_invoice','order','detail','total_amount','amount','balance','payment_received_date','payment_status','received_amount','paid_by_credit','due_date'
    ];

    public function referencetypes(){
        return $this->belongsTo('App\Models\v7\FinanceReferenceType','reference_type');
    }

    public function paymenthistory(){
        return $this->hasMany('App\Models\v7\FinanceInvoicePaymentPaidDetail','detail_id');
    }

    public function unitinfo(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
    }
    public function invoiceinfo(){
        return $this->belongsTo('App\Models\v7\FinanceInvoice','invoice_id');
    }

    public function paymentdetails(){
        return $this->hasMany('App\Models\v7\FinanceInvoicePaymentPaidDetail','detail_id');
    }

}
