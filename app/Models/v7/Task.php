<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
      'title','project_id', 'added_by','assigned_to','start_on','deadline','notes'
    ];

    public function assignedto(){
        return $this->belongsTo('App\Models\v7\User','assigned_to');
    }

    public function addedby(){
        return $this->belongsTo('App\Models\v7\User','added_by');
    }

    public function project(){
        return $this->belongsTo('App\Models\v7\Project','project_id');
    }

    
}
