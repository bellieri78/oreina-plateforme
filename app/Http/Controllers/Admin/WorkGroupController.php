<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WorkGroupJoinApproved;
use App\Mail\WorkGroupJoinRejected;
use App\Models\Member;
use App\Models\WorkGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WorkGroupController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkGroup::withCount('members');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $allowedSorts = ['name', 'created_at'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('name', 'asc');
        }

        $workGroups = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => WorkGroup::count(),
            'active' => WorkGroup::where('is_active', true)->count(),
            'total_members' => \DB::table('work_group_member')->count(),
        ];

        return view('admin.work-groups.index', compact('workGroups', 'stats'));
    }

    public function create()
    {
        return view('admin.work-groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'join_policy' => 'required|in:open,request',
            'has_resources' => 'boolean',
            'has_collaborative_space' => 'boolean',
            'collaborative_space_url' => 'nullable|url|max:255',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['has_resources'] = $request->boolean('has_resources');
        $validated['has_collaborative_space'] = $request->boolean('has_collaborative_space');

        $workGroup = WorkGroup::create($validated);

        return redirect()
            ->route('admin.work-groups.edit', $workGroup)
            ->with('success', 'Groupe de travail cree avec succes.');
    }

    public function edit(WorkGroup $workGroup)
    {
        $workGroup->load('members');

        $existingIds = $workGroup->members()->pluck('members.id')->toArray();
        $availableMembers = Member::whereNotIn('id', $existingIds)
            ->active()
            ->orderBy('last_name')
            ->get();

        return view('admin.work-groups.edit', compact('workGroup', 'availableMembers'));
    }

    public function update(Request $request, WorkGroup $workGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'join_policy' => 'required|in:open,request',
            'has_resources' => 'boolean',
            'has_collaborative_space' => 'boolean',
            'collaborative_space_url' => 'nullable|url|max:255',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['has_resources'] = $request->boolean('has_resources');
        $validated['has_collaborative_space'] = $request->boolean('has_collaborative_space');

        $workGroup->update($validated);

        return redirect()
            ->route('admin.work-groups.edit', $workGroup)
            ->with('success', 'Groupe de travail mis a jour.');
    }

    public function destroy(WorkGroup $workGroup)
    {
        $workGroup->members()->detach();
        $workGroup->delete();

        return redirect()
            ->route('admin.work-groups.index')
            ->with('success', 'Groupe de travail supprime.');
    }

    public function addMember(Request $request, WorkGroup $workGroup)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'role' => 'nullable|string|max:50',
        ]);

        if ($workGroup->members()->where('member_id', $validated['member_id'])->exists()) {
            return back()->with('error', 'Ce contact est deja membre de ce groupe.');
        }

        $workGroup->members()->attach($validated['member_id'], [
            'role' => $validated['role'] ?? 'member',
            'joined_at' => now()->toDateString(),
        ]);

        return back()->with('success', 'Membre ajoute au groupe de travail.');
    }

    public function removeMember(WorkGroup $workGroup, Member $member)
    {
        $workGroup->members()->detach($member->id);

        return back()->with('success', 'Membre retire du groupe de travail.');
    }

    public function approveRequest(WorkGroup $workGroup, Member $member)
    {
        $workGroup->members()->updateExistingPivot($member->id, [
            'status' => 'active',
            'joined_at' => now()->toDateString(),
        ]);

        if ($member->email) {
            Mail::to($member->email)->send(new WorkGroupJoinApproved($workGroup));
        }

        return back()->with('success', 'Demande acceptee.');
    }

    public function rejectRequest(WorkGroup $workGroup, Member $member)
    {
        $workGroup->members()->detach($member->id);

        if ($member->email) {
            Mail::to($member->email)->send(new WorkGroupJoinRejected($workGroup));
        }

        return back()->with('success', 'Demande refusee.');
    }
}
