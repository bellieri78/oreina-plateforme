<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'table_name',
        'record_id',
        'action',
        'old_values',
        'new_values',
        'user_id',
        'ip_address',
        'user_agent',
        'description',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    // Actions
    public const ACTION_INSERT = 'INSERT';
    public const ACTION_UPDATE = 'UPDATE';
    public const ACTION_DELETE = 'DELETE';
    public const ACTION_VIEW = 'VIEW';
    public const ACTION_EXPORT = 'EXPORT';
    public const ACTION_ANONYMIZE = 'ANONYMIZE';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Enregistre une action d'audit
     */
    public static function log(
        string $tableName,
        int $recordId,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): self {
        return self::create([
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
            'created_at' => now(),
        ]);
    }

    /**
     * Enregistre une consultation
     */
    public static function logView(string $tableName, int $recordId): self
    {
        return self::log($tableName, $recordId, self::ACTION_VIEW);
    }

    /**
     * Enregistre un export
     */
    public static function logExport(string $tableName, array $recordIds, string $format): self
    {
        return self::log(
            $tableName,
            0,
            self::ACTION_EXPORT,
            null,
            ['record_ids' => $recordIds, 'format' => $format],
            "Export de " . count($recordIds) . " enregistrements au format {$format}"
        );
    }

    public static function getActions(): array
    {
        return [
            self::ACTION_INSERT => 'Creation',
            self::ACTION_UPDATE => 'Modification',
            self::ACTION_DELETE => 'Suppression',
            self::ACTION_VIEW => 'Consultation',
            self::ACTION_EXPORT => 'Export',
            self::ACTION_ANONYMIZE => 'Anonymisation',
        ];
    }

    public function getActionLabel(): string
    {
        return self::getActions()[$this->action] ?? $this->action;
    }
}
