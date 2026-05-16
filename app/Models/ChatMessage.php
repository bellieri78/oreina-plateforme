<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = ['conversation_id', 'sender_id', 'content'];

    protected $casts = ['deleted_at' => 'datetime'];

    public function conversation(): BelongsTo { return $this->belongsTo(Conversation::class); }
    public function sender(): BelongsTo { return $this->belongsTo(Member::class, 'sender_id'); }

    public function isDeleted(): bool { return $this->deleted_at !== null; }

    public function renderedBody(): string
    {
        if ($this->isDeleted()) {
            return '<em>message supprimé</em>';
        }
        $escaped = e($this->content);
        $linked = preg_replace(
            '~(https?://[^\s<]+)~i',
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
            $escaped
        );
        return nl2br($linked);
    }
}
