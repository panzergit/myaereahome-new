<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class UserProperty extends Model
{
    protected $fillable = [
      'user_id','property_id','status'
    ];  

    public function propinfo(){
      return $this->belongsTo('App\Models\v7\Property','property_id');
  }

}
