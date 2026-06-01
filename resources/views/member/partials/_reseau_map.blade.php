<article class="card panel">
    <div class="panel-head">
        <div>
            <h2>Réseau des adhérents</h2>
        </div>
        <a href="{{ route('member.directory.index') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir la carte complète</a>
    </div>

    <div class="reseau-map-wrap">
        <svg class="reseau-map-svg" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            {{-- Tracé réel de la France (docs/france.svg) ramené dans le viewBox 0-200 et centré verticalement --}}
            <g transform="translate(0,13.5) scale(0.15625)">
                @include('member.partials._france_silhouette')
            </g>

            @foreach($membersByRegion as $code => $region)
                @if($region['count'] > 0)
                <g class="reseau-map-cluster">
                    <circle cx="{{ $region['x'] }}" cy="{{ $region['y'] }}" r="14"/>
                    <text x="{{ $region['x'] }}" y="{{ $region['y'] }}">{{ $region['count'] }}</text>
                </g>
                @endif
            @endforeach
        </svg>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:14px;">
        <div style="text-align:center;padding:12px;background:var(--surface-soft);border-radius:14px;">
            <strong style="display:block;font-size:26px;line-height:1;letter-spacing:-0.04em;">{{ number_format($directoryMembersCount, 0, ',', ' ') }}</strong>
            <span style="display:block;margin-top:4px;font-size:12px;color:var(--muted);">inscrits à l'annuaire</span>
        </div>
        <div style="text-align:center;padding:12px;background:var(--surface-soft);border-radius:14px;">
            <strong style="display:block;font-size:26px;line-height:1;letter-spacing:-0.04em;">{{ $departmentsRepresented }}</strong>
            <span style="display:block;margin-top:4px;font-size:12px;color:var(--muted);">départements représentés</span>
        </div>
    </div>

    @if($randomMemberAvatars->count() > 0)
    <div class="reseau-avatars" style="justify-content:flex-start;margin-top:14px;">
        @foreach($randomMemberAvatars as $m)
        <div class="reseau-avatar" title="{{ $m->full_name ?? '' }}">
            @if($m->photo_path)
                <img src="{{ \Storage::url($m->photo_path) }}" alt="">
            @else
                {{ strtoupper(substr($m->first_name ?? '?', 0, 1)) }}
            @endif
        </div>
        @endforeach
        @if($directoryMembersCount > $randomMemberAvatars->count())
        <div class="reseau-avatar" style="background:var(--surface-soft);color:var(--muted);font-size:11px;">
            +{{ $directoryMembersCount - $randomMemberAvatars->count() }}
        </div>
        @endif
    </div>
    @endif
</article>
