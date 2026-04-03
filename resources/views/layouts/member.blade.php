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

            --bg: #F4F1ED;
            --surface: #FFFFFF;
            --surface-soft: #FAF8F5;
            --surface-sage: #EEF6F1;
            --surface-blue: #EEF4F8;
            --text: #1C2B27;
            --muted: #68756F;
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
            background: rgba(244,241,237,0.84);
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

        /* Welcome section */
        .welcome {
            display: grid;
            grid-template-columns: 1.35fr 0.95fr;
            gap: 18px;
            align-items: stretch;
        }
        .welcome-main {
            padding: 30px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 28px;
            box-shadow: var(--shadow);
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border-radius: 999px;
            background: rgba(133,183,157,0.16);
            color: #2f694e;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 14px;
        }
        .welcome-main h1 {
            margin: 0;
            font-size: clamp(30px, 4vw, 46px);
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
        .welcome-side {
            display: grid;
            gap: 14px;
        }
        .mini-card {
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 132px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
        }
        .mini-card.blue { background: var(--surface-blue); }
        .mini-card.sage { background: var(--surface-sage); }
        .mini-card strong {
            display: block;
            font-size: 24px;
            line-height: 1.1;
            letter-spacing: -0.03em;
        }
        .mini-card p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.55;
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
                background: rgba(244,241,237,0.84);
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
            .contrib-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
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
        $initials = strtoupper(substr($authMember?->first_name ?? $authUser?->name ?? 'U', 0, 1) . substr($authMember?->last_name ?? '', 0, 1));
        $department = $authMember?->postal_code ? substr($authMember->postal_code, 0, 2) : null;
        $authMemberGroups = $authMember?->workGroups()->active()->get() ?? collect();
    @endphp

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

            {{-- User card --}}
            <div class="user-card">
                <div class="avatar">
                    @if($authMember?->photo_path)
                        <img src="{{ Storage::url($authMember->photo_path) }}" alt="">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                <div class="user-details">
                    <strong>{{ $authMember?->full_name ?? $authUser->name }}</strong>
                    <span>
                        @if($department)Dept. {{ $department }}@endif
                    </span>
                    @if($authMember?->isCurrentMember())
                        <div class="user-badge">Adhérent actif</div>
                    @endif
                </div>
            </div>

            {{-- Member's GT --}}
            @if($authMemberGroups->count() > 0)
            <div class="sidebar-gt-list">
                <div class="nav-title">Mes GT</div>
                @foreach($authMemberGroups as $gt)
                <div style="display:flex; align-items:center; gap:8px; padding:4px 12px;">
                    <div style="width:8px; height:8px; border-radius:50%; background:{{ $gt->color }}; flex-shrink:0;"></div>
                    <span class="nav-label" style="color:rgba(255,255,255,0.6); font-size:13px;">{{ $gt->name }}</span>
                </div>
                @endforeach
            </div>
            @endif

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
                <a href="{{ route('member.contributions') }}" class="nav-item {{ request()->routeIs('member.contributions*') || request()->routeIs('member.work-groups*') ? 'active' : '' }}">
                    <i data-lucide="folder-open" class="icon"></i>
                    <span class="nav-label">Mes contributions</span>
                </a>
                <a href="{{ route('member.documents') }}" class="nav-item {{ request()->routeIs('member.documents*') ? 'active' : '' }}">
                    <i data-lucide="file-text" class="icon"></i>
                    <span class="nav-label">Mes documents</span>
                </a>
                <a href="{{ route('member.community') }}" class="nav-item {{ request()->routeIs('member.community*') || request()->routeIs('member.map*') || request()->routeIs('member.chat*') ? 'active' : '' }}">
                    <i data-lucide="users" class="icon"></i>
                    <span class="nav-label">Groupes et réseau</span>
                </a>
            </nav>

            {{-- Navigation — Ressources --}}
            <nav class="nav-group">
                <div class="nav-title">Ressources</div>
                <a href="{{ route('member.journal') }}" class="nav-item {{ request()->routeIs('member.journal*') ? 'active' : '' }}">
                    <i data-lucide="book-open" class="icon"></i>
                    <span class="nav-label">La revue</span>
                </a>
                <a href="{{ route('member.lepis') }}" class="nav-item {{ request()->routeIs('member.lepis*') ? 'active' : '' }}">
                    <i data-lucide="newspaper" class="icon"></i>
                    <span class="nav-label">Lepis</span>
                </a>
            </nav>

            {{-- Footer: return + logout --}}
            <div class="sidebar-footer">
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
