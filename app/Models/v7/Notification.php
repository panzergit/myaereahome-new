<?php

namespace App\Models\v7;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
     protected $fillable = [
        'emp_id', 'type', 'status','ref_id','notification'
    ];

    public function noOfNotification($type){
    	$user = Auth::user();
    	$records = Notification::where('emp_id', $user->id)
    	->where('type', $type)
    	->get();

    	return $records;

    }
}
