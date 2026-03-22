<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportLog extends Model
{
    protected $fillable = [
        'type',
        'filename',
        'total_rows',
        'filters',
        'columns',
        'format',
        'user_id',
        'template_id',
    ];

    protected $casts = [
        'filters' => 'array',
        'columns' => 'array',
    ];

    // ===== RELATIONSHIPS =====

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ExportTemplate::class, 'template_id');
    }

    // ===== ACCESSORS =====

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'members' => 'Contacts',
            'memberships' => 'Adhesions',
            'donations' => 'Dons',
            'volunteer' => 'Benevolat',
            default => ucfirst($this->type),
        };
    }

    // ===== SCOPES =====

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }
}
