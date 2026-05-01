<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LepisBulletinRecipient extends Model
{
    public const FORMAT_PAPER   = 'paper';
    public const FORMAT_DIGITAL = 'digital';

    protected $fillable = [
        'lepis_bulletin_id',
        'member_id',
        'membership_id',
        'format',
        'email_at_snapshot',
        'postal_address_at_snapshot',
        'brevo_list_id',
        'included_at',
    ];

    protected $casts = [
        'postal_address_at_snapshot' => 'array',
        'included_at'                => 'datetime',
        'brevo_list_id'              => 'integer',
    ];

    public function bulletin(): BelongsTo
    {
        return $this->belongsTo(LepisBulletin::class, 'lepis_bulletin_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
