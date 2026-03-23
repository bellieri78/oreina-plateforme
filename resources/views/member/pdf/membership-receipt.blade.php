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
            margin-bottom: 30px;
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
            margin: 25px 0;
        }
        .receipt-number {
            text-align: center;
            font-size: 11px;
            color: #666;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .total {
            font-size: 14px;
            font-weight: bold;
        }
        .total td {
            border-top: 2px solid #333;
        }
        .member-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .thank-you {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: linear-gradient(135deg, rgba(133, 183, 157, 0.1), rgba(22, 48, 43, 0.05));
            border-radius: 5px;
        }
        .footer {
            margin-top: 40px;
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

    <h1>Reçu d'adhésion</h1>
    <div class="receipt-number">
        N° {{ str_pad($membership->id, 6, '0', STR_PAD_LEFT) }} - {{ $membership->start_date->format('Y') }}
    </div>

    <div class="member-info">
        <strong>{{ $member->civilite }} {{ $member->full_name }}</strong><br>
        @if($member->address)
            {{ $member->address }}<br>
            {{ $member->postal_code }} {{ $member->city }}<br>
        @endif
        @if($member->member_number)
            N° adhérent : {{ $member->member_number }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th style="text-align: right;">Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Adhésion {{ $membership->membershipType?->name ?? 'Standard' }}</strong><br>
                    <span style="color: #666; font-size: 11px;">
                        Période : {{ $membership->start_date->format('d/m/Y') }} au {{ $membership->end_date->format('d/m/Y') }}
                    </span>
                </td>
                <td style="text-align: right;">{{ number_format($membership->amount ?? 0, 2, ',', ' ') }} €</td>
            </tr>
            <tr class="total">
                <td>Total</td>
                <td style="text-align: right;">{{ number_format($membership->amount ?? 0, 2, ',', ' ') }} €</td>
            </tr>
        </tbody>
    </table>

    <p><strong>Date de règlement :</strong> {{ $membership->payment_date?->format('d/m/Y') ?? $membership->start_date->format('d/m/Y') }}</p>
    @if($membership->payment_method)
        <p><strong>Mode de paiement :</strong> {{ ucfirst($membership->payment_method) }}</p>
    @endif

    <div class="thank-you">
        <p><strong>Merci pour votre soutien !</strong></p>
        <p>Votre adhésion contribue à la protection des Lépidoptères de France.</p>
    </div>

    <div class="footer">
        <p>OREINA - Association loi 1901</p>
        <p>Siège social : [Adresse] | contact@oreina.org | www.oreina.org</p>
        <p style="margin-top: 10px;">Ce document ne constitue pas un reçu fiscal.</p>
    </div>
</body>
</html>
