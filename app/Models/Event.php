<?php

namespace App\Models;

use App\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    public const VIS_PUBLIC = 'public';
    public const VIS_MEMBERS = 'members';
    public const VIS_RESTRICTED = 'restricted';
    public const VIS_GROUP = 'group';

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
        'visibility',
        'audience_roles',
        'work_group_id',
        'meeting_url',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'price' => 'decimal:2',
        'registration_required' => 'boolean',
        'audience_roles' => 'array',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function workGroup(): BelongsTo
    {
        return $this->belongsTo(WorkGroup::class);
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

    public function scopePublicOnly($query)
    {
        return $query->where('visibility', self::VIS_PUBLIC);
    }

    public function scopeVisibleToMember($query, Member $member)
    {
        $roles = $member->effectiveAdherentRoles();
        $groupIds = $member->workGroups()->wherePivot('status', 'active')
            ->pluck('work_groups.id')->all();

        return $query->where(function ($q) use ($roles, $groupIds) {
            $q->whereIn('visibility', [self::VIS_PUBLIC, self::VIS_MEMBERS]);

            if (! empty($roles)) {
                $q->orWhere(function ($r) use ($roles) {
                    $r->where('visibility', self::VIS_RESTRICTED)
                      ->where(function ($rr) use ($roles) {
                          foreach ($roles as $role) {
                              $rr->orWhereJsonContains('audience_roles', $role);
                          }
                      });
                });
            }

            if (! empty($groupIds)) {
                $q->orWhere(function ($g) use ($groupIds) {
                    $g->where('visibility', self::VIS_GROUP)
                      ->whereIn('work_group_id', $groupIds);
                });
            }
        });
    }

    public function isVisibleToMember(?Member $member): bool
    {
        if ($this->visibility === self::VIS_PUBLIC) {
            return true;
        }
        if (! $member || ! $member->isCurrentMember()) {
            return false;
        }

        return match ($this->visibility) {
            self::VIS_MEMBERS => true,
            self::VIS_RESTRICTED => (bool) array_intersect(
                $this->audience_roles ?? [], $member->effectiveAdherentRoles()
            ),
            self::VIS_GROUP => $member->workGroups()
                ->wherePivot('status', 'active')
                ->where('work_groups.id', $this->work_group_id)
                ->exists(),
            default => false,
        };
    }
}
