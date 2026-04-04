@extends('layouts.hub')

@section('title', 'Lepis — Le bulletin trimestriel')
@section('meta_description', 'Lepis, le bulletin trimestriel des adhérents d\'OREINA : échange, formation, vie associative et observations sur les Lépidoptères de France.')

@push('styles')
<style>
    .lepis-hero {
        background: linear-gradient(135deg, var(--color-oreina-green) 0%, #1a4a3a 100%);
        position: relative;
        overflow: hidden;
    }
    .lepis-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 60%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.05) 0%, transparent 70%);
        pointer-events: none;
    }
    .rubrique-card {
        background: white;
        border: 1px solid rgba(219, 203, 199, 0.5);
        border-radius: 1.5rem;
        padding: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .rubrique-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }
    .rubrique-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: linear-gradient(135deg, var(--color-oreina-green), #1a6b4a);
    }
    .passerelle-block {
        background: var(--color-oreina-beige);
        border-radius: 1rem;
        padding: 1.5rem;
    }
</style>
@endpush

@section('content')
    {{-- Hero --}}
    <section class="lepis-hero pt-28 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 text-white/90 text-sm font-medium mb-6">
                    <i data-lucide="book-open" style="width:16px;height:16px"></i>
                    Publication trimestrielle
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-4">Lepis</h1>
                <p class="text-xl sm:text-2xl text-white/90 max-w-2xl mx-auto">
                    Le bulletin trimestriel des adhérents d'OREINA
                </p>
            </div>
        </div>
    </section>

    {{-- Présentation --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-start">
                <div>
                    <span class="px-4 py-2 rounded-full bg-oreina-green/10 text-oreina-green text-sm font-bold">Présentation</span>
                    <h2 class="text-3xl font-bold text-oreina-dark mt-4 mb-6">La proximité associative</h2>
                    <div class="prose prose-lg text-slate-600">
                        <p>
                            Publication trimestrielle de liaison entre adhérents, Lepis privilégie l'échange,
                            la formation et la vie associative. Sa vocation : renforcer la communauté, transmettre
                            savoir-faire et bonnes pratiques, informer sur les activités d'OREINA.
                        </p>
                        <p>
                            Le ton se veut personnel et convivial, la participation large est encouragée,
                            tous niveaux confondus.
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="card p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <i data-lucide="users" style="width:20px;height:20px;color:var(--color-oreina-green)"></i>
                            <h3 class="font-bold text-oreina-dark text-sm">Public</h3>
                        </div>
                        <p class="text-slate-600 text-sm">Adhérents d'OREINA, tous niveaux</p>
                    </div>
                    <div class="card p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <i data-lucide="file-text" style="width:20px;height:20px;color:var(--color-oreina-green)"></i>
                            <h3 class="font-bold text-oreina-dark text-sm">Format</h3>
                        </div>
                        <p class="text-slate-600 text-sm">Papier, 12-16 pages abondamment illustrées</p>
                    </div>
                    <div class="card p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <i data-lucide="calendar" style="width:20px;height:20px;color:var(--color-oreina-green)"></i>
                            <h3 class="font-bold text-oreina-dark text-sm">Périodicité</h3>
                        </div>
                        <p class="text-slate-600 text-sm">Trimestriel (4 numéros par an)</p>
                    </div>
                    <div class="card p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <i data-lucide="shield-check" style="width:20px;height:20px;color:var(--color-oreina-green)"></i>
                            <h3 class="font-bold text-oreina-dark text-sm">Validation</h3>
                        </div>
                        <p class="text-slate-600 text-sm">Conseil d'administration et adhérents</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Rubriques --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="px-4 py-2 rounded-full bg-oreina-turquoise/10 text-oreina-turquoise text-sm font-bold">Contenu</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Les rubriques de Lepis</h2>
                <p class="text-slate-600 mt-2 max-w-2xl mx-auto">
                    Chaque numéro aborde une diversité de sujets au service de la communauté lépidoptériste.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Avancements projets OREINA --}}
                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="rubrique-icon">
                            <i data-lucide="rocket" style="width:18px;height:18px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Avancements projets OREINA</h3>
                            <p class="text-slate-600 text-sm">
                                Communication sur l'état d'avancement des projets de l'association : bilans d'étape
                                des conventions en cours, nouvelles fonctionnalités d'Artemisiae, résultats des
                                collaborations scientifiques, participation à des programmes nationaux.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Appel à contribution --}}
                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="rubrique-icon">
                            <i data-lucide="megaphone" style="width:18px;height:18px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Appel à contribution</h3>
                            <p class="text-slate-600 text-sm">
                                Sollicitations des membres pour alimenter projets et bases de données : recherche
                                de spécimens pour projets génétiques, demandes d'observations ciblées, collecte
                                de données pour synthèses, participation à des enquêtes participatives.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Fiches d'identification --}}
                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="rubrique-icon">
                            <i data-lucide="search" style="width:18px;height:18px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Fiches d'identification</h3>
                            <p class="text-slate-600 text-sm">
                                Outils pratiques d'aide à la détermination des espèces, contribuant à la montée
                                en compétence des adhérents.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Notes de terrain / Observations remarquables --}}
                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="rubrique-icon">
                            <i data-lucide="binoculars" style="width:18px;height:18px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Notes de terrain / Observations remarquables</h3>
                            <p class="text-slate-600 text-sm">
                                Partage d'observations intéressantes sans portée scientifique majeure : comportements
                                inhabituels, abondances exceptionnelles, phénologies décalées, anecdotes naturalistes
                                significatives.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Actualités membres et associations --}}
                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="rubrique-icon">
                            <i data-lucide="heart" style="width:18px;height:18px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Actualités membres et associations</h3>
                            <p class="text-slate-600 text-sm">
                                Vie de la communauté des lépidoptéristes français : portraits de membres actifs,
                                nouvelles des associations partenaires, hommages et nécrologies, événements marquants.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Conseils techniques --}}
                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="rubrique-icon">
                            <i data-lucide="wrench" style="width:18px;height:18px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Conseils techniques</h3>
                            <p class="text-slate-600 text-sm">
                                Transmission de savoir-faire pratiques : techniques d'observation et de capture,
                                photographie, étalage et conservation, élevage et reproduction, utilisation
                                d'outils (pièges, attractifs...).
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Portfolio --}}
                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="rubrique-icon">
                            <i data-lucide="image" style="width:18px;height:18px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Portfolio</h3>
                            <p class="text-slate-600 text-sm">
                                Valorisation esthétique et documentaire par l'image. Un espace dédié à la beauté
                                des Lépidoptères à travers la photographie.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Courrier des lecteurs --}}
                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="rubrique-icon">
                            <i data-lucide="mail" style="width:18px;height:18px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Courrier des lecteurs</h3>
                            <p class="text-slate-600 text-sm">
                                Échanges et interactions entre membres de l'association. Réactions aux articles
                                précédents, questions et discussions ouvertes.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Infos partenaires --}}
                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="rubrique-icon">
                            <i data-lucide="handshake" style="width:18px;height:18px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Infos partenaires</h3>
                            <p class="text-slate-600 text-sm">
                                Nouvelles et annonces des organismes partenaires : expositions, événements,
                                collaborations et programmes en lien avec les Lépidoptères.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Analyses d'ouvrages --}}
                <div class="rubrique-card">
                    <div class="flex items-start gap-3">
                        <div class="rubrique-icon">
                            <i data-lucide="book-marked" style="width:18px;height:18px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Analyses d'ouvrages</h3>
                            <p class="text-slate-600 text-sm">
                                Évaluations critiques de publications récentes sur les Lépidoptères et l'entomologie.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Passerelles avec Chersotis --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="px-4 py-2 rounded-full bg-oreina-green/10 text-oreina-green text-sm font-bold">Complémentarité</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Passerelles avec Chersotis</h2>
                <p class="text-slate-600 mt-2 max-w-2xl mx-auto">
                    Lepis et Chersotis, la revue scientifique d'OREINA, fonctionnent en complémentarité.
                    Des passerelles existent entre les deux publications.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="passerelle-block">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-oreina-green flex items-center justify-center">
                            <i data-lucide="arrow-up-right" style="width:20px;height:20px;color:white"></i>
                        </div>
                        <h3 class="font-bold text-oreina-dark">De Lepis vers Chersotis</h3>
                    </div>
                    <ul class="space-y-2 text-sm text-slate-700">
                        <li class="flex items-start gap-2">
                            <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--color-oreina-green);flex-shrink:0;margin-top:2px"></i>
                            Observations remarquables approfondies scientifiquement
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--color-oreina-green);flex-shrink:0;margin-top:2px"></i>
                            Notes de terrain développées en études
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--color-oreina-green);flex-shrink:0;margin-top:2px"></i>
                            Appels à contribution débouchant sur des synthèses
                        </li>
                    </ul>
                </div>

                <div class="passerelle-block">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-oreina-turquoise flex items-center justify-center">
                            <i data-lucide="arrow-down-left" style="width:20px;height:20px;color:white"></i>
                        </div>
                        <h3 class="font-bold text-oreina-dark">De Chersotis vers Lepis</h3>
                    </div>
                    <ul class="space-y-2 text-sm text-slate-700">
                        <li class="flex items-start gap-2">
                            <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--color-oreina-turquoise);flex-shrink:0;margin-top:2px"></i>
                            Résumés d'articles scientifiques complexes
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--color-oreina-turquoise);flex-shrink:0;margin-top:2px"></i>
                            « Retour terrain » sur les découvertes publiées
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--color-oreina-turquoise);flex-shrink:0;margin-top:2px"></i>
                            Vulgarisation des avancées taxonomiques
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="stats-banner text-center">
                <h2 class="text-2xl font-bold mb-4">Contribuez à Lepis</h2>
                <p class="text-white/90 mb-8 max-w-2xl mx-auto">
                    Vous avez une observation à partager, une idée d'article ou un conseil technique ?
                    Lepis est ouvert à toutes les contributions de ses adhérents.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('member.lepis.suggest') }}" class="inline-flex items-center gap-2 bg-white text-oreina-teal px-8 py-4 rounded-2xl font-bold hover:shadow-lg transition">
                        <i data-lucide="pen-line" style="width:20px;height:20px"></i>
                        Suggérer un article
                    </a>
                    <a href="{{ route('hub.membership') }}" class="inline-flex items-center gap-2 bg-white/10 text-white border border-white/30 px-8 py-4 rounded-2xl font-bold hover:bg-white/20 transition">
                        <i data-lucide="heart" style="width:20px;height:20px"></i>
                        Adhérer à OREINA
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
