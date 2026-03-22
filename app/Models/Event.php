<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organizer_id',
        'title',
        'slug',
        'description',
        'content',
        'featured_image',
        'event_type',
        'start_date',
        'end_date',
        'location_name',
        'location_address',
        'location_city',
        'latitude',
        'longitude',
        'max_participants',
        'registration_required',
        'registration_url',
        'price',
        'status',
        'published_at',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'price' => 'decimal:2',
        'registration_required' => 'boolean',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function isUpcoming(): bool
    {
        return $this->start_date > now();
    }

    public function isPast(): bool
    {
        return $this->end_date ? $this->end_date < now() : $this->start_date < now();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('start_date', '<', now());
    }

    public function scopeType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([
            $this->location_name,
            $this->location_address,
            $this->location_city,
        ]);

        return implode(', ', $parts);
    }
}
