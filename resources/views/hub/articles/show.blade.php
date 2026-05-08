@extends('layouts.hub')

@section('title', $article->title)
@section('meta_description', $article->summary)

@section('content')
    {{-- Breadcrumb --}}
    <section class="pt-24 pb-4 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center text-sm text-slate-500">
                <a href="{{ route('hub.home') }}" class="hover:text-oreina-green transition">Accueil</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
                <a href="{{ route('hub.articles.index') }}" class="hover:text-oreina-green transition">Actualités</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
                <span class="text-oreina-dark font-medium truncate max-w-xs">{{ $article->title }}</span>
            </nav>
        </div>
    </section>

    {{-- Article Content --}}
    <article class="py-8 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border-2 border-oreina-beige/30 overflow-hidden">
                {{-- Featured Image --}}
                @if($article->featured_image)
                <figure class="aspect-video">
                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                </figure>
                @endif

                <div class="p-8 lg:p-12">
                    {{-- Header --}}
                    <header class="mb-8">
                        <div class="flex flex-wrap items-center gap-3 mb-6">
                            <span class="px-3 py-1.5 text-xs font-bold rounded-lg bg-oreina-green/10 text-oreina-green">
                                {{ ucfirst($article->category) }}
                            </span>
                            <div class="flex items-center gap-2 text-sm text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                    <line x1="16" x2="16" y1="2" y2="6"/>
                                    <line x1="8" x2="8" y1="2" y2="6"/>
                                    <line x1="3" x2="21" y1="10" y2="10"/>
                                </svg>
                                <span>{{ $article->published_at->translatedFormat('d F Y') }}</span>
                            </div>
                        </div>

                        <h1 class="text-3xl lg:text-4xl font-bold text-oreina-dark mb-6 leading-tight">
                            {{ $article->title }}
                        </h1>

                        <p class="text-xl text-slate-600 leading-relaxed">
                            {{ $article->summary }}
                        </p>

                        {{-- Author & Meta --}}
                        <div class="flex items-center justify-between mt-8 pt-8 border-t border-oreina-beige/30">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-oreina-green/10 rounded-full flex items-center justify-center">
                                    <span class="text-oreina-green font-bold text-lg">
                                        {{ substr($article->author->name ?? 'O', 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-semibold text-oreina-dark">{{ $article->author->name ?? 'OREINA' }}</p>
                                    <p class="text-sm text-slate-500">Auteur</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                {{ $article->views_count }} vues
                            </div>
                        </div>
                    </header>

                    {{-- Content --}}
                    <div class="prose prose-lg max-w-none prose-headings:text-oreina-dark prose-headings:font-bold prose-a:text-oreina-green prose-a:no-underline hover:prose-a:underline prose-img:rounded-2xl">
                        {!! $article->content !!}
                    </div>

                    {{-- Document joint --}}
                    @if($article->document_path)
                    <div class="mt-10 p-6 rounded-2xl bg-slate-50 border border-oreina-beige/40 flex flex-wrap items-center gap-4">
                        <div class="pub-card-icon blue flex-shrink-0">
                            <i class="icon icon-blue" data-lucide="file-text"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Document joint</p>
                            <p class="font-bold text-oreina-dark truncate">{{ $article->document_name ?? basename($article->document_path) }}</p>
                        </div>
                        <a href="{{ Storage::url($article->document_path) }}" target="_blank" rel="noopener" class="btn btn-secondary">
                            <i class="icon icon-blue" data-lucide="download"></i>
                            Télécharger
                        </a>
                    </div>
                    @endif

                    {{-- Share --}}
                    <div class="mt-12 pt-8 border-t border-oreina-beige/30">
                        <p class="text-sm font-semibold text-oreina-dark mb-4">Partager cet article</p>
                        <div class="flex gap-3">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener" class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center hover:bg-oreina-green hover:text-white transition text-slate-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5h-4.33C10.24.5,9.5,3.44,9.5,5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4Z"/>
                                </svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}" target="_blank" rel="noopener" class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center hover:bg-oreina-green hover:text-white transition text-slate-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($article->title) }}" target="_blank" rel="noopener" class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center hover:bg-oreina-green hover:text-white transition text-slate-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                            <a href="mailto:?subject={{ urlencode($article->title) }}&body={{ urlencode(request()->url()) }}" class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center hover:bg-oreina-green hover:text-white transition text-slate-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>

    {{-- Related Articles --}}
    @if($relatedArticles->count() > 0)
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-oreina-dark mb-8">Articles similaires</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($relatedArticles as $related)
                <article class="article-card group">
                    <div class="h-48 overflow-hidden bg-slate-200">
                        @if($related->featured_image)
                            <img src="{{ Storage::url($related->featured_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-oreina-green/10 flex items-center justify-center">
                                <svg class="w-12 h-12 text-oreina-green/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <span class="text-xs font-semibold text-slate-500">{{ $related->published_at->format('d/m/Y') }}</span>
                        <h3 class="font-bold text-oreina-dark mt-2 group-hover:text-oreina-green transition line-clamp-2">
                            <a href="{{ route('hub.articles.show', $related) }}">
                                {{ $related->title }}
                            </a>
                        </h3>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Back link --}}
    <section class="py-8 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('hub.articles.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-oreina-green transition font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
                Retour aux actualités
            </a>
        </div>
    </section>
@endsection
