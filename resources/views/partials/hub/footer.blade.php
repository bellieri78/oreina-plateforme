<footer class="footer-gradient text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            {{-- Logo et description --}}
            <div>
                <img src="/images/logo.jpg" alt="OREINA" class="h-14 w-auto mb-6 bg-white rounded-lg p-3" onerror="this.style.display='none';">
                <p class="text-sm leading-relaxed text-oreina-beige">
                    Association loi 1901 pour le partage des connaissances sur les Lépidoptères de France.
                </p>
            </div>

            {{-- Association --}}
            <div>
                <h4 class="font-bold mb-6">Association</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('hub.about') }}" class="text-oreina-beige hover:text-white transition">Qui sommes-nous</a></li>
                    <li><a href="{{ route('hub.membership') }}" class="text-oreina-beige hover:text-white transition">Adhérer</a></li>
                    <li><a href="#" class="text-oreina-beige hover:text-white transition">Groupes de travail</a></li>
                    <li><a href="{{ route('hub.events.index') }}" class="text-oreina-beige hover:text-white transition">Événements</a></li>
                </ul>
            </div>

            {{-- Outils --}}
            <div>
                <h4 class="font-bold mb-6">Outils</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="#" class="text-oreina-beige hover:text-white transition">Artemisiae</a></li>
                    <li><a href="#" class="text-oreina-beige hover:text-white transition">BDC Traits de vie</a></li>
                    <li><a href="{{ route('journal.home') }}" class="text-oreina-beige hover:text-white transition">Revue scientifique</a></li>
                    <li><a href="#" class="text-oreina-beige hover:text-white transition">Labo Lepido</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h4 class="font-bold mb-6">Contact</h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center gap-3 text-oreina-beige">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="20" height="16" x="2" y="4" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                        <a href="mailto:contact@oreina.org" class="hover:text-white transition">contact@oreina.org</a>
                    </li>
                    <li class="flex items-center gap-3 text-oreina-beige">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span>Paris, France</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom --}}
        <div class="border-t border-oreina-beige/20 pt-8 flex flex-col lg:flex-row justify-between items-center gap-6">
            <div class="flex flex-wrap items-center justify-center lg:justify-start gap-6 text-sm text-oreina-beige">
                <span>&copy; {{ date('Y') }} OREINA</span>
                <a href="#" class="hover:text-white transition">Mentions légales</a>
                <a href="#" class="hover:text-white transition">Politique de données</a>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-xs text-oreina-beige/60">Partenaires:</span>
                <span class="px-3 py-1.5 rounded-lg bg-oreina-beige/10 text-oreina-beige text-xs font-semibold">OFB</span>
                <span class="px-3 py-1.5 rounded-lg bg-oreina-beige/10 text-oreina-beige text-xs font-semibold">PATRINAT</span>
                <span class="px-3 py-1.5 rounded-lg bg-oreina-beige/10 text-oreina-beige text-xs font-semibold">MNHN</span>
            </div>
        </div>
    </div>
</footer>
