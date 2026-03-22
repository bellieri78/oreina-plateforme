<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerParticipation extends Model
{
    // Statuts
    public const STATUS_REGISTERED = 'registered';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_ATTENDED = 'attended';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'member_id',
        'volunteer_activity_id',
        'status',
        'hours_worked',
        'notes',
    ];

    protected $casts = [
        'hours_worked' => 'decimal:2',
    ];

    // ===== RELATIONSHIPS =====

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(VolunteerActivity::class, 'volunteer_activity_id');
    }

    // ===== ACCESSORS =====

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_REGISTERED => 'info',
            self::STATUS_CONFIRMED => 'primary',
            self::STATUS_ATTENDED => 'success',
            self::STATUS_ABSENT => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            default => 'secondary',
        };
    }

    // ===== SCOPES =====

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_REGISTERED, self::STATUS_CONFIRMED, self::STATUS_ATTENDED]);
    }

    public function scopeAttended($query)
    {
        return $query->where('status', self::STATUS_ATTENDED);
    }

    // ===== STATIC METHODS =====

    public static function getStatuses(): array
    {
        return [
            self::STATUS_REGISTERED => 'Inscrit',
            self::STATUS_CONFIRMED => 'Confirme',
            self::STATUS_ATTENDED => 'Present',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_CANCELLED => 'Annule',
        ];
    }
}
