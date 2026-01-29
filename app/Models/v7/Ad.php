<?php

namespace App\Models\v7;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
     protected $fillable = [
     'account_id', 'assigned_property','ad_title','ad_image','email','phone','website','description','status','display_order'
    ];

    public function getAdImageAttribute($value)
    {
        if(empty($value)) return $value;
        return is_primary_domain() ? upload_path($value) : $value;
    }
}
