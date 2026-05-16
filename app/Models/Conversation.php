<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'member_low_id', 'member_high_id', 'last_message_at',
        'member_low_read_at', 'member_high_read_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'member_low_read_at' => 'datetime',
        'member_high_read_at' => 'datetime',
    ];

    public function memberLow(): BelongsTo { return $this->belongsTo(Member::class, 'member_low_id'); }
    public function memberHigh(): BelongsTo { return $this->belongsTo(Member::class, 'member_high_id'); }
    public function messages(): HasMany { return $this->hasMany(ChatMessage::class); }

    public static function between(int $a, int $b): self
    {
        return static::firstOrCreate([
            'member_low_id' => min($a, $b),
            'member_high_id' => max($a, $b),
        ]);
    }

    public function isParticipant(Member $m): bool
    {
        return in_array($m->id, [$this->member_low_id, $this->member_high_id], true);
    }

    public function otherMember(Member $m): Member
    {
        $otherId = $m->id === $this->member_low_id ? $this->member_high_id : $this->member_low_id;
        return Member::findOrFail($otherId);
    }

    public function readAtColumnFor(Member $m): string
    {
        return $m->id === $this->member_low_id ? 'member_low_read_at' : 'member_high_read_at';
    }

    public function unreadFor(Member $m): bool
    {
        if (! $this->last_message_at) {
            return false;
        }
        $readAt = $this->{$this->readAtColumnFor($m)};
        return $readAt === null || $readAt->lt($this->last_message_at);
    }

    public function markReadFor(Member $m): void
    {
        if (! $this->isParticipant($m)) {
            return;
        }
        $this->forceFill([$this->readAtColumnFor($m) => now()])->save();
    }
}
