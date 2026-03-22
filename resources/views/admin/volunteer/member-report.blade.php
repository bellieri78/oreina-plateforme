@extends('layouts.admin')

@section('title', 'Rapport benevole - ' . $member->full_name)

@section('breadcrumb')
    <a href="{{ route('admin.volunteer.index') }}">Benevolat</a>
    <span>/</span>
    <span>{{ $member->full_name }}</span>
@endsection

@section('content')
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.25rem;">{{ $member->full_name }}</h2>
            <p style="color: #6b7280;">Rapport d'activite benevole</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.reports.volunteer-certificate', ['member' => $member, 'year' => $year]) }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; margin-right: 4px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Attestation PDF
            </a>
            <a href="{{ route('admin.members.show', $member) }}" class="btn btn-secondary">Voir la fiche membre</a>
        </div>
    </div>

    {{-- Year selector --}}
    <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 1.5rem;">
        <label style="font-weight: 500;">Annee :</label>
        <select onchange="window.location.href='?year='+this.value" class="form-input" style="width: auto;">
            @foreach($years as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>

    {{-- Stats --}}
    <div class="stats-grid" style="margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_activities'] }}</div>
            <div class="stat-label">Activites {{ $year }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['attended'] }}</div>
            <div class="stat-label">Presences</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['total_hours'], 1) }}h</div>
            <div class="stat-label">Heures totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['upcoming'] }}</div>
            <div class="stat-label">A venir</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        {{-- Activity types breakdown --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Repartition par type</h3>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Activites</th>
                            <th>Heures</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($typeBreakdown as $type)
                            <tr>
                                <td>
                                    <span style="display: inline-flex; align-items: center; gap: 4px;">
                                        <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $type['color'] }};"></span>
                                        {{ $type['name'] }}
                                    </span>
                                </td>
                                <td>{{ $type['count'] }}</td>
                                <td>{{ number_format($type['hours'], 1) }}h</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #6b7280;">Aucune donnee.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Monthly breakdown --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Activite mensuelle</h3>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mois</th>
                            <th>Activites</th>
                            <th>Heures</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $months = [1 => 'Janvier', 2 => 'Fevrier', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                                       7 => 'Juillet', 8 => 'Aout', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Decembre'];
                        @endphp
                        @foreach($monthlyBreakdown as $month => $data)
                            @if($data['count'] > 0)
                                <tr>
                                    <td>{{ $months[$month] ?? $month }}</td>
                                    <td>{{ $data['count'] }}</td>
                                    <td>{{ number_format($data['hours'], 1) }}h</td>
                                </tr>
                            @endif
                        @endforeach
                        @if(collect($monthlyBreakdown)->sum('count') === 0)
                            <tr>
                                <td colspan="3" style="text-align: center; color: #6b7280;">Aucune donnee.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Activities list --}}
    <div class="card" style="margin-top: 1.5rem;">
        <div class="card-header">
            <h3 class="card-title">Historique des activites {{ $year }}</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Activite</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Heures</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participations as $participation)
                        <tr>
                            <td>
                                <span style="font-weight: 500;">{{ $participation->activity->activity_date->format('d/m/Y') }}</span>
                                @if($participation->activity->start_time)
                                    <div style="font-size: 0.75rem; color: #6b7280;">{{ \Carbon\Carbon::parse($participation->activity->start_time)->format('H:i') }}</div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.volunteer.show', $participation->activity) }}">{{ $participation->activity->title }}</a>
                                @if($participation->activity->location)
                                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $participation->activity->location }}</div>
                                @endif
                            </td>
                            <td>
                                <span style="display: inline-flex; align-items: center; gap: 4px;">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $participation->activity->activityType?->color ?? '#ccc' }};"></span>
                                    {{ $participation->activity->activityType?->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ ['registered' => 'info', 'confirmed' => 'primary', 'attended' => 'success', 'absent' => 'danger', 'cancelled' => 'secondary'][$participation->status] ?? 'secondary' }}">
                                    {{ ['registered' => 'Inscrit', 'confirmed' => 'Confirme', 'attended' => 'Present', 'absent' => 'Absent', 'cancelled' => 'Annule'][$participation->status] ?? $participation->status }}
                                </span>
                            </td>
                            <td>
                                @if($participation->hours_spent)
                                    {{ number_format($participation->hours_spent, 1) }}h
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #6b7280;">Aucune participation cette annee.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- All-time stats --}}
    <div class="card" style="margin-top: 1.5rem; background: linear-gradient(135deg, #2C5F2D 0%, #16302B 100%); color: white;">
        <div class="card-body">
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Statistiques globales (toutes annees)</h3>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; text-align: center;">
                <div>
                    <div style="font-size: 2rem; font-weight: 600;">{{ $allTimeStats['total_activities'] }}</div>
                    <div style="font-size: 0.875rem; opacity: 0.9;">Activites</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 600;">{{ number_format($allTimeStats['total_hours'], 1) }}h</div>
                    <div style="font-size: 0.875rem; opacity: 0.9;">Heures</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 600;">{{ $allTimeStats['years_active'] }}</div>
                    <div style="font-size: 0.875rem; opacity: 0.9;">Annees actives</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 600;">{{ $allTimeStats['first_activity'] ? $allTimeStats['first_activity']->format('Y') : '-' }}</div>
                    <div style="font-size: 0.875rem; opacity: 0.9;">Depuis</div>
                </div>
            </div>
        </div>
    </div>
@endsection
