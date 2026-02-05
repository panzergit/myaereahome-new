<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Syncable;
class UserProperty extends Model
{
    use Syncable;
    
    protected $fillable = [
      'user_id','property_id','status'
    ];  

    public function propinfo(){
      return $this->belongsTo('App\Models\v7\Property','property_id');
  }

}
