@extends('layouts.member')

@section('title', 'Mon adhésion')
@section('page-title', 'Mon adhésion')
@section('page-subtitle', 'Statut, historique et documents d\'adhésion')

@section('actions')
    @if($isCurrentMember)
        <a href="{{ route('member.membership.card') }}" class="btn btn-secondary">
            <i data-lucide="download"></i>
            Carte d'adhérent
        </a>
    @endif
@endsection

@section('content')
<div class="space-y-6">
    {{-- Current membership status --}}
    <div class="card panel">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold mb-2" style="color:var(--forest)">Statut de votre adhésion</h2>
                @if($isCurrentMember)
                    <p style="color:var(--muted)">
                        Adhésion <strong>{{ $currentMembership->membershipType?->name ?? 'Standard' }}</strong>
                        valide du {{ $currentMembership->start_date->format('d/m/Y') }}
                        au {{ $currentMembership->end_date->format('d/m/Y') }}
                    </p>
                @else
                    <p style="color:var(--muted)">
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
                        <span class="badge sage">
                            <i data-lucide="check-circle"></i>
                            Active
                        </span>
                    @elseif($daysRemaining > 0)
                        <span class="badge" style="background:var(--surface-amber);color:var(--amber)">
                            <i data-lucide="alert-circle"></i>
                            Expire bientôt
                        </span>
                        <p class="text-sm mt-1" style="color:var(--amber)">{{ $daysRemaining }} jour(s) restant(s)</p>
                    @else
                        <span class="badge coral">Expirée</span>
                    @endif
                </div>
            @else
                <a href="{{ route('hub.membership') }}" class="btn btn-primary">
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
                <div class="flex justify-between text-sm mb-2" style="color:var(--muted)">
                    <span>{{ $currentMembership->start_date->format('d/m/Y') }}</span>
                    <span>{{ $currentMembership->end_date->format('d/m/Y') }}</span>
                </div>
                <div class="h-2 rounded-full overflow-hidden" style="background:var(--border)">
                    <div class="h-full rounded-full transition-all" style="background:var(--sage); width: {{ $progress }}%"></div>
                </div>
            </div>
        @endif
    </div>

    {{-- Member card preview --}}
    @if($isCurrentMember && $member)
    <div class="card panel">
        <div class="panel-head">
            <div>
                <h2>Votre carte d'adhérent</h2>
            </div>
        </div>

        <div class="rounded-xl p-6 text-white max-w-md" style="background:linear-gradient(to bottom right, var(--sage), var(--forest))">
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
            <a href="{{ route('member.membership.card') }}" class="btn btn-primary">
                <i data-lucide="download"></i>
                Télécharger en PDF
            </a>
            <a href="{{ route('member.membership.attestation') }}" class="btn btn-secondary">
                Attestation d'adhésion
            </a>
        </div>
    </div>
    @endif

    {{-- Membership history --}}
    <div class="card panel">
        <div class="panel-head">
            <div>
                <h2>Historique des adhésions</h2>
            </div>
        </div>

        @if($membershipHistory->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm border-b" style="color:var(--muted); border-color:var(--border)">
                            <th class="pb-3 font-medium">Période</th>
                            <th class="pb-3 font-medium">Type</th>
                            <th class="pb-3 font-medium">Montant</th>
                            <th class="pb-3 font-medium">Statut</th>
                            <th class="pb-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="--tw-divide-opacity:1; --tw-divide-color:var(--border)">
                        @foreach($membershipHistory as $membership)
                        <tr>
                            <td class="py-3">
                                <span class="font-medium" style="color:var(--forest)">{{ $membership->start_date->format('d/m/Y') }}</span>
                                <span class="mx-1" style="color:var(--muted)">→</span>
                                <span style="color:var(--muted)">{{ $membership->end_date->format('d/m/Y') }}</span>
                            </td>
                            <td class="py-3" style="color:var(--muted)">
                                {{ $membership->membershipType?->name ?? 'Standard' }}
                            </td>
                            <td class="py-3" style="color:var(--muted)">
                                {{ number_format($membership->amount ?? 0, 2, ',', ' ') }} €
                            </td>
                            <td class="py-3">
                                @if($membership->status === 'active' && $membership->end_date >= now())
                                    <span class="badge sage">Active</span>
                                @elseif($membership->status === 'active')
                                    <span class="badge coral">Expirée</span>
                                @else
                                    <span class="badge" style="background:var(--surface-amber);color:var(--amber)">{{ ucfirst($membership->status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('member.documents.membership-receipt', $membership->id) }}" class="text-sm text-link hover:underline">
                                    Reçu
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center py-6" style="color:var(--muted)">Aucune adhésion enregistrée</p>
        @endif
    </div>

    {{-- Renew CTA --}}
    @if(!$isCurrentMember || ($currentMembership && now()->diffInDays($currentMembership->end_date, false) <= 60))
    <div class="rounded-xl p-6 text-center" style="background:linear-gradient(to right, var(--surface-sage), var(--surface-blue))">
        <h3 class="text-lg font-bold mb-2" style="color:var(--forest)">
            @if($isCurrentMember)
                Renouvelez votre adhésion
            @else
                Rejoignez-nous !
            @endif
        </h3>
        <p class="mb-4" style="color:var(--muted)">
            @if($isCurrentMember)
                Votre adhésion expire bientôt. Renouvelez-la pour continuer à profiter de tous vos avantages.
            @else
                Devenez membre de l'association OREINA et accédez à tous les avantages réservés aux adhérents.
            @endif
        </p>
        <a href="{{ route('hub.membership') }}" class="btn btn-primary inline-flex">
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
