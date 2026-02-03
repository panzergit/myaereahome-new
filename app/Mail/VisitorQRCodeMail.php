<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VisitorQRCodeMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $emailData = null;

    public function __construct($eData)
    {
        $this->emailData = $eData;
    }

    public function build()
    {
        return $this->replyTo("no-reply@panzerplayground.com", 'Aerea Home') // Set Reply-To
            ->subject($this->emailData['subject']) // Email subject
            ->view('emails.visitorqrcode', $this->emailData['viewData']); // Blade template for email body
    }
}
