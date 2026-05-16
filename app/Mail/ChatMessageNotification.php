<?php

namespace App\Mail;

use App\Models\Conversation;
use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChatMessageNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Conversation $conversation, public Member $sender) {}

    public function envelope(): Envelope
    {
        $name = $this->sender->first_name ?? 'Un adhérent';
        return new Envelope(subject: "Nouveau message de {$name} sur OREINA");
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chat.new-message',
            with: [
                'sender' => $this->sender,
                'url' => route('member.chat', ['c' => $this->conversation->id]),
            ],
        );
    }
}
