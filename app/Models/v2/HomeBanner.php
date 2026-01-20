<?php

namespace App\Models\v2;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
     protected $fillable = [
     'account_id','banner_title','banner_image','display_order','status'
    ];

    public function bannerproperties(){
        return $this->hasMany('App\Models\v2\HomeBannerProperty','banner_id');
    }

}
