@extends('layouts.admin')
@section('title', 'Don de ' . $donation->donor_name)
@section('breadcrumb')
    <a href="{{ route('admin.donations.index') }}">Dons</a>
    <span>/</span>
    <span>{{ $donation->donor_name }}</span>
@endsection

@section('content')
    <div class="card" style="max-width: 800px;">
        <div class="card-header">
            <h3 class="card-title">Details du don</h3>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('donation.receipt.download', $donation) }}" class="btn btn-success" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Recu fiscal
                </a>
                <a href="{{ route('admin.donations.edit', $donation) }}" class="btn btn-secondary">Modifier</a>
            </div>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 1rem;">Donateur</h4>
                    <div style="margin-bottom: 1rem;">
                        <div style="font-weight: 600; font-size: 1.125rem;">{{ $donation->donor_name }}</div>
                        <div style="color: #6b7280;">{{ $donation->donor_email }}</div>
                    </div>
                    @if($donation->donor_address || $donation->donor_city)
                        <div style="color: #6b7280;">
                            @if($donation->donor_address){{ $donation->donor_address }}<br>@endif
                            {{ $donation->donor_postal_code }} {{ $donation->donor_city }}
                        </div>
                    @endif
                    @if($donation->member)
                        <div style="margin-top: 1rem;">
                            <span class="badge badge-info">Adherent lie</span>
                            <a href="{{ route('admin.members.show', $donation->member) }}" style="color: #356B8A; margin-left: 0.5rem;">
                                {{ $donation->member->full_name }}
                            </a>
                        </div>
                    @endif
                </div>
                <div>
                    <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 1rem;">Don</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: #2dce89; margin-bottom: 1rem;">
                        {{ number_format($donation->amount, 2, ',', ' ') }} EUR
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <span style="color: #6b7280;">Date :</span> {{ $donation->donation_date->format('d/m/Y') }}
                    </div>
                    @if($donation->payment_method)
                        <div style="margin-bottom: 0.5rem;">
                            <span style="color: #6b7280;">Paiement :</span> {{ $donation->payment_method }}
                        </div>
                    @endif
                    @if($donation->payment_reference)
                        <div style="margin-bottom: 0.5rem;">
                            <span style="color: #6b7280;">Reference :</span> {{ $donation->payment_reference }}
                        </div>
                    @endif
                    @if($donation->campaign)
                        <div style="margin-bottom: 0.5rem;">
                            <span style="color: #6b7280;">Campagne :</span> {{ $donation->campaign }}
                        </div>
                    @endif
                    <div style="margin-top: 1rem;">
                        @if($donation->tax_receipt_sent)
                            <span class="badge badge-success">Recu fiscal envoye</span>
                        @else
                            <span class="badge badge-warning">Recu fiscal a envoyer</span>
                        @endif
                    </div>
                </div>
            </div>
            @if($donation->notes)
                <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                    <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Notes</h4>
                    <p style="color: #374151;">{{ $donation->notes }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
