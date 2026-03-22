<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Carte d'adherent OREINA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            background: #ffffff;
        }

        .card {
            width: 85.6mm;
            height: 54mm;
            background: linear-gradient(135deg, #16302B 0%, #2C5F2D 100%);
            border-radius: 3mm;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            padding: 4mm;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 3mm;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 2mm;
        }

        .logo {
            width: 12mm;
            height: 12mm;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            width: 10mm;
            height: 10mm;
        }

        .logo-text {
            color: #ffffff;
        }

        .logo-text h1 {
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .logo-text p {
            font-size: 6pt;
            opacity: 0.9;
            margin-top: 0.5mm;
        }

        .year-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 2mm 3mm;
            border-radius: 2mm;
            font-size: 16pt;
            font-weight: 700;
        }

        .card-body {
            margin-top: 2mm;
        }

        .member-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 3mm;
            border-radius: 2mm;
            margin-bottom: 2mm;
        }

        .member-name {
            font-size: 12pt;
            font-weight: 700;
            margin-bottom: 1mm;
        }

        .member-type {
            font-size: 8pt;
            opacity: 0.9;
        }

        .card-details {
            display: table;
            width: 100%;
            font-size: 7pt;
        }

        .detail-row {
            display: table-row;
        }

        .detail-label {
            display: table-cell;
            padding: 0.5mm 0;
            opacity: 0.8;
            width: 25%;
        }

        .detail-value {
            display: table-cell;
            padding: 0.5mm 0;
            font-weight: 500;
        }

        .card-footer {
            position: absolute;
            bottom: 3mm;
            left: 4mm;
            right: 4mm;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .verification-code {
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            background: rgba(255, 255, 255, 0.15);
            padding: 1mm 2mm;
            border-radius: 1mm;
            letter-spacing: 1px;
        }

        .issue-date {
            font-size: 6pt;
            opacity: 0.7;
        }

        /* Decorative elements */
        .decoration {
            position: absolute;
            width: 40mm;
            height: 40mm;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -15mm;
            right: -10mm;
        }

        .decoration-2 {
            position: absolute;
            width: 30mm;
            height: 30mm;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            bottom: -10mm;
            left: -5mm;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="decoration"></div>
        <div class="decoration-2"></div>

        <div class="card-header">
            <div class="logo-section">
                <div class="logo">
                    <span style="font-size: 16pt; color: #2C5F2D; font-weight: bold;">O</span>
                </div>
                <div class="logo-text">
                    <h1>OREINA</h1>
                    <p>Les Lepidopteres de France</p>
                </div>
            </div>
            <div class="year-badge">{{ $year }}</div>
        </div>

        <div class="card-body">
            <div class="member-info">
                <div class="member-name">{{ $member->full_name }}</div>
                <div class="member-type">{{ $membershipType }}</div>
            </div>

            <div class="card-details">
                <div class="detail-row">
                    <span class="detail-label">N&deg; Adherent :</span>
                    <span class="detail-value">{{ $memberNumber }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Valide du :</span>
                    <span class="detail-value">{{ $validFrom }} au {{ $validUntil }}</span>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="verification-code">{{ $verificationCode }}</div>
            <div class="issue-date">Emise le {{ $issueDate }}</div>
        </div>
    </div>
</body>
</html>
