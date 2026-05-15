<div class="card panel">
    <div class="panel-head"><div><h2>À propos</h2></div></div>
    <div style="white-space:pre-line;line-height:1.7;color:var(--text);">{{ $workGroup->description ?: 'Aucune description pour le moment.' }}</div>

    <h3 style="margin-top:20px;font-size:16px;">Coordinateurs</h3>
    @if($coordinators->count() === 0)
        <p style="color:var(--muted);font-size:14px;">Aucun coordinateur désigné.</p>
    @else
        <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:10px;">
            @foreach($coordinators as $c)
            <div style="display:flex;align-items:center;gap:8px;">
                <div class="reseau-avatar" style="margin:0;">
                    @if($c->photo_path)<img src="{{ \Storage::url($c->photo_path) }}" alt="">@else{{ strtoupper(substr($c->first_name ?? '?',0,1)) }}@endif
                </div>
                <span style="font-size:14px;">{{ $c->full_name ?? $c->first_name }}</span>
            </div>
            @endforeach
        </div>
    @endif
</div>
