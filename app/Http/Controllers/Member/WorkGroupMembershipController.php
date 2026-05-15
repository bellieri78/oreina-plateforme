<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Mail\WorkGroupJoinApproved;
use App\Mail\WorkGroupJoinRejected;
use App\Mail\WorkGroupJoinRequested;
use App\Models\Member;
use App\Models\WorkGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WorkGroupMembershipController extends Controller
{
    private function currentMember(): ?Member
    {
        return Member::where('user_id', auth()->id())->first();
    }

    public function join(WorkGroup $workGroup)
    {
        $member = $this->currentMember();
        abort_unless($member && $member->isCurrentMember(), 403);

        if ($workGroup->members()->where('members.id', $member->id)->exists()) {
            return back()->with('error', 'Vous êtes déjà lié à ce groupe.');
        }

        if ($workGroup->join_policy === 'open') {
            $workGroup->members()->attach($member->id, [
                'role' => 'member', 'status' => 'active', 'joined_at' => now()->toDateString(),
            ]);
            return back()->with('success', 'Vous avez rejoint le groupe.');
        }

        $workGroup->members()->attach($member->id, [
            'role' => 'member', 'status' => 'pending', 'requested_at' => now(),
        ]);

        foreach ($workGroup->coordinators()->get() as $coord) {
            if ($coord->email) {
                Mail::to($coord->email)->send(new WorkGroupJoinRequested($workGroup, $member));
            }
        }

        return back()->with('success', 'Votre demande a été envoyée aux coordinateurs.');
    }

    public function leave(WorkGroup $workGroup)
    {
        $member = $this->currentMember();
        abort_unless($member, 403);

        $this->guardLastCoordinator($workGroup, $member);

        $workGroup->members()->detach($member->id);
        return back()->with('success', 'Vous avez quitté le groupe.');
    }

    public function addMember(Request $request, WorkGroup $workGroup)
    {
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $data = $request->validate([
            'member_id' => 'required|exists:members,id',
            'role' => 'nullable|in:member,coordinator',
        ]);

        if ($workGroup->members()->where('members.id', $data['member_id'])->exists()) {
            return back()->with('error', 'Ce membre est déjà dans le groupe.');
        }

        $workGroup->members()->attach($data['member_id'], [
            'role' => $data['role'] ?? 'member',
            'status' => 'active',
            'joined_at' => now()->toDateString(),
        ]);

        return back()->with('success', 'Membre ajouté.');
    }

    public function removeMember(Request $request, WorkGroup $workGroup, Member $member)
    {
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $this->guardLastCoordinator($workGroup, $member);

        $workGroup->members()->detach($member->id);
        return back()->with('success', 'Membre retiré.');
    }

    public function approve(Request $request, WorkGroup $workGroup, Member $member)
    {
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $workGroup->members()->updateExistingPivot($member->id, [
            'status' => 'active', 'joined_at' => now()->toDateString(),
        ]);

        if ($member->email) {
            Mail::to($member->email)->send(new WorkGroupJoinApproved($workGroup));
        }

        return back()->with('success', 'Demande acceptée.');
    }

    public function reject(Request $request, WorkGroup $workGroup, Member $member)
    {
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $workGroup->members()->detach($member->id);

        if ($member->email) {
            Mail::to($member->email)->send(new WorkGroupJoinRejected($workGroup));
        }

        return back()->with('success', 'Demande refusée.');
    }

    private function guardLastCoordinator(WorkGroup $workGroup, Member $member): void
    {
        $isCoord = $workGroup->members()
            ->wherePivot('status', 'active')->wherePivot('role', 'coordinator')
            ->where('members.id', $member->id)->exists();

        if ($isCoord && $workGroup->coordinators()->count() <= 1) {
            abort(422, "Vous êtes le dernier coordinateur : désignez un autre coordinateur ou contactez un administrateur.");
        }
    }
}
