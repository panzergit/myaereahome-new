<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use File;

class ClaimNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($claim,$subject,$name)
    {
        $this->claim = $claim;
        $this->subject = $subject;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $records = $this->claim;
        $name = $this->name;
                        
        if($this->claim->attachment !=''){
            $attachement = image_storage_domain().$this->claim->attachment;
            return $this->subject($this->subject)->view('admin.emails.claim', compact('records','name'))->attach($attachement);
        }
        else{
           // $attachement = "";
            return $this->subject($this->subject)->view('admin.emails.claim', compact('records','name'));
        }
            

        
    }
}
