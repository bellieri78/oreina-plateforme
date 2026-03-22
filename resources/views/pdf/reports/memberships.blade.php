<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des adhesions {{ $year }}</title>
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
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-secondary {
            background: #e5e7eb;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport des Adhesions {{ $year }}</h1>
        <p>OREINA - Les Lepidopteres de France</p>
    </div>

    {{-- Statistiques --}}
    <div class="section">
        <h2 class="section-title">Statistiques globales</h2>
        <div class="stats-grid">
            <div class="stat-row">
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['total'] }}</div>
                    <div class="stat-label">Total adhesions</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['active'] }}</div>
                    <div class="stat-label">Actives</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['expired'] }}</div>
                    <div class="stat-label">Expirees</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['total_amount'], 0, ',', ' ') }} EUR</div>
                    <div class="stat-label">Montant total</div>
                </div>
            </div>
        </div>

        <table>
            <tr>
                <th>Type d'adhesion</th>
                <th class="text-right">Nombre</th>
            </tr>
            @foreach($stats['by_type'] as $type => $count)
                <tr>
                    <td>{{ ucfirst($type) }}</td>
                    <td class="text-right">{{ $count }}</td>
                </tr>
            @endforeach
        </table>

        <table>
            <tr>
                <th>Mode de paiement</th>
                <th class="text-right">Nombre</th>
            </tr>
            @foreach($stats['by_payment'] as $method => $count)
                <tr>
                    <td>{{ ucfirst($method ?: 'Non renseigne') }}</td>
                    <td class="text-right">{{ $count }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="page-break"></div>

    {{-- Liste detaillee --}}
    <div class="section">
        <h2 class="section-title">Liste des adhesions ({{ $memberships->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Membre</th>
                    <th>Type</th>
                    <th>Debut</th>
                    <th>Fin</th>
                    <th class="text-right">Montant</th>
                    <th class="text-center">Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($memberships as $membership)
                    <tr>
                        <td>{{ $membership->member->full_name ?? '-' }}</td>
                        <td>{{ ucfirst($membership->type) }}</td>
                        <td>{{ $membership->start_date->format('d/m/Y') }}</td>
                        <td>{{ $membership->end_date->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($membership->amount, 2, ',', ' ') }} EUR</td>
                        <td class="text-center">
                            @if($membership->isActive())
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Expiree</span>
                            @endif
                        </td>
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
