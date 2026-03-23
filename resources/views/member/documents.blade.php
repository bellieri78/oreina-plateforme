@extends('layouts.member')

@section('title', 'Mes documents')
@section('subtitle', 'Téléchargez vos reçus et attestations')

@section('content')
<div class="space-y-6">
    {{-- Fiscal receipts (Cerfa) --}}
    <div class="member-card">
        <div class="member-card-header">
            <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
            </svg>
            <h3 class="member-card-title">Reçus fiscaux (dons)</h3>
        </div>

        <p class="text-sm text-gray-600 mb-4">
            Les reçus fiscaux vous permettent de bénéficier d'une réduction d'impôt de 66% du montant de votre don (dans la limite de 20% de votre revenu imposable).
        </p>

        @if($donations->count() > 0)
            <div class="space-y-3">
                @foreach($donations as $donation)
                <div class="document-item">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-oreina-dark">
                                Don de {{ number_format($donation->amount, 2, ',', ' ') }} €
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $donation->donation_date->format('d/m/Y') }}
                                @if($donation->payment_method)
                                    · {{ ucfirst($donation->payment_method) }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('member.documents.cerfa', $donation) }}" class="btn-member-outline text-sm py-2 px-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Cerfa
                    </a>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p>Aucun don enregistré</p>
                <a href="{{ route('hub.membership') }}" class="text-oreina-green hover:underline text-sm mt-2 inline-block">
                    Faire un don
                </a>
            </div>
        @endif
    </div>

    {{-- Membership receipts --}}
    <div class="member-card">
        <div class="member-card-header">
            <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
            </svg>
            <h3 class="member-card-title">Reçus d'adhésion</h3>
        </div>

        @if($memberships->count() > 0)
            <div class="space-y-3">
                @foreach($memberships as $membership)
                <div class="document-item">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-oreina-dark">
                                Adhésion {{ $membership->start_date->format('Y') }}
                                @if($membership->membershipType)
                                    - {{ $membership->membershipType->name }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $membership->start_date->format('d/m/Y') }} → {{ $membership->end_date->format('d/m/Y') }}
                                @if($membership->amount)
                                    · {{ number_format($membership->amount, 2, ',', ' ') }} €
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('member.documents.membership-receipt', $membership->id) }}" class="btn-member-outline text-sm py-2 px-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Reçu
                    </a>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                </svg>
                <p>Aucune adhésion enregistrée</p>
            </div>
        @endif
    </div>

    {{-- Info box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-700">
                <p class="font-medium mb-1">Besoin d'un document spécifique ?</p>
                <p>Si vous avez besoin d'une attestation particulière ou d'un duplicata, contactez-nous à <a href="mailto:adhesion@oreina.org" class="underline">adhesion@oreina.org</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
