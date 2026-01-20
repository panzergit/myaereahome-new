<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class DocsCategory extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'condodoc_categories';
    protected $fillable = [
        'account_id','docs_category','status'
    ];

    public function files(){
        return $this->hasMany('App\Models\v7\CondodocFile','cat_id');
    }

    

   
}
