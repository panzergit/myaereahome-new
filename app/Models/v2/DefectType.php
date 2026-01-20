<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class DefectType extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    //protected $table = 'feedbacks';
    protected $fillable = [
       'location_id', 'account_id','defect_type',
    ];

    

   
}
