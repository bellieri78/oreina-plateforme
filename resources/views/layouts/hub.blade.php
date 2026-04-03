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
        .hub-nav a:hover, .hub-nav a.active { color: var(--text); }
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
        }
        @media (max-width: 480px) {
            .brand-text { display: none; }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('partials.hub.header')

    <main>
        @yield('content')
    </main>

    @include('partials.hub.footer')

    <script>lucide.createIcons();</script>
    @stack('scripts')
</body>
</html>
