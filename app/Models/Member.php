<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
    use SoftDeletes, Notifiable;

    // Types de contact
    public const TYPE_INDIVIDUEL = 'individuel';
    public const TYPE_COLLECTIVITE = 'collectivite';
    public const TYPE_ASSOCIATION = 'association';
    public const TYPE_ENTREPRISE = 'entreprise';
    public const TYPE_AUTRE = 'autre';

    // Civilites
    public const CIVILITES = ['M.', 'Mme', 'Dr', 'Pr'];

    // ===== DIRECTORY =====

    public const DIRECTORY_GROUP_RHOPALO = 'rhopalo';
    public const DIRECTORY_GROUP_MICRO   = 'micro';
    public const DIRECTORY_GROUP_MACRO   = 'macro';
    public const DIRECTORY_GROUP_ZYGENES = 'zygenes';

    public const DIRECTORY_GROUPS = [
        self::DIRECTORY_GROUP_RHOPALO => 'Rhopalocères',
        self::DIRECTORY_GROUP_MICRO   => 'Microlépidoptères',
        self::DIRECTORY_GROUP_MACRO   => 'Macrolépidoptères',
        self::DIRECTORY_GROUP_ZYGENES => 'Zygènes',
    ];

    protected $fillable = [
        'contact_type',
        'civilite',
        'user_id',
        'foyer_titulaire_id',
        'referent_id',
        'organisation_id',
        'fonction_dans_organisation',
        'member_number',
        'first_name',
        'last_name',
        'email',
        'telephone_fixe',
        'mobile',
        'address',
        'postal_code',
        'city',
        'country',
        'latitude',
        'longitude',
        'birth_date',
        'profession',
        'interests',
        'photo_path',
        'newsletter_subscribed',
        'is_active',
        'status',
        'anonymise',
        'date_anonymisation',
        'membership_expires_at',
        'joined_at',
        'created_by',
        'updated_by',
        'deleted_by',
        // Directory (annuaire)
        'directory_opt_in',
        'directory_phone_visible',
        'directory_groups',
        'directory_opt_in_at',
        'directory_opt_in_source',
        // RGPD
        'consent_communication',
        'consent_image',
        'rgpd_reviewed_at',
        'rgpd_review_notes',
        'last_interaction_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'joined_at' => 'datetime',
        'membership_expires_at' => 'datetime',
        'date_anonymisation' => 'datetime',
        'newsletter_subscribed' => 'boolean',
        'is_active' => 'boolean',
        'anonymise' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        // RGPD
        'consent_communication' => 'boolean',
        'consent_image' => 'boolean',
        'rgpd_reviewed_at' => 'datetime',
        'last_interaction_at' => 'datetime',
        // Directory
        'directory_opt_in' => 'boolean',
        'directory_phone_visible' => 'boolean',
        'directory_groups' => 'array',
        'directory_opt_in_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Auto-uppercase last name
        static::saving(function (Member $member) {
            if ($member->last_name) {
                $member->last_name = mb_strtoupper($member->last_name);
            }

            // Track user modifications
            if (auth()->check()) {
                if ($member->exists) {
                    $member->updated_by = auth()->id();
                } else {
                    $member->created_by = auth()->id();
                }
            }
        });

        // Log deletions
        static::deleting(function (Member $member) {
            if (auth()->check()) {
                $member->deleted_by = auth()->id();
                $member->saveQuietly();
            }
        });
    }

    // ===== RELATIONSHIPS =====

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referent_id');
    }

    public function foyerTitulaire(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'foyer_titulaire_id');
    }

    public function membresFoyer(): HasMany
    {
        return $this->hasMany(Member::class, 'foyer_titulaire_id');
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'organisation_id');
    }

    public function employes(): HasMany
    {
        return $this->hasMany(Member::class, 'organisation_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function lepisBulletinRecipients(): HasMany
    {
        return $this->hasMany(LepisBulletinRecipient::class)->orderByDesc('included_at');
    }

    public function lepisSuggestions(): HasMany
    {
        return $this->hasMany(\App\Models\LepisSuggestion::class)->orderByDesc('submitted_at');
    }

    public function submissions(): HasMany
    {
        // Member.user_id maps directly to Submission.author_id — no intermediate table needed.
        return $this->hasMany(\App\Models\Submission::class, 'author_id', 'user_id')->orderByDesc('created_at');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function partnerships(): HasMany
    {
        return $this->hasMany(MemberPartnership::class);
    }

    public function partnershipTypes(): BelongsToMany
    {
        return $this->belongsToMany(PartnershipType::class, 'member_partnerships')
            ->withPivot(['date_debut', 'date_fin', 'notes'])
            ->withTimestamps();
    }

    public function consents(): HasMany
    {
        return $this->hasMany(Consent::class);
    }

    public function consentHistory(): HasMany
    {
        return $this->hasMany(ConsentHistory::class);
    }

    public function rgpdConsentHistory(): HasMany
    {
        return $this->hasMany(RgpdConsentHistory::class);
    }

    public function rgpdReviews(): HasMany
    {
        return $this->hasMany(RgpdReview::class);
    }

    public function structures(): BelongsToMany
    {
        return $this->belongsToMany(Structure::class, 'member_structure')
            ->withPivot(['role', 'joined_at', 'left_at', 'notes'])
            ->withTimestamps();
    }

    public function activeStructures(): BelongsToMany
    {
        return $this->structures()->whereNull('member_structure.left_at');
    }

    public function volunteerParticipations(): HasMany
    {
        return $this->hasMany(VolunteerParticipation::class);
    }

    public function volunteerActivities(): BelongsToMany
    {
        return $this->belongsToMany(VolunteerActivity::class, 'volunteer_participations')
            ->withPivot(['status', 'hours_worked', 'notes'])
            ->withTimestamps();
    }

    public function workGroups(): BelongsToMany
    {
        return $this->belongsToMany(WorkGroup::class, 'work_group_member')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'member_tag')
            ->withPivot(['source'])
            ->withTimestamps();
    }

    /**
     * Add a tag to this member
     */
    public function addTag(string $tagName, string $source = 'manual'): void
    {
        $tag = Tag::findOrCreateByName($tagName, $source);

        if (!$this->tags()->where('tag_id', $tag->id)->exists()) {
            $this->tags()->attach($tag->id, ['source' => $source]);
        }
    }

    /**
     * Remove a tag from this member
     */
    public function removeTag(string $tagName): void
    {
        $tag = Tag::where('slug', \Illuminate\Support\Str::slug($tagName))->first();
        if ($tag) {
            $this->tags()->detach($tag->id);
        }
    }

    /**
     * Check if member has a specific tag
     */
    public function hasTag(string $tagName): bool
    {
        return $this->tags()
            ->where('slug', \Illuminate\Support\Str::slug($tagName))
            ->exists();
    }

    /**
     * Get total volunteer hours for this member
     */
    public function getTotalVolunteerHours(): float
    {
        return $this->volunteerParticipations()
            ->where('status', 'attended')
            ->sum('hours_worked') ?? 0;
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ===== ACCESSORS =====

    public function getFullNameAttribute(): string
    {
        $parts = [];
        if ($this->civilite) {
            $parts[] = $this->civilite;
        }
        if ($this->first_name) {
            $parts[] = $this->first_name;
        }
        if ($this->last_name) {
            $parts[] = $this->last_name;
        }

        return implode(' ', $parts) ?: 'Sans nom';
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->contact_type !== self::TYPE_INDIVIDUEL) {
            return $this->last_name ?: $this->first_name ?: 'Sans nom';
        }

        return $this->full_name;
    }

    // ===== MEMBERSHIP METHODS =====

    public function currentMembership(): ?Membership
    {
        return $this->memberships()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->latest('end_date')
            ->first();
    }

    public function isCurrentMember(): bool
    {
        return $this->currentMembership() !== null;
    }

    public function wasEverMember(): bool
    {
        return $this->memberships()
            ->where('status', 'active')
            ->exists();
    }

    public function isInDirectory(): bool
    {
        return (bool) $this->directory_opt_in && $this->isCurrentMember();
    }

    public function directoryDepartment(): ?string
    {
        return $this->postal_code ? substr($this->postal_code, 0, 2) : null;
    }

    // ===== PARTNERSHIP METHODS =====

    /**
     * Retourne tous les types de partenariat actifs (manuels + auto-calcules)
     */
    public function getActivePartnerships(): array
    {
        $partnerships = [];

        // Types manuels actifs
        $manualTypes = $this->partnerships()
            ->active()
            ->with('partnershipType')
            ->get()
            ->pluck('partnershipType.code')
            ->toArray();

        $partnerships = array_merge($partnerships, $manualTypes);

        // Types auto-calcules
        if ($this->isCurrentMember()) {
            $partnerships[] = PartnershipType::ADHERENT;
        } elseif ($this->wasEverMember()) {
            $partnerships[] = PartnershipType::ANCIEN_ADHERENT;
        }

        return array_unique($partnerships);
    }

    /**
     * Verifie si le membre a un type de partenariat specifique
     */
    public function hasPartnership(string $typeCode): bool
    {
        return in_array($typeCode, $this->getActivePartnerships());
    }

    /**
     * Ajoute un type de partenariat manuel
     */
    public function addPartnership(string $typeCode, ?string $dateDebut = null, ?string $notes = null): ?MemberPartnership
    {
        $type = PartnershipType::where('code', $typeCode)
            ->where('is_auto_calculated', false)
            ->first();

        if (!$type) {
            return null;
        }

        return MemberPartnership::create([
            'member_id' => $this->id,
            'partnership_type_id' => $type->id,
            'date_debut' => $dateDebut ?? now()->toDateString(),
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
    }

    // ===== CONSENT METHODS =====

    public function hasConsent(string $type): bool
    {
        return $this->consents()
            ->where('type', $type)
            ->where('status', true)
            ->exists();
    }

    public function getConsent(string $type): ?Consent
    {
        return $this->consents()->where('type', $type)->first();
    }

    public function setConsent(string $type, bool $status, string $method = 'admin', string $source = 'admin'): Consent
    {
        $consent = $this->getConsent($type);

        if ($consent) {
            $consent->updateStatus($status, $method, $source);
            return $consent->fresh();
        }

        return Consent::create([
            'member_id' => $this->id,
            'type' => $type,
            'status' => $status,
            'consent_date' => now(),
            'method' => $method,
            'source' => $source,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_by' => auth()->id(),
        ]);
    }

    // ===== ANONYMIZATION =====

    /**
     * Anonymise le contact (RGPD - droit a l'oubli)
     */
    public function anonymize(): void
    {
        // Log before anonymization
        AuditLog::log(
            'members',
            $this->id,
            AuditLog::ACTION_ANONYMIZE,
            $this->only(['first_name', 'last_name', 'email', 'phone', 'address']),
            null,
            'Anonymisation RGPD'
        );

        $this->update([
            'first_name' => 'ANONYME',
            'last_name' => 'ANONYME',
            'email' => 'anonyme-' . $this->id . '@anonymise.local',
            'phone' => null,
            'telephone_fixe' => null,
            'mobile' => null,
            'address' => null,
            'postal_code' => null,
            'city' => null,
            'birth_date' => null,
            'profession' => null,
            'interests' => null,
            'photo_path' => null,
            'latitude' => null,
            'longitude' => null,
            'anonymise' => true,
            'date_anonymisation' => now(),
            // RGPD consents
            'newsletter_subscribed' => false,
            'consent_communication' => false,
            'consent_image' => false,
            // Directory (annuaire)
            'directory_opt_in' => false,
            'directory_phone_visible' => false,
            'directory_groups' => null,
            'directory_opt_in_at' => null,
            'directory_opt_in_source' => null,
        ]);

        // Delete photo file if exists
        if ($this->photo_path && \Storage::exists($this->photo_path)) {
            \Storage::delete($this->photo_path);
        }

        // Remove all consents
        $this->consents()->delete();
    }

    /**
     * Update last interaction timestamp
     */
    public function recordInteraction(): void
    {
        $this->update(['last_interaction_at' => now()]);
    }

    /**
     * Set RGPD consent with history tracking
     */
    public function setRgpdConsent(string $type, bool $value, string $source = 'manual', ?string $notes = null): void
    {
        $columnMap = [
            RgpdConsentHistory::TYPE_NEWSLETTER    => 'newsletter_subscribed',
            RgpdConsentHistory::TYPE_COMMUNICATION => 'consent_communication',
            RgpdConsentHistory::TYPE_IMAGE         => 'consent_image',
            RgpdConsentHistory::TYPE_DIRECTORY     => 'directory_opt_in',
        ];

        $column = $columnMap[$type] ?? null;
        if (!$column) {
            return;
        }

        if ($this->{$column} !== $value) {
            RgpdConsentHistory::create([
                'member_id' => $this->id,
                'consent_type' => $type,
                'value' => $value,
                'source' => $source,
                'notes' => $notes,
                'user_id' => auth()->id(),
            ]);

            $updates = [$column => $value];

            if ($type === RgpdConsentHistory::TYPE_DIRECTORY) {
                if ($value === true) {
                    $updates['directory_opt_in_at'] = now();
                    $updates['directory_opt_in_source'] = $source;
                }
                // Sur false : on n'efface PAS opt_in_at/source ni groups/phone_visible
                // (préservation pour ré-activation future).
            }

            $this->update($updates);
        }
    }

    /**
     * Mark as RGPD reviewed
     */
    public function markRgpdReviewed(string $notes = null): void
    {
        $this->update([
            'rgpd_reviewed_at' => now(),
            'rgpd_review_notes' => $notes,
        ]);
    }

    // ===== SCOPES =====

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotAnonymized($query)
    {
        return $query->where('anonymise', false);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('contact_type', $type);
    }

    public function scopeIndividuels($query)
    {
        return $query->where('contact_type', self::TYPE_INDIVIDUEL);
    }

    public function scopeOrganisations($query)
    {
        return $query->whereIn('contact_type', [
            self::TYPE_COLLECTIVITE,
            self::TYPE_ASSOCIATION,
            self::TYPE_ENTREPRISE,
        ]);
    }

    public function scopeInDirectory($query)
    {
        return $query
            ->where('directory_opt_in', true)
            ->whereHas('memberships', fn ($m) =>
                $m->where('status', 'active')
                  ->whereDate('end_date', '>=', now()->toDateString())
            );
    }

    public function scopeCurrentMembers($query)
    {
        return $query->whereHas('memberships', function ($q) {
            $q->where('status', 'active')
                ->where('end_date', '>=', now());
        });
    }

    public function scopeFormerMembers($query)
    {
        return $query->whereHas('memberships', function ($q) {
            $q->where('status', 'active');
        })->whereDoesntHave('memberships', function ($q) {
            $q->where('status', 'active')
                ->where('end_date', '>=', now());
        });
    }

    public function scopeWithPartnership($query, string $typeCode)
    {
        return $query->whereHas('partnerships', function ($q) use ($typeCode) {
            $q->active()->whereHas('partnershipType', function ($q2) use ($typeCode) {
                $q2->where('code', $typeCode);
            });
        });
    }

    // ===== RGPD SCOPES =====

    /**
     * Members without interaction for X months
     */
    public function scopeNoInteractionSince($query, int $months)
    {
        $date = now()->subMonths($months);
        return $query->where(function ($q) use ($date) {
            $q->whereNull('last_interaction_at')
              ->orWhere('last_interaction_at', '<', $date);
        });
    }

    /**
     * Members not updated for X months
     */
    public function scopeNotUpdatedSince($query, int $months)
    {
        return $query->where('updated_at', '<', now()->subMonths($months));
    }

    /**
     * Members with expired membership for X months
     */
    public function scopeExpiredMembershipSince($query, int $months)
    {
        $date = now()->subMonths($months);
        return $query->where('membership_expires_at', '<', $date)
            ->whereDoesntHave('memberships', function ($q) {
                $q->where('status', 'active')
                  ->where('end_date', '>=', now());
            });
    }

    /**
     * Donors without donation for X months
     */
    public function scopeInactiveDonorSince($query, int $months)
    {
        $date = now()->subMonths($months);
        return $query->whereHas('donations')
            ->whereDoesntHave('donations', function ($q) use ($date) {
                $q->where('donation_date', '>=', $date);
            });
    }

    /**
     * Members needing RGPD review
     */
    public function scopeNeedsRgpdReview($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('rgpd_reviewed_at')
              ->orWhere('rgpd_reviewed_at', '<', now()->subYear());
        });
    }

    // ===== PROFILE COMPLETION =====

    /**
     * Pourcentage de complétion du profil pour la barre sidebar.
     * 7 champs comptent, chacun 1/7 (~14.3%).
     */
    public function profileCompletionPercent(): int
    {
        $fields = [
            'photo_path',
            'birth_date',
            'postal_code',
            'city',
            'mobile',
            'interests',
            'profession',
        ];

        $filled = 0;
        foreach ($fields as $field) {
            $value = $this->{$field};
            if ($value !== null && $value !== '') {
                $filled++;
            }
        }

        return (int) round(($filled / count($fields)) * 100);
    }

    // ===== STATIC METHODS =====

    public static function getContactTypes(): array
    {
        return [
            self::TYPE_INDIVIDUEL => 'Individuel',
            self::TYPE_COLLECTIVITE => 'Collectivite',
            self::TYPE_ASSOCIATION => 'Association',
            self::TYPE_ENTREPRISE => 'Entreprise',
            self::TYPE_AUTRE => 'Autre',
        ];
    }

    public static function generateMemberNumber(): string
    {
        $year = date('Y');
        $lastMember = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastMember ? (int) substr($lastMember->member_number, -4) + 1 : 1;

        return sprintf('OR%s%04d', $year, $sequence);
    }
}
