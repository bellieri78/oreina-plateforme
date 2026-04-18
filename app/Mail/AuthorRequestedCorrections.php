<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to editorial staff (assigned editor + layout editor) when the author
 * signals corrections on the layout. Caller must iterate and dispatch per recipient.
 */
class AuthorRequestedCorrections extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Submission $submission,
        public string $comment,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Corrections demandées par l\'auteur — ' . $this->submission->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submissions.author-requested-corrections',
            with: [
                'authorName' => $this->submission->author?->name ?? 'Auteur inconnu',
                'title' => $this->submission->title,
                'comment' => $this->comment,
                'adminUrl' => route('admin.submissions.layout', $this->submission),
            ],
        );
    }
}
