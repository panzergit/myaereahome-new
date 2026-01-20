<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class UserProperty extends Model
{
    protected $fillable = [
      'user_id','property_id','status'
    ];  

    public function propinfo(){
      return $this->hasOne('App\Models\v2\Property','property_id');
  }

}
