<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMember extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Member $member
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue chez OREINA - Les Lépidoptères de France',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.members.welcome',
            with: [
                'memberName' => $this->member->first_name,
                'memberNumber' => $this->member->member_number,
                'expiresAt' => $this->member->membership_expires_at,
            ],
        );
    }
}
