<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = ['member_id', 'content'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
