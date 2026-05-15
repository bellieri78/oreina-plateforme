<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\WorkGroup;
use App\Models\WorkGroupForumCategory;
use App\Models\WorkGroupForumPost;
use App\Models\WorkGroupForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkGroupForumController extends Controller
{
    public function index(WorkGroup $workGroup)
    {
        abort_unless($workGroup->has_forum, 404);
        $user = auth()->user();
        abort_unless($user->can('view', $workGroup), 403);

        return redirect()->route('member.work-groups.show', [$workGroup, 'tab' => 'discussions']);
    }

    public function showThread(WorkGroup $workGroup, WorkGroupForumThread $thread)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($thread->category->work_group_id === $workGroup->id, 404);

        $user = auth()->user();
        abort_unless($user->can('view', $workGroup), 403);

        $member = Member::where('user_id', $user->id)->first();
        $status = $workGroup->membershipStatusFor($member);
        $canManage = $user->can('manage', $workGroup);
        $canParticipate = $user->can('participate', $workGroup);

        $thread->load('author', 'category');
        $posts = $thread->posts()->with('author')->oldest()->paginate(30);

        $isSubscribed = $thread->isSubscribed($member);

        return view('member.work-groups.forum.thread', compact(
            'workGroup', 'thread', 'posts', 'member', 'status', 'canManage', 'canParticipate', 'isSubscribed'
        ));
    }

    public function storeThread(Request $request, WorkGroup $workGroup)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($request->user()->can('participate', $workGroup), 403);

        $data = $request->validate([
            'work_group_forum_category_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $category = WorkGroupForumCategory::where('id', $data['work_group_forum_category_id'])
            ->where('work_group_id', $workGroup->id)
            ->firstOrFail();

        $member = Member::where('user_id', auth()->id())->first();

        $thread = DB::transaction(function () use ($category, $member, $data) {
            $thread = WorkGroupForumThread::create([
                'work_group_forum_category_id' => $category->id,
                'member_id' => $member?->id,
                'title' => $data['title'],
                'last_posted_at' => now(),
            ]);
            WorkGroupForumPost::create([
                'work_group_forum_thread_id' => $thread->id,
                'member_id' => $member?->id,
                'content' => $data['content'],
            ]);
            return $thread;
        });

        if ($member) {
            $thread->subscribers()->syncWithoutDetaching([$member->id]);
        }

        return redirect()
            ->route('member.work-groups.forum.threads.show', [$workGroup, $thread])
            ->with('success', 'Fil créé.');
    }

    public function destroyThread(Request $request, WorkGroup $workGroup, WorkGroupForumThread $thread)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($thread->category->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $thread->delete();

        return redirect()
            ->route('member.work-groups.show', [$workGroup, 'tab' => 'discussions'])
            ->with('success', 'Fil supprimé.');
    }

    public function pinThread(Request $request, WorkGroup $workGroup, WorkGroupForumThread $thread)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($thread->category->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $thread->update(['is_pinned' => ! $thread->is_pinned]);

        return back()->with('success', $thread->is_pinned ? 'Fil épinglé.' : 'Fil désépinglé.');
    }

    public function lockThread(Request $request, WorkGroup $workGroup, WorkGroupForumThread $thread)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($thread->category->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $thread->update(['is_locked' => ! $thread->is_locked]);

        return back()->with('success', $thread->is_locked ? 'Fil verrouillé.' : 'Fil déverrouillé.');
    }

    public function subscribe(Request $request, WorkGroup $workGroup, WorkGroupForumThread $thread)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($thread->category->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('participate', $workGroup), 403);

        $member = Member::where('user_id', $request->user()->id)->first();
        if ($member) {
            $thread->subscribers()->syncWithoutDetaching([$member->id]);
        }

        return back()->with('success', 'Vous suivez ce fil.');
    }

    public function unsubscribe(Request $request, WorkGroup $workGroup, WorkGroupForumThread $thread)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($thread->category->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('participate', $workGroup), 403);

        $member = Member::where('user_id', $request->user()->id)->first();
        if ($member) {
            $thread->subscribers()->detach($member->id);
        }

        return back()->with('success', 'Vous ne suivez plus ce fil.');
    }
}
