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

    {{-- GT cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @if($workGroups->count() > 0)
            @foreach($workGroups as $gt)
            <a href="{{ $gt->website_url ?? route('member.work-groups') }}" class="block" style="background: linear-gradient(135deg, {{ $gt->color }}, {{ $gt->color }}cc); padding: 1.5rem; border-radius: 1.5rem; color: white; min-height: 110px; display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; transition: all 0.3s ease; box-shadow: 0 10px 30px {{ $gt->color }}40;" onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 15px 40px {{ $gt->color }}50'" onmouseleave="this.style.transform='';this.style.boxShadow='0 10px 30px {{ $gt->color }}40'">
                <div style="position: absolute; top: -20px; right: -20px; width: 70px; height: 70px; border-radius: 50%; background: rgba(255,255,255,0.12);"></div>
                <div style="position: absolute; bottom: -10px; left: -10px; width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.08);"></div>
                <div>
                    <div style="font-weight: 700; font-size: 0.9375rem;">{{ $gt->name }}</div>
                    <div style="font-size: 0.6875rem; opacity: 0.85; margin-top: 0.25rem;">{{ Str::limit($gt->description, 50) }}</div>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.75rem;">
                    <div style="font-size: 0.6875rem; opacity: 0.8;">{{ $gt->members_count }} membres</div>
                    @if(in_array($gt->id, $myGroupIds))
                        <span style="font-size: 0.625rem; background: rgba(255,255,255,0.25); padding: 0.2rem 0.625rem; border-radius: 9999px; font-weight: 600; backdrop-filter: blur(8px);">Membre</span>
                    @endif
                </div>
            </a>
            @endforeach
        @else
            <div class="gt-card-placeholder">
                <div class="label">Groupes de travail</div>
                <div class="sub">Aucun groupe actif</div>
            </div>
        @endif
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
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
        <div class="member-stat" style="border: 2px dashed rgba(219,203,199,0.4); background: rgba(219,203,199,0.06);">
            <div class="member-stat-value" style="color: #DBCBC7;">—</div>
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
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($latestIssues as $issue)
            <div class="border-2 border-oreina-beige/30 rounded-2xl p-4 hover:border-oreina-turquoise/40 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
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
