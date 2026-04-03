@extends('layouts.journal')

@section('title', 'À propos')
@section('meta_description', 'À propos de la revue OREINA - Mission, comité éditorial et politique de publication.')

@section('content')
    <div style="padding: 36px 0;">
        <div class="container">
            {{-- Header --}}
            <div class="text-center mb-12">
                <div class="p-4 rounded-2xl inline-flex mb-6" style="background:var(--accent-surface)">
                    <i data-lucide="info" style="width:40px;height:40px;color:var(--accent)"></i>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold mb-4">À propos de la revue</h1>
                <p class="text-slate-600 max-w-2xl mx-auto">
                    OREINA est une revue scientifique en accès libre dédiée à l'étude des Lépidoptères de France.
                </p>
            </div>

            {{-- Mission --}}
            <div class="bg-white rounded-3xl border border-oreina-beige/50 p-6 sm:p-8 lg:p-12 mb-8">
                <h2 class="text-xl font-bold mb-6">Notre mission</h2>
                <div class="prose prose-slate max-w-none">
                    <p>
                        La revue OREINA a pour mission de diffuser les connaissances scientifiques sur les papillons
                        de France et des régions limitrophes. Elle publie des travaux originaux de haute qualité,
                        soumis à une évaluation rigoureuse par les pairs.
                    </p>
                    <p>
                        Nos domaines de publication couvrent :
                    </p>
                    <ul>
                        <li>La systématique et la taxonomie</li>
                        <li>La biogéographie et la répartition</li>
                        <li>L'écologie et la biologie des espèces</li>
                        <li>La conservation et la protection</li>
                        <li>Les inventaires faunistiques régionaux</li>
                    </ul>
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
                            OREINA est une revue en accès libre (Open Access). Tous les articles sont disponibles
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
                        Tous les manuscrits soumis à OREINA font l'objet d'une évaluation par les pairs
                        (peer review). Ce processus garantit la qualité et la rigueur scientifique des
                        publications.
                    </p>
                    <div class="grid sm:grid-cols-3 gap-6 mt-8 not-prose">
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3" style="background:var(--accent-surface)">
                                <i data-lucide="check-circle" style="width:24px;height:24px;color:var(--accent)"></i>
                            </div>
                            <h3 class="font-semibold mb-1">Double aveugle</h3>
                            <p class="text-sm text-slate-600">Anonymat des auteurs et relecteurs</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3" style="background:var(--accent-surface)">
                                <i data-lucide="clock" style="width:24px;height:24px;color:var(--accent)"></i>
                            </div>
                            <h3 class="font-semibold mb-1">Délai rapide</h3>
                            <p class="text-sm text-slate-600">Décision sous 8 semaines</p>
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
                        Les articles publiés dans OREINA reçoivent un identifiant DOI (Digital Object Identifier)
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
