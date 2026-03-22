<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de benevolat - {{ $member->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.6;
        }
        .container {
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo {
            font-size: 28pt;
            font-weight: bold;
            color: #2C5F2D;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12pt;
            color: #666;
            margin-bottom: 20px;
        }
        .title {
            font-size: 22pt;
            color: #16302B;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-top: 2px solid #2C5F2D;
            border-bottom: 2px solid #2C5F2D;
            padding: 15px 0;
            margin: 30px 0;
        }
        .content {
            text-align: justify;
            margin-bottom: 30px;
        }
        .content p {
            margin-bottom: 15px;
        }
        .highlight {
            background: #e8f5e9;
            padding: 15px 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .highlight-name {
            font-size: 18pt;
            font-weight: bold;
            color: #2C5F2D;
            text-align: center;
        }
        .stats-box {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 20px;
            margin: 25px 0;
        }
        .stats-title {
            font-weight: bold;
            color: #2C5F2D;
            margin-bottom: 15px;
            font-size: 12pt;
        }
        .stats-grid {
            display: table;
            width: 100%;
        }
        .stats-row {
            display: table-row;
        }
        .stats-cell {
            display: table-cell;
            text-align: center;
            padding: 10px;
        }
        .stats-value {
            font-size: 24pt;
            font-weight: bold;
            color: #2C5F2D;
        }
        .stats-label {
            font-size: 9pt;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }
        th {
            background: #f3f4f6;
        }
        .text-right {
            text-align: right;
        }
        .signature-area {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .signature-left, .signature-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .signature-right {
            text-align: right;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        .footer {
            position: fixed;
            bottom: 30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">OREINA</div>
            <div class="subtitle">Les Lepidopteres de France</div>
        </div>

        <div class="title">Attestation de Benevolat</div>

        <div class="content">
            <p>Je soussigne(e), President(e) de l'association OREINA, certifie que :</p>

            <div class="highlight">
                <div class="highlight-name">{{ $member->full_name }}</div>
            </div>

            <p>
                A participe benevolement aux activites de l'association au cours de l'annee <strong>{{ $year }}</strong>.
            </p>

            <div class="stats-box">
                <div class="stats-title">Engagement benevole en {{ $year }}</div>
                <div class="stats-grid">
                    <div class="stats-row">
                        <div class="stats-cell">
                            <div class="stats-value">{{ $stats['total_activities'] }}</div>
                            <div class="stats-label">activites</div>
                        </div>
                        <div class="stats-cell">
                            <div class="stats-value">{{ number_format($stats['total_hours'], 1) }}h</div>
                            <div class="stats-label">heures de benevolat</div>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($stats['by_type']) > 0)
                <p><strong>Repartition par type d'activite :</strong></p>
                <table>
                    <thead>
                        <tr>
                            <th>Type d'activite</th>
                            <th class="text-right">Participations</th>
                            <th class="text-right">Heures</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['by_type'] as $typeName => $data)
                            <tr>
                                <td>{{ $typeName }}</td>
                                <td class="text-right">{{ $data['count'] }}</td>
                                <td class="text-right">{{ number_format($data['hours'], 1) }}h</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <p>
                Cette attestation est delivree a l'interesse(e) pour servir et valoir ce que de droit.
            </p>
        </div>

        <div class="signature-area">
            <div class="signature-left">
                <p>Fait a _________________</p>
                <p>Le {{ $generated_at->format('d/m/Y') }}</p>
            </div>
            <div class="signature-right">
                <p>Le/La President(e)</p>
                <div class="signature-line">
                    Signature et cachet
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        OREINA - Les Lepidopteres de France - Association loi 1901<br>
        Document genere le {{ $generated_at->format('d/m/Y a H:i') }}
    </div>
</body>
</html>
