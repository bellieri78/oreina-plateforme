<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - OREINA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
    <style>
        [x-cloak] { display: none !important; }
        .breadcrumb i[data-lucide] { width: 14px; height: 14px; }
    </style>
</head>
<body class="admin-body">
    <div id="app">
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <aside class="admin-sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
                    <img src="{{ asset('images/logo.jpg') }}" alt="OREINA" onerror="this.style.display='none'">
                    <span>OREINA</span>
                </a>
            </div>

            @php
                $authUser = auth()->user();
                $sectionActive = [
                    'vie-asso'  => request()->routeIs('admin.members.*', 'admin.structures.*', 'admin.map.*', 'admin.memberships.*', 'admin.member-cards.*', 'admin.lepis.*', 'admin.lepis-suggestions.*', 'admin.journal.lepis-queue'),
                    'finances'  => request()->routeIs('admin.donations.*', 'admin.products.*', 'admin.purchases.*'),
                    'benevolat' => request()->routeIs('admin.volunteer.*'),
                    'contenu'   => request()->routeIs('admin.articles.*', 'admin.events.*', 'admin.brevo.*', 'admin.import-export.*'),
                    'revue'     => request()->routeIs('admin.journal-issues.*', 'admin.submissions.*', 'admin.reviews.*', 'admin.journal.queue.*', 'admin.journal.mine', 'admin.journal.submissions.*'),
                    'admin'     => request()->routeIs('admin.users.*', 'admin.settings.*', 'admin.rgpd.*', 'admin.reports.*', 'admin.documentation'),
                ];
            @endphp
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Tableau de bord</span>
                    </a>
                </div>

                {{-- Vie associative --}}
                <div class="nav-section" x-data="navSection('vie-asso', {{ $sectionActive['vie-asso'] ? 'true' : 'false' }})">
                    <button type="button" class="nav-section-title" @click="toggle()">
                        <span>Vie associative</span>
                        <svg class="chevron" :class="{ open: open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                    <div class="nav-section-items" x-show="open" x-transition.duration.150ms>
                        <a href="{{ route('admin.members.index') }}" class="nav-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                            <i data-lucide="users"></i>
                            <span>Contacts</span>
                        </a>
                        <a href="{{ route('admin.structures.index') }}" class="nav-link {{ request()->routeIs('admin.structures.*') ? 'active' : '' }}">
                            <i data-lucide="building-2"></i>
                            <span>Structures</span>
                        </a>
                        <a href="{{ route('admin.map.index') }}" class="nav-link {{ request()->routeIs('admin.map.*') ? 'active' : '' }}">
                            <i data-lucide="map"></i>
                            <span>Carte</span>
                        </a>
                        <a href="{{ route('admin.memberships.index') }}" class="nav-link {{ request()->routeIs('admin.memberships.*') ? 'active' : '' }}">
                            <i data-lucide="credit-card"></i>
                            <span>Adhésions</span>
                        </a>
                        <a href="{{ route('admin.member-cards.index') }}" class="nav-link {{ request()->routeIs('admin.member-cards.*') ? 'active' : '' }}">
                            <i data-lucide="id-card"></i>
                            <span>Cartes d'adhérent</span>
                        </a>
                        <a href="{{ route('admin.lepis.index') }}" class="nav-link {{ request()->routeIs('admin.lepis.*') && ! request()->routeIs('admin.lepis-suggestions.*') ? 'active' : '' }}">
                            <i data-lucide="newspaper"></i>
                            <span>Bulletins Lepis</span>
                        </a>
                        <a href="{{ route('admin.lepis-suggestions.index') }}" class="nav-link {{ request()->routeIs('admin.lepis-suggestions.*') ? 'active' : '' }}">
                            <i data-lucide="message-square-quote"></i>
                            <span>Suggestions Lepis</span>
                        </a>
                        @can('access-lepis-queue')
                            @php
                                $lepisQueueCount = \App\Models\Submission::where('status', 'rejected_pending_lepis')->count();
                            @endphp
                            <a href="{{ route('admin.journal.lepis-queue') }}" class="nav-link {{ request()->routeIs('admin.journal.lepis-queue') ? 'active' : '' }}" style="display:flex;align-items:center;justify-content:space-between;">
                                <span style="display:flex;align-items:center;gap:8px;">
                                    <i data-lucide="file-warning"></i>
                                    <span>File Lepis (articles Chersotis)</span>
                                </span>
                                @if($lepisQueueCount > 0)
                                    <span style="background:#d97706;color:white;font-size:10px;font-weight:700;padding:1px 7px;border-radius:10px;">{{ $lepisQueueCount }}</span>
                                @endif
                            </a>
                        @endcan
                    </div>
                </div>

                {{-- Finances --}}
                <div class="nav-section" x-data="navSection('finances', {{ $sectionActive['finances'] ? 'true' : 'false' }})">
                    <button type="button" class="nav-section-title" @click="toggle()">
                        <span>Finances</span>
                        <svg class="chevron" :class="{ open: open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                    <div class="nav-section-items" x-show="open" x-transition.duration.150ms>
                        <a href="{{ route('admin.donations.index') }}" class="nav-link {{ request()->routeIs('admin.donations.*') ? 'active' : '' }}">
                            <i data-lucide="heart"></i>
                            <span>Dons</span>
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            <i data-lucide="shopping-bag"></i>
                            <span>Produits</span>
                        </a>
                        <a href="{{ route('admin.purchases.index') }}" class="nav-link {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">
                            <i data-lucide="shopping-cart"></i>
                            <span>Achats</span>
                        </a>
                    </div>
                </div>

                {{-- Bénévolat --}}
                <div class="nav-section" x-data="navSection('benevolat', {{ $sectionActive['benevolat'] ? 'true' : 'false' }})">
                    <button type="button" class="nav-section-title" @click="toggle()">
                        <span>Bénévolat</span>
                        <svg class="chevron" :class="{ open: open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                    <div class="nav-section-items" x-show="open" x-transition.duration.150ms>
                        <a href="{{ route('admin.volunteer.index') }}" class="nav-link {{ request()->routeIs('admin.volunteer.index') ? 'active' : '' }}">
                            <i data-lucide="hand-heart"></i>
                            <span>Tableau de bord</span>
                        </a>
                        <a href="{{ route('admin.volunteer.activities') }}" class="nav-link {{ request()->routeIs('admin.volunteer.activities') || request()->routeIs('admin.volunteer.show') || request()->routeIs('admin.volunteer.edit') || request()->routeIs('admin.volunteer.create') ? 'active' : '' }}">
                            <i data-lucide="calendar-days"></i>
                            <span>Activités</span>
                        </a>
                    </div>
                </div>

                {{-- Contenu & communication --}}
                <div class="nav-section" x-data="navSection('contenu', {{ $sectionActive['contenu'] ? 'true' : 'false' }})">
                    <button type="button" class="nav-section-title" @click="toggle()">
                        <span>Contenu & communication</span>
                        <svg class="chevron" :class="{ open: open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                    <div class="nav-section-items" x-show="open" x-transition.duration.150ms>
                        <a href="{{ route('admin.articles.index') }}" class="nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                            <i data-lucide="file-text"></i>
                            <span>Articles</span>
                        </a>
                        <a href="{{ route('admin.events.index') }}" class="nav-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                            <i data-lucide="calendar"></i>
                            <span>Événements</span>
                        </a>
                        <a href="{{ route('admin.brevo.index') }}" class="nav-link {{ request()->routeIs('admin.brevo.*') ? 'active' : '' }}">
                            <i data-lucide="mail"></i>
                            <span>Brevo (Emails)</span>
                        </a>
                        <a href="{{ route('admin.import-export.index') }}" class="nav-link {{ request()->routeIs('admin.import-export.*') ? 'active' : '' }}">
                            <i data-lucide="arrow-up-from-line"></i>
                            <span>Import / Export</span>
                        </a>
                    </div>
                </div>

                {{-- Revue Chersotis --}}
                <div class="nav-section" x-data="navSection('revue', {{ $sectionActive['revue'] ? 'true' : 'false' }})">
                    <button type="button" class="nav-section-title" @click="toggle()">
                        <span>Revue Chersotis</span>
                        <svg class="chevron" :class="{ open: open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                    <div class="nav-section-items" x-show="open" x-transition.duration.150ms>
                        <a href="{{ route('admin.journal-issues.index') }}" class="nav-link {{ request()->routeIs('admin.journal-issues.*') ? 'active' : '' }}">
                            <i data-lucide="book-open"></i>
                            <span>Numéros</span>
                        </a>
                        <a href="{{ route('admin.submissions.index') }}" class="nav-link {{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}">
                            <i data-lucide="inbox"></i>
                            <span>Soumissions</span>
                        </a>
                        <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                            <i data-lucide="clipboard-list"></i>
                            <span>Reviews</span>
                        </a>
                        @if($authUser && ($authUser->hasCapability(\App\Models\EditorialCapability::EDITOR)
                            || $authUser->hasCapability(\App\Models\EditorialCapability::CHIEF_EDITOR)
                            || $authUser->isAdmin()))
                            <a href="{{ route('admin.journal.queue.index') }}" class="nav-link {{ request()->routeIs('admin.journal.queue.*') ? 'active' : '' }}">
                                <i data-lucide="list-todo"></i>
                                <span>File d'attente</span>
                            </a>
                            @if($authUser->hasCapability(\App\Models\EditorialCapability::EDITOR))
                                <a href="{{ route('admin.journal.mine') }}" class="nav-link {{ request()->routeIs('admin.journal.mine') ? 'active' : '' }}">
                                    <i data-lucide="user-round"></i>
                                    <span>Mes articles</span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Administration --}}
                <div class="nav-section" x-data="navSection('admin', {{ $sectionActive['admin'] ? 'true' : 'false' }})">
                    <button type="button" class="nav-section-title" @click="toggle()">
                        <span>Administration</span>
                        <svg class="chevron" :class="{ open: open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                    <div class="nav-section-items" x-show="open" x-transition.duration.150ms>
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i data-lucide="user-cog"></i>
                            <span>Utilisateurs</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.index') || request()->routeIs('admin.settings.update') || request()->routeIs('admin.settings.clear-cache') ? 'active' : '' }}">
                            <i data-lucide="settings"></i>
                            <span>Paramètres</span>
                        </a>
                        <a href="{{ route('admin.settings.statistics') }}" class="nav-link {{ request()->routeIs('admin.settings.statistics') ? 'active' : '' }}">
                            <i data-lucide="bar-chart-3"></i>
                            <span>Statistiques</span>
                        </a>
                        <a href="{{ route('admin.rgpd.index') }}" class="nav-link {{ request()->routeIs('admin.rgpd.*') ? 'active' : '' }}">
                            <i data-lucide="shield-check"></i>
                            <span>RGPD</span>
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i data-lucide="file-bar-chart"></i>
                            <span>Rapports PDF</span>
                        </a>
                        <a href="{{ route('admin.documentation') }}" class="nav-link {{ request()->routeIs('admin.documentation') ? 'active' : '' }}">
                            <i data-lucide="book-marked"></i>
                            <span>Documentation</span>
                        </a>
                    </div>
                </div>
            </nav>

            <script>
                // Alpine helper : sections sidebar collapsibles avec mémoire localStorage.
                // Si la route courante est dans la section (forceOpen=true), on force l'ouverture.
                // Sinon on respecte le choix user (localStorage), sinon ouvert par défaut.
                window.navSection = function(slug, forceOpen) {
                    return {
                        open: (function() {
                            if (forceOpen) return true;
                            const stored = localStorage.getItem('sb-' + slug);
                            return stored === null ? true : stored === 'open';
                        })(),
                        toggle() {
                            this.open = !this.open;
                            localStorage.setItem('sb-' + slug, this.open ? 'open' : 'closed');
                        }
                    };
                };
            </script>

            <div class="sidebar-footer">
                <div class="user-menu">
                    <div class="user-avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <div class="user-role">{{ \App\Models\User::getRoles()[auth()->user()->role ?? 'user'] ?? 'Utilisateur' }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-navbar">
                <div class="navbar-left">
                    <button class="sidebar-toggle" onclick="toggleSidebar()">
                        <i data-lucide="menu"></i>
                    </button>
                    @hasSection('breadcrumb')
                    <nav class="breadcrumb">
                        <a href="{{ route('admin.dashboard') }}">Accueil</a>
                        <i data-lucide="chevron-right" class="breadcrumb-separator"></i>
                        @yield('breadcrumb')
                    </nav>
                    @endif
                </div>
                <div class="navbar-right">
                    <a href="{{ url('/') }}" target="_blank" rel="noopener" class="btn btn-ghost" title="Ouvrir le site vitrine (hub)">
                        <i data-lucide="external-link"></i>
                        Site vitrine
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-ghost">
                            <i data-lucide="log-out"></i>
                            Deconnexion
                        </button>
                    </form>
                </div>
            </header>

            <div class="admin-content">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    <script>
        if (window.lucide) {
            lucide.createIcons();
        } else {
            document.addEventListener('DOMContentLoaded', () => {
                if (window.lucide) lucide.createIcons();
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
