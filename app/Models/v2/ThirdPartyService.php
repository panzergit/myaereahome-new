<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class ThirdPartyService extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'third_party_services';
    protected $fillable = [
        'account_id','name',
    ];


    
   
}
