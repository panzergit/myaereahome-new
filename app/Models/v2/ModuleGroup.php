<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class ModuleGroup extends Model
{
    protected $fillable = [
        'name','status','oderby'
    ];
    public function modules(){
        return $this->hasMany('App\Models\v2\Module','parent')->orderBy('parent','asc')->orderBy('orderby','asc');
    }

    public function mobilemodules(){
        return $this->hasMany('App\Models\v2\Module','parent')->where('admin_status',1)->orderBy('parent','asc')->orderBy('orderby','asc');
    }
}
