@php
    $actPalette = [
        'join'     => ['#fdeede','#b45309','user-plus'],
        'thread'   => ['#e7f3ec','#2f694e','message-square'],
        'resource' => ['#f3ecfb','#7c3aed','folder'],
    ];
@endphp
<div class="card panel">
    <div class="panel-head"><div><h2>Activité du groupe</h2></div></div>

    @forelse($activity as $event)
    @php($pal = $actPalette[$event['type']] ?? ['#e4eef5','#356B8A','activity'])
    <div class="gt-feed-item">
        <span class="gt-feed-ic" style="background:{{ $pal[0] }};color:{{ $pal[1] }};"><i data-lucide="{{ $pal[2] }}"></i></span>
        <div class="gt-feed-body">
            @if($event['href'])
                <a href="{{ $event['href'] }}" class="text-link" style="display:block;font-weight:700;color:var(--text);">{{ $event['label'] }}</a>
            @else
                <span style="display:block;font-weight:700;">{{ $event['label'] }}</span>
            @endif
        </div>
        <span class="gt-feed-time">{{ $event['date']->diffForHumans() }}</span>
    </div>
    @empty
    <p style="color:var(--muted);padding:8px 0;">Aucune activité récente.</p>
    @endforelse
</div>
