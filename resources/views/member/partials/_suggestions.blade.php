@php
$cards = [];

if ($suggestionWorkGroup) {
    $cards[] = [
        'eyebrow' => 'Groupes à rejoindre',
        'title' => $suggestionWorkGroup->name,
        'subtitle' => \Str::limit($suggestionWorkGroup->description ?? 'Groupe thématique', 60),
        'cta_label' => 'Rejoindre',
        'cta_href' => route('member.work-groups.show', $suggestionWorkGroup),
        'cta_class' => 'btn-primary',
    ];
}

if ($suggestionArticle) {
    $cards[] = [
        'eyebrow' => 'Article recommandé',
        'title' => $suggestionArticle->title,
        'subtitle' => 'Par ' . ($suggestionArticle->author?->name ?? 'auteur inconnu'),
        'cta_label' => 'Lire l\'article',
        'cta_href' => route('journal.submissions.show', $suggestionArticle),
        'cta_class' => 'btn-secondary',
    ];
}

// Observation tendance — hardcodé Phase 1
$cards[] = [
    'eyebrow' => 'Observation tendance',
    'title' => 'Zygaena fausta',
    'subtitle' => '12 observations ce mois-ci',
    'cta_label' => 'Voir les observations',
    'cta_href' => '#',
    'cta_class' => 'btn-secondary',
];

if ($suggestionEvent) {
    $cards[] = [
        'eyebrow' => 'Événement proche',
        'title' => $suggestionEvent->title,
        'subtitle' => $suggestionEvent->location_city ?? 'En ligne',
        'cta_label' => 'En savoir plus',
        'cta_href' => route('hub.events.show', $suggestionEvent),
        'cta_class' => 'btn-primary',
    ];
}
@endphp

@if($isCurrentMember && count($cards) >= 2)
<section>
    <h2 style="margin:24px 0 14px;">Suggestions pour vous</h2>
    <div class="suggestions-grid">
        @foreach($cards as $c)
        <article class="suggestion-card">
            <span class="eyebrow">{{ $c['eyebrow'] }}</span>
            <strong>{{ $c['title'] }}</strong>
            <p>{{ $c['subtitle'] }}</p>
            <a href="{{ $c['cta_href'] }}" class="btn {{ $c['cta_class'] }}"
               @if($c['cta_href'] === '#') onclick="event.preventDefault(); alert('Bientôt disponible');" @endif>
                {{ $c['cta_label'] }}
            </a>
        </article>
        @endforeach
    </div>
</section>
@endif
