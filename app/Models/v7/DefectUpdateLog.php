<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class DefectUpdateLog extends Model
{
     protected $fillable = [
      'def_id','sub_id','updated_by','old_defect_location','old_defect_type','new_defect_location','new_defect_type','remarks'
    ];

    public function getoldlocation(){
        return $this->belongsTo('App\Models\v7\DefectLocation','old_defect_location');
    }

    public function getoldtype(){
        return $this->belongsTo('App\Models\v7\DefectType','old_defect_type');
    }

    public function getnewlocation(){
        return $this->belongsTo('App\Models\v7\DefectLocation','new_defect_location');
    }

    public function getnewtype(){
        return $this->belongsTo('App\Models\v7\DefectType','new_defect_type');
    }

    public function modifiedby(){
        return $this->belongsTo('App\Models\v7\User','updated_by');
    }

}
