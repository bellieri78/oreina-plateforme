@extends('layouts.admin')
@section('title', 'Evenements')
@section('breadcrumb')<span>Evenements</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Evenements
            </h1>
            <p class="page-subtitle">{{ $events->total() }} evenement(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.events.export', request()->query()) }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter
            </a>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvel evenement
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['total'] }}</span>
                <span class="stat-card-label">Total evenements</span>
            </div>
        </div>

        <div class="stat-card stat-card-info">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['upcoming'] }}</span>
                <span class="stat-card-label">A venir</span>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['published'] }}</span>
                <span class="stat-card-label">Publies</span>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['this_month'] }}</span>
                <span class="stat-card-label">Ce mois</span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <form action="{{ route('admin.events.index') }}" method="GET" id="filterForm">
            <div class="filters-bar">
                <div class="search-group">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un evenement..." class="search-input">
                </div>

                <div class="filters-group">
                    <select name="status" class="form-select">
                        <option value="">Tous statuts</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publie</option>
                    </select>

                    <select name="period" class="form-select">
                        <option value="">Toutes periodes</option>
                        <option value="upcoming" {{ request('period') === 'upcoming' ? 'selected' : '' }}>A venir</option>
                        <option value="past" {{ request('period') === 'past' ? 'selected' : '' }}>Passes</option>
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
                    @if(request()->hasAny(['search', 'status', 'period', 'event_type', 'year']))
                        <a href="{{ route('admin.events.index') }}" class="btn btn-ghost">Reset</a>
                    @endif
                </div>
            </div>

            <!-- Advanced filters -->
            <div class="filters-advanced" id="advancedFilters" style="display: {{ request()->hasAny(['event_type', 'year']) ? 'flex' : 'none' }};">
                @if($eventTypes->isNotEmpty())
                    <div class="filter-group">
                        <label class="form-label">Type</label>
                        <select name="event_type" class="form-select">
                            <option value="">Tous</option>
                            @foreach($eventTypes as $type)
                                <option value="{{ $type }}" {{ request('event_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if($years->isNotEmpty())
                    <div class="filter-group">
                        <label class="form-label">Annee</label>
                        <select name="year" class="form-select">
                            <option value="">Toutes</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
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
                            <a href="{{ route('admin.events.index', array_merge(request()->query(), ['sort' => 'title', 'direction' => request('sort') === 'title' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="sort-header {{ request('sort') === 'title' ? 'active' : '' }}">
                                Evenement
                                @if(request('sort') === 'title')
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
                            <a href="{{ route('admin.events.index', array_merge(request()->query(), ['sort' => 'start_date', 'direction' => request('sort') === 'start_date' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="sort-header {{ request('sort') === 'start_date' || !request('sort') ? 'active' : '' }}">
                                Date
                                @if(request('sort') === 'start_date' || !request('sort'))
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
                        <th>Lieu</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $e)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $e->id }}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                <div class="event-cell">
                                    <div class="event-date-badge">
                                        <span class="event-date-day">{{ $e->start_date->format('d') }}</span>
                                        <span class="event-date-month">{{ strtoupper($e->start_date->locale('fr')->shortMonthName) }}</span>
                                    </div>
                                    <div class="event-info">
                                        <a href="{{ route('admin.events.show', $e) }}" class="event-title">
                                            {{ Str::limit($e->title, 50) }}
                                        </a>
                                        @if($e->location_city)
                                            <span class="event-location">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                </svg>
                                                {{ $e->location_city }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="event-datetime">
                                    <span class="event-date-main">{{ $e->start_date->format('d/m/Y') }}</span>
                                    <span class="event-time">{{ $e->start_date->format('H:i') }}</span>
                                </div>
                                @if($e->end_date && $e->end_date->format('Y-m-d') !== $e->start_date->format('Y-m-d'))
                                    <div class="event-date-end">au {{ $e->end_date->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td>
                                @if($e->location_city)
                                    <span class="text-muted">{{ $e->location_city }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($e->event_type)
                                    <span class="badge badge-outline">{{ $e->event_type }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="event-badges">
                                    @if($e->status === 'published')
                                        <span class="badge badge-success">Publie</span>
                                    @else
                                        <span class="badge badge-default">Brouillon</span>
                                    @endif
                                    @if($e->start_date > now())
                                        <span class="badge badge-info">A venir</span>
                                    @elseif($e->end_date && $e->end_date > now())
                                        <span class="badge badge-warning">En cours</span>
                                    @else
                                        <span class="badge badge-default">Passe</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    <a href="{{ route('admin.events.show', $e) }}" class="btn btn-ghost btn-sm" title="Voir">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.events.edit', $e) }}" class="btn btn-ghost btn-sm" title="Modifier">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.events.destroy', $e) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet evenement ?');">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="empty-state-title">Aucun evenement trouve</h3>
                                    <p class="empty-state-description">Aucun evenement ne correspond a vos criteres de recherche.</p>
                                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Creer un evenement
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($events->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Affichage de {{ $events->firstItem() }} a {{ $events->lastItem() }} sur {{ $events->total() }} resultats
                </div>
                <div class="pagination">
                    @if($events->onFirstPage())
                        <span class="pagination-btn disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $events->previousPageUrl() }}" class="pagination-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @foreach($events->getUrlRange(max(1, $events->currentPage() - 2), min($events->lastPage(), $events->currentPage() + 2)) as $page => $url)
                        @if($page == $events->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($events->hasMorePages())
                        <a href="{{ $events->nextPageUrl() }}" class="pagination-btn">
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
                <select id="bulkStatusSelect" class="form-select" style="width: auto;" onchange="bulkStatus()">
                    <option value="">Changer statut...</option>
                    <option value="draft">Brouillon</option>
                    <option value="published">Publie</option>
                </select>
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
    <form id="bulkDeleteForm" action="{{ route('admin.events.bulk-delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkDeleteIds">
    </form>
    <form id="bulkStatusForm" action="{{ route('admin.events.bulk-status') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkStatusIds">
        <input type="hidden" name="status" id="bulkStatusValue">
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
            window.location.href = '{{ route('admin.events.export') }}?ids=' + ids;
        }
    }

    function bulkDelete() {
        const ids = getSelectedIds();
        if (ids && confirm('Supprimer ' + ids.split(',').length + ' evenement(s) ?')) {
            document.getElementById('bulkDeleteIds').value = ids;
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    function bulkStatus() {
        const select = document.getElementById('bulkStatusSelect');
        const status = select.value;
        if (!status) return;

        const ids = getSelectedIds();
        if (ids && confirm('Changer le statut de ' + ids.split(',').length + ' evenement(s) en "' + status + '" ?')) {
            document.getElementById('bulkStatusIds').value = ids;
            document.getElementById('bulkStatusValue').value = status;
            document.getElementById('bulkStatusForm').submit();
        }
        select.value = '';
    }
    </script>
@endsection
