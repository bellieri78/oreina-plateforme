<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'submission_id',
        'reviewer_id',
        'assigned_by',
        'status',
        'invited_at',
        'responded_at',
        'due_date',
        'completed_at',
        'recommendation',
        'comments_to_editor',
        'comments_to_author',
        'score_originality',
        'score_methodology',
        'score_clarity',
        'score_significance',
        'score_references',
        'review_file',
        'last_reminder_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'last_reminder_at' => 'datetime',
        'score_originality' => 'integer',
        'score_methodology' => 'integer',
        'score_clarity' => 'integer',
        'score_significance' => 'integer',
        'score_references' => 'integer',
    ];

    // Statuses
    public const STATUS_INVITED = 'invited';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_EXPIRED = 'expired';

    // Recommendations
    public const RECOMMENDATION_ACCEPT = 'accept';
    public const RECOMMENDATION_MINOR = 'minor_revision';
    public const RECOMMENDATION_MAJOR = 'major_revision';
    public const RECOMMENDATION_REJECT = 'reject';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_INVITED => 'Invité',
            self::STATUS_ACCEPTED => 'Accepté',
            self::STATUS_DECLINED => 'Décliné',
            self::STATUS_COMPLETED => 'Terminé',
            self::STATUS_EXPIRED => 'Expiré',
        ];
    }

    public static function getRecommendations(): array
    {
        return [
            self::RECOMMENDATION_ACCEPT => 'Accepter',
            self::RECOMMENDATION_MINOR => 'Révision mineure',
            self::RECOMMENDATION_MAJOR => 'Révision majeure',
            self::RECOMMENDATION_REJECT => 'Rejeter',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_INVITED, self::STATUS_ACCEPTED]);
    }

    public function getAverageScoreAttribute(): ?float
    {
        $scores = array_filter([
            $this->score_originality,
            $this->score_methodology,
            $this->score_clarity,
            $this->score_significance,
            $this->score_references,
        ]);

        if (empty($scores)) {
            return null;
        }

        return round(array_sum($scores) / count($scores), 1);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_INVITED, self::STATUS_ACCEPTED]);
    }

    public function scopeForSubmission($query, int $submissionId)
    {
        return $query->where('submission_id', $submissionId);
    }

    public function scopeByReviewer($query, int $reviewerId)
    {
        return $query->where('reviewer_id', $reviewerId);
    }
}
