<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class ChatAttachment extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'chat_attachments';
    protected $fillable = [
        'user_id','account_id','unit_id','chat_room_id','attachment_image'
    ];

   
   
}
