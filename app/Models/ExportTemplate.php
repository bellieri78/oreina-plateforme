<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExportTemplate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'columns',
        'filters',
        'options',
        'description',
        'is_default',
        'created_by',
    ];

    protected $casts = [
        'columns' => 'array',
        'filters' => 'array',
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
        return $this->hasMany(ExportLog::class, 'template_id');
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
            'volunteer' => 'Benevolat',
        ];
    }

    public static function getAvailableColumns(string $type): array
    {
        return match ($type) {
            'members' => [
                'id' => 'ID',
                'member_number' => 'Numero adherent',
                'title' => 'Civilite',
                'first_name' => 'Prenom',
                'last_name' => 'Nom',
                'email' => 'Email',
                'phone' => 'Telephone',
                'address' => 'Adresse',
                'postal_code' => 'Code postal',
                'city' => 'Ville',
                'country' => 'Pays',
                'status' => 'Statut',
                'newsletter' => 'Newsletter',
                'created_at' => 'Date creation',
            ],
            'memberships' => [
                'id' => 'ID',
                'member_name' => 'Membre',
                'member_email' => 'Email',
                'type' => 'Type',
                'amount' => 'Montant',
                'start_date' => 'Date debut',
                'end_date' => 'Date fin',
                'payment_method' => 'Mode paiement',
                'status' => 'Statut',
                'created_at' => 'Date creation',
            ],
            'donations' => [
                'id' => 'ID',
                'member_name' => 'Donateur',
                'member_email' => 'Email',
                'amount' => 'Montant',
                'donation_date' => 'Date don',
                'payment_method' => 'Mode paiement',
                'campaign' => 'Campagne',
                'receipt_sent' => 'Recu envoye',
                'created_at' => 'Date creation',
            ],
            'volunteer' => [
                'id' => 'ID',
                'title' => 'Titre',
                'type' => 'Type activite',
                'date' => 'Date',
                'location' => 'Lieu',
                'participants' => 'Participants',
                'hours' => 'Heures',
                'status' => 'Statut',
            ],
            default => [],
        };
    }
}
