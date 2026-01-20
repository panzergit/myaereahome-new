<?php

namespace App\Models\v7;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class CondodocFile extends Model
{
     protected $fillable = [
     'account_id', 'cat_id','docs_file','docs_file_name','original_file_name','status'
    ];

    public function category(){
        return $this->belongsTo('App\Models\v7\DocsCategory','cat_id');
    }

    

}
