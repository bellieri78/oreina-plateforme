@extends('layouts.hub')

@section('title', isset($currentCategory) ? $currentCategory : 'Actualités')
@section('meta_description', 'Découvrez les dernières actualités d\'OREINA : observations, publications, conservation des Lépidoptères de France.')

@section('content')
    {{-- Header --}}
    <section class="pt-28 pb-12 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="icon-box bg-oreina-green text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-oreina-dark">
                        @if(isset($currentCategory))
                            {{ $currentCategory }}
                        @else
                            Actualités
                        @endif
                    </h1>
                    <p class="text-slate-500 mt-1">Suivez l'actualité de l'association et des Lépidoptères de France</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Categories Filter --}}
    <section class="border-b border-oreina-beige/30 bg-white sticky top-20 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex gap-3 py-4 overflow-x-auto">
                <a href="{{ route('hub.articles.index') }}"
                   class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition {{ !isset($category) ? 'bg-oreina-green text-white shadow-lg' : 'bg-oreina-beige/30 text-oreina-dark hover:bg-oreina-beige/50' }}">
                    Tous
                </a>
                @foreach($categories as $slug => $name)
                <a href="{{ route('hub.articles.category', $slug) }}"
                   class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition {{ (isset($category) && $category === $slug) ? 'bg-oreina-green text-white shadow-lg' : 'bg-oreina-beige/30 text-oreina-dark hover:bg-oreina-beige/50' }}">
                    {{ $name }}
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Articles Grid --}}
    <section class="py-12 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($articles->count() > 0)
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($articles as $article)
                    <article class="article-card group">
                        <div class="h-48 overflow-hidden bg-slate-200">
                            @if($article->featured_image)
                                <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-oreina-green/10 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-oreina-green/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-lg bg-oreina-green/10 text-oreina-green">
                                    {{ ucfirst($article->category) }}
                                </span>
                                <span class="text-xs text-slate-500">
                                    {{ $article->published_at->format('d/m/Y') }}
                                </span>
                            </div>
                            <h2 class="text-lg font-bold text-oreina-dark mb-3 group-hover:text-oreina-green transition line-clamp-2">
                                <a href="{{ route('hub.articles.show', $article) }}">
                                    {{ $article->title }}
                                </a>
                            </h2>
                            <p class="text-slate-500 text-sm line-clamp-2 mb-4">
                                {{ $article->summary }}
                            </p>
                            <a href="{{ route('hub.articles.show', $article) }}" class="inline-flex items-center gap-2 font-bold text-sm text-oreina-blue hover:gap-3 transition-all">
                                Lire la suite
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-3xl border-2 border-oreina-beige/30">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-slate-900">Aucun article</h3>
                    <p class="text-slate-500 mt-2">Il n'y a pas encore d'article dans cette catégorie.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
