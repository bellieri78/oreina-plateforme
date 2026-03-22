@extends('layouts.admin')

@section('title', 'Benevolat')

@section('breadcrumb')
    <span>Benevolat</span>
@endsection

@section('content')
    {{-- Year selector --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <label style="font-weight: 500;">Annee :</label>
            <select onchange="window.location.href='?year='+this.value" class="form-input" style="width: auto;">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.volunteer.activities') }}" class="btn btn-secondary">Toutes les activites</a>
            <a href="{{ route('admin.volunteer.create') }}" class="btn btn-primary">Nouvelle activite</a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid" style="margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_activities'] }}</div>
            <div class="stat-label">Activites {{ $year }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['completed'] }}</div>
            <div class="stat-label">Terminees</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['upcoming'] }}</div>
            <div class="stat-label">A venir</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['total_hours'], 1) }}h</div>
            <div class="stat-label">Heures totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_volunteers'] }}</div>
            <div class="stat-label">Benevoles actifs</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        {{-- Upcoming activities --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Activites a venir</h3>
                <a href="{{ route('admin.volunteer.activities', ['status' => 'planned']) }}" class="btn btn-secondary btn-sm">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Activite</th>
                            <th>Inscrits</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingActivities as $activity)
                            <tr>
                                <td>
                                    <span style="font-weight: 500;">{{ $activity->activity_date->format('d/m') }}</span>
                                    @if($activity->start_time)
                                        <span style="font-size: 0.75rem; color: #6b7280;">{{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.volunteer.show', $activity) }}">{{ $activity->title }}</a>
                                    <div style="font-size: 0.75rem; color: #6b7280;">
                                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: {{ $activity->activityType?->color ?? '#ccc' }}; margin-right: 4px;"></span>
                                        {{ $activity->activityType?->name }}
                                    </div>
                                </td>
                                <td>
                                    {{ $activity->confirmed_participants_count }}
                                    @if($activity->max_participants)
                                        / {{ $activity->max_participants }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #6b7280;">Aucune activite planifiee.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent activities --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Activites recentes</h3>
                <a href="{{ route('admin.volunteer.activities', ['status' => 'completed']) }}" class="btn btn-secondary btn-sm">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Activite</th>
                            <th>Presents</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivities as $activity)
                            <tr>
                                <td>{{ $activity->activity_date->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.volunteer.show', $activity) }}">{{ $activity->title }}</a>
                                    <div style="font-size: 0.75rem; color: #6b7280;">
                                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: {{ $activity->activityType?->color ?? '#ccc' }}; margin-right: 4px;"></span>
                                        {{ $activity->activityType?->name }}
                                    </div>
                                </td>
                                <td>{{ $activity->attended_participants_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #6b7280;">Aucune activite passee.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top volunteers --}}
    <div class="card" style="margin-top: 1.5rem;">
        <div class="card-header">
            <h3 class="card-title">Top benevoles {{ $year }}</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Benevole</th>
                        <th>Heures</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topVolunteers as $index => $volunteer)
                        <tr>
                            <td>
                                @if($index < 3)
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: {{ ['#FFD700', '#C0C0C0', '#CD7F32'][$index] }}; color: #fff; font-weight: bold; font-size: 0.75rem;">
                                        {{ $index + 1 }}
                                    </span>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.volunteer.member-report', $volunteer) }}">{{ $volunteer->full_name }}</a>
                            </td>
                            <td>
                                <span style="font-weight: 500;">{{ number_format($volunteer->total_hours, 1) }}h</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #6b7280;">Aucune donnee pour cette annee.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
