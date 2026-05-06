<header class="site-header" x-data="{ mobileOpen: false, newsletterOpen: false }">
    {{-- ====== TOP BAR (ligne 1) ====== --}}
    <div class="site-topbar">
        <div class="container site-topbar-inner">
            <button type="button"
                    class="topbar-link"
                    @click="newsletterOpen = true"
                    aria-label="S'inscrire à la newsletter">
                <i data-lucide="mail"></i>
                <span class="topbar-link-label">Newsletter</span>
            </button>

            @auth
                <a href="{{ route('member.dashboard') }}" class="btn btn-sm btn-ghost-dark">
                    <i data-lucide="user-round"></i>
                    <span class="btn-label">Mon espace OREINA</span>
                </a>
            @else
                <a href="{{ route('hub.login') }}" class="btn btn-sm btn-ghost-dark">
                    <i data-lucide="log-in"></i>
                    <span class="btn-label">Connexion</span>
                </a>
            @endauth

            <a href="{{ route('hub.membership') }}" class="btn btn-sm btn-primary">
                <i data-lucide="heart-plus"></i>
                <span class="btn-label">Adhérer</span>
            </a>
        </div>
    </div>

    {{-- ====== MAIN BAR (ligne 2) ====== --}}
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
            @foreach($headerMenu as $item)
                @if($item->children->isEmpty())
                    <a href="{{ $item->url }}"
                       @class(['active' => request()->path() === ltrim($item->url, '/')])
                       {!! $item->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' !!}>
                        {{ $item->label }}
                    </a>
                @else
                    <div class="hub-nav-dropdown" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <a href="{{ $item->url }}" class="hub-nav-dropdown-toggle"
                           {!! $item->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' !!}>
                            {{ $item->label }} ▾
                        </a>
                        <div class="hub-nav-dropdown-menu" x-show="open" x-transition style="display: none;">
                            @foreach($item->children as $child)
                                <a href="{{ $child->url }}"
                                   {!! $child->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' !!}>
                                    {{ $child->label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>

        {{-- Mobile hamburger --}}
        <button type="button" class="mobile-menu-toggle" @click="mobileOpen = !mobileOpen">
            <i x-show="!mobileOpen" data-lucide="menu"></i>
            <i x-show="mobileOpen" data-lucide="x" x-cloak></i>
        </button>
    </div>

    {{-- ====== MOBILE NAV PANEL ====== --}}
    <div class="mobile-nav" x-show="mobileOpen" x-transition x-cloak>
        <nav class="hub-nav-mobile">
            @foreach($headerMenu as $item)
                @if($item->children->isEmpty())
                    <a href="{{ $item->url }}" {!! $item->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' !!}>
                        {{ $item->label }}
                    </a>
                @else
                    <div x-data="{ subOpen: false }">
                        <button type="button" class="hub-nav-mobile-toggle" @click="subOpen = !subOpen" style="display: flex; align-items: center; justify-content: space-between; width: 100%; background: none; border: none; padding: inherit; cursor: pointer; text-align: left; font: inherit; color: inherit;">
                            <span>{{ $item->label }}</span>
                            <span x-text="subOpen ? '▴' : '▾'"></span>
                        </button>
                        <div x-show="subOpen" x-transition x-cloak style="padding-left: 1rem;">
                            @foreach($item->children as $child)
                                <a href="{{ $child->url }}" {!! $child->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' !!}>
                                    {{ $child->label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>
    </div>

    @include('partials.hub.newsletter-modal')
</header>
