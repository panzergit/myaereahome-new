<?php

namespace App\Models\v7;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
     protected $fillable = [
     'account_id', 'name','status','title','first_name','middle_name','last_name','suffix','supplier_display_name','company_name','email','phone_number','mobile_number','fax','others','website','address1','address2','city','province','postal_code','country','notes','attachement','business_id','billing_rate','payment_term','account_no','expense_category','opening_balance','opening_balance_date'
    ];

}
