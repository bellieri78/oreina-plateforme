<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditorialCapability extends Model
{
    public const CHIEF_EDITOR  = 'chief_editor';
    public const EDITOR        = 'editor';
    public const REVIEWER      = 'reviewer';
    public const LAYOUT_EDITOR = 'layout_editor';
    public const LEPIS_EDITOR  = 'lepis_editor';

    public const ALL = [
        self::CHIEF_EDITOR,
        self::EDITOR,
        self::REVIEWER,
        self::LAYOUT_EDITOR,
        self::LEPIS_EDITOR,
    ];

    protected $fillable = [
        'user_id',
        'capability',
        'granted_by_user_id',
        'granted_at',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
    ];

    public static function labels(): array
    {
        return [
            self::CHIEF_EDITOR  => 'Rédacteur en chef',
            self::EDITOR        => 'Éditeur',
            self::REVIEWER      => 'Relecteur',
            self::LAYOUT_EDITOR => 'Maquettiste',
            self::LEPIS_EDITOR  => 'Rédacteur en chef Lepis',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by_user_id');
    }
}
