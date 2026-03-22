@extends('layouts.admin')
@section('title', 'Produits')
@section('breadcrumb')<span>Produits</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Produits
            </h1>
            <p class="page-subtitle">{{ $products->total() }} produit(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.products.export', request()->query()) }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau produit
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $totalProducts }}</span>
                <span class="stat-card-label">Total produits</span>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $activeProducts }}</span>
                <span class="stat-card-label">Produits actifs</span>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $magazineCount }}</span>
                <span class="stat-card-label">Magazines</span>
            </div>
        </div>

        <div class="stat-card stat-card-info">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($totalSales, 0, ',', ' ') }} EUR</span>
                <span class="stat-card-label">Total ventes</span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <form action="{{ route('admin.products.index') }}" method="GET" id="filterForm">
            <div class="filters-bar">
                <div class="search-group">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher produit..." class="search-input">
                </div>

                <div class="filters-group">
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach(\App\Models\Product::getTypeOptions() as $value => $label)
                            <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <select name="year" class="form-select">
                        <option value="">Toutes les annees</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
                    </select>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    @if(request()->hasAny(['search', 'type', 'year', 'status']))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost">Reset</a>
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
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Annee</th>
                        <th>
                            <a href="{{ route('admin.products.index', array_merge(request()->query(), ['sort' => 'price', 'direction' => request('sort') === 'price' && request('direction') === 'desc' ? 'asc' : 'desc'])) }}" class="sort-header {{ request('sort') === 'price' ? 'active' : '' }}">
                                Prix
                            </a>
                        </th>
                        <th>Ventes</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="contact-cell">
                                    <div class="contact-avatar" style="background: {{ $product->product_type === 'magazine' ? '#3b82f6' : ($product->product_type === 'rencontre' ? '#10b981' : '#f59e0b') }};">
                                        {{ strtoupper(substr($product->name, 0, 1)) }}
                                    </div>
                                    <div class="contact-info">
                                        <a href="{{ route('admin.products.show', $product) }}" class="contact-name">{{ $product->name }}</a>
                                        @if($product->sku)
                                            <span class="contact-email">SKU: {{ $product->sku }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $product->product_type === 'magazine' ? 'info' : ($product->product_type === 'rencontre' ? 'success' : 'warning') }}">
                                    {{ $product->getTypeLabel() }}
                                </span>
                            </td>
                            <td>{{ $product->year ?? '-' }}</td>
                            <td><span class="amount">{{ number_format($product->price, 2, ',', ' ') }} EUR</span></td>
                            <td>{{ $product->purchases_count }}</td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge badge-success">Actif</span>
                                @else
                                    <span class="badge badge-secondary">Inactif</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-ghost btn-sm" title="Voir">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-ghost btn-sm" title="Modifier">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce produit ?');">
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
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <h3 class="empty-state-title">Aucun produit trouve</h3>
                                    <p class="empty-state-description">Aucun produit ne correspond a vos criteres de recherche.</p>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Ajouter un produit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Affichage de {{ $products->firstItem() }} a {{ $products->lastItem() }} sur {{ $products->total() }} resultats
                </div>
                <div class="pagination">
                    {{ $products->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
