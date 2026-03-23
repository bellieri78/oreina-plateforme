@extends('layouts.member')

@section('title', 'La revue OREINA')
@section('subtitle', 'Accédez aux numéros de la revue')

@section('content')
<div class="space-y-6">
    {{-- Access info --}}
    @if(!$isCurrentMember)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="text-sm text-amber-700">
                <p class="font-medium mb-1">Accès limité</p>
                <p>Le téléchargement des numéros de la revue est réservé aux adhérents à jour de cotisation. <a href="{{ route('hub.membership') }}" class="underline font-medium">Adhérez maintenant</a> pour accéder à tous les numéros.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Issues grid --}}
    <div class="member-card">
        <div class="member-card-header">
            <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h3 class="member-card-title">Numéros disponibles</h3>
        </div>

        @if($issues->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($issues as $issue)
                <div class="border border-gray-200 rounded-xl overflow-hidden hover:border-oreina-green hover:shadow-md transition group">
                    {{-- Cover --}}
                    <div class="aspect-[3/4] bg-gradient-to-br from-oreina-turquoise to-oreina-teal flex items-center justify-center relative">
                        @if($issue->cover_image)
                            <img src="{{ Storage::url($issue->cover_image) }}" alt="{{ $issue->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-center text-white p-4">
                                <div class="text-3xl font-bold mb-1">OREINA</div>
                                <div class="text-sm opacity-75">Vol. {{ $issue->volume }} - N°{{ $issue->issue_number }}</div>
                            </div>
                        @endif

                        @if(!$isCurrentMember)
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <svg class="w-12 h-12 text-white/75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-4">
                        <div class="text-sm text-gray-500 mb-1">
                            Volume {{ $issue->volume }} - Numéro {{ $issue->issue_number }}
                        </div>
                        <h4 class="font-semibold text-oreina-dark mb-2 line-clamp-2">
                            {{ $issue->title ?? 'OREINA' }}
                        </h4>
                        <div class="text-xs text-gray-400 mb-3">
                            {{ $issue->publication_date?->translatedFormat('F Y') }}
                        </div>

                        @if($isCurrentMember && $issue->pdf_file)
                            <a href="{{ route('member.journal.download', $issue) }}" class="btn-member w-full justify-center text-sm py-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Télécharger PDF
                            </a>
                        @elseif(!$isCurrentMember)
                            <span class="block text-center text-sm text-gray-400 py-2">
                                Réservé aux adhérents
                            </span>
                        @else
                            <span class="block text-center text-sm text-gray-400 py-2">
                                PDF non disponible
                            </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($issues->hasPages())
            <div class="mt-6">
                {{ $issues->links() }}
            </div>
            @endif
        @else
            <div class="text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p>Aucun numéro disponible pour le moment</p>
            </div>
        @endif
    </div>

    {{-- Link to public journal --}}
    <div class="text-center">
        <a href="{{ route('journal.home') }}" class="text-oreina-green hover:underline inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            Consulter les articles en accès libre sur le site de la revue
        </a>
    </div>
</div>
@endsection
