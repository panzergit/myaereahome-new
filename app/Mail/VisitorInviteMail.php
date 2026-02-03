<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitorInviteMail extends Mailable
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
     * Get the message envelope.
     */
    public function build()
    {
        return $this->replyTo("no-reply@panzerplayground.com", 'Aerea Home') // Set Reply-To
            ->subject($this->emailData['subject']) // Email subject
            ->view('emails.visitorinvite', $this->emailData['viewData']); // Blade template for email body
    }
}
