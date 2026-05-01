<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LepisBulletin extends Model
{
    public const STATUS_DRAFT   = 'draft';
    public const STATUS_MEMBERS = 'members';
    public const STATUS_PUBLIC  = 'public';

    public const DEFAULT_ANNOUNCEMENT_SUBJECT = 'Téléchargez Lepis #[N], le bulletin des adhérents oreina';

    public const DEFAULT_ANNOUNCEMENT_BODY = <<<'MD'
Cher(e)s ami(e)s, cher(e)s collègues, cher(e)s adhérent(e)s,

Lors de votre adhésion vous avez souscrit à la version numérique de Lepis, le bulletin des adhérents d'oreina. Cliquez ci-dessous pour consulter ce numéro.

**[Télécharger Lepis]({{lien_bulletin}})**

Nous vous souhaitons une bonne lecture et espérons que vous participerez activement à l'élaboration des suivants.

Le Conseil d'administration.
MD;

    protected $fillable = [
        'title',
        'issue_number',
        'quarter',
        'year',
        'pdf_path',
        'status',
        'published_to_members_at',
        'published_public_at',
        'summary',
        'cover_image',
        'announcement_subject',
        'announcement_body',
        'brevo_list_id',
        'brevo_list_name',
        'brevo_synced_at',
        'brevo_sync_failed',
    ];

    protected $casts = [
        'published_to_members_at' => 'datetime',
        'published_public_at'     => 'datetime',
        'brevo_synced_at'         => 'datetime',
        'brevo_sync_failed'       => 'boolean',
        'brevo_list_id'           => 'integer',
    ];

    protected $attributes = [
        'status'            => 'draft',
        'brevo_sync_failed' => false,
    ];

    public function scopeVisibleOnHub($query)
    {
        return $query->whereIn('status', [self::STATUS_MEMBERS, self::STATUS_PUBLIC]);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isInMembersPhase(): bool
    {
        return $this->status === self::STATUS_MEMBERS;
    }

    public function isPublic(): bool
    {
        return $this->status === self::STATUS_PUBLIC;
    }

    public function getQuarterLabelAttribute(): string
    {
        return match ($this->quarter) {
            'Q1' => 'Printemps',
            'Q2' => 'Été',
            'Q3' => 'Automne',
            'Q4' => 'Hiver',
            default => $this->quarter,
        };
    }

    public function getBrevoListUrlAttribute(): ?string
    {
        return $this->brevo_list_id
            ? "https://app.brevo.com/contact/list/id/{$this->brevo_list_id}"
            : null;
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(LepisBulletinRecipient::class, 'lepis_bulletin_id');
    }

    public function paperRecipientsCount(): int
    {
        return $this->recipients()->where('format', LepisBulletinRecipient::FORMAT_PAPER)->count();
    }

    public function digitalRecipientsCount(): int
    {
        return $this->recipients()->where('format', LepisBulletinRecipient::FORMAT_DIGITAL)->count();
    }
}
