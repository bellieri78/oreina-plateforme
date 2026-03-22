<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Permission extends Model
{
    protected $fillable = [
        'module',
        'action',
        'name',
        'description',
        'sort_order',
    ];

    /**
     * Users with this permission
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions')
            ->withTimestamps();
    }

    /**
     * Get all permissions grouped by module
     */
    public static function getGroupedByModule(): array
    {
        return Cache::remember('permissions.grouped', 3600, function () {
            return static::orderBy('sort_order')
                ->get()
                ->groupBy('module')
                ->toArray();
        });
    }

    /**
     * Get module labels
     */
    public static function getModuleLabels(): array
    {
        return [
            'members' => 'Contacts',
            'memberships' => 'Adhesions',
            'donations' => 'Dons',
            'articles' => 'Articles',
            'events' => 'Evenements',
            'journal' => 'Revue scientifique',
            'submissions' => 'Soumissions',
            'reviews' => 'Reviews',
            'users' => 'Utilisateurs',
            'settings' => 'Parametres',
            'rgpd' => 'RGPD',
            'map' => 'Carte',
        ];
    }

    /**
     * Get module label
     */
    public function getModuleLabelAttribute(): string
    {
        return self::getModuleLabels()[$this->module] ?? $this->module;
    }

    /**
     * Clear permissions cache
     */
    public static function clearCache(): void
    {
        Cache::forget('permissions.grouped');
    }

    /**
     * Find permission by module and action
     */
    public static function findByModuleAction(string $module, string $action): ?self
    {
        return static::where('module', $module)
            ->where('action', $action)
            ->first();
    }
}
