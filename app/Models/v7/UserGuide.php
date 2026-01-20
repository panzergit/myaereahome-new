<?php

namespace App\Models\v7;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class UserGuide extends Model
{
     protected $fillable = [
     'account_id', 'cat_id','url_type','url_link','docs_file','file_image','docs_file_name','original_file_name','status'
    ];

   
}
