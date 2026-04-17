@extends('layouts.journal')

@section('title', 'Recherche : ' . $query)
@section('meta_description', 'Résultats de recherche pour "' . $query . '" dans la revue OREINA.')

@section('content')
    <div style="padding: 36px 0;">
        <div class="container">
            {{-- Header --}}
            <div class="mb-8 sm:mb-12">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 rounded-2xl" style="background:var(--accent-surface)">
                        <i data-lucide="search" style="width:28px;height:28px;color:var(--accent)"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold">Recherche</h1>
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
                            class="flex-1 px-4 py-3 rounded-xl border border-oreina-beige/60 focus:ring-2 focus:border-transparent bg-white"
                            style="outline:none;"
                            onfocus="this.style.borderColor='var(--accent)';this.style.boxShadow='0 0 0 4px rgba(15,118,110,0.08)'"
                            onblur="this.style.borderColor='';this.style.boxShadow=''"
                        >
                        <button type="submit" class="btn btn-primary px-6">
                            <i data-lucide="search"></i>
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
                                    <div class="w-full h-full flex items-center justify-center" style="background:var(--accent-surface)">
                                        <i data-lucide="file-text" style="width:64px;height:64px;color:var(--accent);opacity:0.3"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="md:col-span-3 p-5 sm:p-6 lg:p-8">
                                <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                                    <span class="px-2.5 sm:px-3 py-1 sm:py-1.5 text-xs font-bold rounded-lg" style="background:var(--accent-surface);color:var(--accent)">
                                        Article scientifique
                                    </span>
                                    <div class="flex items-center gap-1.5 sm:gap-2 text-slate-500 text-xs sm:text-sm">
                                        <i data-lucide="calendar" style="width:14px;height:14px"></i>
                                        <span>{{ $article->published_at?->format('d/m/Y') ?? 'Non publié' }}</span>
                                    </div>
                                </div>

                                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold mb-2 sm:mb-3 leading-tight group-hover:transition" style="">
                                    <a href="{{ route('journal.articles.show', $article) }}" style="color:inherit">
                                        {{ $article->title }}
                                    </a>
                                </h3>

                                <p class="text-slate-600 text-sm sm:text-base mb-3 sm:mb-4 leading-relaxed line-clamp-2">
                                    {{ $article->abstract }}
                                </p>

                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-3 sm:mb-4">
                                    <i data-lucide="user" style="width:14px;height:14px;color:#94a3b8"></i>
                                    <span class="text-xs sm:text-sm font-medium text-slate-700">{{ $article->display_authors ?? $article->author?->name ?? 'Auteur inconnu' }}</span>
                                </div>

                                @if($article->keywords && is_array($article->keywords) && count($article->keywords) > 0)
                                <div class="flex flex-wrap gap-1.5 sm:gap-2 mb-4 sm:mb-5">
                                    @foreach(array_slice($article->keywords, 0, 4) as $keyword)
                                    <span class="px-2 sm:px-3 py-1 text-xs font-medium rounded-lg
                                        {{ stripos($keyword, $query ?? '') !== false ? '' : 'bg-slate-100 text-slate-600' }}"
                                        @if(stripos($keyword, $query ?? '') !== false) style="background:var(--accent-surface);color:var(--accent)" @endif>
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
                                    <a href="{{ route('journal.articles.show', $article) }}" class="btn btn-primary text-xs sm:text-sm" style="height:36px;padding:0 16px">
                                        Lire l'article
                                        <i data-lucide="chevron-right" style="width:14px;height:14px"></i>
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
                    <i data-lucide="search" style="width:80px;height:80px;color:#cbd5e1;margin:0 auto 16px"></i>
                    <h3 class="text-xl font-semibold text-slate-900">Aucun résultat</h3>
                    <p class="text-slate-500 mt-2 mb-6">Aucun article ne correspond à votre recherche.</p>

                    <div class="max-w-md mx-auto text-left bg-slate-50 rounded-xl p-6">
                        <h4 class="font-semibold text-slate-900 mb-3">Suggestions :</h4>
                        <ul class="text-sm text-slate-600 space-y-2">
                            <li class="flex items-start gap-2">
                                <i data-lucide="chevron-right" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;color:var(--accent)"></i>
                                Vérifiez l'orthographe de vos termes
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="chevron-right" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;color:var(--accent)"></i>
                                Essayez des termes plus généraux
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="chevron-right" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;color:var(--accent)"></i>
                                Utilisez des noms scientifiques
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('journal.articles.index') }}" class="btn btn-secondary">
                            Parcourir tous les articles
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
