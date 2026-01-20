<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'name','type','menu_position','group_id','status','oderby'
    ];
}
