<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class ModuleSetting extends Model
{
    protected $fillable = [
        'role_id','module_id','view','create','edit','delete'
    ];

    public function Module(){
        return $this->belongsTo('App\Models\v7\Module','module_id');
    }

    public function User(){
        return $this->belongsTo('App\Models\v7\User','emp_id');
    }

}
