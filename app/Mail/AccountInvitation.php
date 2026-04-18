<?php

namespace App\Mail;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class AccountInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $author,
        public Submission $submission,
        public User $invitedBy,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Un article vous concernant a été déposé sur Chersotis',
        );
    }

    public function content(): Content
    {
        $claimUrl = URL::temporarySignedRoute(
            'account.claim',
            now()->addDays(config('journal.invitation_expiration_days', 14)),
            ['user' => $this->author->id]
        );

        return new Content(
            markdown: 'emails.account-invitation',
            with: [
                'author' => $this->author,
                'submission' => $this->submission,
                'invitedBy' => $this->invitedBy,
                'claimUrl' => $claimUrl,
                'expirationDate' => now()->addDays(config('journal.invitation_expiration_days', 14))->format('d/m/Y'),
            ],
        );
    }
}
