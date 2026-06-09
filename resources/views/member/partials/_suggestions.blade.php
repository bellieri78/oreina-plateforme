@php
$cards = [];

if ($suggestionArticle) {
    $cards[] = [
        'eyebrow'   => 'Article',
        'title'     => \Str::limit($suggestionArticle->title, 64),
        'subtitle'  => 'Par ' . ($suggestionArticle->author?->name ?? 'auteur OREINA'),
        'cta_label' => "Lire l'article",
        'cta_class' => 'btn-secondary',
        'cta_href'  => route('journal.articles.show', $suggestionArticle),
        'image'     => asset('images/projets/ident/pyrgus-malvoides.jpg'),
    ];
}

// Observation locale — démo Phase 1
$cards[] = [
    'eyebrow'   => 'Observation locale',
    'title'     => 'Zygaena fausta',
    'subtitle'  => 'Vu dans les Alpes-de-Haute-Provence',
    'cta_label' => 'Voir les observations',
    'cta_class' => 'btn-secondary',
    'cta_href'  => '#',
    'image'     => asset('images/projets/qualif/stigmella-hemargyrella.jpg'),
];

// Ressource — démo Phase 1
$cards[] = [
    'eyebrow'   => 'Ressource',
    'title'     => "Clé d'identification des Zygènes",
    'subtitle'  => 'Version mise à jour 2024',
    'cta_label' => 'Consulter',
    'cta_class' => 'btn-secondary',
    'cta_href'  => '#',
    'image'     => asset('images/pourquoi/chersotis-oreina.jpg'),
];

if ($suggestionWorkGroup) {
    $cards[] = [
        'eyebrow'   => 'Groupe à rejoindre',
        'title'     => $suggestionWorkGroup->name,
        'subtitle'  => \Str::limit($suggestionWorkGroup->description ?? 'Groupe thématique', 56),
        'cta_label' => 'Rejoindre',
        'cta_class' => 'btn-primary',
        'cta_href'  => route('member.work-groups.show', $suggestionWorkGroup),
        'image'     => asset('images/magazine/oreina-n68.jpg'),
    ];
}
@endphp

@if($isCurrentMember && count($cards) >= 2)
<section>
    <h2 style="margin:8px 0 14px;">Suggestions pour vous</h2>
    <div class="suggestions-grid">
        @foreach(array_slice($cards, 0, 3) as $c)
        <article class="suggestion-card has-image">
            <div class="sugg-body">
                <span class="eyebrow">{{ $c['eyebrow'] }}</span>
                <strong>{{ $c['title'] }}</strong>
                <p>{{ $c['subtitle'] }}</p>
                <a href="{{ $c['cta_href'] }}" class="btn {{ $c['cta_class'] }}"
                   @if($c['cta_href'] === '#') onclick="event.preventDefault(); alert('Bientôt disponible');" @endif>
                    {{ $c['cta_label'] }}
                </a>
            </div>
            <div class="sugg-media">
                <img src="{{ $c['image'] }}" alt="" onerror="this.parentNode.style.display='none';">
            </div>
        </article>
        @endforeach
    </div>
</section>
@endif
