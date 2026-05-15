<?php

namespace App\Mail;

use App\Models\Member;
use App\Models\WorkGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkGroupJoinRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public WorkGroup $workGroup, public Member $applicant) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nouvelle demande pour le groupe ' . $this->workGroup->name);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.work-groups.requested',
            with: [
                'workGroup' => $this->workGroup,
                'applicant' => $this->applicant,
                'manageUrl' => route('member.work-groups.show', $this->workGroup) . '?tab=manage',
            ],
        );
    }
}
