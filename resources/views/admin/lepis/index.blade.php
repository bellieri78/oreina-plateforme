@extends('layouts.admin')
@section('title', 'Bulletins Lepis')
@section('breadcrumb')<span>Lepis</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                Bulletins Lepis
            </h1>
            <p class="page-subtitle">{{ $bulletins->total() }} bulletin(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.lepis-suggestions.index') }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                Suggestions
            </a>
            <a href="{{ route('admin.lepis.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau bulletin
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
                <span class="stat-card-label">Total bulletins</span>
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
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <form action="{{ route('admin.lepis.index') }}" method="GET" id="filterForm">
            <div class="filters-bar">
                <div class="search-group">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un bulletin..." class="search-input">
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    @if(request()->hasAny(['search']))
                        <a href="{{ route('admin.lepis.index') }}" class="btn btn-ghost">Reset</a>
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
                        <th>
                            <a href="{{ route('admin.lepis.index', array_merge(request()->query(), ['sort' => 'title', 'direction' => request('sort') === 'title' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="sort-header {{ request('sort') === 'title' ? 'active' : '' }}">
                                Titre
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
                        <th>N&deg;</th>
                        <th>Trimestre / Annee</th>
                        <th>Statut</th>
                        <th>Date publication</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bulletins as $bulletin)
                        <tr>
                            <td><strong>{{ $bulletin->title }}</strong></td>
                            <td>
                                <span class="badge badge-outline">{{ $bulletin->issue_number }}</span>
                            </td>
                            <td>
                                <span>{{ $bulletin->quarter_label }} {{ $bulletin->year }}</span>
                            </td>
                            <td>
                                @if($bulletin->is_published)
                                    <span class="badge badge-success">Publie</span>
                                @else
                                    <span class="badge badge-default">Brouillon</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted">{{ $bulletin->published_at ? $bulletin->published_at->format('d/m/Y') : '-' }}</span>
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    @if($bulletin->pdf_path)
                                        <a href="{{ Storage::url($bulletin->pdf_path) }}" target="_blank" class="btn btn-ghost btn-sm" title="Voir PDF">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.lepis.toggle-publish', $bulletin) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-sm" title="{{ $bulletin->is_published ? 'Depublier' : 'Publier' }}">
                                            @if($bulletin->is_published)
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                </svg>
                                            @else
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.lepis.edit', $bulletin) }}" class="btn btn-ghost btn-sm" title="Modifier">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.lepis.destroy', $bulletin) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce bulletin ?');">
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
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                        </svg>
                                    </div>
                                    <h3 class="empty-state-title">Aucun bulletin</h3>
                                    <p class="empty-state-description">Aucun bulletin Lepis n'a encore ete cree.</p>
                                    <a href="{{ route('admin.lepis.create') }}" class="btn btn-primary">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Creer un bulletin
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bulletins->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Affichage de {{ $bulletins->firstItem() }} a {{ $bulletins->lastItem() }} sur {{ $bulletins->total() }} resultats
                </div>
                <div class="pagination">
                    @if($bulletins->onFirstPage())
                        <span class="pagination-btn disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $bulletins->previousPageUrl() }}" class="pagination-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @foreach($bulletins->getUrlRange(max(1, $bulletins->currentPage() - 2), min($bulletins->lastPage(), $bulletins->currentPage() + 2)) as $page => $url)
                        @if($page == $bulletins->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($bulletins->hasMorePages())
                        <a href="{{ $bulletins->nextPageUrl() }}" class="pagination-btn">
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
@endsection
