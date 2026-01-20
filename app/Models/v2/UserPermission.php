<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $fillable = [
        'account_id','unit_no','user_id','module_id','view','create','edit','delete'
    ];

    public function Module(){
        return $this->belongsTo('App\Models\v2\Module','module_id');
    }

    public function User(){
        return $this->belongsTo('App\Models\v2\User','emp_id');
    }

}
