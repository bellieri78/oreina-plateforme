<?php
namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewCompleted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Review $review) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Évaluation déposée — ' . config('journal.name'));
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reviews.completed',
            with: [
                'reviewerName' => $this->review->reviewer?->name,
                'submissionTitle' => $this->review->submission?->title,
                'recommendation' => \App\Models\Review::getRecommendations()[$this->review->recommendation] ?? $this->review->recommendation,
                'showUrl' => url('/extranet/submissions/' . $this->review->submission_id),
            ],
        );
    }
}
