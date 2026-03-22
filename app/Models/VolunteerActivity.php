<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VolunteerActivity extends Model
{
    // Statuts
    public const STATUS_PLANNED = 'planned';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'title',
        'description',
        'activity_type_id',
        'structure_id',
        'activity_date',
        'start_time',
        'end_time',
        'location',
        'city',
        'organizer_id',
        'status',
        'max_participants',
        'notes',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // ===== RELATIONSHIPS =====

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(VolunteerActivityType::class, 'activity_type_id');
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'organizer_id');
    }

    public function participations(): HasMany
    {
        return $this->hasMany(VolunteerParticipation::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'volunteer_participations')
            ->withPivot(['status', 'hours_worked', 'notes'])
            ->withTimestamps();
    }

    public function confirmedParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivotIn('status', ['confirmed', 'attended']);
    }

    public function attendedParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivot('status', 'attended');
    }

    // ===== ACCESSORS =====

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PLANNED => 'info',
            self::STATUS_ONGOING => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'secondary',
            default => 'secondary',
        };
    }

    public function getDurationAttribute(): ?float
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        return $this->end_time->diffInMinutes($this->start_time) / 60;
    }

    public function getTotalHoursAttribute(): float
    {
        return $this->participations()
            ->whereIn('status', ['attended'])
            ->sum('hours_worked') ?? 0;
    }

    // ===== METHODS =====

    public function isUpcoming(): bool
    {
        return $this->activity_date >= now()->startOfDay()
            && $this->status === self::STATUS_PLANNED;
    }

    public function isPast(): bool
    {
        return $this->activity_date < now()->startOfDay();
    }

    public function canRegister(): bool
    {
        if ($this->status !== self::STATUS_PLANNED) {
            return false;
        }

        if ($this->max_participants && $this->confirmedParticipants()->count() >= $this->max_participants) {
            return false;
        }

        return true;
    }

    public function addParticipant(Member $member, string $status = 'registered'): void
    {
        if (!$this->participants()->where('member_id', $member->id)->exists()) {
            $this->participants()->attach($member->id, ['status' => $status]);
        }
    }

    public function removeParticipant(Member $member): void
    {
        $this->participants()->detach($member->id);
    }

    public function markParticipantAttended(Member $member, ?float $hours = null): void
    {
        $this->participants()->updateExistingPivot($member->id, [
            'status' => 'attended',
            'hours_worked' => $hours ?? $this->duration,
        ]);
    }

    // ===== SCOPES =====

    public function scopeUpcoming($query)
    {
        return $query->where('activity_date', '>=', now()->startOfDay())
            ->where('status', self::STATUS_PLANNED)
            ->orderBy('activity_date');
    }

    public function scopePast($query)
    {
        return $query->where('activity_date', '<', now()->startOfDay())
            ->orderByDesc('activity_date');
    }

    public function scopeOfType($query, int $typeId)
    {
        return $query->where('activity_type_id', $typeId);
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeInYear($query, int $year)
    {
        return $query->whereYear('activity_date', $year);
    }

    // ===== STATIC METHODS =====

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PLANNED => 'Planifie',
            self::STATUS_ONGOING => 'En cours',
            self::STATUS_COMPLETED => 'Termine',
            self::STATUS_CANCELLED => 'Annule',
        ];
    }
}
