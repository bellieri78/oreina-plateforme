<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    protected $fillable = [
        'type',
        'filename',
        'total_rows',
        'imported_rows',
        'updated_rows',
        'skipped_rows',
        'error_rows',
        'errors',
        'options',
        'status',
        'user_id',
        'template_id',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'errors' => 'array',
        'options' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ImportTemplate::class, 'template_id');
    }

    // ===== ACCESSORS =====

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'processing' => 'En cours',
            'completed' => 'Termine',
            'failed' => 'Echec',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'secondary',
            'processing' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            default => 'secondary',
        };
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->total_rows === 0) {
            return 0;
        }

        return round(($this->imported_rows + $this->updated_rows) / $this->total_rows * 100, 1);
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        $seconds = $this->completed_at->diffInSeconds($this->started_at);

        if ($seconds < 60) {
            return $seconds . 's';
        }

        return floor($seconds / 60) . 'min ' . ($seconds % 60) . 's';
    }

    // ===== SCOPES =====

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }
}
