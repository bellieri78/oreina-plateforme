{{-- Hub Header - OREINA Design System V4 --}}
<header class="site-header" x-data="{ mobileOpen: false }">
    <div class="container site-header-inner">
        {{-- Brand --}}
        <a href="{{ route('hub.home') }}" class="brand">
            <div class="brand-mark">
                <img src="/images/logo.jpg" alt="OREINA" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;" onerror="this.style.display='none'; this.nextElementSibling.style.display='';">
                <span style="display:none;font-weight:900;">O</span>
            </div>
            <div class="brand-text">
                <strong>OREINA</strong>
                <span>Lépidoptères de France</span>
            </div>
        </a>

        {{-- Desktop Navigation --}}
        <nav class="nav nav-desktop">
            <a href="{{ route('hub.about') }}" @class(['active' => request()->routeIs('hub.about')])>Association</a>
            <a href="#portail">Portail</a>
            <a href="#projets">Projets</a>
            <a href="{{ route('hub.articles.index') }}" @class(['active' => request()->routeIs('hub.articles.*')])>Actualités</a>
            <a href="#reseau">Réseau</a>
        </nav>

        {{-- Header Actions (desktop) --}}
        <div class="header-actions header-actions-desktop">
            @auth
                <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">
                    <i data-lucide="user-round"></i>
                    <span>Mon espace</span>
                </a>
            @endauth
            <a href="{{ route('hub.membership') }}" class="btn btn-primary">
                <i data-lucide="heart-plus"></i>
                <span>Adhérer</span>
            </a>
        </div>

        {{-- Mobile hamburger --}}
        <button
            type="button"
            class="mobile-menu-toggle"
            aria-label="Ouvrir le menu"
            @click="mobileOpen = !mobileOpen"
            :aria-expanded="mobileOpen"
        >
            <i x-show="!mobileOpen" data-lucide="menu"></i>
            <i x-show="mobileOpen" data-lucide="x" x-cloak></i>
        </button>
    </div>

    {{-- Mobile Navigation --}}
    <div class="mobile-nav" x-show="mobileOpen" x-collapse x-cloak>
        <nav class="nav nav-mobile">
            <a href="{{ route('hub.home') }}" @class(['active' => request()->routeIs('hub.home')])>Accueil</a>
            <a href="{{ route('hub.about') }}" @class(['active' => request()->routeIs('hub.about')])>Association</a>
            <a href="#portail">Portail</a>
            <a href="#projets">Projets</a>
            <a href="{{ route('hub.articles.index') }}" @class(['active' => request()->routeIs('hub.articles.*')])>Actualités</a>
            <a href="#reseau">Réseau</a>
        </nav>
        <div class="header-actions header-actions-mobile">
            @auth
                <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">
                    <i data-lucide="user-round"></i>
                    <span>Mon espace</span>
                </a>
            @endauth
            <a href="{{ route('hub.membership') }}" class="btn btn-primary">
                <i data-lucide="heart-plus"></i>
                <span>Adhérer</span>
            </a>
        </div>
    </div>
</header>

@push('styles')
<style>
    /* ── Site Header ─────────────────────────────────────── */
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

    /* Brand */
    .brand {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
        text-decoration: none;
        color: inherit;
    }

    .brand-mark {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: var(--surface, #fff);
        border: 1px solid var(--border, rgba(22,48,43,0.08));
        box-shadow: var(--shadow, 0 1px 3px rgba(22,48,43,0.06));
        display: grid;
        place-items: center;
        font-weight: 900;
        color: var(--forest, #2C5F2D);
        overflow: hidden;
        flex-shrink: 0;
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
        color: var(--muted, #6b7280);
        font-size: 12px;
        white-space: nowrap;
    }

    /* Navigation */
    .nav {
        display: flex;
        align-items: center;
        gap: 24px;
        color: var(--muted, #6b7280);
        font-size: 15px;
        font-weight: 600;
    }

    .nav a {
        text-decoration: none;
        color: inherit;
        transition: color 0.15s ease;
    }

    .nav a:hover,
    .nav a.active {
        color: var(--text, #16302B);
    }

    /* Header actions */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Mobile toggle */
    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        color: var(--text, #16302B);
    }

    .mobile-menu-toggle i,
    .mobile-menu-toggle svg {
        width: 24px;
        height: 24px;
    }

    /* Mobile nav panel */
    .mobile-nav {
        padding: 0 var(--container-pad, 24px) 20px;
        border-top: 1px solid rgba(22,48,43,0.06);
    }

    .nav-mobile {
        flex-direction: column;
        align-items: stretch;
        gap: 4px;
        padding: 12px 0;
    }

    .nav-mobile a {
        padding: 10px 14px;
        border-radius: 10px;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .nav-mobile a:hover,
    .nav-mobile a.active {
        background: rgba(22,48,43,0.05);
        color: var(--text, #16302B);
    }

    .header-actions-mobile {
        flex-direction: column;
        padding-top: 8px;
        border-top: 1px solid rgba(22,48,43,0.06);
    }

    .header-actions-mobile .btn {
        width: 100%;
        justify-content: center;
    }

    /* ── Responsive ──────────────────────────────────────── */
    @media (min-width: 1081px) {
        .mobile-menu-toggle,
        .mobile-nav {
            display: none !important;
        }
    }

    @media (max-width: 1080px) {
        .nav-desktop,
        .header-actions-desktop {
            display: none;
        }

        .mobile-menu-toggle {
            display: flex;
            align-items: center;
        }

        .site-header-inner {
            min-height: 64px;
        }
    }

    @media (max-width: 480px) {
        .brand-text {
            display: none;
        }
    }
</style>
@endpush
