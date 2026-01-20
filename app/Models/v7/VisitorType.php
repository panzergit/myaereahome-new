<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class VisitorType extends Model
{

    //protected $table = 'feedbacks';
    protected $fillable = [
        'account_id','visiting_purpose','id_required','limit_set','qr_scan_limit','end_date_required','compinfo_required','cat_dropdown','sub_category'
    ];

    public function propertyinfo(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }

    public function subcategory(){
        return $this->hasMany('App\Models\v7\VisitorTypeSubcategory','type_id');
    }


   
}
