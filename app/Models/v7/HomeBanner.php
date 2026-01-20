<?php

namespace App\Models\v7;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
     protected $fillable = [
     'account_id', 'assigned_property','banner_title','banner_image','banner_url_type','banner_url','module','ref_id','display_order','status'
    ];

    public function bannerproperties(){
        return $this->hasMany('App\Models\v7\HomeBannerProperty','banner_id');
    }

}
