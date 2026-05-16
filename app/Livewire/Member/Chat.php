<?php

namespace App\Livewire\Member;

use App\Models\ChatBlock;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Member;
use App\Services\ChatService;
use Livewire\Component;

class Chat extends Component
{
    public ?int $selectedConversationId = null;
    public ?int $draftTargetId = null;
    public string $body = '';

    public function mount(?int $initialConversationId = null, ?int $initialTargetId = null): void
    {
        $me = $this->me();
        if ($initialConversationId) {
            $c = Conversation::find($initialConversationId);
            if ($c && $c->isParticipant($me)) {
                $this->selectedConversationId = $c->id;
                $c->markReadFor($me);
            }
        } elseif ($initialTargetId) {
            $this->draftTargetId = $initialTargetId;
        }
    }

    private function me(): Member
    {
        return Member::where('user_id', auth()->id())->firstOrFail();
    }

    public function selectConversation(int $id): void
    {
        $me = $this->me();
        $c = Conversation::find($id);
        if (! $c || ! $c->isParticipant($me)) {
            return;
        }
        $this->selectedConversationId = $id;
        $this->draftTargetId = null;
        $c->markReadFor($me);
    }

    public function sendMessage(ChatService $chat): void
    {
        $this->validate(['body' => 'required|string|max:2000']);
        $me = $this->me();
        $c = $this->selectedConversationId ? Conversation::find($this->selectedConversationId) : null;
        if ($c && ! $c->isParticipant($me)) {
            $c = null;
        }
        $draft = (! $c && $this->draftTargetId) ? Member::find($this->draftTargetId) : null;
        if (! $c && ! $draft) {
            return;
        }
        if ($c) {
            $err = $chat->canSend($me, $c);
            if ($err !== true) {
                $this->addError('body', $err);
                return;
            }
        }
        try {
            $message = $chat->send($me, $c, $draft, $this->body);
        } catch (\RuntimeException $e) {
            $this->addError('body', $e->getMessage());
            return;
        }
        $this->selectedConversationId = $message->conversation_id;
        $this->draftTargetId = null;
        $this->body = '';
    }

    public function deleteMessage(int $messageId): void
    {
        $me = $this->me();
        $m = ChatMessage::find($messageId);
        if (! $m || $m->sender_id !== $me->id || $m->isDeleted()) {
            return;
        }
        $m->forceFill(['content' => '', 'deleted_at' => now()])->save();
    }

    public function blockOther(ChatService $chat): void
    {
        $me = $this->me();
        $c = $this->selectedConversationId ? Conversation::find($this->selectedConversationId) : null;
        if (! $c || ! $c->isParticipant($me)) {
            return;
        }
        $chat->block($me, $c->otherMember($me));
    }

    public function unblockOther(ChatService $chat): void
    {
        $me = $this->me();
        $c = $this->selectedConversationId ? Conversation::find($this->selectedConversationId) : null;
        if (! $c || ! $c->isParticipant($me)) {
            return;
        }
        $chat->unblock($me, $c->otherMember($me));
    }

    public function render()
    {
        $me = $this->me();

        $conversations = Conversation::where(fn ($q) => $q
                ->where('member_low_id', $me->id)->orWhere('member_high_id', $me->id))
            ->whereNotNull('last_message_at')
            ->orderByDesc('last_message_at')
            ->get();

        $active = $this->selectedConversationId ? Conversation::find($this->selectedConversationId) : null;
        if ($active && ! $active->isParticipant($me)) {
            $active = null;
        }
        $draftTarget = (! $active && $this->draftTargetId) ? Member::find($this->draftTargetId) : null;

        $messages = $active
            ? $active->messages()->with('sender')->orderBy('created_at')->get()
            : collect();
        $other = $active ? $active->otherMember($me) : $draftTarget;

        $blocked = $other ? ChatBlock::existsBetween($me->id, $other->id) : false;
        $iBlocked = $other
            ? ChatBlock::where('blocker_id', $me->id)->where('blocked_id', $other->id)->exists()
            : false;

        return view('livewire.member.chat', [
            'me' => $me,
            'conversations' => $conversations,
            'active' => $active,
            'draftTarget' => $draftTarget,
            'messages' => $messages,
            'other' => $other,
            'blocked' => $blocked,
            'iBlocked' => $iBlocked,
        ]);
    }
}
