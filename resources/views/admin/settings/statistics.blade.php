@extends('layouts.admin')
@section('title', 'Statistiques')
@section('breadcrumb')<span>Administration</span><span>/</span><span>Statistiques</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistiques
            </h1>
            <p class="page-subtitle">Vue d'ensemble de la plateforme</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Parametres
            </a>
        </div>
    </div>

    <!-- Main Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card stat-card-info">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($stats['users']['total']) }}</span>
                <span class="stat-card-label">Utilisateurs</span>
                <span class="stat-card-detail">{{ $stats['users']['active'] }} actifs</span>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($stats['members']['total']) }}</span>
                <span class="stat-card-label">Membres</span>
                <span class="stat-card-detail">{{ $stats['members']['active'] }} actifs</span>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($stats['donations']['this_year'], 0, ',', ' ') }} EUR</span>
                <span class="stat-card-label">Dons {{ now()->year }}</span>
                <span class="stat-card-detail">Total: {{ number_format($stats['donations']['total_amount'], 0, ',', ' ') }} EUR</span>
            </div>
        </div>

        <div class="stat-card stat-card-turquoise">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ $journalStats['issues']['total'] }}</span>
                <span class="stat-card-label">Numeros revue</span>
                <span class="stat-card-detail">{{ $journalStats['issues']['published'] }} publies</span>
            </div>
        </div>
    </div>

    <!-- Distribution Charts -->
    <div class="stats-grid-2">
        <!-- Users by Role -->
        <div class="card">
            <div class="card-header-simple">
                <h3 class="card-title-simple">Utilisateurs par role</h3>
            </div>
            <div class="card-body-simple">
                @php
                    $roleLabels = [
                        'user' => 'Utilisateur',
                        'author' => 'Auteur',
                        'reviewer' => 'Reviewer',
                        'editor' => 'Editeur',
                        'admin' => 'Administrateur',
                    ];
                    $roleColors = [
                        'user' => 'var(--oreina-dark)',
                        'author' => '#3b82f6',
                        'reviewer' => '#f59e0b',
                        'editor' => '#8b5cf6',
                        'admin' => '#ef4444',
                    ];
                    $maxRole = max($stats['users']['by_role'] ?: [1]);
                @endphp
                <div class="progress-list">
                    @foreach($stats['users']['by_role'] as $role => $count)
                        <div class="progress-item">
                            <div class="progress-header">
                                <span class="progress-label">{{ $roleLabels[$role] ?? $role }}</span>
                                <span class="progress-value">{{ $count }}</span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill" style="width: {{ ($count / $maxRole) * 100 }}%; background-color: {{ $roleColors[$role] ?? 'var(--oreina-dark)' }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Memberships by Type -->
        <div class="card">
            <div class="card-header-simple">
                <h3 class="card-title-simple">Adhesions par type</h3>
            </div>
            <div class="card-body-simple">
                @php
                    $typeColors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#f97316'];
                    $maxType = max($stats['memberships']['by_type'] ?: [1]);
                    $colorIndex = 0;
                @endphp
                @if(count($stats['memberships']['by_type']) > 0)
                    <div class="progress-list">
                        @foreach($stats['memberships']['by_type'] as $type => $count)
                            <div class="progress-item">
                                <div class="progress-header">
                                    <span class="progress-label">{{ $type }}</span>
                                    <span class="progress-value">{{ $count }}</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill" style="width: {{ ($count / $maxType) * 100 }}%; background-color: {{ $typeColors[$colorIndex % count($typeColors)] }};"></div>
                                </div>
                            </div>
                            @php $colorIndex++; @endphp
                        @endforeach
                    </div>
                @else
                    <div class="empty-state-small">
                        <p class="text-muted">Aucune adhesion</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Journal Statistics -->
    <div class="card mb-4">
        <div class="card-header-simple">
            <h3 class="card-title-simple">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="margin-right: 0.5rem; vertical-align: middle;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Revue Scientifique
            </h3>
        </div>
        <div class="card-body-simple">
            <div class="stats-grid-3">
                <!-- Submissions by Status -->
                <div class="stat-section">
                    <h4 class="stat-section-title">Soumissions par statut</h4>
                    @php
                        $statusLabels = [
                            'draft' => 'Brouillon',
                            'submitted' => 'Soumis',
                            'desk_review' => 'Pre-evaluation',
                            'in_review' => 'En evaluation',
                            'revision' => 'Revision',
                            'accepted' => 'Accepte',
                            'rejected' => 'Rejete',
                            'published' => 'Publie',
                        ];
                        $statusColors = [
                            'draft' => 'default',
                            'submitted' => 'info',
                            'desk_review' => 'warning',
                            'in_review' => 'primary',
                            'revision' => 'warning',
                            'accepted' => 'success',
                            'rejected' => 'danger',
                            'published' => 'success',
                        ];
                    @endphp
                    @if(count($journalStats['submissions']['by_status']) > 0)
                        <div class="badge-list">
                            @foreach($journalStats['submissions']['by_status'] as $status => $count)
                                <div class="badge-list-item">
                                    <span class="badge badge-{{ $statusColors[$status] ?? 'default' }}">
                                        {{ $statusLabels[$status] ?? $status }}
                                    </span>
                                    <span class="badge-count">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Aucune soumission</p>
                    @endif
                </div>

                <!-- Reviews by Status -->
                <div class="stat-section">
                    <h4 class="stat-section-title">Evaluations par statut</h4>
                    @php
                        $reviewStatusLabels = [
                            'invited' => 'Invite',
                            'accepted' => 'Accepte',
                            'declined' => 'Decline',
                            'completed' => 'Complete',
                            'expired' => 'Expire',
                        ];
                        $reviewStatusColors = [
                            'invited' => 'info',
                            'accepted' => 'warning',
                            'declined' => 'danger',
                            'completed' => 'success',
                            'expired' => 'default',
                        ];
                    @endphp
                    @if(count($journalStats['reviews']['by_status']) > 0)
                        <div class="badge-list">
                            @foreach($journalStats['reviews']['by_status'] as $status => $count)
                                <div class="badge-list-item">
                                    <span class="badge badge-{{ $reviewStatusColors[$status] ?? 'default' }}">
                                        {{ $reviewStatusLabels[$status] ?? $status }}
                                    </span>
                                    <span class="badge-count">{{ $count }}</span>
                                </div>
                            @endforeach

                            @if($journalStats['reviews']['overdue'] > 0)
                                <div class="badge-list-item" style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--oreina-border);">
                                    <span class="badge badge-danger">En retard</span>
                                    <span class="badge-count text-danger" style="font-weight: 600;">{{ $journalStats['reviews']['overdue'] }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted">Aucune evaluation</p>
                    @endif
                </div>

                <!-- Reviews by Recommendation -->
                <div class="stat-section">
                    <h4 class="stat-section-title">Recommandations</h4>
                    @php
                        $recLabels = [
                            'accept' => 'Accepter',
                            'minor_revision' => 'Revision mineure',
                            'major_revision' => 'Revision majeure',
                            'reject' => 'Rejeter',
                        ];
                        $recColors = [
                            'accept' => 'success',
                            'minor_revision' => 'info',
                            'major_revision' => 'warning',
                            'reject' => 'danger',
                        ];
                    @endphp
                    @if(count($journalStats['reviews']['by_recommendation']) > 0)
                        <div class="badge-list">
                            @foreach($journalStats['reviews']['by_recommendation'] as $rec => $count)
                                <div class="badge-list-item">
                                    <span class="badge badge-{{ $recColors[$rec] ?? 'default' }}">
                                        {{ $recLabels[$rec] ?? $rec }}
                                    </span>
                                    <span class="badge-count">{{ $count }}</span>
                                </div>
                            @endforeach

                            @if($journalStats['reviews']['avg_review_time_days'])
                                <div class="badge-list-item" style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--oreina-border);">
                                    <span class="text-muted" style="font-size: 0.8rem;">Temps moyen d'evaluation</span>
                                    <span class="badge-count">{{ round($journalStats['reviews']['avg_review_time_days']) }} jours</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted">Aucune recommandation</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Statistics -->
    <div class="stats-grid-2">
        <!-- Articles Hub -->
        <div class="card">
            <div class="card-header-simple">
                <h3 class="card-title-simple">Articles Hub</h3>
            </div>
            <div class="card-body-simple">
                <div class="content-stats-grid">
                    <div class="content-stat-box">
                        <span class="content-stat-value">{{ $contentStats['articles']['total'] }}</span>
                        <span class="content-stat-label">Total</span>
                    </div>
                    <div class="content-stat-box content-stat-box-success">
                        <span class="content-stat-value">{{ $contentStats['articles']['published'] }}</span>
                        <span class="content-stat-label">Publies</span>
                    </div>
                </div>

                <div class="badge-list" style="margin-top: 1rem;">
                    @php
                        $articleStatusLabels = [
                            'draft' => 'Brouillon',
                            'submitted' => 'Soumis',
                            'validated' => 'Valide',
                            'published' => 'Publie',
                        ];
                    @endphp
                    @foreach($contentStats['articles']['by_status'] as $status => $count)
                        <div class="badge-list-item">
                            <span class="text-muted" style="font-size: 0.85rem;">{{ $articleStatusLabels[$status] ?? $status }}</span>
                            <span class="badge-count">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Events -->
        <div class="card">
            <div class="card-header-simple">
                <h3 class="card-title-simple">Evenements</h3>
            </div>
            <div class="card-body-simple">
                <div class="content-stats-grid content-stats-grid-3">
                    <div class="content-stat-box">
                        <span class="content-stat-value">{{ $contentStats['events']['total'] }}</span>
                        <span class="content-stat-label">Total</span>
                    </div>
                    <div class="content-stat-box content-stat-box-info">
                        <span class="content-stat-value">{{ $contentStats['events']['upcoming'] }}</span>
                        <span class="content-stat-label">A venir</span>
                    </div>
                    <div class="content-stat-box content-stat-box-muted">
                        <span class="content-stat-value">{{ $contentStats['events']['past'] }}</span>
                        <span class="content-stat-label">Passes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Donations Chart -->
    @if(!empty($stats['donations']['by_month']))
    <div class="card">
        <div class="card-header-simple">
            <h3 class="card-title-simple">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="margin-right: 0.5rem; vertical-align: middle;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Dons par mois ({{ now()->year }})
            </h3>
        </div>
        <div class="card-body-simple">
            <div class="chart-container">
                @php
                    $maxDonation = max($stats['donations']['by_month'] ?: [1]);
                    $months = ['01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Avr', '05' => 'Mai', '06' => 'Jun',
                               '07' => 'Jul', '08' => 'Aou', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'];
                @endphp
                @for($m = 1; $m <= 12; $m++)
                    @php
                        $monthKey = now()->year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
                        $amount = $stats['donations']['by_month'][$monthKey] ?? 0;
                        $height = $maxDonation > 0 ? ($amount / $maxDonation) * 100 : 0;
                    @endphp
                    <div class="chart-bar-wrapper">
                        <div class="chart-bar" style="height: {{ $height }}%;" title="{{ number_format($amount, 0, ',', ' ') }} EUR"></div>
                        <span class="chart-label">{{ $months[str_pad($m, 2, '0', STR_PAD_LEFT)] }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>
    @endif
@endsection
