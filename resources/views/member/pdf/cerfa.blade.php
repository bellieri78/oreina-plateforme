<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
            padding: 30px;
        }
        .header {
            border: 2px solid #333;
            padding: 15px;
            margin-bottom: 20px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .cerfa-ref {
            font-size: 10px;
            color: #666;
        }
        h1 {
            font-size: 14px;
            text-align: center;
            margin: 10px 0;
            text-transform: uppercase;
        }
        h2 {
            font-size: 12px;
            background: #f0f0f0;
            padding: 8px;
            margin: 15px 0 10px 0;
        }
        .section {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .row {
            margin: 5px 0;
        }
        .label {
            font-weight: bold;
        }
        .amount {
            font-size: 16px;
            font-weight: bold;
            color: #16302B;
        }
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #333;
            margin-right: 5px;
            vertical-align: middle;
        }
        .checkbox.checked {
            background: #333;
        }
        .signature-area {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #666;
            text-align: center;
        }
        .important {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            margin: 15px 0;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div>
                <strong>OREINA</strong><br>
                Association loi 1901<br>
                Intérêt général
            </div>
            <div class="cerfa-ref">
                Cerfa n° 11580*04<br>
                Article 200, 238 bis et 978 du CGI
            </div>
        </div>
        <h1>Reçu au titre des dons à certains organismes d'intérêt général</h1>
    </div>

    <h2>1. Organisme bénéficiaire du don</h2>
    <div class="section">
        <div class="row"><span class="label">Nom :</span> OREINA - Les Lépidoptères de France</div>
        <div class="row"><span class="label">Adresse :</span> [Adresse du siège social]</div>
        <div class="row"><span class="label">Objet :</span> Étude et protection des Lépidoptères de France</div>
        <div class="row">
            <span class="checkbox checked"></span> reconnu d'utilité publique
            <span class="checkbox"></span> d'intérêt général
        </div>
    </div>

    <h2>2. Donateur</h2>
    <div class="section">
        <div class="row"><span class="label">Nom :</span> {{ $member->last_name }}</div>
        <div class="row"><span class="label">Prénom :</span> {{ $member->first_name }}</div>
        <div class="row"><span class="label">Adresse :</span> {{ $member->address }}</div>
        <div class="row">{{ $member->postal_code }} {{ $member->city }}</div>
    </div>

    <h2>3. Don</h2>
    <div class="section">
        <div class="row"><span class="label">Date du don :</span> {{ $donation->donation_date->format('d/m/Y') }}</div>
        <div class="row">
            <span class="label">Montant :</span>
            <span class="amount">{{ number_format($donation->amount, 2, ',', ' ') }} €</span>
        </div>
        <div class="row"><span class="label">En toutes lettres :</span> {{ \NumberFormatter::create('fr_FR', \NumberFormatter::SPELLOUT)->format($donation->amount) }} euros</div>
        <div class="row" style="margin-top: 10px;">
            <span class="label">Forme du don :</span><br>
            <span class="checkbox {{ $donation->payment_method !== 'nature' ? 'checked' : '' }}"></span> Numéraire
            <span class="checkbox"></span> Autres
        </div>
        <div class="row">
            <span class="label">Mode de versement :</span><br>
            <span class="checkbox {{ $donation->payment_method === 'cheque' ? 'checked' : '' }}"></span> Chèque
            <span class="checkbox {{ in_array($donation->payment_method, ['card', 'online', 'virement']) ? 'checked' : '' }}"></span> Virement/CB
            <span class="checkbox {{ $donation->payment_method === 'especes' ? 'checked' : '' }}"></span> Espèces
        </div>
    </div>

    <div class="important">
        <strong>Information :</strong> Le don ouvre droit à une réduction d'impôt sur le revenu égale à 66% de son montant, dans la limite de 20% du revenu imposable. Pour les entreprises, la réduction est de 60% dans la limite de 0,5% du chiffre d'affaires.
    </div>

    <div class="signature-area">
        <div class="row">
            <span class="label">Date :</span> {{ now()->format('d/m/Y') }}
        </div>
        <div class="row" style="margin-top: 20px;">
            <span class="label">Signature du responsable de l'organisme :</span>
        </div>
        <div style="margin-top: 40px; border-bottom: 1px solid #333; width: 200px;"></div>
    </div>

    <div class="footer">
        <p>Ce reçu est à conserver et à joindre à votre déclaration de revenus.</p>
        <p>OREINA - N° RNA : [Numéro RNA] | SIRET : [Numéro SIRET]</p>
    </div>
</body>
</html>
