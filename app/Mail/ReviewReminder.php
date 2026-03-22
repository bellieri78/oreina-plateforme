<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewReminder extends Mailable implements ShouldQueue
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
            subject: 'Rappel: Evaluation en attente - OREINA',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.review-reminder',
            with: [
                'review' => $this->review,
                'submission' => $this->review->submission,
                'dueDate' => $this->review->due_date?->format('d/m/Y'),
                'isOverdue' => $this->review->due_date && $this->review->due_date < now(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
