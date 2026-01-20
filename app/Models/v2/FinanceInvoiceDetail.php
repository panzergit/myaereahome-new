<?php

namespace App\Models\v2;
use Session;

use Illuminate\Database\Eloquent\Model;

class FinanceInvoiceDetail extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'finance_invoice_details';

    protected $fillable = [
        'info_id','detail_type','reference_type','reference','description','amount','qty','tot_amount','status'
    ];

    public function types(){
        return $this->belongsTo('App\Models\v2\FinanceReferenceType','reference_type');
    }

}
