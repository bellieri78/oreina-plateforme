<article class="card panel">
    <div class="panel-head">
        <div>
            <h2>Réseau des adhérents</h2>
        </div>
        <a href="{{ route('member.directory.index') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir l'annuaire</a>
    </div>

    <div class="reseau-map-wrap">
        <svg class="reseau-map-svg" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100,10 L120,25 L140,20 L155,35 L165,55 L170,75 L160,85 L170,100 L165,115 L155,125 L145,140 L130,155 L120,170 L100,180 L85,175 L75,185 L60,175 L50,160 L40,145 L35,130 L30,115 L35,100 L30,85 L35,70 L45,55 L55,40 L70,30 L85,20 Z"
                  fill="rgba(133,183,157,0.12)" stroke="rgba(133,183,157,0.4)" stroke-width="1"/>

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

    @if($randomMemberAvatars->count() > 0)
    <div class="reseau-avatars">
        @foreach($randomMemberAvatars as $m)
        <div class="reseau-avatar" title="{{ $m->full_name ?? '' }}">
            @if($m->photo_path)
                <img src="{{ \Storage::url($m->photo_path) }}" alt="">
            @else
                {{ strtoupper(substr($m->first_name ?? '?', 0, 1)) }}
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <p style="text-align:center;margin:12px 0 0;color:var(--muted);font-size:13px;">
        <strong style="color:var(--text);">{{ $totalActiveMembers }}</strong> adhérents répartis sur le territoire
    </p>

    <form action="{{ route('member.directory.index') }}" method="GET" class="reseau-search">
        <i data-lucide="search" style="width:16px;height:16px;color:var(--muted);"></i>
        <input type="text" name="q" placeholder="Ville, département, nom…">
        <button type="submit" class="topbar-icon" style="width:32px;height:32px;">
            <i data-lucide="arrow-right"></i>
        </button>
    </form>
</article>
