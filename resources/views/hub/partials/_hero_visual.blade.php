{{--
    Partial : visuel du chapô des pages projets
    --------------------------------------------------
    Usage :
    @include('hub.partials._hero_visual', [
        'image'    => '/images/projets/seqref/saturnia-pavonia.webp',
        'fallback' => '/images/projets/seqref/saturnia-pavonia.jpg',
        'alt'      => 'Saturnia pavonia (Linnaeus, 1758), mâle au repos sur écorce',
        'species'  => 'Saturnia pavonia',
        'author'   => '(Linnaeus, 1758)',
        'caption'  => 'Pyrénées, V.2024',
        'credit'   => 'D. Demergès',
        'ramp'     => 'blue',
    ])

    Paramètres :
    - image    : URL absolue du fichier WebP (préféré)
    - fallback : URL absolue du fichier JPEG de secours
    - alt      : texte alternatif (accessibilité)
    - species  : nom binominal, affiché en italique
    - author   : auteur de l'espèce — ex. "(Linnaeus, 1758)"
    - caption  : contexte géographique et temporel
    - credit   : crédit photographique
    - ramp     : couleur d'accent — blue | sage | coral | gold | green
--}}

@php
    $ramp = $ramp ?? 'blue';
    $rampMap = [
        'blue'   => 'from-oreina-blue/10 to-oreina-turquoise/10',
        'sage'   => 'from-oreina-green/10 to-oreina-turquoise/10',
        'coral'  => 'from-oreina-coral/10 to-oreina-yellow/10',
        'gold'   => 'from-oreina-yellow/10 to-oreina-coral/10',
        'green'  => 'from-oreina-green/10 to-oreina-yellow/10',
    ];
    $gradient = $rampMap[$ramp] ?? $rampMap['blue'];
@endphp

<figure class="rounded-3xl shadow-lg overflow-hidden relative bg-gradient-to-br {{ $gradient }}"
        style="aspect-ratio: 4/5; min-height: 340px;">
    <picture>
        @isset($image)
            <source srcset="{{ $image }}" type="image/webp">
        @endisset
        <img src="{{ $fallback ?? $image }}"
             alt="{{ $alt }}"
             class="w-full h-full object-cover"
             loading="lazy"
             onerror="this.style.display='none'; this.parentElement.parentElement.querySelector('.hero-visual-fallback').style.display='flex';">
    </picture>

    {{-- Légende scientifique en overlay --}}
    <figcaption class="absolute bottom-0 left-0 right-0
                       bg-gradient-to-t from-black/75 via-black/40 to-transparent
                       text-white text-sm px-5 py-4 leading-tight">
        <em>{{ $species }}</em>@isset($author) {{ $author }}@endisset@isset($caption) — {{ $caption }}@endisset
        @isset($credit)
            <span class="block text-white/70 text-xs mt-1">© {{ $credit }}</span>
        @endisset
    </figcaption>

    {{-- Fallback affiché si l'image est introuvable --}}
    <div class="hero-visual-fallback absolute inset-0 items-center justify-center text-center p-8"
         style="display: none;">
        <div>
            <i data-lucide="image" style="width:48px;height:48px;color:#cbd5e1;margin:0 auto 12px"></i>
            <p class="text-slate-500 text-sm font-bold"><em>{{ $species }}</em></p>
            <p class="text-slate-400 text-xs mt-1">Photo à intégrer</p>
        </div>
    </div>
</figure>
