<article class="card panel">
    <div class="panel-head" style="margin-bottom:14px;">
        <div><h2>À la une</h2></div>
    </div>

    @if($latestLepisBulletin)
        <a href="{{ route('hub.lepis.bulletins.show', $latestLepisBulletin) }}" class="feature-card">
            <img src="{{ $latestLepisBulletin->cover_image ? \Storage::url($latestLepisBulletin->cover_image) : asset('images/magazine/oreina-n68.jpg') }}"
                 alt="" onerror="this.src='{{ asset('images/magazine/oreina-n68.jpg') }}'">
            @if($latestLepisBulletin->issue_number)
                <span class="feature-card-tag">#{{ $latestLepisBulletin->issue_number }}</span>
            @endif
            <div class="feature-card-body">
                <h3>{{ $latestLepisBulletin->title ?? 'Lepis n°' . $latestLepisBulletin->issue_number }}</h3>
                <p>Bulletin naturaliste OREINA{{ $latestLepisBulletin->year ? ' · ' . $latestLepisBulletin->year : '' }}</p>
                <span class="btn-feature">
                    <i data-lucide="book-open" style="width:14px;height:14px;"></i>Lire en ligne
                </span>
            </div>
        </a>
    @else
        <div class="feature-card">
            <img src="{{ asset('images/magazine/oreina-n68.jpg') }}" alt="">
            <div class="feature-card-body">
                <h3>Bulletin Lepis</h3>
                <p>Le dernier numéro sera bientôt disponible ici.</p>
                <a href="{{ route('hub.lepis.bulletins.index') }}" class="btn-feature">
                    <i data-lucide="book-open" style="width:14px;height:14px;"></i>Voir les numéros
                </a>
            </div>
        </div>
    @endif

    <div style="margin-top:auto;padding-top:14px;text-align:right;">
        <a href="{{ route('hub.lepis.bulletins.index') }}" class="text-link"><i data-lucide="arrow-right"></i>Tous les numéros</a>
    </div>
</article>
