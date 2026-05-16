<?php

namespace App\Services;

use App\Mail\ChatMessageNotification;
use App\Models\ChatBlock;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Member;
use Illuminate\Support\Facades\Mail;

class ChatService
{
    public function canStartWith(Member $me, Member $target): true|string
    {
        if ($target->id === $me->id) {
            return 'Conversation impossible avec soi-même.';
        }
        if (! $target->isInDirectory()) {
            return "Cet adhérent n'est pas joignable via l'annuaire.";
        }
        return true;
    }

    /** @return array{conversation: ?Conversation, target: Member} */
    public function resolveTarget(Member $me, Member $target): array
    {
        $conversation = Conversation::where('member_low_id', min($me->id, $target->id))
            ->where('member_high_id', max($me->id, $target->id))
            ->first();
        return ['conversation' => $conversation, 'target' => $target];
    }

    public function canSend(Member $sender, Conversation $c): true|string
    {
        if (! $c->isParticipant($sender)) {
            return 'Action non autorisée.';
        }
        $other = $c->otherMember($sender);
        if (ChatBlock::existsBetween($sender->id, $other->id)) {
            $iBlocked = ChatBlock::where('blocker_id', $sender->id)
                ->where('blocked_id', $other->id)->exists();
            return $iBlocked
                ? 'Vous avez bloqué cet adhérent.'
                : 'Vous ne pouvez plus écrire à cet adhérent.';
        }
        return true;
    }

    public function send(Member $sender, ?Conversation $c, ?Member $draftTarget, string $content): ChatMessage
    {
        if (! $c) {
            if (! $draftTarget) {
                throw new \RuntimeException('Destinataire manquant.');
            }
            $check = $this->canStartWith($sender, $draftTarget);
            if ($check !== true) {
                throw new \RuntimeException($check);
            }
            $c = Conversation::between($sender->id, $draftTarget->id);
        }
        $send = $this->canSend($sender, $c);
        if ($send !== true) {
            throw new \RuntimeException($send);
        }
        $recipient = $c->otherMember($sender);
        $wasUnread = $c->unreadFor($recipient);
        $message = $c->messages()->create([
            'sender_id' => $sender->id,
            'content' => trim($content),
        ]);
        $c->forceFill(['last_message_at' => now()])->save();
        $c->markReadFor($sender);
        if (! $wasUnread && $recipient->email) {
            Mail::to($recipient->email)->queue(new ChatMessageNotification($c, $sender));
        }
        return $message;
    }

    public function block(Member $me, Member $target): void
    {
        ChatBlock::firstOrCreate(['blocker_id' => $me->id, 'blocked_id' => $target->id]);
    }

    public function unblock(Member $me, Member $target): void
    {
        ChatBlock::where('blocker_id', $me->id)->where('blocked_id', $target->id)->delete();
    }

    public function unreadConversationCount(Member $m): int
    {
        return Conversation::whereNotNull('last_message_at')
            ->where(function ($q) use ($m) {
                $q->where(function ($w) use ($m) {
                    $w->where('member_low_id', $m->id)
                      ->where(fn ($x) => $x->whereNull('member_low_read_at')
                          ->orWhereColumn('member_low_read_at', '<', 'last_message_at'));
                })->orWhere(function ($w) use ($m) {
                    $w->where('member_high_id', $m->id)
                      ->where(fn ($x) => $x->whereNull('member_high_read_at')
                          ->orWhereColumn('member_high_read_at', '<', 'last_message_at'));
                });
            })
            ->count();
    }
}
