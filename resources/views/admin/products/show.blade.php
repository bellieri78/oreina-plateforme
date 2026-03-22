@extends('layouts.admin')
@section('title', $product->name)
@section('breadcrumb')
    <a href="{{ route('admin.products.index') }}">Produits</a>
    <span>/</span>
    <span>{{ $product->name }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">{{ $product->name }}</h1>
            <p class="page-subtitle">
                <span class="badge badge-{{ $product->product_type === 'magazine' ? 'info' : ($product->product_type === 'rencontre' ? 'success' : 'warning') }}">
                    {{ $product->getTypeLabel() }}
                </span>
                @if(!$product->is_active)
                    <span class="badge badge-secondary">Inactif</span>
                @endif
            </p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($product->price, 2, ',', ' ') }} EUR</span>
                <span class="stat-card-label">Prix unitaire</span>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $purchaseCount }}</span>
                <span class="stat-card-label">Ventes</span>
            </div>
        </div>

        <div class="stat-card stat-card-info">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($totalRevenue, 0, ',', ' ') }} EUR</span>
                <span class="stat-card-label">Chiffre d'affaires</span>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        <!-- Product Details -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Details du produit</h3>
            </div>
            <div class="card-body">
                <dl style="display: grid; gap: 1rem;">
                    @if($product->sku)
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Code SKU</dt>
                        <dd style="font-weight: 500;">{{ $product->sku }}</dd>
                    </div>
                    @endif
                    @if($product->year)
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Annee</dt>
                        <dd style="font-weight: 500;">{{ $product->year }}</dd>
                    </div>
                    @endif
                    @if($product->issue_number)
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Numero</dt>
                        <dd style="font-weight: 500;">{{ $product->issue_number }}</dd>
                    </div>
                    @endif
                    @if($product->event_date)
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Date evenement</dt>
                        <dd style="font-weight: 500;">{{ $product->event_date->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                    @if($product->event_location)
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Lieu</dt>
                        <dd style="font-weight: 500;">{{ $product->event_location }}</dd>
                    </div>
                    @endif
                    @if($product->stock_quantity !== null)
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Stock</dt>
                        <dd style="font-weight: 500;">{{ $product->stock_quantity }}</dd>
                    </div>
                    @endif
                    @if($product->description)
                    <div>
                        <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Description</dt>
                        <dd>{{ $product->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Recent Purchases -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Achats recents</h3>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Membre</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPurchases as $purchase)
                            <tr>
                                <td>
                                    @if($purchase->member)
                                        <a href="{{ route('admin.members.show', $purchase->member) }}">
                                            {{ $purchase->member->full_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                                <td>{{ number_format($purchase->total_amount, 2, ',', ' ') }} EUR</td>
                                <td>
                                    <span class="badge badge-{{ $purchase->source === 'import' ? 'warning' : 'info' }}">
                                        {{ $purchase->getSourceLabel() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Aucun achat</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
