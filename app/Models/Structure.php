<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Structure extends Model
{
    // Types de structures
    public const TYPE_NATIONAL = 'national';
    public const TYPE_REGIONAL = 'regional';
    public const TYPE_DEPARTEMENTAL = 'departemental';
    public const TYPE_LOCAL = 'local';

    // Roles dans une structure
    public const ROLE_RESPONSABLE = 'responsable';
    public const ROLE_CORRESPONDANT = 'correspondant';
    public const ROLE_MEMBRE = 'membre';
    public const ROLE_TRESORIER = 'tresorier';
    public const ROLE_SECRETAIRE = 'secretaire';

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'type',
        'description',
        'departement_code',
        'region',
        'email',
        'phone',
        'address',
        'postal_code',
        'city',
        'responsable_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Structure::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'responsable_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_structure')
            ->withPivot(['role', 'joined_at', 'left_at', 'notes'])
            ->withTimestamps();
    }

    public function activeMembers(): BelongsToMany
    {
        return $this->members()->whereNull('member_structure.left_at');
    }

    // ===== ACCESSORS =====

    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    public function getDepthAttribute(): int
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    // ===== METHODS =====

    /**
     * Get all ancestors (parents) of this structure
     */
    public function getAncestors(): Collection
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get all descendants (children) of this structure
     */
    public function getDescendants(): Collection
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getDescendants());
        }

        return $descendants;
    }

    /**
     * Get all member IDs including children structures
     */
    public function getAllMemberIds(): array
    {
        $memberIds = $this->activeMembers()->pluck('members.id')->toArray();

        foreach ($this->children as $child) {
            $memberIds = array_merge($memberIds, $child->getAllMemberIds());
        }

        return array_unique($memberIds);
    }

    /**
     * Count all members including children structures
     */
    public function countAllMembers(): int
    {
        return count($this->getAllMemberIds());
    }

    /**
     * Check if this structure is ancestor of another
     */
    public function isAncestorOf(Structure $structure): bool
    {
        $parent = $structure->parent;

        while ($parent) {
            if ($parent->id === $this->id) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    /**
     * Check if this structure is descendant of another
     */
    public function isDescendantOf(Structure $structure): bool
    {
        return $structure->isAncestorOf($this);
    }

    // ===== SCOPES =====

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeByDepartement($query, string $code)
    {
        return $query->where('departement_code', $code);
    }

    // ===== STATIC METHODS =====

    public static function getTypes(): array
    {
        return [
            self::TYPE_NATIONAL => 'National',
            self::TYPE_REGIONAL => 'Regional',
            self::TYPE_DEPARTEMENTAL => 'Departemental',
            self::TYPE_LOCAL => 'Local',
        ];
    }

    public static function getRoles(): array
    {
        return [
            self::ROLE_RESPONSABLE => 'Responsable',
            self::ROLE_CORRESPONDANT => 'Correspondant',
            self::ROLE_TRESORIER => 'Tresorier',
            self::ROLE_SECRETAIRE => 'Secretaire',
            self::ROLE_MEMBRE => 'Membre',
        ];
    }

    /**
     * Get structures as a flat list with indentation for select boxes
     */
    public static function getTreeForSelect(): array
    {
        $structures = [];

        $addChildren = function ($parent, $depth) use (&$structures, &$addChildren) {
            $children = self::where('parent_id', $parent)
                ->active()
                ->ordered()
                ->get();

            foreach ($children as $child) {
                $structures[$child->id] = str_repeat('— ', $depth) . $child->name;
                $addChildren($child->id, $depth + 1);
            }
        };

        // Start with root structures
        $roots = self::roots()->active()->ordered()->get();
        foreach ($roots as $root) {
            $structures[$root->id] = $root->name;
            $addChildren($root->id, 1);
        }

        return $structures;
    }

    /**
     * Generate unique code based on type and location
     */
    public static function generateCode(string $type, ?string $departement = null, ?string $name = null): string
    {
        $prefix = match ($type) {
            self::TYPE_NATIONAL => 'NAT',
            self::TYPE_REGIONAL => 'REG',
            self::TYPE_DEPARTEMENTAL => 'DEP',
            self::TYPE_LOCAL => 'LOC',
            default => 'STR',
        };

        if ($departement) {
            $code = $prefix . '-' . strtoupper($departement);
        } elseif ($name) {
            $slug = substr(preg_replace('/[^A-Z0-9]/', '', strtoupper($name)), 0, 3);
            $code = $prefix . '-' . $slug;
        } else {
            $code = $prefix . '-' . date('Ymd');
        }

        // Ensure uniqueness
        $baseCode = $code;
        $counter = 1;
        while (self::where('code', $code)->exists()) {
            $code = $baseCode . '-' . $counter++;
        }

        return $code;
    }
}
