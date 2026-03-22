@extends('layouts.admin')
@section('title', 'RGPD - Tableau de bord')
@section('breadcrumb')<span>Administration</span><span>/</span><span>RGPD</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                </svg>
                RGPD - Conformite
            </h1>
            <p class="page-subtitle">Gestion des donnees personnelles et consentements</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.rgpd.trash') }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Corbeille
            </a>
            <a href="{{ route('admin.rgpd.settings') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Parametres
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Alerts Summary Cards -->
    <div class="stats-cards">
        <a href="{{ route('admin.rgpd.alerts', ['type' => 'no_interaction']) }}" class="stat-card stat-card-clickable stat-card-warning">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($alerts['no_interaction']) }}</span>
                <span class="stat-card-label">Sans interaction</span>
                <span class="stat-card-detail">{{ $settings['retention_no_interaction'] ?? 36 }} mois+</span>
            </div>
        </a>

        <a href="{{ route('admin.rgpd.alerts', ['type' => 'not_updated']) }}" class="stat-card stat-card-clickable stat-card-orange">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($alerts['not_updated']) }}</span>
                <span class="stat-card-label">Non mis a jour</span>
                <span class="stat-card-detail">{{ $settings['retention_not_updated'] ?? 60 }} mois+</span>
            </div>
        </a>

        <a href="{{ route('admin.rgpd.alerts', ['type' => 'expired_membership']) }}" class="stat-card stat-card-clickable stat-card-danger">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($alerts['expired_membership']) }}</span>
                <span class="stat-card-label">Adhesion expiree</span>
                <span class="stat-card-detail">{{ $settings['retention_expired_membership'] ?? 24 }} mois+</span>
            </div>
        </a>

        <a href="{{ route('admin.rgpd.alerts', ['type' => 'inactive_donor']) }}" class="stat-card stat-card-clickable stat-card-purple">
            <div class="stat-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">{{ number_format($alerts['inactive_donor']) }}</span>
                <span class="stat-card-label">Donateur inactif</span>
                <span class="stat-card-detail">{{ $settings['retention_inactive_donor'] ?? 48 }} mois+</span>
            </div>
        </a>
    </div>

    <!-- Consent and Anonymization Stats -->
    <div class="stats-grid-2">
        <!-- Consent Statistics -->
        <div class="card">
            <div class="card-header-simple">
                <h3 class="card-title-simple">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="margin-right: 0.5rem; vertical-align: middle;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Consentements
                </h3>
            </div>
            <div class="card-body-simple">
                @php
                    $total = max($consentStats['total'], 1);
                @endphp
                <div class="progress-list">
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Newsletter</span>
                            <span class="progress-value">{{ $consentStats['newsletter'] }} / {{ $consentStats['total'] }}</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: {{ ($consentStats['newsletter'] / $total) * 100 }}%; background-color: #3b82f6;"></div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Communication</span>
                            <span class="progress-value">{{ $consentStats['communication'] }} / {{ $consentStats['total'] }}</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: {{ ($consentStats['communication'] / $total) * 100 }}%; background-color: #10b981;"></div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Droit a l'image</span>
                            <span class="progress-value">{{ $consentStats['image'] }} / {{ $consentStats['total'] }}</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: {{ ($consentStats['image'] / $total) * 100 }}%; background-color: #8b5cf6;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anonymization & Trash Stats -->
        <div class="card">
            <div class="card-header-simple">
                <h3 class="card-title-simple">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="margin-right: 0.5rem; vertical-align: middle;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Etat des donnees
                </h3>
            </div>
            <div class="card-body-simple">
                <div class="content-stats-grid">
                    <div class="content-stat-box">
                        <span class="content-stat-value">{{ number_format($anonymizationStats['anonymized']) }}</span>
                        <span class="content-stat-label">Contacts anonymises</span>
                    </div>
                    <div class="content-stat-box content-stat-box-danger">
                        <span class="content-stat-value">{{ number_format($anonymizationStats['deleted']) }}</span>
                        <span class="content-stat-label">Dans la corbeille</span>
                    </div>
                </div>

                <div class="rgpd-active-contacts">
                    <span class="rgpd-active-label">Contacts actifs</span>
                    <span class="rgpd-active-value">{{ number_format($consentStats['total']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reviews -->
    <div class="card">
        <div class="card-header-simple">
            <h3 class="card-title-simple">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="margin-right: 0.5rem; vertical-align: middle;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Dernieres actions RGPD
            </h3>
        </div>

        @if($recentReviews->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="empty-state-title">Aucune action recente</h3>
                <p class="empty-state-description">Les dernieres actions RGPD apparaitront ici.</p>
            </div>
        @else
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Contact</th>
                            <th>Type d'alerte</th>
                            <th>Action</th>
                            <th>Par</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentReviews as $review)
                            <tr>
                                <td>
                                    @if($review->member)
                                        <a href="{{ route('admin.members.show', $review->member) }}" class="article-title">
                                            {{ $review->member->full_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Contact supprime</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $alertLabels = [
                                            'no_interaction' => ['label' => 'Sans interaction', 'color' => 'warning'],
                                            'not_updated' => ['label' => 'Non mis a jour', 'color' => 'warning'],
                                            'expired_membership' => ['label' => 'Adhesion expiree', 'color' => 'danger'],
                                            'inactive_donor' => ['label' => 'Donateur inactif', 'color' => 'primary'],
                                            'manual' => ['label' => 'Manuel', 'color' => 'default'],
                                        ];
                                        $alert = $alertLabels[$review->alert_type] ?? ['label' => $review->alert_type, 'color' => 'default'];
                                    @endphp
                                    <span class="badge badge-{{ $alert['color'] }}">{{ $alert['label'] }}</span>
                                </td>
                                <td>
                                    @php
                                        $actionLabels = [
                                            'keep' => ['label' => 'Conserver', 'color' => 'success'],
                                            'update' => ['label' => 'Mettre a jour', 'color' => 'info'],
                                            'contact' => ['label' => 'Contacter', 'color' => 'warning'],
                                            'anonymize' => ['label' => 'Anonymiser', 'color' => 'danger'],
                                        ];
                                        $action = $actionLabels[$review->action] ?? ['label' => $review->action, 'color' => 'default'];
                                    @endphp
                                    <span class="badge badge-{{ $action['color'] }}">{{ $action['label'] }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $review->user?->name ?? 'Systeme' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
