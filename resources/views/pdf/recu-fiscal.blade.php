<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reçu Fiscal {{ $receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            color: #16302B;
            line-height: 1.4;
            padding: 20px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #85B79D;
            padding-bottom: 15px;
        }
        .logo-section {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
        }
        .logo-section img {
            max-width: 80px;
            max-height: 80px;
        }
        .title-section {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .main-title {
            font-size: 18pt;
            font-weight: bold;
            color: #85B79D;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12pt;
            color: #356B8A;
        }
        .receipt-number {
            display: table-cell;
            width: 150px;
            vertical-align: middle;
            text-align: right;
        }
        .receipt-badge {
            background-color: #85B79D;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 10pt;
        }

        .legal-notice {
            background-color: #f8f9fa;
            border: 1px solid #E1E3EA;
            border-left: 4px solid #356B8A;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 9pt;
        }

        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #356B8A;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #E1E3EA;
        }

        .info-grid {
            width: 100%;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .info-label {
            display: table-cell;
            width: 180px;
            font-weight: bold;
            color: #5E6278;
        }
        .info-value {
            display: table-cell;
        }

        .amount-box {
            background: linear-gradient(135deg, rgba(133, 183, 157, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%);
            border: 2px solid #85B79D;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .amount-label {
            font-size: 10pt;
            color: #5E6278;
            margin-bottom: 5px;
        }
        .amount-value {
            font-size: 24pt;
            font-weight: bold;
            color: #85B79D;
        }
        .amount-letters {
            font-size: 10pt;
            font-style: italic;
            color: #5E6278;
            margin-top: 5px;
        }

        .columns {
            display: table;
            width: 100%;
        }
        .column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        .column-gap {
            display: table-cell;
            width: 4%;
        }

        .certification {
            background-color: #f8f9fa;
            border: 1px solid #E1E3EA;
            padding: 15px;
            margin-top: 20px;
            font-size: 9pt;
        }
        .certification-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .checkbox-item {
            margin: 5px 0;
            padding-left: 20px;
            position: relative;
        }
        .checkbox-item:before {
            content: "☑";
            position: absolute;
            left: 0;
            color: #85B79D;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        .signature-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .signature-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .signature-box {
            border: 1px dashed #E1E3EA;
            padding: 15px;
            min-height: 80px;
        }
        .signature-label {
            font-size: 9pt;
            color: #5E6278;
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #E1E3EA;
            font-size: 8pt;
            color: #5E6278;
            text-align: center;
        }
        .footer-notice {
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-section">
                @if(file_exists(public_path($association['logo'] ?? 'images/logo.jpg')))
                    <img src="{{ public_path($association['logo'] ?? 'images/logo.jpg') }}" alt="Logo">
                @endif
            </div>
            <div class="title-section">
                <div class="main-title">REÇU AU TITRE DES DONS</div>
                <div class="subtitle">Articles 200, 238 bis et 978 du Code général des impôts</div>
            </div>
            <div class="receipt-number">
                <div class="receipt-badge">N° {{ $receipt_number }}</div>
            </div>
        </div>

        <div class="legal-notice">
            <strong>Important :</strong> Ce reçu vous permet de bénéficier d'une réduction d'impôt égale à 66% du montant
            du don, dans la limite de 20% du revenu imposable. Les dons aux associations d'intérêt général
            ouvrent droit à cette réduction fiscale.
        </div>

        <div class="columns">
            <div class="column">
                <div class="section">
                    <div class="section-title">Organisme bénéficiaire</div>
                    <div class="info-grid">
                        <div class="info-row">
                            <span class="info-label">Nom :</span>
                            <span class="info-value">{{ $association['name'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Adresse :</span>
                            <span class="info-value">{{ $association['address'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"></span>
                            <span class="info-value">{{ $association['postal_code'] }} {{ $association['city'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">SIRET :</span>
                            <span class="info-value">{{ $association['siret'] }}</span>
                        </div>
                        @if(!empty($association['rna']))
                        <div class="info-row">
                            <span class="info-label">N° RNA :</span>
                            <span class="info-value">{{ $association['rna'] }}</span>
                        </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label">Objet :</span>
                            <span class="info-value">{{ $association['objet'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column-gap"></div>
            <div class="column">
                <div class="section">
                    <div class="section-title">Donateur</div>
                    <div class="info-grid">
                        <div class="info-row">
                            <span class="info-label">Nom :</span>
                            <span class="info-value">{{ $donor['name'] }}</span>
                        </div>
                        @if(!empty($donor['address']))
                        <div class="info-row">
                            <span class="info-label">Adresse :</span>
                            <span class="info-value">{{ $donor['address'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"></span>
                            <span class="info-value">{{ $donor['postal_code'] }} {{ $donor['city'] }}</span>
                        </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label">Email :</span>
                            <span class="info-value">{{ $donor['email'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="amount-box">
            <div class="amount-label">Montant du don</div>
            <div class="amount-value">{{ number_format($amount, 2, ',', ' ') }} €</div>
            <div class="amount-letters">{{ $amount_letters }}</div>
        </div>

        <div class="section">
            <div class="section-title">Informations sur le don</div>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Date du versement :</span>
                    <span class="info-value">{{ $donation_date->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mode de versement :</span>
                    <span class="info-value">{{ $payment_method }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nature du don :</span>
                    <span class="info-value">Numéraire (somme d'argent)</span>
                </div>
                @if($donation->campaign)
                <div class="info-row">
                    <span class="info-label">Campagne :</span>
                    <span class="info-value">{{ $donation->campaign }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="certification">
            <div class="certification-title">L'organisme bénéficiaire certifie sur l'honneur que :</div>
            <div class="checkbox-item">Il est une association d'intérêt général au sens des articles 200 et 238 bis du CGI</div>
            <div class="checkbox-item">Il est à but non lucratif et ne fonctionne pas au profit d'un cercle restreint de personnes</div>
            <div class="checkbox-item">Le don ne donne lieu à aucune contrepartie directe ou indirecte au profit du donateur</div>
            <div class="checkbox-item">Les conditions d'application de la réduction d'impôt sont remplies</div>
        </div>

        <div class="signature-section">
            <div class="signature-left">
                <div class="signature-label">Date d'émission : {{ $receipt_date->format('d/m/Y') }}</div>
            </div>
            <div class="signature-right">
                <div class="signature-box">
                    <div class="signature-label">Signature et cachet de l'organisme</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p class="footer-notice">
                Ce document doit être conservé et joint à votre déclaration de revenus.
                Il tient lieu de justificatif en cas de contrôle de l'administration fiscale.
            </p>
            <p style="margin-top: 10px;">
                {{ $association['name'] }} - SIRET : {{ $association['siret'] }}
                @if(!empty($association['rna']))
                 - RNA : {{ $association['rna'] }}
                @endif
            </p>
        </div>
    </div>
</body>
</html>
