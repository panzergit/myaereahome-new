<?php

namespace App\Models\v7;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class MagazineFile extends Model
{
     protected $fillable = [
     'account_id', 'cat_id','docs_file','file_image','docs_file_name','original_file_name','status'
    ];

    public function category(){
        return $this->belongsTo('App\Models\v7\MagazineCategory','cat_id');
    }

    

}
