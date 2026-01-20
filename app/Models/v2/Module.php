<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'name','type','group_id','status','oderby'
    ];
}
