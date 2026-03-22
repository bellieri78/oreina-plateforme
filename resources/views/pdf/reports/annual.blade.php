<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport annuel {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            padding: 20px 0 30px;
            border-bottom: 2px solid #2C5F2D;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 22pt;
            color: #2C5F2D;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11pt;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 14pt;
            color: #2C5F2D;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-row {
            display: table-row;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
        }
        .stat-value {
            font-size: 20pt;
            font-weight: bold;
            color: #2C5F2D;
        }
        .stat-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            font-size: 9pt;
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
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #999;
        }
        .page-break {
            page-break-after: always;
        }
        .highlight {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .highlight-value {
            font-size: 24pt;
            font-weight: bold;
            color: #2C5F2D;
        }
        .highlight-label {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>OREINA - Rapport Annuel {{ $year }}</h1>
        <p>Les Lepidopteres de France</p>
    </div>

    {{-- Resume global --}}
    <div class="section">
        <h2 class="section-title">Resume</h2>
        <div class="stats-grid">
            <div class="stat-row">
                <div class="stat-box">
                    <div class="stat-value">{{ $totalMembers }}</div>
                    <div class="stat-label">Contacts total</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $activeMembers }}</div>
                    <div class="stat-label">Adherents actifs</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($donationStats['total_amount'], 0, ',', ' ') }} EUR</div>
                    <div class="stat-label">Dons collectes</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($volunteerStats['total_hours'], 0) }}h</div>
                    <div class="stat-label">Heures benevoles</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Adhesions --}}
    <div class="section">
        <h2 class="section-title">Adhesions</h2>
        <table>
            <tr>
                <th>Indicateur</th>
                <th class="text-right">Valeur</th>
            </tr>
            <tr>
                <td>Nouvelles adhesions en {{ $year }}</td>
                <td class="text-right">{{ $membershipStats['total'] }}</td>
            </tr>
            <tr>
                <td>Montant total des cotisations</td>
                <td class="text-right">{{ number_format($membershipStats['total_amount'], 2, ',', ' ') }} EUR</td>
            </tr>
            @foreach($membershipStats['by_type'] as $type => $count)
                <tr>
                    <td>Adhesions "{{ ucfirst($type) }}"</td>
                    <td class="text-right">{{ $count }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    {{-- Dons --}}
    <div class="section">
        <h2 class="section-title">Dons</h2>
        <table>
            <tr>
                <th>Indicateur</th>
                <th class="text-right">Valeur</th>
            </tr>
            <tr>
                <td>Nombre de dons</td>
                <td class="text-right">{{ $donationStats['total_count'] }}</td>
            </tr>
            <tr>
                <td>Montant total</td>
                <td class="text-right">{{ number_format($donationStats['total_amount'], 2, ',', ' ') }} EUR</td>
            </tr>
            <tr>
                <td>Donateurs uniques</td>
                <td class="text-right">{{ $donationStats['unique_donors'] }}</td>
            </tr>
            @if($donationStats['total_count'] > 0)
                <tr>
                    <td>Don moyen</td>
                    <td class="text-right">{{ number_format($donationStats['total_amount'] / $donationStats['total_count'], 2, ',', ' ') }} EUR</td>
                </tr>
            @endif
        </table>
    </div>

    {{-- Benevolat --}}
    <div class="section">
        <h2 class="section-title">Benevolat</h2>
        <table>
            <tr>
                <th>Indicateur</th>
                <th class="text-right">Valeur</th>
            </tr>
            <tr>
                <td>Nombre d'activites</td>
                <td class="text-right">{{ $volunteerStats['total_activities'] }}</td>
            </tr>
            <tr>
                <td>Activites terminees</td>
                <td class="text-right">{{ $volunteerStats['completed_activities'] }}</td>
            </tr>
            <tr>
                <td>Heures totales de benevolat</td>
                <td class="text-right">{{ number_format($volunteerStats['total_hours'], 1, ',', ' ') }}h</td>
            </tr>
            <tr>
                <td>Benevoles actifs</td>
                <td class="text-right">{{ $volunteerStats['unique_volunteers'] }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Rapport genere le {{ $generated_at->format('d/m/Y a H:i') }} - OREINA - Les Lepidopteres de France
    </div>
</body>
</html>
