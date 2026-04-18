<header class="site-header" x-data="{ mobileOpen: false }">
    <div class="container site-header-inner">
        {{-- Brand --}}
        <div class="brand">
            <a href="{{ route('hub.home') }}" class="brand-mark">
                <img src="/images/logo.jpg" alt="O" onerror="this.style.display='none'; this.nextElementSibling.style.display='';">
                <span style="display:none;font-weight:900;">O</span>
            </a>
            <a href="{{ route('journal.home') }}" class="brand-text">
                <strong>Chersotis</strong>
                <span>Revue scientifique OREINA</span>
            </a>
        </div>

        {{-- Desktop Navigation --}}
        <nav class="hub-nav">
            <a href="{{ route('journal.articles.index') }}" @class(['active' => request()->routeIs('journal.articles.*')])>Articles</a>
            <a href="{{ route('journal.issues.index') }}" @class(['active' => request()->routeIs('journal.issues.*')])>Numéros</a>
            <a href="{{ route('journal.authors') }}" @class(['active' => request()->routeIs('journal.authors')])>Auteurs</a>
            <a href="{{ route('journal.about') }}" @class(['active' => request()->routeIs('journal.about')])>À propos</a>
        </nav>

        {{-- Desktop Actions --}}
        <div class="header-actions">
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
    <div class="mobile-nav" x-show="mobileOpen" x-transition x-cloak>
        <nav class="hub-nav-mobile">
            <a href="{{ route('journal.articles.index') }}" @class(['active' => request()->routeIs('journal.articles.*')])>Articles</a>
            <a href="{{ route('journal.issues.index') }}" @class(['active' => request()->routeIs('journal.issues.*')])>Numéros</a>
            <a href="{{ route('journal.authors') }}" @class(['active' => request()->routeIs('journal.authors')])>Auteurs</a>
            <a href="{{ route('journal.about') }}" @class(['active' => request()->routeIs('journal.about')])>À propos</a>
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
