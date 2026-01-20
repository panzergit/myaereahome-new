<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class HomeBannerProperty extends Model
{
    protected $fillable = [
      'banner_id','property_id','status'
    ];  

    public function propinfo(){
      return $this->hasOne('App\Models\v7\Property','property_id');
  }

}
