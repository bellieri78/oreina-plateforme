<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consent extends Model
{
    protected $fillable = [
        'member_id',
        'type',
        'status',
        'consent_date',
        'method',
        'source',
        'ip_address',
        'user_agent',
        'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'consent_date' => 'datetime',
    ];

    // Types de consentement
    public const TYPE_NEWSLETTER = 'newsletter';
    public const TYPE_EMAIL_MARKETING = 'email_marketing';
    public const TYPE_DATA_STORAGE = 'data_storage';
    public const TYPE_PHOTO_USAGE = 'photo_usage';
    public const TYPE_DATA_SHARING = 'data_sharing';

    // Methodes de collecte
    public const METHOD_WEB_FORM = 'web_form';
    public const METHOD_PAPER = 'paper';
    public const METHOD_EMAIL = 'email';
    public const METHOD_PHONE = 'phone';
    public const METHOD_BREVO_WEBHOOK = 'brevo_webhook';
    public const METHOD_ADMIN = 'admin';

    // Sources
    public const SOURCE_BREVO = 'brevo';
    public const SOURCE_FORMULAIRE_INSCRIPTION = 'formulaire_inscription';
    public const SOURCE_FORMULAIRE_CONTACT = 'formulaire_contact';
    public const SOURCE_NEWSLETTER_HUB = 'newsletter_hub';
    public const SOURCE_ADMIN = 'admin';
    public const SOURCE_IMPORT = 'import';
    public const SOURCE_HELLOASSO = 'helloasso';

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_NEWSLETTER => 'Newsletter',
            self::TYPE_EMAIL_MARKETING => 'Communications marketing',
            self::TYPE_DATA_STORAGE => 'Stockage des donnees',
            self::TYPE_PHOTO_USAGE => 'Utilisation des photos',
            self::TYPE_DATA_SHARING => 'Partage des donnees',
        ];
    }

    public static function getMethods(): array
    {
        return [
            self::METHOD_WEB_FORM => 'Formulaire web',
            self::METHOD_PAPER => 'Document papier',
            self::METHOD_EMAIL => 'Par email',
            self::METHOD_PHONE => 'Par telephone',
            self::METHOD_BREVO_WEBHOOK => 'Automatique (Brevo)',
            self::METHOD_ADMIN => 'Saisie administrative',
        ];
    }

    public static function getSources(): array
    {
        return [
            self::SOURCE_BREVO => 'Brevo',
            self::SOURCE_FORMULAIRE_INSCRIPTION => 'Formulaire d\'inscription',
            self::SOURCE_FORMULAIRE_CONTACT => 'Formulaire de contact',
            self::SOURCE_NEWSLETTER_HUB => 'Newsletter (site Hub)',
            self::SOURCE_ADMIN => 'Administration',
            self::SOURCE_IMPORT => 'Import en masse',
            self::SOURCE_HELLOASSO => 'HelloAsso',
        ];
    }

    /**
     * Met a jour le consentement et enregistre l'historique
     */
    public function updateStatus(
        bool $newStatus,
        string $method = self::METHOD_ADMIN,
        string $source = self::SOURCE_ADMIN,
        ?int $userId = null
    ): void {
        $oldStatus = $this->status;

        // Enregistrer l'historique
        ConsentHistory::create([
            'member_id' => $this->member_id,
            'type' => $this->type,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'method' => $method,
            'source' => $source,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changed_by' => $userId ?? auth()->id(),
            'changed_at' => now(),
        ]);

        // Mettre a jour le consentement
        $this->update([
            'status' => $newStatus,
            'consent_date' => now(),
            'method' => $method,
            'source' => $source,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
