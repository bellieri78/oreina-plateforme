@extends('layouts.journal')

@section('title', 'Archives')
@section('meta_description', 'Archives de la revue OREINA - Consultez tous les numéros publiés.')

@section('content')
    <div style="padding: 36px 0;">
        <div class="container">
            {{-- Header --}}
            <div class="mb-8 sm:mb-12">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 rounded-2xl" style="background:var(--accent-surface)">
                        <i data-lucide="book-open" style="width:28px;height:28px;color:var(--accent)"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold">Archives</h1>
                        <p class="text-slate-600 mt-1">Tous les numéros de la revue OREINA</p>
                    </div>
                </div>
            </div>

            @if($issues->count() > 0)
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($issues as $issue)
                    <article class="card group">
                        {{-- Cover --}}
                        <div class="aspect-[3/4] rounded-2xl mb-4 flex items-center justify-center relative overflow-hidden" style="background:linear-gradient(135deg,var(--accent),#0d5c55)">
                            @if($issue->cover_image)
                                <img src="{{ Storage::url($issue->cover_image) }}" alt="{{ $issue->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="text-center text-white p-4">
                                    <div class="text-6xl font-bold mb-2">{{ $issue->issue_number }}</div>
                                    <div class="text-sm opacity-75">Volume {{ $issue->volume_number }}</div>
                                </div>
                            @endif
                        </div>

                        <h3 class="font-bold mb-2">
                            {{ $issue->title }}
                        </h3>

                        <p class="text-sm text-slate-500 mb-3">
                            {{ $issue->publication_date?->translatedFormat('F Y') }}
                        </p>

                        @if($issue->page_count)
                        <p class="text-xs text-slate-400 mb-4">{{ $issue->page_count }} pages</p>
                        @endif

                        <a href="{{ route('journal.issues.show', $issue) }}" class="text-link inline-flex items-center gap-2 text-sm font-semibold" style="color:var(--accent)">
                            Consulter
                            <i data-lucide="chevron-right" style="width:16px;height:16px"></i>
                        </a>
                    </article>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $issues->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-2xl border border-oreina-beige/50">
                    <i data-lucide="book-open" style="width:80px;height:80px;color:#cbd5e1;margin:0 auto 16px"></i>
                    <h3 class="text-xl font-semibold text-slate-900">Aucun numéro publié</h3>
                    <p class="text-slate-500 mt-2">Les archives seront bientôt disponibles.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
