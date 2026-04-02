@extends('layouts.member')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    {{-- Greeting header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-oreina-dark">
                Bonjour, <span class="text-oreina-green">{{ $member?->first_name ?? $user->name }}</span> !
            </h1>
            <p class="text-sm text-gray-400 mt-0.5">
                {{ now()->translatedFormat('l j F Y') }}
                @if($isCurrentMember)
                    — Bonne saison de terrain !
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($isCurrentMember)
                <span class="status-badge active">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Adhérent à jour
                </span>
            @else
                <span class="status-badge expired">Adhésion expirée</span>
                <a href="{{ route('hub.membership') }}" class="btn-member text-xs">Renouveler</a>
            @endif
        </div>
    </div>

    {{-- GT Placeholder cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="gt-card-placeholder">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="label">Validation ID</div>
            <div class="sub">Bientôt disponible</div>
        </div>
        <div class="gt-card-placeholder">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            <div class="label">SeqRef Barcoding</div>
            <div class="sub">Bientôt disponible</div>
        </div>
        <div class="gt-card-placeholder">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
            <div class="label">Traits de vie</div>
            <div class="sub">Bientôt disponible</div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="member-stat">
            <div class="member-stat-value">{{ $stats['membership_years'] }}</div>
            <div class="member-stat-label">Année(s) adhésion</div>
        </div>
        <div class="member-stat">
            <div class="member-stat-value">{{ number_format($stats['total_donations'], 0, ',', ' ') }} €</div>
            <div class="member-stat-label">Total dons</div>
        </div>
        <div class="member-stat">
            <div class="member-stat-value">{{ $stats['donation_count'] }}</div>
            <div class="member-stat-label">Don(s)</div>
        </div>
        <div class="member-stat" style="border: 2px dashed #e0d8d3; background: transparent;">
            <div class="member-stat-value text-gray-300">—</div>
            <div class="member-stat-label">Observations <span class="text-[10px]">(bientôt)</span></div>
        </div>
    </div>

    {{-- Two columns: feed + donations --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Activity feed --}}
        <div class="member-card">
            <div class="member-card-header">
                <span class="dot" style="background: #2C5F2D;"></span>
                Activité récente
            </div>
            @if($member)
                @livewire('member.activity-feed', ['memberId' => $member->id, 'isCurrentMember' => $isCurrentMember])
            @else
                <p class="text-gray-500 text-center py-6 text-sm">Complétez votre profil pour voir votre activité</p>
            @endif
        </div>

        {{-- Recent donations --}}
        <div class="member-card">
            <div class="member-card-header">
                <span class="dot" style="background: #EDC442;"></span>
                Derniers dons
            </div>
            @if($recentDonations->count() > 0)
                <div class="space-y-2">
                    @foreach($recentDonations as $donation)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <div class="font-semibold text-sm text-oreina-dark">{{ number_format($donation->amount, 2, ',', ' ') }} €</div>
                            <div class="text-xs text-gray-400">{{ $donation->donation_date->format('d/m/Y') }}</div>
                        </div>
                        <a href="{{ route('member.documents.cerfa', $donation) }}" class="text-xs text-oreina-green hover:underline font-medium">
                            Reçu fiscal →
                        </a>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('member.documents') }}" class="block mt-3 text-center text-xs text-oreina-green font-medium hover:underline">
                    Voir tous mes documents
                </a>
            @else
                <p class="text-gray-400 text-center py-6 text-sm">Aucun don enregistré</p>
            @endif
        </div>
    </div>

    {{-- Latest journal issues --}}
    @if($isCurrentMember && $latestIssues->count() > 0)
    <div class="member-card">
        <div class="member-card-header">
            <span class="dot" style="background: #14B8A6;"></span>
            Derniers numéros de la revue
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach($latestIssues as $issue)
            <div class="border border-gray-100 rounded-xl p-3 hover:border-oreina-green/50 transition">
                <div class="text-xs text-gray-400 mb-0.5">Vol. {{ $issue->volume_number }} — N°{{ $issue->issue_number }}</div>
                <div class="font-semibold text-sm text-oreina-dark mb-1">{{ $issue->title ?? 'OREINA' }}</div>
                <div class="text-[10px] text-gray-300 mb-2">{{ $issue->publication_date?->translatedFormat('F Y') }}</div>
                @if($issue->pdf_file)
                <a href="{{ route('member.journal.download', $issue) }}" class="inline-flex items-center gap-1 text-xs text-oreina-green font-medium hover:underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Télécharger
                </a>
                @endif
            </div>
            @endforeach
        </div>
        <a href="{{ route('member.journal') }}" class="block mt-3 text-center text-xs text-oreina-green font-medium hover:underline">
            Voir tous les numéros
        </a>
    </div>
    @endif
</div>
@endsection
