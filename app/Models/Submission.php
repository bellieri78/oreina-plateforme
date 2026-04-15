<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    protected $fillable = [
        'author_id',
        'journal_issue_id',
        'title',
        'abstract',
        'content_html',
        'content_blocks',
        'references',
        'acknowledgements',
        'author_affiliations',
        'manuscript_file',
        'supplementary_files',
        'pdf_file',
        'featured_image',
        'co_authors',
        'keywords',
        'status',
        'editor_id',
        'editor_notes',
        'decision',
        'decision_at',
        'doi',
        'start_page',
        'end_page',
        'submitted_at',
        'received_at',
        'accepted_at',
        'published_at',
    ];

    protected $casts = [
        'co_authors' => 'array',
        'keywords' => 'array',
        'supplementary_files' => 'array',
        'content_blocks' => 'array',
        'references' => 'array',
        'author_affiliations' => 'array',
        'decision_at' => 'datetime',
        'submitted_at' => 'datetime',
        'received_at' => 'date',
        'accepted_at' => 'date',
        'published_at' => 'datetime',
    ];

    // Statuses
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_DESK_REVIEW = 'desk_review';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_REVISION = 'revision';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PUBLISHED = 'published';

    // Decisions
    public const DECISION_ACCEPT = 'accept';
    public const DECISION_MINOR = 'minor_revision';
    public const DECISION_MAJOR = 'major_revision';
    public const DECISION_REJECT = 'reject';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Brouillon',
            self::STATUS_SUBMITTED => 'Soumis',
            self::STATUS_DESK_REVIEW => 'Évaluation initiale',
            self::STATUS_IN_REVIEW => 'En révision',
            self::STATUS_REVISION => 'Révision demandée',
            self::STATUS_ACCEPTED => 'Accepté',
            self::STATUS_REJECTED => 'Rejeté',
            self::STATUS_PUBLISHED => 'Publié',
        ];
    }

    public static function getDecisions(): array
    {
        return [
            self::DECISION_ACCEPT => 'Accepter',
            self::DECISION_MINOR => 'Révision mineure',
            self::DECISION_MAJOR => 'Révision majeure',
            self::DECISION_REJECT => 'Rejeter',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function journalIssue(): BelongsTo
    {
        return $this->belongsTo(JournalIssue::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function completedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', Review::STATUS_COMPLETED);
    }

    public function pendingReviews(): HasMany
    {
        return $this->hasMany(Review::class)->whereIn('status', [Review::STATUS_INVITED, Review::STATUS_ACCEPTED]);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && $this->published_at !== null;
    }

    public function canBeReviewed(): bool
    {
        return in_array($this->status, [self::STATUS_DESK_REVIEW, self::STATUS_IN_REVIEW]);
    }

    public function needsDecision(): bool
    {
        return $this->status === self::STATUS_IN_REVIEW
            && $this->completedReviews()->count() >= 2;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_SUBMITTED => 'info',
            self::STATUS_DESK_REVIEW => 'warning',
            self::STATUS_IN_REVIEW => 'primary',
            self::STATUS_REVISION => 'warning',
            self::STATUS_ACCEPTED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_PUBLISHED => 'success',
            default => 'gray',
        };
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopePendingReview($query)
    {
        return $query->whereIn('status', [self::STATUS_SUBMITTED, self::STATUS_DESK_REVIEW, self::STATUS_IN_REVIEW]);
    }

    public function scopeByAuthor($query, int $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function getPageRangeAttribute(): ?string
    {
        if ($this->start_page && $this->end_page) {
            return "{$this->start_page}-{$this->end_page}";
        }
        return null;
    }

    public function getCitationAttribute(): string
    {
        $authors = $this->author->name;
        if (!empty($this->co_authors)) {
            $coAuthorNames = collect($this->co_authors)->pluck('name')->join(', ');
            $authors .= ", {$coAuthorNames}";
        }

        $issue = $this->journalIssue;
        $pages = $this->page_range ?? '';
        $year = $this->published_at?->format('Y') ?? '';

        return "{$authors} ({$year}). {$this->title}. Oreina, {$issue?->volume_number}({$issue?->issue_number}), {$pages}.";
    }
}
