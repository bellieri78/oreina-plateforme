@extends('layouts.journal')

@section('title', 'À propos de Chersotis')
@section('meta_description', 'À propos de Chersotis — Revue scientifique numérique en accès libre sur les Lépidoptères de France, publiée par l\'association OREINA.')

@section('content')
    <div style="padding: 36px 0;">
        <div class="container">
            {{-- Header --}}
            <div class="text-center mb-12">
                <div class="p-4 rounded-2xl inline-flex mb-6" style="background:var(--accent-surface)">
                    <i data-lucide="info" style="width:40px;height:40px;color:var(--accent)"></i>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold mb-4">À propos de Chersotis</h1>
                <p class="text-slate-600 max-w-2xl mx-auto">
                    Chersotis est la revue scientifique numérique en accès libre de l'association OREINA,
                    dédiée à l'étude des Lépidoptères de France.
                </p>
            </div>

            {{-- Mission --}}
            <div class="bg-white rounded-3xl border border-oreina-beige/50 p-6 sm:p-8 lg:p-12 mb-8">
                <h2 class="text-xl font-bold mb-6">Notre mission</h2>
                <div class="prose prose-slate max-w-none">
                    <p>
                        Publication scientifique numérique gratuite, exigeante mais accessible, Chersotis valorise
                        les travaux inédits sur les Lépidoptères de France. Sa vocation : faire avancer les
                        connaissances et servir de référence aux chercheurs, gestionnaires et naturalistes.
                    </p>

                    <h3 class="text-lg font-semibold mt-6 mb-3">Domaines de publication</h3>
                    <ul>
                        <li>Taxonomie - classification, nomenclature et systématique des Lépidoptères de France</li>
                        <li>Faunistique - présence, répartition et statut des espèces sur le territoire français</li>
                        <li>Inventaires - compilations et analyses de données d'inventaires avec portée scientifique</li>
                        <li>Travaux de recherche - études originales répondant à des questions scientifiques précises</li>
                        <li>Écologie et biologie des espèces - cycle de vie, comportement et relations écologiques</li>
                        <li>Acquisition de données - méthodes et outils pour l'obtention de données sur les Lépidoptères</li>
                    </ul>

                    <h3 class="text-lg font-semibold mt-6 mb-3">Public cible</h3>
                    <p>
                        Chersotis s'adresse aux chercheurs, gestionnaires et naturalistes travaillant sur
                        les Lépidoptères de France.
                    </p>
                </div>
            </div>

            {{-- Format & Periodicity --}}
            <div class="grid sm:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-3xl border border-oreina-beige/50 p-6 sm:p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:var(--accent-surface)">
                            <i data-lucide="monitor" style="width:24px;height:24px;color:var(--accent)"></i>
                        </div>
                        <div>
                            <h3 class="font-bold mb-2">Format</h3>
                            <p class="text-slate-600 text-sm">
                                Numérique, avec DOI attribués pour un référencement scientifique pérenne.
                                Impression annuelle disponible sur appel à souscription.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-3xl border border-oreina-beige/50 p-6 sm:p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:var(--accent-surface)">
                            <i data-lucide="calendar" style="width:24px;height:24px;color:var(--accent)"></i>
                        </div>
                        <div>
                            <h3 class="font-bold mb-2">Périodicité</h3>
                            <p class="text-slate-600 text-sm">
                                Publication au fil de l'eau, sans contrainte de pagination.
                                Les articles sont publiés dès leur acceptation par le comité de lecture.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Open Access --}}
            <div class="rounded-3xl p-6 sm:p-8 lg:p-12 mb-8 text-white" style="background:linear-gradient(135deg,var(--accent),#0d5c55)">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,0.10)">
                        <i data-lucide="lock-open" style="width:28px;height:28px;color:white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold mb-3">Accès libre</h2>
                        <p class="text-white/90">
                            Chersotis est une revue en accès libre (Open Access). Tous les articles sont disponibles
                            gratuitement et immédiatement, sans barrière d'abonnement. Nous croyons que la science
                            doit être accessible à tous.
                        </p>
                        <p class="text-white/80 mt-3 text-sm">
                            Les articles sont publiés sous licence Creative Commons Attribution (CC BY 4.0).
                        </p>
                    </div>
                </div>
            </div>

            {{-- Peer Review --}}
            <div class="bg-white rounded-3xl border border-oreina-beige/50 p-6 sm:p-8 lg:p-12 mb-8">
                <h2 class="text-xl font-bold mb-6">Évaluation par les pairs</h2>
                <div class="prose prose-slate max-w-none">
                    <p>
                        Tous les manuscrits soumis à Chersotis font l'objet d'une évaluation par un comité
                        de lecture strict avec experts externes. Ce processus garantit la qualité et la rigueur
                        scientifique des publications.
                    </p>
                    <div class="grid sm:grid-cols-2 gap-6 mt-8 not-prose">
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3" style="background:var(--accent-surface)">
                                <i data-lucide="clock" style="width:24px;height:24px;color:var(--accent)"></i>
                            </div>
                            <h3 class="font-semibold mb-1">Délai rapide</h3>
                            <p class="text-sm text-slate-600">Décision sous 4 semaines</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3" style="background:var(--accent-surface)">
                                <i data-lucide="users" style="width:24px;height:24px;color:var(--accent)"></i>
                            </div>
                            <h3 class="font-semibold mb-1">Experts qualifiés</h3>
                            <p class="text-sm text-slate-600">Spécialistes du domaine</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Passerelles Chersotis / Lepis --}}
            <div class="bg-white rounded-3xl border border-oreina-beige/50 p-6 sm:p-8 lg:p-12 mb-8">
                <h2 class="text-xl font-bold mb-6">Passerelles avec Lepis</h2>
                <div class="prose prose-slate max-w-none">
                    <p>
                        Chersotis et Lepis, le bulletin trimestriel des adhérents d'OREINA, fonctionnent en
                        complémentarité. Des passerelles existent entre les deux publications pour enrichir
                        mutuellement leurs contenus.
                    </p>
                    <div class="grid sm:grid-cols-2 gap-6 mt-6 not-prose">
                        <div class="p-4 rounded-xl border border-oreina-beige/50 bg-slate-50">
                            <div class="flex items-center gap-2 mb-3">
                                <i data-lucide="arrow-right" style="width:18px;height:18px;color:var(--accent)"></i>
                                <h3 class="font-semibold text-sm">De Lepis vers Chersotis</h3>
                            </div>
                            <ul class="text-sm text-slate-600 space-y-1">
                                <li>Observations remarquables approfondies scientifiquement</li>
                                <li>Notes de terrain développées en études</li>
                                <li>Appels à contribution débouchant sur des synthèses</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl border border-oreina-beige/50 bg-slate-50">
                            <div class="flex items-center gap-2 mb-3">
                                <i data-lucide="arrow-left" style="width:18px;height:18px;color:var(--accent)"></i>
                                <h3 class="font-semibold text-sm">De Chersotis vers Lepis</h3>
                            </div>
                            <ul class="text-sm text-slate-600 space-y-1">
                                <li>Résumés d'articles scientifiques complexes</li>
                                <li>« Retour terrain » sur les découvertes publiées</li>
                                <li>Vulgarisation des avancées taxonomiques</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Editorial Board --}}
            <div class="bg-white rounded-3xl border border-oreina-beige/50 p-6 sm:p-8 lg:p-12 mb-8">
                <h2 class="text-xl font-bold mb-6">Comité éditorial</h2>
                <div class="space-y-6">
                    <div class="border-b border-oreina-beige/50 pb-6">
                        <h3 class="font-semibold mb-1">Rédacteur en chef</h3>
                        <p class="text-slate-600">À définir</p>
                    </div>
                    <div class="border-b border-oreina-beige/50 pb-6">
                        <h3 class="font-semibold mb-1">Comité de rédaction</h3>
                        <p class="text-slate-600">Composition à venir</p>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-1">Comité scientifique</h3>
                        <p class="text-slate-600">Composition à venir</p>
                    </div>
                </div>
            </div>

            {{-- Indexing --}}
            <div class="bg-white rounded-3xl border border-oreina-beige/50 p-6 sm:p-8 lg:p-12 mb-8">
                <h2 class="text-xl font-bold mb-6">Indexation</h2>
                <div class="prose prose-slate max-w-none">
                    <p>
                        Les articles publiés dans Chersotis reçoivent un identifiant DOI (Digital Object Identifier)
                        via Crossref, assurant leur référencement pérenne et leur citabilité.
                    </p>
                    <div class="flex flex-wrap gap-4 mt-6 not-prose">
                        <div class="px-4 py-2 bg-slate-100 rounded-lg text-sm font-medium text-slate-700">
                            DOI Crossref
                        </div>
                        <div class="px-4 py-2 bg-slate-100 rounded-lg text-sm font-medium text-slate-700">
                            Google Scholar
                        </div>
                        <div class="px-4 py-2 bg-slate-100 rounded-lg text-sm font-medium text-slate-700">
                            Zoological Record
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div class="bg-slate-50 rounded-2xl p-6 text-center">
                <h3 class="font-bold mb-2">Contact éditorial</h3>
                <p class="text-slate-600 mb-4">Pour toute question concernant la revue :</p>
                <a href="mailto:revue@oreina.org" class="font-medium hover:underline" style="color:var(--accent)">
                    revue@oreina.org
                </a>
            </div>
        </div>
    </div>
@endsection
