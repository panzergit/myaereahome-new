<?php

namespace App\Models\v7;
use DateTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EformRegVehicleDoc extends Model
{   	
    
    protected $fillable = [
        'reg_id','cat','file','file_original'];
    
    
    public function category_name(){
        return $this->belongsTo('App\Models\v7\EformRegVehicleFileCat','cat');
    }


}


