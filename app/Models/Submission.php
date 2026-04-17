<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
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
        'display_authors',
        'manuscript_file',
        'supplementary_files',
        'pdf_file',
        'featured_image',
        'co_authors',
        'keywords',
        'status',
        'editor_id',
        'layout_editor_id',
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
        'redirected_to_lepis',
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
        'status' => SubmissionStatus::class,
        'redirected_to_lepis' => 'boolean',
    ];

    // Statuses
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_UNDER_INITIAL_REVIEW  = 'under_initial_review';
    public const STATUS_REVISION_REQUESTED    = 'revision_requested';
    public const STATUS_UNDER_PEER_REVIEW     = 'under_peer_review';
    public const STATUS_REVISION_AFTER_REVIEW = 'revision_after_review';
    public const STATUS_IN_PRODUCTION         = 'in_production';
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
        return SubmissionStatus::labels();
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

    public function layoutEditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'layout_editor_id');
    }

    public function journalIssue(): BelongsTo
    {
        return $this->belongsTo(JournalIssue::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(\App\Models\SubmissionTransition::class);
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
        return $this->status === SubmissionStatus::Published && $this->published_at !== null;
    }

    public function canBeReviewed(): bool
    {
        return in_array($this->status, [
            SubmissionStatus::UnderInitialReview,
            SubmissionStatus::UnderPeerReview,
        ], true);
    }

    public function needsDecision(): bool
    {
        return $this->status === SubmissionStatus::UnderPeerReview
            && $this->completedReviews()->count() >= 2;
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status instanceof SubmissionStatus
            ? $this->status->color()
            : 'gray';
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePublished($query)
    {
        return $query->where('status', SubmissionStatus::Published->value);
    }

    public function scopePendingReview($query)
    {
        return $query->whereIn('status', [
            SubmissionStatus::Submitted->value,
            SubmissionStatus::UnderInitialReview->value,
            SubmissionStatus::UnderPeerReview->value,
        ]);
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

        $volume = $issue ? "Chersotis, Tome {$issue->volume_number}" : config('journal.name');
        return "{$authors} ({$year}). {$this->title}. {$volume}, {$pages}.";
    }
}
