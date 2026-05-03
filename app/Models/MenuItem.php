<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class MenuItem extends Model
{
    public const LOCATION_HEADER = 'header';
    public const LOCATION_FOOTER = 'footer';

    protected $fillable = [
        'parent_id',
        'location',
        'label',
        'url',
        'sort_order',
        'is_active',
        'open_in_new_tab',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeForLocation($q, string $location)
    {
        return $q->where('location', $location);
    }

    /** Un item dont parent_id != null ne peut pas avoir d'enfants (limite 2 niveaux). */
    public function canHaveChildren(): bool
    {
        return $this->parent_id === null;
    }

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('menu.header');
            Cache::forget('menu.footer');
        });
        static::deleted(function () {
            Cache::forget('menu.header');
            Cache::forget('menu.footer');
        });
    }
}
