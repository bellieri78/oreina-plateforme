<footer class="site-footer">
    <div class="container">
        <div class="footer-card">
            <div>
                <strong style="display:block;font-size:18px;margin-bottom:6px;letter-spacing:-0.03em;">OREINA</strong>
                <p>Association pour l'étude et la protection des Lépidoptères de France. Science participative, réseau naturaliste et outils numériques au service de la connaissance.</p>
                <p style="margin-top:12px;font-size:13px;opacity:.6;">&copy; {{ date('Y') }} OREINA — Tous droits réservés</p>
            </div>
            <div class="footer-links">
                @foreach($footerMenu as $item)
                    <a href="{{ $item->url }}" {!! $item->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' !!}>
                        {{ $item->label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
