<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class FeedbackOption extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    //protected $table = 'feedbacks';
    protected $fillable = [
        'account_id','feedback_option',
    ];

    public function takeover_appt(){
        return $this->hasOne('App\UnittakeoverAppointment','unit_no','id')->where('status','=', 1)->orwhere('status','=', 2);
    }

    public function propertyinfo(){
        return $this->belongsTo('App\Property','account_id');
    }

   
}
