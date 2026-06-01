<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Member;

class Article extends Model
{
    use SoftDeletes;

    public const VIS_PUBLIC = 'public';
    public const VIS_MEMBERS = 'members';
    public const VIS_RESTRICTED = 'restricted';

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'summary',
        'content',
        'featured_image',
        'document_path',
        'document_name',
        'category',
        'status',
        'validated_by',
        'validated_at',
        'published_at',
        'validation_notes',
        'is_featured',
        'newsletter_sent',
        'views_count',
        'visibility',
        'audience_roles',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'newsletter_sent' => 'boolean',
        'audience_roles' => 'array',
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

    public function scopePublicOnly($query)
    {
        return $query->where('visibility', self::VIS_PUBLIC);
    }

    public function scopeVisibleToMember($query, Member $member)
    {
        $roles = $member->effectiveAdherentRoles();

        return $query->where(function ($q) use ($roles) {
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
            default => false,
        };
    }
}
