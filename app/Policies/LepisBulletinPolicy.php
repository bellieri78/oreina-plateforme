<?php

namespace App\Policies;

use App\Models\EditorialCapability;
use App\Models\LepisBulletin;
use App\Models\User;

class LepisBulletinPolicy
{
    public function download(?User $user, LepisBulletin $bulletin): bool
    {
        if ($bulletin->isPublic()) {
            return true;
        }

        if ($user === null) {
            return false;
        }

        if ($user->hasCapability(EditorialCapability::LEPIS_EDITOR) || $user->isAdmin()) {
            return true;
        }

        if ($bulletin->isInMembersPhase()) {
            return (bool) $user->member?->isCurrentMember();
        }

        return false;
    }
}
