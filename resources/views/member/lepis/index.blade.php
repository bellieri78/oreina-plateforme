@extends('layouts.member')

@section('title', 'Lepis - Bulletins')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-oreina-dark">Lepis</h1>
            <p class="text-sm text-gray-400 mt-0.5">Bulletin de liaison de l'association</p>
        </div>
        <a href="{{ route('member.lepis.suggest') }}" class="btn-member text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Suggerer un article
        </a>
    </div>

    {{-- Bulletins Grid --}}
    @if($bulletins->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($bulletins as $bulletin)
            <div class="member-card">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">N{{ $bulletin->issue_number }}</div>
                        <h3 class="font-bold text-sm text-oreina-dark">{{ $bulletin->title ?? 'Lepis n' . $bulletin->issue_number }}</h3>
                    </div>
                </div>
                <div class="text-xs text-gray-500 mb-3">
                    {{ $bulletin->quarter_label }} {{ $bulletin->year }}
                </div>
                @if($bulletin->pdf_path)
                    <a href="{{ route('member.lepis.download', $bulletin) }}" class="inline-flex items-center gap-1.5 text-xs text-oreina-green font-medium hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Telecharger le PDF
                    </a>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $bulletins->links() }}
        </div>
    @else
        <div class="member-card text-center py-8">
            <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            <p class="text-gray-400 text-sm">Aucun bulletin disponible pour le moment</p>
        </div>
    @endif
</div>
@endsection
