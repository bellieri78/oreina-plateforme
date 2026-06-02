@php
    $resPalette = [
        ['#e7f3ec','#2f694e','file-text'],
        ['#e4eef5','#356B8A','book-open'],
        ['#f3ecfb','#7c3aed','link'],
        ['#fdeede','#b45309','clipboard-list'],
        ['#fde8ea','#b91c1c','hard-drive'],
    ];
@endphp
<div class="card panel">
    <div class="panel-head">
        <div><h2>Ressources du groupe</h2></div>
        <button type="button" @click="tab='ressources'" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;"><i data-lucide="arrow-right"></i>Voir toutes les ressources</button>
    </div>
    @foreach(array_values(config('work_group_resources.categories')) as $i => $catLabel)
    @php($catKey = array_keys(config('work_group_resources.categories'))[$i])
    @php($pal = $resPalette[$i % count($resPalette)])
    <div class="gt-listrow">
        <span style="display:inline-flex;align-items:center;gap:10px;">
            <span class="gt-sq" style="background:{{ $pal[0] }};color:{{ $pal[1] }};"><i data-lucide="{{ $pal[2] }}"></i></span>
            {{ $catLabel }}
        </span>
        <strong>{{ $resourceCounts[$catKey] ?? 0 }}</strong>
    </div>
    @endforeach
</div>
