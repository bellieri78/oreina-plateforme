<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkGroupForumThread extends Model
{
    protected $fillable = [
        'work_group_forum_category_id', 'member_id', 'title',
        'is_pinned', 'is_locked', 'last_posted_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'last_posted_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(WorkGroupForumCategory::class, 'work_group_forum_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(WorkGroupForumPost::class);
    }

    public function firstPost(): ?WorkGroupForumPost
    {
        return $this->posts()->oldest()->first();
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('is_pinned')->orderByDesc('last_posted_at');
    }

    public function subscribers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Member::class,
            'work_group_forum_thread_subscriptions',
            'work_group_forum_thread_id',
            'member_id'
        )->withTimestamps();
    }

    public function isSubscribed(?Member $member): bool
    {
        return $member
            ? $this->subscribers()->where('members.id', $member->id)->exists()
            : false;
    }
}
