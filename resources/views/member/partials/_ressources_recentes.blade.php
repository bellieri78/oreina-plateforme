@php
$resources = [
    ['icon' => 'file-text', 'color_bg' => 'rgba(53,107,138,0.10)', 'color_fg' => 'var(--blue)', 'title' => 'Guide des papillons de jour du Massif central', 'meta' => 'PDF · 4.2 Mo'],
    ['icon' => 'file-text', 'color_bg' => 'rgba(239,122,92,0.16)', 'color_fg' => 'var(--coral)', 'title' => 'Protocole de suivi des Lépidoptères', 'meta' => 'PDF · 1.8 Mo'],
    ['icon' => 'video', 'color_bg' => 'rgba(237,196,66,0.20)', 'color_fg' => '#8b6c05', 'title' => 'Webinaire — Les Zygènes de France', 'meta' => 'Vidéo · 52 min'],
    ['icon' => 'book', 'color_bg' => 'rgba(124,58,237,0.10)', 'color_fg' => '#7c3aed', 'title' => 'Fiches espèces — Zygènes lavicolées', 'meta' => 'PDF · 3.1 Mo'],
];
@endphp

<article class="card panel">
    <div class="panel-head">
        <div>
            <h2>Ressources récentes</h2>
        </div>
        <a href="{{ route('hub.lepis.bulletins.index') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir toutes les ressources</a>
    </div>

    <div class="resource-list">
        @foreach($resources as $r)
        <a href="#" class="resource-item" onclick="event.preventDefault(); alert('Bientôt disponible');">
            <div class="resource-item-icon" style="background: {{ $r['color_bg'] }}; color: {{ $r['color_fg'] }};">
                <i data-lucide="{{ $r['icon'] }}"></i>
            </div>
            <div>
                <strong>{{ $r['title'] }}</strong>
                <small>{{ $r['meta'] }}</small>
            </div>
        </a>
        @endforeach
    </div>
</article>
