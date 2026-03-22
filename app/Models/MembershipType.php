<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration_months',
        'is_active',
        'sort_order',
        'is_legacy',
        'valid_from',
        'valid_until',
        'for_foreign',
        'for_organization',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_legacy' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'for_foreign' => 'boolean',
        'for_organization' => 'boolean',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeLegacy($query)
    {
        return $query->where('is_legacy', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_legacy', false);
    }

    public function scopeForOrganizations($query)
    {
        return $query->where('for_organization', true);
    }

    public function scopeForIndividuals($query)
    {
        return $query->where('for_organization', false);
    }

    public function isValidForDate(?string $date = null): bool
    {
        $date = $date ? \Carbon\Carbon::parse($date) : now();

        if ($this->valid_from && $date->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $date->gt($this->valid_until)) {
            return false;
        }

        return true;
    }
}
