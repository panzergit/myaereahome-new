<?php

namespace App\Models\v7;;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class ChatBoxTnc extends Model
{
    protected $table ="chat_box_tnc";
     protected $fillable = [
        'terms_and_condition','status'
    ];


}
