<header class="header-glass fixed top-0 left-0 right-0 z-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-10">
        <div class="flex justify-between items-center h-16 sm:h-20">
            {{-- Mobile menu + Logo --}}
            <div class="flex items-center gap-2 sm:gap-4">
                <button type="button" class="p-2 sm:p-2.5 text-slate-600 hover:bg-slate-100 rounded-xl transition-all lg:hidden" id="journal-sidebar-toggle">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <a href="{{ route('journal.home') }}" class="flex items-center gap-2 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 lg:h-14 lg:w-14 bg-oreina-dark rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg sm:text-xl">O</span>
                    </div>
                    <div class="hidden sm:block border-l border-oreina-beige pl-3 sm:pl-4">
                        <div class="text-sm sm:text-base font-bold tracking-tight text-oreina-dark">Revue OREINA</div>
                        <div class="text-xs font-medium text-oreina-turquoise">Publications scientifiques</div>
                    </div>
                </a>
            </div>

            {{-- Search bar (desktop) --}}
            <div class="flex-1 max-w-xl mx-4 lg:mx-12 hidden md:block">
                <form action="{{ route('journal.search') }}" method="GET" class="relative">
                    <svg class="absolute left-3 lg:left-5 top-1/2 -translate-y-1/2 w-4 h-4 lg:w-5 lg:h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="q" placeholder="Rechercher par titre, auteur, mots-clés..."
                           class="w-full pl-10 lg:pl-14 pr-4 lg:pr-5 py-2.5 lg:py-3.5 bg-gray-50 border border-oreina-beige/60 rounded-xl lg:rounded-2xl transition-all text-sm lg:text-base text-slate-700 placeholder:text-slate-400 font-medium focus:border-oreina-turquoise focus:bg-white focus:ring-4 focus:ring-oreina-turquoise/10 outline-none">
                </form>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 sm:gap-4">
                <a href="{{ route('hub.home') }}" class="hidden lg:flex items-center gap-2 text-sm text-slate-600 hover:text-oreina-dark transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Site OREINA
                </a>
                <a href="{{ route('journal.submit') }}" class="hidden sm:inline-flex btn-turquoise text-sm py-2 px-4">
                    Soumettre un article
                </a>
            </div>
        </div>
    </div>

    {{-- Mobile search --}}
    <div class="md:hidden px-4 py-3 border-t border-oreina-beige/30">
        <form action="{{ route('journal.search') }}" method="GET" class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" name="q" placeholder="Rechercher..."
                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-oreina-beige/60 rounded-xl text-sm text-slate-700 placeholder:text-slate-400 font-medium focus:border-oreina-turquoise focus:bg-white outline-none">
        </form>
    </div>
</header>

@push('scripts')
<script>
    document.getElementById('journal-sidebar-toggle')?.addEventListener('click', function() {
        const sidebar = document.getElementById('journal-sidebar');
        const overlay = document.getElementById('journal-sidebar-overlay');
        sidebar?.classList.toggle('-translate-x-full');
        overlay?.classList.toggle('hidden');
    });
</script>
@endpush
