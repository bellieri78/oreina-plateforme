<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Mon espace') - OREINA</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* === 3-COLUMN MEMBER LAYOUT === */
        .member-layout {
            display: grid;
            grid-template-columns: 240px 1fr 280px;
            min-height: 100vh;
        }

        /* Left sidebar */
        .member-sidebar {
            background: linear-gradient(180deg, #16302B 0%, #1a3a35 100%);
            padding: 1.5rem 1.25rem;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .member-nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            color: rgba(255, 255, 255, 0.65);
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
            text-decoration: none;
        }
        .member-nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .member-nav-item.active {
            background: rgba(133, 183, 157, 0.2);
            color: #85B79D;
        }
        .member-nav-item.disabled {
            opacity: 0.35;
            pointer-events: none;
        }
        .member-nav-item svg {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }

        /* Right sidebar */
        .member-sidebar-right {
            background: #faf8f6;
            border-left: 1px solid #ede7e2;
            padding: 1.75rem 1.25rem;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        /* Main content */
        .member-main {
            background: #f5f2ef;
            padding: 1.75rem 2rem;
            overflow-y: auto;
        }

        /* Shared component styles */
        .member-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #ede7e2;
            padding: 1.25rem;
        }
        .member-card-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: #16302B;
        }
        .member-card-header .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        .member-stat {
            text-align: center;
            padding: 1rem;
            background: #faf8f6;
            border-radius: 0.75rem;
        }
        .member-stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #2C5F2D;
        }
        .member-stat-label {
            font-size: 0.75rem;
            color: #999;
            margin-top: 0.125rem;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-badge.active {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }
        .status-badge.expired {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
        .btn-member {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #85B79D;
            color: white;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.8125rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-member:hover {
            background: #6fa386;
        }

        /* GT placeholder cards */
        .gt-card-placeholder {
            padding: 1.25rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #e5e7eb, #f3f4f6);
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #9ca3af;
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

        /* === RESPONSIVE === */

        /* Tablet: icon-only left sidebar, right sidebar below content */
        @media (min-width: 768px) and (max-width: 1024px) {
            .member-layout {
                grid-template-columns: 60px 1fr;
                grid-template-rows: 1fr auto;
            }
            .member-sidebar {
                padding: 1rem 0.5rem;
                align-items: center;
            }
            .member-sidebar .nav-label,
            .member-sidebar .sidebar-profile-details,
            .member-sidebar .sidebar-logo-text,
            .member-sidebar .sidebar-user-info {
                display: none;
            }
            .member-sidebar .sidebar-logo-img {
                margin: 0 auto 1rem;
            }
            .member-sidebar-right {
                grid-column: 1 / -1;
                position: static;
                height: auto;
                border-left: none;
                border-top: 1px solid #ede7e2;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 1rem;
                padding: 1.5rem;
            }
        }

        /* Mobile: hamburger left sidebar, right sidebar stacked */
        @media (max-width: 767px) {
            .member-layout {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr auto;
            }
            .member-sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                width: 280px;
                z-index: 50;
                transition: left 0.3s ease;
                height: 100vh;
            }
            .member-sidebar.open {
                left: 0;
            }
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
            .member-mobile-header {
                display: flex;
            }
            .member-sidebar-right {
                position: static;
                height: auto;
                border-left: none;
                border-top: 1px solid #ede7e2;
            }
            .member-main {
                padding: 1rem;
            }
        }
        @media (min-width: 768px) {
            .member-mobile-header {
                display: none;
            }
            .sidebar-overlay {
                display: none !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="member-layout">
        {{-- LEFT SIDEBAR --}}
        <aside class="member-sidebar" id="memberSidebar">
            {{-- Logo --}}
            <a href="{{ route('hub.home') }}" class="flex items-center gap-2.5 mb-6">
                <img src="/images/logo.jpg" alt="OREINA" class="sidebar-logo-img h-8 w-auto rounded-lg" onerror="this.style.display='none'">
                <div class="sidebar-logo-text">
                    <div class="text-white font-bold text-sm">OREINA</div>
                    <div class="text-[10px] text-oreina-green">Mon espace</div>
                </div>
            </a>

            {{-- Profile summary --}}
            @php
                $authUser = auth()->user();
                $authMember = \App\Models\Member::where('user_id', $authUser->id)->first();
                $initials = strtoupper(substr($authMember?->first_name ?? $authUser->name, 0, 1) . substr($authMember?->last_name ?? '', 0, 1));
                $department = $authMember?->postal_code ? substr($authMember->postal_code, 0, 2) : null;
                $authMemberGroups = $authMember?->workGroups()->active()->get() ?? collect();
            @endphp
            <div class="text-center mb-6">
                <div class="relative inline-block mb-2">
                    @if($authMember?->photo_path)
                        <img src="{{ Storage::url($authMember->photo_path) }}" alt="" class="w-16 h-16 rounded-full border-2 border-white/20 object-cover">
                    @else
                        <div class="w-16 h-16 rounded-full bg-oreina-green/30 flex items-center justify-center border-2 border-white/20">
                            <span class="text-white font-bold text-lg">{{ $initials }}</span>
                        </div>
                    @endif
                </div>
                <div class="sidebar-profile-details">
                    <div class="text-white font-semibold text-sm">{{ $authMember?->full_name ?? $authUser->name }}</div>
                    @if($department)
                        <div class="text-white/50 text-xs mt-0.5">Dept. {{ $department }}</div>
                    @endif
                    @if($authMember?->isCurrentMember())
                        <span class="inline-block mt-1.5 text-[10px] font-semibold bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full">Adhérent actif</span>
                    @endif
                </div>
            </div>

            {{-- Member's GT --}}
            @if($authMemberGroups->count() > 0)
            <div class="mb-4 px-1 sidebar-profile-details">
                <div class="text-white/40 text-[10px] font-semibold uppercase tracking-wider mb-2">Mes GT</div>
                @foreach($authMemberGroups as $gt)
                <div class="flex items-center gap-2 mb-1">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: {{ $gt->color }}; flex-shrink: 0;"></div>
                    <span class="text-white/60 text-xs truncate">{{ $gt->name }}</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Navigation --}}
            <nav class="space-y-0.5 flex-1">
                <a href="{{ route('member.dashboard') }}" class="member-nav-item {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span class="nav-label">Tableau de bord</span>
                </a>
                <a href="{{ route('member.profile') }}" class="member-nav-item {{ request()->routeIs('member.profile*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="nav-label">Mon profil</span>
                </a>
                <a href="{{ route('member.membership') }}" class="member-nav-item {{ request()->routeIs('member.membership*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                    <span class="nav-label">Mon adhésion</span>
                </a>
                <a href="{{ route('member.contributions') }}" class="member-nav-item {{ request()->routeIs('member.contributions*') || request()->routeIs('member.work-groups*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span class="nav-label">Mes contributions</span>
                </a>
                <a href="#" class="member-nav-item disabled" title="Bientôt disponible">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="nav-label">Communauté</span>
                </a>
                <a href="{{ route('member.documents') }}" class="member-nav-item {{ request()->routeIs('member.documents*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="nav-label">Mes documents</span>
                </a>
                <a href="{{ route('member.journal') }}" class="member-nav-item {{ request()->routeIs('member.journal*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span class="nav-label">La revue</span>
                </a>
                <a href="{{ route('member.lepis') }}" class="member-nav-item {{ request()->routeIs('member.lepis*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    <span class="nav-label">Lepis</span>
                </a>
                <a href="{{ route('member.profile.preferences') }}" class="member-nav-item {{ request()->routeIs('member.profile.preferences*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="nav-label">Préférences</span>
                </a>
            </nav>

            {{-- Bottom: return + logout --}}
            <div class="pt-4 border-t border-white/10 mt-4 sidebar-user-info">
                <a href="{{ route('hub.home') }}" class="member-nav-item text-xs">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span class="nav-label">Retour au site</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="member-nav-item text-xs w-full text-left text-red-400 hover:text-red-300 hover:bg-red-500/10">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="nav-label">Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- MOBILE OVERLAY --}}
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleMobileSidebar()"></div>

        {{-- MAIN CONTENT --}}
        <div class="member-main">
            {{-- Mobile header --}}
            <div class="member-mobile-header items-center gap-3 mb-4 -mt-2">
                <button type="button" class="p-2 rounded-lg hover:bg-white/50" onclick="toggleMobileSidebar()">
                    <svg class="w-6 h-6 text-oreina-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <a href="{{ route('hub.home') }}" class="flex items-center gap-2">
                    <img src="/images/logo.jpg" alt="OREINA" class="h-7 w-auto rounded" onerror="this.style.display='none'">
                    <span class="font-bold text-oreina-dark text-sm">Mon espace</span>
                </a>
            </div>

            {{-- Flash messages --}}
            @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </div>

        {{-- RIGHT SIDEBAR --}}
        @hasSection('sidebar-right')
            @yield('sidebar-right')
        @else
            <x-member.sidebar-right :events="$upcomingEvents ?? collect()" />
        @endif
    </div>

    <script>
        function toggleMobileSidebar() {
            document.getElementById('memberSidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        }
    </script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
