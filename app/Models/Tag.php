<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
        'source',
    ];

    // Predefined colors for auto-generated tags
    public const COLORS = [
        '#ef4444', // red
        '#f97316', // orange
        '#eab308', // yellow
        '#22c55e', // green
        '#14b8a6', // teal
        '#3b82f6', // blue
        '#8b5cf6', // violet
        '#ec4899', // pink
        '#6b7280', // gray
    ];

    protected static function booted(): void
    {
        static::creating(function (Tag $tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
            if (empty($tag->color)) {
                // Assign a random color from the palette
                $tag->color = self::COLORS[array_rand(self::COLORS)];
            }
        });
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_tag')
            ->withPivot(['source'])
            ->withTimestamps();
    }

    /**
     * Find or create a tag by name
     */
    public static function findOrCreateByName(string $name, string $source = 'manual', ?string $color = null): self
    {
        $slug = Str::slug($name);

        return self::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'source' => $source,
                'color' => $color ?? self::COLORS[array_rand(self::COLORS)],
            ]
        );
    }

    /**
     * Get members count
     */
    public function getMembersCountAttribute(): int
    {
        return $this->members()->count();
    }
}
