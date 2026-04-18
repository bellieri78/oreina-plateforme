<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LepisArticleReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Un article vous a été transmis — Bulletin Lepis',
        );
    }

    public function content(): Content
    {
        // Motifs saisis par l'éditeur Chersotis lors du rejet+reco Lepis
        $queueTransition = $this->submission->transitions()
            ->where('to_status', 'rejected_pending_lepis')
            ->orderByDesc('created_at')
            ->first();

        return new Content(
            markdown: 'emails.lepis-article-received',
            with: [
                'submission' => $this->submission,
                'author' => $this->submission->author,
                'chersotisNotes' => $queueTransition?->notes,
            ],
        );
    }
}
