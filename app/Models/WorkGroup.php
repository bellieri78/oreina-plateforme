<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class WorkGroup extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'color', 'icon', 'website_url', 'is_active',
        'has_resources', 'has_collaborative_space', 'collaborative_space_url',
        'has_forum', 'join_policy', 'cover_image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_resources' => 'boolean',
        'has_collaborative_space' => 'boolean',
        'has_forum' => 'boolean',
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
            ->withPivot('role', 'joined_at', 'status', 'requested_at')
            ->withTimestamps();
    }

    public function activeMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'active');
    }

    public function coordinators(): BelongsToMany
    {
        return $this->members()
            ->wherePivot('status', 'active')
            ->wherePivot('role', 'coordinator');
    }

    public function pendingRequests(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'pending');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(WorkGroupResource::class);
    }

    public function forumCategories(): HasMany
    {
        return $this->hasMany(WorkGroupForumCategory::class);
    }

    public function forumThreads(): HasManyThrough
    {
        return $this->hasManyThrough(WorkGroupForumThread::class, WorkGroupForumCategory::class);
    }

    public function coverUrl(): ?string
    {
        return $this->cover_image ? \Storage::url($this->cover_image) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isCoordinator(?Member $member): bool
    {
        if (! $member) {
            return false;
        }

        return $this->members()
            ->wherePivot('status', 'active')
            ->wherePivot('role', 'coordinator')
            ->where('members.id', $member->id)
            ->exists();
    }

    public function membershipStatusFor(?Member $member): ?string
    {
        if (! $member) {
            return null;
        }

        $pivot = $this->members()->where('members.id', $member->id)->first()?->pivot;

        return $pivot?->status;
    }
}
