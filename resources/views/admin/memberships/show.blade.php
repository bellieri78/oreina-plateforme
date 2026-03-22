@extends('layouts.admin')
@section('title', 'Adhesion')
@section('breadcrumb')
    <a href="{{ route('admin.memberships.index') }}">Adhesions</a>
    <span>/</span>
    <span>Details</span>
@endsection

@section('content')
    <div class="card" style="max-width: 800px;">
        <div class="card-header">
            <h3 class="card-title">Details de l'adhesion</h3>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('admin.memberships.edit', $membership) }}" class="btn btn-secondary">Modifier</a>
            </div>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 1rem;">Membre</h4>
                    @if($membership->member)
                        <div style="margin-bottom: 1rem;">
                            <div style="font-weight: 600; font-size: 1.125rem;">{{ $membership->member->full_name }}</div>
                            <div style="color: #6b7280;">{{ $membership->member->email }}</div>
                        </div>
                        <a href="{{ route('admin.members.show', $membership->member) }}" class="btn btn-secondary" style="font-size: 0.875rem;">
                            Voir le profil
                        </a>
                    @else
                        <span style="color: #9ca3af;">Membre supprime</span>
                    @endif
                </div>
                <div>
                    <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 1rem;">Adhesion</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: #356B8A; margin-bottom: 1rem;">
                        {{ number_format($membership->amount_paid, 0, ',', ' ') }} EUR
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <span style="color: #6b7280;">Type :</span> {{ $membership->membershipType?->name ?? 'Standard' }}
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <span style="color: #6b7280;">Periode :</span> {{ $membership->start_date->format('d/m/Y') }} - {{ $membership->end_date->format('d/m/Y') }}
                    </div>
                    @if($membership->payment_method)
                        <div style="margin-bottom: 0.5rem;">
                            <span style="color: #6b7280;">Paiement :</span> {{ $membership->payment_method }}
                        </div>
                    @endif
                    @if($membership->payment_reference)
                        <div style="margin-bottom: 0.5rem;">
                            <span style="color: #6b7280;">Reference :</span> {{ $membership->payment_reference }}
                        </div>
                    @endif
                    <div style="margin-top: 1rem;">
                        @if($membership->end_date >= now())
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-warning">Expiree</span>
                        @endif
                    </div>
                </div>
            </div>
            @if($membership->notes)
                <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                    <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Notes</h4>
                    <p style="color: #374151;">{{ $membership->notes }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
