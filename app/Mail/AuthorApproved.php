<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuthorApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Approbation auteur reçue — ' . $this->submission->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submissions.author-approved',
            with: [
                'authorName' => $this->submission->author->name,
                'title' => $this->submission->title,
                'adminUrl' => route('admin.submissions.show', $this->submission),
            ],
        );
    }
}
