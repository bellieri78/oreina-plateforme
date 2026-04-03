@extends('layouts.journal')

@section('title', $issue->title)
@section('meta_description', $issue->description)

@section('content')
    <div style="padding: 36px 0;">
        <div class="container">
            {{-- Breadcrumb --}}
            <nav class="flex items-center text-sm text-slate-500 mb-8">
                <a href="{{ route('journal.issues.index') }}" class="hover:underline transition" style="color:inherit">Archives</a>
                <i data-lucide="chevron-right" style="width:16px;height:16px;margin:0 8px"></i>
                <span class="text-slate-900">{{ $issue->title }}</span>
            </nav>

            <div class="grid lg:grid-cols-3 gap-8">
                {{-- Cover & Info --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-3xl p-6 border border-oreina-beige/50 sticky top-24">
                        <div class="aspect-[3/4] rounded-2xl mb-6 flex items-center justify-center" style="background:linear-gradient(135deg,var(--accent),#0d5c55)">
                            @if($issue->cover_image)
                                <img src="{{ Storage::url($issue->cover_image) }}" alt="{{ $issue->title }}" class="w-full h-full object-cover rounded-2xl">
                            @else
                                <div class="text-center text-white">
                                    <div class="text-7xl font-bold mb-2">{{ $issue->issue_number }}</div>
                                    <div class="text-lg opacity-75">Volume {{ $issue->volume_number }}</div>
                                </div>
                            @endif
                        </div>

                        <h1 class="text-2xl font-bold mb-2">{{ $issue->title }}</h1>
                        <p class="text-slate-500 mb-4">{{ $issue->publication_date?->translatedFormat('F Y') }}</p>

                        @if($issue->description)
                        <p class="text-slate-600 text-sm mb-6">{{ $issue->description }}</p>
                        @endif

                        <div class="space-y-3 text-sm">
                            @if($issue->page_count)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pages</span>
                                <span class="font-medium text-slate-900">{{ $issue->page_count }}</span>
                            </div>
                            @endif
                            @if($issue->doi)
                            <div class="flex justify-between">
                                <span class="text-slate-500">DOI</span>
                                <span class="font-mono text-xs text-slate-900">{{ $issue->doi }}</span>
                            </div>
                            @endif
                        </div>

                        @if($issue->pdf_file)
                        <a href="{{ Storage::url($issue->pdf_file) }}" class="btn btn-primary w-full mt-6 justify-center" target="_blank">
                            <i data-lucide="download"></i>
                            Télécharger le PDF
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Articles --}}
                <div class="lg:col-span-2">
                    <h2 class="text-xl font-bold mb-6">Sommaire</h2>

                    @if($articles->count() > 0)
                        <div class="space-y-4">
                            @foreach($articles as $article)
                            <article class="bg-white rounded-2xl p-6 border border-oreina-beige/50 hover:shadow-lg transition">
                                <h3 class="font-bold mb-2">
                                    <a href="{{ route('journal.articles.show', $article) }}" class="hover:underline" style="color:inherit;--hover:var(--accent)">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-slate-500 mb-2">{{ $article->author?->name }}</p>
                                @if($article->start_page && $article->end_page)
                                <p class="text-xs text-slate-400">pp. {{ $article->start_page }}-{{ $article->end_page }}</p>
                                @endif
                            </article>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-white rounded-2xl border border-oreina-beige/50">
                            <p class="text-slate-500">Aucun article dans ce numéro.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
