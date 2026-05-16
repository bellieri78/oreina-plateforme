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
        if (! $user->can('view', $workGroup)) {
            return redirect()->route('hub.membership')
                ->with('error', "Les groupes de travail sont réservés aux adhérents à jour de cotisation.");
        }

        $member = Member::where('user_id', $user->id)->first();
        $status = $workGroup->membershipStatusFor($member);
        $canManage = $user->can('manage', $workGroup);
        $canParticipate = $user->can('participate', $workGroup);

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

        $forumCategories = $workGroup->has_forum
            ? $workGroup->forumCategories()
                ->ordered()
                ->with(['threads' => fn ($q) => $q->ordered()->withCount('posts')->with('author')])
                ->get()
            : collect();

        $joinEvents = $workGroup->members()
            ->wherePivot('status', 'active')
            ->orderByPivot('joined_at', 'desc')
            ->limit(10)
            ->get()
            ->filter(fn ($m) => $m->pivot->joined_at)
            ->map(fn ($m) => [
                'type' => 'join',
                'date' => \Illuminate\Support\Carbon::parse($m->pivot->joined_at),
                'label' => ($m->full_name ?? $m->first_name ?? 'Un membre') . ' a rejoint le groupe',
                'href' => null,
            ]);

        $threadEvents = \App\Models\WorkGroupForumThread::query()
            ->whereHas('category', fn ($q) => $q->where('work_group_id', $workGroup->id))
            ->with('author')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($t) => [
                'type' => 'thread',
                'date' => $t->created_at,
                'label' => 'Nouveau fil : ' . $t->title,
                'href' => route('member.work-groups.forum.threads.show', [$workGroup, $t]),
            ]);

        $resourceEvents = $workGroup->resources()
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'type' => 'resource',
                'date' => $r->created_at,
                'label' => 'Nouvelle ressource : ' . $r->title,
                'href' => null,
            ]);

        $activity = $joinEvents
            ->concat($threadEvents)
            ->concat($resourceEvents)
            ->sortByDesc('date')
            ->values()
            ->take(10);

        $projects = $workGroup->projects()->ordered()->get();

        return view('member.work-groups.show', compact(
            'workGroup', 'member', 'status', 'canManage', 'canViewResources',
            'coordinators', 'resources', 'pending', 'activeMembers',
            'canParticipate', 'forumCategories', 'activity', 'projects'
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
