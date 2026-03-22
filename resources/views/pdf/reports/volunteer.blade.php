<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport du benevolat {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            padding: 15px 0 20px;
            border-bottom: 2px solid #2C5F2D;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18pt;
            color: #2C5F2D;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 10pt;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 12pt;
            color: #2C5F2D;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-row {
            display: table-row;
        }
        .stat-box {
            display: table-cell;
            width: 20%;
            padding: 8px;
            text-align: center;
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
        }
        .stat-value {
            font-size: 16pt;
            font-weight: bold;
            color: #2C5F2D;
        }
        .stat-label {
            font-size: 7pt;
            color: #666;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            text-align: left;
            font-size: 8pt;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            color: #999;
        }
        .page-break {
            page-break-after: always;
        }
        .rank {
            display: inline-block;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            text-align: center;
            line-height: 18px;
            font-size: 8pt;
            font-weight: bold;
            color: white;
        }
        .rank-1 { background: #FFD700; }
        .rank-2 { background: #C0C0C0; }
        .rank-3 { background: #CD7F32; }
        .rank-other { background: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport du Benevolat {{ $year }}</h1>
        <p>OREINA - Les Lepidopteres de France</p>
    </div>

    {{-- Statistiques --}}
    <div class="section">
        <h2 class="section-title">Statistiques globales</h2>
        <div class="stats-grid">
            <div class="stat-row">
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['total_activities'] }}</div>
                    <div class="stat-label">Activites</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['completed'] }}</div>
                    <div class="stat-label">Terminees</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['total_hours'], 0) }}h</div>
                    <div class="stat-label">Heures totales</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['unique_volunteers'] }}</div>
                    <div class="stat-label">Benevoles actifs</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['total_participations'] }}</div>
                    <div class="stat-label">Participations</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Par type d'activite --}}
    <div class="section">
        <h2 class="section-title">Par type d'activite</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th class="text-center">Activites</th>
                    <th class="text-center">Participations</th>
                    <th class="text-right">Heures</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['by_type'] as $typeName => $data)
                    <tr>
                        <td>{{ $typeName }}</td>
                        <td class="text-center">{{ $data['count'] }}</td>
                        <td class="text-center">{{ $data['participants'] }}</td>
                        <td class="text-right">{{ number_format($data['hours'], 1) }}h</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Evolution mensuelle --}}
    <div class="section">
        <h2 class="section-title">Evolution mensuelle</h2>
        <table>
            <thead>
                <tr>
                    <th>Mois</th>
                    <th class="text-center">Activites</th>
                    <th class="text-center">Participations</th>
                    <th class="text-right">Heures</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $monthNames = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
                @endphp
                @foreach($stats['by_month'] as $month => $data)
                    @if($data['activities'] > 0)
                        <tr>
                            <td>{{ $monthNames[$month - 1] }}</td>
                            <td class="text-center">{{ $data['activities'] }}</td>
                            <td class="text-center">{{ $data['participants'] }}</td>
                            <td class="text-right">{{ number_format($data['hours'], 1) }}h</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    {{-- Top benevoles --}}
    <div class="section">
        <h2 class="section-title">Top benevoles {{ $year }}</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Benevole</th>
                    <th class="text-center">Activites</th>
                    <th class="text-right">Heures</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['top_volunteers'] as $index => $volunteer)
                    <tr>
                        <td class="text-center">
                            @if($index < 3)
                                <span class="rank rank-{{ $index + 1 }}">{{ $index + 1 }}</span>
                            @else
                                <span class="rank rank-other">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td>{{ $volunteer['member']->full_name ?? '-' }}</td>
                        <td class="text-center">{{ $volunteer['activities'] }}</td>
                        <td class="text-right">{{ number_format($volunteer['hours'], 1) }}h</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Liste des activites --}}
    <div class="section">
        <h2 class="section-title">Liste des activites ({{ $activities->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Titre</th>
                    <th>Type</th>
                    <th class="text-center">Presents</th>
                    <th class="text-right">Heures</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities->where('status', 'completed') as $activity)
                    <tr>
                        <td>{{ $activity->activity_date->format('d/m/Y') }}</td>
                        <td>{{ $activity->title }}</td>
                        <td>{{ $activity->activityType?->name ?? '-' }}</td>
                        <td class="text-center">{{ $activity->participations->where('status', 'attended')->count() }}</td>
                        <td class="text-right">{{ number_format($activity->total_hours, 1) }}h</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Rapport genere le {{ $generated_at->format('d/m/Y a H:i') }} - OREINA - Les Lepidopteres de France
    </div>
</body>
</html>
