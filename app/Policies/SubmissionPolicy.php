<?php

namespace App\Policies;

use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;

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
        return $user->isAdmin()
            || $user->hasCapability(EditorialCapability::CHIEF_EDITOR)
            || $submission->editor_id === $user->id;
    }

    public function manageCapabilities(User $user, User $target): bool
    {
        return $user->isAdmin() || $user->hasCapability(EditorialCapability::CHIEF_EDITOR);
    }
}
