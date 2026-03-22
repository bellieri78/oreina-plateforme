@extends('layouts.admin')

@section('title', 'Contacts')
@section('breadcrumb')
    <span>Contacts</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">
            <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Contacts
        </h1>
        <p class="page-subtitle">{{ number_format($members->total()) }} contact(s)</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.members.export', request()->query()) }}" class="btn btn-outline">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Exporter
        </a>
        <a href="{{ route('admin.members.import') }}" class="btn btn-outline">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Importer
        </a>
        <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau contact
        </a>
    </div>
</div>

{{-- Import errors --}}
@if(session('import_errors'))
<div class="alert alert-danger mb-4">
    <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <strong>Erreurs d'import :</strong>
        <ul class="mt-2">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

{{-- Filters --}}
<div class="card mb-4">
    <form action="{{ route('admin.members.index') }}" method="GET" id="filterForm" class="filters-bar">
        <div class="filters-row">
            <div class="filter-group search-group">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher nom, email..." class="form-input">
            </div>

            <div class="filter-group">
                <select name="contact_type" class="form-select">
                    <option value="">Tous les types</option>
                    @foreach($contactTypes ?? [] as $type)
                        <option value="{{ $type }}" {{ request('contact_type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
                </select>
            </div>

            <div class="filter-group">
                <select name="membership" class="form-select">
                    <option value="">Toutes adhesions</option>
                    <option value="active" {{ request('membership') === 'active' ? 'selected' : '' }}>Adhesion active</option>
                    <option value="expired" {{ request('membership') === 'expired' ? 'selected' : '' }}>Adhesion expiree</option>
                    <option value="none" {{ request('membership') === 'none' ? 'selected' : '' }}>Sans adhesion</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="button" class="btn btn-outline btn-sm" onclick="toggleAdvancedFilters()">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    Plus de filtres
                </button>
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'status', 'membership', 'contact_type', 'city', 'country', 'date_from', 'date_to']))
                <a href="{{ route('admin.members.index') }}" class="btn btn-ghost btn-sm">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Effacer
                </a>
                @endif
            </div>
        </div>

        {{-- Advanced filters --}}
        <div class="filters-row mt-3" id="advancedFilters" style="display: {{ request()->hasAny(['city', 'country', 'date_from', 'date_to']) ? 'flex' : 'none' }};">
            <div class="filter-group">
                <input type="text" name="city" value="{{ request('city') }}" placeholder="Ville..." class="form-input">
            </div>
            <div class="filter-group">
                <select name="country" class="form-select">
                    <option value="">Tous les pays</option>
                    @foreach($countries as $country)
                        <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input" placeholder="Date du">
            </div>
            <div class="filter-group">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input" placeholder="Date au">
            </div>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card">
    @if($members->isEmpty())
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h3 class="empty-state-title">Aucun contact trouve</h3>
        <p class="empty-state-description">
            @if(request()->hasAny(['search', 'status', 'membership']))
                Aucun contact ne correspond a vos criteres de recherche.
            @else
                Commencez par ajouter votre premier contact.
            @endif
        </p>
        <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau contact
        </a>
    </div>
    @else
    <div class="table-container">
        <table class="data-table" id="membersTable">
            <thead>
                <tr>
                    <th class="checkbox-col">
                        <input type="checkbox" id="selectAll" class="checkbox-select-all">
                    </th>
                    <th>
                        <a href="{{ route('admin.members.index', array_merge(request()->query(), ['sort' => 'last_name', 'direction' => request('sort') === 'last_name' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="sort-header">
                            Nom
                            @if(request('sort') === 'last_name')
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if(request('direction') === 'desc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    @endif
                                </svg>
                            @endif
                        </a>
                    </th>
                    <th>Type</th>
                    <th>Ville</th>
                    <th>Adhesion</th>
                    <th>Statut</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $member)
                <tr data-member-id="{{ $member->id }}">
                    <td class="checkbox-col">
                        <input type="checkbox" class="checkbox-select-row" value="{{ $member->id }}">
                    </td>
                    <td>
                        <div class="contact-cell">
                            <div class="contact-avatar {{ strtolower($member->contact_type ?? 'autre') }}">
                                {{ strtoupper(substr($member->last_name ?? $member->first_name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="contact-info">
                                <a href="{{ route('admin.members.show', $member) }}" class="contact-name">
                                    {{ $member->last_name }} {{ $member->first_name }}
                                </a>
                                @if($member->email)
                                <span class="contact-email">{{ $member->email }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($member->contact_type)
                        <span class="badge badge-type-{{ strtolower(str_replace(['é', 'è'], 'e', $member->contact_type)) }}">
                            {{ $member->contact_type }}
                        </span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($member->city)
                            {{ $member->city }}
                            @if($member->postal_code)
                                <span class="text-muted">({{ substr($member->postal_code, 0, 2) }})</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $activeMembership = $member->memberships?->first(fn($m) => $m->end_date >= now());
                        @endphp
                        @if($activeMembership)
                            <span class="badge badge-success">Active</span>
                        @elseif($member->memberships?->count() > 0)
                            <span class="badge badge-default">Expiree</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($member->is_active)
                            <span class="badge badge-success">Actif</span>
                        @else
                            <span class="badge badge-default">Inactif</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="table-actions">
                            <a href="{{ route('admin.members.show', $member) }}" class="btn btn-ghost btn-sm" title="Voir">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('admin.members.edit', $member) }}" class="btn btn-ghost btn-sm" title="Modifier">
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

    @if($members->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Affichage {{ ($members->currentPage() - 1) * $members->perPage() + 1 }}
            - {{ min($members->currentPage() * $members->perPage(), $members->total()) }}
            sur {{ $members->total() }}
        </div>
        <div class="pagination">
            @if($members->onFirstPage())
                <span class="pagination-btn disabled">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </span>
            @else
                <a href="{{ $members->previousPageUrl() }}" class="pagination-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            @endif

            @foreach($members->getUrlRange(max(1, $members->currentPage() - 2), min($members->lastPage(), $members->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="pagination-btn {{ $page == $members->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($members->hasMorePages())
                <a href="{{ $members->nextPageUrl() }}" class="pagination-btn">
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
    @endif
</div>

{{-- Selection bar --}}
<div class="selection-bar" id="selectionBar">
    <div class="selection-bar-content">
        <span class="selection-count">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span id="selectedCount">0</span> contact(s) selectionne(s)
        </span>
        <div class="selection-actions">
            <div class="dropdown">
                <button class="btn btn-sm" onclick="toggleDropdown(this)">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exporter
                </button>
                <div class="dropdown-menu dropdown-menu-top">
                    <a href="#" class="dropdown-item" onclick="exportSelection('csv'); return false;">
                        <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export CSV
                    </a>
                    <a href="#" class="dropdown-item" onclick="exportSelection('xlsx'); return false;">
                        <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>
            <button class="btn btn-ghost btn-sm" onclick="clearSelection()">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Annuler
            </button>
        </div>
    </div>
</div>

{{-- Hidden forms --}}
<form id="bulkDeleteForm" action="{{ route('admin.members.bulk-delete') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkDeleteIds">
</form>

<script>
let selectedIds = new Set();

// Toggle advanced filters
function toggleAdvancedFilters() {
    const advanced = document.getElementById('advancedFilters');
    advanced.style.display = advanced.style.display === 'none' ? 'flex' : 'none';
}

// Dropdown toggle
function toggleDropdown(btn) {
    const menu = btn.nextElementSibling;
    const isOpen = menu.classList.contains('show');
    document.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
    if (!isOpen) {
        menu.classList.add('show');
    }
}

// Close dropdowns on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
    }
});

// Select all checkbox
const selectAllCheckbox = document.getElementById('selectAll');
if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.checkbox-select-row');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            const id = parseInt(cb.value);
            if (this.checked) {
                selectedIds.add(id);
                cb.closest('tr').classList.add('selected');
            } else {
                selectedIds.delete(id);
                cb.closest('tr').classList.remove('selected');
            }
        });
        updateSelectionBar();
    });
}

// Individual checkboxes
document.querySelectorAll('.checkbox-select-row').forEach(cb => {
    cb.addEventListener('change', function() {
        const id = parseInt(this.value);
        if (this.checked) {
            selectedIds.add(id);
            this.closest('tr').classList.add('selected');
        } else {
            selectedIds.delete(id);
            this.closest('tr').classList.remove('selected');
        }

        // Update select all state
        const allCheckboxes = document.querySelectorAll('.checkbox-select-row');
        const checkedCount = document.querySelectorAll('.checkbox-select-row:checked').length;
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = checkedCount === allCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < allCheckboxes.length;
        }

        updateSelectionBar();
    });
});

function updateSelectionBar() {
    const selectionBar = document.getElementById('selectionBar');
    const selectedCount = document.getElementById('selectedCount');

    if (selectedIds.size > 0) {
        selectionBar.classList.add('show');
        selectedCount.textContent = selectedIds.size;
    } else {
        selectionBar.classList.remove('show');
    }
}

function clearSelection() {
    selectedIds.clear();
    document.querySelectorAll('.checkbox-select-row').forEach(cb => {
        cb.checked = false;
        cb.closest('tr').classList.remove('selected');
    });
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    }
    updateSelectionBar();
}

function exportSelection(format) {
    if (selectedIds.size === 0) {
        alert('Aucun contact selectionne');
        return;
    }
    const ids = Array.from(selectedIds).join(',');
    window.location.href = '{{ route('admin.members.export') }}?ids=' + ids + '&format=' + format;
    document.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
}
</script>

<style>
.sort-header {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    color: inherit;
    text-decoration: none;
}
.sort-header:hover {
    color: var(--oreina-dark);
}
.pagination-btn.disabled {
    opacity: 0.4;
    pointer-events: none;
}
</style>
@endsection
