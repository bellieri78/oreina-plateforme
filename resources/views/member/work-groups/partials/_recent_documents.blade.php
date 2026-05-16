<div class="card panel">
    <div class="panel-head">
        <div><h2>Documents récents</h2></div>
        <button type="button" @click="tab='documents'" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;"><i data-lucide="arrow-right"></i>Tout voir</button>
    </div>
    @forelse($recentDocuments as $doc)
    @php($ext = strtoupper(pathinfo($doc->file_path ?? '', PATHINFO_EXTENSION) ?: 'FICHIER'))
    @php($exists = $doc->file_path && \Storage::disk('public')->exists($doc->file_path))
    @php($size = $exists ? \Storage::disk('public')->size($doc->file_path) : null)
    @php($sizeTxt = $size === null ? '—' : ($size >= 1048576 ? round($size/1048576,1).' Mo' : round($size/1024).' Ko'))
    <div style="display:flex;align-items:flex-start;gap:10px;padding:9px 0;border-bottom:1px solid var(--border);">
        <i data-lucide="file-text" style="width:16px;height:16px;flex-shrink:0;margin-top:3px;color:var(--blue);"></i>
        <div style="flex:1;min-width:0;">
            <a href="{{ $doc->url() }}" target="_blank" rel="noopener" class="text-link" style="display:block;font-weight:700;">{{ $doc->title }}</a>
            <small style="color:var(--muted);">{{ $ext }} · {{ $sizeTxt }} · Modifié {{ $doc->updated_at->diffForHumans() }}</small>
        </div>
    </div>
    @empty
    <p style="color:var(--muted);padding:8px 0;">Aucun document pour le moment.</p>
    @endforelse
</div>
