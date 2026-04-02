<?php

namespace App\Livewire\Member;

use App\Models\ChatMessage;
use App\Models\Member;
use Livewire\Component;

class Chat extends Component
{
    public int $memberId;
    public string $message = '';
    public bool $expanded = false;

    public function mount(int $memberId, bool $expanded = false): void
    {
        $this->memberId = $memberId;
        $this->expanded = $expanded;
    }

    public function sendMessage(): void
    {
        $this->validate([
            'message' => 'required|max:500',
        ]);

        ChatMessage::create([
            'member_id' => $this->memberId,
            'content' => trim($this->message),
        ]);

        $this->message = '';
    }

    public function getMessages()
    {
        $limit = $this->expanded ? 50 : 10;

        return ChatMessage::with('member:id,first_name,last_name')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    public function render()
    {
        return view('livewire.member.chat', [
            'messages' => $this->getMessages(),
        ]);
    }
}
