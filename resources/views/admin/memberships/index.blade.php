@extends('layouts.admin')
@section('title', 'Adhesions')
@section('breadcrumb')<span>Adhesions</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                </svg>
                Adhesions
            </h1>
            <p class="page-subtitle">{{ $memberships->total() }} adhesion(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.memberships.export', request()->query()) }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter
            </a>
            <a href="{{ route('admin.memberships.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle adhesion
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card stat-card-success">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $activeCount }}</span>
                <span class="stat-card-label">Adhesions actives</span>
            </div>
        </div>

        <div class="stat-card stat-card-danger">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $expiredCount }}</span>
                <span class="stat-card-label">Adhesions expirees</span>
            </div>
        </div>

        <div class="stat-card stat-card-primary">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $expiringCount ?? 0 }}</span>
                <span class="stat-card-label">Expirent bientot</span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <form action="{{ route('admin.memberships.index') }}" method="GET" id="filterForm">
            <div class="filters-bar">
                <div class="search-group">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher adherent..." class="search-input">
                </div>

                <div class="filters-group">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actives</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expirees</option>
                    </select>

                    <select name="year" class="form-select">
                        <option value="">Toutes les annees</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
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
                    @if(request()->hasAny(['search', 'status', 'year', 'type', 'payment_method', 'date_from', 'date_to']))
                        <a href="{{ route('admin.memberships.index') }}" class="btn btn-ghost">Reset</a>
                    @endif
                </div>
            </div>

            <!-- Advanced filters -->
            <div class="filters-advanced" id="advancedFilters" style="display: {{ request()->hasAny(['type', 'payment_method', 'date_from', 'date_to']) ? 'flex' : 'none' }};">
                <div class="filter-group">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">Tous</option>
                        @foreach($types as $id => $name)
                            <option value="{{ $id }}" {{ request('type') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
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
                    <label class="form-label">Date debut du</label>
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
                        <th>Membre</th>
                        <th>Type</th>
                        <th>
                            <a href="{{ route('admin.memberships.index', array_merge(request()->query(), ['sort' => 'start_date', 'direction' => request('sort') === 'start_date' && request('direction') === 'desc' ? 'asc' : 'desc'])) }}" class="sort-header {{ request('sort') === 'start_date' ? 'active' : '' }}">
                                Periode
                                @if(request('sort') === 'start_date')
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
                            <a href="{{ route('admin.memberships.index', array_merge(request()->query(), ['sort' => 'amount_paid', 'direction' => request('sort') === 'amount_paid' && request('direction') === 'desc' ? 'asc' : 'desc'])) }}" class="sort-header {{ request('sort') === 'amount_paid' ? 'active' : '' }}">
                                Montant
                                @if(request('sort') === 'amount_paid')
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
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($memberships as $m)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $m->id }}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                @if($m->member)
                                    <div class="contact-cell">
                                        <div class="contact-avatar contact-avatar-member">
                                            {{ strtoupper(substr($m->member->first_name ?? $m->member->last_name, 0, 1)) }}
                                        </div>
                                        <div class="contact-info">
                                            <a href="{{ route('admin.members.show', $m->member) }}" class="contact-name">
                                                {{ $m->member->full_name }}
                                            </a>
                                            @if($m->member->email)
                                                <span class="contact-email">{{ $m->member->email }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-outline">{{ $m->membershipType?->name ?? 'Standard' }}</span>
                            </td>
                            <td>
                                <div class="date-range">
                                    <span>{{ $m->start_date->format('d/m/Y') }}</span>
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                    <span>{{ $m->end_date->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="amount">{{ number_format($m->amount_paid, 0, ',', ' ') }} EUR</span>
                            </td>
                            <td>
                                @if($m->end_date >= now())
                                    <span class="badge badge-success">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Active
                                    </span>
                                @else
                                    <span class="badge badge-danger">Expiree</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    <a href="{{ route('admin.memberships.show', $m) }}" class="btn btn-ghost btn-sm" title="Voir">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.memberships.edit', $m) }}" class="btn btn-ghost btn-sm" title="Modifier">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.memberships.destroy', $m) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cette adhesion ?');">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="empty-state-title">Aucune adhesion trouvee</h3>
                                    <p class="empty-state-description">Aucune adhesion ne correspond a vos criteres de recherche.</p>
                                    <a href="{{ route('admin.memberships.create') }}" class="btn btn-primary">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Ajouter une adhesion
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($memberships->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Affichage de {{ $memberships->firstItem() }} a {{ $memberships->lastItem() }} sur {{ $memberships->total() }} resultats
                </div>
                <div class="pagination">
                    @if($memberships->onFirstPage())
                        <span class="pagination-btn disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $memberships->previousPageUrl() }}" class="pagination-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @foreach($memberships->getUrlRange(max(1, $memberships->currentPage() - 2), min($memberships->lastPage(), $memberships->currentPage() + 2)) as $page => $url)
                        @if($page == $memberships->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($memberships->hasMorePages())
                        <a href="{{ $memberships->nextPageUrl() }}" class="pagination-btn">
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
                <button type="button" class="btn btn-danger btn-sm" onclick="bulkDelete()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden form for bulk delete -->
    <form id="bulkDeleteForm" action="{{ route('admin.memberships.bulk-delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkDeleteIds">
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
            window.location.href = '{{ route('admin.memberships.export') }}?ids=' + ids;
        }
    }

    function bulkDelete() {
        const ids = getSelectedIds();
        if (ids && confirm('Supprimer ' + ids.split(',').length + ' adhesion(s) ?')) {
            document.getElementById('bulkDeleteIds').value = ids;
            document.getElementById('bulkDeleteForm').submit();
        }
    }
    </script>
@endsection
