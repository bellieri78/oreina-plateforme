@extends('layouts.hub')

@section('title', 'Le magazine oreina — 2008-2026')
@section('meta_description', 'Le magazine oreina, trimestriel de l\'association des lépidoptéristes de France : 72 numéros, près de 900 articles parus depuis 2008. Une mémoire éditoriale entièrement accessible en ligne sur le portail bibliographique d\'Artemisiae.')

@push('styles')
<style>
    .mag-cover-card {
        background: white;
        border: 1px solid rgba(219, 203, 199, 0.5);
        border-radius: 1rem;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .mag-cover-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.08);
    }
    .mag-cover-card .cover-image {
        aspect-ratio: 3/4;
        background-size: cover;
        background-position: center;
        background-color: #f4f1ec;
    }
    .mag-cover-card .cover-image.placeholder {
        background: linear-gradient(135deg, #EF7A5C 0%, #EDC442 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .mag-cover-card .cover-image.placeholder::after {
        content: attr(data-issue);
        color: rgba(255,255,255,0.92);
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        text-shadow: 0 2px 12px rgba(0,0,0,0.18);
        font-style: italic;
    }
    .mag-cover-card .cover-caption {
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
        color: var(--color-slate-600, #475569);
    }
    .mag-hero-visual {
        min-height: 320px;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        background-size: cover;
        background-position: center;
        background-color: #f4f1ec;
    }
    .mag-hero-visual.placeholder {
        background: linear-gradient(135deg, #16302B 0%, #2C5F2D 35%, #EF7A5C 70%, #EDC442 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        padding: 2rem;
    }
    .mag-hero-visual.placeholder .hero-label {
        font-family: Georgia, serif;
        font-style: italic;
        font-size: 3.5rem;
        font-weight: 700;
        text-shadow: 0 4px 24px rgba(0,0,0,0.25);
        line-height: 1;
    }
    .mag-hero-visual.placeholder .hero-sublabel {
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: 0.18em;
        margin-top: 0.75rem;
        opacity: 0.92;
    }
    .timeline-step {
        position: relative;
        padding-left: 2.25rem;
        padding-bottom: 2rem;
        border-left: 2px solid rgba(239, 122, 92, 0.25);
    }
    .timeline-step:last-child { padding-bottom: 0; }
    .timeline-step::before {
        content: "";
        position: absolute;
        left: -9px;
        top: 0.25rem;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--color-oreina-coral, #EF7A5C);
        box-shadow: 0 0 0 4px rgba(239, 122, 92, 0.15);
    }
    .stat-block {
        background: white;
        border: 1px solid rgba(219, 203, 199, 0.5);
        border-radius: 1rem;
        padding: 1.25rem;
        text-align: center;
    }
    .stat-block .stat-number {
        font-size: 2.25rem;
        font-weight: 700;
        line-height: 1.1;
        color: var(--color-oreina-coral, #EF7A5C);
    }
    .stat-block .stat-label {
        font-size: 0.85rem;
        color: var(--color-slate-600, #475569);
        margin-top: 0.4rem;
        line-height: 1.3;
    }
</style>
@endpush

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-16 bg-warm">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="eyebrow coral mb-6">
                <i class="icon icon-coral" data-lucide="newspaper"></i>
                Le magazine historique
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark"><em>oreina</em></h1>
            <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl mx-auto">
                Dix-huit ans de lépidoptérologie française en 72 numéros
            </p>
        </div>
    </section>

    {{-- Chapô + visuel principal --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-stretch">
                <div class="flex flex-col justify-center">
                    <span class="inline-flex self-start px-4 py-2 rounded-full bg-oreina-coral/10 text-oreina-coral text-sm font-bold">2008 — 2026</span>
                    <h2 class="text-3xl font-bold text-oreina-dark mt-4 mb-6">Une mémoire éditoriale ouverte</h2>
                    <div class="space-y-4 leading-relaxed text-slate-600 text-lg">
                        <p>
                            Trimestriel illustré paru pour la première fois en 2008, le magazine <em>oreina</em> a accompagné pendant dix-huit ans la communauté française des lépidoptéristes. Ses pages ont couvert la diversité des hétérocères et des rhopalocères de métropole, de Corse et d'outre-mer, mêlant taxonomie, faunistique, écologie, biologie des espèces, techniques de terrain et vie associative.
                        </p>
                        <p>
                            Du <strong>n° 1</strong> aux <strong>72</strong> numéros parus, c'est une production rare dans l'entomologie francophone amateur : <strong>près de 900 articles</strong>, des centaines d'auteurs, et un fonds documentaire entièrement numérisé, librement consultable sur le portail bibliographique d'<em>Artemisiae</em>.
                        </p>
                    </div>
                </div>
                <div class="mag-hero-visual placeholder">
                    <div>
                        <div class="hero-label">oreina</div>
                        <div class="hero-sublabel">2008 — 2026</div>
                    </div>
                </div>
            </div>

            {{-- Chiffres clés --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-12">
                <div class="stat-block">
                    <div class="stat-number">72</div>
                    <div class="stat-label">numéros parus<br>de 2008 à 2026</div>
                </div>
                <div class="stat-block">
                    <div class="stat-number">~900</div>
                    <div class="stat-label">articles<br>scientifiques et naturalistes</div>
                </div>
                <div class="stat-block">
                    <div class="stat-number">18</div>
                    <div class="stat-label">ans de parution<br>trimestrielle</div>
                </div>
                <div class="stat-block">
                    <div class="stat-number">100 %</div>
                    <div class="stat-label">des articles en accès libre<br>sur <em>Artemisiae</em></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Genèse et trajectoire --}}
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow sage mb-4 inline-flex">
                    <i class="icon icon-sage" data-lucide="history"></i>
                    Genèse
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">D'<em>Alexanor</em> à <em>oreina</em></h2>
            </div>

            <div class="space-y-4 leading-relaxed text-slate-600 text-lg">
                <p>
                    Au milieu des années 2000, la communauté française des lépidoptéristes traverse une période de fragilité éditoriale. <em>Alexanor</em>, revue historique de référence depuis 1959, est en pause. Les espaces de mise en relation entre lépidoptéristes amateurs et professionnels se sont raréfiés. Il manque un support fédérateur où la diversité des pratiques naturalistes puisse se retrouver.
                </p>
                <p>
                    C'est dans ce contexte, et dans la dynamique de la parution du <em>Guide des papillons nocturnes de France</em> chez Delachaux et Niestlé, que naît oreina : association déclarée le 10 janvier 2007, premier numéro de son magazine éponyme en 2008. Le format est d'emblée installé : trimestriel, généreusement illustré, ouvert à toutes les familles de Lépidoptères. Très vite, le magazine s'impose comme un rendez-vous attendu des lépidoptéristes francophones et structure autour de lui la communauté qui fait aujourd'hui la force de l'association.
                </p>
                <p>
                    Pendant dix-huit ans, le magazine va remplir trois fonctions complémentaires : <strong>diffuser des connaissances inédites</strong> sur les Lépidoptères de France (descriptions d'espèces, premières mentions, révisions taxonomiques, études écologiques), <strong>transmettre des savoir-faire</strong> (techniques d'élevage, méthodes de chasse, identification de groupes difficiles, portfolios), et <strong>faire vivre le réseau</strong> en valorisant les contributions de plusieurs centaines d'auteurs amateurs et professionnels.
                </p>
            </div>
        </div>
    </section>

    {{-- Galerie de couvertures --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="px-4 py-2 rounded-full bg-oreina-blue/10 text-oreina-blue text-sm font-bold">Quelques couvertures</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Dix-huit ans d'iconographie lépidoptérique</h2>
                <p class="text-slate-600 mt-2 max-w-2xl mx-auto">
                    Chaque trimestre, une espèce, un milieu, un dossier mis en couverture. Un panorama visuel de la diversité des Lépidoptères de France.
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                {{-- Placeholders gradient OREINA. Remplacer chaque cover-image par
                     style="background-image: url('/images/magazine/oreina-nXX.jpg');"
                     (et retirer la classe `placeholder`) au fur et à mesure du dépôt des couvertures. --}}
                <figure class="mag-cover-card">
                    <div class="cover-image placeholder" data-issue="n° 1"></div>
                    <figcaption class="cover-caption"><strong>n° 1</strong> · 2008</figcaption>
                </figure>
                <figure class="mag-cover-card">
                    <div class="cover-image placeholder" data-issue="n° 10"></div>
                    <figcaption class="cover-caption"><strong>n° 10</strong> · 2010</figcaption>
                </figure>
                <figure class="mag-cover-card">
                    <div class="cover-image placeholder" data-issue="n° 25"></div>
                    <figcaption class="cover-caption"><strong>n° 25</strong> · 2014</figcaption>
                </figure>
                <figure class="mag-cover-card">
                    <div class="cover-image placeholder" data-issue="n° 40"></div>
                    <figcaption class="cover-caption"><strong>n° 40</strong> · 2017</figcaption>
                </figure>
                <figure class="mag-cover-card">
                    <div class="cover-image placeholder" data-issue="n° 55"></div>
                    <figcaption class="cover-caption"><strong>n° 55</strong> · 2021</figcaption>
                </figure>
                <figure class="mag-cover-card">
                    <div class="cover-image placeholder" data-issue="n° 63-64"></div>
                    <figcaption class="cover-caption"><strong>n° 63-64</strong> · 2023 · Zygènes</figcaption>
                </figure>
                <figure class="mag-cover-card">
                    <div class="cover-image placeholder" data-issue="n° 65"></div>
                    <figcaption class="cover-caption"><strong>n° 65</strong> · 2024</figcaption>
                </figure>
                <figure class="mag-cover-card">
                    <div class="cover-image placeholder" data-issue="n° 66"></div>
                    <figcaption class="cover-caption"><strong>n° 66</strong> · 2024 · <em>Omia albertlegraini</em></figcaption>
                </figure>
                <figure class="mag-cover-card">
                    <div class="cover-image placeholder" data-issue="n° 67"></div>
                    <figcaption class="cover-caption"><strong>n° 67</strong> · 2024 · <em>Lycia isabellae</em></figcaption>
                </figure>
                <figure class="mag-cover-card">
                    <div class="cover-image placeholder" data-issue="n° 68"></div>
                    <figcaption class="cover-caption"><strong>n° 68</strong> · 2024 · Rencontres</figcaption>
                </figure>
                <figure class="mag-cover-card">
                    <div class="cover-image placeholder" data-issue="n° 69"></div>
                    <figcaption class="cover-caption"><strong>n° 69</strong> · 2025 · Migrations</figcaption>
                </figure>
                <figure class="mag-cover-card">
                    <div class="cover-image placeholder" data-issue="n° 72"></div>
                    <figcaption class="cover-caption"><strong>n° 72</strong> · 2026</figcaption>
                </figure>
            </div>
        </div>
    </section>

    {{-- Champs éditoriaux --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="px-4 py-2 rounded-full bg-oreina-green/10 text-oreina-green text-sm font-bold">Champs éditoriaux</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Ce qu'on y trouve</h2>
                <p class="text-slate-600 mt-2 max-w-2xl mx-auto">
                    La ligne éditoriale historique du magazine couvre l'ensemble des champs de la lépidoptérologie française, des micros aux macros, des rhopalocères aux hétérocères.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="pub-card-icon blue flex-shrink-0">
                            <i class="icon icon-blue" data-lucide="layers"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Taxonomie &amp; nomenclature</h3>
                            <p class="text-slate-600 text-sm">
                                Descriptions d'espèces nouvelles, révisions, synonymies, changements de combinaison.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="pub-card-icon sage flex-shrink-0">
                            <i class="icon icon-sage" data-lucide="map"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Faunistique &amp; chorologie</h3>
                            <p class="text-slate-600 text-sm">
                                Premières mentions nationales, régionales, départementales ; extensions et contractions d'aire.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="pub-card-icon coral flex-shrink-0">
                            <i class="icon icon-coral" data-lucide="leaf"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Écologie &amp; biologie</h3>
                            <p class="text-slate-600 text-sm">
                                Cycles biologiques, relations plantes-hôtes, comportements, adaptations aux milieux.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="pub-card-icon gold flex-shrink-0">
                            <i class="icon icon-gold" data-lucide="search"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Identification &amp; techniques</h3>
                            <p class="text-slate-600 text-sm">
                                Clés et fiches pour groupes difficiles, méthodes de chasse, élevage, préparation.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="pub-card-icon turquoise flex-shrink-0">
                            <i class="icon icon-turquoise" data-lucide="image"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Portfolios &amp; iconographie</h3>
                            <p class="text-slate-600 text-sm">
                                Galeries photographiques thématiques, mises en image de milieux, de séries, de complexes d'espèces.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="pub-card-icon blue flex-shrink-0">
                            <i class="icon icon-blue" data-lucide="users"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Vie du réseau</h3>
                            <p class="text-slate-600 text-sm">
                                Rencontres annuelles, comptes rendus de sessions, hommages, actualités associatives.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Trajectoire / repères chronologiques --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="milestone"></i>
                    Repères
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Quelques jalons</h2>
            </div>

            <div class="pl-2">
                <div class="timeline-step">
                    <div class="text-sm text-oreina-coral font-bold mb-1">10 janvier 2007</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Déclaration d'oreina</h3>
                    <p class="text-slate-600 text-sm">
                        Création de l'association loi 1901 par David Demergès et Roland Robineau, autour d'un projet éditorial dédié aux Lépidoptères de France.
                    </p>
                </div>

                <div class="timeline-step">
                    <div class="text-sm text-oreina-coral font-bold mb-1">2008</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Parution du n° 1</h3>
                    <p class="text-slate-600 text-sm">
                        Premier numéro du magazine trimestriel <em>oreina</em>. Le format éditorial s'installe : illustration soignée, articles courts et fouillés, ouverture à toutes les familles de Lépidoptères.
                    </p>
                </div>

                <div class="timeline-step">
                    <div class="text-sm text-oreina-coral font-bold mb-1">2018</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Lancement d'<em>Artemisiae</em></h3>
                    <p class="text-slate-600 text-sm">
                        Mise en ligne de la plateforme naturaliste d'oreina, qui hébergera progressivement le portail bibliographique donnant accès à l'intégralité du fonds magazine.
                    </p>
                </div>

                <div class="timeline-step">
                    <div class="text-sm text-oreina-coral font-bold mb-1">2024</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Convention pluriannuelle avec l'OFB</h3>
                    <p class="text-slate-600 text-sm">
                        Reconnaissance de l'expertise lépidoptérologique d'oreina à l'échelle nationale : TAXREF, SEQREF, BDC, IDENT, QUALIF. Le besoin de clarifier la ligne éditoriale devient stratégique.
                    </p>
                </div>

                <div class="timeline-step">
                    <div class="text-sm text-oreina-coral font-bold mb-1">20 décembre 2025</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Assemblée générale extraordinaire</h3>
                    <p class="text-slate-600 text-sm">
                        Décision de scinder l'offre éditoriale historique en deux supports complémentaires : <em>Lepis</em> pour la vie associative, <em>Chersotis</em> pour la science.
                    </p>
                </div>

                <div class="timeline-step">
                    <div class="text-sm text-oreina-coral font-bold mb-1">2026</div>
                    <h3 class="font-bold text-oreina-dark mb-2">n° 72 — dernier numéro du magazine <em>oreina</em></h3>
                    <p class="text-slate-600 text-sm">
                        Aboutissement de dix-huit ans de parution trimestrielle. Le magazine cède la place à un nouveau dispositif éditorial à deux supports, qui prolonge et spécialise son héritage.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Filiation : Lepis + Chersotis --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="px-4 py-2 rounded-full bg-oreina-coral/10 text-oreina-coral text-sm font-bold">Filiation</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Ce que le magazine devient</h2>
                <p class="text-slate-600 mt-2 max-w-2xl mx-auto">
                    À partir de 2026, l'héritage du magazine <em>oreina</em> se prolonge dans deux publications complémentaires, conçues pour mieux servir leurs publics respectifs.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <a href="{{ route('hub.lepis') }}" class="passerelle-block hover:shadow-lg transition group">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="pub-card-icon sage flex-shrink-0">
                            <i class="icon icon-sage" data-lucide="book-open"></i>
                        </div>
                        <h3 class="font-bold text-oreina-dark group-hover:text-oreina-green transition"><em>Lepis</em></h3>
                    </div>
                    <p class="text-slate-700 text-sm mb-3">
                        Le bulletin trimestriel des adhérents : vie associative, partages d'expériences, fiches techniques, notes de chasses, portfolios.
                    </p>
                    <span class="text-oreina-green text-sm font-bold inline-flex items-center gap-1">
                        Découvrir <i data-lucide="arrow-right" style="width:16px;height:16px"></i>
                    </span>
                </a>

                <a href="{{ url('/revue') }}" class="passerelle-block hover:shadow-lg transition group">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="pub-card-icon blue flex-shrink-0">
                            <i class="icon icon-blue" data-lucide="microscope"></i>
                        </div>
                        <h3 class="font-bold text-oreina-dark group-hover:text-oreina-turquoise transition"><em>Chersotis</em></h3>
                    </div>
                    <p class="text-slate-700 text-sm mb-3">
                        La revue scientifique numérique en accès libre : articles inédits avec DOI, comité de lecture, publication au fil de l'eau.
                    </p>
                    <span class="text-oreina-turquoise text-sm font-bold inline-flex items-center gap-1">
                        Découvrir <i data-lucide="arrow-right" style="width:16px;height:16px"></i>
                    </span>
                </a>
            </div>
        </div>
    </section>

    {{-- CTA — accès au fonds Artemisiae --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="cta-panel">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="library"></i>Fonds bibliographique</div>
                <h2>Consulter les 72 numéros</h2>
                <p>
                    L'intégralité des numéros du magazine <em>oreina</em> et des articles parus depuis 2008 est librement accessible, en lecture et en téléchargement PDF, sur le portail bibliographique d'<em>Artemisiae</em>.
                </p>
                <div class="content-actions">
                    <a href="https://oreina.org/artemisiae/biblio/index.php?module=magazine&amp;action=listemag"
                       target="_blank" rel="noopener"
                       class="btn btn-primary">
                        <i class="icon icon-sage" data-lucide="external-link"></i>
                        Accéder à la liste des numéros
                    </a>
                    <a href="{{ route('hub.outils.artemisiae') }}" class="btn btn-ghost-light">
                        <i class="icon icon-white" data-lucide="globe-2"></i>
                        À propos d'<em>Artemisiae</em>
                    </a>
                </div>
            </article>
        </div>
    </section>
@endsection
