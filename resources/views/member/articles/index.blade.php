@extends('layouts.member')

@section('title', 'Actualités')

@section('content')
<section>
    <div class="panel-head" style="margin-bottom:16px;">
        <div>
            <h2 style="margin:0;">Actualités du réseau</h2>
            <p style="color:var(--muted);margin:4px 0 0;">Les actualités qui vous concernent : publiques et réservées à votre profil.</p>
        </div>
    </div>

    @if($categories->isNotEmpty())
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
        <a href="{{ route('member.articles.index') }}" class="space-row-chip {{ request('category') ? '' : 'gold' }}">Toutes</a>
        @foreach($categories as $cat)
            <a href="{{ route('member.articles.index', ['category' => $cat]) }}" class="space-row-chip {{ request('category') === $cat ? 'gold' : '' }}">{{ $cat }}</a>
        @endforeach
    </div>
    @endif

    @if($articles->isEmpty())
        <div class="card panel"><p style="color:var(--muted);">Aucune actualité pour le moment.</p></div>
    @else
        <div class="news-feed">
            @foreach($articles as $a)
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
        <div style="margin-top:18px;">{{ $articles->links() }}</div>
    @endif
</section>
@endsection
