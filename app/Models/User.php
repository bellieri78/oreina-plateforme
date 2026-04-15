<?php

namespace App\Models;

use App\Models\EditorialCapability;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // Roles
    public const ROLE_USER = 'user';
    public const ROLE_AUTHOR = 'author';
    public const ROLE_REVIEWER = 'reviewer';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public static function getRoles(): array
    {
        return [
            self::ROLE_USER => 'Utilisateur',
            self::ROLE_AUTHOR => 'Auteur',
            self::ROLE_REVIEWER => 'Reviewer',
            self::ROLE_EDITOR => 'Editeur',
            self::ROLE_ADMIN => 'Administrateur',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->hasCapability(EditorialCapability::CHIEF_EDITOR);
    }

    public function isEditor(): bool
    {
        return $this->isAdmin() || $this->hasCapability(EditorialCapability::EDITOR);
    }

    public function isReviewer(): bool
    {
        return $this->isEditor() || $this->hasCapability(EditorialCapability::REVIEWER);
    }

    public function isAuthor(): bool
    {
        return $this->isReviewer() || $this->role === self::ROLE_AUTHOR;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function getRoleLabelAttribute(): string
    {
        return self::getRoles()[$this->role] ?? $this->role;
    }

    // Relations
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'author_id');
    }

    public function assignedReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function capabilities(): HasMany
    {
        return $this->hasMany(EditorialCapability::class);
    }

    public function hasCapability(string $capability): bool
    {
        return $this->capabilities()->where('capability', $capability)->exists();
    }

    public function grantCapability(string $capability, ?User $grantedBy = null): EditorialCapability
    {
        return $this->capabilities()->firstOrCreate(
            ['capability' => $capability],
            [
                'granted_by_user_id' => $grantedBy?->id,
                'granted_at' => now(),
            ]
        );
    }

    public function revokeCapability(string $capability): void
    {
        $this->capabilities()->where('capability', $capability)->delete();
    }

    public function scopeWithCapability(Builder $query, string $capability): Builder
    {
        return $query->whereHas('capabilities', fn ($q) => $q->where('capability', $capability));
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withTimestamps();
    }

    // ===== PERMISSIONS =====

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $module, string $action): bool
    {
        // Admins have all permissions
        if ($this->isAdmin()) {
            return true;
        }

        return $this->getCachedPermissions()->contains(function ($perm) use ($module, $action) {
            return $perm['module'] === $module && $perm['action'] === $action;
        });
    }

    /**
     * Check if user can perform action on module
     */
    public function can($abilities, $arguments = []): bool
    {
        // If string ability with dot notation, check module permission
        if (is_string($abilities) && str_contains($abilities, '.')) {
            [$module, $action] = explode('.', $abilities, 2);
            return $this->hasPermission($module, $action);
        }

        return parent::can($abilities, $arguments);
    }

    /**
     * Get cached permissions for this user
     */
    public function getCachedPermissions()
    {
        return Cache::remember("user.{$this->id}.permissions", 3600, function () {
            return $this->permissions()->get(['module', 'action']);
        });
    }

    /**
     * Clear user's permission cache
     */
    public function clearPermissionCache(): void
    {
        Cache::forget("user.{$this->id}.permissions");
    }

    /**
     * Sync permissions from array of permission IDs
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
        $this->clearPermissionCache();
    }

    /**
     * Grant a permission
     */
    public function grantPermission(string $module, string $action): bool
    {
        $permission = Permission::findByModuleAction($module, $action);
        if ($permission && !$this->permissions()->where('permission_id', $permission->id)->exists()) {
            $this->permissions()->attach($permission->id);
            $this->clearPermissionCache();
            return true;
        }
        return false;
    }

    /**
     * Revoke a permission
     */
    public function revokePermission(string $module, string $action): bool
    {
        $permission = Permission::findByModuleAction($module, $action);
        if ($permission) {
            $this->permissions()->detach($permission->id);
            $this->clearPermissionCache();
            return true;
        }
        return false;
    }

    /**
     * Get all permission IDs for this user
     */
    public function getPermissionIds(): array
    {
        return $this->permissions()->pluck('permissions.id')->toArray();
    }

    /**
     * Check if user has any permission for a module
     */
    public function hasAnyPermissionFor(string $module): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->getCachedPermissions()->contains('module', $module);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification());
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeReviewers($query)
    {
        return $query->whereIn('role', [self::ROLE_REVIEWER, self::ROLE_EDITOR, self::ROLE_ADMIN]);
    }

    public function scopeEditors($query)
    {
        return $query->whereIn('role', [self::ROLE_EDITOR, self::ROLE_ADMIN]);
    }
}
