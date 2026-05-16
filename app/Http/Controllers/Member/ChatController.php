<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request, ChatService $chat)
    {
        $member = Member::where('user_id', auth()->id())->first();
        if (! $member) {
            return view('member.chat.index', [
                'member' => null,
                'initialConversationId' => null,
                'initialTargetId' => null,
            ]);
        }

        $initialConversationId = null;
        $initialTargetId = null;

        if ($request->filled('with')) {
            $target = Member::find((int) $request->query('with'));
            if (! $target || $target->id === $member->id) {
                abort(404);
            }
            $check = $chat->canStartWith($member, $target);
            if ($check !== true) {
                return redirect()->route('member.directory.index')->with('error', $check);
            }
            $resolved = $chat->resolveTarget($member, $target);
            if ($resolved['conversation']) {
                $initialConversationId = $resolved['conversation']->id;
            } else {
                $initialTargetId = $target->id;
            }
        } elseif ($request->filled('c')) {
            $initialConversationId = (int) $request->query('c');
        }

        return view('member.chat.index', compact('member', 'initialConversationId', 'initialTargetId'));
    }
}
