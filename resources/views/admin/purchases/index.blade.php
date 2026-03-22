@extends('layouts.admin')
@section('title', 'Achats')
@section('breadcrumb')<span>Achats</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Achats
            </h1>
            <p class="page-subtitle">{{ $purchases->total() }} achat(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.purchases.export', request()->query()) }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter
            </a>
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvel achat
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $totalPurchases }}</span>
                <span class="stat-card-label">Total achats</span>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($yearAmount, 0, ',', ' ') }} EUR</span>
                <span class="stat-card-label">Total {{ now()->year }}</span>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $importCount }}</span>
                <span class="stat-card-label">Importes</span>
            </div>
        </div>

        <div class="stat-card stat-card-info">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $manualCount }}</span>
                <span class="stat-card-label">Saisies manuelles</span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <form action="{{ route('admin.purchases.index') }}" method="GET" id="filterForm">
            <div class="filters-bar">
                <div class="search-group">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher membre..." class="search-input">
                </div>

                <div class="filters-group">
                    <select name="product_id" class="form-select">
                        <option value="">Tous les produits</option>
                        @foreach($products as $id => $name)
                            <option value="{{ $id }}" {{ request('product_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>

                    <select name="source" class="form-select">
                        <option value="">Toutes les sources</option>
                        @foreach(\App\Models\Purchase::getSourceOptions() as $value => $label)
                            <option value="{{ $value }}" {{ request('source') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <select name="year" class="form-select">
                        <option value="">Toutes les annees</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    @if(request()->hasAny(['search', 'product_id', 'source', 'year', 'payment_method']))
                        <a href="{{ route('admin.purchases.index') }}" class="btn btn-ghost">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>Membre</th>
                        <th>Produit</th>
                        <th>Qte</th>
                        <th>
                            <a href="{{ route('admin.purchases.index', array_merge(request()->query(), ['sort' => 'total_amount', 'direction' => request('sort') === 'total_amount' && request('direction') === 'desc' ? 'asc' : 'desc'])) }}" class="sort-header">
                                Montant
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.purchases.index', array_merge(request()->query(), ['sort' => 'purchase_date', 'direction' => request('sort') === 'purchase_date' && request('direction') === 'desc' ? 'asc' : 'desc'])) }}" class="sort-header">
                                Date
                            </a>
                        </th>
                        <th>Source</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $purchase->id }}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                @if($purchase->member)
                                    <div class="contact-cell">
                                        <div class="contact-avatar contact-avatar-member">
                                            {{ strtoupper(substr($purchase->member->first_name ?? $purchase->member->last_name, 0, 1)) }}
                                        </div>
                                        <div class="contact-info">
                                            <a href="{{ route('admin.members.show', $purchase->member) }}" class="contact-name">
                                                {{ $purchase->member->full_name }}
                                            </a>
                                            @if($purchase->member->email)
                                                <span class="contact-email">{{ $purchase->member->email }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($purchase->product)
                                    <a href="{{ route('admin.products.show', $purchase->product) }}">{{ $purchase->product->name }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $purchase->quantity }}</td>
                            <td><span class="amount">{{ number_format($purchase->total_amount, 2, ',', ' ') }} EUR</span></td>
                            <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $purchase->source === 'import' ? 'warning' : ($purchase->source === 'helloasso' ? 'success' : 'info') }}">
                                    {{ $purchase->getSourceLabel() }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    <a href="{{ route('admin.purchases.show', $purchase) }}" class="btn btn-ghost btn-sm" title="Voir">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.purchases.edit', $purchase) }}" class="btn btn-ghost btn-sm" title="Modifier">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.purchases.destroy', $purchase) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet achat ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm text-danger" title="Supprimer">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="empty-state-title">Aucun achat trouve</h3>
                                    <p class="empty-state-description">Aucun achat ne correspond a vos criteres de recherche.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Affichage de {{ $purchases->firstItem() }} a {{ $purchases->lastItem() }} sur {{ $purchases->total() }} resultats
                </div>
                <div class="pagination">
                    {{ $purchases->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Selection Bar -->
    <div class="selection-bar" id="selectionBar" style="display: none;">
        <div class="selection-bar-content">
            <span class="selection-count"><span id="selectedCount">0</span> element(s) selectionne(s)</span>
            <div class="selection-actions">
                <button type="button" class="btn btn-danger btn-sm" onclick="bulkDelete()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    <form id="bulkDeleteForm" action="{{ route('admin.purchases.bulk-delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkDeleteIds">
    </form>

    <script>
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = selectAll.checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        document.getElementById('selectionBar').style.display = checked.length > 0 ? 'flex' : 'none';
        document.getElementById('selectedCount').textContent = checked.length;
    }

    function bulkDelete() {
        const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value).join(',');
        if (ids && confirm('Supprimer ' + ids.split(',').length + ' achat(s) ?')) {
            document.getElementById('bulkDeleteIds').value = ids;
            document.getElementById('bulkDeleteForm').submit();
        }
    }
    </script>
@endsection
