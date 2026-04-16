<?php
namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSubmissionAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nouvelle soumission — ' . config('journal.name'));
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submissions.new-alert',
            with: [
                'submission' => $this->submission,
                'authorName' => $this->submission->author?->name ?? 'Inconnu',
                'queueUrl' => url('/extranet/revue/file-attente'),
            ],
        );
    }
}
