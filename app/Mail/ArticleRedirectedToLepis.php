<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticleRedirectedToLepis extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre article a été transmis au bulletin Lepis',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.article-redirected-to-lepis',
            with: [
                'submission' => $this->submission,
                'author' => $this->submission->author,
            ],
        );
    }
}
