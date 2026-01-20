<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'logo','company_name','company_reg_no','company_email','company_contact','company_address','currency','prefix_code','no_of_digits','payroll_notes'
    ];

}
