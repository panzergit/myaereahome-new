<?php

namespace App\Models\v7;
use Session;

use Illuminate\Database\Eloquent\Model;

class FinanceInvoiceInfo extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'finance_invoice_info';

    protected $fillable = [
        'account_id','created_by','month','due_date','batch_no','comp_name','comp_address','bank_name','account_no','bank_code','bank_address','status','notes','notification_status'
    ];

    public function details(){
        return $this->hasMany('App\Models\v7\FinanceInvoiceDetail','info_id')->orderBy('id','asc');;
    }

    public function admininfo(){
        return $this->belongsTo('App\Models\v7\User','created_by');
    }

    public function invoices(){
        return $this->hasMany('App\Models\v7\FinanceInvoice','info_id');
    }

   

}
