<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #85B79D 0%, #16302B 100%);
            color: white;
            padding: 15px;
            height: 100%;
        }
        .card {
            height: 100%;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 18px;
            font-weight: bold;
        }
        .logo-sub {
            font-size: 8px;
            opacity: 0.8;
        }
        .validity {
            text-align: right;
            font-size: 9px;
        }
        .validity-date {
            font-size: 11px;
            font-weight: bold;
        }
        .member-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .member-label {
            font-size: 7px;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .footer {
            position: absolute;
            bottom: 15px;
            left: 15px;
            right: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
        }
        .member-number {
            font-family: monospace;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <div>
                <div class="logo">OREINA</div>
                <div class="logo-sub">Les Lépidoptères de France</div>
            </div>
            <div class="validity">
                <div>Valide jusqu'au</div>
                <div class="validity-date">{{ $membership->end_date->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="member-label">Membre</div>
        <div class="member-name">{{ $member->full_name }}</div>

        <div class="footer">
            <div>
                <div class="member-label">N° adhérent</div>
                <div class="member-number">{{ $member->member_number ?? 'N/A' }}</div>
            </div>
            <div style="text-align: right;">
                {{ $membership->membershipType?->name ?? 'Membre' }}
            </div>
        </div>
    </div>
</body>
</html>
