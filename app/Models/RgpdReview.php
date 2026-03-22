<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RgpdReview extends Model
{
    protected $fillable = [
        'member_id',
        'alert_type',
        'action',
        'notes',
        'next_review_date',
        'user_id',
    ];

    protected $casts = [
        'next_review_date' => 'date',
    ];

    public const ALERT_NO_INTERACTION = 'no_interaction';
    public const ALERT_NOT_UPDATED = 'not_updated';
    public const ALERT_EXPIRED_MEMBERSHIP = 'expired_membership';
    public const ALERT_INACTIVE_DONOR = 'inactive_donor';

    public const ACTION_KEEP = 'keep';
    public const ACTION_UPDATE = 'update';
    public const ACTION_CONTACT = 'contact';
    public const ACTION_ANONYMIZE = 'anonymize';

    public static function getAlertTypes(): array
    {
        return [
            self::ALERT_NO_INTERACTION => 'Sans interaction',
            self::ALERT_NOT_UPDATED => 'Non mis a jour',
            self::ALERT_EXPIRED_MEMBERSHIP => 'Adhesion expiree',
            self::ALERT_INACTIVE_DONOR => 'Donateur inactif',
        ];
    }

    public static function getActions(): array
    {
        return [
            self::ACTION_KEEP => 'Conserver',
            self::ACTION_UPDATE => 'Mettre a jour',
            self::ACTION_CONTACT => 'Contacter',
            self::ACTION_ANONYMIZE => 'Anonymiser',
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
