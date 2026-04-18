@extends('layouts.admin')
@section('title', 'Soumissions')
@section('breadcrumb')<span>Revue</span><span>/</span><span>Soumissions</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Soumissions
            </h1>
            <p class="page-subtitle">{{ $submissions->total() }} manuscrit(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.submissions.export', request()->query()) }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter
            </a>
            <a href="{{ route('admin.submissions.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle soumission
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards" style="grid-template-columns: repeat(5, 1fr);">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['total'] }}</span>
                <span class="stat-card-label">Total</span>
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

        <div class="stat-card stat-card-info">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['revision'] }}</span>
                <span class="stat-card-label">Revision</span>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['accepted'] }}</span>
                <span class="stat-card-label">Acceptes</span>
            </div>
        </div>

        <div class="stat-card stat-card-turquoise">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['published'] }}</span>
                <span class="stat-card-label">Publies</span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <form action="{{ route('admin.submissions.index') }}" method="GET">
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
                        @foreach(\App\Models\Submission::getStatuses() as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <select name="journal_issue_id" class="form-select">
                        <option value="">Tous les numeros</option>
                        @foreach($issues as $issue)
                            <option value="{{ $issue->id }}" {{ request('journal_issue_id') == $issue->id ? 'selected' : '' }}>
                                Vol.{{ $issue->volume_number }} N&deg;{{ $issue->issue_number }}
                            </option>
                        @endforeach
                    </select>

                    <select name="decision" class="form-select">
                        <option value="">Toutes decisions</option>
                        @foreach(\App\Models\Submission::getDecisions() as $key => $label)
                            <option value="{{ $key }}" {{ request('decision') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    @if(request()->hasAny(['search', 'status', 'journal_issue_id', 'decision']))
                        <a href="{{ route('admin.submissions.index') }}" class="btn btn-ghost">Reset</a>
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
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Numero</th>
                        <th>Reviews</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $s)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $s->id }}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                <div class="article-cell">
                                    <a href="{{ route('admin.submissions.show', $s) }}" class="article-title">
                                        {{ Str::limit($s->title, 40) }}
                                    </a>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem; margin-top: 0.25rem;">
                                    Soumis le {{ $s->submitted_at?->format('d/m/Y') ?? $s->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                @if($s->author)
                                    <div class="contact-cell">
                                        <div class="contact-avatar-small">
                                            {{ strtoupper(substr($s->author->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $s->author->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($s->journalIssue)
                                    <span class="badge badge-info">Vol.{{ $s->journalIssue->volume_number }} N&deg;{{ $s->journalIssue->issue_number }}</span>
                                @else
                                    <span class="badge badge-default">Non assigne</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $s->reviews_count > 0 ? 'info' : 'default' }}">
                                    {{ $s->reviews_count }} review(s)
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'submitted' => 'info',
                                        'under_initial_review' => 'warning',
                                        'under_peer_review' => 'primary',
                                        'revision_after_review' => 'warning',
                                        'accepted' => 'success',
                                        'rejected' => 'danger',
                                        'published' => 'success',
                                    ];
                                    $sStatusValue = $s->status instanceof \App\Enums\SubmissionStatus ? $s->status->value : $s->status;
                                @endphp
                                <span class="badge badge-{{ $statusColors[$sStatusValue] ?? 'default' }}">
                                    {{ $s->status instanceof \App\Enums\SubmissionStatus ? $s->status->label() : (\App\Models\Submission::getStatuses()[$s->status] ?? $s->status) }}
                                </span>
                                @if($s->decision)
                                    <div class="text-muted" style="font-size: 0.7rem; margin-top: 0.25rem;">
                                        {{ \App\Models\Submission::getDecisions()[$s->decision] ?? $s->decision }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    <a href="{{ route('admin.submissions.show', $s) }}" class="btn btn-ghost btn-sm" title="Voir">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.submissions.edit', $s) }}" class="btn btn-ghost btn-sm" title="Modifier">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if($s->canBeReviewed())
                                        <a href="{{ route('admin.reviews.create', ['submission_id' => $s->id]) }}" class="btn btn-primary btn-sm" title="Assigner review">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="empty-state-title">Aucune soumission</h3>
                                    <p class="empty-state-description">Aucune soumission ne correspond a vos criteres.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($submissions->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Affichage de {{ $submissions->firstItem() }} a {{ $submissions->lastItem() }} sur {{ $submissions->total() }} resultats
                </div>
                <div class="pagination">
                    @if($submissions->onFirstPage())
                        <span class="pagination-btn disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $submissions->previousPageUrl() }}" class="pagination-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @foreach($submissions->getUrlRange(max(1, $submissions->currentPage() - 2), min($submissions->lastPage(), $submissions->currentPage() + 2)) as $page => $url)
                        @if($page == $submissions->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($submissions->hasMorePages())
                        <a href="{{ $submissions->nextPageUrl() }}" class="pagination-btn">
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
                    @foreach(\App\Models\Submission::getStatuses() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select id="bulkIssueSelect" class="form-select" style="width: auto;" onchange="bulkIssue()">
                    <option value="">Assigner numero...</option>
                    @foreach($issues as $issue)
                        <option value="{{ $issue->id }}">Vol.{{ $issue->volume_number }} N&deg;{{ $issue->issue_number }}</option>
                    @endforeach
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

    <!-- Hidden forms -->
    <form id="bulkDeleteForm" action="{{ route('admin.submissions.bulk-delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkDeleteIds">
    </form>
    <form id="bulkStatusForm" action="{{ route('admin.submissions.bulk-status') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkStatusIds">
        <input type="hidden" name="status" id="bulkStatusValue">
    </form>
    <form id="bulkIssueForm" action="{{ route('admin.submissions.bulk-assign-issue') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkIssueIds">
        <input type="hidden" name="journal_issue_id" id="bulkIssueValue">
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
        if (ids) window.location.href = '{{ route('admin.submissions.export') }}?ids=' + ids;
    }

    function bulkDelete() {
        const ids = getSelectedIds();
        if (ids && confirm('Supprimer ' + ids.split(',').length + ' soumission(s) ?')) {
            document.getElementById('bulkDeleteIds').value = ids;
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    function bulkStatus() {
        const select = document.getElementById('bulkStatusSelect');
        const status = select.value;
        if (!status) return;
        const ids = getSelectedIds();
        if (ids && confirm('Changer le statut de ' + ids.split(',').length + ' soumission(s) ?')) {
            document.getElementById('bulkStatusIds').value = ids;
            document.getElementById('bulkStatusValue').value = status;
            document.getElementById('bulkStatusForm').submit();
        }
        select.value = '';
    }

    function bulkIssue() {
        const select = document.getElementById('bulkIssueSelect');
        const issue = select.value;
        if (!issue) return;
        const ids = getSelectedIds();
        if (ids && confirm('Assigner ' + ids.split(',').length + ' soumission(s) a ce numero ?')) {
            document.getElementById('bulkIssueIds').value = ids;
            document.getElementById('bulkIssueValue').value = issue;
            document.getElementById('bulkIssueForm').submit();
        }
        select.value = '';
    }
    </script>
@endsection
