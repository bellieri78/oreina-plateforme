@extends('layouts.admin')
@section('title', 'Tableau de bord')
@section('breadcrumb')<span>Accueil</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i data-lucide="home" class="page-title-icon"></i>
                Tableau de bord
            </h1>
            <p class="page-subtitle">Bienvenue, {{ auth()->user()->name }}</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.settings.statistics') }}" class="btn btn-outline">
                <i data-lucide="bar-chart-3"></i>
                Statistiques
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="dashboard-stats">
        <div class="dashboard-stat-card dashboard-stat-blue">
            <div class="dashboard-stat-icon">
                <i data-lucide="users" style="width:28px;height:28px;"></i>
            </div>
            <div class="dashboard-stat-content">
                <span class="dashboard-stat-value">{{ $stats['members_active'] }}</span>
                <span class="dashboard-stat-label">Contacts actifs</span>
                <span class="dashboard-stat-detail">{{ $stats['members_total'] }} total / +{{ $stats['members_new_month'] }} ce mois</span>
            </div>
        </div>

        <div class="dashboard-stat-card dashboard-stat-green">
            <div class="dashboard-stat-icon">
                <i data-lucide="circle-dollar-sign" style="width:28px;height:28px;"></i>
            </div>
            <div class="dashboard-stat-content">
                <span class="dashboard-stat-value">{{ number_format($stats['donations_year'], 0, ',', ' ') }} EUR</span>
                <span class="dashboard-stat-label">Dons {{ now()->year }}</span>
                <span class="dashboard-stat-detail">{{ $stats['donations_count_year'] }} dons / {{ $stats['donations_pending_receipt'] }} recus en attente</span>
            </div>
        </div>

        <div class="dashboard-stat-card dashboard-stat-purple">
            <div class="dashboard-stat-icon">
                <i data-lucide="id-card" style="width:28px;height:28px;"></i>
            </div>
            <div class="dashboard-stat-content">
                <span class="dashboard-stat-value">{{ $stats['memberships_active'] }}</span>
                <span class="dashboard-stat-label">Adhesions actives</span>
                <span class="dashboard-stat-detail">{{ $stats['memberships_expired'] }} expirees / {{ number_format($stats['memberships_year_amount'], 0, ',', ' ') }} EUR {{ now()->year }}</span>
            </div>
        </div>

        <div class="dashboard-stat-card dashboard-stat-orange">
            <div class="dashboard-stat-icon">
                <i data-lucide="book-open" style="width:28px;height:28px;"></i>
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
                <i data-lucide="trending-up" style="width:20px;height:20px;margin-right:0.5rem;vertical-align:middle;"></i>
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
                    <i data-lucide="calendar-days" style="width:18px;height:18px;margin-right:0.5rem;vertical-align:middle;"></i>
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
                    <i data-lucide="zap" style="width:18px;height:18px;margin-right:0.5rem;vertical-align:middle;"></i>
                    Actions rapides
                </h3>
            </div>
            <div class="card-body-simple">
                <div class="quick-actions-grid">
                    <a href="{{ route('admin.members.create') }}" class="quick-action">
                        <div class="quick-action-icon quick-action-icon-blue">
                            <i data-lucide="user-plus" style="width:24px;height:24px;"></i>
                        </div>
                        <span>Nouveau contact</span>
                    </a>
                    <a href="{{ route('admin.memberships.create') }}" class="quick-action">
                        <div class="quick-action-icon quick-action-icon-purple">
                            <i data-lucide="credit-card" style="width:24px;height:24px;"></i>
                        </div>
                        <span>Nouvelle adhesion</span>
                    </a>
                    <a href="{{ route('admin.donations.create') }}" class="quick-action">
                        <div class="quick-action-icon quick-action-icon-green">
                            <i data-lucide="heart-handshake" style="width:24px;height:24px;"></i>
                        </div>
                        <span>Nouveau don</span>
                    </a>
                    <a href="{{ route('admin.articles.create') }}" class="quick-action">
                        <div class="quick-action-icon quick-action-icon-orange">
                            <i data-lucide="pencil" style="width:24px;height:24px;"></i>
                        </div>
                        <span>Nouvel article</span>
                    </a>
                    <a href="{{ route('admin.events.create') }}" class="quick-action">
                        <div class="quick-action-icon quick-action-icon-yellow">
                            <i data-lucide="calendar-plus" style="width:24px;height:24px;"></i>
                        </div>
                        <span>Nouvel evenement</span>
                    </a>
                    <a href="{{ route('admin.members.import') }}" class="quick-action">
                        <div class="quick-action-icon quick-action-icon-indigo">
                            <i data-lucide="upload" style="width:24px;height:24px;"></i>
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
