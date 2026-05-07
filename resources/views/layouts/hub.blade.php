<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'OREINA - Association pour l\'étude et la protection des Lépidoptères de France')">
    <title>@yield('title', 'OREINA') - Les Lépidoptères de France</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* === OREINA Design System V4 — Hub Styles === */
        :root {
            --forest: #16302B;
            --sage: #85B79D;
            --blue: #356B8A;
            --gold: #EDC442;
            --coral: #EF7A5C;
            --beige: #DBCBC7;
            --bg: #F5F2EE;
            --surface: #FFFFFF;
            --surface-soft: #FAF8F5;
            --surface-blue: #EEF4F8;
            --surface-sage: #EEF6F1;
            --text: #1C2B27;
            --muted: #67746F;
            --border: rgba(22,48,43,0.10);
            --shadow: 0 16px 36px rgba(22,48,43,0.08);
            --radius-xl: 28px;
            --radius-lg: 20px;
            --radius-md: 14px;
            --container: 1180px;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: var(--bg);
            line-height: 1.5;
            font-size: 15px;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 0% 0%, rgba(53,107,138,0.04), transparent 18%),
                radial-gradient(circle at 100% 0%, rgba(133,183,157,0.05), transparent 18%),
                radial-gradient(circle at 100% 100%, rgba(237,196,66,0.05), transparent 20%);
            z-index: -1;
        }
        a { color: inherit; text-decoration: none; }
        button { font: inherit; }
        img { max-width: 100%; display: block; }
        h1, h2, h3, h4 { font-weight: 800; line-height: 1.05; letter-spacing: -0.04em; color: var(--text); }

        .container {
            width: min(calc(100% - 32px), var(--container));
            margin: 0 auto;
        }

        /* Icons */
        .icon {
            width: 18px; height: 18px;
            display: inline-flex; align-items: center; justify-content: center;
            flex: 0 0 18px;
        }
        .icon svg { width: 18px; height: 18px; stroke-width: 2; }
        .icon-white { color: white; }
        .icon-blue { color: var(--blue); }
        .icon-sage { color: var(--forest); }
        .icon-gold { color: #8b6c05; }
        .icon-coral { color: var(--coral); }

        /* === BUTTONS === */
        .btn {
            height: 46px;
            padding: 0 18px;
            border-radius: 14px;
            border: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            font-weight: 800;
            font-size: 14px;
            transition: 0.2s ease;
            white-space: nowrap;
            text-decoration: none;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary {
            background: var(--gold);
            color: var(--forest);
            box-shadow: 0 12px 24px rgba(237,196,66,0.18);
        }
        .btn-secondary {
            background: rgba(53,107,138,0.08);
            color: var(--blue);
            border: 1px solid rgba(53,107,138,0.14);
        }
        .btn-ghost-light {
            background: rgba(255,255,255,0.14);
            color: white;
            border: 1px solid rgba(255,255,255,0.16);
        }
        .btn-sm {
            height: 36px;
            padding: 0 14px;
            font-size: 13px;
            border-radius: 12px;
        }
        .btn-ghost-dark {
            background: rgba(255,255,255,0.10);
            color: white;
            border: 1px solid rgba(255,255,255,0.18);
        }
        .btn-ghost-dark:hover {
            background: rgba(255,255,255,0.18);
        }

        /* === SITE TOPBAR (ligne 1) === */
        .site-topbar {
            background: var(--forest);
            color: rgba(255,255,255,0.92);
            font-size: 13px;
        }
        .site-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 16px;
            min-height: 44px;
            padding: 4px 0;
        }
        .topbar-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: 0;
            color: rgba(255,255,255,0.85);
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 8px;
            transition: color 0.15s ease, background 0.15s ease;
        }
        .topbar-link:hover { color: white; background: rgba(255,255,255,0.06); }
        .topbar-link i, .topbar-link svg { width: 16px; height: 16px; }

        /* === HEADER === */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(12px);
            background: rgba(245,242,238,0.84);
            border-bottom: 1px solid rgba(22,48,43,0.06);
        }
        .site-header-inner {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }
        .brand-mark {
            height: 52px;
            width: auto;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: var(--forest);
            overflow: hidden;
            flex-shrink: 0;
        }
        .brand-mark img {
            height: 100%;
            width: auto;
        }
        .brand-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
            border-left: 1px solid #DBCBC7;
            padding-left: 1rem;
        }
        .brand-text strong {
            font-size: 18px;
            letter-spacing: -0.03em;
            white-space: nowrap;
        }
        .brand-text span {
            color: var(--muted);
            font-size: 12px;
            white-space: nowrap;
        }
        .hub-nav {
            display: flex;
            align-items: center;
            gap: 24px;
            color: var(--muted);
            font-size: 15px;
            font-weight: 600;
        }
        .hub-nav a {
            position: relative;
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .hub-nav a:hover {
            color: var(--text);
        }
        .hub-nav a.active {
            color: #16302B;
            background: rgba(133, 183, 157, 0.12);
        }
        .hub-nav a.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 2px;
            background: #16302B;
            border-radius: 1px;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            color: var(--text);
        }
        .mobile-menu-toggle i, .mobile-menu-toggle svg { width: 24px; height: 24px; }
        .mobile-nav {
            padding: 0 16px 20px;
            border-top: 1px solid rgba(22,48,43,0.06);
        }
        .mobile-nav .hub-nav-mobile {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 12px 0;
        }
        .mobile-nav .hub-nav-mobile a {
            padding: 10px 14px;
            border-radius: 10px;
            color: var(--muted);
            font-size: 15px;
            font-weight: 600;
        }
        .mobile-nav .hub-nav-mobile a:hover, .mobile-nav .hub-nav-mobile a.active {
            background: rgba(22,48,43,0.05);
            color: var(--text);
        }
        .mobile-nav .header-actions-mobile {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(22,48,43,0.06);
        }
        .mobile-nav .header-actions-mobile .btn {
            width: 100%;
            justify-content: center;
        }

        /* === EYEBROW === */
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
        }
        .eyebrow.sage { background: rgba(133,183,157,0.16); color: #2f694e; }
        .eyebrow.gold { background: rgba(237,196,66,0.20); color: #8b6c05; }
        .eyebrow.blue { background: rgba(53,107,138,0.10); color: var(--blue); }

        /* === PUB CARD ICON === */
        .pub-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
        }
        .pub-card-icon.sage { background: rgba(133,183,157,0.14); }
        .pub-card-icon.coral { background: rgba(239,122,92,0.10); }
        .pub-card-icon.gold { background: rgba(237,196,66,0.14); }
        .pub-card-icon.blue { background: rgba(53,107,138,0.10); }

        /* === TEXT LINK === */
        .text-link {
            color: var(--blue);
            font-size: 14px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        /* === FOOTER === */
        .site-footer {
            padding: 22px 0 36px;
        }
        .footer-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
            padding: 22px 26px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        .footer-card p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
            max-width: 540px;
        }
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        /* === RESPONSIVE === */
        @media (min-width: 1081px) {
            .mobile-menu-toggle, .mobile-nav { display: none !important; }
        }
        @media (max-width: 1080px) {
            .hub-nav:not(.hub-nav-mobile), .header-actions:not(.header-actions-mobile) { display: none; }
            .mobile-menu-toggle { display: flex; align-items: center; }
            .site-header-inner { min-height: 64px; }
        }
        @media (max-width: 760px) {
            .footer-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 22px;
            }
            .site-topbar-inner { gap: 8px; min-height: 40px; }
            .site-topbar .btn-label,
            .site-topbar .topbar-link-label { display: none; }
            .site-topbar .btn {
                width: 36px;
                padding: 0;
                border-radius: 50%;
            }
            .site-topbar .topbar-link {
                width: 36px;
                height: 36px;
                padding: 0;
                justify-content: center;
                border-radius: 50%;
                background: rgba(255,255,255,0.08);
            }
        }
        @media (max-width: 480px) {
            .brand-text { display: none; }
        }

        /* === NEWSLETTER MODAL === */
        .newsletter-modal {
            position: fixed;
            inset: 0;
            z-index: 60;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .newsletter-modal-overlay {
            position: absolute;
            inset: 0;
            background: rgba(22,48,43,0.55);
            backdrop-filter: blur(2px);
        }
        .newsletter-modal-card {
            position: relative;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: 0 30px 60px rgba(22,48,43,0.20);
            padding: 28px;
            width: 100%;
            max-width: 480px;
        }
        .newsletter-modal-close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: 0;
            cursor: pointer;
            padding: 6px;
            color: var(--muted);
            border-radius: 8px;
        }
        .newsletter-modal-close:hover { background: rgba(22,48,43,0.06); }
        .newsletter-modal-title {
            margin: 0 0 8px;
            font-size: 22px;
        }
        .newsletter-modal-lede {
            margin: 0 0 20px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
        }
        .newsletter-form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .newsletter-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .newsletter-form-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 13px;
            color: var(--text);
        }
        .newsletter-form-field span em {
            font-style: normal;
            color: var(--muted);
            font-weight: 400;
        }
        .newsletter-form-field input[type=text],
        .newsletter-form-field input[type=email] {
            height: 44px;
            padding: 0 12px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--surface-soft);
            font: inherit;
            color: var(--text);
            transition: border-color 0.15s ease;
        }
        .newsletter-form-field input:focus {
            outline: none;
            border-color: var(--blue);
        }
        .newsletter-form-checkbox {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            font-size: 13px;
            color: var(--text);
            line-height: 1.4;
            cursor: pointer;
        }
        .newsletter-form-checkbox input {
            margin-top: 3px;
            flex-shrink: 0;
        }
        .newsletter-form-error {
            color: #b3261e;
            font-size: 12px;
        }
        .newsletter-form-error-global {
            margin: 0;
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(179,38,30,0.08);
            border: 1px solid rgba(179,38,30,0.20);
        }
        .newsletter-form-submit {
            margin-top: 4px;
            justify-content: center;
        }
        .newsletter-success {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            padding: 20px 0;
            text-align: center;
        }
        .newsletter-success p { margin: 0; font-weight: 700; color: var(--text); }
        .newsletter-success-icon {
            color: var(--sage);
            width: 44px;
            height: 44px;
        }
        @media (max-width: 480px) {
            .newsletter-form-row { grid-template-columns: 1fr; }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('partials.email-verification-notice')
    @include('partials.hub.header')

    <main>
        @yield('content')
    </main>

    @include('partials.hub.newsletter-modal')

    @include('partials.hub.footer')

    <script>lucide.createIcons();</script>
    @stack('scripts')
</body>
</html>
