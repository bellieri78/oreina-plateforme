@extends('layouts.admin')
@section('title', 'Cartes d\'adherent')
@section('breadcrumb')<span>Cartes d'adherent</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Cartes d'adherent
            </h1>
            <p class="page-subtitle">Generation et envoi des cartes de membre</p>
        </div>
        <div class="page-header-actions">
            <button type="button" class="btn btn-outline" onclick="generateAllCards()" id="btnGenerateAll" style="display: none;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Generer la selection
            </button>
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
                <span class="stat-card-value">{{ $stats['total_active'] }}</span>
                <span class="stat-card-label">Adherents actifs</span>
            </div>
        </div>

        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['cards_generated'] ?? 0 }}</span>
                <span class="stat-card-label">Cartes generees</span>
            </div>
        </div>

        <div class="stat-card stat-card-info">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['cards_sent'] ?? 0 }}</span>
                <span class="stat-card-label">Envoyees par email</span>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['expiring_soon'] ?? 0 }}</span>
                <span class="stat-card-label">Expirent bientot</span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <form method="GET" id="filterForm">
            <div class="filters-bar">
                <div class="search-group">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, numero, email..." class="search-input">
                </div>

                <div class="filters-group">
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach($membershipTypes as $id => $name)
                            <option value="{{ $id }}" {{ request('type') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    <a href="{{ route('admin.member-cards.index') }}" class="btn btn-ghost">Reset</a>
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
                        <th>Adherent</th>
                        <th>N&deg; membre</th>
                        <th>Type d'adhesion</th>
                        <th>Valide jusqu'au</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        @php
                            $membership = $member->currentMembership();
                        @endphp
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $member->id }}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                <div class="contact-cell">
                                    <div class="contact-avatar contact-avatar-member">
                                        {{ strtoupper(substr($member->first_name ?? $member->last_name, 0, 1)) }}
                                    </div>
                                    <div class="contact-info">
                                        <a href="{{ route('admin.members.show', $member) }}" class="contact-name">
                                            {{ $member->full_name }}
                                        </a>
                                        @if($member->email)
                                            <span class="contact-email">{{ $member->email }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($member->member_number)
                                    <code class="member-number">{{ $member->member_number }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($membership?->membershipType?->name)
                                    <span class="badge badge-outline">{{ $membership->membershipType->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($membership?->end_date)
                                    <div class="validity-date">
                                        <span>{{ $membership->end_date->format('d/m/Y') }}</span>
                                        @if($membership->end_date->diffInDays(now()) < 30 && $membership->end_date > now())
                                            <span class="badge badge-warning">Expire bientot</span>
                                        @elseif($membership->end_date < now())
                                            <span class="badge badge-danger">Expiree</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    <a href="{{ route('admin.member-cards.preview', $member) }}" class="btn btn-ghost btn-sm" target="_blank" title="Apercu">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.member-cards.download', $member) }}" class="btn btn-ghost btn-sm" title="Telecharger PDF">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>
                                    @if($member->email)
                                        <a href="{{ route('admin.member-cards.send', $member) }}" class="btn btn-ghost btn-sm" title="Envoyer par email" onclick="return confirm('Envoyer la carte par email a {{ $member->email }} ?');">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                    </div>
                                    <h3 class="empty-state-title">Aucun adherent actif</h3>
                                    <p class="empty-state-description">Aucun adherent actif ne correspond a vos criteres de recherche.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($members->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Affichage de {{ $members->firstItem() }} a {{ $members->lastItem() }} sur {{ $members->total() }} resultats
                </div>
                <div class="pagination">
                    @if($members->onFirstPage())
                        <span class="pagination-btn disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $members->previousPageUrl() }}" class="pagination-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @foreach($members->getUrlRange(max(1, $members->currentPage() - 2), min($members->lastPage(), $members->currentPage() + 2)) as $page => $url)
                        @if($page == $members->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($members->hasMorePages())
                        <a href="{{ $members->nextPageUrl() }}" class="pagination-btn">
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
            <span class="selection-count"><span id="selectedCount">0</span> carte(s) selectionnee(s)</span>
            <div class="selection-actions">
                <form action="{{ route('admin.member-cards.batch') }}" method="POST" style="display: inline;" id="batchDownloadForm">
                    @csrf
                    <input type="hidden" name="ids" id="bulkIds">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Telecharger les cartes (PDF)
                    </button>
                </form>
                <form action="{{ route('admin.member-cards.batch-send') }}" method="POST" style="display: inline;" id="batchSendForm">
                    @csrf
                    <input type="hidden" name="ids" id="bulkSendIds">
                    <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('Envoyer les cartes par email aux adherents selectionnes ?');">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Envoyer par email
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card info-card">
        <div class="card-body">
            <div class="info-card-header">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h4>A propos des cartes d'adherent</h4>
            </div>
            <ul class="info-list">
                <li>Les cartes sont generees au format PDF, taille carte de credit (85,6 x 54 mm).</li>
                <li>Seuls les adherents avec une adhesion active peuvent avoir une carte.</li>
                <li>Le telechargement par lot genere un PDF A4 avec plusieurs cartes par page.</li>
                <li>Chaque carte contient un code de verification unique.</li>
            </ul>
        </div>
    </div>

    <script>
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
        const bulkIds = document.getElementById('bulkIds');
        const bulkSendIds = document.getElementById('bulkSendIds');
        const btnGenerateAll = document.getElementById('btnGenerateAll');

        const ids = Array.from(checkboxes).map(cb => cb.value).join(',');

        if (checkboxes.length > 0) {
            selectionBar.style.display = 'flex';
            btnGenerateAll.style.display = 'inline-flex';
            selectedCount.textContent = checkboxes.length;
            bulkIds.value = ids;
            bulkSendIds.value = ids;
        } else {
            selectionBar.style.display = 'none';
            btnGenerateAll.style.display = 'none';
        }

        const allCheckboxes = document.querySelectorAll('.row-checkbox');
        const selectAll = document.getElementById('selectAll');
        selectAll.checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
        selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
    }

    function generateAllCards() {
        document.getElementById('batchDownloadForm').submit();
    }
    </script>
@endsection
