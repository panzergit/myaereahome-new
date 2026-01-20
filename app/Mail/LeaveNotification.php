<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LeaveNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data,$subject,$name)
    {
        $this->data = $data;
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
        $records = $this->data;
        $name = $this->name;

        return $this->subject($this->subject)->view('admin.emails.leave', compact('records','name'));          
    }
}
