@extends('layouts.hub')

@section('title', 'Accueil')
@section('meta_description', 'OREINA - Association des Lépidoptères de France. Rejoignez une communauté passionnée au service de la connaissance des papillons.')

@push('styles')
<style>
    /* ── Hero ── */
    .hero { padding: 0 0 34px; }

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
        letter-spacing: -0.06em;
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

    /* ── News ── */
    .news-layout {
        display: grid;
        grid-template-columns: 1.5fr 0.95fr;
        gap: 18px;
        align-items: start;
    }

    .news-feature,
    .news-item,
    .content-panel,
    .tool-panel,
    .cta-panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow);
    }

    .news-feature { overflow: hidden; }

    .news-feature-media {
        height: 360px;
        background-size: cover;
        background-position: center;
    }

    .news-feature-body { padding: 26px; }

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

    .news-feature h3,
    .news-item h3,
    .content-panel h3,
    .tool-panel h3,
    .cta-panel h2 {
        margin: 12px 0 10px;
        line-height: 1.08;
        letter-spacing: -0.04em;
    }

    .news-feature h3 { font-size: 34px; }
    .news-item h3,
    .content-panel h3,
    .tool-panel h3 { font-size: 24px; }

    .news-feature p,
    .news-item p,
    .content-panel p,
    .tool-panel p,
    .cta-panel p {
        margin: 0;
        color: var(--muted);
        font-size: 15px;
        line-height: 1.7;
    }

    .news-list {
        display: grid;
        gap: 14px;
    }

    .news-item {
        padding: 22px;
        background: var(--surface-soft);
    }

    /* ── Split sections ── */
    .split-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 28px;
        align-items: center;
    }

    .visual-block {
        min-height: 460px;
        border-radius: 32px;
        overflow: hidden;
        box-shadow: var(--shadow);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .content-panel {
        padding: 34px;
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

    .content-body { max-width: 540px; }
    .content-body p + p { margin-top: 12px; }

    .content-actions {
        margin-top: 22px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    /* ── Tool panel ── */
    .tool-panel {
        padding: 30px;
        background: #eef4f8;
    }

    .tool-kpis {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
    }

    .tool-kpi {
        padding: 12px 14px;
        border-radius: 16px;
        background: rgba(255,255,255,0.72);
        border: 1px solid rgba(22,48,43,0.06);
        min-width: 140px;
    }

    .tool-kpi strong {
        display: block;
        font-size: 28px;
        line-height: 1;
        margin-bottom: 4px;
        letter-spacing: -0.04em;
        color: var(--forest);
    }

    .tool-kpi span {
        font-size: 13px;
        color: var(--muted);
        line-height: 1.45;
    }

    /* ── Projects ── */
    .project-list {
        display: grid;
        gap: 12px;
        margin-top: 22px;
    }

    .project-row {
        display: grid;
        grid-template-columns: 42px 1fr auto;
        gap: 14px;
        align-items: center;
        padding: 16px 18px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 18px;
        box-shadow: var(--shadow);
    }

    .project-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        background: rgba(53,107,138,0.08);
    }

    .project-row:nth-child(2) .project-icon { background: rgba(133,183,157,0.16); }
    .project-row:nth-child(3) .project-icon { background: rgba(237,196,66,0.18); }
    .project-row:nth-child(4) .project-icon { background: rgba(239,122,92,0.12); }
    .project-row:nth-child(5) .project-icon { background: rgba(53,107,138,0.08); }

    .project-main strong {
        display: block;
        font-size: 16px;
        letter-spacing: -0.02em;
    }

    .project-main span {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.5;
    }

    .project-pill {
        padding: 8px 10px;
        border-radius: 999px;
        background: var(--surface-soft);
        border: 1px solid rgba(22,48,43,0.06);
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    /* ── CTA ── */
    .cta-panel {
        position: relative;
        overflow: hidden;
        padding: 38px;
        background: var(--forest);
        color: white;
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

    .cta-panel p {
        max-width: 760px;
        color: rgba(255,255,255,0.82);
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
        .news-layout,
        .split-section {
            grid-template-columns: 1fr;
        }

        .hero-card {
            min-height: 88vh;
        }
    }

    @media (max-width: 760px) {
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
        .tool-panel,
        .cta-panel,
        .news-feature-body,
        .news-item {
            padding: 22px;
        }

        .project-row {
            grid-template-columns: 42px 1fr;
        }

        .project-pill {
            grid-column: 2;
            justify-self: start;
        }
    }
</style>
@endpush

@section('content')
    {{-- 1. Hero Section --}}
    <section class="hero">
        <article class="hero-card">
            <div class="hero-content">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="leaf"></i>Association loi 1901 · réseau naturaliste · science participative</div>
                <h1>Observer, comprendre et protéger les Lépidoptères de France</h1>
                <p>OREINA fédère une communauté de naturalistes, structure la connaissance et développe des outils pour partager, qualifier et valoriser les données à l'échelle nationale.</p>

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

    {{-- 2. Actualités Section --}}
    <section id="actualites">
        <div class="container">
            <div class="section-head">
                <div>
                    <h2>Actualités</h2>
                    <p>Dernières nouvelles de la communauté, des projets et des publications OREINA.</p>
                </div>
                <a href="{{ route('hub.articles.index') }}" class="text-link"><i class="icon icon-blue" data-lucide="arrow-right"></i>Toutes les actualités</a>
            </div>

            @if(isset($latestArticles) && $latestArticles->count() > 0)
                <div class="news-layout">
                    {{-- Featured article --}}
                    <article class="news-feature">
                        <div class="news-feature-media" style="background-image: url('{{ $latestArticles->first()->featured_image ? Storage::url($latestArticles->first()->featured_image) : '/images/actu2.jpg' }}');"></div>
                        <div class="news-feature-body">
                            <div class="tag tag-gold"><i class="icon icon-gold" data-lucide="calendar-days"></i>Événement</div>
                            <div class="news-date">{{ $latestArticles->first()->published_at->format('d F Y') }}</div>
                            <h3><a href="{{ route('hub.articles.show', $latestArticles->first()) }}">{{ $latestArticles->first()->title }}</a></h3>
                            <p>{{ $latestArticles->first()->summary }}</p>
                        </div>
                    </article>

                    {{-- News list --}}
                    <div class="news-list">
                        @foreach($latestArticles->skip(1)->take(3) as $article)
                        <article class="news-item">
                            <div class="tag tag-blue"><i class="icon icon-blue" data-lucide="microscope"></i>Article</div>
                            <div class="news-date">{{ $article->published_at->format('d F Y') }}</div>
                            <h3><a href="{{ route('hub.articles.show', $article) }}">{{ $article->title }}</a></h3>
                            <p>{{ Str::limit($article->summary, 120) }}</p>
                        </article>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Static fallback content --}}
                <div class="news-layout">
                    <article class="news-feature">
                        <div class="news-feature-media" style="background-image: url('/images/actu2.jpg');"></div>
                        <div class="news-feature-body">
                            <div class="tag tag-gold"><i class="icon icon-gold" data-lucide="calendar-days"></i>Événement</div>
                            <div class="news-date">05 février 2026</div>
                            <h3>Sortie terrain : à la découverte des Zygènes des Pyrénées</h3>
                            <p>Compte-rendu, retour d'expérience et préparation des prochaines sorties : retrouvez les moments forts de nos actions de terrain.</p>
                        </div>
                    </article>

                    <div class="news-list">
                        <article class="news-item">
                            <div class="tag tag-blue"><i class="icon icon-blue" data-lucide="microscope"></i>Science</div>
                            <div class="news-date">28 février 2026</div>
                            <h3>Guide d'identification : les Sphingidés de France</h3>
                            <p>Une mise en avant plus forte des contenus scientifiques et pédagogiques publiés par l'association.</p>
                        </article>

                        <article class="news-item">
                            <div class="tag tag-sage"><i class="icon icon-sage" data-lucide="database"></i>Portail</div>
                            <div class="news-date">Mars 2026</div>
                            <h3>Mise à jour d'Artemisiae et nouvelles ressources</h3>
                            <p>Actualités techniques, nouveautés fonctionnelles et valorisation des usages du portail.</p>
                        </article>

                        <article class="news-item">
                            <div class="tag tag-gold"><i class="icon icon-gold" data-lucide="users"></i>Réseau</div>
                            <div class="news-date">Avril 2026</div>
                            <h3>Ateliers, groupes de travail et vie associative</h3>
                            <p>Une place plus visible pour montrer qu'OREINA est aussi une communauté active.</p>
                        </article>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- 3. Association Section (split) --}}
    <section id="association">
        <div class="container split-section">
            <div class="content-panel">
                <div class="eyebrow sage"><i class="icon icon-sage" data-lucide="trees"></i>L'association</div>
                <div class="content-body">
                    <h3>Une association scientifique et un réseau naturaliste</h3>
                    <p>OREINA fédère des naturalistes, structure la connaissance sur les Lépidoptères, développe des outils, soutient des projets et anime des dynamiques collectives à l'échelle nationale.</p>
                    <p>Depuis 2007, l'association mobilise chercheurs, amateurs éclairés et passionnés autour d'une mission commune : observer, comprendre et protéger les papillons de France.</p>
                    <div class="content-actions">
                        <a href="{{ route('hub.about') }}" class="btn btn-secondary"><i class="icon icon-blue" data-lucide="info"></i>Qui sommes-nous ?</a>
                    </div>
                </div>
            </div>

            <div class="visual-block" style="background-image: url('/images/about-mission.jpg');"></div>
        </div>
    </section>

    {{-- 4. Portail & Outils Section (split, reversed) --}}
    <section id="portail">
        <div class="container split-section">
            <div class="visual-block" style="background-image: url('/images/actu1.JPG');"></div>

            <div class="tool-panel">
                <div class="eyebrow" style="background:rgba(53,107,138,0.10);color:var(--blue);"><i class="icon icon-blue" data-lucide="database"></i>Portail & outils</div>
                <h3>Un portail national de données et d'expertise</h3>
                <p>Artemisiae centralise les observations, les traits de vie et les références bibliographiques. Un écosystème numérique au service de la connaissance des Lépidoptères.</p>

                <div class="tool-kpis">
                    <div class="tool-kpi">
                        <strong>2,1 M+</strong>
                        <span>observations dans le portail</span>
                    </div>
                    <div class="tool-kpi">
                        <strong>25 639</strong>
                        <span>traits de vie mobilisables</span>
                    </div>
                    <div class="tool-kpi">
                        <strong>23 520</strong>
                        <span>références documentaires</span>
                    </div>
                </div>

                <div class="content-actions">
                    <a href="#" class="btn btn-primary"><i class="icon icon-sage" data-lucide="database"></i>Accéder à Artemisiae</a>
                    <a href="#" class="btn btn-secondary"><i class="icon icon-blue" data-lucide="book-open"></i>Découvrir les outils</a>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. Projets Section --}}
    <section id="projets">
        <div class="container">
            <div class="section-head">
                <div>
                    <h2>Projets et actions</h2>
                    <p>Six projets majeurs portés par nos bénévoles, soutenus par l'OFB et l'Union Européenne.</p>
                </div>
            </div>

            <div class="project-list">
                <article class="project-row">
                    <div class="project-icon"><i class="icon icon-blue" data-lucide="binary"></i></div>
                    <div class="project-main">
                        <strong>TAXREF</strong>
                        <span>Référentiel taxonomique national des Lépidoptères.</span>
                    </div>
                    <div class="project-pill">Référentiel</div>
                </article>

                <article class="project-row">
                    <div class="project-icon"><i class="icon icon-sage" data-lucide="dna"></i></div>
                    <div class="project-main">
                        <strong>SEQREF</strong>
                        <span>Barcoding ADN et référentiel génétique.</span>
                    </div>
                    <div class="project-pill">Génétique</div>
                </article>

                <article class="project-row">
                    <div class="project-icon"><i class="icon icon-gold" data-lucide="search"></i></div>
                    <div class="project-main">
                        <strong>IDENT</strong>
                        <span>Outils d'identification, contenus d'aide et formations.</span>
                    </div>
                    <div class="project-pill">Identification</div>
                </article>

                <article class="project-row">
                    <div class="project-icon"><i class="icon icon-coral" data-lucide="badge-check"></i></div>
                    <div class="project-main">
                        <strong>QUALIF</strong>
                        <span>Validation et qualification des données naturalistes.</span>
                    </div>
                    <div class="project-pill">Qualité des données</div>
                </article>

                <article class="project-row">
                    <div class="project-icon"><i class="icon icon-blue" data-lucide="scroll-text"></i></div>
                    <div class="project-main">
                        <strong>Revue scientifique</strong>
                        <span>Publications, diffusion des connaissances et valorisation des travaux.</span>
                    </div>
                    <div class="project-pill">Publication</div>
                </article>
            </div>
        </div>
    </section>

    {{-- 6. Réseau / Communauté Section (split) --}}
    <section id="reseau">
        <div class="container split-section">
            <div class="content-panel">
                <div class="eyebrow gold"><i class="icon icon-gold" data-lucide="users-round"></i>Communauté</div>
                <div class="content-body">
                    <h3>Des groupes de travail pour contribuer activement</h3>
                    <p>Participez activement à l'amélioration des connaissances en rejoignant nos groupes thématiques : taxonomie, écologie, conservation, et bien plus encore. Chaque groupe structure ses échanges, partage ses ressources et fait avancer des projets concrets.</p>
                    <p>Que vous soyez débutant ou expert, il y a un espace pour vous dans le réseau OREINA.</p>
                    <div class="content-actions">
                        <a href="{{ route('member.work-groups') }}" class="btn btn-secondary"><i class="icon icon-blue" data-lucide="users"></i>Voir les groupes</a>
                        <a href="{{ route('hub.membership') }}" class="btn btn-primary"><i class="icon icon-sage" data-lucide="heart-plus"></i>Rejoindre OREINA</a>
                    </div>
                </div>
            </div>

            <div class="visual-block" style="background-image: url('/images/actu3.JPG');"></div>
        </div>
    </section>

    {{-- 7. CTA Section --}}
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
    // Re-initialize Lucide icons after Livewire/Blade rendering
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
