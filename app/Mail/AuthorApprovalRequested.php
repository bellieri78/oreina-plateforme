<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to the submission's author when the editorial team requests their
 * approval on the final layout. Caller: Mail::to($submission->author)->queue(...).
 */
class AuthorApprovalRequested extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre article est prêt pour publication — ' . config('journal.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submissions.author-approval-requested',
            with: [
                'authorName' => $this->submission->author?->name ?? 'Auteur inconnu',
                'title' => $this->submission->title,
                'showUrl' => route('journal.submissions.show', $this->submission),
            ],
        );
    }
}
