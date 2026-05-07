@extends('layouts.hub')

@section('title', 'À propos')
@section('meta_description', 'OREINA, association loi 1901 dédiée à l\'étude scientifique, à la vulgarisation et à la protection des Lépidoptères de France depuis 2007.')

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-16 bg-warm">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="eyebrow sage mb-6">
                <i class="icon icon-sage" data-lucide="info"></i>
                L'association
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">À propos d'oreina</h1>
            <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl mx-auto">
                Association loi 1901 fondée en 2007 — les papillons de France
            </p>
        </div>
    </section>

    {{-- Mission --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="px-4 py-2 rounded-full bg-oreina-green/10 text-oreina-green text-sm font-bold">Notre mission</span>
                    <h2 class="text-3xl font-bold text-oreina-dark mt-4 mb-6">Étudier, faire connaître et protéger les Lépidoptères de France</h2>
                    <div class="prose prose-lg text-slate-600">
                        <p>
                            oreina est une association loi 1901 fondée en 2007 par des lépidoptéristes français. Elle a pour but l'étude scientifique des Lépidoptères de France, sa vulgarisation et leur protection.
                        </p></br>
                        <p>
                            En près de vingt ans, l'association est passée d'un projet centré sur le magazine <em>oreina</em> à un acteur scientifique national reconnu. Elle réunit aujourd'hui un réseau de naturalistes, amateurs et professionnels, qui contribuent collectivement à la connaissance, à la qualification et à la diffusion des données sur la diversité et l'écologie des papillons.
                        </p></br>
                        <p>
                            Cette expertise s'inscrit dans des partenariats institutionnels structurants : Office français de la biodiversité (OFB), Muséum national d'Histoire naturelle (MNHN), PatriNat, Réserves naturelles de France (RNF), et dans le cadre du dispositif européen de suivi des pollinisateurs (EU-PoMS), où oreina est référent national pour les Lépidoptères nocturnes.
                        </p>
                    </div>
                </div>
                <div class="card p-0 overflow-hidden aspect-square flex items-center justify-center bg-gradient-to-br from-oreina-green/10 to-oreina-turquoise/10">
                    <img src="/images/about-mission.jpg" alt="Lépidoptère observé sur le terrain" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<i data-lucide=&quot;bug&quot; style=&quot;width:128px;height:128px;color:var(--color-oreina-green);opacity:0.3&quot;></i>';if(window.lucide)window.lucide.createIcons();">
                </div>
            </div>
        </div>
    </section>

    {{-- Values --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="px-4 py-2 rounded-full bg-oreina-turquoise/10 text-oreina-turquoise text-sm font-bold">Nos valeurs</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Ce qui nous guide</h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="card p-8 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-oreina-green to-oreina-teal rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="lightbulb" style="width:32px;height:32px;color:white"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Expertise scientifique</h3>
                    <p class="text-slate-600 text-sm">Rigueur taxonomique, données qualifiées, méthodes documentées et publications référencées.</p>
                </div>

                <div class="card p-8 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-oreina-turquoise to-oreina-blue rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="users" style="width:32px;height:32px;color:white"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Réseau et partage</h3>
                    <p class="text-slate-600 text-sm">Une communauté de naturalistes, amateurs et spécialistes, qui mettent en commun observations et savoir-faire.</p>
                </div>

                <div class="card p-8 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-oreina-yellow to-oreina-coral rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="book-open" style="width:32px;height:32px;color:white"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Vulgarisation</h3>
                    <p class="text-slate-600 text-sm">Diffusion des connaissances par nos publications, en libre accès pour la revue scientifique <em>Chersotis</em>.</p>
                </div>

                <div class="card p-8 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-oreina-beige to-slate-300 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="globe-2" style="width:32px;height:32px;color:var(--color-oreina-dark)"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Conservation</h3>
                    <p class="text-slate-600 text-sm">Contribution à l'évaluation de l'état des populations et appui aux gestionnaires d'espaces naturels.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Activities --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="px-4 py-2 rounded-full bg-oreina-yellow/20 text-oreina-dark text-sm font-bold">Nos axes de travail</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Quatre piliers stratégiques</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">Le projet associatif d'oreina structure son action autour de quatre axes complémentaires.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="card p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-oreina-green to-oreina-teal rounded-2xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="microscope" style="width:28px;height:28px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-oreina-dark mb-3">Une expertise sur les Lépidoptères</h3>
                            <p class="text-slate-600">
                                Développement et maintenance de référentiels nationaux : taxonomie (TAXREF), bibliothèque de séquences moléculaires (SEQREF), traits de vie (BDC), critères d'identification (IDENT) et qualification de la donnée (QUALIF). Validation experte des observations via la plateforme <em>Artemisiae</em>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-oreina-turquoise to-oreina-blue rounded-2xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="leaf" style="width:28px;height:28px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-oreina-dark mb-3">Une expertise au service de la nature</h3>
                            <p class="text-slate-600">
                                Contribution à l'évaluation de l'état de conservation (Listes rouges, Rhopalocères et Zygaenidae), participation au dispositif EU-PoMS de suivi des pollinisateurs, appui aux gestionnaires d'espaces naturels et participation aux politiques publiques nationales.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-oreina-yellow to-oreina-coral rounded-2xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="graduation-cap" style="width:28px;height:28px;color:white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-oreina-dark mb-3">Transmission des savoirs</h3>
                            <p class="text-slate-600">
                                Deux publications complémentaires depuis 2026 : <em>Lepis</em>, bulletin trimestriel de la vie associative et naturaliste, et <em>Chersotis</em>, revue scientifique en accès ouvert publiée en flux continu. Formations thématiques au sein des groupes de travail (validateurs <em>Artemisiae</em>, Zygènes, etc.).
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-oreina-beige to-slate-400 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="users-round" style="width:28px;height:28px;color:var(--color-oreina-dark)"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-oreina-dark mb-3">Une identité associative</h3>
                            <p class="text-slate-600">
                                Rencontres annuelles, groupes de travail thématiques, animation territoriale par les adhérents. Un fonctionnement bénévole soutenu, depuis 2025, par une coordinatrice salariée du réseau scientifique.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Partners --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="px-4 py-2 rounded-full bg-oreina-green/10 text-oreina-green text-sm font-bold">Partenaires institutionnels</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Conventions et collaborations</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">oreina conduit ses projets dans le cadre de conventions pluriannuelles avec des établissements publics et au sein de consortiums scientifiques nationaux et européens.</p>
            </div>

            <div class="flex flex-wrap justify-center items-center gap-8">
                <div class="w-32 h-20 bg-slate-100 rounded-xl flex items-center justify-center">
                    <span class="text-slate-400 font-bold text-sm">OFB</span>
                </div>
                <div class="w-32 h-20 bg-slate-100 rounded-xl flex items-center justify-center">
                    <span class="text-slate-400 font-bold text-sm">MNHN</span>
                </div>
                <div class="w-32 h-20 bg-slate-100 rounded-xl flex items-center justify-center">
                    <span class="text-slate-400 font-bold text-sm">PatriNat</span>
                </div>
                <div class="w-32 h-20 bg-slate-100 rounded-xl flex items-center justify-center">
                    <span class="text-slate-400 font-bold text-sm">RNF</span>
                </div>
                <div class="w-32 h-20 bg-slate-100 rounded-xl flex items-center justify-center">
                    <span class="text-slate-400 font-bold text-sm">OPIE</span>
                </div>
                <div class="w-32 h-20 bg-slate-100 rounded-xl flex items-center justify-center">
                    <span class="text-slate-400 font-bold text-sm">EU-PoMS</span>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="stats-banner text-center">
                <h2 class="text-2xl font-bold mb-4">Rejoindre oreina</h2>
                <p class="text-white/90 mb-8 max-w-2xl mx-auto">
                    Naturalistes débutants, amateurs aguerris, spécialistes ou structures partenaires : adhérer à oreina, c'est rejoindre un réseau actif de contributeurs à la connaissance des Lépidoptères de France.
                </p>
                <a href="{{ route('hub.membership') }}" class="btn btn-primary">
                    <i class="icon icon-sage" data-lucide="heart-handshake"></i>
                    Devenir membre
                </a>
            </div>
        </div>
    </section>
@endsection