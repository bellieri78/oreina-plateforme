@extends('layouts.admin')
@section('title', 'Reviews')
@section('breadcrumb')<span>Revue</span><span>/</span><span>Reviews</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Evaluations
            </h1>
            <p class="page-subtitle">{{ $reviews->total() }} review(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.reviews.export', request()->query()) }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter
            </a>
            <a href="{{ route('admin.reviews.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Assigner reviewer
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['total'] }}</span>
                <span class="stat-card-label">Total reviews</span>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['pending'] }}</span>
                <span class="stat-card-label">En attente</span>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['completed'] }}</span>
                <span class="stat-card-label">Completees</span>
            </div>
        </div>

        <div class="stat-card stat-card-danger">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['overdue'] }}</span>
                <span class="stat-card-label">En retard</span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <form action="{{ route('admin.reviews.index') }}" method="GET">
            <div class="filters-bar">
                <div class="search-group">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="search-input">
                </div>

                <div class="filters-group">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="invited" {{ request('status') === 'invited' ? 'selected' : '' }}>Invite</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepte</option>
                        <option value="declined" {{ request('status') === 'declined' ? 'selected' : '' }}>Decline</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Complete</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expire</option>
                    </select>

                    <select name="recommendation" class="form-select">
                        <option value="">Toutes recommandations</option>
                        <option value="accept" {{ request('recommendation') === 'accept' ? 'selected' : '' }}>Accepter</option>
                        <option value="minor_revision" {{ request('recommendation') === 'minor_revision' ? 'selected' : '' }}>Revision mineure</option>
                        <option value="major_revision" {{ request('recommendation') === 'major_revision' ? 'selected' : '' }}>Revision majeure</option>
                        <option value="reject" {{ request('recommendation') === 'reject' ? 'selected' : '' }}>Rejeter</option>
                    </select>

                    <label class="filter-checkbox">
                        <input type="checkbox" name="overdue" value="1" {{ request('overdue') === '1' ? 'checked' : '' }} onchange="this.form.submit()">
                        <span>En retard uniquement</span>
                    </label>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    @if(request()->hasAny(['search', 'status', 'recommendation', 'overdue']))
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-ghost">Reset</a>
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
                        <th>Soumission</th>
                        <th>Reviewer</th>
                        <th>Echeance</th>
                        <th>Statut</th>
                        <th>Recommandation</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $r)
                        @php
                            $isOverdue = $r->due_date && $r->due_date < now() && in_array($r->status, ['invited', 'accepted']);
                        @endphp
                        <tr class="{{ $isOverdue ? 'row-warning' : '' }}">
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $r->id }}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                <a href="{{ route('admin.submissions.show', $r->submission_id) }}" class="article-title">
                                    {{ Str::limit($r->submission?->title ?? '-', 35) }}
                                </a>
                            </td>
                            <td>
                                @if($r->reviewer)
                                    <div class="contact-cell">
                                        <div class="contact-avatar-small">
                                            {{ strtoupper(substr($r->reviewer->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $r->reviewer->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($r->due_date)
                                    <span class="{{ $isOverdue ? 'text-danger' : 'text-muted' }}" style="{{ $isOverdue ? 'font-weight: 600;' : '' }}">
                                        {{ $r->due_date->format('d/m/Y') }}
                                    </span>
                                    @if($isOverdue)
                                        <div style="font-size: 0.65rem; color: var(--danger-color); font-weight: 600; text-transform: uppercase;">
                                            En retard
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusLabels = [
                                        'invited' => ['Invite', 'info'],
                                        'accepted' => ['Accepte', 'primary'],
                                        'declined' => ['Decline', 'default'],
                                        'completed' => ['Complete', 'success'],
                                        'expired' => ['Expire', 'danger'],
                                    ];
                                    [$label, $color] = $statusLabels[$r->status] ?? [$r->status, 'default'];
                                @endphp
                                <span class="badge badge-{{ $color }}">{{ $label }}</span>
                            </td>
                            <td>
                                @if($r->recommendation)
                                    @php
                                        $recLabels = [
                                            'accept' => ['Accepter', 'success'],
                                            'minor_revision' => ['Rev. mineure', 'info'],
                                            'major_revision' => ['Rev. majeure', 'warning'],
                                            'reject' => ['Rejeter', 'danger'],
                                        ];
                                        [$recLabel, $recColor] = $recLabels[$r->recommendation] ?? [$r->recommendation, 'default'];
                                    @endphp
                                    <span class="badge badge-{{ $recColor }}">{{ $recLabel }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    <a href="{{ route('admin.reviews.show', $r) }}" class="btn btn-ghost btn-sm" title="Voir">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.reviews.edit', $r) }}" class="btn btn-ghost btn-sm" title="Modifier">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <h3 class="empty-state-title">Aucune review</h3>
                                    <p class="empty-state-description">Aucune review ne correspond a vos criteres.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($reviews->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Affichage de {{ $reviews->firstItem() }} a {{ $reviews->lastItem() }} sur {{ $reviews->total() }} resultats
                </div>
                <div class="pagination">
                    @if($reviews->onFirstPage())
                        <span class="pagination-btn disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $reviews->previousPageUrl() }}" class="pagination-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @foreach($reviews->getUrlRange(max(1, $reviews->currentPage() - 2), min($reviews->lastPage(), $reviews->currentPage() + 2)) as $page => $url)
                        @if($page == $reviews->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($reviews->hasMorePages())
                        <a href="{{ $reviews->nextPageUrl() }}" class="pagination-btn">
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
                <button type="button" class="btn btn-primary btn-sm" onclick="sendReminders()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Envoyer rappel
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

    <!-- Hidden forms -->
    <form id="bulkDeleteForm" action="{{ route('admin.reviews.bulk-delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkDeleteIds">
    </form>
    <form id="reminderForm" action="{{ route('admin.reviews.send-reminder') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="reminderIds">
    </form>

    <script>
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = selectAll.checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const selectionBar = document.getElementById('selectionBar');
        document.getElementById('selectedCount').textContent = checked.length;
        selectionBar.style.display = checked.length > 0 ? 'flex' : 'none';

        const all = document.querySelectorAll('.row-checkbox');
        const selectAll = document.getElementById('selectAll');
        selectAll.checked = checked.length === all.length && all.length > 0;
        selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
    }

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value).join(',');
    }

    function exportSelected() {
        const ids = getSelectedIds();
        if (ids) window.location.href = '{{ route('admin.reviews.export') }}?ids=' + ids;
    }

    function bulkDelete() {
        const ids = getSelectedIds();
        if (ids && confirm('Supprimer ' + ids.split(',').length + ' review(s) ?')) {
            document.getElementById('bulkDeleteIds').value = ids;
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    function sendReminders() {
        const ids = getSelectedIds();
        if (ids && confirm('Envoyer un rappel a ' + ids.split(',').length + ' reviewer(s) ?')) {
            document.getElementById('reminderIds').value = ids;
            document.getElementById('reminderForm').submit();
        }
    }
    </script>
@endsection
