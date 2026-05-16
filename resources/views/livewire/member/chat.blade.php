<div wire:poll.5s style="display:grid;grid-template-columns:300px 1fr;gap:0;min-height:520px;border:1px solid var(--border);border-radius:14px;overflow:hidden;">
    {{-- Liste des conversations --}}
    <div style="border-right:1px solid var(--border);overflow-y:auto;max-height:620px;">
        <div style="padding:14px 16px;font-weight:800;border-bottom:1px solid var(--border);">Conversations</div>
        @forelse($conversations as $conv)
            @php($cOther = $conv->otherMember($me))
            @php($cUnread = $conv->unreadFor($me))
            <button type="button" wire:click="selectConversation({{ $conv->id }})"
                style="display:flex;gap:10px;width:100%;text-align:left;background:{{ $active && $active->id===$conv->id ? 'var(--surface-soft,#f3f4f6)' : 'transparent' }};border:none;border-bottom:1px solid var(--border);padding:12px 16px;cursor:pointer;">
                <span style="width:34px;height:34px;border-radius:50%;background:var(--sage);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:12px;flex-shrink:0;">
                    {{ strtoupper(mb_substr($cOther->first_name ?? '',0,1).mb_substr($cOther->last_name ?? '',0,1)) }}
                </span>
                <span style="min-width:0;flex:1;">
                    <span style="display:flex;justify-content:space-between;gap:8px;">
                        <strong style="font-size:14px;">{{ $cOther->first_name }} {{ $cOther->last_name }}</strong>
                        @if($cUnread)<span style="background:var(--gold);color:var(--forest);font-size:10px;font-weight:800;padding:2px 7px;border-radius:999px;">nouveau</span>@endif
                    </span>
                    <span style="display:block;color:var(--muted);font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ \Illuminate\Support\Str::limit(optional($conv->messages()->whereNull('deleted_at')->latest()->first())->content ?? 'Message supprimé', 38) }}
                    </span>
                </span>
            </button>
        @empty
            <p style="padding:18px 16px;color:var(--muted);font-size:13px;">Aucune conversation. Trouvez un adhérent dans l'annuaire et cliquez « Envoyer un message ».</p>
        @endforelse
    </div>

    {{-- Fil actif / brouillon --}}
    <div style="display:flex;flex-direction:column;max-height:620px;">
        @if($active || $draftTarget)
            <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <strong>{{ $other?->first_name }} {{ $other?->last_name }}</strong>
                @if($active)
                    @if($iBlocked)
                        <button type="button" wire:click="unblockOther" class="btn btn-secondary" style="font-size:12px;">Débloquer</button>
                    @elseif(!$blocked)
                        <button type="button" wire:click="blockOther" wire:confirm="Bloquer cet adhérent ? Il ne pourra plus vous écrire."
                            class="btn btn-secondary" style="font-size:12px;">Bloquer</button>
                    @endif
                @endif
            </div>

            <div style="flex:1;overflow-y:auto;padding:18px;display:flex;flex-direction:column;gap:10px;">
                @forelse($messages as $msg)
                    @php($mine = $msg->sender_id === $me->id)
                    <div style="align-self:{{ $mine ? 'flex-end' : 'flex-start' }};max-width:74%;">
                        <div style="background:{{ $mine ? 'var(--blue)' : 'var(--surface-soft,#f1f5f9)' }};color:{{ $mine ? '#fff' : 'inherit' }};padding:9px 13px;border-radius:13px;font-size:14px;word-break:break-word;">
                            {!! $msg->renderedBody() !!}
                        </div>
                        <div style="font-size:10px;color:var(--muted);margin-top:3px;text-align:{{ $mine ? 'right' : 'left' }};">
                            {{ $msg->created_at->diffForHumans() }}
                            @if($mine && !$msg->isDeleted())
                                · <button type="button" wire:click="deleteMessage({{ $msg->id }})" wire:confirm="Supprimer ce message ?" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:10px;text-decoration:underline;padding:0;">supprimer</button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p style="color:var(--muted);font-size:13px;text-align:center;margin:auto;">Démarrez la conversation.</p>
                @endforelse
            </div>

            @if($blocked)
                <div style="padding:14px 18px;border-top:1px solid var(--border);color:var(--muted);font-size:13px;">
                    {{ $iBlocked ? 'Vous avez bloqué cet adhérent.' : 'Vous ne pouvez plus écrire à cet adhérent.' }}
                </div>
            @else
                <form wire:submit="sendMessage" style="border-top:1px solid var(--border);padding:12px 14px;display:flex;gap:10px;">
                    <input type="text" wire:model="body" maxlength="2000" placeholder="Votre message..."
                        style="flex:1;border:1px solid var(--border);border-radius:10px;padding:9px 12px;font-size:14px;">
                    <button type="submit" class="btn btn-primary" style="flex-shrink:0;">
                        <i data-lucide="send" style="width:16px;height:16px;"></i>
                    </button>
                </form>
                @error('body')<p style="color:#dc2626;font-size:12px;padding:6px 16px 0;">{{ $message }}</p>@enderror
            @endif
        @else
            <div style="margin:auto;text-align:center;color:var(--muted);padding:40px;">
                <i data-lucide="messages-square" style="width:34px;height:34px;"></i>
                <p style="margin-top:10px;font-size:14px;">Sélectionnez une conversation, ou contactez un adhérent depuis l'annuaire.</p>
            </div>
        @endif
    </div>
</div>
