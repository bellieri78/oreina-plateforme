@php
$contribs = [
    ['icon' => 'trending-up', 'label' => 'Observations transmises', 'value' => $stats['observations_transmitted']],
    ['icon' => 'check-circle', 'label' => 'Validations effectuées', 'value' => $stats['validations_done']],
    ['icon' => 'file-plus', 'label' => 'Articles soumis', 'value' => $stats['articles_submitted']],
    ['icon' => 'badge-check', 'label' => 'Articles publiés', 'value' => $stats['articles_published']],
    ['icon' => 'file', 'label' => 'Documents partagés', 'value' => $stats['documents_shared']],
];
$maxValue = max(array_column($contribs, 'value')) ?: 1;
@endphp

<article class="card panel">
    <div class="panel-head">
        <div>
            <h2>Mes contributions</h2>
        </div>
        <a href="{{ route('member.contributions') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir toutes mes contributions</a>
    </div>

    <div>
        @foreach($contribs as $c)
        <div class="contrib-row">
            <div class="contrib-row-head">
                <span class="label">
                    <i data-lucide="{{ $c['icon'] }}"></i>{{ $c['label'] }}
                </span>
                <span class="value">{{ $c['value'] }}</span>
            </div>
            <div class="contrib-bar">
                <div class="contrib-bar-fill" style="width: {{ $c['value'] > 0 ? round(($c['value'] / $maxValue) * 100) : 0 }}%;"></div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top:18px;">
        <a href="{{ route('member.contributions') }}" class="btn btn-secondary" style="width:100%;">
            <i data-lucide="arrow-right"></i>Accéder à toutes mes contributions
        </a>
    </div>
</article>
