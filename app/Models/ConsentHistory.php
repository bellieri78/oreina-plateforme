<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsentHistory extends Model
{
    protected $table = 'consent_history';

    protected $fillable = [
        'member_id',
        'type',
        'old_status',
        'new_status',
        'method',
        'source',
        'ip_address',
        'user_agent',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'old_status' => 'boolean',
        'new_status' => 'boolean',
        'changed_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getTypeLabel(): string
    {
        return Consent::getTypes()[$this->type] ?? $this->type;
    }

    public function getMethodLabel(): string
    {
        return Consent::getMethods()[$this->method] ?? $this->method;
    }

    public function getSourceLabel(): string
    {
        return Consent::getSources()[$this->source] ?? $this->source;
    }
}
