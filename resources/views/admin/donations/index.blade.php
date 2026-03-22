@extends('layouts.admin')
@section('title', 'Dons')
@section('breadcrumb')<span>Dons</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                Dons
            </h1>
            <p class="page-subtitle">{{ $donations->total() }} don(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.donations.export', request()->query()) }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter
            </a>
            <a href="{{ route('admin.donations.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau don
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card stat-card-success">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($yearAmount, 0, ',', ' ') }} EUR</span>
                <span class="stat-card-label">Total {{ now()->year }}</span>
            </div>
        </div>

        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($totalAmount, 0, ',', ' ') }} EUR</span>
                <span class="stat-card-label">Total general</span>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $pendingReceipts ?? 0 }}</span>
                <span class="stat-card-label">Recus a envoyer</span>
            </div>
        </div>

        <div class="stat-card stat-card-info">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $donorsCount ?? $donations->total() }}</span>
                <span class="stat-card-label">Donateurs</span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <form action="{{ route('admin.donations.index') }}" method="GET" id="filterForm">
            <div class="filters-bar">
                <div class="search-group">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher donateur..." class="search-input">
                </div>

                <div class="filters-group">
                    <select name="year" class="form-select">
                        <option value="">Toutes les annees</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>

                    <select name="receipt" class="form-select">
                        <option value="">Tous les recus</option>
                        <option value="1" {{ request('receipt') === '1' ? 'selected' : '' }}>Recu envoye</option>
                        <option value="0" {{ request('receipt') === '0' ? 'selected' : '' }}>A envoyer</option>
                    </select>

                    <button type="button" class="btn btn-ghost" onclick="toggleAdvancedFilters()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        Plus de filtres
                    </button>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    @if(request()->hasAny(['search', 'year', 'receipt', 'payment_method', 'campaign', 'amount_min', 'amount_max', 'date_from', 'date_to']))
                        <a href="{{ route('admin.donations.index') }}" class="btn btn-ghost">Reset</a>
                    @endif
                </div>
            </div>

            <!-- Advanced filters -->
            <div class="filters-advanced" id="advancedFilters" style="display: {{ request()->hasAny(['payment_method', 'campaign', 'amount_min', 'amount_max', 'date_from', 'date_to']) ? 'flex' : 'none' }};">
                <div class="filter-group">
                    <label class="form-label">Mode paiement</label>
                    <select name="payment_method" class="form-select">
                        <option value="">Tous</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method }}" {{ request('payment_method') === $method ? 'selected' : '' }}>{{ $method }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="form-label">Campagne</label>
                    <select name="campaign" class="form-select">
                        <option value="">Toutes</option>
                        @foreach($campaigns as $campaign)
                            <option value="{{ $campaign }}" {{ request('campaign') === $campaign ? 'selected' : '' }}>{{ $campaign }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="form-label">Montant min</label>
                    <input type="number" name="amount_min" value="{{ request('amount_min') }}" placeholder="0" class="form-input" style="width: 100px;">
                </div>
                <div class="filter-group">
                    <label class="form-label">Montant max</label>
                    <input type="number" name="amount_max" value="{{ request('amount_max') }}" placeholder="10000" class="form-input" style="width: 100px;">
                </div>
                <div class="filter-group">
                    <label class="form-label">Date du</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
                </div>
                <div class="filter-group">
                    <label class="form-label">au</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
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
                        <th>
                            <a href="{{ route('admin.donations.index', array_merge(request()->query(), ['sort' => 'donor_name', 'direction' => request('sort') === 'donor_name' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="sort-header {{ request('sort') === 'donor_name' ? 'active' : '' }}">
                                Donateur
                                @if(request('sort') === 'donor_name')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                                        @if(request('direction') === 'desc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.donations.index', array_merge(request()->query(), ['sort' => 'amount', 'direction' => request('sort') === 'amount' && request('direction') === 'desc' ? 'asc' : 'desc'])) }}" class="sort-header {{ request('sort') === 'amount' ? 'active' : '' }}">
                                Montant
                                @if(request('sort') === 'amount')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                                        @if(request('direction') === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.donations.index', array_merge(request()->query(), ['sort' => 'donation_date', 'direction' => request('sort') === 'donation_date' && request('direction') === 'desc' ? 'asc' : 'desc'])) }}" class="sort-header {{ request('sort', 'donation_date') === 'donation_date' ? 'active' : '' }}">
                                Date
                                @if(request('sort', 'donation_date') === 'donation_date')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                                        @if(request('direction', 'desc') === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>Paiement</th>
                        <th>Recu fiscal</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donations as $d)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $d->id }}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                <div class="contact-cell">
                                    <div class="contact-avatar contact-avatar-donation">
                                        {{ strtoupper(substr($d->donor_name, 0, 1)) }}
                                    </div>
                                    <div class="contact-info">
                                        <a href="{{ route('admin.donations.show', $d) }}" class="contact-name">
                                            {{ $d->donor_name }}
                                        </a>
                                        @if($d->donor_email)
                                            <span class="contact-email">{{ $d->donor_email }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="amount amount-success">{{ number_format($d->amount, 0, ',', ' ') }} EUR</span>
                            </td>
                            <td>{{ $d->donation_date->format('d/m/Y') }}</td>
                            <td>
                                @if($d->payment_method)
                                    <span class="badge badge-outline">{{ $d->payment_method }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($d->tax_receipt_sent)
                                    <span class="badge badge-success">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Envoye
                                    </span>
                                @else
                                    <span class="badge badge-warning">A envoyer</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    <a href="{{ route('admin.donations.edit', $d) }}" class="btn btn-ghost btn-sm" title="Modifier">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('donation.receipt.download', $d) }}" class="btn btn-ghost btn-sm" target="_blank" title="Telecharger recu">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="empty-state-title">Aucun don trouve</h3>
                                    <p class="empty-state-description">Aucun don ne correspond a vos criteres de recherche.</p>
                                    <a href="{{ route('admin.donations.create') }}" class="btn btn-primary">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Ajouter un don
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($donations->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Affichage de {{ $donations->firstItem() }} a {{ $donations->lastItem() }} sur {{ $donations->total() }} resultats
                </div>
                <div class="pagination">
                    @if($donations->onFirstPage())
                        <span class="pagination-btn disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $donations->previousPageUrl() }}" class="pagination-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @foreach($donations->getUrlRange(max(1, $donations->currentPage() - 2), min($donations->lastPage(), $donations->currentPage() + 2)) as $page => $url)
                        @if($page == $donations->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($donations->hasMorePages())
                        <a href="{{ $donations->nextPageUrl() }}" class="pagination-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <span class="pagination-btn disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Selection Bar (floating) -->
    <div class="selection-bar" id="selectionBar" style="display: none;">
        <div class="selection-bar-content">
            <span class="selection-count"><span id="selectedCount">0</span> element(s) selectionne(s)</span>
            <div class="selection-actions">
                <button type="button" class="btn btn-outline btn-sm" onclick="exportSelected()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exporter
                </button>
                <button type="button" class="btn btn-success btn-sm" onclick="bulkReceipt('sent')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Marquer envoye
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="bulkReceipt('not_sent')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Marquer non envoye
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="bulkDelete()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden forms for bulk actions -->
    <form id="bulkDeleteForm" action="{{ route('admin.donations.bulk-delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkDeleteIds">
    </form>
    <form id="bulkReceiptForm" action="{{ route('admin.donations.bulk-receipt') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkReceiptIds">
        <input type="hidden" name="status" id="bulkReceiptStatus">
    </form>

    <script>
    function toggleAdvancedFilters() {
        const advanced = document.getElementById('advancedFilters');
        advanced.style.display = advanced.style.display === 'none' ? 'flex' : 'none';
    }

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const selectionBar = document.getElementById('selectionBar');
        const selectedCount = document.getElementById('selectedCount');

        if (checkboxes.length > 0) {
            selectionBar.style.display = 'flex';
            selectedCount.textContent = checkboxes.length;
        } else {
            selectionBar.style.display = 'none';
        }

        const allCheckboxes = document.querySelectorAll('.row-checkbox');
        const selectAll = document.getElementById('selectAll');
        selectAll.checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
        selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
    }

    function getSelectedIds() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value).join(',');
    }

    function exportSelected() {
        const ids = getSelectedIds();
        if (ids) {
            window.location.href = '{{ route('admin.donations.export') }}?ids=' + ids;
        }
    }

    function bulkDelete() {
        const ids = getSelectedIds();
        if (ids && confirm('Supprimer ' + ids.split(',').length + ' don(s) ?')) {
            document.getElementById('bulkDeleteIds').value = ids;
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    function bulkReceipt(status) {
        const ids = getSelectedIds();
        const action = status === 'sent' ? 'comme envoyes' : 'comme non envoyes';
        if (ids && confirm('Marquer ' + ids.split(',').length + ' recu(s) ' + action + ' ?')) {
            document.getElementById('bulkReceiptIds').value = ids;
            document.getElementById('bulkReceiptStatus').value = status;
            document.getElementById('bulkReceiptForm').submit();
        }
    }
    </script>
@endsection
