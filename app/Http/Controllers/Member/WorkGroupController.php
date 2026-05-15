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
            ->withCount(['members as active_members_count' => fn ($q) => $q->where('work_group_member.status', 'active')])
            ->orderBy('name')
            ->get();

        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        $myStatuses = [];
        if ($member) {
            foreach ($member->workGroups()->withPivot('status')->get() as $g) {
                $myStatuses[$g->id] = $g->pivot->status;
            }
        }

        return view('member.work-groups.index', compact('workGroups', 'member', 'myStatuses'));
    }

    public function show(WorkGroup $workGroup)
    {
        $user = auth()->user();
        abort_unless($user->can('view', $workGroup), 403);

        $member = Member::where('user_id', $user->id)->first();
        $status = $workGroup->membershipStatusFor($member);
        $canManage = $user->can('manage', $workGroup);

        // Ressources réservées aux membres actifs du groupe (ou aux gestionnaires).
        // Un adhérent en aperçu (non-membre / demande en attente) ne voit pas le contenu.
        $canViewResources = $workGroup->has_resources && ($status === 'active' || $canManage);

        $workGroup->loadCount(['members as active_members_count' => fn ($q) => $q->where('work_group_member.status', 'active')]);
        $coordinators = $workGroup->coordinators()->get();
        $resources = $canViewResources
            ? $workGroup->resources()->orderBy('category')->orderBy('title')->get()->groupBy('category')
            : collect();
        $pending = $canManage && $workGroup->join_policy === 'request'
            ? $workGroup->pendingRequests()->get()
            : collect();
        $activeMembers = $canManage ? $workGroup->activeMembers()->get() : collect();

        return view('member.work-groups.show', compact(
            'workGroup', 'member', 'status', 'canManage', 'canViewResources',
            'coordinators', 'resources', 'pending', 'activeMembers'
        ));
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
