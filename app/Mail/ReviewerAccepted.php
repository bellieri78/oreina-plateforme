<?php
namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewerAccepted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Review $review) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Invitation acceptée — ' . config('journal.name'));
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reviews.accepted',
            with: [
                'review' => $this->review,
                'reviewerName' => $this->review->reviewer?->name,
                'submissionTitle' => $this->review->submission?->title,
                'dueDate' => $this->review->due_date?->format('d/m/Y'),
            ],
        );
    }
}
