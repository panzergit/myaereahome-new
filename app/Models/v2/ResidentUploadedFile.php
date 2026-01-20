<?php

namespace App\Models\v2;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class ResidentUploadedFile extends Model
{
     protected $fillable = [
     'account_id', 'ref_id','docs_file','docs_file_name','original_file_name','status'
    ];

}
