@extends('layouts.member')

@section('title', 'La revue OREINA')
@section('page-title', 'La revue')
@section('page-subtitle', 'Publications scientifiques du réseau')

@section('content')
<div class="space-y-6">
    {{-- Access info --}}
    @if(!$isCurrentMember)
    <div class="rounded-xl p-4" style="background:var(--surface-amber); border:1px solid var(--border)">
        <div class="flex gap-3">
            <i data-lucide="alert-circle" class="flex-shrink-0 mt-0.5" style="color:var(--amber)"></i>
            <div class="text-sm" style="color:var(--forest)">
                <p class="font-medium mb-1">Accès limité</p>
                <p>Le téléchargement des numéros de la revue est réservé aux adhérents à jour de cotisation. <a href="{{ route('hub.membership') }}" class="underline font-medium">Adhérez maintenant</a> pour accéder à tous les numéros.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Issues grid --}}
    <div class="card panel">
        <div class="panel-head">
            <div>
                <h2>Numéros disponibles</h2>
            </div>
        </div>

        @if($issues->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($issues as $issue)
                <div class="border rounded-xl overflow-hidden hover:shadow-md transition group" style="border-color:var(--border)">
                    {{-- Cover --}}
                    <div class="aspect-[3/4] flex items-center justify-center relative" style="background:linear-gradient(to bottom right, var(--sage), var(--forest))">
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
                            <i data-lucide="lock" style="width:48px;height:48px;color:rgba(255,255,255,0.75)"></i>
                        </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-4">
                        <div class="text-sm mb-1" style="color:var(--muted)">
                            Volume {{ $issue->volume }} - Numéro {{ $issue->issue_number }}
                        </div>
                        <h4 class="font-semibold mb-2 line-clamp-2" style="color:var(--forest)">
                            {{ $issue->title ?? 'OREINA' }}
                        </h4>
                        <div class="text-xs mb-3" style="color:var(--muted)">
                            {{ $issue->publication_date?->translatedFormat('F Y') }}
                        </div>

                        @if($isCurrentMember && $issue->pdf_file)
                            <a href="{{ route('member.journal.download', $issue) }}" class="btn btn-primary w-full justify-center text-sm py-2">
                                <i data-lucide="download"></i>
                                Télécharger PDF
                            </a>
                        @elseif(!$isCurrentMember)
                            <span class="block text-center text-sm py-2" style="color:var(--muted)">
                                Réservé aux adhérents
                            </span>
                        @else
                            <span class="block text-center text-sm py-2" style="color:var(--muted)">
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
            <div class="text-center py-12" style="color:var(--muted)">
                <i data-lucide="book-open" class="mx-auto mb-4" style="width:64px;height:64px;display:block;color:var(--border)"></i>
                <p>Aucun numéro disponible pour le moment</p>
            </div>
        @endif
    </div>

    {{-- Link to public journal --}}
    <div class="text-center">
        <a href="{{ route('journal.home') }}" class="text-link hover:underline inline-flex items-center gap-2">
            <i data-lucide="external-link"></i>
            Consulter les articles en accès libre sur le site de la revue
        </a>
    </div>
</div>
@endsection
