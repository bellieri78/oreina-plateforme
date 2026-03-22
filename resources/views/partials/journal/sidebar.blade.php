{{-- Overlay for mobile --}}
<div id="journal-sidebar-overlay" class="fixed inset-0 bg-black/50 lg:hidden hidden z-30" onclick="document.getElementById('journal-sidebar').classList.add('-translate-x-full'); this.classList.add('hidden');"></div>

{{-- Sidebar --}}
<aside id="journal-sidebar" class="fixed lg:sticky top-16 sm:top-20 left-0 h-[calc(100vh-4rem)] sm:h-[calc(100vh-5rem)] w-16 sm:w-20 journal-sidebar text-white transition-transform duration-300 z-40 overflow-y-auto -translate-x-full lg:translate-x-0">
    <nav class="p-3 sm:p-4 space-y-1">
        {{-- Accueil --}}
        <a href="{{ route('journal.home') }}" class="flex items-center justify-center px-2 sm:px-3 py-2.5 sm:py-3.5 rounded-2xl transition-all group {{ request()->routeIs('journal.home') ? 'bg-oreina-beige text-oreina-dark' : 'hover:bg-white/10' }}" title="Accueil">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 {{ request()->routeIs('journal.home') ? '' : 'text-oreina-turquoise' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
        </a>

        {{-- Articles --}}
        <a href="{{ route('journal.articles.index') }}" class="flex items-center justify-center px-2 sm:px-3 py-2.5 sm:py-3.5 rounded-2xl transition-all group {{ request()->routeIs('journal.articles.*') ? 'bg-oreina-beige text-oreina-dark' : 'hover:bg-white/10' }}" title="Articles">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 {{ request()->routeIs('journal.articles.*') ? '' : 'text-oreina-turquoise' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
        </a>

        {{-- Archives / Numéros --}}
        <a href="{{ route('journal.issues.index') }}" class="flex items-center justify-center px-2 sm:px-3 py-2.5 sm:py-3 rounded-2xl transition-all group {{ request()->routeIs('journal.issues.*') ? 'bg-oreina-beige text-oreina-dark' : 'hover:bg-white/10' }}" title="Archives">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 {{ request()->routeIs('journal.issues.*') ? '' : 'text-oreina-turquoise' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
            </svg>
        </a>

        {{-- Soumettre --}}
        <a href="{{ route('journal.submit') }}" class="flex items-center justify-center px-2 sm:px-3 py-2.5 sm:py-3 rounded-2xl transition-all group {{ request()->routeIs('journal.submit') ? 'bg-oreina-beige text-oreina-dark' : 'hover:bg-white/10' }}" title="Soumettre un article">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 {{ request()->routeIs('journal.submit') ? '' : 'text-oreina-turquoise' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/>
            </svg>
        </a>

        {{-- Instructions aux auteurs --}}
        <a href="{{ route('journal.authors') }}" class="flex items-center justify-center px-2 sm:px-3 py-2.5 sm:py-3 rounded-2xl transition-all group {{ request()->routeIs('journal.authors') ? 'bg-oreina-beige text-oreina-dark' : 'hover:bg-white/10' }}" title="Instructions aux auteurs">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 {{ request()->routeIs('journal.authors') ? '' : 'text-oreina-turquoise' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
            </svg>
        </a>

        {{-- À propos --}}
        <a href="{{ route('journal.about') }}" class="flex items-center justify-center px-2 sm:px-3 py-2.5 sm:py-3 rounded-2xl transition-all group {{ request()->routeIs('journal.about') ? 'bg-oreina-beige text-oreina-dark' : 'hover:bg-white/10' }}" title="À propos">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 {{ request()->routeIs('journal.about') ? '' : 'text-oreina-turquoise' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
            </svg>
        </a>
    </nav>
</aside>
