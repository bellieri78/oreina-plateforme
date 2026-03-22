@extends('layouts.journal')

@section('title', 'Recherche : ' . $query)
@section('meta_description', 'Résultats de recherche pour "' . $query . '" dans la revue OREINA.')

@section('content')
    <div class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            {{-- Header --}}
            <div class="mb-8 sm:mb-12">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 rounded-2xl bg-oreina-turquoise/10">
                        <svg class="w-7 h-7 text-oreina-turquoise" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-oreina-dark">Recherche</h1>
                        <p class="text-slate-600 mt-1">
                            {{ $articles->total() }} résultat(s) pour « <span class="font-semibold">{{ $query }}</span> »
                        </p>
                    </div>
                </div>

                {{-- Search form --}}
                <form action="{{ route('journal.search') }}" method="GET" class="mt-6">
                    <div class="flex gap-3 max-w-xl">
                        <input
                            type="text"
                            name="q"
                            value="{{ $query }}"
                            placeholder="Rechercher des articles..."
                            class="flex-1 px-4 py-3 rounded-xl border border-oreina-beige/60 focus:ring-2 focus:ring-oreina-turquoise focus:border-transparent bg-white"
                        >
                        <button type="submit" class="btn-turquoise px-6">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            @if($articles->count() > 0)
                {{-- Results --}}
                <div class="space-y-6">
                    @foreach($articles as $article)
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

                                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold mb-2 sm:mb-3 leading-tight text-oreina-dark group-hover:text-oreina-turquoise transition">
                                    <a href="{{ route('journal.articles.show', $article) }}">
                                        {{ $article->title }}
                                    </a>
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
                                    @foreach(array_slice($article->keywords, 0, 4) as $keyword)
                                    <span class="px-2 sm:px-3 py-1 bg-slate-100 text-slate-600 text-xs font-medium rounded-lg
                                        @if(stripos($keyword, $query ?? '') !== false) bg-oreina-turquoise/20 text-oreina-teal @endif">
                                        {{ $keyword }}
                                    </span>
                                    @endforeach
                                </div>
                                @endif

                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 pt-3 sm:pt-4 border-t border-oreina-beige/50">
                                    @if($article->doi)
                                    <p class="text-xs sm:text-sm text-slate-500 font-mono break-all">DOI: {{ $article->doi }}</p>
                                    @else
                                    <p class="text-xs sm:text-sm text-slate-500">{{ $article->journalIssue?->full_reference ?? '' }}</p>
                                    @endif
                                    <a href="{{ route('journal.articles.show', $article) }}" class="flex items-center justify-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-oreina-turquoise text-white rounded-lg hover:shadow-md transition-all font-semibold text-xs sm:text-sm">
                                        Lire l'article
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <polyline points="9 18 15 12 9 6"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $articles->appends(['q' => $query])->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-2xl border border-oreina-beige/50">
                    <svg class="w-20 h-20 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-slate-900">Aucun résultat</h3>
                    <p class="text-slate-500 mt-2 mb-6">Aucun article ne correspond à votre recherche.</p>

                    <div class="max-w-md mx-auto text-left bg-slate-50 rounded-xl p-6">
                        <h4 class="font-semibold text-slate-900 mb-3">Suggestions :</h4>
                        <ul class="text-sm text-slate-600 space-y-2">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-oreina-turquoise flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <polyline points="9 18 15 12 9 6"/>
                                </svg>
                                Vérifiez l'orthographe de vos termes
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-oreina-turquoise flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <polyline points="9 18 15 12 9 6"/>
                                </svg>
                                Essayez des termes plus généraux
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-oreina-turquoise flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <polyline points="9 18 15 12 9 6"/>
                                </svg>
                                Utilisez des noms scientifiques
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('journal.articles.index') }}" class="btn-secondary">
                            Parcourir tous les articles
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
