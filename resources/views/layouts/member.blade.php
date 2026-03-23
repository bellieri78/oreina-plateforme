<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Mon espace') - OREINA</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Member space specific styles */
        .member-sidebar {
            background: linear-gradient(180deg, #16302B 0%, #1a3a35 100%);
            min-height: 100vh;
        }

        .member-nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            border-radius: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            transition: all 0.2s;
        }

        .member-nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .member-nav-item.active {
            background: rgba(133, 183, 157, 0.2);
            color: #85B79D;
        }

        .member-nav-item svg {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }

        .member-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid rgba(219, 203, 199, 0.3);
            padding: 1.5rem;
        }

        .member-card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(219, 203, 199, 0.3);
        }

        .member-card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #16302B;
        }

        .member-stat {
            text-align: center;
            padding: 1.5rem;
            background: rgba(133, 183, 157, 0.1);
            border-radius: 1rem;
        }

        .member-stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #85B79D;
        }

        .member-stat-label {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
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

        .status-badge.pending {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .document-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border: 1px solid rgba(219, 203, 199, 0.3);
            border-radius: 0.75rem;
            transition: all 0.2s;
        }

        .document-item:hover {
            border-color: #85B79D;
            background: rgba(133, 183, 157, 0.05);
        }

        .btn-member {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: #85B79D;
            color: white;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .btn-member:hover {
            background: #6fa386;
            transform: translateY(-1px);
        }

        .btn-member-outline {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: white;
            color: #16302B;
            border: 1px solid rgba(219, 203, 199, 0.5);
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .btn-member-outline:hover {
            border-color: #85B79D;
            background: rgba(133, 183, 157, 0.05);
        }

        /* Mobile sidebar toggle */
        @media (max-width: 1024px) {
            .member-sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                width: 280px;
                z-index: 50;
                transition: left 0.3s ease;
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
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="member-sidebar w-64 flex-shrink-0 p-4 lg:p-6" id="memberSidebar">
            {{-- Logo --}}
            <a href="{{ route('hub.home') }}" class="flex items-center gap-3 mb-8">
                <img src="/images/logo.jpg" alt="OREINA" class="h-10 w-auto rounded-lg" onerror="this.style.display='none'">
                <div>
                    <div class="text-white font-bold">OREINA</div>
                    <div class="text-xs text-oreina-green">Mon espace</div>
                </div>
            </a>

            {{-- Navigation --}}
            <nav class="space-y-1">
                <a href="{{ route('member.dashboard') }}" class="member-nav-item {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Tableau de bord
                </a>

                <a href="{{ route('member.profile') }}" class="member-nav-item {{ request()->routeIs('member.profile*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Mon profil
                </a>

                <a href="{{ route('member.membership') }}" class="member-nav-item {{ request()->routeIs('member.membership*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                    </svg>
                    Mon adhésion
                </a>

                <a href="{{ route('member.documents') }}" class="member-nav-item {{ request()->routeIs('member.documents*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Mes documents
                </a>

                <a href="{{ route('member.journal') }}" class="member-nav-item {{ request()->routeIs('member.journal*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    La revue
                </a>
            </nav>

            {{-- User info at bottom --}}
            <div class="mt-auto pt-8 border-t border-white/10 mt-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-oreina-green/20 flex items-center justify-center">
                        <span class="text-oreina-green font-bold">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-white font-medium text-sm truncate">{{ auth()->user()->name ?? 'Utilisateur' }}</div>
                        <div class="text-white/50 text-xs truncate">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </div>

                <div class="space-y-1">
                    <a href="{{ route('hub.home') }}" class="member-nav-item text-sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour au site
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="member-nav-item text-sm w-full text-left text-red-400 hover:text-red-300 hover:bg-red-500/10">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Mobile overlay --}}
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleMobileSidebar()"></div>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Top bar --}}
            <header class="bg-white border-b border-gray-200 px-4 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        {{-- Mobile menu button --}}
                        <button type="button" class="lg:hidden p-2 rounded-lg hover:bg-gray-100" onclick="toggleMobileSidebar()">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <div>
                            <h1 class="text-xl font-bold text-oreina-dark">@yield('title', 'Mon espace')</h1>
                            @hasSection('subtitle')
                            <p class="text-sm text-gray-500">@yield('subtitle')</p>
                            @endif
                        </div>
                    </div>

                    @hasSection('actions')
                    <div class="flex items-center gap-3">
                        @yield('actions')
                    </div>
                    @endif
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 p-4 lg:p-8">
                {{-- Flash messages --}}
                @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleMobileSidebar() {
            document.getElementById('memberSidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        }
    </script>

    @stack('scripts')
</body>
</html>
