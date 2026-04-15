<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionDecision extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Submission $submission
    ) {}

    public function envelope(): Envelope
    {
        $decision = match($this->submission->decision) {
            'accept' => 'Acceptation',
            'minor_revision' => 'Révision mineure demandée',
            'major_revision' => 'Révision majeure demandée',
            'reject' => 'Décision éditoriale',
            default => 'Décision éditoriale',
        };

        return new Envelope(
            subject: "{$decision} - " . config('journal.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submissions.decision',
            with: [
                'authorName' => $this->submission->author->name,
                'title' => $this->submission->title,
                'decision' => $this->submission->decision,
                'decisionLabel' => Submission::getDecisions()[$this->submission->decision] ?? $this->submission->decision,
                'editorNotes' => $this->submission->editor_notes,
                'decisionAt' => $this->submission->decision_at,
            ],
        );
    }
}
