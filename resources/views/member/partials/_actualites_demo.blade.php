@php
$actus = [
    [
        'thumb' => 'images/projets/qualif/stigmella-hemargyrella.jpg',
        'type' => 'Article',
        'type_color' => 'blue',
        'title' => 'Une nouvelle espèce de Zygaena découverte dans le Massif central',
        'meta' => 'Par Jean-Luc Couzi · il y a 4h',
    ],
    [
        'thumb' => 'images/pourquoi/chersotis-oreina.jpg',
        'type' => 'Événement',
        'type_color' => 'sage',
        'title' => 'Sortie collective — Inventaire des Lépidoptères de nuit',
        'meta' => '12 juin 2024 · Saint-Nectaire (63)',
    ],
    [
        'thumb' => 'images/pourquoi/claude-dufay.jpg',
        'type' => 'Appel à contributions',
        'type_color' => 'gold',
        'title' => 'Relecture Chersotis n°1-2026 : appel aux relecteurs',
        'meta' => 'Date limite : 30 juin 2024',
    ],
    [
        'thumb' => 'images/projets/ident/pyrgus-malvoides.jpg',
        'type' => 'Observation remarquable',
        'type_color' => 'coral',
        'title' => 'Agrius convolvuli observé en migration dans le Gers',
        'meta' => 'Par Maxime Guérin · il y a 1j',
    ],
    [
        'thumb' => 'images/magazine/oreina-n68.jpg',
        'type' => 'Publication',
        'type_color' => 'sage',
        'title' => 'Le nouveau bulletin OREINA n°1-2026 est disponible !',
        'meta' => 'Par l\'équipe de rédaction · il y a 2j',
    ],
];
@endphp

<article class="card panel">
    <div class="panel-head">
        <div>
            <h2>Actualités du réseau</h2>
        </div>
        <a href="#" class="text-link" onclick="event.preventDefault();"><i data-lucide="arrow-right"></i>Voir toutes les actualités</a>
    </div>

    <div class="news-feed">
        @foreach($actus as $a)
        <a href="#" class="news-feed-item" onclick="event.preventDefault();">
            <img src="{{ asset($a['thumb']) }}" alt="" class="news-feed-thumb" onerror="this.style.visibility='hidden'">
            <div>
                <span class="news-feed-type {{ $a['type_color'] }}">{{ $a['type'] }}</span>
                <strong>{{ $a['title'] }}</strong>
                <p>{{ $a['meta'] }}</p>
            </div>
        </a>
        @endforeach
    </div>
</article>
