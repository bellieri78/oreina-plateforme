<article class="card panel">
    <div class="panel-head">
        <div>
            <h2>Actualités du réseau</h2>
        </div>
        <a href="{{ route('member.articles.index') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir toutes les actualités</a>
    </div>

    @if($memberArticles->isEmpty())
        <p style="color:var(--muted);padding:16px 0;">Aucune actualité pour le moment.</p>
    @else
        <div class="news-feed">
            @foreach($memberArticles as $a)
            <a href="{{ route('hub.articles.show', $a) }}" class="news-feed-item">
                <img src="{{ $a->featured_image ? \Storage::url($a->featured_image) : asset('images/magazine/oreina-n68.jpg') }}"
                     alt="" class="news-feed-thumb" onerror="this.style.visibility='hidden'">
                <div>
                    @if($a->visibility !== \App\Models\Article::VIS_PUBLIC)
                        <span class="news-feed-type gold">
                            @if($a->visibility === \App\Models\Article::VIS_MEMBERS)Adhérents@else{{ implode(' · ', array_map(fn ($r) => \App\Models\Member::ADHERENT_ROLES[$r] ?? $r, $a->audience_roles ?? [])) }}@endif
                        </span>
                    @elseif($a->category)
                        <span class="news-feed-type sage">{{ $a->category }}</span>
                    @endif
                    <strong>{{ $a->title }}</strong>
                    <p>{{ $a->published_at?->translatedFormat('d M Y') }}</p>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</article>
