@extends('layouts.hub')

@section('title', "Lepis n°{$bulletin->issue_number} — {$bulletin->title}")

@section('content')
@php
    $user = auth()->user();
    $member = $user?->member;
    $isCurrentMember = (bool) ($member?->isCurrentMember() ?? false);
    $canDownload = $bulletin->isPublic() || ($bulletin->isInMembersPhase() && $isCurrentMember);
@endphp

<div class="max-w-4xl mx-auto py-12 px-4">
    <div class="mb-6">
        <a href="{{ route('hub.lepis.bulletins.index') }}" class="text-sm text-oreina-green hover:underline">← Tous les numéros</a>
    </div>

    <article class="bg-white rounded-lg shadow overflow-hidden">
        @if ($bulletin->cover_image)
            <img src="{{ Storage::url($bulletin->cover_image) }}" alt="Couverture Lepis n°{{ $bulletin->issue_number }}" class="w-full h-64 object-cover">
        @endif

        <div class="p-8">
            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500 mb-3">
                <span>Lepis n°{{ $bulletin->issue_number }}</span>
                <span>•</span>
                <span>{{ $bulletin->quarter_label }} {{ $bulletin->year }}</span>
                @if ($bulletin->isInMembersPhase())
                    <span class="ml-auto px-2 py-0.5 bg-amber-100 text-amber-800 rounded text-xs">Réservé adhérents à jour</span>
                @endif
            </div>

            <h1 class="text-3xl font-bold text-oreina-dark mb-6">{{ $bulletin->title }}</h1>

            @if ($bulletin->summary)
                <div class="prose max-w-none mb-8">
                    {!! \Illuminate\Support\Str::markdown($bulletin->summary) !!}
                </div>
            @endif

            <div class="flex flex-wrap gap-3 border-t pt-6">
                @if ($canDownload)
                    <a href="{{ route('hub.lepis.bulletins.download', $bulletin) }}"
                       class="inline-flex items-center px-5 py-2 bg-oreina-green text-white rounded hover:bg-oreina-dark">
                        Télécharger le PDF
                    </a>
                @elseif (! $user)
                    <a href="{{ route('hub.login') }}" class="px-5 py-2 bg-oreina-green text-white rounded hover:bg-oreina-dark">Se connecter</a>
                    <a href="{{ route('hub.membership') }}" class="px-5 py-2 bg-oreina-beige text-oreina-dark rounded hover:bg-oreina-light">Adhérer à OREINA</a>
                @else
                    <a href="{{ route('hub.membership') }}" class="px-5 py-2 bg-amber-500 text-white rounded hover:bg-amber-600">Renouveler ma cotisation</a>
                @endif
            </div>
        </div>
    </article>

    <nav class="mt-8 flex justify-between text-sm">
        @if ($previous)
            <a href="{{ route('hub.lepis.bulletins.show', $previous) }}" class="text-oreina-green hover:underline">
                ← n°{{ $previous->issue_number }} ({{ $previous->quarter }} {{ $previous->year }})
            </a>
        @else
            <span></span>
        @endif

        @if ($next)
            <a href="{{ route('hub.lepis.bulletins.show', $next) }}" class="text-oreina-green hover:underline">
                n°{{ $next->issue_number }} ({{ $next->quarter }} {{ $next->year }}) →
            </a>
        @endif
    </nav>
</div>
@endsection
