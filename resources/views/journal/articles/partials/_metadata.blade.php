{{-- Metadata Box --}}
<div class="article-meta">
    <div class="meta-grid">
        @if($submission->doi)
        <div>
            <p class="meta-label">DOI</p>
            <p class="meta-value mono">{{ $submission->doi }}</p>
        </div>
        @endif
        <div>
            <p class="meta-label">Date de publication</p>
            <p class="meta-value">{{ $submission->published_at?->translatedFormat('d F Y') ?? '-' }}</p>
        </div>
        <div>
            <p class="meta-label">Type d'article</p>
            <p class="meta-value">Article de recherche</p>
        </div>
        <div>
            <p class="meta-label">Accès</p>
            <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank" rel="noopener" title="Cet article est en accès libre">
                <img src="/images/open-access.png" alt="Open Access" class="meta-logo">
            </a>
        </div>
        <div>
            <p class="meta-label">Licence</p>
            <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank" rel="noopener" title="CC BY 4.0 — Utilisation libre avec attribution">
                <span class="cc-logo">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M15 9.354a4 4 0 1 0 0 5.292"/>
                    </svg>
                    CC BY 4.0
                </span>
            </a>
        </div>
    </div>
</div>
