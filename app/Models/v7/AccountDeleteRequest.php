<?php

namespace App\Models\v7;
use Illuminate\Database\Eloquent\Model;

class AccountDeleteRequest extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'name', 'unit', 'requested_by', 'reason', 'status', 'deleted_date'
    ];

    public function user_info(){
        return $this->belongsTo('App\Models\v7\User','requested_by');
    }
    
    public function property(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }
}