@extends('layouts.hub')

@section('title', 'Accueil')
@section('meta_description', 'OREINA - Association des Lépidoptères de France. Rejoignez une communauté passionnée au service de la connaissance des papillons.')

@push('styles')
<style>
    /* ── Hero ── */
    .hero { padding: 0; }

    .hero-card {
        position: relative;
        overflow: hidden;
        min-height: 92vh;
        border-radius: 0;
        background:
            linear-gradient(rgba(22,48,43,0.60), rgba(22,48,43,0.72)),
            url('/images/hero-bg.jpg') center/cover;
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
        background: linear-gradient(90deg, rgba(53,107,138,0.12), transparent 55%);
        pointer-events: none;
    }

    .hero-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.00) 0%, rgba(22,48,43,0.10) 52%, rgba(22,48,43,0.26) 100%);
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
        font-size: clamp(42px, 6vw, 76px);
        font-weight: 700;
        line-height: 0.94;
        letter-spacing: -0.02em;
        color: white;
    }

    .hero p {
        margin: 18px 0 0;
        max-width: 700px;
        color: rgba(255,255,255,0.90);
        font-size: 19px;
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
        justify-content: space-between;
        align-items: end;
        gap: 20px;
        flex-wrap: wrap;
        padding-top: 18px;
        border-top: 1px solid rgba(255,255,255,0.12);
    }

    .hero-credit {
        color: rgba(255,255,255,0.78);
        font-size: 13px;
        max-width: 460px;
        line-height: 1.55;
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
        min-height: 92px;
    }

    .hero-stat strong {
        display: block;
        font-size: 28px;
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
        font-size: clamp(30px, 4vw, 44px);
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
        color: var(--blue);
        font-size: 14px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    /* ── Story Layout (2-column) ── */
    .story-layout {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 28px;
        align-items: start;
    }

    .main-story {
        display: flex;
        flex-direction: column;
        gap: 48px;
    }

    /* ── Aside News (right column) ── */
    .aside-news {
        position: sticky;
        top: 100px;
    }

    .aside-news h3 {
        margin: 0 0 20px;
        font-size: 24px;
        letter-spacing: -0.04em;
    }

    .aside-news-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .aside-news-item {
        padding: 14px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 14px;
        align-items: start;
    }

    .aside-news-thumb {
        width: 120px;
        border-radius: 10px;
        background-size: cover;
        background-position: center;
        grid-row: 1 / -1;
        align-self: stretch;
        flex-shrink: 0;
    }

    .aside-news-item:not(:has(.aside-news-thumb)) {
        grid-template-columns: 1fr;
    }

    .aside-news-item .tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .aside-news-item .news-date {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
    }

    .aside-news-item h4 {
        margin: 4px 0 4px;
        font-size: 15px;
        line-height: 1.2;
        letter-spacing: -0.03em;
    }

    .aside-news-item h4 a:hover {
        color: var(--blue);
    }

    .aside-news-item p {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .aside-news-footer {
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid var(--border);
    }

    /* ── Content blocks (narrative) ── */
    .content-panel {
        background: transparent;
        border: 0;
        box-shadow: none;
    }

    .content-panel .eyebrow {
        background: rgba(53,107,138,0.08);
        color: var(--blue);
        margin-bottom: 16px;
    }

    .content-panel .eyebrow.sage {
        background: rgba(133,183,157,0.16);
        color: #2f694e;
    }

    .content-panel .eyebrow.gold {
        background: rgba(237,196,66,0.20);
        color: #8b6c05;
    }

    .content-panel .eyebrow.blue {
        background: rgba(53,107,138,0.10);
        color: var(--blue);
    }

    .content-body { max-width: 680px; }
    .content-body p { color: var(--muted); font-size: 15px; line-height: 1.7; margin: 0; }
    .content-body p + p { margin-top: 12px; }

    .content-body h3 {
        margin: 0 0 14px;
        font-size: 28px;
        line-height: 1.1;
        letter-spacing: -0.04em;
    }

    .content-actions {
        margin-top: 22px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .split-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 28px;
        align-items: center;
    }

    .visual-block {
        min-height: 400px;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: var(--shadow);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .transition-text {
        margin-top: 14px;
        font-size: 15px;
        color: var(--muted);
        font-style: italic;
        line-height: 1.6;
    }

    /* ── Tags ── */
    .tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .tag-gold {
        background: rgba(237,196,66,0.18);
        color: #8b6c05;
        border-color: rgba(237,196,66,0.20);
    }

    .tag-blue {
        background: rgba(53,107,138,0.10);
        color: var(--blue);
        border-color: rgba(53,107,138,0.12);
    }

    .tag-sage {
        background: rgba(133,183,157,0.18);
        color: #2f694e;
        border-color: rgba(133,183,157,0.20);
    }

    .news-date {
        margin-top: 14px;
        color: var(--muted);
        font-size: 13px;
        font-weight: 700;
    }

    /* ── Project cards ── */
    .project-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .project-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .project-card {
        padding: 30px;
        border-radius: var(--radius-xl);
        border: 1px solid transparent;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .project-card::before {
        content: "";
        position: absolute;
        inset: 0;
        opacity: 0.06;
        pointer-events: none;
        background: radial-gradient(circle at 100% 100%, currentColor, transparent 60%);
    }

    .project-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 48px rgba(22,48,43,0.14);
    }

    .project-card h3 {
        margin: 0;
        font-size: 20px;
        letter-spacing: -0.03em;
    }

    .project-card p {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.65;
    }

    .project-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: grid;
        place-items: center;
    }

    .project-card-icon .icon {
        width: 22px;
        height: 22px;
        flex: 0 0 22px;
    }

    .project-card-icon .icon svg {
        width: 22px;
        height: 22px;
    }

    .project-card-pill {
        display: inline-flex;
        align-self: flex-start;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    /* Card color themes */
    .project-card-gold {
        background: linear-gradient(145deg, #FBF6DF 0%, #F5EBC4 100%);
        border-color: rgba(237,196,66,0.22);
    }
    .project-card-gold .project-card-pill { background: rgba(237,196,66,0.22); color: #8b6c05; }
    .project-card-gold .project-card-icon { background: rgba(237,196,66,0.22); }

    .project-card-coral {
        background: linear-gradient(145deg, #FEF0EB 0%, #FBDDD3 100%);
        border-color: rgba(239,122,92,0.18);
    }
    .project-card-coral .project-card-pill { background: rgba(239,122,92,0.16); color: #b5452a; }
    .project-card-coral .project-card-icon { background: rgba(239,122,92,0.16); }

    .project-card-sage {
        background: linear-gradient(145deg, #EEF6F1 0%, #D6ECDF 100%);
        border-color: rgba(133,183,157,0.24);
    }
    .project-card-sage .project-card-pill { background: rgba(133,183,157,0.22); color: #2f694e; }
    .project-card-sage .project-card-icon { background: rgba(133,183,157,0.22); }

    .project-card-blue {
        background: linear-gradient(145deg, #EEF4F8 0%, #D5E5EF 100%);
        border-color: rgba(53,107,138,0.16);
    }
    .project-card-blue .project-card-pill { background: rgba(53,107,138,0.14); color: var(--blue); }
    .project-card-blue .project-card-icon { background: rgba(53,107,138,0.14); }

    /* ── Publications grid ── */
    .pub-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-top: 18px;
    }

    .pub-card {
        padding: 26px;
        border-radius: var(--radius-xl);
        background: var(--surface);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        display: flex;
        flex-direction: column;
        gap: 10px;
        transition: all 0.3s ease;
    }

    .pub-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 40px rgba(22,48,43,0.10);
    }

    .pub-card h4 {
        margin: 0;
        font-size: 20px;
        letter-spacing: -0.03em;
    }

    .pub-card p {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.65;
    }

    .pub-card .text-link {
        margin-top: auto;
    }

    .pub-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: grid;
        place-items: center;
    }

    /* ── Tool list (sober) ── */
    .tool-list {
        display: flex;
        flex-direction: column;
        gap: 0;
        margin-top: 18px;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        background: var(--surface);
    }

    .tool-list-row {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px 22px;
        border-bottom: 1px solid var(--border);
        transition: background 0.15s ease;
    }

    .tool-list-row:last-child {
        border-bottom: 0;
    }

    .tool-list-row:hover {
        background: var(--surface-soft);
    }

    .tool-list-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--surface-blue);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .tool-list-body {
        flex: 1;
        min-width: 0;
    }

    .tool-list-body strong {
        display: block;
        font-size: 15px;
        letter-spacing: -0.02em;
        margin-bottom: 2px;
    }

    .tool-list-body span {
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .tool-list-stat {
        flex-shrink: 0;
        text-align: right;
        font-size: 14px;
        font-weight: 800;
        color: var(--blue);
        white-space: nowrap;
    }

    /* ── CTA ── */
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
        background: rgba(237,196,66,0.14);
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
        line-height: 1.08;
        letter-spacing: -0.04em;
        font-size: clamp(26px, 3.5vw, 38px);
        color: white;
    }

    .cta-panel p {
        max-width: 760px;
        color: rgba(255,255,255,0.82);
        margin: 0;
        font-size: 15px;
        line-height: 1.7;
    }

    /* ── Eyebrow base ── */
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
        background: var(--gold);
        color: var(--forest);
        box-shadow: 0 12px 24px rgba(237,196,66,0.18);
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
    .icon-blue { color: var(--blue); }
    .icon-sage { color: var(--forest); }
    .icon-gold { color: #8b6c05; }
    .icon-coral { color: var(--coral); }

    /* ── Responsive ── */
    @media (max-width: 1080px) {
        .story-layout {
            grid-template-columns: 1fr;
        }

        .aside-news {
            order: 99;
            position: static;
        }

        .split-section {
            grid-template-columns: 1fr;
        }

        .hero-card {
            min-height: 88vh;
        }

        .project-grid,
        .project-grid-4 {
            grid-template-columns: repeat(2, 1fr);
        }

        .pub-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .project-grid-4 {
            grid-template-columns: 1fr;
        }

        .hero-content {
            padding: 28px 16px 36px;
        }

        .hero-card {
            min-height: 82vh;
            border-radius: 0 0 28px 28px;
        }

        .hero-bottom,
        .hero-stats {
            grid-template-columns: 1fr;
            width: 100%;
        }

        .hero-stats {
            display: grid;
        }

        .content-panel,
        .cta-panel {
            padding: 22px;
        }

        .pub-grid {
            grid-template-columns: 1fr;
        }

        .tool-list-row {
            padding: 14px 16px;
        }

        .tool-list-stat {
            display: none;
        }
    }
</style>
@endpush

@section('content')
    {{-- 1. Hero Section --}}
    <section class="hero">
        <article class="hero-card">
            <div class="hero-content">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="leaf"></i>Association loi 1901 depuis 2007</div>
                <h1>Lépidoptères de France</h1>
                <p>OREINA fédère naturalistes et scientifiques pour observer, comprendre et protéger les papillons de France.</p>

                <div class="hero-actions">
                    <a href="{{ route('hub.about') }}" class="btn btn-primary"><i class="icon icon-sage" data-lucide="sparkles"></i>Découvrir OREINA</a>
                    <a href="#" class="btn btn-ghost-light"><i class="icon icon-white" data-lucide="database"></i>Explorer Artemisiae</a>
                </div>

                <div class="hero-bottom">
                    <div class="hero-credit">
                        <strong style="display:block;color:white;font-size:14px;margin-bottom:4px;letter-spacing:-0.01em;">Argolamprotes micella</strong>
                        <span>Photographie : OREINA</span>
                    </div>

                    <div class="hero-stats">
                        <div class="hero-stat">
                            <strong>300+</strong>
                            <span>adhérents, bénévoles et contributeurs</span>
                        </div>
                        <div class="hero-stat">
                            <strong>5 362</strong>
                            <span>espèces documentées dans les outils</span>
                        </div>
                        <div class="hero-stat">
                            <strong>2,1 M+</strong>
                            <span>données naturalistes valorisées</span>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </section>

    {{-- 2. Association + Actus (2 colonnes) --}}
    <section style="background:white; width:100vw; margin-left:calc(50% - 50vw); padding-left:calc(50vw - 50%); padding-right:calc(50vw - 50%);">
        <div class="container">
            <div class="story-layout">

                {{-- LEFT: Association --}}
                <div>

                    {{-- Block A: L'association --}}
                    <div class="content-panel">
                        <div class="eyebrow sage"><i class="icon icon-sage" data-lucide="trees"></i>L'association</div>
                        <div class="content-body">
                            <h3>Une association à la croisée du naturalisme et de la science</h3>
                            <p>Sur le terrain, OREINA rassemble des naturalistes passionnés qui observent, photographient et inventorient les papillons à travers toute la France. Ce réseau de bénévoles, qu'ils soient débutants ou confirmés, participe à des sorties collectives, alimente des bases de données et transmet un savoir vivant, ancré dans l'expérience directe de la nature.</p>
                            <p>En parallèle, l'association structure cette connaissance pour la rendre utile à la recherche. Publications scientifiques avec comité de relecture, référentiels taxonomiques, qualification des observations, barcoding génétique : OREINA produit des données fiables et contribue activement aux programmes nationaux. C'est cette double vocation — naturaliste et scientifique — qui fait sa singularité.</p>
                            <div class="content-actions">
                                <a href="{{ route('hub.about') }}" class="btn btn-secondary"><i class="icon icon-blue" data-lucide="info"></i>Qui sommes-nous ?</a>
                            </div>
                        </div>
                    </div>
                    <div class="visual-block" style="background-image: url('/images/about-mission.jpg'); margin-top: 24px; min-height: 280px;"></div>

                </div>

                {{-- RIGHT: Actus sidebar (seulement ici) --}}
                <div class="aside-news">
                    <h3>Actualités</h3>

                    @php
                        $allArticles = collect()
                            ->merge($featuredArticles ?? collect())
                            ->merge($latestArticles ?? collect())
                            ->unique('id')
                            ->sortByDesc('published_at')
                            ->values();
                    @endphp

                    <div class="aside-news-list">
                        @forelse($allArticles->take(3) as $article)
                            <article class="aside-news-item">
                                @if($article->featured_image)
                                    <div class="aside-news-thumb" style="background-image: url('{{ Storage::url($article->featured_image) }}');"></div>
                                @endif
                                <div>
                                    <div class="news-date">{{ $article->published_at->translatedFormat('d F Y') }}</div>
                                    <h4><a href="{{ route('hub.articles.show', $article) }}">{{ $article->title }}</a></h4>
                                    <p>{{ Str::limit($article->summary, 80) }}</p>
                                </div>
                            </article>
                        @empty
                            <article class="aside-news-item">
                                <div class="aside-news-thumb" style="background-image: url('/images/actu1.JPG');"></div>
                                <div>
                                    <div class="news-date">Février 2026</div>
                                    <h4>Sortie terrain dans les Pyrénées</h4>
                                    <p>Compte-rendu et retour d'expérience.</p>
                                </div>
                            </article>
                            <article class="aside-news-item">
                                <div class="aside-news-thumb" style="background-image: url('/images/actu1.JPG');"></div>
                                <div>
                                    <div class="news-date">Mars 2026</div>
                                    <h4>Mise à jour d'Artemisiae</h4>
                                    <p>Nouveautés du portail naturaliste.</p>
                                </div>
                            </article>
                            <article class="aside-news-item">
                                <div class="aside-news-thumb" style="background-image: url('/images/DSC_379.jpg');"></div>
                                <div>
                                    <div class="news-date">Avril 2026</div>
                                    <h4>Vie associative et groupes</h4>
                                    <p>Ateliers et échanges du réseau.</p>
                                </div>
                            </article>
                        @endforelse
                    </div>

                    <div class="aside-news-footer">
                        <a href="{{ route('hub.articles.index') }}" class="text-link"><i class="icon icon-blue" data-lucide="arrow-right"></i>Toutes les actualités</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Communauté (split: photo gauche, texte droite) --}}
    <section>
        <div class="container split-section">
            <div class="content-panel">
                <div class="eyebrow gold"><i class="icon icon-gold" data-lucide="users-round"></i>Communauté</div>
                <div class="content-body">
                    <h3>Naturalistes, spécialistes, partenaires : un réseau pour tous</h3>
                    <p>Au coeur du réseau, des naturalistes de terrain observent, documentent et partagent leurs découvertes au quotidien. Autour d'eux, des experts et spécialistes — taxonomistes, écologues, généticiens — structurent et valident cette connaissance. Et à l'échelle institutionnelle, des partenaires comme l'OFB, le MNHN ou le PatriNat soutiennent et relaient les travaux de l'association.</p>
                    <p>Quel que soit votre profil, il y a un espace pour vous dans le réseau.</p>
                    <p style="font-style:italic; color:var(--forest);">Et concrètement, comment ça se traduit ?</p>
                    <div class="content-actions">
                        <a href="{{ route('member.work-groups') }}" class="btn btn-secondary"><i class="icon icon-blue" data-lucide="users"></i>Voir les groupes</a>
                        <a href="{{ route('hub.membership') }}" class="btn btn-primary"><i class="icon icon-sage" data-lucide="heart-plus"></i>Rejoindre OREINA</a>
                    </div>
                </div>
            </div>
            <div class="visual-block" style="background-image: url('/images/DSC_379.jpg');"></div>
        </div>
    </section>

    {{-- 4. Projets et actions (pleine largeur, fond blanc) --}}
    <section style="background:white; width:100vw; margin-left:calc(50% - 50vw); padding-left:calc(50vw - 50%); padding-right:calc(50vw - 50%);">
        <div class="container">
            <div class="section-head" style="flex-direction:column; align-items:flex-start;">
                <h2>Projets et actions</h2>
                <p>Des projets concrets, du plus accessible au plus spécialisé.</p>
            </div>

            <div class="project-grid-4">
                <article class="pub-card">
                    <div class="pub-card-icon" style="background: rgba(237,196,66,0.14);">
                        <i class="icon icon-gold" data-lucide="search"></i>
                    </div>
                    <h4>IDENT</h4>
                    <p>Apprendre à reconnaître les espèces, accéder aux guides et aux formations.</p>
                    <a href="#" class="text-link"><i data-lucide="arrow-right"></i>En savoir plus</a>
                </article>

                <article class="pub-card">
                    <div class="pub-card-icon" style="background: rgba(239,122,92,0.10);">
                        <i class="icon icon-coral" data-lucide="badge-check"></i>
                    </div>
                    <h4>QUALIF</h4>
                    <p>Valider, vérifier et garantir la fiabilité des observations partagées.</p>
                    <a href="#" class="text-link"><i data-lucide="arrow-right"></i>En savoir plus</a>
                </article>

                <article class="pub-card">
                    <div class="pub-card-icon" style="background: rgba(133,183,157,0.14);">
                        <i class="icon icon-sage" data-lucide="dna"></i>
                    </div>
                    <h4>SEQREF</h4>
                    <p>Construire une bibliothèque moléculaire de référence pour les Lépidoptères.</p>
                    <a href="#" class="text-link"><i data-lucide="arrow-right"></i>En savoir plus</a>
                </article>

                <article class="pub-card">
                    <div class="pub-card-icon" style="background: rgba(53,107,138,0.10);">
                        <i class="icon icon-blue" data-lucide="binary"></i>
                    </div>
                    <h4>TAXREF</h4>
                    <p>Nommer, classer et harmoniser les connaissances taxonomiques.</p>
                    <a href="#" class="text-link"><i data-lucide="arrow-right"></i>En savoir plus</a>
                </article>
            </div>
        </div>
    </section>

    {{-- 5. Publications (split: photo gauche, texte droite) --}}
    <section>
        <div class="container split-section">
            <div class="visual-block" style="background-image: url('/images/lepis.png');"></div>

            <div class="content-panel">
                <div class="eyebrow blue"><i class="icon icon-blue" data-lucide="scroll-text"></i>Publications</div>
                <div class="content-body">
                    <h3>Publier et partager les connaissances</h3>
                    <p>Le réseau produit deux publications complémentaires qui alimentent et valorisent les travaux de la communauté.</p>
                </div>

                <div class="pub-grid">
                    <article class="pub-card">
                        <div class="pub-card-icon" style="background: rgba(20,184,166,0.10);">
                            <i class="icon" style="color:#0d9488;" data-lucide="book-open-text"></i>
                        </div>
                        <h4>Chersotis</h4>
                        <p>Revue scientifique avec comité de relecture et DOI.</p>
                        <a href="{{ route('journal.home') }}" class="text-link"><i data-lucide="arrow-right"></i>Découvrir</a>
                    </article>

                    <article class="pub-card">
                        <div class="pub-card-icon" style="background: rgba(237,196,66,0.14);">
                            <i class="icon icon-gold" data-lucide="newspaper"></i>
                        </div>
                        <h4>Lepis</h4>
                        <p>Bulletin trimestriel des adhérents.</p>
                        <a href="#" class="text-link"><i data-lucide="arrow-right"></i>En savoir plus</a>
                    </article>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. Outils et bases de données (split: texte gauche, photo droite) --}}
    <section>
        <div class="container split-section">
            <div class="content-panel">
                <div class="eyebrow" style="background:rgba(53,107,138,0.08); color:var(--blue);"><i class="icon icon-blue" data-lucide="database"></i>Outils & données</div>
                <div class="content-body">
                    <h3>Des outils numériques au service de la connaissance</h3>
                </div>

                <div class="tool-list">
                    <div class="tool-list-row">
                        <div class="tool-list-icon"><i class="icon icon-blue" data-lucide="database"></i></div>
                        <div class="tool-list-body">
                            <strong>Artemisiae</strong>
                            <span>Portail de données naturalistes</span>
                        </div>
                        <div class="tool-list-stat">2,1 M+</div>
                    </div>
                    <div class="tool-list-row">
                        <div class="tool-list-icon"><i class="icon icon-blue" data-lucide="bug"></i></div>
                        <div class="tool-list-body">
                            <strong>Lepfunc</strong>
                            <span>Base des traits de vie</span>
                        </div>
                        <div class="tool-list-stat">25 639</div>
                    </div>
                    <div class="tool-list-row">
                        <div class="tool-list-icon"><i class="icon icon-blue" data-lucide="library"></i></div>
                        <div class="tool-list-body">
                            <strong>Base bibliographique</strong>
                            <span>Références documentaires</span>
                        </div>
                        <div class="tool-list-stat">23 520</div>
                    </div>
                </div>
            </div>

            <div class="visual-block" style="background-image: url('/images/actu1.JPG');"></div>
        </div>
    </section>

    {{-- 7. CTA Section (full width) --}}
    <section>
        <div class="container">
            <article class="cta-panel">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="heart-handshake"></i>Adhésion & compte</div>
                <h2>Participer à la connaissance des Lépidoptères de France</h2>
                <p>Rejoignez une communauté engagée. Adhérez à l'association pour soutenir nos projets, accéder aux outils et contribuer à la science participative.</p>
                <div class="content-actions">
                    <a href="{{ route('hub.membership') }}" class="btn btn-primary"><i class="icon icon-sage" data-lucide="heart-plus"></i>Adhérer à l'association</a>
                    <a href="#" class="btn btn-ghost-light"><i class="icon icon-white" data-lucide="user-round-plus"></i>Créer un compte</a>
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
