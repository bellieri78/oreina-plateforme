<div wire:poll.5s>
    {{-- Messages list --}}
    <div class="{{ $expanded ? 'h-[500px]' : 'h-48' }} overflow-y-auto space-y-2 mb-3 px-1" id="chat-messages">
        @forelse($messages as $msg)
            <div class="flex items-start gap-2">
                <div class="w-6 h-6 rounded-full bg-oreina-green/20 flex items-center justify-center flex-shrink-0">
                    <span class="text-[9px] font-bold text-oreina-green">
                        {{ strtoupper(substr($msg->member->first_name ?? '', 0, 1) . substr($msg->member->last_name ?? '', 0, 1)) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-2">
                        <span class="text-xs font-semibold text-oreina-dark">{{ $msg->member->first_name }}</span>
                        <span class="text-[10px] text-gray-300">{{ $msg->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-gray-600 break-words">{{ $msg->content }}</p>
                </div>
            </div>
        @empty
            <div class="flex items-center justify-center h-full">
                <p class="text-xs text-gray-400">Aucun message. Lancez la discussion !</p>
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
            class="flex-1 text-xs border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-oreina-green focus:ring-1 focus:ring-oreina-green/30"
        >
        <button type="submit" class="bg-oreina-green text-white rounded-lg px-3 py-2 hover:bg-oreina-green/80 transition flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
        </button>
    </form>
    @error('message') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
</div>
