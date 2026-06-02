<div class="card panel">
    <div class="panel-head">
        <div><h2>Projets collaboratifs</h2></div>
        <button type="button" @click="tab='projets'" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;"><i data-lucide="arrow-right"></i>Voir tous les projets</button>
    </div>

    @if($projects->count())
    <div class="gt-proj-grid">
        @foreach($projects->take(3) as $project)
        @php
            $badge = match($project->status) {
                'a_lancer' => 'gold',
                'en_cours' => 'sage',
                'diffuse'  => 'blue',
                default    => '',
            };
        @endphp
        <div class="gt-proj">
            <div class="gt-proj-thumb" style="background:{{ $workGroup->color ?? '#85B79D' }};">
                <i data-lucide="folder-kanban"></i>
            </div>
            <div class="gt-proj-bd">
                <strong style="font-size:13.5px;line-height:1.3;">{{ $project->title }}</strong>
                <span class="badge {{ $badge }}" style="align-self:flex-start;">{{ $project->statusLabel() }}</span>
                <div style="display:flex;align-items:center;gap:8px;margin-top:auto;">
                    <div style="flex:1;height:7px;border-radius:999px;background:rgba(0,0,0,0.07);overflow:hidden;"><span style="display:block;height:100%;background:var(--sage);border-radius:999px;width:{{ $project->progressClamped() }}%;"></span></div>
                    <small style="color:var(--muted);white-space:nowrap;font-weight:700;">{{ $project->progressClamped() }} %</small>
                </div>
            </div>
            <div class="gt-proj-foot">
                @if($project->deliverable_url)
                <a href="{{ $project->deliverable_url }}" target="_blank" rel="noopener" class="text-link" style="font-size:13px;"><i data-lucide="external-link" style="width:14px;height:14px;"></i>Voir le projet</a>
                @else
                <button type="button" @click="tab='projets'" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;"><i data-lucide="arrow-right" style="width:14px;height:14px;"></i>Voir le projet</button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p style="color:var(--muted);padding:8px 0;">Aucun projet pour le moment.</p>
    @endif
</div>
