<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LepisSuggestion extends Model
{
    protected $fillable = [
        'member_id', 'title', 'content', 'attachment_path', 'status', 'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
