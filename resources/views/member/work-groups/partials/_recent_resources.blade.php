<div class="card panel">
    <div class="panel-head">
        <div><h2>Ressources</h2></div>
        <button type="button" @click="tab='ressources'" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;"><i data-lucide="arrow-right"></i>Tout voir</button>
    </div>

    @forelse($recentResources as $resource)
    <div style="display:flex;align-items:flex-start;gap:10px;padding:9px 0;border-bottom:1px solid var(--border);">
        <i data-lucide="{{ $resource->isFile() ? 'file-text' : 'link' }}" style="width:16px;height:16px;flex-shrink:0;margin-top:3px;color:var(--blue);"></i>
        <div style="flex:1;min-width:0;">
            <a href="{{ $resource->url() }}" target="_blank" rel="noopener" class="text-link" style="display:block;font-weight:700;">{{ $resource->title }}</a>
            <small style="color:var(--muted);"><span class="badge">{{ $resource->categoryLabel() }}</span></small>
        </div>
    </div>
    @empty
    <p style="color:var(--muted);padding:8px 0;">Aucune ressource pour le moment.</p>
    @endforelse
</div>
