<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RgpdConsentHistory extends Model
{
    protected $table = 'rgpd_consent_history';

    protected $fillable = [
        'member_id',
        'consent_type',
        'value',
        'source',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'value' => 'boolean',
    ];

    public const TYPE_NEWSLETTER = 'newsletter';
    public const TYPE_COMMUNICATION = 'communication';
    public const TYPE_IMAGE = 'image';
    public const TYPE_DIRECTORY = 'directory';

    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_IMPORT = 'import';
    public const SOURCE_FORM = 'form';
    public const SOURCE_API = 'api';

    public static function getTypes(): array
    {
        return [
            self::TYPE_NEWSLETTER => 'Newsletter',
            self::TYPE_COMMUNICATION => 'Communication',
            self::TYPE_IMAGE => 'Droit a l\'image',
            self::TYPE_DIRECTORY => 'Annuaire des adhérents',
        ];
    }

    public static function getSources(): array
    {
        return [
            self::SOURCE_MANUAL => 'Saisie manuelle',
            self::SOURCE_IMPORT => 'Import',
            self::SOURCE_FORM => 'Formulaire',
            self::SOURCE_API => 'API',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
