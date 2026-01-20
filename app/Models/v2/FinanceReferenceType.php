<?php

namespace App\Models\v2;
use Session;

use Illuminate\Database\Eloquent\Model;

class FinanceReferenceType extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'finance_reference_types';

    protected $fillable = [
        'reference_type','reference_name','status'
    ];

}
