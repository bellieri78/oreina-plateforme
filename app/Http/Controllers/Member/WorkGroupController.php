<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\WorkGroup;

class WorkGroupController extends Controller
{
    public function index()
    {
        $workGroups = WorkGroup::active()
            ->withCount('members')
            ->orderBy('name')
            ->get();

        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();
        $myGroupIds = $member?->workGroups()->pluck('work_groups.id')->toArray() ?? [];

        return view('member.work-groups.index', compact('workGroups', 'myGroupIds'));
    }

    public function contributions()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        $myGroups = $member?->workGroups()
            ->active()
            ->withCount('members')
            ->orderBy('name')
            ->get() ?? collect();

        $stats = [
            'groups_count' => $myGroups->count(),
            'total_members' => $myGroups->sum('members_count'),
        ];

        return view('member.contributions.index', compact('myGroups', 'stats', 'member'));
    }
}
