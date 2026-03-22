@extends('layouts.admin')
@section('title', 'Articles')
@section('breadcrumb')<span>Articles</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                Articles
            </h1>
            <p class="page-subtitle">{{ $articles->total() }} article(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.articles.export', request()->query()) }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter
            </a>
            <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvel article
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['total'] }}</span>
                <span class="stat-card-label">Total articles</span>
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
                <span class="stat-card-value">{{ $stats['draft'] }}</span>
                <span class="stat-card-label">Brouillons</span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <form action="{{ route('admin.articles.index') }}" method="GET" id="filterForm">
            <div class="filters-bar">
                <div class="search-group">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un article..." class="search-input">
                </div>

                <div class="filters-group">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Soumis</option>
                        <option value="validated" {{ request('status') === 'validated' ? 'selected' : '' }}>Valide</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publie</option>
                    </select>

                    @if($categories->isNotEmpty())
                        <select name="category" class="form-select">
                            <option value="">Toutes categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    @endif

                    <button type="button" class="btn btn-ghost" onclick="toggleAdvancedFilters()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        Plus de filtres
                    </button>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    @if(request()->hasAny(['search', 'status', 'category', 'author_id', 'is_featured']))
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-ghost">Reset</a>
                    @endif
                </div>
            </div>

            <!-- Advanced filters -->
            <div class="filters-advanced" id="advancedFilters" style="display: {{ request()->hasAny(['author_id', 'is_featured']) ? 'flex' : 'none' }};">
                @if($authors->isNotEmpty())
                    <div class="filter-group">
                        <label class="form-label">Auteur</label>
                        <select name="author_id" class="form-select">
                            <option value="">Tous</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" {{ request('author_id') == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="filter-group">
                    <label class="form-label">Vedette</label>
                    <select name="is_featured" class="form-select">
                        <option value="">Tous</option>
                        <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>Oui</option>
                        <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>Non</option>
                    </select>
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
                            <a href="{{ route('admin.articles.index', array_merge(request()->query(), ['sort' => 'title', 'direction' => request('sort') === 'title' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="sort-header {{ request('sort') === 'title' ? 'active' : '' }}">
                                Article
                                @if(request('sort') === 'title')
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
                        <th>Auteur</th>
                        <th>Categorie</th>
                        <th>Statut</th>
                        <th>
                            <a href="{{ route('admin.articles.index', array_merge(request()->query(), ['sort' => 'published_at', 'direction' => request('sort') === 'published_at' && request('direction') === 'desc' ? 'asc' : 'desc'])) }}" class="sort-header {{ request('sort') === 'published_at' ? 'active' : '' }}">
                                Date
                                @if(request('sort') === 'published_at')
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
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $a)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $a->id }}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                <div class="article-cell">
                                    <a href="{{ route('admin.articles.show', $a) }}" class="article-title">
                                        {{ Str::limit($a->title, 50) }}
                                    </a>
                                    @if($a->is_featured)
                                        <span class="badge badge-warning">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                            Vedette
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($a->author)
                                    <div class="contact-cell">
                                        <div class="contact-avatar contact-avatar-small">
                                            {{ strtoupper(substr($a->author->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $a->author->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($a->category)
                                    <span class="badge badge-outline">{{ $a->category }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @switch($a->status)
                                    @case('published')
                                        <span class="badge badge-success">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Publie
                                        </span>
                                        @break
                                    @case('validated')
                                        <span class="badge badge-info">Valide</span>
                                        @break
                                    @case('submitted')
                                        <span class="badge badge-warning">Soumis</span>
                                        @break
                                    @default
                                        <span class="badge badge-default">Brouillon</span>
                                @endswitch
                            </td>
                            <td>
                                <span class="text-muted">{{ $a->published_at?->format('d/m/Y') ?? $a->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    <a href="{{ route('admin.articles.show', $a) }}" class="btn btn-ghost btn-sm" title="Voir">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.articles.edit', $a) }}" class="btn btn-ghost btn-sm" title="Modifier">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.articles.destroy', $a) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet article ?');">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                        </svg>
                                    </div>
                                    <h3 class="empty-state-title">Aucun article trouve</h3>
                                    <p class="empty-state-description">Aucun article ne correspond a vos criteres de recherche.</p>
                                    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Creer un article
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Affichage de {{ $articles->firstItem() }} a {{ $articles->lastItem() }} sur {{ $articles->total() }} resultats
                </div>
                <div class="pagination">
                    @if($articles->onFirstPage())
                        <span class="pagination-btn disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $articles->previousPageUrl() }}" class="pagination-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @foreach($articles->getUrlRange(max(1, $articles->currentPage() - 2), min($articles->lastPage(), $articles->currentPage() + 2)) as $page => $url)
                        @if($page == $articles->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($articles->hasMorePages())
                        <a href="{{ $articles->nextPageUrl() }}" class="pagination-btn">
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
                    <option value="submitted">Soumis</option>
                    <option value="validated">Valide</option>
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
    <form id="bulkDeleteForm" action="{{ route('admin.articles.bulk-delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkDeleteIds">
    </form>
    <form id="bulkStatusForm" action="{{ route('admin.articles.bulk-status') }}" method="POST" style="display: none;">
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
            window.location.href = '{{ route('admin.articles.export') }}?ids=' + ids;
        }
    }

    function bulkDelete() {
        const ids = getSelectedIds();
        if (ids && confirm('Supprimer ' + ids.split(',').length + ' article(s) ?')) {
            document.getElementById('bulkDeleteIds').value = ids;
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    function bulkStatus() {
        const select = document.getElementById('bulkStatusSelect');
        const status = select.value;
        if (!status) return;

        const ids = getSelectedIds();
        if (ids && confirm('Changer le statut de ' + ids.split(',').length + ' article(s) en "' + status + '" ?')) {
            document.getElementById('bulkStatusIds').value = ids;
            document.getElementById('bulkStatusValue').value = status;
            document.getElementById('bulkStatusForm').submit();
        }
        select.value = '';
    }
    </script>
@endsection
