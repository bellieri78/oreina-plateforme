@php
$resources = [
    ['icon' => 'file-text', 'color_bg' => 'rgba(53,107,138,0.10)', 'color_fg' => 'var(--blue)', 'title' => 'Guide des papillons de jour du Massif central', 'meta' => 'Guide PDF · 24.8 Mo'],
    ['icon' => 'file-text', 'color_bg' => 'rgba(239,122,92,0.16)', 'color_fg' => 'var(--coral)', 'title' => 'Protocoles de suivi des Lépidoptères', 'meta' => 'Document · 3.1 Mo'],
    ['icon' => 'video', 'color_bg' => 'rgba(237,196,66,0.20)', 'color_fg' => '#8b6c05', 'title' => 'Webinaire — Les Zygaenidae de France', 'meta' => 'Replay · 58 min'],
    ['icon' => 'book', 'color_bg' => 'rgba(124,58,237,0.10)', 'color_fg' => '#7c3aed', 'title' => 'Fiches espèces — Zygaena lavandulae', 'meta' => 'Fiche espèce · 1.2 Mo'],
];
@endphp

<article class="card panel">
    <div class="panel-head">
        <div>
            <h2>Ressources récentes</h2>
        </div>
        <a href="{{ route('hub.lepis.bulletins.index') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir toutes les ressources</a>
    </div>

    @if($latestLepisBulletin)
    <div class="resource-featured">
        <img src="{{ $latestLepisBulletin->cover_image ? \Storage::url($latestLepisBulletin->cover_image) : asset('images/magazine/oreina-n68.jpg') }}"
             alt="" onerror="this.src='{{ asset('images/magazine/oreina-n68.jpg') }}'">
        <div>
            <small style="color:var(--muted);">Dernier bulletin</small>
            <strong style="font-size:17px;display:block;margin-top:4px;">
                {{ $latestLepisBulletin->title ?? 'OREINA n°' . $latestLepisBulletin->issue_number . '-' . $latestLepisBulletin->year }}
            </strong>
            <small style="display:block;margin-top:4px;color:var(--muted);">Revue scientifique et naturaliste</small>
            <div style="margin-top:10px;">
                <a href="{{ route('hub.lepis.bulletins.show', $latestLepisBulletin) }}" class="btn btn-primary" style="height:36px;padding:0 14px;font-size:13px;">
                    <i data-lucide="book-open" style="width:14px;height:14px;"></i>Lire en ligne
                </a>
            </div>
            @if($latestLepisBulletin->pdf_path)
            <small style="display:block;margin-top:8px;">
                <a href="{{ route('hub.lepis.bulletins.download', $latestLepisBulletin) }}" style="color:var(--blue);">
                    <i data-lucide="download" style="width:12px;height:12px;display:inline;"></i> Télécharger le PDF
                </a>
            </small>
            @endif
        </div>
    </div>
    @endif

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
