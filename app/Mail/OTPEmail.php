<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTPEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $emailData = null;

    /**
     * Create a new message instance.
     */
    public function __construct($eData)
    {
        $this->emailData = $eData;
    }
    /**
     * Build the message.
     */
    public function build()
    {
        $viewData = $this->emailData;
        return $this->replyTo("no-reply@panzerplayground.com", 'Aerea Home') // Set Reply-To
                    ->subject($this->emailData['subject']) // Email subject
                    ->view('emails.verifyotp')// Email view file
                    ->with($viewData);
    }
}
