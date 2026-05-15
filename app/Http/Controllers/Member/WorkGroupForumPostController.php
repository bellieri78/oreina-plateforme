<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\WorkGroup;
use App\Models\WorkGroupForumPost;
use App\Models\WorkGroupForumThread;
use Illuminate\Http\Request;

class WorkGroupForumPostController extends Controller
{
    public function store(Request $request, WorkGroup $workGroup, WorkGroupForumThread $thread)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($thread->category->work_group_id === $workGroup->id, 404);

        $user = $request->user();
        abort_unless($user->can('participate', $workGroup), 403);

        if ($thread->is_locked && ! $user->can('manage', $workGroup)) {
            abort(403, 'Ce fil est verrouillé.');
        }

        $data = $request->validate(['content' => 'required|string']);
        $member = Member::where('user_id', $user->id)->first();

        WorkGroupForumPost::create([
            'work_group_forum_thread_id' => $thread->id,
            'member_id' => $member?->id,
            'content' => $data['content'],
        ]);

        $thread->update(['last_posted_at' => now()]);

        return back()->with('success', 'Réponse publiée.');
    }

    public function update(Request $request, WorkGroup $workGroup, WorkGroupForumPost $post)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($post->thread->category->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('update', $post), 403);

        $data = $request->validate(['content' => 'required|string']);
        $post->update(['content' => $data['content']]);

        return back()->with('success', 'Message modifié.');
    }

    public function destroy(Request $request, WorkGroup $workGroup, WorkGroupForumPost $post)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($post->thread->category->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('delete', $post), 403);

        $post->delete();

        return back()->with('success', 'Message supprimé.');
    }
}
