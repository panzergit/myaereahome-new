<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class EformSetting extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'eform_settings';
    protected $fillable = [
        'account_id','eform_type','general_info','refund_amount','padding_lift_fee','payable_to','payment_mode_cheque','payment_mode_bank','payment_mode_cash','official_notes','hacking_work_permitted_days','hacking_work_not_permitted_saturday','hacking_work_not_permitted_sunday','hacking_work_not_permitted_holiday','hacking_work_start_time','hacking_work_end_time'
    ];

    public function gettype(){
        return $this->belongsTo('App\Models\v7\Module','eform_type');
    }

    
   
}
