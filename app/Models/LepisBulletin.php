<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LepisBulletin extends Model
{
    protected $fillable = [
        'title', 'issue_number', 'quarter', 'year', 'pdf_path', 'published_at', 'is_published',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getQuarterLabelAttribute(): string
    {
        return match ($this->quarter) {
            'Q1' => 'Printemps',
            'Q2' => 'Été',
            'Q3' => 'Automne',
            'Q4' => 'Hiver',
            default => $this->quarter,
        };
    }
}
