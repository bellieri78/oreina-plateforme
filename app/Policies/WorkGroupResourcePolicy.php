<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkGroup;
use App\Models\WorkGroupResource;

class WorkGroupResourcePolicy
{
    public function create(User $user, WorkGroup $workGroup): bool
    {
        return app(WorkGroupPolicy::class)->manage($user, $workGroup);
    }

    public function delete(User $user, WorkGroupResource $resource): bool
    {
        return app(WorkGroupPolicy::class)->manage($user, $resource->workGroup);
    }
}
