<div class="card panel">
    <div class="panel-head"><div><h2>Activité récente</h2></div></div>

    @forelse($activity as $event)
    <div style="display:flex;align-items:flex-start;gap:12px;padding:8px 0;border-bottom:1px solid var(--border);">
        <div class="reseau-avatar" style="margin:0;width:32px;height:32px;flex-shrink:0;">
            <i data-lucide="{{ $event['type'] === 'join' ? 'user-plus' : ($event['type'] === 'thread' ? 'message-square' : 'folder') }}" style="width:15px;height:15px;"></i>
        </div>
        <div style="flex:1;min-width:0;">
            @if($event['href'])
                <a href="{{ $event['href'] }}" class="text-link" style="display:block;">{{ $event['label'] }}</a>
            @else
                <span style="display:block;">{{ $event['label'] }}</span>
            @endif
            <small style="color:var(--muted);">{{ $event['date']->diffForHumans() }}</small>
        </div>
    </div>
    @empty
    <p style="color:var(--muted);padding:8px 0;">Aucune activité récente.</p>
    @endforelse
</div>
