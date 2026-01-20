<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class ResidentFileSubmission extends Model
{
     protected $fillable = [
      'account_id','unit_no','cat_id','notes', 'user_id','status','remarks'
    ];

    public function user(){
        return $this->belongsTo('App\Models\v7\User','user_id');
    }

    public function files(){
        return $this->hasMany('App\Models\v7\ResidentUploadedFile','ref_id');
    }

    public function category(){
        return $this->belongsTo('App\Models\v7\DocsCategory','cat_id');
    }

    public function getunit(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
    }

   

}
