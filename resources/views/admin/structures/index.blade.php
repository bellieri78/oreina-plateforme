@extends('layouts.admin')

@section('title', 'Structures')

@section('breadcrumb')
    <span>Structures & Organismes</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">
            <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Structures & Organismes
        </h1>
        <p class="page-subtitle">{{ $stats['total'] }} structure(s)</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.structures.export', request()->query()) }}" class="btn btn-outline">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Exporter
        </a>
        <a href="{{ route('admin.structures.create') }}" class="btn btn-primary">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle structure
        </a>
    </div>
</div>

{{-- Stats cards --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total structures</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['regional'] }}</div>
            <div class="stat-label">Regionales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['departemental'] }}</div>
            <div class="stat-label">Departementales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['local'] }}</div>
            <div class="stat-label">Locales</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <form method="GET" action="{{ route('admin.structures.index') }}" class="filters-bar">
        <div class="filters-row">
            <div class="filter-group search-group">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une structure..." class="form-input">
            </div>

            <div class="filter-group">
                <select name="type" class="form-select">
                    <option value="">Tous les types</option>
                    @foreach(\App\Models\Structure::getTypes() as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <select name="parent" class="form-select">
                    <option value="">Toutes les structures</option>
                    <option value="root" {{ request('parent') == 'root' ? 'selected' : '' }}>Structures racines</option>
                    @foreach($parentStructures as $id => $name)
                        <option value="{{ $id }}" {{ request('parent') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'type', 'parent', 'status']))
                <a href="{{ route('admin.structures.index') }}" class="btn btn-ghost btn-sm">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Effacer
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- View toggle & content --}}
<div class="card">
    <div class="view-toggle">
        <div class="view-toggle-buttons">
            <a href="{{ route('admin.structures.index', array_merge(request()->query(), ['view' => 'grid'])) }}"
               class="view-toggle-btn {{ $viewMode === 'grid' ? 'active' : '' }}" title="Vue grille">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </a>
            <a href="{{ route('admin.structures.index', array_merge(request()->query(), ['view' => 'list'])) }}"
               class="view-toggle-btn {{ $viewMode === 'list' ? 'active' : '' }}" title="Vue liste">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </a>
            <a href="{{ route('admin.structures.index', array_merge(request()->query(), ['view' => 'tree'])) }}"
               class="view-toggle-btn {{ $viewMode === 'tree' ? 'active' : '' }}" title="Vue arbre">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
            </a>
        </div>
        <span class="view-toggle-info">{{ $structures->count() }} resultat(s)</span>
    </div>

    @if($structures->isEmpty())
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <h3 class="empty-state-title">Aucune structure trouvee</h3>
        <p class="empty-state-description">
            @if(request()->hasAny(['search', 'type', 'parent', 'status']))
                Modifiez vos filtres ou creez une nouvelle structure.
            @else
                Commencez par creer votre premiere structure.
            @endif
        </p>
        <a href="{{ route('admin.structures.create') }}" class="btn btn-primary">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle structure
        </a>
    </div>
    @elseif($viewMode === 'grid')
    {{-- Grid view --}}
    <div class="structures-grid">
        @foreach($structures as $structure)
        @php
            $typeColors = [
                'national' => '#dc2626',
                'regional' => '#d97706',
                'departemental' => '#0891b2',
                'local' => '#7c3aed',
            ];
            $typeIcons = [
                'national' => 'globe',
                'regional' => 'map',
                'departemental' => 'map-pin',
                'local' => 'home',
            ];
            $color = $typeColors[$structure->type] ?? '#6b7280';
        @endphp
        <a href="{{ route('admin.structures.show', $structure) }}" class="structure-card">
            <div class="structure-header">
                <div class="structure-icon" style="background: {{ $color }};">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @switch($structure->type)
                            @case('national')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @break
                            @case('regional')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                @break
                            @case('departemental')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                @break
                            @default
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        @endswitch
                    </svg>
                </div>
                <div class="structure-type-badge">{{ $structure->type_label }}</div>
            </div>
            <div class="structure-body">
                <h3 class="structure-name">{{ $structure->name }}</h3>
                @if($structure->code)
                <p class="structure-code">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                    {{ $structure->code }}
                </p>
                @endif
                @if($structure->city || $structure->departement_code)
                <p class="structure-location">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    {{ $structure->city ?? '' }}
                    @if($structure->departement_code)
                        ({{ $structure->departement_code }})
                    @endif
                </p>
                @endif
                @if($structure->email)
                <p class="structure-email">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $structure->email }}
                </p>
                @endif
                @if($structure->responsable)
                <p class="structure-responsable">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ $structure->responsable->full_name }}
                </p>
                @endif
            </div>
            @if($structure->active_members_count > 0 || $structure->children_count > 0 || !$structure->is_active)
            <div class="structure-footer">
                <div class="structure-tags">
                    @if(!$structure->is_active)
                    <span class="tag tag-inactive">Inactif</span>
                    @endif
                    @if($structure->active_members_count > 0)
                    <span class="tag">{{ $structure->active_members_count }} membre(s)</span>
                    @endif
                    @if($structure->children_count > 0)
                    <span class="tag">{{ $structure->children_count }} sous-structure(s)</span>
                    @endif
                </div>
            </div>
            @endif
        </a>
        @endforeach
    </div>
    @elseif($viewMode === 'tree')
    {{-- Tree view --}}
    <div class="card-body">
        <div class="structure-tree">
            @foreach($structures as $structure)
                @include('admin.structures._tree-item', ['structure' => $structure, 'depth' => 0])
            @endforeach
        </div>
    </div>
    @else
    {{-- List view --}}
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Structure</th>
                    <th>Type</th>
                    <th>Parent</th>
                    <th>Responsable</th>
                    <th>Membres</th>
                    <th>Statut</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($structures as $structure)
                @php
                    $typeColors = [
                        'national' => '#dc2626',
                        'regional' => '#d97706',
                        'departemental' => '#0891b2',
                        'local' => '#7c3aed',
                    ];
                    $color = $typeColors[$structure->type] ?? '#6b7280';
                @endphp
                <tr>
                    <td>
                        <div class="contact-cell">
                            <div class="structure-icon-sm" style="background: {{ $color }};">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="contact-info">
                                <a href="{{ route('admin.structures.show', $structure) }}" class="contact-name">
                                    {{ $structure->name }}
                                </a>
                                @if($structure->code)
                                <span class="contact-email">{{ $structure->code }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background: {{ $color }}15; color: {{ $color }};">
                            {{ $structure->type_label }}
                        </span>
                    </td>
                    <td>
                        @if($structure->parent)
                            <a href="{{ route('admin.structures.show', $structure->parent) }}" class="text-link">
                                {{ $structure->parent->name }}
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($structure->responsable)
                            <a href="{{ route('admin.members.show', $structure->responsable) }}" class="text-link">
                                {{ $structure->responsable->full_name }}
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($structure->active_members_count > 0)
                            <span class="badge badge-info">{{ $structure->active_members_count }}</span>
                        @else
                            <span class="text-muted">0</span>
                        @endif
                    </td>
                    <td>
                        @if($structure->is_active)
                            <span class="badge badge-success">Actif</span>
                        @else
                            <span class="badge badge-default">Inactif</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="table-actions">
                            <a href="{{ route('admin.structures.show', $structure) }}" class="btn btn-ghost btn-sm" title="Voir">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('admin.structures.edit', $structure) }}" class="btn btn-ghost btn-sm" title="Modifier">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($structures instanceof \Illuminate\Pagination\LengthAwarePaginator && $structures->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Affichage {{ ($structures->currentPage() - 1) * $structures->perPage() + 1 }}
            - {{ min($structures->currentPage() * $structures->perPage(), $structures->total()) }}
            sur {{ $structures->total() }}
        </div>
        <div class="pagination">
            @if($structures->onFirstPage())
                <span class="pagination-btn disabled">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </span>
            @else
                <a href="{{ $structures->previousPageUrl() }}" class="pagination-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            @endif

            @foreach($structures->getUrlRange(max(1, $structures->currentPage() - 2), min($structures->lastPage(), $structures->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="pagination-btn {{ $page == $structures->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($structures->hasMorePages())
                <a href="{{ $structures->nextPageUrl() }}" class="pagination-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span class="pagination-btn disabled">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
