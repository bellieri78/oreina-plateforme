<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnershipType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'color',
        'icon',
        'is_auto_calculated',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_auto_calculated' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Types auto-calcules
    public const ADHERENT = 'ADHERENT';
    public const ANCIEN_ADHERENT = 'ANCIEN_ADHERENT';

    // Types manuels
    public const BENEVOLE = 'BENEVOLE';
    public const BENEVOLE_PONCTUEL = 'BENEVOLE_PONCTUEL';
    public const DONATEUR = 'DONATEUR';
    public const PARTENAIRE = 'PARTENAIRE';
    public const FOURNISSEUR = 'FOURNISSEUR';
    public const ELU = 'ELU';

    public function memberPartnerships(): HasMany
    {
        return $this->hasMany(MemberPartnership::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeManual($query)
    {
        return $query->where('is_auto_calculated', false);
    }

    public function scopeAutoCalculated($query)
    {
        return $query->where('is_auto_calculated', true);
    }

    public static function getByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }
}
