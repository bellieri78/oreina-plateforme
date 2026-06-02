<div class="card panel">
    <div class="panel-head">
        <div><h2>Projets collaboratifs</h2></div>
        <button type="button" @click="tab='projets'" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;"><i data-lucide="arrow-right"></i>Voir tous les projets</button>
    </div>

    @forelse($projects->take(2) as $project)
    @php
        $badge = match($project->status) {
            'a_lancer' => 'gold',
            'en_cours' => 'sage',
            'diffuse'  => 'blue',
            default    => '',
        };
    @endphp
    <div style="padding:12px 0;border-bottom:1px solid var(--border);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
            <strong>{{ $project->title }}</strong>
            <span class="badge {{ $badge }}" style="white-space:nowrap;">{{ $project->statusLabel() }}</span>
        </div>
        <div style="margin-top:8px;display:flex;align-items:center;gap:10px;">
            <div style="flex:1;height:8px;border-radius:999px;background:rgba(0,0,0,0.07);overflow:hidden;"><span style="display:block;height:100%;background:var(--sage);border-radius:999px;width:{{ $project->progressClamped() }}%;"></span></div>
            <small style="color:var(--muted);white-space:nowrap;">{{ $project->progressClamped() }} %</small>
        </div>
        @if($project->deliverable_url)
        <a href="{{ $project->deliverable_url }}" target="_blank" rel="noopener" class="text-link" style="display:inline-flex;margin-top:8px;"><i data-lucide="external-link" style="width:14px;height:14px;"></i>Voir le projet</a>
        @else
        <button type="button" @click="tab='projets'" class="text-link" style="background:none;border:none;cursor:pointer;display:inline-flex;margin-top:8px;"><i data-lucide="arrow-right" style="width:14px;height:14px;"></i>Voir le projet</button>
        @endif
    </div>
    @empty
    <p style="color:var(--muted);padding:8px 0;">Aucun projet pour le moment.</p>
    @endforelse
</div>
