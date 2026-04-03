<header class="site-header" x-data="{ mobileOpen: false }">
    <div class="container site-header-inner">
        {{-- Brand --}}
        <a href="{{ route('journal.home') }}" class="brand">
            <div class="brand-mark" style="background:var(--accent);">
                <img src="/images/logo.jpg" alt="O" style="mix-blend-mode:luminosity;opacity:0.9;" onerror="this.style.display='none'; this.nextElementSibling.style.display='';">
                <span style="display:none;color:white;font-weight:900;font-size:22px;">O</span>
            </div>
            <div class="brand-text">
                <strong>Revue OREINA</strong>
                <span>Publications scientifiques</span>
            </div>
        </a>

        {{-- Desktop Navigation --}}
        <nav class="hub-nav">
            <a href="{{ route('journal.home') }}" @class(['active' => request()->routeIs('journal.home')])>Accueil</a>
            <a href="{{ route('journal.articles.index') }}" @class(['active' => request()->routeIs('journal.articles.*')])>Articles</a>
            <a href="{{ route('journal.issues.index') }}" @class(['active' => request()->routeIs('journal.issues.*')])>Num&eacute;ros</a>
            <a href="{{ route('journal.authors') }}" @class(['active' => request()->routeIs('journal.authors')])>Auteurs</a>
            <a href="{{ route('journal.about') }}" @class(['active' => request()->routeIs('journal.about')])>&Agrave; propos</a>
        </nav>

        {{-- Desktop Actions --}}
        <div class="header-actions">
            <div class="header-search">
                <form action="{{ route('journal.search') }}" method="GET">
                    <i data-lucide="search"></i>
                    <input type="text" name="q" placeholder="Rechercher articles, auteurs...">
                </form>
            </div>
            <a href="{{ route('hub.home') }}" class="btn btn-secondary">
                <i data-lucide="arrow-left"></i>Site OREINA
            </a>
            <a href="{{ route('journal.submit') }}" class="btn btn-primary">
                <i data-lucide="file-plus"></i>Soumettre
            </a>
        </div>

        {{-- Mobile hamburger --}}
        <button type="button" class="mobile-menu-toggle" @click="mobileOpen = !mobileOpen">
            <i x-show="!mobileOpen" data-lucide="menu"></i>
            <i x-show="mobileOpen" data-lucide="x" x-cloak></i>
        </button>
    </div>

    {{-- Mobile Navigation --}}
    <div class="mobile-nav" x-show="mobileOpen" x-collapse x-cloak>
        <div class="mobile-search">
            <form action="{{ route('journal.search') }}" method="GET" style="position:relative;">
                <i data-lucide="search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:var(--muted);pointer-events:none;"></i>
                <input type="text" name="q" placeholder="Rechercher articles, auteurs...">
            </form>
        </div>
        <nav class="hub-nav-mobile">
            <a href="{{ route('journal.home') }}" @class(['active' => request()->routeIs('journal.home')])>Accueil</a>
            <a href="{{ route('journal.articles.index') }}" @class(['active' => request()->routeIs('journal.articles.*')])>Articles</a>
            <a href="{{ route('journal.issues.index') }}" @class(['active' => request()->routeIs('journal.issues.*')])>Num&eacute;ros</a>
            <a href="{{ route('journal.authors') }}" @class(['active' => request()->routeIs('journal.authors')])>Auteurs</a>
            <a href="{{ route('journal.about') }}" @class(['active' => request()->routeIs('journal.about')])>&Agrave; propos</a>
        </nav>
        <div class="header-actions-mobile">
            <a href="{{ route('hub.home') }}" class="btn btn-secondary">
                <i data-lucide="arrow-left"></i>Site OREINA
            </a>
            <a href="{{ route('journal.submit') }}" class="btn btn-primary">
                <i data-lucide="file-plus"></i>Soumettre un article
            </a>
        </div>
    </div>
</header>
