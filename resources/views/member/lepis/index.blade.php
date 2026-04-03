@extends('layouts.member')

@section('title', 'Lepis - Bulletins')
@section('page-title', 'Lepis')
@section('page-subtitle', 'Bulletins trimestriels des adhérents')

@section('content')
<div class="space-y-6">
    {{-- Header action --}}
    <div class="flex justify-end">
        <a href="{{ route('member.lepis.suggest') }}" class="btn btn-primary text-xs">
            <i data-lucide="plus"></i>
            Suggerer un article
        </a>
    </div>

    {{-- Bulletins Grid --}}
    @if($bulletins->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($bulletins as $bulletin)
            <div class="card panel">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <div class="text-xs mb-0.5" style="color:var(--muted)">N{{ $bulletin->issue_number }}</div>
                        <h3 class="font-bold text-sm" style="color:var(--forest)">{{ $bulletin->title ?? 'Lepis n' . $bulletin->issue_number }}</h3>
                    </div>
                </div>
                <div class="text-xs mb-3" style="color:var(--muted)">
                    {{ $bulletin->quarter_label }} {{ $bulletin->year }}
                </div>
                @if($bulletin->pdf_path)
                    <a href="{{ route('member.lepis.download', $bulletin) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-link hover:underline">
                        <i data-lucide="download" style="width:16px;height:16px"></i>
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
        <div class="card panel text-center py-8">
            <i data-lucide="newspaper" class="mx-auto mb-3" style="width:40px;height:40px;display:block;color:var(--border)"></i>
            <p class="text-sm" style="color:var(--muted)">Aucun bulletin disponible pour le moment</p>
        </div>
    @endif
</div>
@endsection
