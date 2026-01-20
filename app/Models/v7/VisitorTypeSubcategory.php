<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class VisitorTypeSubcategory extends Model
{

    protected $table = 'visitor_type_subcategories';
    protected $fillable = [
        'account_id','type_id','sub_category','status'
    ];

    public function propertyinfo(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }
  



   
}
