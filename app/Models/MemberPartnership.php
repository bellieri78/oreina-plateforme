<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberPartnership extends Model
{
    protected $fillable = [
        'member_id',
        'partnership_type_id',
        'date_debut',
        'date_fin',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function partnershipType(): BelongsTo
    {
        return $this->belongsTo(PartnershipType::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Verifie si le partenariat est actuellement actif
     */
    public function isActive(): bool
    {
        $now = now()->toDateString();

        // Pas de date de debut = actif depuis toujours
        if ($this->date_debut && $this->date_debut > $now) {
            return false;
        }

        // Pas de date de fin = toujours actif
        if ($this->date_fin && $this->date_fin < $now) {
            return false;
        }

        return true;
    }

    public function scopeActive($query)
    {
        $now = now()->toDateString();

        return $query->where(function ($q) use ($now) {
            $q->whereNull('date_debut')
                ->orWhere('date_debut', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('date_fin')
                ->orWhere('date_fin', '>=', $now);
        });
    }

    public function scopeForType($query, string $typeCode)
    {
        return $query->whereHas('partnershipType', function ($q) use ($typeCode) {
            $q->where('code', $typeCode);
        });
    }
}
