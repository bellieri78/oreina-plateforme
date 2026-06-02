<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            width: 100%;
            height: 100%;
            color: #356B8A;
        }
        .bg {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ffffff;
            border: 1px solid #DBCBC7;
        }
        .watermark {
            position: fixed;
            top: 6px;
            right: 8px;
            width: 150px;
        }
        .label {
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #7DA0B5;
            margin-bottom: 2px;
        }
        .brand { font-size: 20px; font-weight: bold; letter-spacing: 1px; color: #356B8A; }
        .value-sm { font-size: 11px; font-weight: bold; color: #356B8A; }
        .member-name { font-size: 16px; font-weight: bold; color: #356B8A; }
        .member-number { font-family: 'Courier New', monospace; font-size: 12px; font-weight: bold; color: #356B8A; }

        .top-left    { position: fixed; top: 18px;  left: 20px; }
        .member      { position: fixed; top: 66px;  left: 20px; }
        .validity    { position: fixed; top: 104px; left: 20px; }
        .bottom-left { position: fixed; bottom: 18px; left: 20px; }
        .bottom-right{ position: fixed; bottom: 18px; right: 20px; text-align: right; font-size: 11px; font-weight: bold; color: #356B8A; }
    </style>
</head>
<body>
    @php
        $flyPath = public_path('images/logo-papillon-watermark.png');
        $flyData = is_file($flyPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($flyPath)) : null;
    @endphp

    <div class="bg"></div>

    @if($flyData)
        <img src="{{ $flyData }}" alt="" class="watermark">
    @endif

    <div class="top-left">
        <div class="label">Association</div>
        <div class="brand">OREINA</div>
    </div>

    <div class="member">
        <div class="label">Membre</div>
        <div class="member-name">{{ $member->full_name }}</div>
    </div>

    <div class="validity">
        <div class="label">Valide jusqu'au</div>
        <div class="value-sm">{{ $membership->end_date->format('d/m/Y') }}</div>
    </div>

    <div class="bottom-left">
        <div class="label">N° adhérent</div>
        <div class="member-number">{{ $member->member_number ?? 'N/A' }}</div>
    </div>

    <div class="bottom-right">
        {{ $membership->membershipType?->name ?? 'Membre' }}
    </div>
</body>
</html>
