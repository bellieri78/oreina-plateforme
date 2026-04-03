@extends('layouts.journal')

@section('title', 'Accueil')
@section('meta_description', 'Revue OREINA - Accès libre à des articles scientifiques de haute qualité sur les Lépidoptères de France.')

@push('styles')
<style>
    /* ── Hero ── */
    .hero { padding: 0; }

    .hero-card {
        position: relative;
        overflow: hidden;
        min-height: 60vh;
        border-radius: 0;
        background:
            linear-gradient(rgba(15,118,110,0.52), rgba(13,75,70,0.72)),
            url('/images/journal-hero.jpg') center/cover;
        box-shadow: var(--shadow);
        display: flex;
        align-items: flex-end;
        width: 100vw;
        margin-left: calc(50% - 50vw);
        margin-right: calc(50% - 50vw);
    }

    .hero-card::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(15,118,110,0.10), transparent 55%);
        pointer-events: none;
    }

    .hero-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.00) 0%, rgba(13,75,70,0.12) 52%, rgba(13,75,70,0.30) 100%);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: var(--container);
        margin: 0 auto;
        padding: 48px 16px 56px;
        color: white;
    }

    .hero .eyebrow {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.14);
        margin-bottom: 18px;
    }

    .hero h1 {
        margin: 0;
        max-width: 900px;
        font-size: clamp(38px, 5.5vw, 68px);
        font-weight: 700;
        line-height: 0.96;
        letter-spacing: -0.02em;
        color: white;
    }

    .hero p {
        margin: 16px 0 0;
        max-width: 680px;
        color: rgba(255,255,255,0.90);
        font-size: 18px;
        line-height: 1.72;
        text-wrap: balance;
    }

    .hero-actions {
        margin-top: 26px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .hero-bottom {
        margin-top: 34px;
        display: flex;
        justify-content: flex-end;
        align-items: end;
        gap: 20px;
        flex-wrap: wrap;
        padding-top: 18px;
        border-top: 1px solid rgba(255,255,255,0.12);
    }

    .hero-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        width: min(100%, 540px);
    }

    .hero-stat {
        padding: 16px;
        border-radius: 20px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.14);
        backdrop-filter: blur(10px);
        min-height: 88px;
    }

    .hero-stat strong {
        display: block;
        font-size: 26px;
        line-height: 1;
        letter-spacing: -0.04em;
        margin-bottom: 6px;
    }

    .hero-stat span {
        color: rgba(255,255,255,0.82);
        font-size: 13px;
        line-height: 1.45;
    }

    /* ── Sections ── */
    section { padding: 36px 0; }

    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: end;
        gap: 16px;
        margin-bottom: 18px;
    }

    .section-head h2 {
        margin: 0;
        font-size: clamp(28px, 4vw, 42px);
        line-height: 1;
        letter-spacing: -0.05em;
    }

    .section-head p {
        margin: 10px 0 0;
        color: var(--muted);
        font-size: 15px;
        max-width: 760px;
        line-height: 1.7;
    }

    .text-link {
        color: var(--accent, #0f766e);
        font-size: 14px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    /* ── Eyebrow ── */
    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 800;
    }

    /* ── Buttons ── */
    .btn {
        height: 46px;
        padding: 0 18px;
        border-radius: 14px;
        border: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        font-weight: 800;
        transition: 0.2s ease;
        white-space: nowrap;
        font-family: inherit;
        font-size: inherit;
        text-decoration: none;
    }

    .btn:hover { transform: translateY(-1px); }

    .btn-primary {
        background: var(--accent, #0f766e);
        color: white;
        box-shadow: 0 12px 24px rgba(15,118,110,0.18);
    }

    .btn-secondary {
        background: rgba(53,107,138,0.08);
        color: var(--blue);
        border: 1px solid rgba(53,107,138,0.14);
    }

    .btn-ghost-light {
        background: rgba(255,255,255,0.14);
        color: white;
        border: 1px solid rgba(255,255,255,0.16);
    }

    /* ── Icons ── */
    .icon {
        width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 18px;
    }

    .icon svg {
        width: 18px;
        height: 18px;
        stroke-width: 2;
    }

    .icon-white { color: white; }
    .icon-teal { color: var(--accent, #0f766e); }
    .icon-blue { color: var(--blue); }

    /* ── Articles grid ── */
    .articles-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .article-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
    }

    .article-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 48px rgba(15,118,110,0.10);
    }

    .article-card-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .article-card-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .article-card-meta .tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .tag-teal {
        background: rgba(20,184,166,0.10);
        color: var(--accent, #0f766e);
        border-color: rgba(20,184,166,0.14);
    }

    .article-date {
        color: var(--muted);
        font-size: 13px;
        font-weight: 700;
    }

    .article-card h3 {
        margin: 0 0 10px;
        font-size: 20px;
        line-height: 1.15;
        letter-spacing: -0.03em;
    }

    .article-card h3 a {
        color: inherit;
        text-decoration: none;
    }

    .article-card h3 a:hover {
        color: var(--accent, #0f766e);
    }

    .article-card-abstract {
        margin: 0 0 14px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.65;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .article-card-author {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--muted);
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .article-keywords {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 16px;
    }

    .keyword {
        padding: 4px 10px;
        border-radius: 999px;
        background: var(--surface-soft, rgba(0,0,0,0.03));
        border: 1px solid var(--border);
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
    }

    .article-card-footer {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .article-doi {
        font-size: 12px;
        font-family: monospace;
        color: var(--muted);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 60%;
    }

    /* ── Featured article (first card spans full width) ── */
    .article-card-featured {
        grid-column: 1 / -1;
        display: grid;
        grid-template-columns: 1fr 1.2fr;
    }

    .article-card-featured .article-card-media {
        min-height: 320px;
        background-size: cover;
        background-position: center;
        background-color: rgba(20,184,166,0.08);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .article-card-featured .article-card-body {
        padding: 30px;
    }

    .article-card-featured h3 {
        font-size: 28px;
    }

    .article-card-featured .article-card-abstract {
        -webkit-line-clamp: 4;
        font-size: 15px;
    }

    /* ── Empty state ── */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
    }

    .empty-state h3 {
        margin: 16px 0 6px;
        font-size: 20px;
        letter-spacing: -0.02em;
    }

    .empty-state p {
        margin: 0;
        color: var(--muted);
        font-size: 15px;
    }

    /* ── CTA panel ── */
    .cta-panel {
        position: relative;
        overflow: hidden;
        padding: 38px;
        background: var(--forest);
        color: white;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow);
    }

    .cta-panel::after {
        content: "";
        position: absolute;
        right: -34px;
        bottom: -34px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: rgba(20,184,166,0.14);
    }

    .cta-panel > * { position: relative; z-index: 1; }

    .cta-panel .eyebrow {
        background: rgba(255,255,255,0.10);
        border: 1px solid rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.86);
        margin-bottom: 14px;
    }

    .cta-panel h2 {
        margin: 12px 0 10px;
        font-size: clamp(26px, 3.5vw, 36px);
        line-height: 1.08;
        letter-spacing: -0.04em;
        color: white;
    }

    .cta-panel p {
        margin: 0;
        max-width: 760px;
        color: rgba(255,255,255,0.82);
        font-size: 15px;
        line-height: 1.7;
    }

    .content-actions {
        margin-top: 22px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    /* ── Responsive ── */
    @media (max-width: 1080px) {
        .articles-grid {
            grid-template-columns: 1fr;
        }

        .article-card-featured {
            grid-template-columns: 1fr;
        }

        .article-card-featured .article-card-media {
            min-height: 220px;
        }
    }

    @media (max-width: 760px) {
        .hero-content {
            padding: 28px 16px 36px;
        }

        .hero-card {
            min-height: 55vh;
        }

        .hero-bottom {
            flex-direction: column;
        }

        .hero-stats {
            grid-template-columns: 1fr;
            width: 100%;
        }

        .section-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .cta-panel {
            padding: 22px;
        }

        .article-card-body {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
    {{-- 1. Hero Section --}}
    <section class="hero">
        <article class="hero-card">
            <div class="hero-content">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="book-open-text"></i>Peer-reviewed &middot; Open access</div>
                <h1>Revue scientifique OREINA</h1>
                <p>Articles originaux sur la systématique, l'écologie, la biogéographie et la conservation des Lépidoptères de France. Relecture par les pairs et publication en flux continu.</p>

                <div class="hero-actions">
                    <a href="{{ route('journal.articles.index') }}" class="btn btn-primary"><i class="icon icon-white" data-lucide="library"></i>Explorer les articles</a>
                    <a href="{{ route('journal.submit') }}" class="btn btn-ghost-light"><i class="icon icon-white" data-lucide="upload"></i>Soumettre un manuscrit</a>
                </div>

                <div class="hero-bottom">
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <strong>{{ $recentArticles->count() }}+</strong>
                            <span>articles publiés en accès libre</span>
                        </div>
                        <div class="hero-stat">
                            <strong>DOI</strong>
                            <span>attribution Crossref pour chaque article</span>
                        </div>
                        <div class="hero-stat">
                            <strong>Pairs</strong>
                            <span>relecture par un comité scientifique</span>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </section>

    {{-- 2. Articles Section --}}
    <section id="articles" style="background:white; width:100vw; margin-left:calc(50% - 50vw); padding-left:calc(50vw - 50%); padding-right:calc(50vw - 50%);">
        <div class="container">
            <div class="section-head">
                <div>
                    <h2>Derniers articles</h2>
                    <p>Les publications les plus récentes de la revue, en accès libre.</p>
                </div>
                <a href="{{ route('journal.articles.index') }}" class="text-link"><i class="icon icon-teal" data-lucide="arrow-right"></i>Tous les articles</a>
            </div>

            @if($recentArticles->count() > 0)
                @php
                    $featuredArticle = $recentArticles->first();
                    $otherArticles = $recentArticles->skip(1);
                @endphp

                <div class="articles-grid">
                    {{-- Featured article --}}
                    <article class="article-card article-card-featured">
                        <div class="article-card-media" style="background-image: url('{{ $featuredArticle->featured_image ? Storage::url($featuredArticle->featured_image) : '' }}');">
                            @unless($featuredArticle->featured_image)
                                <i class="icon" data-lucide="file-text" style="width:48px;height:48px;color:rgba(15,118,110,0.25);"></i>
                            @endunless
                        </div>
                        <div class="article-card-body">
                            <div class="article-card-meta">
                                <span class="tag tag-teal"><i class="icon icon-teal" data-lucide="flask-conical"></i>Article scientifique</span>
                                <span class="article-date">{{ $featuredArticle->published_at?->translatedFormat('d F Y') ?? 'Non publié' }}</span>
                            </div>
                            <h3><a href="{{ route('journal.articles.show', $featuredArticle) }}">{{ $featuredArticle->title }}</a></h3>
                            <div class="article-card-author">
                                <i class="icon" data-lucide="user" style="width:14px;height:14px;flex:0 0 14px;"></i>
                                {{ $featuredArticle->author?->name ?? 'Auteur inconnu' }}
                            </div>
                            <p class="article-card-abstract">{{ $featuredArticle->abstract }}</p>
                            @if($featuredArticle->keywords && is_array($featuredArticle->keywords) && count($featuredArticle->keywords) > 0)
                                <div class="article-keywords">
                                    @foreach(array_slice($featuredArticle->keywords, 0, 4) as $keyword)
                                        <span class="keyword">{{ $keyword }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="article-card-footer">
                                @if($featuredArticle->doi)
                                    <span class="article-doi">DOI: {{ $featuredArticle->doi }}</span>
                                @else
                                    <span class="article-doi">{{ $featuredArticle->journalIssue?->full_reference ?? '' }}</span>
                                @endif
                                <a href="{{ route('journal.articles.show', $featuredArticle) }}" class="text-link"><i class="icon icon-teal" data-lucide="arrow-right"></i>Lire</a>
                            </div>
                        </div>
                    </article>

                    {{-- Other articles --}}
                    @foreach($otherArticles as $article)
                    <article class="article-card">
                        <div class="article-card-body">
                            <div class="article-card-meta">
                                <span class="tag tag-teal"><i class="icon icon-teal" data-lucide="flask-conical"></i>Article</span>
                                <span class="article-date">{{ $article->published_at?->translatedFormat('d F Y') ?? 'Non publié' }}</span>
                            </div>
                            <h3><a href="{{ route('journal.articles.show', $article) }}">{{ $article->title }}</a></h3>
                            <div class="article-card-author">
                                <i class="icon" data-lucide="user" style="width:14px;height:14px;flex:0 0 14px;"></i>
                                {{ $article->author?->name ?? 'Auteur inconnu' }}
                            </div>
                            <p class="article-card-abstract">{{ $article->abstract }}</p>
                            @if($article->keywords && is_array($article->keywords) && count($article->keywords) > 0)
                                <div class="article-keywords">
                                    @foreach(array_slice($article->keywords, 0, 3) as $keyword)
                                        <span class="keyword">{{ $keyword }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="article-card-footer">
                                @if($article->doi)
                                    <span class="article-doi">DOI: {{ $article->doi }}</span>
                                @else
                                    <span class="article-doi">{{ $article->journalIssue?->full_reference ?? '' }}</span>
                                @endif
                                <a href="{{ route('journal.articles.show', $article) }}" class="text-link"><i class="icon icon-teal" data-lucide="arrow-right"></i>Lire</a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="icon" data-lucide="file-text" style="width:48px;height:48px;color:var(--muted);margin:0 auto;display:block;"></i>
                    <h3>Aucun article publié</h3>
                    <p>Les premiers articles seront bientôt disponibles.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- 3. CTA Section --}}
    <section>
        <div class="container">
            <article class="cta-panel">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="pen-tool"></i>Appel à contributions</div>
                <h2>Publiez vos recherches dans la revue OREINA</h2>
                <p>La revue publie des articles originaux sur la systématique, l'écologie, la biogéographie et la conservation des Lépidoptères. Soumettez votre manuscrit pour une relecture par les pairs et une diffusion en accès libre avec attribution DOI.</p>
                <div class="content-actions">
                    <a href="{{ route('journal.submit') }}" class="btn btn-primary"><i class="icon icon-white" data-lucide="upload"></i>Soumettre un manuscrit</a>
                    <a href="{{ route('journal.authors') }}" class="btn btn-ghost-light"><i class="icon icon-white" data-lucide="book-open"></i>Instructions aux auteurs</a>
                </div>
            </article>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
