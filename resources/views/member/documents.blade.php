@extends('layouts.member')

@section('title', 'Mes documents')
@section('page-title', 'Mes documents')
@section('page-subtitle', 'Reçus fiscaux et attestations')

@section('content')
<div class="space-y-6">
    {{-- Fiscal receipts (Cerfa) --}}
    <div class="card panel">
        <div class="panel-head">
            <div>
                <h2>Reçus fiscaux (dons)</h2>
            </div>
        </div>

        <p class="text-sm mb-4" style="color:var(--muted)">
            Les reçus fiscaux vous permettent de bénéficier d'une réduction d'impôt de 66% du montant de votre don (dans la limite de 20% de votre revenu imposable).
        </p>

        @if($donations->count() > 0)
            <div class="space-y-3">
                @foreach($donations as $donation)
                <div class="todo-item">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background:var(--surface-amber)">
                            <i data-lucide="file-text" style="color:var(--amber)"></i>
                        </div>
                        <div>
                            <div class="font-medium" style="color:var(--forest)">
                                Don de {{ number_format($donation->amount, 2, ',', ' ') }} €
                            </div>
                            <div class="text-sm" style="color:var(--muted)">
                                {{ $donation->donation_date->format('d/m/Y') }}
                                @if($donation->payment_method)
                                    · {{ ucfirst($donation->payment_method) }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('member.documents.cerfa', $donation) }}" class="btn btn-secondary text-sm py-2 px-3">
                        <i data-lucide="download"></i>
                        Cerfa
                    </a>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8" style="color:var(--muted)">
                <i data-lucide="file-text" class="mx-auto mb-3" style="width:48px;height:48px;display:block;color:var(--border)"></i>
                <p>Aucun don enregistré</p>
                <a href="{{ route('hub.membership') }}" class="text-link hover:underline text-sm mt-2 inline-block">
                    Faire un don
                </a>
            </div>
        @endif
    </div>

    {{-- Membership receipts --}}
    <div class="card panel">
        <div class="panel-head">
            <div>
                <h2>Reçus d'adhésion</h2>
            </div>
        </div>

        @if($memberships->count() > 0)
            <div class="space-y-3">
                @foreach($memberships as $membership)
                <div class="todo-item">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background:var(--surface-sage)">
                            <i data-lucide="check-circle" style="color:var(--sage)"></i>
                        </div>
                        <div>
                            <div class="font-medium" style="color:var(--forest)">
                                Adhésion {{ $membership->start_date->format('Y') }}
                                @if($membership->membershipType)
                                    - {{ $membership->membershipType->name }}
                                @endif
                            </div>
                            <div class="text-sm" style="color:var(--muted)">
                                {{ $membership->start_date->format('d/m/Y') }} → {{ $membership->end_date->format('d/m/Y') }}
                                @if($membership->amount)
                                    · {{ number_format($membership->amount, 2, ',', ' ') }} €
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('member.documents.membership-receipt', $membership->id) }}" class="btn btn-secondary text-sm py-2 px-3">
                        <i data-lucide="download"></i>
                        Reçu
                    </a>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8" style="color:var(--muted)">
                <i data-lucide="id-card" class="mx-auto mb-3" style="width:48px;height:48px;display:block;color:var(--border)"></i>
                <p>Aucune adhésion enregistrée</p>
            </div>
        @endif
    </div>

    {{-- Info box --}}
    <div class="rounded-lg p-4" style="background:var(--surface-blue); border:1px solid var(--border)">
        <div class="flex gap-3">
            <i data-lucide="info" class="flex-shrink-0 mt-0.5" style="color:var(--info)"></i>
            <div class="text-sm" style="color:var(--forest)">
                <p class="font-medium mb-1">Besoin d'un document spécifique ?</p>
                <p>Si vous avez besoin d'une attestation particulière ou d'un duplicata, contactez-nous à <a href="mailto:adhesion@oreina.org" class="underline">adhesion@oreina.org</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
