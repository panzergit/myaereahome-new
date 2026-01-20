<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class MpAdsImage extends Model
{
    protected $table = 'mp_ads_images';
     protected $fillable = [
        'ref_id','upload','status'
    ];

}
