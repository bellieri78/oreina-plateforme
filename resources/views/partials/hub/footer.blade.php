<footer class="site-footer">
    <div class="container">
        <div class="footer-card">
            <div>
                <strong style="display:block;font-size:18px;margin-bottom:6px;letter-spacing:-0.03em;">OREINA</strong>
                <p>Association pour l'étude et la protection des Lépidoptères de France. Science participative, réseau naturaliste et outils numériques au service de la connaissance.</p>
                <p style="margin-top:12px;font-size:13px;opacity:.6;">&copy; {{ date('Y') }} OREINA — Tous droits réservés</p>
            </div>
            <div class="footer-links">
                <a href="{{ route('hub.about') }}">Association</a>
                <a href="{{ route('hub.home') }}">Portail</a>
                <a href="#">Projets</a>
                <a href="{{ route('hub.articles.index') }}">Actualités</a>
                <a href="{{ route('hub.contact') }}">Réseau</a>
                <a href="#">Mentions légales</a>
                <a href="#">Politique de données</a>
            </div>
        </div>
    </div>
</footer>
