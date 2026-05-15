<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkGroupForumPost extends Model
{
    protected $fillable = ['work_group_forum_thread_id', 'member_id', 'content'];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(WorkGroupForumThread::class, 'work_group_forum_thread_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * Contenu sûr pour affichage : HTML échappé, retours à la ligne,
     * URLs http(s) transformées en liens. Anti-XSS : on échappe AVANT
     * de linkifier, le résultat ne doit être sorti qu'avec {!! !!}.
     */
    public function renderedContent(): string
    {
        $escaped = e($this->content);
        $linked = preg_replace(
            '~(https?://[^\s<]+)~i',
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
            $escaped
        );

        return nl2br($linked);
    }

    public function isFirstInThread(): bool
    {
        return $this->thread
            ? $this->id === $this->thread->posts()->oldest()->value('id')
            : false;
    }
}
