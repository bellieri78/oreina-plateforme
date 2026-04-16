<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ReviewInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Review $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation a evaluer un manuscrit - OREINA',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.review-invitation',
            with: [
                'review' => $this->review,
                'submission' => $this->review->submission,
                'dueDate' => $this->review->due_date?->format('d/m/Y'),
                'assignedBy' => $this->review->assignedBy,
                'respondUrl' => URL::signedRoute('review.respond', ['review' => $this->review->id]),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
