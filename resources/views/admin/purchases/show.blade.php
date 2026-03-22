@extends('layouts.admin')
@section('title', 'Detail achat')
@section('breadcrumb')
    <a href="{{ route('admin.purchases.index') }}">Achats</a>
    <span>/</span>
    <span>Detail</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Detail de l'achat #{{ $purchase->id }}</h1>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.purchases.edit', $purchase) }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informations achat</h3>
            </div>
            <div class="card-body">
                <dl style="display: grid; gap: 1rem;">
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Produit</dt>
                        <dd style="font-weight: 500;">
                            @if($purchase->product)
                                <a href="{{ route('admin.products.show', $purchase->product) }}">{{ $purchase->product->name }}</a>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Quantite</dt>
                        <dd style="font-weight: 500;">{{ $purchase->quantity }}</dd>
                    </div>
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Prix unitaire</dt>
                        <dd style="font-weight: 500;">{{ number_format($purchase->unit_price, 2, ',', ' ') }} EUR</dd>
                    </div>
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Total</dt>
                        <dd style="font-weight: 600; font-size: 1.25rem; color: #2C5F2D;">{{ number_format($purchase->total_amount, 2, ',', ' ') }} EUR</dd>
                    </div>
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Date d'achat</dt>
                        <dd style="font-weight: 500;">{{ $purchase->purchase_date->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Source</dt>
                        <dd>
                            <span class="badge badge-{{ $purchase->source === 'import' ? 'warning' : 'info' }}">
                                {{ $purchase->getSourceLabel() }}
                            </span>
                        </dd>
                    </div>
                    @if($purchase->payment_method)
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Moyen de paiement</dt>
                        <dd style="font-weight: 500;">{{ ucfirst($purchase->payment_method) }}</dd>
                    </div>
                    @endif
                    @if($purchase->payment_reference)
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Reference</dt>
                        <dd style="font-weight: 500;">{{ $purchase->payment_reference }}</dd>
                    </div>
                    @endif
                    @if($purchase->notes)
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Notes</dt>
                        <dd>{{ $purchase->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Membre</h3>
            </div>
            <div class="card-body">
                @if($purchase->member)
                    <div class="contact-cell" style="margin-bottom: 1rem;">
                        <div class="contact-avatar contact-avatar-member" style="width: 48px; height: 48px; font-size: 1.25rem;">
                            {{ strtoupper(substr($purchase->member->first_name ?? $purchase->member->last_name, 0, 1)) }}
                        </div>
                        <div class="contact-info">
                            <a href="{{ route('admin.members.show', $purchase->member) }}" class="contact-name" style="font-size: 1.125rem;">
                                {{ $purchase->member->full_name }}
                            </a>
                            @if($purchase->member->email)
                                <span class="contact-email">{{ $purchase->member->email }}</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('admin.members.show', $purchase->member) }}" class="btn btn-outline btn-sm">Voir la fiche membre</a>
                @else
                    <p class="text-muted">Membre non trouve</p>
                @endif
            </div>
        </div>
    </div>

    @if($purchase->legacyMembership)
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Adhesion liee (import)</h3>
        </div>
        <div class="card-body">
            <p>Cet achat est lie a l'adhesion de l'annee {{ $purchase->legacyMembership->start_date->year }}.</p>
            <p><strong>Periode:</strong> {{ $purchase->legacyMembership->start_date->format('d/m/Y') }} - {{ $purchase->legacyMembership->end_date->format('d/m/Y') }}</p>
            <p><strong>Montant adhesion:</strong> {{ number_format($purchase->legacyMembership->amount_paid, 2, ',', ' ') }} EUR</p>
        </div>
    </div>
    @endif
@endsection
