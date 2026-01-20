<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class ChatBoxCategory extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    //protected $table = 'feedbacks';
    protected $fillable = [
        'account_id','name','status'
    ];

   
   
}
