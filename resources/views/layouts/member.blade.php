<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Mon espace') - OREINA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* === DESIGN SYSTEM V4 — CSS CUSTOM PROPERTIES === */
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
            --surface-sage: #EEF6F1;
            --surface-blue: #EEF4F8;
            --text: #1C2B27;
            --muted: #67746F;
            --border: rgba(22,48,43,0.10);
            --shadow: 0 14px 32px rgba(22,48,43,0.08);
            --radius-xl: 24px;
            --radius-lg: 18px;
            --radius-md: 14px;
            --container: 1440px;
            --sidebar-width: 288px;
            --topbar-height: 76px;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: var(--bg);
            margin: 0;
            padding: 0;
            font-size: 15px;
            line-height: 1.6;
        }

        /* Override Tailwind v4 heading resets */
        h1, h2, h3, h4 {
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -0.04em;
            color: var(--text);
        }
        h1 { font-size: clamp(30px, 4vw, 46px); letter-spacing: -0.05em; line-height: 0.98; }
        h2 { font-size: 26px; }
        h3 { font-size: 20px; }
        h4 { font-size: 16px; }

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

        .icon {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 18px;
        }
        .icon svg {
            width: 18px;
            height: 18px;
            stroke-width: 2;
        }
        .icon-blue { color: var(--blue); }
        .icon-sage { color: var(--forest); }
        .icon-gold { color: #8b6c05; }
        .icon-coral { color: var(--coral); }
        .icon-white { color: white; }

        /* === APP GRID — 2 COLUMN (sidebar + main) === */
        .app {
            min-height: 100vh;
            display: grid;
            grid-template-columns: var(--sidebar-width) 1fr;
        }

        /* === SIDEBAR === */
        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 18px;
            background: var(--forest);
            color: white;
            display: flex;
            flex-direction: column;
            gap: 18px;
            border-right: 1px solid rgba(255,255,255,0.08);
            overflow-y: auto;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
        }
        .brand-mark {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.10);
            font-weight: 900;
            color: white;
            flex-shrink: 0;
            overflow: hidden;
        }
        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .brand-text strong {
            display: block;
            font-size: 18px;
            letter-spacing: -0.03em;
        }
        .brand-text span {
            display: block;
            margin-top: 2px;
            font-size: 12px;
            color: rgba(255,255,255,0.66);
        }

        /* User card */
        .user-card {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .avatar {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: var(--sage);
            color: var(--forest);
            font-weight: 800;
            font-size: 20px;
            box-shadow: inset 0 0 0 3px rgba(255,255,255,0.18);
            flex: 0 0 54px;
            overflow: hidden;
        }
        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .user-card strong {
            display: block;
            font-size: 15px;
        }
        .user-card .user-details span {
            display: block;
            margin-top: 4px;
            color: rgba(255,255,255,0.66);
            font-size: 13px;
            line-height: 1.4;
        }
        .user-card .user-badge {
            display: inline-block;
            margin-top: 6px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(133,183,157,0.25);
            color: var(--sage);
            padding: 2px 10px;
            border-radius: 999px;
        }

        /* GT list in sidebar */
        .sidebar-gt-list {
            padding: 0 4px;
        }

        /* Navigation */
        .nav-group {
            display: grid;
            gap: 4px;
        }
        .nav-title {
            padding: 0 12px 4px;
            color: rgba(255,255,255,0.44);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 16px;
            color: rgba(255,255,255,0.84);
            border: 1px solid transparent;
            transition: 0.2s ease;
            font-weight: 600;
            font-size: 15px;
        }
        .nav-item:hover,
        .nav-item.active {
            background: rgba(133,183,157,0.16);
            border-color: rgba(133,183,157,0.18);
            color: white;
        }
        .nav-item .icon {
            color: rgba(255,255,255,0.84);
        }
        .nav-item.disabled {
            opacity: 0.35;
            pointer-events: none;
        }
        .nav-item.nav-item-locked {
            color: rgba(255,255,255,0.55);
            border: 1px dashed rgba(237,196,66,0.35);
        }
        .nav-item.nav-item-locked:hover {
            background: rgba(237,196,66,0.08);
            border-color: rgba(237,196,66,0.50);
            color: rgba(255,255,255,0.75);
        }
        .nav-item.nav-item-locked .icon {
            color: rgba(255,255,255,0.55);
        }
        .nav-badge-lock {
            margin-left: auto;
            background: var(--gold);
            color: var(--forest);
            font-size: 10px;
            font-weight: 800;
            padding: 3px 7px;
            border-radius: 999px;
            letter-spacing: 0.04em;
        }
        .nav-item.nav-item-danger {
            color: rgba(239,68,68,0.8);
        }
        .nav-item.nav-item-danger:hover {
            background: rgba(239,68,68,0.12);
            border-color: rgba(239,68,68,0.18);
            color: #fca5a5;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: grid;
            gap: 4px;
        }

        /* === MAIN AREA === */
        .main {
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        /* === TOPBAR (glassmorphism) === */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            min-height: var(--topbar-height);
            background: rgba(245,242,238,0.84);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(22,48,43,0.06);
        }
        .topbar-inner {
            min-height: var(--topbar-height);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .topbar-title strong {
            display: block;
            font-size: 20px;
            letter-spacing: -0.03em;
        }
        .topbar-title span {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 13px;
        }
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* === CONTENT AREA === */
        .content {
            padding: 24px;
            display: grid;
            gap: 22px;
            flex: 1;
        }

        /* === CARDS (Design System V4) === */
        .member-card,
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
        }
        .member-card:hover {
            box-shadow: 0 20px 48px rgba(22,48,43,0.12);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        .member-card-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--forest);
        }
        .member-card-header .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        /* Stats */
        .member-stat {
            text-align: center;
            padding: 20px;
            border-radius: 20px;
            background: var(--surface-soft);
            border: 1px solid rgba(22,48,43,0.06);
            transition: all 0.3s ease;
        }
        .member-stat:hover {
            box-shadow: 0 10px 30px rgba(22,48,43,0.08);
            transform: translateY(-2px);
        }
        .member-stat-value {
            font-size: 34px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.05em;
            color: var(--text);
        }
        .member-stat-label {
            font-size: 14px;
            color: var(--muted);
            margin-top: 8px;
            line-height: 1.5;
        }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }
        .status-badge.active {
            background: rgba(133,183,157,0.18);
            color: #2f694e;
        }
        .status-badge.expired {
            background: rgba(239,68,68,0.1);
            color: #dc2626;
        }

        /* === BUTTONS (Design System V4) === */
        .btn {
            height: 44px;
            padding: 0 16px;
            border-radius: 14px;
            border: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s ease;
            white-space: nowrap;
            text-decoration: none;
        }
        .btn:hover { transform: translateY(-1px); }

        .btn-primary,
        .btn-member {
            background: var(--gold);
            color: var(--forest);
            box-shadow: 0 12px 24px rgba(237,196,66,0.18);
        }
        .btn-primary:hover,
        .btn-member:hover {
            box-shadow: 0 16px 32px rgba(237,196,66,0.28);
            transform: translateY(-1px);
        }

        .btn-secondary,
        .btn-member-outline {
            background: rgba(53,107,138,0.08);
            color: var(--blue);
            border: 1px solid rgba(53,107,138,0.14);
        }
        .btn-secondary:hover,
        .btn-member-outline:hover {
            background: rgba(53,107,138,0.14);
            transform: translateY(-1px);
        }

        /* GT placeholder cards */
        .gt-card-placeholder {
            padding: 1.25rem;
            border-radius: var(--radius-xl);
            background: linear-gradient(135deg, rgba(219, 203, 199, 0.15), rgba(133, 183, 157, 0.08));
            border: 2px dashed rgba(22,48,43,0.12);
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--muted);
            text-align: center;
        }
        .gt-card-placeholder .label {
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        .gt-card-placeholder .sub {
            font-size: 0.625rem;
        }

        /* Interactive links */
        .member-link {
            color: var(--blue);
            font-weight: 800;
            text-decoration: none;
            transition: all 0.2s;
        }
        .member-link:hover {
            color: var(--forest);
            text-decoration: underline;
        }

        /* Panels */
        .panel {
            padding: 24px;
        }
        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 14px;
            margin-bottom: 18px;
        }
        .panel-head > div:first-child {
            flex: 1;
            min-width: 0;
        }
        .panel-head h2 {
            margin: 0;
            font-size: 26px;
            line-height: 1.05;
            letter-spacing: -0.04em;
        }
        .panel-head p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
            max-width: 620px;
        }

        .text-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--blue);
            font-size: 14px;
            font-weight: 800;
            white-space: nowrap;
        }

        /* === DASHBOARD COMPONENTS (Design System V4) === */

        /* Welcome section — refonte v5 (texte + photo papillon) */
        .welcome {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 18px;
            align-items: stretch;
        }
        .welcome-main {
            padding: 30px;
            border-radius: 28px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .welcome-main.is-member {
            background: var(--surface-sage);
            border: 1px solid rgba(133,183,157,0.30);
        }
        .welcome-main.is-visitor {
            background: #FBF6DF;
            border: 1px solid rgba(237,196,66,0.30);
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 14px;
            width: fit-content;
        }
        .eyebrow.eyebrow-member {
            background: rgba(133,183,157,0.20);
            color: #2f694e;
        }
        .eyebrow.eyebrow-visitor {
            background: rgba(237,196,66,0.20);
            color: #8b6c05;
        }
        .welcome-main h1 {
            margin: 0;
            font-size: clamp(30px, 4vw, 46px);
            font-weight: 700;
            line-height: 0.98;
            letter-spacing: -0.05em;
            max-width: 720px;
        }
        .welcome-main p {
            margin: 14px 0 0;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
            max-width: 760px;
        }
        .quick-actions {
            margin-top: 22px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .welcome-photo {
            border-radius: 20px;
            overflow: hidden;
            background: var(--surface-soft);
            min-height: 280px;
        }
        .welcome-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* KPI bar — bandeau stats sous le hero */
        .kpi-bar {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 18px;
            align-items: end;
            padding: 20px 24px;
            background: var(--surface-soft);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
        }
        .kpi-bar-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }
        .kpi-bar .stat {
            padding: 0;
            background: transparent;
            border: 0;
        }

        /* ═══ TOPBAR enrichie (refonte mockup mature) ═══ */
        .topbar-search {
            flex: 1;
            max-width: 480px;
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            background: rgba(255,255,255,0.7);
            border: 1px solid var(--border);
            border-radius: 999px;
        }
        .topbar-search input {
            flex: 1;
            border: 0;
            background: transparent;
            outline: none;
            font: inherit;
            font-size: 14px;
            color: var(--text);
        }
        .topbar-search kbd {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            background: rgba(22,48,43,0.06);
            color: var(--muted);
            font-family: inherit;
        }
        .topbar-tools {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .topbar-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: transparent;
            color: var(--muted);
            border: 1px solid transparent;
            cursor: pointer;
            transition: 0.2s ease;
        }
        .topbar-icon:hover {
            background: var(--surface-soft);
            color: var(--text);
        }
        .topbar-popover {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 240px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 14px;
            font-size: 13px;
            z-index: 20;
        }

        /* ═══ SIDEBAR — Profil complété ═══ */
        .sidebar-progress {
            padding: 0 4px;
        }
        .sidebar-progress-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: rgba(255,255,255,0.44);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .sidebar-progress-bar {
            height: 6px;
            background: rgba(255,255,255,0.10);
            border-radius: 999px;
            overflow: hidden;
        }
        .sidebar-progress-fill {
            height: 100%;
            background: var(--gold);
            border-radius: 999px;
            transition: width 0.4s ease;
        }

        /* ═══ SIDEBAR — Mes rôles ═══ */
        .sidebar-roles {
            padding: 0 4px;
            display: grid;
            gap: 8px;
        }
        .role-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 12px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.06);
        }
        .role-chip-avatar {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            color: white;
            flex-shrink: 0;
        }
        .role-chip-avatar svg {
            width: 14px;
            height: 14px;
        }
        .role-chip-body strong {
            display: block;
            color: white;
            font-size: 13px;
            line-height: 1.2;
        }
        .role-chip-body span {
            display: block;
            margin-top: 2px;
            color: rgba(255,255,255,0.55);
            font-size: 11px;
        }

        /* ═══ SIDEBAR CTA Artemisiae (footer) ═══ */
        .sidebar-cta {
            margin: 4px 4px 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 14px;
            border-radius: 14px;
            background: var(--gold);
            color: var(--forest);
            font-weight: 800;
            font-size: 14px;
            transition: 0.2s ease;
        }
        .sidebar-cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(237,196,66,0.30);
        }

        /* ═══ Membre depuis (user-card) ═══ */
        .user-card .member-since {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            color: rgba(255,255,255,0.55);
        }

        /* ═══ KPI bar 5 stats (extension) ═══ */
        .kpi-bar-stats {
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        }
        @media (max-width: 1100px) {
            .kpi-bar-stats {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }
        @media (max-width: 760px) {
            .kpi-bar-stats {
                grid-template-columns: 1fr !important;
            }
        }
        .kpi-item {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .kpi-item-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }
        .kpi-item-icon svg {
            width: 20px;
            height: 20px;
        }
        .kpi-item strong {
            display: block;
            font-size: 24px;
            line-height: 1;
            letter-spacing: -0.03em;
        }
        .kpi-item span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            margin-top: 4px;
            line-height: 1.4;
        }

        /* ═══ Hero carousel ═══ */
        .hero-carousel {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            background: var(--surface-soft);
            min-height: 280px;
        }
        .hero-carousel-slide {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: flex-end;
            transition: opacity 0.5s ease;
        }
        .hero-carousel-slide img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hero-carousel-caption {
            position: relative;
            padding: 20px;
            color: white;
            background: linear-gradient(transparent, rgba(0,0,0,0.55));
            width: 100%;
        }
        .hero-carousel-caption .eyebrow {
            background: rgba(255,255,255,0.18);
            color: white;
            margin-bottom: 8px;
        }
        .hero-carousel-caption strong {
            display: block;
            font-size: 22px;
            font-style: italic;
            font-weight: 600;
        }
        .hero-carousel-caption small {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            opacity: 0.8;
        }
        .hero-carousel-dots {
            position: absolute;
            bottom: 14px;
            right: 14px;
            display: flex;
            gap: 6px;
            z-index: 2;
        }
        .hero-carousel-dot {
            width: 24px;
            height: 4px;
            border-radius: 2px;
            background: rgba(255,255,255,0.4);
            cursor: pointer;
            transition: background 0.2s;
        }
        .hero-carousel-dot.active {
            background: var(--gold);
        }

        /* ═══ Mes groupes & projets — carousel cards ═══ */
        .groups-carousel {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: 280px;
            gap: 16px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding-bottom: 8px;
        }
        .group-card {
            scroll-snap-align: start;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .group-card-cover {
            height: 120px;
            display: grid;
            place-items: center;
            color: white;
            position: relative;
        }
        .group-card-cover svg {
            width: 40px;
            height: 40px;
        }
        .group-card-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            position: absolute;
            bottom: -24px;
            left: 16px;
            background: white;
            border: 3px solid white;
            display: grid;
            place-items: center;
        }
        .group-card-avatar svg {
            width: 22px;
            height: 22px;
        }
        .group-card-body {
            padding: 36px 16px 16px;
        }
        .group-card-body h3 {
            margin: 0;
            font-size: 17px;
            line-height: 1.15;
        }
        .group-card-body .subtitle {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            color: var(--muted);
        }
        .group-card-chips {
            margin-top: 12px;
            display: flex;
            gap: 12px;
            font-size: 12px;
            color: var(--muted);
        }
        .group-card-chips span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .group-card-chips svg {
            width: 14px;
            height: 14px;
        }

        /* ═══ Actualités du réseau ═══ */
        .news-feed {
            display: grid;
            gap: 8px;
        }
        .news-feed-item {
            display: grid;
            grid-template-columns: 64px 1fr;
            gap: 14px;
            padding: 10px;
            border-radius: 14px;
            transition: background 0.2s;
        }
        .news-feed-item:hover {
            background: var(--surface-soft);
        }
        .news-feed-thumb {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            object-fit: cover;
            background: var(--surface-soft);
        }
        .news-feed-type {
            display: inline-block;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 6px;
            margin-bottom: 4px;
        }
        .news-feed-type.blue { background: rgba(53,107,138,0.10); color: var(--blue); }
        .news-feed-type.sage { background: rgba(133,183,157,0.18); color: #2f694e; }
        .news-feed-type.gold { background: rgba(237,196,66,0.20); color: #8b6c05; }
        .news-feed-type.coral { background: rgba(239,122,92,0.16); color: var(--coral); }
        .news-feed-item strong {
            display: block;
            font-size: 14px;
            line-height: 1.3;
        }
        .news-feed-item p {
            margin: 4px 0 0;
            font-size: 12px;
            color: var(--muted);
        }

        /* ═══ Ressources récentes ═══ */
        .resource-featured {
            display: grid;
            grid-template-columns: 110px 1fr;
            gap: 14px;
            padding: 14px;
            background: var(--surface-soft);
            border-radius: 18px;
            margin-bottom: 14px;
        }
        .resource-featured img {
            width: 110px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }
        .resource-list {
            display: grid;
            gap: 8px;
        }
        .resource-item {
            display: grid;
            grid-template-columns: 40px 1fr;
            gap: 12px;
            padding: 10px;
            border-radius: 12px;
            transition: background 0.2s;
        }
        .resource-item:hover {
            background: var(--surface-soft);
        }
        .resource-item-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: grid;
            place-items: center;
        }
        .resource-item strong {
            display: block;
            font-size: 14px;
        }
        .resource-item small {
            color: var(--muted);
            font-size: 12px;
        }

        /* ═══ Mes contributions — barres ═══ */
        .contrib-row {
            margin-bottom: 14px;
        }
        .contrib-row-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        .contrib-row-head .label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        .contrib-row-head .label svg {
            width: 16px;
            height: 16px;
            color: var(--blue);
        }
        .contrib-row-head .value {
            font-weight: 800;
            font-size: 16px;
        }
        .contrib-bar {
            height: 4px;
            background: rgba(22,48,43,0.06);
            border-radius: 2px;
            overflow: hidden;
        }
        .contrib-bar-fill {
            height: 100%;
            background: var(--sage);
            border-radius: 2px;
        }

        /* ═══ Réseau adhérents — carte ═══ */
        .reseau-map-wrap {
            position: relative;
            display: grid;
            place-items: center;
        }
        .reseau-map-svg {
            width: 240px;
            height: 240px;
        }
        .reseau-map-cluster {
            cursor: default;
        }
        .reseau-map-cluster circle {
            fill: rgba(133,183,157,0.85);
            stroke: white;
            stroke-width: 1.5;
        }
        .reseau-map-cluster text {
            fill: var(--forest);
            font-size: 11px;
            font-weight: 800;
            text-anchor: middle;
            dominant-baseline: central;
        }
        .reseau-avatars {
            display: flex;
            margin-top: 12px;
            justify-content: center;
        }
        .reseau-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--sage);
            color: var(--forest);
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 13px;
            border: 2px solid white;
            margin-left: -8px;
            overflow: hidden;
        }
        .reseau-avatar:first-child {
            margin-left: 0;
        }
        .reseau-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .reseau-search {
            margin-top: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: var(--surface-soft);
            border-radius: 999px;
            border: 1px solid var(--border);
        }
        .reseau-search input {
            flex: 1;
            border: 0;
            background: transparent;
            outline: none;
            font: inherit;
            font-size: 13px;
        }

        /* ═══ Agenda — date blocks ═══ */
        .agenda-list {
            display: grid;
            gap: 10px;
        }
        .agenda-item {
            display: grid;
            grid-template-columns: 56px 1fr;
            gap: 12px;
            align-items: center;
        }
        .agenda-date {
            width: 56px;
            text-align: center;
            background: var(--surface-sage);
            border-radius: 12px;
            padding: 8px 0;
        }
        .agenda-date small {
            display: block;
            font-size: 10px;
            font-weight: 800;
            color: #8b6c05;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .agenda-date strong {
            display: block;
            font-size: 22px;
            line-height: 1;
            margin-top: 2px;
            color: var(--text);
        }
        .agenda-item-body strong {
            display: block;
            font-size: 14px;
            line-height: 1.25;
        }
        .agenda-item-body small {
            color: var(--muted);
            font-size: 12px;
            display: block;
            margin-top: 4px;
        }

        /* ═══ Suggestions pour vous ═══ */
        .suggestions-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-top: 22px;
        }
        @media (max-width: 1240px) {
            .suggestions-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 760px) {
            .suggestions-grid { grid-template-columns: 1fr; }
        }
        .suggestion-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
        }
        .suggestion-card .eyebrow {
            font-size: 10px;
            color: var(--muted);
            background: transparent;
            padding: 0;
            margin: 0;
            letter-spacing: 0.1em;
        }
        .suggestion-card strong {
            display: block;
            font-size: 15px;
            line-height: 1.2;
        }
        .suggestion-card p {
            margin: 0;
            font-size: 12px;
            color: var(--muted);
        }
        .suggestion-card .btn {
            align-self: flex-end;
            height: 32px;
            padding: 0 12px;
            font-size: 12px;
            margin-top: 8px;
        }

        /* ═══ Layout grille à 3 colonnes (contributions / map / agenda) ═══ */
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1.2fr 1fr;
            gap: 18px;
            align-items: start;
        }
        @media (max-width: 1240px) {
            .grid-3 { grid-template-columns: 1fr; }
        }

        /* Grid & Stack */
        .grid {
            display: grid;
            grid-template-columns: 1.15fr 0.95fr;
            gap: 18px;
            align-items: start;
        }
        .stack {
            display: grid;
            gap: 18px;
        }

        /* Contributions grid */
        .contrib-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }
        .stat {
            padding: 20px;
            border-radius: 20px;
            background: var(--surface-soft);
            border: 1px solid rgba(22,48,43,0.06);
        }
        .stat strong {
            display: block;
            font-size: 34px;
            line-height: 1;
            letter-spacing: -0.05em;
        }
        .stat span {
            display: block;
            margin-top: 8px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
        }

        /* Activity list */
        .activity-list,
        .todo-list,
        .news-list,
        .table-list {
            display: grid;
            gap: 12px;
        }
        .activity-item,
        .todo-item,
        .news-item,
        .table-row {
            padding: 16px;
            border-radius: 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
        }
        .activity-item {
            display: grid;
            grid-template-columns: 44px 1fr auto;
            gap: 14px;
            align-items: start;
            background: var(--surface-soft);
        }
        .bullet {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: rgba(133,183,157,0.16);
            flex: 0 0 44px;
        }
        .bullet.blue { background: rgba(53,107,138,0.10); }
        .bullet.gold { background: rgba(237,196,66,0.18); }
        .bullet.coral { background: rgba(239,122,92,0.12); }

        .activity-item strong,
        .todo-item strong,
        .news-item strong,
        .table-row strong {
            display: block;
            font-size: 15px;
            line-height: 1.35;
            letter-spacing: -0.01em;
        }
        .activity-item p,
        .todo-item p,
        .news-item p,
        .table-row p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.55;
        }
        .time, .meta {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        /* Todo items */
        .todo-item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: start;
            background: var(--surface-soft);
        }
        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }
        .status.gold { background: rgba(237,196,66,0.20); color: #8b6c05; }
        .status.blue { background: rgba(53,107,138,0.10); color: var(--blue); }
        .status.sage { background: rgba(133,183,157,0.18); color: #2f694e; }
        .status.coral { background: rgba(234,88,12,0.12); color: #c2410c; }
        .status.approval {
            background: #7c3aed;
            color: #fff;
            box-shadow: 0 0 0 0 rgba(124,58,237,0.6);
            animation: approvalPulse 1.8s ease-in-out infinite;
        }
        @keyframes approvalPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(124,58,237,0.55); }
            50%      { box-shadow: 0 0 0 8px rgba(124,58,237,0); }
        }

        /* News items */
        .news-item {
            background: var(--surface-soft);
        }
        .news-item:nth-child(1) { background: #FBF6DF; }
        .news-item:nth-child(2) { background: #EEF4F8; }
        .news-item:nth-child(3) { background: #EEF6F1; }

        /* Observations table */
        .obs-table {
            display: grid;
            gap: 10px;
        }
        .table-row {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 14px;
            align-items: center;
            background: var(--surface-soft);
        }
        .table-badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        /* Badges */
        .badge {
            padding: 7px 10px;
            border-radius: 999px;
            border: 1px solid rgba(22,48,43,0.06);
            background: white;
            font-size: 12px;
            font-weight: 800;
            color: var(--muted);
            white-space: nowrap;
        }
        .badge.blue { color: var(--blue); background: rgba(53,107,138,0.08); }
        .badge.sage { color: #2f694e; background: rgba(133,183,157,0.16); }
        .badge.gold { color: #8b6c05; background: rgba(237,196,66,0.18); }
        .badge.coral { color: var(--coral); background: rgba(239,122,92,0.12); }

        /* Flash messages */
        .flash-success {
            padding: 14px 18px;
            background: var(--surface-sage);
            border: 1px solid rgba(133,183,157,0.25);
            color: #2f694e;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
        }
        .flash-error {
            padding: 14px 18px;
            background: rgba(239,68,68,0.06);
            border: 1px solid rgba(239,68,68,0.15);
            color: #dc2626;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        /* === MOBILE OVERLAY === */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }
        .sidebar-overlay.open {
            display: block;
        }

        /* Mobile header (hidden on desktop) */
        .member-mobile-header {
            display: none;
        }

        /* === RESPONSIVE === */

        /* Tablet: icon-only sidebar */
        @media (min-width: 768px) and (max-width: 1024px) {
            :root {
                --sidebar-width: 72px;
            }
            .sidebar {
                padding: 12px;
                align-items: center;
            }
            .sidebar .brand-text,
            .sidebar .user-details,
            .sidebar .sidebar-gt-list,
            .sidebar .nav-title,
            .sidebar .nav-label,
            .sidebar .nav-badge-lock,
            .sidebar .sidebar-footer .nav-label {
                display: none;
            }
            .brand-mark {
                width: 40px;
                height: 40px;
                border-radius: 12px;
            }
            .brand { padding: 4px; justify-content: center; }
            .user-card {
                padding: 10px;
                justify-content: center;
            }
            .avatar {
                width: 40px;
                height: 40px;
                font-size: 16px;
                flex: 0 0 40px;
            }
            .nav-item {
                padding: 12px;
                justify-content: center;
            }
        }

        /* Mobile: hamburger sidebar */
        @media (max-width: 767px) {
            .app {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: fixed;
                left: -300px;
                top: 0;
                width: 288px;
                z-index: 50;
                transition: left 0.3s ease;
                height: 100vh;
            }
            .sidebar.open {
                left: 0;
            }
            .member-mobile-header {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 16px;
                background: rgba(245,242,238,0.84);
                backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(22,48,43,0.06);
            }
            .member-mobile-header button {
                background: none;
                border: none;
                padding: 6px;
                border-radius: 10px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .member-mobile-header button:hover {
                background: rgba(22,48,43,0.06);
            }
            .topbar {
                display: none;
            }
            .content {
                padding: 16px;
            }
        }

        /* Dashboard responsive */
        @media (max-width: 1240px) {
            .welcome,
            .grid {
                grid-template-columns: 1fr;
            }
            .welcome-photo {
                min-height: 240px;
            }
            .contrib-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .welcome-photo { display: none; }
            .kpi-bar { grid-template-columns: 1fr; }
            .kpi-bar-stats { grid-template-columns: 1fr; gap: 12px; }
            .topbar-inner {
                padding: 14px 16px;
                align-items: flex-start;
                flex-direction: column;
            }
            .topbar-actions {
                width: 100%;
            }
            .contrib-grid {
                grid-template-columns: 1fr;
            }
            .activity-item,
            .table-row {
                grid-template-columns: 44px 1fr;
            }
            .time,
            .table-badges,
            .meta {
                grid-column: 2;
                justify-self: start;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @php
        $authUser = auth()->user();
        $authMember = $authUser ? \App\Models\Member::where('user_id', $authUser->id)->first() : null;
        $isAuthCurrentMember = $authMember?->isCurrentMember() ?? false;
        $initials = strtoupper(substr($authMember?->first_name ?? $authUser?->name ?? 'U', 0, 1) . substr($authMember?->last_name ?? '', 0, 1));
        $department = $authMember?->postal_code ? substr($authMember->postal_code, 0, 2) : null;
        $authMemberGroups = $authMember?->workGroups()->active()->get() ?? collect();
        $authProfileCompletion = $authMember?->profileCompletionPercent() ?? 0;
        $authMemberSince = $authMember?->created_at?->year;
    @endphp

    @include('partials.email-verification-notice')
    <div class="app">
        {{-- LEFT SIDEBAR --}}
        <aside class="sidebar" id="memberSidebar">
            {{-- Brand --}}
            <a href="{{ route('hub.home') }}" class="brand">
                <div class="brand-mark">
                    <img src="/images/logo.jpg" alt="O" onerror="this.style.display='none'; this.parentNode.textContent='O';">
                </div>
                <div class="brand-text">
                    <strong>OREINA</strong>
                    <span>Espace membre</span>
                </div>
            </a>

            @include('member.partials._sidebar_user_card')

            @include('member.partials._sidebar_progress')

            @include('member.partials._sidebar_roles')

            {{-- Navigation — Mon espace --}}
            <nav class="nav-group">
                <div class="nav-title">Mon espace</div>
                <a href="{{ route('member.dashboard') }}" class="nav-item {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard" class="icon"></i>
                    <span class="nav-label">Tableau de bord</span>
                </a>
                <a href="{{ route('member.profile') }}" class="nav-item {{ request()->routeIs('member.profile*') && !request()->routeIs('member.profile.preferences*') ? 'active' : '' }}">
                    <i data-lucide="user-round" class="icon"></i>
                    <span class="nav-label">Mon profil</span>
                </a>
                <a href="{{ route('member.membership') }}" class="nav-item {{ request()->routeIs('member.membership*') ? 'active' : '' }}">
                    <i data-lucide="heart" class="icon"></i>
                    <span class="nav-label">Mon adhésion</span>
                </a>
                <a href="{{ route('member.profile.preferences') }}" class="nav-item {{ request()->routeIs('member.profile.preferences*') ? 'active' : '' }}">
                    <i data-lucide="settings" class="icon"></i>
                    <span class="nav-label">Préférences</span>
                </a>
            </nav>

            {{-- Navigation — Contribuer --}}
            <nav class="nav-group">
                <div class="nav-title">Contribuer</div>
                <a href="{{ route('journal.submissions.create') }}" class="nav-item {{ request()->routeIs('journal.submissions.create') ? 'active' : '' }}">
                    <i data-lucide="file-plus" class="icon"></i>
                    <span class="nav-label">Soumettre un article</span>
                </a>
                <a href="{{ route('journal.submissions.index') }}" class="nav-item {{ request()->routeIs('journal.submissions*') ? 'active' : '' }}">
                    <i data-lucide="file-check" class="icon"></i>
                    <span class="nav-label">Mes soumissions</span>
                </a>

                @if($isAuthCurrentMember)
                <a href="{{ route('member.contributions') }}" class="nav-item {{ request()->routeIs('member.contributions*') ? 'active' : '' }}">
                    <i data-lucide="folder-open" class="icon"></i>
                    <span class="nav-label">Mes contributions</span>
                </a>
                <a href="{{ route('member.documents') }}" class="nav-item {{ request()->routeIs('member.documents*') ? 'active' : '' }}">
                    <i data-lucide="file-text" class="icon"></i>
                    <span class="nav-label">Mes documents</span>
                </a>
                @else
                <a href="{{ route('hub.membership') }}" class="nav-item nav-item-locked">
                    <i data-lucide="folder-open" class="icon"></i>
                    <span class="nav-label">Mes contributions</span>
                    <span class="nav-badge-lock">Adhérent</span>
                </a>
                <a href="{{ route('hub.membership') }}" class="nav-item nav-item-locked">
                    <i data-lucide="file-text" class="icon"></i>
                    <span class="nav-label">Mes documents</span>
                    <span class="nav-badge-lock">Adhérent</span>
                </a>
                @endif
            </nav>

            {{-- Navigation — Réseau --}}
            <nav class="nav-group">
                <div class="nav-title">Réseau</div>
                @if($isAuthCurrentMember)
                <a href="{{ route('member.directory.index') }}" class="nav-item {{ request()->routeIs('member.directory*') ? 'active' : '' }}">
                    <i data-lucide="users-round" class="icon"></i>
                    <span class="nav-label">Annuaire des adhérents</span>
                </a>
                <a href="{{ route('member.work-groups') }}" class="nav-item {{ request()->routeIs('member.work-groups*') ? 'active' : '' }}">
                    <i data-lucide="users" class="icon"></i>
                    <span class="nav-label">Mes groupes</span>
                </a>
                <a href="{{ route('member.chat') }}" class="nav-item {{ request()->routeIs('member.chat*') ? 'active' : '' }}">
                    <i data-lucide="message-circle" class="icon"></i>
                    <span class="nav-label">Chat</span>
                </a>
                @else
                <a href="{{ route('hub.membership') }}" class="nav-item nav-item-locked">
                    <i data-lucide="users-round" class="icon"></i>
                    <span class="nav-label">Annuaire des adhérents</span>
                    <span class="nav-badge-lock">Adhérent</span>
                </a>
                <a href="{{ route('hub.membership') }}" class="nav-item nav-item-locked">
                    <i data-lucide="users" class="icon"></i>
                    <span class="nav-label">Mes groupes</span>
                    <span class="nav-badge-lock">Adhérent</span>
                </a>
                <a href="{{ route('hub.membership') }}" class="nav-item nav-item-locked">
                    <i data-lucide="message-circle" class="icon"></i>
                    <span class="nav-label">Chat</span>
                    <span class="nav-badge-lock">Adhérent</span>
                </a>
                @endif
            </nav>

            {{-- Navigation — Ressources --}}
            <nav class="nav-group">
                <div class="nav-title">Ressources</div>
                <a href="{{ route('journal.articles.index') }}" class="nav-item {{ request()->routeIs('journal.articles*') ? 'active' : '' }}">
                    <i data-lucide="book-open" class="icon"></i>
                    <span class="nav-label">Chersotis</span>
                </a>
                @if($isAuthCurrentMember)
                <a href="{{ route('hub.lepis.bulletins.index') }}" class="nav-item {{ request()->routeIs('member.lepis*') ? 'active' : '' }}">
                    <i data-lucide="newspaper" class="icon"></i>
                    <span class="nav-label">Lepis</span>
                </a>
                @else
                <a href="{{ route('hub.membership') }}" class="nav-item nav-item-locked">
                    <i data-lucide="newspaper" class="icon"></i>
                    <span class="nav-label">Lepis</span>
                    <span class="nav-badge-lock">Adhérent</span>
                </a>
                @endif
                <a href="#" class="nav-item" onclick="event.preventDefault(); alert('Publications bientôt disponibles');">
                    <i data-lucide="book-marked" class="icon"></i>
                    <span class="nav-label">Publications</span>
                </a>
                <a href="#" class="nav-item" onclick="event.preventDefault(); alert('Documents bientôt disponibles');">
                    <i data-lucide="folder" class="icon"></i>
                    <span class="nav-label">Documents</span>
                </a>
                <a href="#" class="nav-item" onclick="event.preventDefault(); alert('Webinaires & replays bientôt disponibles');">
                    <i data-lucide="video" class="icon"></i>
                    <span class="nav-label">Webinaires & replays</span>
                </a>
            </nav>

            {{-- Navigation — Aide --}}
            <nav class="nav-group">
                <div class="nav-title">Aide</div>
                <a href="{{ route('hub.faq') }}" class="nav-item">
                    <i data-lucide="circle-help" class="icon"></i>
                    <span class="nav-label">FAQ</span>
                </a>
                <a href="mailto:contact@oreina.org" class="nav-item">
                    <i data-lucide="mail" class="icon"></i>
                    <span class="nav-label">Nous contacter</span>
                </a>
            </nav>

            {{-- Footer: Artemisiae CTA + return + logout --}}
            <div class="sidebar-footer">
                <a href="#" class="sidebar-cta" onclick="event.preventDefault(); alert('Lien Artemisiae à configurer');">
                    <i data-lucide="external-link" class="icon"></i>
                    <span class="nav-label">Explorer Artemisiae</span>
                </a>
                @if($authUser?->isAdmin() || $authUser?->isEditor())
                <a href="{{ route('admin.dashboard') }}" class="nav-item">
                    <i data-lucide="shield" class="icon"></i>
                    <span class="nav-label">Extranet</span>
                </a>
                @endif
                <a href="{{ route('hub.home') }}" class="nav-item">
                    <i data-lucide="arrow-left" class="icon"></i>
                    <span class="nav-label">Retour au site</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-item nav-item-danger" style="width:100%; background:none; border:none; cursor:pointer; text-align:left;">
                        <i data-lucide="log-out" class="icon"></i>
                        <span class="nav-label">Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- MOBILE OVERLAY --}}
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleMobileSidebar()"></div>

        {{-- MAIN --}}
        <div class="main">
            {{-- Mobile header --}}
            <div class="member-mobile-header">
                <button type="button" onclick="toggleMobileSidebar()">
                    <i data-lucide="menu" style="width:24px;height:24px;color:var(--forest);"></i>
                </button>
                <a href="{{ route('hub.home') }}" style="display:flex; align-items:center; gap:8px;">
                    <img src="/images/logo.jpg" alt="OREINA" style="height:28px; width:auto; border-radius:8px;" onerror="this.style.display='none'">
                    <strong style="color:var(--forest); font-size:15px;">Mon espace</strong>
                </a>
            </div>

            {{-- Topbar --}}
            <div class="topbar">
                <div class="topbar-inner">
                    <div class="topbar-title">
                        <strong>@yield('page-title', 'Tableau de bord')</strong>
                        @hasSection('page-subtitle')
                            <span>@yield('page-subtitle')</span>
                        @endif
                    </div>
                    <div class="topbar-actions">
                        @yield('topbar-actions')
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="content">
                {{-- Flash messages --}}
                @if(session('success'))
                <div class="flash-success">
                    <i data-lucide="check-circle" class="icon"></i>
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="flash-error">
                    <i data-lucide="alert-circle" class="icon"></i>
                    {{ session('error') }}
                </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script>
        function toggleMobileSidebar() {
            document.getElementById('memberSidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        }
    </script>
    <script>lucide.createIcons();</script>

    @stack('scripts')
</body>
</html>
