<?php

namespace App\Models\v7;
use Session;

use Illuminate\Database\Eloquent\Model;

class FinanceShareSetting extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'finance_share_settings';

    protected $fillable = [
        'account_id','management_fund_share','sinking_fund_share','share_amount','no_of_billing_month','interest','int_percentage','tax','tax_percentage','due_period_value','due_period_type','qrcode_file'
    ];

}
