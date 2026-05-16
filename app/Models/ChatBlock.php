<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatBlock extends Model
{
    protected $fillable = ['blocker_id', 'blocked_id'];

    public function blocker(): BelongsTo { return $this->belongsTo(Member::class, 'blocker_id'); }
    public function blocked(): BelongsTo { return $this->belongsTo(Member::class, 'blocked_id'); }

    public static function existsBetween(int $a, int $b): bool
    {
        return static::where(fn ($q) => $q->where('blocker_id', $a)->where('blocked_id', $b))
            ->orWhere(fn ($q) => $q->where('blocker_id', $b)->where('blocked_id', $a))
            ->exists();
    }
}
