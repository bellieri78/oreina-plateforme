<header class="header-glass fixed top-0 left-0 right-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            {{-- Logo Section --}}
            <a href="{{ route('hub.home') }}" class="flex items-center gap-4">
                <img src="/images/logo.jpg" alt="OREINA" class="h-14 w-auto rounded-lg" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="w-14 h-14 bg-oreina-green rounded-full items-center justify-center hidden">
                    <span class="text-white font-bold text-2xl">O</span>
                </div>
                <div class="hidden sm:block border-l border-oreina-beige pl-4">
                    <div class="text-base font-bold text-oreina-dark tracking-tight">OREINA</div>
                    <div class="text-xs font-medium text-oreina-green">Lépidoptères de France</div>
                </div>
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden lg:flex items-center gap-8">
                <a href="{{ route('hub.about') }}" class="text-oreina-dark font-medium hover:text-oreina-green transition {{ request()->routeIs('hub.about') ? 'text-oreina-green' : '' }}">
                    Association
                </a>
                <a href="#outils" class="text-oreina-dark font-medium hover:text-oreina-green transition">
                    Outils
                </a>
                <a href="#projets" class="text-oreina-dark font-medium hover:text-oreina-green transition">
                    Projets
                </a>
                <a href="{{ route('hub.articles.index') }}" class="text-oreina-dark font-medium hover:text-oreina-green transition {{ request()->routeIs('hub.articles.*') ? 'text-oreina-green' : '' }}">
                    Actualités
                </a>
            </nav>

            {{-- CTA + Mobile menu button --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('hub.membership') }}" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    <span class="hidden sm:inline">Adhérer</span>
                </a>

                {{-- Mobile menu button --}}
                <button type="button" class="lg:hidden p-2 rounded-xl hover:bg-oreina-beige/50 transition" id="mobile-menu-btn" aria-label="Menu">
                    <svg class="w-6 h-6 text-oreina-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Navigation --}}
        <div class="lg:hidden hidden" id="mobile-menu">
            <div class="py-4 space-y-1 border-t border-oreina-beige/30">
                <a href="{{ route('hub.home') }}" class="block px-4 py-3 rounded-xl hover:bg-oreina-beige/30 transition font-medium {{ request()->routeIs('hub.home') ? 'bg-oreina-beige/30 text-oreina-green' : 'text-oreina-dark' }}">
                    Accueil
                </a>
                <a href="{{ route('hub.about') }}" class="block px-4 py-3 rounded-xl hover:bg-oreina-beige/30 transition font-medium {{ request()->routeIs('hub.about') ? 'bg-oreina-beige/30 text-oreina-green' : 'text-oreina-dark' }}">
                    Association
                </a>
                <a href="{{ route('hub.articles.index') }}" class="block px-4 py-3 rounded-xl hover:bg-oreina-beige/30 transition font-medium {{ request()->routeIs('hub.articles.*') ? 'bg-oreina-beige/30 text-oreina-green' : 'text-oreina-dark' }}">
                    Actualités
                </a>
                <a href="{{ route('hub.events.index') }}" class="block px-4 py-3 rounded-xl hover:bg-oreina-beige/30 transition font-medium {{ request()->routeIs('hub.events.*') ? 'bg-oreina-beige/30 text-oreina-green' : 'text-oreina-dark' }}">
                    Événements
                </a>
                <a href="{{ route('hub.membership') }}" class="block px-4 py-3 rounded-xl hover:bg-oreina-beige/30 transition font-medium {{ request()->routeIs('hub.membership') ? 'bg-oreina-beige/30 text-oreina-green' : 'text-oreina-dark' }}">
                    Adhésion
                </a>
                <a href="{{ route('hub.contact') }}" class="block px-4 py-3 rounded-xl hover:bg-oreina-beige/30 transition font-medium {{ request()->routeIs('hub.contact') ? 'bg-oreina-beige/30 text-oreina-green' : 'text-oreina-dark' }}">
                    Contact
                </a>
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
@endpush
