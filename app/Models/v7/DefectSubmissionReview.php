<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class DefectSubmissionReview extends Model
{
     protected $fillable = [
      'def_submission_id','user_id','status','remarks'
    ];

   

}
