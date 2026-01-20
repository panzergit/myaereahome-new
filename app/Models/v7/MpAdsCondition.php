<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class MpAdsCondition extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'mp_ads_conditions';
    protected $fillable = [
        'account_id','type',
    ];

    

   
}
