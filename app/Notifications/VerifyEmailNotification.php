<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends BaseVerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verifyUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Vérifiez votre adresse email — ' . config('journal.name'))
            ->from(config('journal.contact_email'), config('journal.name'))
            ->view('emails.verify-email', [
                'user' => $notifiable,
                'verifyUrl' => $verifyUrl,
            ]);
    }
}
