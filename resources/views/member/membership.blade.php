@extends('layouts.member')

@section('title', 'Mon adhésion')
@section('subtitle', 'Gérez votre adhésion à l\'association')

@section('actions')
    @if($isCurrentMember)
        <a href="{{ route('member.membership.card') }}" class="btn-member-outline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Carte d'adhérent
        </a>
    @endif
@endsection

@section('content')
<div class="space-y-6">
    {{-- Current membership status --}}
    <div class="member-card">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-oreina-dark mb-2">Statut de votre adhésion</h2>
                @if($isCurrentMember)
                    <p class="text-gray-600">
                        Adhésion <strong>{{ $currentMembership->membershipType?->name ?? 'Standard' }}</strong>
                        valide du {{ $currentMembership->start_date->format('d/m/Y') }}
                        au {{ $currentMembership->end_date->format('d/m/Y') }}
                    </p>
                @else
                    <p class="text-gray-600">
                        Vous n'avez pas d'adhésion active actuellement.
                    </p>
                @endif
            </div>

            @if($isCurrentMember)
                @php
                    $daysRemaining = now()->diffInDays($currentMembership->end_date, false);
                @endphp
                <div class="text-right">
                    @if($daysRemaining > 30)
                        <span class="status-badge active">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Active
                        </span>
                    @elseif($daysRemaining > 0)
                        <span class="status-badge pending">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Expire bientôt
                        </span>
                        <p class="text-sm text-amber-600 mt-1">{{ $daysRemaining }} jour(s) restant(s)</p>
                    @else
                        <span class="status-badge expired">Expirée</span>
                    @endif
                </div>
            @else
                <a href="{{ route('hub.membership') }}" class="btn-member">
                    Adhérer maintenant
                </a>
            @endif
        </div>

        @if($isCurrentMember)
            {{-- Progress bar --}}
            @php
                $totalDays = $currentMembership->start_date->diffInDays($currentMembership->end_date);
                $elapsedDays = $currentMembership->start_date->diffInDays(now());
                $progress = min(100, max(0, ($elapsedDays / $totalDays) * 100));
            @endphp
            <div class="mt-6">
                <div class="flex justify-between text-sm text-gray-500 mb-2">
                    <span>{{ $currentMembership->start_date->format('d/m/Y') }}</span>
                    <span>{{ $currentMembership->end_date->format('d/m/Y') }}</span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-oreina-green rounded-full transition-all" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        @endif
    </div>

    {{-- Member card preview --}}
    @if($isCurrentMember && $member)
    <div class="member-card">
        <div class="member-card-header">
            <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
            </svg>
            <h3 class="member-card-title">Votre carte d'adhérent</h3>
        </div>

        <div class="bg-gradient-to-br from-oreina-green to-oreina-dark rounded-xl p-6 text-white max-w-md">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <div class="text-xs opacity-75 uppercase tracking-wider">Association</div>
                    <div class="text-xl font-bold">OREINA</div>
                </div>
                <div class="text-right">
                    <div class="text-xs opacity-75">Valide jusqu'au</div>
                    <div class="font-semibold">{{ $currentMembership->end_date->format('d/m/Y') }}</div>
                </div>
            </div>
            <div class="mb-4">
                <div class="text-xs opacity-75 uppercase tracking-wider mb-1">Membre</div>
                <div class="text-lg font-semibold">{{ $member->full_name }}</div>
            </div>
            <div class="flex justify-between items-end">
                <div>
                    <div class="text-xs opacity-75">N° adhérent</div>
                    <div class="font-mono">{{ $member->member_number ?? 'N/A' }}</div>
                </div>
                <div class="text-xs opacity-75">
                    {{ $currentMembership->membershipType?->name ?? 'Membre' }}
                </div>
            </div>
        </div>

        <div class="mt-4 flex gap-3">
            <a href="{{ route('member.membership.card') }}" class="btn-member">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Télécharger en PDF
            </a>
            <a href="{{ route('member.membership.attestation') }}" class="btn-member-outline">
                Attestation d'adhésion
            </a>
        </div>
    </div>
    @endif

    {{-- Membership history --}}
    <div class="member-card">
        <div class="member-card-header">
            <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="member-card-title">Historique des adhésions</h3>
        </div>

        @if($membershipHistory->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 border-b">
                            <th class="pb-3 font-medium">Période</th>
                            <th class="pb-3 font-medium">Type</th>
                            <th class="pb-3 font-medium">Montant</th>
                            <th class="pb-3 font-medium">Statut</th>
                            <th class="pb-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($membershipHistory as $membership)
                        <tr>
                            <td class="py-3">
                                <span class="font-medium text-oreina-dark">{{ $membership->start_date->format('d/m/Y') }}</span>
                                <span class="text-gray-400 mx-1">→</span>
                                <span class="text-gray-600">{{ $membership->end_date->format('d/m/Y') }}</span>
                            </td>
                            <td class="py-3 text-gray-600">
                                {{ $membership->membershipType?->name ?? 'Standard' }}
                            </td>
                            <td class="py-3 text-gray-600">
                                {{ number_format($membership->amount ?? 0, 2, ',', ' ') }} €
                            </td>
                            <td class="py-3">
                                @if($membership->status === 'active' && $membership->end_date >= now())
                                    <span class="status-badge active">Active</span>
                                @elseif($membership->status === 'active')
                                    <span class="status-badge expired">Expirée</span>
                                @else
                                    <span class="status-badge pending">{{ ucfirst($membership->status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('member.documents.membership-receipt', $membership->id) }}" class="text-sm text-oreina-green hover:underline">
                                    Reçu
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-6">Aucune adhésion enregistrée</p>
        @endif
    </div>

    {{-- Renew CTA --}}
    @if(!$isCurrentMember || ($currentMembership && now()->diffInDays($currentMembership->end_date, false) <= 60))
    <div class="bg-gradient-to-r from-oreina-green/10 to-oreina-turquoise/10 rounded-xl p-6 text-center">
        <h3 class="text-lg font-bold text-oreina-dark mb-2">
            @if($isCurrentMember)
                Renouvelez votre adhésion
            @else
                Rejoignez-nous !
            @endif
        </h3>
        <p class="text-gray-600 mb-4">
            @if($isCurrentMember)
                Votre adhésion expire bientôt. Renouvelez-la pour continuer à profiter de tous vos avantages.
            @else
                Devenez membre de l'association OREINA et accédez à tous les avantages réservés aux adhérents.
            @endif
        </p>
        <a href="{{ route('hub.membership') }}" class="btn-member inline-flex">
            @if($isCurrentMember)
                Renouveler mon adhésion
            @else
                Adhérer maintenant
            @endif
        </a>
    </div>
    @endif
</div>
@endsection
