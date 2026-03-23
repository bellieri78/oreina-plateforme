@extends('layouts.member')

@section('title', 'Tableau de bord')
@section('subtitle', 'Bienvenue dans votre espace personnel')

@section('content')
<div class="space-y-6">
    {{-- Welcome card --}}
    <div class="member-card bg-gradient-to-r from-oreina-green/10 to-oreina-turquoise/10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-oreina-dark mb-2">
                    Bonjour {{ $member?->first_name ?? $user->name }} !
                </h2>
                @if($isCurrentMember)
                    <p class="text-gray-600">
                        Votre adhésion est valide jusqu'au
                        <span class="font-semibold text-oreina-green">{{ $currentMembership->end_date->format('d/m/Y') }}</span>
                    </p>
                @else
                    <p class="text-gray-600">
                        Vous n'avez pas d'adhésion active.
                        <a href="{{ route('hub.membership') }}" class="text-oreina-green font-semibold hover:underline">Adhérez maintenant</a>
                    </p>
                @endif
            </div>
            @if($isCurrentMember)
                <span class="status-badge active">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Adhérent à jour
                </span>
            @else
                <span class="status-badge expired">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Adhésion expirée
                </span>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="member-stat">
            <div class="member-stat-value">{{ $stats['membership_years'] }}</div>
            <div class="member-stat-label">Année(s) d'adhésion</div>
        </div>
        <div class="member-stat">
            <div class="member-stat-value">{{ number_format($stats['total_donations'], 0, ',', ' ') }} €</div>
            <div class="member-stat-label">Total des dons</div>
        </div>
        <div class="member-stat">
            <div class="member-stat-value">{{ $stats['donation_count'] }}</div>
            <div class="member-stat-label">Don(s) effectué(s)</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Quick actions --}}
        <div class="member-card">
            <div class="member-card-header">
                <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <h3 class="member-card-title">Actions rapides</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('member.profile') }}" class="document-item">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="font-medium text-oreina-dark">Modifier mon profil</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                @if($isCurrentMember)
                <a href="{{ route('member.membership.card') }}" class="document-item">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                            </svg>
                        </div>
                        <span class="font-medium text-oreina-dark">Télécharger ma carte</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </a>
                @endif

                <a href="{{ route('member.documents') }}" class="document-item">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="font-medium text-oreina-dark">Mes documents</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Recent donations --}}
        <div class="member-card">
            <div class="member-card-header">
                <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="member-card-title">Derniers dons</h3>
            </div>
            @if($recentDonations->count() > 0)
                <div class="space-y-3">
                    @foreach($recentDonations as $donation)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <div class="font-medium text-oreina-dark">{{ number_format($donation->amount, 2, ',', ' ') }} €</div>
                            <div class="text-sm text-gray-500">{{ $donation->donation_date->format('d/m/Y') }}</div>
                        </div>
                        <a href="{{ route('member.documents.cerfa', $donation) }}" class="text-sm text-oreina-green hover:underline">
                            Reçu fiscal
                        </a>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('member.documents') }}" class="block mt-4 text-center text-sm text-oreina-green font-medium hover:underline">
                    Voir tous mes documents
                </a>
            @else
                <p class="text-gray-500 text-center py-4">Aucun don enregistré</p>
            @endif
        </div>
    </div>

    {{-- Latest journal issues (for members) --}}
    @if($isCurrentMember && count($latestIssues) > 0)
    <div class="member-card">
        <div class="member-card-header">
            <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h3 class="member-card-title">Derniers numéros de la revue</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($latestIssues as $issue)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-oreina-green transition">
                <div class="text-sm text-gray-500 mb-1">Vol. {{ $issue->volume }} - N°{{ $issue->issue_number }}</div>
                <div class="font-medium text-oreina-dark mb-2">{{ $issue->title ?? 'OREINA' }}</div>
                <div class="text-xs text-gray-400 mb-3">{{ $issue->publication_date?->format('F Y') }}</div>
                @if($issue->pdf_file)
                <a href="{{ route('member.journal.download', $issue) }}" class="inline-flex items-center gap-1 text-sm text-oreina-green font-medium hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Télécharger
                </a>
                @endif
            </div>
            @endforeach
        </div>
        <a href="{{ route('member.journal') }}" class="block mt-4 text-center text-sm text-oreina-green font-medium hover:underline">
            Voir tous les numéros
        </a>
    </div>
    @endif
</div>
@endsection
