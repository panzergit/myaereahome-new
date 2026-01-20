<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class PropertyPermission extends Model
{
    protected $fillable = [
        'property_id','module_id','view','create','edit','delete'
    ];

    public function Module(){
        return $this->belongsTo('App\Models\v7\Module','module_id');
    }

    public function property(){
        return $this->belongsTo('App\Models\v7\Property','property_id');
    }

}
