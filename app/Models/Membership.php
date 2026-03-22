<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    protected $fillable = [
        'member_id',
        'membership_type_id',
        'start_date',
        'end_date',
        'amount_paid',
        'payment_method',
        'payment_reference',
        'status',
        'renewal_reminder_sent',
        'renewal_reminder_sent_at',
        'notes',
        'import_source',
        'import_reference',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount_paid' => 'decimal:2',
        'renewal_reminder_sent' => 'boolean',
        'renewal_reminder_sent_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    public function linkedPurchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'legacy_membership_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date >= now();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->isActive() && $this->end_date <= now()->addDays($days);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('end_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->active()
            ->where('end_date', '<=', now()->addDays($days));
    }
}
