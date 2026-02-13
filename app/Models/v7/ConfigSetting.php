<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class ConfigSetting extends Model
{
    protected $fillable = [
        'name', 'value', 'status'
    ];
}
