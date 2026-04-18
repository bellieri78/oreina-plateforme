<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LepisQueueNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle soumission en file Lepis — Chersotis',
        );
    }

    public function content(): Content
    {
        $transition = $this->submission->transitions()
            ->where('to_status', 'rejected_pending_lepis')
            ->orderByDesc('created_at')
            ->first();

        return new Content(
            markdown: 'emails.lepis-queue-notification',
            with: [
                'submission' => $this->submission,
                'author' => $this->submission->author,
                'notes' => $transition?->notes,
                'actor' => $transition?->actor,
            ],
        );
    }
}
