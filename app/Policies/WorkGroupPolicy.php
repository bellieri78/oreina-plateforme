<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;
use App\Models\WorkGroup;

class WorkGroupPolicy
{
    private function memberOf(User $user): ?Member
    {
        return Member::where('user_id', $user->id)->first();
    }

    public function view(User $user, WorkGroup $workGroup): bool
    {
        $member = $this->memberOf($user);
        return $member?->isCurrentMember() ?? false;
    }

    public function manage(User $user, WorkGroup $workGroup): bool
    {
        if ($user->isAdmin() || $user->isEditor()) {
            return true;
        }

        return $workGroup->isCoordinator($this->memberOf($user));
    }

    public function participate(User $user, WorkGroup $workGroup): bool
    {
        $member = $this->memberOf($user);
        if (! $member) {
            return false;
        }

        return $workGroup->membershipStatusFor($member) === 'active';
    }
}
