@extends('layouts.admin')
@section('title', 'Tableau de bord')
@section('breadcrumb')<span>Accueil</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Tableau de bord
            </h1>
            <p class="page-subtitle">Bienvenue, {{ auth()->user()->name }}</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.settings.statistics') }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistiques
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="dashboard-stats">
        <div class="dashboard-stat-card dashboard-stat-blue">
            <div class="dashboard-stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="dashboard-stat-content">
                <span class="dashboard-stat-value">{{ $stats['members_active'] }}</span>
                <span class="dashboard-stat-label">Contacts actifs</span>
                <span class="dashboard-stat-detail">{{ $stats['members_total'] }} total / +{{ $stats['members_new_month'] }} ce mois</span>
            </div>
        </div>

        <div class="dashboard-stat-card dashboard-stat-green">
            <div class="dashboard-stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="dashboard-stat-content">
                <span class="dashboard-stat-value">{{ number_format($stats['donations_year'], 0, ',', ' ') }} EUR</span>
                <span class="dashboard-stat-label">Dons {{ now()->year }}</span>
                <span class="dashboard-stat-detail">{{ $stats['donations_count_year'] }} dons / {{ $stats['donations_pending_receipt'] }} recus en attente</span>
            </div>
        </div>

        <div class="dashboard-stat-card dashboard-stat-purple">
            <div class="dashboard-stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                </svg>
            </div>
            <div class="dashboard-stat-content">
                <span class="dashboard-stat-value">{{ $stats['memberships_active'] }}</span>
                <span class="dashboard-stat-label">Adhesions actives</span>
                <span class="dashboard-stat-detail">{{ $stats['memberships_expired'] }} expirees / {{ number_format($stats['memberships_year_amount'], 0, ',', ' ') }} EUR {{ now()->year }}</span>
            </div>
        </div>

        <div class="dashboard-stat-card dashboard-stat-orange">
            <div class="dashboard-stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div class="dashboard-stat-content">
                <span class="dashboard-stat-value">{{ $stats['submissions_pending'] }}</span>
                <span class="dashboard-stat-label">Soumissions en attente</span>
                <span class="dashboard-stat-detail">{{ $stats['articles_published'] }} articles / {{ $stats['articles_draft'] }} brouillons</span>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="card mb-4">
        <div class="card-header-simple">
            <h3 class="card-title-simple">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="margin-right: 0.5rem; vertical-align: middle;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                Evolution sur 12 mois
            </h3>
        </div>
        <div class="card-body-simple">
            <div class="dashboard-chart-container">
                <canvas id="evolutionChart" height="280"></canvas>
            </div>
        </div>
    </div>

    <!-- Content Grid - Row 1 -->
    <div class="dashboard-grid-3">
        <!-- Recent Members -->
        <div class="card">
            <div class="card-header-simple" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title-simple">Derniers contacts</h3>
                <a href="{{ route('admin.members.index') }}" class="btn btn-ghost btn-sm">Voir tout</a>
            </div>
            <div class="dashboard-list">
                @forelse($recentMembers as $member)
                    <a href="{{ route('admin.members.show', $member) }}" class="dashboard-list-item">
                        <div class="dashboard-list-avatar">
                            {{ strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)) }}
                        </div>
                        <div class="dashboard-list-content">
                            <span class="dashboard-list-title">{{ $member->full_name }}</span>
                            <span class="dashboard-list-sub">{{ $member->created_at->format('d/m/Y') }}</span>
                        </div>
                    </a>
                @empty
                    <div class="dashboard-list-empty">Aucun contact</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="card">
            <div class="card-header-simple" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title-simple">Derniers dons</h3>
                <a href="{{ route('admin.donations.index') }}" class="btn btn-ghost btn-sm">Voir tout</a>
            </div>
            <div class="dashboard-list">
                @forelse($recentDonations as $donation)
                    <a href="{{ route('admin.donations.show', $donation) }}" class="dashboard-list-item">
                        <div class="dashboard-list-badge dashboard-list-badge-success">
                            {{ number_format($donation->amount, 0, ',', ' ') }} EUR
                        </div>
                        <div class="dashboard-list-content">
                            <span class="dashboard-list-title">{{ $donation->donor_name }}</span>
                            <span class="dashboard-list-sub">{{ $donation->donation_date->format('d/m/Y') }}</span>
                        </div>
                    </a>
                @empty
                    <div class="dashboard-list-empty">Aucun don</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Memberships -->
        <div class="card">
            <div class="card-header-simple" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title-simple">Dernieres adhesions</h3>
                <a href="{{ route('admin.memberships.index') }}" class="btn btn-ghost btn-sm">Voir tout</a>
            </div>
            <div class="dashboard-list">
                @forelse($recentMemberships as $membership)
                    <a href="{{ route('admin.memberships.show', $membership) }}" class="dashboard-list-item">
                        <div class="dashboard-list-badge {{ $membership->end_date >= now() ? 'dashboard-list-badge-success' : 'dashboard-list-badge-warning' }}">
                            {{ $membership->end_date >= now() ? 'Active' : 'Expiree' }}
                        </div>
                        <div class="dashboard-list-content">
                            <span class="dashboard-list-title">{{ $membership->member?->full_name ?? '-' }}</span>
                            <span class="dashboard-list-sub">{{ number_format($membership->amount_paid, 0, ',', ' ') }} EUR - {{ $membership->membershipType?->name ?? 'Standard' }}</span>
                        </div>
                    </a>
                @empty
                    <div class="dashboard-list-empty">Aucune adhesion</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Content Grid - Row 2 -->
    <div class="dashboard-grid-2">
        <!-- Upcoming Events -->
        <div class="card">
            <div class="card-header-simple" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title-simple">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18" style="margin-right: 0.5rem; vertical-align: middle;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Prochains evenements
                </h3>
                <a href="{{ route('admin.events.index') }}" class="btn btn-ghost btn-sm">Voir tout</a>
            </div>
            <div class="dashboard-list">
                @forelse($upcomingEvents as $event)
                    <a href="{{ route('admin.events.show', $event) }}" class="dashboard-list-item">
                        <div class="dashboard-list-date">
                            <span class="dashboard-date-day">{{ $event->start_date->format('d') }}</span>
                            <span class="dashboard-date-month">{{ $event->start_date->translatedFormat('M') }}</span>
                        </div>
                        <div class="dashboard-list-content">
                            <span class="dashboard-list-title">{{ $event->title }}</span>
                            <span class="dashboard-list-sub">{{ $event->location ?? 'Lieu a definir' }}</span>
                        </div>
                    </a>
                @empty
                    <div class="dashboard-list-empty">Aucun evenement a venir</div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header-simple">
                <h3 class="card-title-simple">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18" style="margin-right: 0.5rem; vertical-align: middle;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Actions rapides
                </h3>
            </div>
            <div class="card-body-simple">
                <div class="quick-actions-grid">
                    <a href="{{ route('admin.members.create') }}" class="quick-action">
                        <div class="quick-action-icon quick-action-icon-blue">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <span>Nouveau contact</span>
                    </a>
                    <a href="{{ route('admin.memberships.create') }}" class="quick-action">
                        <div class="quick-action-icon quick-action-icon-purple">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                            </svg>
                        </div>
                        <span>Nouvelle adhesion</span>
                    </a>
                    <a href="{{ route('admin.donations.create') }}" class="quick-action">
                        <div class="quick-action-icon quick-action-icon-green">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span>Nouveau don</span>
                    </a>
                    <a href="{{ route('admin.articles.create') }}" class="quick-action">
                        <div class="quick-action-icon quick-action-icon-orange">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <span>Nouvel article</span>
                    </a>
                    <a href="{{ route('admin.events.create') }}" class="quick-action">
                        <div class="quick-action-icon quick-action-icon-yellow">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span>Nouvel evenement</span>
                    </a>
                    <a href="{{ route('admin.members.import') }}" class="quick-action">
                        <div class="quick-action-icon quick-action-icon-indigo">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </div>
                        <span>Importer CSV</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    const months = @json($months);

    const labels = Object.values(months).map(m => m.label);
    const donationsData = Object.values(months).map(m => m.donations);
    const membershipsData = Object.values(months).map(m => m.memberships);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Dons (EUR)',
                    data: donationsData,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    yAxisID: 'y'
                },
                {
                    label: 'Adhesions',
                    data: membershipsData,
                    type: 'line',
                    borderColor: 'rgba(139, 92, 246, 1)',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(139, 92, 246, 1)',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Dons (EUR)',
                        color: '#6b7280',
                        font: { weight: '500' }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Adhesions',
                        color: '#6b7280',
                        font: { weight: '500' }
                    },
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12 }
                    }
                }
            }
        }
    });
});
</script>
@endsection
