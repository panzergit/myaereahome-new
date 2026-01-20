<?php

namespace App\Services;

use Mail;

class PHPMailerService
{
    public function sendMail($toEmail, $subject, $viewName, $viewData)
    {
        $body = view($viewName, $viewData)->render();
        try {
            Mail::send([], [], function ($m) use ($toEmail,$subject,$body) {
                $m->to($toEmail)->subject($subject)->setBody($body, 'text/html');
            });
            \Log::info("Mailgun executed");
        } catch (Exception $e) {
            \Log::error('Mail Error: ' . $e->getMessage());
        }
    }
}

?>