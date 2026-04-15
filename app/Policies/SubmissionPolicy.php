<?php

namespace App\Policies;

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
}
