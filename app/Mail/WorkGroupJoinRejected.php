<?php

namespace App\Mail;

use App\Models\WorkGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkGroupJoinRejected extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public WorkGroup $workGroup) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Votre demande pour ' . $this->workGroup->name);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.work-groups.rejected',
            with: ['workGroup' => $this->workGroup],
        );
    }
}
