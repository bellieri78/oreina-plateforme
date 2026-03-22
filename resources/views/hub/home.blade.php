@extends('layouts.hub')

@section('title', 'Accueil')
@section('meta_description', 'OREINA - Association des Lépidoptères de France. Rejoignez une communauté passionnée au service de la connaissance des papillons.')

@section('content')
    {{-- Hero Section --}}
    <section class="relative h-screen flex items-center justify-center overflow-hidden bg-cover bg-center" style="background-image: url('/images/hero-bg.jpg');">
        <div class="absolute inset-0 hero-overlay"></div>

        <div class="relative z-10 text-white text-center px-4 sm:px-6 lg:px-8 max-w-5xl">
            <div class="badge text-oreina-beige mb-8">
                <svg class="w-4 h-4 text-oreina-green" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
                <span class="text-white text-sm font-semibold">Association loi 1901 depuis 2007</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold mb-6 tracking-tight">
                OREINA
            </h1>

            <p class="text-xl sm:text-2xl lg:text-3xl mb-4 font-medium text-oreina-beige">
                Une communauté passionnée au service des Lépidoptères de France
            </p>

            <p class="text-base sm:text-lg lg:text-xl mb-12 opacity-95 leading-relaxed max-w-3xl mx-auto">
                Rejoignez plus de 300 adhérents qui partagent leurs connaissances, développent des outils et contribuent à la science participative.
            </p>

            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('hub.membership') }}" class="btn-primary text-lg px-8 py-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Rejoindre l'association
                </a>
                <a href="#outils" class="inline-flex items-center gap-2 bg-white/15 text-white px-8 py-4 rounded-2xl font-bold border-2 border-white/30 backdrop-blur hover:bg-white/25 transition text-lg">
                    Découvrir nos outils
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </a>
            </div>
        </div>

        <button onclick="document.getElementById('association').scrollIntoView({ behavior: 'smooth' })" class="hidden sm:flex absolute bottom-12 left-1/2 -translate-x-1/2 flex-col items-center gap-2 animate-bounce-slow cursor-pointer">
            <span class="text-white text-sm font-medium opacity-75">Découvrir</span>
            <svg class="w-6 h-6 text-white opacity-75 rotate-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="m9 18 6-6-6-6"/>
            </svg>
        </button>
    </section>

    {{-- Association Section --}}
    <section id="association" class="py-20 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-oreina-dark mb-4">Une association vivante et engagée</h2>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">
                    OREINA, ce sont avant tout des bénévoles passionnés qui agissent au quotidien pour le partage des connaissances
                </p>
            </div>

            {{-- Two main cards --}}
            <div class="grid md:grid-cols-2 gap-8 mb-8">
                {{-- Adhérer card --}}
                <div class="profile-card bg-oreina-green/5 border-oreina-green/25">
                    <div class="flex items-start gap-6 mb-8">
                        <div class="icon-box bg-oreina-green text-white flex-shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-oreina-dark mb-3">Adhérer à OREINA</h3>
                            <p class="text-slate-500 leading-relaxed">
                                Rejoignez une communauté de naturalistes, chercheurs et passionnés qui font vivre l'association au quotidien. Accédez au bulletin Lepis, aux webinaires et participez aux sorties terrain.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('hub.membership') }}" class="btn-primary w-full justify-center py-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        Devenir adhérent
                    </a>
                </div>

                {{-- Groupes de travail card --}}
                <div class="profile-card bg-oreina-yellow/5 border-oreina-yellow/25">
                    <div class="flex items-start gap-6 mb-8">
                        <div class="icon-box bg-oreina-yellow text-oreina-dark flex-shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-oreina-dark mb-3">Groupes de travail</h3>
                            <p class="text-slate-500 leading-relaxed">
                                Participez activement à l'amélioration des connaissances en rejoignant nos groupes thématiques : taxonomie, écologie, conservation, et bien plus encore.
                            </p>
                        </div>
                    </div>
                    <a href="#" class="inline-flex items-center gap-2 w-full justify-center py-4 bg-oreina-yellow text-oreina-dark rounded-2xl font-bold transition hover:shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                        Rejoindre un GT
                    </a>
                </div>
            </div>

            {{-- Three smaller cards --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Bulletin Lepis --}}
                <div class="card card-alt">
                    <div class="icon-box bg-oreina-blue/15 text-oreina-blue mb-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-oreina-dark mb-3">Bulletin Lepis</h4>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Le bulletin trimestriel des adhérents avec actualités, synthèses scientifiques et contributions des membres</p>
                    <a href="#" class="inline-flex items-center gap-2 font-bold text-sm text-oreina-blue hover:gap-3 transition-all">
                        Dernier numéro
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>

                {{-- Labo Lepido --}}
                <div class="card">
                    <div class="icon-box bg-oreina-coral/15 text-oreina-coral mb-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m22 8-6 4 6 4V8Z"/>
                            <rect width="14" height="12" x="2" y="6" rx="2" ry="2"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-oreina-dark mb-3">Labo Lepido</h4>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Webinaires mensuels de formation à l'identification, replays disponibles pour tous les adhérents</p>
                    <a href="#" class="inline-flex items-center gap-2 font-bold text-sm text-oreina-coral hover:gap-3 transition-all">
                        Voir les replays
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>

                {{-- Événements --}}
                <div class="card card-alt">
                    <div class="icon-box bg-oreina-green/15 text-oreina-green mb-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" x2="16" y1="2" y2="6"/>
                            <line x1="8" x2="8" y1="2" y2="6"/>
                            <line x1="3" x2="21" y1="10" y2="10"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-oreina-dark mb-3">Événements {{ date('Y') }}</h4>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">AG, sorties terrain, ateliers d'identification... Consultez notre agenda associatif complet</p>
                    <a href="{{ route('hub.events.index') }}" class="inline-flex items-center gap-2 font-bold text-sm text-oreina-green hover:gap-3 transition-all">
                        Voir l'agenda
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Outils Section --}}
    <section id="outils" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-oreina-dark mb-4">Nos outils numériques</h2>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">
                    Développés par et pour la communauté, des outils professionnels au service de tous
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Espèces --}}
                <div class="card">
                    <div class="icon-box bg-oreina-yellow/10 text-oreina-yellow mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M8 2v4"/>
                            <path d="M16 2v4"/>
                            <rect width="16" height="16" x="4" y="4" rx="2"/>
                            <path d="M8 10h8"/>
                            <path d="M8 14h8"/>
                        </svg>
                    </div>
                    <div class="text-4xl font-bold text-oreina-dark mb-2 tracking-tight">5,362</div>
                    <div class="text-base font-semibold text-oreina-dark mb-2">Espèces documentées</div>
                    <p class="text-sm text-slate-500 mb-6">Base taxonomique complète des Lépidoptères</p>
                    <a href="#" class="inline-flex items-center gap-2 w-full justify-center py-3 bg-oreina-yellow text-oreina-dark rounded-2xl font-bold hover:shadow-lg transition">
                        Consulter
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>

                {{-- Observations --}}
                <div class="card card-alt">
                    <div class="icon-box bg-oreina-blue/10 text-oreina-blue mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <ellipse cx="12" cy="5" rx="9" ry="3"/>
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                        </svg>
                    </div>
                    <div class="text-4xl font-bold text-oreina-dark mb-2 tracking-tight">2.1M+</div>
                    <div class="text-base font-semibold text-oreina-dark mb-2">Observations</div>
                    <p class="text-sm text-slate-500 mb-6">Portail Artemisiae de données naturalistes</p>
                    <a href="#" class="btn-primary w-full justify-center py-3">
                        Accéder
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>

                {{-- Traits de vie --}}
                <div class="card">
                    <div class="icon-box bg-oreina-green/10 text-oreina-green mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3v18h18"/>
                            <path d="m19 9-5 5-4-4-3 3"/>
                        </svg>
                    </div>
                    <div class="text-4xl font-bold text-oreina-dark mb-2 tracking-tight">25,639</div>
                    <div class="text-base font-semibold text-oreina-dark mb-2">Traits de vie</div>
                    <p class="text-sm text-slate-500 mb-6">Base de connaissances scientifiques</p>
                    <a href="#" class="inline-flex items-center gap-2 w-full justify-center py-3 bg-oreina-green text-white rounded-2xl font-bold hover:shadow-lg transition">
                        Explorer
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>

                {{-- Références --}}
                <div class="card card-alt">
                    <div class="icon-box bg-oreina-coral/10 text-oreina-coral mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m16 6 4 14"/>
                            <path d="M12 6v14"/>
                            <path d="M8 8v12"/>
                            <path d="M4 4v16"/>
                        </svg>
                    </div>
                    <div class="text-4xl font-bold text-oreina-dark mb-2 tracking-tight">23,520</div>
                    <div class="text-base font-semibold text-oreina-dark mb-2">Références</div>
                    <p class="text-sm text-slate-500 mb-6">Sources bibliographiques indexées</p>
                    <a href="#" class="inline-flex items-center gap-2 w-full justify-center py-3 bg-oreina-coral text-white rounded-2xl font-bold hover:shadow-lg transition">
                        Consulter
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Projets Section --}}
    <section id="projets" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-oreina-dark mb-4">Projets scientifiques</h2>
                <p class="text-lg text-slate-500">6 projets majeurs portés par nos bénévoles, soutenus par l'OFB et l'Union Européenne</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- TAXREF --}}
                <div class="card card-alt">
                    <div class="flex justify-between items-start mb-6">
                        <div class="icon-box bg-oreina-blue text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="8" r="6"/>
                                <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                            </svg>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold badge-active">Actif</span>
                    </div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-3">TAXREF</h3>
                    <p class="text-sm text-slate-500 mb-6">Référentiel taxonomique national des Lépidoptères</p>
                    <a href="#" class="inline-flex items-center gap-2 font-bold text-sm text-oreina-blue hover:gap-3 transition-all">
                        En savoir plus
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>

                {{-- SEQREF --}}
                <div class="card">
                    <div class="flex justify-between items-start mb-6">
                        <div class="icon-box bg-oreina-green text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold badge-active">Actif</span>
                    </div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-3">SEQREF</h3>
                    <p class="text-sm text-slate-500 mb-6">Barcoding ADN et référentiel génétique</p>
                    <a href="#" class="inline-flex items-center gap-2 font-bold text-sm text-oreina-green hover:gap-3 transition-all">
                        En savoir plus
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>

                {{-- BDC Artemisiae --}}
                <div class="card card-alt">
                    <div class="flex justify-between items-start mb-6">
                        <div class="icon-box bg-oreina-yellow text-oreina-dark">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 3v18h18"/>
                                <path d="m19 9-5 5-4-4-3 3"/>
                            </svg>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold badge-active">Actif</span>
                    </div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-3">BDC Artemisiae</h3>
                    <p class="text-sm text-slate-500 mb-6">Base de connaissances des traits biologiques</p>
                    <a href="#" class="inline-flex items-center gap-2 font-bold text-sm text-oreina-yellow hover:gap-3 transition-all">
                        En savoir plus
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>

                {{-- IDENT --}}
                <div class="card">
                    <div class="flex justify-between items-start mb-6">
                        <div class="icon-box bg-oreina-blue text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold badge-active">Actif</span>
                    </div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-3">IDENT</h3>
                    <p class="text-sm text-slate-500 mb-6">Outils d'identification et formations</p>
                    <a href="#" class="inline-flex items-center gap-2 font-bold text-sm text-oreina-blue hover:gap-3 transition-all">
                        En savoir plus
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>

                {{-- QUALIF --}}
                <div class="card card-alt">
                    <div class="flex justify-between items-start mb-6">
                        <div class="icon-box bg-oreina-green text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold badge-active">Actif</span>
                    </div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-3">QUALIF</h3>
                    <p class="text-sm text-slate-500 mb-6">Validation et qualification des données</p>
                    <a href="#" class="inline-flex items-center gap-2 font-bold text-sm text-oreina-green hover:gap-3 transition-all">
                        En savoir plus
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>

                {{-- Revue scientifique --}}
                <div class="card">
                    <div class="flex justify-between items-start mb-6">
                        <div class="icon-box bg-oreina-coral text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" x2="8" y1="13" y2="13"/>
                                <line x1="16" x2="8" y1="17" y2="17"/>
                                <polyline points="10 9 9 9 8 9"/>
                            </svg>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold badge-new">Nouveau</span>
                    </div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-3">Revue scientifique</h3>
                    <p class="text-sm text-slate-500 mb-6">Publications avec DOI - Lancement mai 2026</p>
                    <a href="{{ route('journal.home') }}" class="inline-flex items-center gap-2 font-bold text-sm text-oreina-coral hover:gap-3 transition-all">
                        En savoir plus
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Actualités Section --}}
    <section id="actualites" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap justify-between items-start gap-4 mb-16">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-oreina-dark mb-2">Actualités</h2>
                    <p class="text-lg text-slate-500">Dernières nouvelles de la communauté OREINA</p>
                </div>
                <a href="{{ route('hub.articles.index') }}" class="inline-flex items-center gap-2 font-bold text-oreina-blue hover:gap-3 transition-all">
                    Toutes les actus
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </a>
            </div>

            @if($latestArticles->count() > 0)
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($latestArticles->take(3) as $article)
                <article class="article-card group">
                    <div class="h-56 overflow-hidden bg-slate-200">
                        @if($article->featured_image)
                            <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-oreina-green/10 flex items-center justify-center">
                                <svg class="w-16 h-16 text-oreina-green/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-8">
                        <div class="flex items-center gap-2 text-xs font-semibold text-oreina-green mb-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                <line x1="16" x2="16" y1="2" y2="6"/>
                                <line x1="8" x2="8" y1="2" y2="6"/>
                                <line x1="3" x2="21" y1="10" y2="10"/>
                            </svg>
                            <span>{{ $article->published_at->format('d F Y') }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-oreina-dark mb-3 group-hover:text-oreina-green transition">
                            <a href="{{ route('hub.articles.show', $article) }}">
                                {{ $article->title }}
                            </a>
                        </h3>
                        <p class="text-slate-500 text-sm mb-6 line-clamp-2">{{ $article->summary }}</p>
                        <a href="{{ route('hub.articles.show', $article) }}" class="inline-flex items-center gap-2 font-bold text-sm text-oreina-blue hover:gap-3 transition-all">
                            Lire l'article
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
            @else
            <div class="text-center py-16 bg-white rounded-3xl border-2 border-oreina-beige/30">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"/>
                </svg>
                <h3 class="text-xl font-semibold text-slate-900">Aucune actualité</h3>
                <p class="text-slate-500 mt-2">Les premières actualités seront bientôt publiées.</p>
            </div>
            @endif
        </div>
    </section>
@endsection
