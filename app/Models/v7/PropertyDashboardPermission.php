<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class PropertyDashboardPermission extends Model
{
    protected $fillable = [
        'property_id','module_id','display_option','display_position'
    ];

    public function Module(){
        return $this->belongsTo('App\Models\v7\Module','module_id');
    }


}
