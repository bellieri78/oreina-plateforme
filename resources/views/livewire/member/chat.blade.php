<div wire:poll.5s>
    {{-- Messages list --}}
    <div class="{{ $expanded ? 'h-[500px]' : 'h-48' }} overflow-y-auto space-y-2 mb-3 px-1" id="chat-messages">
        @forelse($messages as $msg)
            <div class="flex items-start gap-2">
                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0" style="background:var(--surface-sage)">
                    <span class="font-bold" style="font-size:9px;color:var(--sage)">
                        {{ strtoupper(substr($msg->member->first_name ?? '', 0, 1) . substr($msg->member->last_name ?? '', 0, 1)) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-2">
                        <span class="text-xs font-semibold" style="color:var(--forest)">{{ $msg->member->first_name }}</span>
                        <span style="font-size:10px;color:var(--muted)">{{ $msg->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs break-words" style="color:var(--muted)">{{ $msg->content }}</p>
                </div>
            </div>
        @empty
            <div class="flex items-center justify-center h-full">
                <p class="text-xs" style="color:var(--muted)">Aucun message. Lancez la discussion !</p>
            </div>
        @endforelse
    </div>

    {{-- Input --}}
    <form wire:submit="sendMessage" class="flex gap-2">
        <input
            type="text"
            wire:model="message"
            placeholder="Votre message..."
            maxlength="500"
            class="flex-1 text-xs border rounded-lg px-3 py-2" style="border-color:var(--border)"
        >
        <button type="submit" class="btn btn-primary rounded-lg px-3 py-2 flex-shrink-0">
            <i data-lucide="send" style="width:16px;height:16px"></i>
        </button>
    </form>
    @error('message') <p style="font-size:10px;color:var(--coral)" class="mt-1">{{ $message }}</p> @enderror
</div>
