<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VolunteerActivityType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    public function activities(): HasMany
    {
        return $this->hasMany(VolunteerActivity::class, 'activity_type_id');
    }

    // ===== SCOPES =====

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ===== STATIC METHODS =====

    public static function getForSelect(): array
    {
        return self::active()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();
    }
}
