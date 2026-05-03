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
