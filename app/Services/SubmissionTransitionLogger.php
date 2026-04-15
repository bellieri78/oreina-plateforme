<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\SubmissionTransition;
use App\Models\User;

class SubmissionTransitionLogger
{
    public function log(
        Submission $submission,
        string $action,
        ?User $actor = null,
        ?User $target = null,
        ?string $fromStatus = null,
        ?string $toStatus = null,
        ?string $notes = null,
    ): SubmissionTransition {
        return SubmissionTransition::create([
            'submission_id'  => $submission->id,
            'actor_user_id'  => $actor?->id,
            'action'         => $action,
            'target_user_id' => $target?->id,
            'from_status'    => $fromStatus,
            'to_status'      => $toStatus,
            'notes'          => $notes,
        ]);
    }
}
