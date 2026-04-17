<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - OREINA</title>
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

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Tableau de bord</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Contacts</div>
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
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Adhesions</div>
                    <a href="{{ route('admin.memberships.index') }}" class="nav-link {{ request()->routeIs('admin.memberships.*') ? 'active' : '' }}">
                        <i data-lucide="credit-card"></i>
                        <span>Adhesions</span>
                    </a>
                    <a href="{{ route('admin.member-cards.index') }}" class="nav-link {{ request()->routeIs('admin.member-cards.*') ? 'active' : '' }}">
                        <i data-lucide="id-card"></i>
                        <span>Cartes d'adherent</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Finances</div>
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

                <div class="nav-section">
                    <div class="nav-section-title">Benevolat</div>
                    <a href="{{ route('admin.volunteer.index') }}" class="nav-link {{ request()->routeIs('admin.volunteer.index') ? 'active' : '' }}">
                        <i data-lucide="hand-heart"></i>
                        <span>Tableau de bord</span>
                    </a>
                    <a href="{{ route('admin.volunteer.activities') }}" class="nav-link {{ request()->routeIs('admin.volunteer.activities') || request()->routeIs('admin.volunteer.show') || request()->routeIs('admin.volunteer.edit') || request()->routeIs('admin.volunteer.create') ? 'active' : '' }}">
                        <i data-lucide="calendar-days"></i>
                        <span>Activites</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Contenu</div>
                    <a href="{{ route('admin.articles.index') }}" class="nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                        <i data-lucide="file-text"></i>
                        <span>Articles</span>
                    </a>
                    <a href="{{ route('admin.events.index') }}" class="nav-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                        <i data-lucide="calendar"></i>
                        <span>Evenements</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Revue</div>
                    <a href="{{ route('admin.journal-issues.index') }}" class="nav-link {{ request()->routeIs('admin.journal-issues.*') ? 'active' : '' }}">
                        <i data-lucide="book-open"></i>
                        <span>Numeros</span>
                    </a>
                    <a href="{{ route('admin.submissions.index') }}" class="nav-link {{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}">
                        <i data-lucide="inbox"></i>
                        <span>Soumissions</span>
                    </a>
                    <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                        <i data-lucide="clipboard-list"></i>
                        <span>Reviews</span>
                    </a>
                    @php $authUser = auth()->user(); @endphp
                    @if($authUser && ($authUser->hasCapability(\App\Models\EditorialCapability::EDITOR)
                        || $authUser->hasCapability(\App\Models\EditorialCapability::CHIEF_EDITOR)
                        || $authUser->isAdmin()))
                        <a href="{{ route('admin.journal.queue.index') }}" class="nav-link {{ request()->routeIs('admin.journal.queue.*') ? 'active' : '' }}">
                            <i data-lucide="list-todo"></i>
                            <span>File d'attente Chersotis</span>
                        </a>
                        @if($authUser->hasCapability(\App\Models\EditorialCapability::EDITOR))
                            <a href="{{ route('admin.journal.mine') }}" class="nav-link {{ request()->routeIs('admin.journal.mine') ? 'active' : '' }}">
                                <i data-lucide="user-round"></i>
                                <span>Mes articles</span>
                            </a>
                        @endif
                    @endif
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Outils</div>
                    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i data-lucide="file-bar-chart"></i>
                        <span>Rapports PDF</span>
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

                <div class="nav-section">
                    <div class="nav-section-title">Administration</div>
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i data-lucide="user-cog"></i>
                        <span>Utilisateurs</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.index') || request()->routeIs('admin.settings.update') || request()->routeIs('admin.settings.clear-cache') ? 'active' : '' }}">
                        <i data-lucide="settings"></i>
                        <span>Parametres</span>
                    </a>
                    <a href="{{ route('admin.settings.statistics') }}" class="nav-link {{ request()->routeIs('admin.settings.statistics') ? 'active' : '' }}">
                        <i data-lucide="bar-chart-3"></i>
                        <span>Statistiques</span>
                    </a>
                    <a href="{{ route('admin.rgpd.index') }}" class="nav-link {{ request()->routeIs('admin.rgpd.*') ? 'active' : '' }}">
                        <i data-lucide="shield-check"></i>
                        <span>RGPD</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Aide</div>
                    <a href="{{ route('admin.documentation') }}" class="nav-link {{ request()->routeIs('admin.documentation') ? 'active' : '' }}">
                        <i data-lucide="book-marked"></i>
                        <span>Documentation</span>
                    </a>
                </div>
            </nav>

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
