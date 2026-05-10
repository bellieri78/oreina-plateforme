<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LaboLepidoProposition extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data)
    {
    }

    public function envelope(): Envelope
    {
        $type = $this->data['type_proposition'] === 'animer' ? 'Animation' : 'Suggestion';

        return new Envelope(
            subject: "[Labo Lépidos] {$type} — {$this->data['sujet']}",
            replyTo: [new Address($this->data['email'], $this->data['nom'])],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.labo-lepido-proposition',
            with: ['data' => $this->data],
        );
    }
}
