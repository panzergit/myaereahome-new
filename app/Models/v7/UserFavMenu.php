<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class UserFavMenu extends Model
{
    protected $fillable = [
        'account_id','unit_no','user_id','module_id'
    ];

    public function Module(){
        return $this->belongsTo('App\Models\v7\Module','module_id');
    }

    public function User(){
        return $this->belongsTo('App\Models\v7\User','emp_id');
    }

}
