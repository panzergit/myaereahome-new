<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class FacilityType extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    //protected $table = 'feedbacks';
    protected $fillable = [
        'account_id','facility_type','timing','blockout_days','notes','facility_image','calendar_availability_start','next_booking_allowed','allowed_booking_for','next_booking_allowed_days','allowed_size','payment_required','booking_fee','booking_deposit','cut_of_days','cut_of_amount_percentage','status'
    ];

    public function propertyinfo(){
        return $this->belongsTo('App\Models\v2\Property','account_id');
    }
   
}
