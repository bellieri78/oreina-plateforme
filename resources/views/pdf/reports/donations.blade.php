<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des dons {{ $year }}</title>
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
            width: 25%;
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
        .months {
            font-size: 8pt;
        }
        .months td {
            padding: 3px 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport des Dons {{ $year }}</h1>
        <p>OREINA - Les Lepidopteres de France</p>
    </div>

    {{-- Statistiques --}}
    <div class="section">
        <h2 class="section-title">Statistiques globales</h2>
        <div class="stats-grid">
            <div class="stat-row">
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['total_count'] }}</div>
                    <div class="stat-label">Nombre de dons</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['total_amount'], 0, ',', ' ') }} EUR</div>
                    <div class="stat-label">Total collecte</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['unique_donors'] }}</div>
                    <div class="stat-label">Donateurs uniques</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['total_count'] > 0 ? number_format($stats['average_amount'], 0, ',', ' ') : 0 }} EUR</div>
                    <div class="stat-label">Don moyen</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Evolution mensuelle --}}
    <div class="section">
        <h2 class="section-title">Evolution mensuelle</h2>
        <table class="months">
            <thead>
                <tr>
                    <th>Mois</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-right">Montant</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $monthNames = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
                @endphp
                @foreach($stats['by_month'] as $month => $data)
                    <tr>
                        <td>{{ $monthNames[$month - 1] }}</td>
                        <td class="text-center">{{ $data['count'] }}</td>
                        <td class="text-right">{{ number_format($data['amount'], 2, ',', ' ') }} EUR</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; background: #e8f5e9;">
                    <td>Total</td>
                    <td class="text-center">{{ $stats['total_count'] }}</td>
                    <td class="text-right">{{ number_format($stats['total_amount'], 2, ',', ' ') }} EUR</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Par mode de paiement --}}
    <div class="section">
        <h2 class="section-title">Par mode de paiement</h2>
        <table>
            <thead>
                <tr>
                    <th>Mode</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-right">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['by_payment'] as $method => $data)
                    <tr>
                        <td>{{ ucfirst($method ?: 'Non renseigne') }}</td>
                        <td class="text-center">{{ $data['count'] }}</td>
                        <td class="text-right">{{ number_format($data['amount'], 2, ',', ' ') }} EUR</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    {{-- Liste detaillee --}}
    <div class="section">
        <h2 class="section-title">Liste des dons ({{ $donations->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Donateur</th>
                    <th class="text-right">Montant</th>
                    <th>Paiement</th>
                    <th class="text-center">Recu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($donations as $donation)
                    <tr>
                        <td>{{ $donation->donation_date->format('d/m/Y') }}</td>
                        <td>{{ $donation->member->full_name ?? '-' }}</td>
                        <td class="text-right">{{ number_format($donation->amount, 2, ',', ' ') }} EUR</td>
                        <td>{{ ucfirst($donation->payment_method ?? '-') }}</td>
                        <td class="text-center">{{ $donation->receipt_sent ? 'Oui' : 'Non' }}</td>
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
