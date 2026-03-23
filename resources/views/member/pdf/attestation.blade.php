<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #85B79D;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #16302B;
        }
        .logo-sub {
            font-size: 12px;
            color: #85B79D;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            color: #16302B;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .content {
            margin: 30px 0;
        }
        .member-info {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .member-info p {
            margin: 5px 0;
        }
        .signature {
            margin-top: 60px;
            text-align: right;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #333;
            width: 200px;
            display: inline-block;
        }
        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">OREINA</div>
        <div class="logo-sub">Association pour l'étude et la protection des Lépidoptères de France</div>
    </div>

    <h1>Attestation d'adhésion</h1>

    <div class="content">
        <p>Je soussigné(e), Président(e) de l'association OREINA, certifie que :</p>

        <div class="member-info">
            <p><strong>{{ $member->civilite }} {{ $member->full_name }}</strong></p>
            @if($member->address)
            <p>{{ $member->address }}</p>
            <p>{{ $member->postal_code }} {{ $member->city }}</p>
            @endif
            @if($member->member_number)
            <p>N° adhérent : {{ $member->member_number }}</p>
            @endif
        </div>

        <p>est membre de notre association pour la période du <strong>{{ $membership->start_date->format('d/m/Y') }}</strong> au <strong>{{ $membership->end_date->format('d/m/Y') }}</strong>.</p>

        <p>Type d'adhésion : <strong>{{ $membership->membershipType?->name ?? 'Membre' }}</strong></p>

        @if($membership->amount)
        <p>Montant de la cotisation : <strong>{{ number_format($membership->amount, 2, ',', ' ') }} €</strong></p>
        @endif

        <p>Cette attestation est délivrée pour servir et valoir ce que de droit.</p>
    </div>

    <div class="signature">
        <p>Fait à Paris, le {{ now()->format('d/m/Y') }}</p>
        <p>Le/La Président(e)</p>
        <div class="signature-line"></div>
    </div>

    <div class="footer">
        <p>OREINA - Association loi 1901</p>
        <p>Siège social : [Adresse du siège] | contact@oreina.org | www.oreina.org</p>
    </div>
</body>
</html>
