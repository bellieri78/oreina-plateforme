@extends('layouts.admin')
@section('title', 'Utilisateurs')
@section('breadcrumb')<span>Administration</span><span>/</span><span>Utilisateurs</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Utilisateurs
            </h1>
            <p class="page-subtitle">{{ $users->total() }} compte(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.users.export', request()->query()) }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Nouvel utilisateur
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['total'] }}</span>
                <span class="stat-card-label">Total utilisateurs</span>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['active'] }}</span>
                <span class="stat-card-label">Actifs</span>
            </div>
        </div>

        <div class="stat-card stat-card-danger">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['admins'] }}</span>
                <span class="stat-card-label">Administrateurs</span>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['reviewers'] }}</span>
                <span class="stat-card-label">Reviewers</span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <form action="{{ route('admin.users.index') }}" method="GET">
            <div class="filters-bar">
                <div class="search-group">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un utilisateur..." class="search-input">
                </div>

                <div class="filters-group">
                    <select name="role" class="form-select">
                        <option value="">Tous les roles</option>
                        @foreach(\App\Models\User::getRoles() as $key => $label)
                            <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    @if(request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Reset</a>
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
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Statut</th>
                        <th>Cree le</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr class="{{ !$u->is_active ? 'row-inactive' : '' }}">
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $u->id }}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                <div class="contact-cell">
                                    <div class="contact-avatar-small">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users.show', $u) }}" class="article-title">
                                            {{ $u->name }}
                                        </a>
                                        @if($u->id === auth()->id())
                                            <span class="badge badge-info" style="font-size: 0.6rem; margin-left: 0.25rem;">Vous</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ $u->email }}</span>
                            </td>
                            <td>
                                @php
                                    $roleColors = [
                                        'user' => 'default',
                                        'author' => 'info',
                                        'reviewer' => 'warning',
                                        'editor' => 'primary',
                                        'admin' => 'danger',
                                    ];
                                @endphp
                                <span class="badge badge-{{ $roleColors[$u->role] ?? 'default' }}">
                                    {{ \App\Models\User::getRoles()[$u->role] ?? $u->role }}
                                </span>
                            </td>
                            <td>
                                @if($u->is_active)
                                    <span class="badge badge-success">Actif</span>
                                @else
                                    <span class="badge badge-default">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted">{{ $u->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    <a href="{{ route('admin.users.show', $u) }}" class="btn btn-ghost btn-sm" title="Voir">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-ghost btn-sm" title="Modifier">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-sm text-danger" title="Supprimer">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="empty-state-title">Aucun utilisateur</h3>
                                    <p class="empty-state-description">Aucun utilisateur ne correspond a vos criteres.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Affichage de {{ $users->firstItem() }} a {{ $users->lastItem() }} sur {{ $users->total() }} resultats
                </div>
                <div class="pagination">
                    @if($users->onFirstPage())
                        <span class="pagination-btn disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="pagination-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                        @if($page == $users->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="pagination-btn">
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
                <select id="bulkRoleSelect" class="form-select" style="width: auto;" onchange="bulkRole()">
                    <option value="">Changer role...</option>
                    @foreach(\App\Models\User::getRoles() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-success btn-sm" onclick="bulkActivate()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Activer
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="bulkDeactivate()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    Desactiver
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
    <form id="bulkDeleteForm" action="{{ route('admin.users.bulk-delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkDeleteIds">
    </form>
    <form id="bulkRoleForm" action="{{ route('admin.users.bulk-role') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkRoleIds">
        <input type="hidden" name="role" id="bulkRoleValue">
    </form>
    <form id="bulkStatusForm" action="{{ route('admin.users.bulk-status') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulkStatusIds">
        <input type="hidden" name="is_active" id="bulkStatusValue">
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
        if (ids) window.location.href = '{{ route('admin.users.export') }}?ids=' + ids;
    }

    function bulkDelete() {
        const ids = getSelectedIds();
        if (ids && confirm('Supprimer ' + ids.split(',').length + ' utilisateur(s) ?')) {
            document.getElementById('bulkDeleteIds').value = ids;
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    function bulkActivate() {
        const ids = getSelectedIds();
        if (ids && confirm('Activer ' + ids.split(',').length + ' utilisateur(s) ?')) {
            document.getElementById('bulkStatusIds').value = ids;
            document.getElementById('bulkStatusValue').value = '1';
            document.getElementById('bulkStatusForm').submit();
        }
    }

    function bulkDeactivate() {
        const ids = getSelectedIds();
        if (ids && confirm('Desactiver ' + ids.split(',').length + ' utilisateur(s) ?')) {
            document.getElementById('bulkStatusIds').value = ids;
            document.getElementById('bulkStatusValue').value = '0';
            document.getElementById('bulkStatusForm').submit();
        }
    }

    function bulkRole() {
        const select = document.getElementById('bulkRoleSelect');
        const role = select.value;
        if (!role) return;
        const ids = getSelectedIds();
        if (ids && confirm('Changer le role de ' + ids.split(',').length + ' utilisateur(s) ?')) {
            document.getElementById('bulkRoleIds').value = ids;
            document.getElementById('bulkRoleValue').value = role;
            document.getElementById('bulkRoleForm').submit();
        }
        select.value = '';
    }
    </script>
@endsection
