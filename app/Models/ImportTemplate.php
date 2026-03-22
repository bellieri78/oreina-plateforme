<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportTemplate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'mapping',
        'options',
        'description',
        'is_default',
        'created_by',
    ];

    protected $casts = [
        'mapping' => 'array',
        'options' => 'array',
        'is_default' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ImportLog::class, 'template_id');
    }

    // ===== SCOPES =====

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ===== STATIC =====

    public static function getTypes(): array
    {
        return [
            'members' => 'Contacts',
            'memberships' => 'Adhesions',
            'donations' => 'Dons',
        ];
    }

    public static function getDefaultMapping(string $type): array
    {
        return match ($type) {
            'members' => [
                'first_name' => ['prenom', 'first_name', 'firstname', 'prénom'],
                'last_name' => ['nom', 'last_name', 'lastname', 'name'],
                'email' => ['email', 'mail', 'e-mail', 'courriel'],
                'phone' => ['telephone', 'phone', 'tel', 'téléphone', 'mobile'],
                'address' => ['adresse', 'address', 'rue'],
                'postal_code' => ['code_postal', 'postal_code', 'cp', 'zip'],
                'city' => ['ville', 'city'],
                'country' => ['pays', 'country'],
            ],
            'memberships' => [
                'member_email' => ['email', 'mail', 'membre_email'],
                'type' => ['type', 'type_adhesion', 'formule'],
                'amount' => ['montant', 'amount', 'prix'],
                'start_date' => ['date_debut', 'start_date', 'debut'],
                'end_date' => ['date_fin', 'end_date', 'fin', 'expiration'],
                'payment_method' => ['mode_paiement', 'payment', 'paiement'],
            ],
            'donations' => [
                'member_email' => ['email', 'mail', 'donateur_email'],
                'amount' => ['montant', 'amount', 'somme'],
                'donation_date' => ['date', 'date_don', 'donation_date'],
                'payment_method' => ['mode_paiement', 'payment', 'paiement'],
                'campaign' => ['campagne', 'campaign', 'source'],
            ],
            default => [],
        };
    }
}
