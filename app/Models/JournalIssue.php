<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalIssue extends Model
{
    protected $fillable = [
        'volume_number',
        'issue_number',
        'title',
        'slug',
        'description',
        'cover_image',
        'pdf_file',
        'publication_date',
        'status',
        'doi',
        'page_count',
    ];

    protected $casts = [
        'publication_date' => 'date',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function articles(): HasMany
    {
        return $this->submissions()->where('status', 'published');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeVolume($query, int $volume)
    {
        return $query->where('volume_number', $volume);
    }

    public function getFullReferenceAttribute(): string
    {
        return "Volume {$this->volume_number}, Numéro {$this->issue_number}";
    }

    public function getCitationAttribute(): string
    {
        $date = $this->publication_date?->format('Y') ?? '';
        return "Oreina, {$this->volume_number}({$this->issue_number}), {$date}";
    }
}
