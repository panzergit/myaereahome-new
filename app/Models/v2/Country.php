<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'countries';
    protected $fillable = [
        'country_name','status'
    ];

    

   
}
