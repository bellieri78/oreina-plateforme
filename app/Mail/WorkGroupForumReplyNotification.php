<?php

namespace App\Mail;

use App\Models\WorkGroupForumPost;
use App\Models\WorkGroupForumThread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkGroupForumReplyNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public WorkGroupForumThread $thread, public WorkGroupForumPost $post) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nouvelle réponse — ' . $this->thread->title);
    }

    public function content(): Content
    {
        $workGroup = $this->thread->category->workGroup;

        return new Content(
            markdown: 'emails.work-groups.forum-reply',
            with: [
                'thread' => $this->thread,
                'post' => $this->post,
                'author' => $this->post->author,
                'url' => route('member.work-groups.forum.threads.show', [$workGroup, $this->thread]),
            ],
        );
    }
}
