<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionTransition extends Model
{
    public const ACTION_EDITOR_ASSIGNED        = 'editor_assigned';
    public const ACTION_EDITOR_TAKEN           = 'editor_taken';
    public const ACTION_EDITOR_REVOKED         = 'editor_revoked';
    public const ACTION_LAYOUT_EDITOR_ASSIGNED = 'layout_editor_assigned';
    public const ACTION_LAYOUT_EDITOR_REVOKED  = 'layout_editor_revoked';
    public const ACTION_REVIEWER_INVITED       = 'reviewer_invited';
    public const ACTION_STATUS_CHANGED         = 'status_changed';

    protected $fillable = [
        'submission_id',
        'actor_user_id',
        'action',
        'target_user_id',
        'from_status',
        'to_status',
        'notes',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
