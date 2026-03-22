@extends('layouts.journal')

@section('title', 'Archives')
@section('meta_description', 'Archives de la revue OREINA - Consultez tous les numéros publiés.')

@section('content')
    <div class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            {{-- Header --}}
            <div class="mb-8 sm:mb-12">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 rounded-2xl bg-oreina-turquoise/10">
                        <svg class="w-7 h-7 text-oreina-turquoise" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-oreina-dark">Archives</h1>
                        <p class="text-slate-600 mt-1">Tous les numéros de la revue OREINA</p>
                    </div>
                </div>
            </div>

            @if($issues->count() > 0)
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($issues as $issue)
                    <article class="card group">
                        {{-- Cover placeholder --}}
                        <div class="aspect-[3/4] bg-gradient-to-br from-oreina-teal to-oreina-teal-dark rounded-2xl mb-4 flex items-center justify-center relative overflow-hidden">
                            @if($issue->cover_image)
                                <img src="{{ Storage::url($issue->cover_image) }}" alt="{{ $issue->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="text-center text-white p-4">
                                    <div class="text-6xl font-bold mb-2">{{ $issue->issue_number }}</div>
                                    <div class="text-sm opacity-75">Volume {{ $issue->volume_number }}</div>
                                </div>
                            @endif
                        </div>

                        <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-turquoise transition">
                            {{ $issue->title }}
                        </h3>

                        <p class="text-sm text-slate-500 mb-3">
                            {{ $issue->publication_date?->translatedFormat('F Y') }}
                        </p>

                        @if($issue->page_count)
                        <p class="text-xs text-slate-400 mb-4">{{ $issue->page_count }} pages</p>
                        @endif

                        <a href="{{ route('journal.issues.show', $issue) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-oreina-turquoise hover:text-oreina-teal transition">
                            Consulter
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </a>
                    </article>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $issues->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-2xl border border-oreina-beige/50">
                    <svg class="w-20 h-20 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-slate-900">Aucun numéro publié</h3>
                    <p class="text-slate-500 mt-2">Les archives seront bientôt disponibles.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
