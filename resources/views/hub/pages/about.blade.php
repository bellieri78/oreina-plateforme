@extends('layouts.hub')

@section('title', 'À propos')
@section('meta_description', 'Découvrez OREINA, association dédiée à l\'étude et à la protection des Lépidoptères de France depuis plus de 20 ans.')

@section('content')
    {{-- Header --}}
    <section class="pt-28 pb-12 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="icon-box bg-oreina-turquoise text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4M12 8h.01"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-oreina-dark">À propos d'OREINA</h1>
                    <p class="text-slate-500 mt-1">Une association passionnée depuis 2007</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Mission --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="px-4 py-2 rounded-full bg-oreina-green/10 text-oreina-green text-sm font-bold">Notre mission</span>
                    <h2 class="text-3xl font-bold text-oreina-dark mt-4 mb-6">Étudier et protéger les papillons de France</h2>
                    <div class="prose prose-lg text-slate-600">
                        <p>
                            OREINA est une association loi 1901 fondée par des passionnés de Lépidoptères. Notre mission est de promouvoir l'étude, la connaissance et la protection des papillons de France.
                        </p>
                        <p>
                            Nous rassemblons amateurs éclairés et spécialistes autour d'une passion commune : les papillons. À travers nos publications, nos sorties terrain et nos programmes de recherche, nous contribuons à une meilleure connaissance de ce groupe fascinant d'insectes.
                        </p>
                        <p>
                            Face au déclin de nombreuses espèces, OREINA s'engage également dans des actions de conservation et de sensibilisation du grand public.
                        </p>
                    </div>
                </div>
                <div class="card p-0 overflow-hidden aspect-square flex items-center justify-center bg-gradient-to-br from-oreina-green/10 to-oreina-turquoise/10">
                    <img src="/images/about-mission.jpg" alt="Papillon observé" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<svg class=\'w-32 h-32 text-oreina-green/30\' fill=\'currentColor\' viewBox=\'0 0 24 24\'><path d=\'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z\'/></svg>'">
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
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Science</h3>
                    <p class="text-slate-600 text-sm">Rigueur scientifique dans nos publications et nos travaux de recherche.</p>
                </div>

                <div class="card p-8 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-oreina-turquoise to-oreina-blue rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Passion</h3>
                    <p class="text-slate-600 text-sm">L'amour des papillons et de la nature guide toutes nos actions.</p>
                </div>

                <div class="card p-8 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-oreina-yellow to-oreina-coral rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Partage</h3>
                    <p class="text-slate-600 text-sm">Transmission des connaissances et convivialité entre membres.</p>
                </div>

                <div class="card p-8 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-oreina-beige to-slate-300 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-oreina-dark" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Conservation</h3>
                    <p class="text-slate-600 text-sm">Protection des espèces et de leurs habitats naturels.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Activities --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="px-4 py-2 rounded-full bg-oreina-yellow/20 text-oreina-dark text-sm font-bold">Activités</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Ce que nous faisons</h2>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="card p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-oreina-green to-oreina-teal rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-oreina-dark mb-3">Publication scientifique</h3>
                            <p class="text-slate-600">
                                La revue OREINA, publiée 4 fois par an, est une publication de référence sur les Lépidoptères de France. Elle accueille des articles de synthèse, des notes faunistiques et des actualités entomologiques.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-oreina-turquoise to-oreina-blue rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-oreina-dark mb-3">Sorties terrain</h3>
                            <p class="text-slate-600">
                                Tout au long de l'année, nous organisons des sorties d'observation dans différentes régions de France. Ces moments de partage permettent de découvrir la diversité des papillons français.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-oreina-yellow to-oreina-coral rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-oreina-dark mb-3">Atlas et inventaires</h3>
                            <p class="text-slate-600">
                                OREINA contribue aux programmes nationaux d'inventaire et de suivi des populations de papillons. Ces données sont essentielles pour la conservation des espèces.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-oreina-beige to-slate-400 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-oreina-dark" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-oreina-dark mb-3">Conférences et formations</h3>
                            <p class="text-slate-600">
                                Des conférences et des ateliers sont régulièrement organisés pour approfondir les connaissances sur les Lépidoptères : identification, élevage, conservation...
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
                <span class="px-4 py-2 rounded-full bg-oreina-green/10 text-oreina-green text-sm font-bold">Partenaires</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Ils nous font confiance</h2>
            </div>

            <div class="flex flex-wrap justify-center items-center gap-8">
                <div class="w-32 h-20 bg-slate-100 rounded-xl flex items-center justify-center">
                    <span class="text-slate-400 font-bold text-sm">OFB</span>
                </div>
                <div class="w-32 h-20 bg-slate-100 rounded-xl flex items-center justify-center">
                    <span class="text-slate-400 font-bold text-sm">PATRINAT</span>
                </div>
                <div class="w-32 h-20 bg-slate-100 rounded-xl flex items-center justify-center">
                    <span class="text-slate-400 font-bold text-sm">MNHN</span>
                </div>
                <div class="w-32 h-20 bg-slate-100 rounded-xl flex items-center justify-center">
                    <span class="text-slate-400 font-bold text-sm">INPN</span>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="stats-banner text-center">
                <h2 class="text-2xl font-bold mb-4">Rejoignez l'aventure</h2>
                <p class="text-white/90 mb-8 max-w-2xl mx-auto">
                    Que vous soyez débutant ou expert, rejoignez notre communauté de passionnés et contribuez à la connaissance des papillons de France.
                </p>
                <a href="{{ route('hub.membership') }}" class="inline-flex items-center gap-2 bg-white text-oreina-teal px-8 py-4 rounded-2xl font-bold hover:shadow-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    Devenir membre
                </a>
            </div>
        </div>
    </section>
@endsection
