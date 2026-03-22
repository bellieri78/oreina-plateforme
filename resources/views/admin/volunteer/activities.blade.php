@extends('layouts.admin')

@section('title', 'Activites benevoles')

@section('breadcrumb')
    <a href="{{ route('admin.volunteer.index') }}">Benevolat</a>
    <span>/</span>
    <span>Activites</span>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Gestion des activites
            </h1>
            <p class="page-subtitle">Organisez et suivez les chantiers, animations et sorties nature</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.volunteer.export', request()->query()) }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter
            </a>
            <a href="{{ route('admin.volunteer.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle activite
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card stat-card-info">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['upcoming'] ?? 0 }}</span>
                <span class="stat-card-label">Activites a venir</span>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['total_volunteers'] ?? 0 }}</span>
                <span class="stat-card-label">Benevoles inscrits</span>
            </div>
        </div>

        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($stats['total_hours'] ?? 0, 0) }}h</span>
                <span class="stat-card-label">Heures prevues</span>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $stats['fill_rate'] ?? 0 }}%</span>
                <span class="stat-card-label">Taux de remplissage</span>
            </div>
        </div>
    </div>

    <!-- Type Filters -->
    @if($activityTypes->isNotEmpty())
    <div class="type-filters">
        <a href="{{ route('admin.volunteer.activities', array_diff_key(request()->query(), ['type' => ''])) }}"
           class="type-filter-btn {{ !request('type') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            Toutes
        </a>
        @foreach($activityTypes as $id => $name)
            @php
                $type = \App\Models\VolunteerActivityType::find($id);
                $color = $type?->color ?? '#7ab51d';
            @endphp
            <a href="{{ route('admin.volunteer.activities', array_merge(request()->query(), ['type' => $id])) }}"
               class="type-filter-btn {{ request('type') == $id ? 'active' : '' }}">
                <span class="type-filter-marker" style="background: {{ $color }}"></span>
                {{ $name }}
            </a>
        @endforeach
    </div>
    @endif

    <!-- Filters Card -->
    <div class="card mb-4">
        <form method="GET" action="{{ route('admin.volunteer.activities') }}">
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            <div class="filters-bar">
                <div class="search-group">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une activite..." class="search-input">
                </div>

                <div class="filters-group">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        @foreach(\App\Models\VolunteerActivity::getStatuses() as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <select name="year" class="form-select">
                        <option value="">Toutes les annees</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>

                    <select name="sort" class="form-select">
                        <option value="date" {{ request('sort', 'date') == 'date' ? 'selected' : '' }}>Tri: Date</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Tri: Titre</option>
                        <option value="participants" {{ request('sort') == 'participants' ? 'selected' : '' }}>Tri: Participants</option>
                    </select>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    @if(request()->hasAny(['search', 'status', 'year', 'sort']))
                        <a href="{{ route('admin.volunteer.activities', request('type') ? ['type' => request('type')] : []) }}" class="btn btn-ghost">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Activities Grid -->
    <div class="card">
        @if($activities->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="empty-state-title">Aucune activite trouvee</h3>
                <p class="empty-state-description">
                    @if(request()->hasAny(['search', 'status', 'type', 'year']))
                        Aucune activite ne correspond a vos criteres de recherche.
                    @else
                        Commencez par creer votre premiere activite.
                    @endif
                </p>
                <a href="{{ route('admin.volunteer.create') }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouvelle activite
                </a>
            </div>
        @else
            <div class="activities-grid">
                @foreach($activities as $activity)
                    @php
                        $color = $activity->activityType?->color ?? '#7ab51d';
                        $fillRate = $activity->max_participants > 0
                            ? round(($activity->participants_count / $activity->max_participants) * 100)
                            : 0;
                        $isPast = $activity->activity_date < today();
                        $isToday = $activity->activity_date->isToday();
                        $isThisWeek = !$isPast && !$isToday && $activity->activity_date <= now()->addDays(7);
                    @endphp
                    <div class="activity-card {{ $isPast ? 'activity-past' : '' }} {{ $isToday ? 'activity-today' : '' }} {{ $isThisWeek ? 'activity-soon' : '' }}">
                        <div class="activity-header" style="background: {{ $color }}"></div>
                        <div class="activity-content">
                            <div class="activity-top">
                                <div class="activity-type-icon" style="background: {{ $color }}20; color: {{ $color }}">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="activity-badges">
                                    <span class="badge badge-{{ $activity->status_color }}">
                                        {{ $activity->status_label }}
                                    </span>
                                </div>
                            </div>

                            <h3 class="activity-title">
                                <a href="{{ route('admin.volunteer.show', $activity) }}">
                                    {{ Str::limit($activity->title, 50) }}
                                </a>
                            </h3>

                            @if($activity->description)
                                <p class="activity-description">
                                    {{ Str::limit($activity->description, 100) }}
                                </p>
                            @endif

                            <div class="activity-infos">
                                <div class="activity-info">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $activity->activity_date->format('d/m/Y') }}
                                </div>
                                @if($activity->start_time)
                                    <div class="activity-info">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }}
                                        @if($activity->end_time)
                                            - {{ \Carbon\Carbon::parse($activity->end_time)->format('H:i') }}
                                        @endif
                                    </div>
                                @endif
                                @if($activity->city || $activity->location)
                                    <div class="activity-info">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $activity->city ?? $activity->location }}
                                    </div>
                                @endif
                            </div>

                            @if($activity->max_participants)
                                <div class="activity-gauge">
                                    <div class="gauge-bar">
                                        <div class="gauge-fill {{ $fillRate >= 100 ? 'gauge-full' : ($fillRate >= 80 ? 'gauge-almost' : '') }}"
                                             style="width: {{ min(100, $fillRate) }}%"></div>
                                    </div>
                                    <span class="gauge-text">{{ $activity->participants_count }}/{{ $activity->max_participants }}</span>
                                </div>
                            @endif

                            <div class="activity-actions">
                                <a href="{{ route('admin.volunteer.show', $activity) }}" class="btn btn-sm btn-outline">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Voir
                                </a>
                                <a href="{{ route('admin.volunteer.edit', $activity) }}" class="btn btn-sm btn-ghost" title="Modifier">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($activities->hasPages())
                <div class="pagination-container">
                    <div class="pagination-info">
                        Affichage de {{ $activities->firstItem() }} a {{ $activities->lastItem() }} sur {{ $activities->total() }} resultats
                    </div>
                    <div class="pagination">
                        @if($activities->onFirstPage())
                            <span class="pagination-btn disabled">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $activities->previousPageUrl() }}" class="pagination-btn">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </a>
                        @endif

                        @foreach($activities->getUrlRange(max(1, $activities->currentPage() - 2), min($activities->lastPage(), $activities->currentPage() + 2)) as $page => $url)
                            @if($page == $activities->currentPage())
                                <span class="pagination-btn active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($activities->hasMorePages())
                            <a href="{{ $activities->nextPageUrl() }}" class="pagination-btn">
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
        @endif
    </div>
@endsection
