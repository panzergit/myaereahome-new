<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class MpAdsType extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'mp_ads_types';
    protected $fillable = [
        'account_id','type',
    ];

    

   
}
