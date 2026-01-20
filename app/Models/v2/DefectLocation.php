<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class DefectLocation extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    //protected $table = 'feedbacks';
    protected $fillable = [
        'account_id','defect_location',
    ];


    public function types(){
        return $this->hasMany('App\Models\v2\DefectType','location_id');
    }
    

   
}
