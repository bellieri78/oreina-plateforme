<header class="site-header" x-data="{ mobileOpen: false }">
    <div class="container site-header-inner">
        {{-- Brand --}}
        <a href="{{ route('hub.home') }}" class="brand">
            <div class="brand-mark">
                <img src="/images/logo.jpg" alt="O" onerror="this.style.display='none'; this.nextElementSibling.style.display='';">
                <span style="display:none;font-weight:900;">O</span>
            </div>
            <div class="brand-text">
                <strong>OREINA</strong>
                <span>Lépidoptères de France</span>
            </div>
        </a>

        {{-- Desktop Navigation --}}
        <nav class="hub-nav">
            <a href="{{ route('hub.about') }}" @class(['active' => request()->routeIs('hub.about')])>Association</a>
            <a href="#projets">Projets</a>
            <a href="{{ route('hub.articles.index') }}" @class(['active' => request()->routeIs('hub.articles.*')])>Actualités</a>
            <a href="#reseau">Réseau</a>
            <a href="{{ route('journal.home') }}" @class(['active' => request()->routeIs('journal.*')])>Revue</a>
        </nav>

        {{-- Desktop Actions --}}
        <div class="header-actions">
            @auth
                <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">
                    <i data-lucide="user-round"></i>Mon espace
                </a>
            @else
                <a href="{{ route('hub.login') }}" class="btn btn-secondary">
                    <i data-lucide="log-in"></i>Connexion
                </a>
            @endauth
            <a href="{{ route('hub.membership') }}" class="btn btn-primary">
                <i data-lucide="heart-plus"></i>Adhérer
            </a>
        </div>

        {{-- Mobile hamburger --}}
        <button type="button" class="mobile-menu-toggle" @click="mobileOpen = !mobileOpen">
            <i x-show="!mobileOpen" data-lucide="menu"></i>
            <i x-show="mobileOpen" data-lucide="x" x-cloak></i>
        </button>
    </div>

    {{-- Mobile Navigation --}}
    <div class="mobile-nav" x-show="mobileOpen" x-transition x-cloak>
        <nav class="hub-nav-mobile">
            <a href="{{ route('hub.home') }}">Accueil</a>
            <a href="{{ route('hub.about') }}">Association</a>
            <a href="#projets">Projets</a>
            <a href="{{ route('hub.articles.index') }}">Actualités</a>
            <a href="#reseau">Réseau</a>
            <a href="{{ route('journal.home') }}">Revue</a>
        </nav>
        <div class="header-actions-mobile">
            @auth
                <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">
                    <i data-lucide="user-round"></i>Mon espace
                </a>
            @else
                <a href="{{ route('hub.login') }}" class="btn btn-secondary">
                    <i data-lucide="log-in"></i>Connexion
                </a>
            @endauth
            <a href="{{ route('hub.membership') }}" class="btn btn-primary">
                <i data-lucide="heart-plus"></i>Adhérer
            </a>
        </div>
    </div>
</header>
