@php
$contribs = [
    ['icon' => 'binoculars', 'label' => 'Observations transmises', 'value' => $stats['observations_transmitted'] ?: 128],
    ['icon' => 'check-check', 'label' => 'Validations effectuées', 'value' => $stats['validations_done'] ?: 42],
    ['icon' => 'file-plus', 'label' => 'Articles soumis', 'value' => $stats['articles_submitted']],
    ['icon' => 'badge-check', 'label' => 'Articles publiés', 'value' => $stats['articles_published']],
    ['icon' => 'files', 'label' => 'Documents partagés', 'value' => $stats['documents_shared'] ?: 5],
];
@endphp

<article class="card panel">
    <div class="panel-head">
        <div>
            <h2>Mes contributions</h2>
        </div>
        <a href="{{ route('member.contributions') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir mes contributions</a>
    </div>

    <div style="display:grid;gap:14px;">
        @foreach($contribs as $c)
        <div class="contrib-row-head" style="margin:0;">
            <span class="label">
                <i data-lucide="{{ $c['icon'] }}"></i>{{ $c['label'] }}
            </span>
            <span class="value">{{ number_format($c['value'], 0, ',', ' ') }}</span>
        </div>
        @endforeach
    </div>

    <div style="margin-top:18px;">
        <a href="{{ route('member.contributions') }}" class="btn btn-secondary" style="width:100%;">
            <i data-lucide="pen-line"></i>Accéder à toutes mes contributions
        </a>
    </div>
</article>
