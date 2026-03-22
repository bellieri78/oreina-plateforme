<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cartes d'adherent OREINA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 10mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            background: #ffffff;
        }

        .cards-container {
            width: 100%;
        }

        .card-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5mm;
            page-break-inside: avoid;
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
            border: 0.5pt solid #cccccc;
        }

        .card-header {
            margin-bottom: 3mm;
        }

        .logo-row {
            display: table;
            width: 100%;
        }

        .logo-cell {
            display: table-cell;
            vertical-align: top;
        }

        .logo {
            width: 10mm;
            height: 10mm;
            background: #ffffff;
            border-radius: 50%;
            text-align: center;
            line-height: 10mm;
            float: left;
            margin-right: 2mm;
        }

        .logo span {
            font-size: 14pt;
            color: #2C5F2D;
            font-weight: bold;
        }

        .logo-text h1 {
            font-size: 12pt;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .logo-text p {
            font-size: 5pt;
            opacity: 0.9;
        }

        .year-badge {
            float: right;
            background: rgba(255, 255, 255, 0.2);
            padding: 1.5mm 2.5mm;
            border-radius: 1.5mm;
            font-size: 14pt;
            font-weight: 700;
        }

        .card-body {
            margin-top: 2mm;
            clear: both;
        }

        .member-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 2.5mm;
            border-radius: 1.5mm;
            margin-bottom: 2mm;
        }

        .member-name {
            font-size: 11pt;
            font-weight: 700;
            margin-bottom: 1mm;
        }

        .member-type {
            font-size: 7pt;
            opacity: 0.9;
        }

        .card-details {
            font-size: 6.5pt;
        }

        .detail-row {
            margin-bottom: 0.5mm;
        }

        .detail-label {
            opacity: 0.8;
        }

        .detail-value {
            font-weight: 500;
        }

        .card-footer {
            position: absolute;
            bottom: 2.5mm;
            left: 4mm;
            right: 4mm;
        }

        .footer-left {
            float: left;
        }

        .footer-right {
            float: right;
        }

        .verification-code {
            font-family: 'Courier New', monospace;
            font-size: 7pt;
            background: rgba(255, 255, 255, 0.15);
            padding: 1mm 1.5mm;
            border-radius: 1mm;
            letter-spacing: 0.5px;
        }

        .issue-date {
            font-size: 5pt;
            opacity: 0.7;
            line-height: 9pt;
        }

        .page-info {
            text-align: center;
            font-size: 8pt;
            color: #666666;
            margin-bottom: 5mm;
        }
    </style>
</head>
<body>
    @php
        $chunked = array_chunk($cards, 10); // 2 cards per row, 5 rows per page
    @endphp

    @foreach($chunked as $pageIndex => $pageCards)
        @if($pageIndex > 0)
            <div style="page-break-before: always;"></div>
        @endif

        <div class="page-info">
            Cartes d'adherent OREINA - Page {{ $pageIndex + 1 }}/{{ count($chunked) }}
        </div>

        <div class="cards-container">
            @foreach(array_chunk($pageCards, 2) as $row)
                <div class="card-row">
                    @foreach($row as $card)
                        <div class="card">
                            <div class="card-header">
                                <div class="year-badge">{{ $card['year'] }}</div>
                                <div class="logo">
                                    <span>O</span>
                                </div>
                                <div class="logo-text">
                                    <h1>OREINA</h1>
                                    <p>Les Lepidopteres de France</p>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="member-info">
                                    <div class="member-name">{{ $card['member']->full_name }}</div>
                                    <div class="member-type">{{ $card['membershipType'] }}</div>
                                </div>

                                <div class="card-details">
                                    <div class="detail-row">
                                        <span class="detail-label">N&deg; :</span>
                                        <span class="detail-value">{{ $card['memberNumber'] }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Valide :</span>
                                        <span class="detail-value">{{ $card['validFrom'] }} - {{ $card['validUntil'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="footer-left">
                                    <span class="verification-code">{{ $card['verificationCode'] }}</span>
                                </div>
                                <div class="footer-right">
                                    <span class="issue-date">{{ $card['issueDate'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if(count($row) < 2)
                        <div class="card" style="visibility: hidden;"></div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</body>
</html>
