<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'cards';
    protected $fillable = [
        'account_id','building_no','card','unit_no','remarks'
    ];
    public function getbuilding(){
        return $this->belongsTo('App\Models\v2\Building','building_no');
    }
    public function getunit(){
        return $this->belongsTo('App\Models\v2\Unit','unit_no');
    }

    public function propertyinfo(){
        return $this->belongsTo('App\Models\v2\Property','account_id');
    }

   
}
