<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'summary',
        'content',
        'featured_image',
        'category',
        'status',
        'validated_by',
        'validated_at',
        'published_at',
        'validation_notes',
        'is_featured',
        'newsletter_sent',
        'views_count',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'newsletter_sent' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopePendingValidation($query)
    {
        return $query->where('status', 'submitted');
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}
