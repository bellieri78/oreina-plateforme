<?php

namespace App\Policies;

use App\Enums\SubmissionStatus;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;
use App\Services\SubmissionStateMachine;

class SubmissionPolicy
{
    public function view(User $user, Submission $submission): bool
    {
        return $this->hasAccess($user, $submission);
    }

    public function viewFile(User $user, Submission $submission): bool
    {
        return $this->hasAccess($user, $submission);
    }

    private function hasAccess(User $user, Submission $submission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $submission->author_id) {
            return true;
        }

        if ($submission->editor_id !== null && $user->id === $submission->editor_id) {
            return true;
        }

        if ($submission->reviews()->where('reviewer_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    public function viewEditorial(User $user, Submission $submission): bool
    {
        if ($user->hasCapability(EditorialCapability::CHIEF_EDITOR) || $user->isAdmin()) {
            return true;
        }
        if ($submission->editor_id === $user->id) {
            return true;
        }
        if ($submission->layout_editor_id === $user->id) {
            return true;
        }
        return $submission->reviews()->where('reviewer_id', $user->id)->exists();
    }

    public function takeEditor(User $user, Submission $submission): bool
    {
        return $submission->editor_id === null
            && $user->hasCapability(EditorialCapability::EDITOR);
    }

    public function assignEditor(User $user, Submission $submission): bool
    {
        return $user->isAdmin() || $user->hasCapability(EditorialCapability::CHIEF_EDITOR);
    }

    public function assignLayoutEditor(User $user, Submission $submission): bool
    {
        return $user->isAdmin() || $user->hasCapability(EditorialCapability::CHIEF_EDITOR);
    }

    public function assignReviewer(User $user, Submission $submission): bool
    {
        if (!$submission->status->isEditorial()) {
            return false;
        }

        return $user->isAdmin()
            || $user->hasCapability(EditorialCapability::CHIEF_EDITOR)
            || $submission->editor_id === $user->id;
    }

    public function manageCapabilities(User $user, User $target): bool
    {
        return $user->isAdmin() || $user->hasCapability(EditorialCapability::CHIEF_EDITOR);
    }

    public function transitionTo(User $user, Submission $submission, SubmissionStatus $target): bool
    {
        $current = $submission->status;

        if (!$current instanceof SubmissionStatus) {
            return false;
        }

        if (!app(SubmissionStateMachine::class)->canTransition($current, $target)) {
            return false;
        }

        $editorialLayoutTeam = $user->isAdmin()
            || $user->hasCapability(EditorialCapability::CHIEF_EDITOR)
            || $submission->layout_editor_id === $user->id;

        return match ($target) {
            SubmissionStatus::Submitted => $user->id === $submission->author_id,

            SubmissionStatus::UnderInitialReview =>
                $current === SubmissionStatus::RevisionRequested
                    ? $user->id === $submission->author_id
                    : ($current === SubmissionStatus::Submitted
                        ? ($user->isAdmin()
                            || $user->hasCapability(EditorialCapability::CHIEF_EDITOR)
                            || $submission->editor_id === $user->id)
                        : false),

            SubmissionStatus::UnderPeerReview =>
                $current === SubmissionStatus::RevisionAfterReview
                    ? $user->id === $submission->author_id
                    : ($user->isAdmin()
                        || $user->hasCapability(EditorialCapability::CHIEF_EDITOR)
                        || $submission->editor_id === $user->id),

            SubmissionStatus::RevisionRequested,
            SubmissionStatus::RevisionAfterReview,
            SubmissionStatus::Accepted,
            SubmissionStatus::Rejected =>
                $user->isAdmin()
                || $user->hasCapability(EditorialCapability::CHIEF_EDITOR)
                || $submission->editor_id === $user->id,

            SubmissionStatus::AwaitingAuthorApproval =>
                $user->isAdmin()
                || $user->hasCapability(EditorialCapability::CHIEF_EDITOR)
                || $submission->editor_id === $user->id
                || $submission->layout_editor_id === $user->id,

            SubmissionStatus::Published,
            SubmissionStatus::InProduction =>
                $current === SubmissionStatus::AwaitingAuthorApproval
                    ? $user->id === $submission->author_id
                    : $editorialLayoutTeam,

            default => false,
        };
    }
}
