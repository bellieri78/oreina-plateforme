<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;
use App\Models\WorkGroupForumPost;

class WorkGroupForumPostPolicy
{
    private function manages(User $user, WorkGroupForumPost $post): bool
    {
        $workGroup = $post->thread?->category?->workGroup;
        return $workGroup
            ? app(WorkGroupPolicy::class)->manage($user, $workGroup)
            : false;
    }

    private function isAuthor(User $user, WorkGroupForumPost $post): bool
    {
        $member = Member::where('user_id', $user->id)->first();
        return $member && $post->member_id === $member->id;
    }

    public function update(User $user, WorkGroupForumPost $post): bool
    {
        if ($this->manages($user, $post)) {
            return true;
        }

        $locked = $post->thread?->is_locked ?? false;
        return ! $locked && $this->isAuthor($user, $post);
    }

    public function delete(User $user, WorkGroupForumPost $post): bool
    {
        return $this->update($user, $post);
    }
}
