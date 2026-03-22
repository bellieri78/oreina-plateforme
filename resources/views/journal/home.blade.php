@extends('layouts.journal')

@section('title', 'Accueil')
@section('meta_description', 'Revue OREINA - Accès libre à des articles scientifiques de haute qualité sur les Lépidoptères de France.')

@section('content')
    {{-- Hero Section --}}
    <div class="relative h-[60vh] sm:h-[80vh] lg:h-screen flex items-center justify-center overflow-hidden bg-cover bg-center" style="background-image: url('/images/journal-hero.jpg');">
        <div class="absolute inset-0 journal-hero-overlay"></div>

        <div class="relative z-10 text-white px-4 sm:px-6 lg:px-20 max-w-5xl">
            <div class="badge text-oreina-beige mb-4 sm:mb-8">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                </svg>
                <span class="text-xs sm:text-sm font-semibold text-white">Publication en flux continu</span>
            </div>

            <h1 class="text-3xl sm:text-5xl lg:text-7xl font-bold mb-4 sm:mb-6 leading-tight tracking-tight">
                Revue scientifique<br />OREINA
            </h1>

            <p class="text-lg sm:text-2xl lg:text-3xl mb-4 sm:mb-8 font-medium leading-tight text-oreina-beige">
                Publications sur les Lépidoptères de France
            </p>

            <p class="text-sm sm:text-base lg:text-xl mb-4 sm:mb-6 opacity-95 leading-relaxed max-w-3xl">
                Accès libre à des articles scientifiques de haute qualité, soumis à une relecture par les pairs
                et publiés en flux continu pour une diffusion rapide des découvertes.
            </p>

            <p class="text-xs sm:text-sm lg:text-lg mb-6 sm:mb-12 opacity-90 leading-relaxed max-w-2xl">
                Contribuez à la science en publiant vos travaux ou en explorant les dernières recherches.
            </p>

            <a href="#articles" class="btn-turquoise text-sm sm:text-base lg:text-lg inline-flex items-center gap-2 sm:gap-3">
                <span>Explorer les articles</span>
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </a>
        </div>

        <button onclick="document.getElementById('articles').scrollIntoView({ behavior: 'smooth' })" class="hidden sm:flex absolute bottom-6 sm:bottom-12 left-1/2 -translate-x-1/2 flex-col items-center gap-2 animate-bounce-slow cursor-pointer">
            <span class="text-white text-xs sm:text-sm font-medium opacity-75">Découvrir</span>
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>
    </div>

    {{-- Articles Section --}}
    <div id="articles" class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            {{-- Section header --}}
            <div class="mb-6 sm:mb-8">
                <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                    <div class="p-2 sm:p-3 rounded-2xl bg-oreina-turquoise/10">
                        <svg class="w-5 h-5 sm:w-7 sm:h-7 text-oreina-turquoise" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-oreina-dark">Derniers articles</h2>
                        <p class="text-slate-600 text-xs sm:text-sm lg:text-base mt-1">{{ $recentArticles->count() }} articles récents</p>
                    </div>
                </div>
            </div>

            @if($recentArticles->count() > 0)
                {{-- Articles grid --}}
                <div class="space-y-4 sm:space-y-6">
                    @foreach($recentArticles as $article)
                    <article class="article-card group">
                        <div class="grid md:grid-cols-4 gap-0">
                            <div class="md:col-span-1 h-48 sm:h-64 md:h-auto bg-slate-200 overflow-hidden relative">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent z-10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                @if($article->featured_image)
                                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-oreina-teal/20 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-oreina-teal/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="md:col-span-3 p-5 sm:p-6 lg:p-8">
                                <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                                    <span class="px-2.5 sm:px-3 py-1 sm:py-1.5 text-xs font-bold rounded-lg bg-oreina-turquoise/10 text-oreina-teal">
                                        Article scientifique
                                    </span>
                                    <div class="flex items-center gap-1.5 sm:gap-2 text-slate-500 text-xs sm:text-sm">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                        </svg>
                                        <span>{{ $article->published_at?->format('d/m/Y') ?? 'Non publié' }}</span>
                                    </div>
                                </div>

                                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold mb-2 sm:mb-3 leading-tight text-oreina-dark">
                                    {{ $article->title }}
                                </h3>

                                <p class="text-slate-600 text-sm sm:text-base mb-3 sm:mb-4 leading-relaxed line-clamp-2">
                                    {{ $article->abstract }}
                                </p>

                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-3 sm:mb-4">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-xs sm:text-sm font-medium text-slate-700">{{ $article->author?->name ?? 'Auteur inconnu' }}</span>
                                </div>

                                @if($article->keywords && is_array($article->keywords) && count($article->keywords) > 0)
                                <div class="flex flex-wrap gap-1.5 sm:gap-2 mb-4 sm:mb-5">
                                    @foreach(array_slice($article->keywords, 0, 3) as $keyword)
                                    <span class="px-2 sm:px-3 py-1 bg-slate-100 text-slate-600 text-xs font-medium rounded-lg">{{ $keyword }}</span>
                                    @endforeach
                                </div>
                                @endif

                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 pt-3 sm:pt-4 border-t border-oreina-beige/50">
                                    @if($article->doi)
                                    <p class="text-xs sm:text-sm text-slate-500 font-mono break-all">DOI: {{ $article->doi }}</p>
                                    @else
                                    <p class="text-xs sm:text-sm text-slate-500">{{ $article->journalIssue?->full_reference ?? '' }}</p>
                                    @endif
                                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">
                                        <a href="{{ route('journal.articles.show', $article) }}" class="flex items-center justify-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-oreina-turquoise text-white rounded-lg hover:shadow-md transition-all font-semibold text-xs sm:text-sm">
                                            Lire l'article
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <polyline points="9 18 15 12 9 6"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <div class="mt-8 sm:mt-12 flex justify-center">
                    <a href="{{ route('journal.articles.index') }}" class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-3.5 bg-white text-slate-700 rounded-xl hover:bg-slate-50 transition-all font-semibold border border-oreina-beige/60 hover:shadow-md text-sm sm:text-base text-center">
                        Voir tous les articles
                    </a>
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-2xl border border-oreina-beige/50">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-slate-900">Aucun article publié</h3>
                    <p class="text-slate-500 mt-1">Les premiers articles seront bientôt disponibles.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- CTA Submit --}}
    <div class="py-12 sm:py-16 px-4 sm:px-6 lg:px-12 bg-white">
        <div class="max-w-4xl mx-auto text-center">
            <div class="p-3 rounded-2xl bg-oreina-turquoise/10 inline-flex mb-6">
                <svg class="w-8 h-8 text-oreina-turquoise" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/>
                </svg>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-oreina-dark mb-4">Publiez vos recherches</h2>
            <p class="text-slate-600 mb-8 max-w-2xl mx-auto">
                La revue OREINA publie des articles originaux sur la systématique, l'écologie, la biogéographie et la conservation des Lépidoptères. Soumettez votre manuscrit pour une relecture par les pairs.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('journal.submit') }}" class="btn-turquoise">
                    Soumettre un article
                </a>
                <a href="{{ route('journal.authors') }}" class="btn-secondary">
                    Instructions aux auteurs
                </a>
            </div>
        </div>
    </div>
@endsection
