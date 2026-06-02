<div class="card panel">
    <div class="panel-head">
        <div><h2>Ressources du groupe</h2></div>
        <button type="button" @click="tab='ressources'" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;"><i data-lucide="arrow-right"></i>Voir toutes les ressources</button>
    </div>
    @foreach(config('work_group_resources.categories') as $catKey => $catLabel)
    <div class="gt-res-item">
        <span style="display:inline-flex;align-items:center;gap:10px;"><i data-lucide="folder" style="width:16px;height:16px;color:var(--blue);"></i>{{ $catLabel }}</span>
        <strong>{{ $resourceCounts[$catKey] ?? 0 }}</strong>
    </div>
    @endforeach
    <div class="gt-res-item" style="border-bottom:none;font-weight:800;">
        <span>Total</span><strong>{{ $resourceTotal }}</strong>
    </div>
</div>
