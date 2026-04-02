<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class WorkGroup extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'color', 'icon', 'website_url', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (WorkGroup $wg) {
            if (empty($wg->slug)) {
                $wg->slug = Str::slug($wg->name);
            }
        });
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'work_group_member')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function leaders(): BelongsToMany
    {
        return $this->members()->wherePivot('role', 'leader');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
