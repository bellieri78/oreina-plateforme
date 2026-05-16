<div class="card panel">
    <div class="panel-head"><div><h2>Projets en cours</h2></div></div>

    @forelse($projects as $project)
    @php
        $badge = match($project->status) {
            'a_lancer' => 'gold',
            'en_cours' => 'sage',
            'diffuse'  => 'blue',
            default    => '',
        };
    @endphp
    <div style="padding:10px 0;border-bottom:1px solid var(--border);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
            <strong>{{ $project->title }}</strong>
            <span class="badge {{ $badge }}" style="white-space:nowrap;">{{ $project->statusLabel() }}</span>
        </div>
        @if($project->description)
        <p style="margin:6px 0 0;color:var(--muted);font-size:14px;">{{ $project->description }}</p>
        @endif
        @if($project->deliverable_url)
        <a href="{{ $project->deliverable_url }}" target="_blank" rel="noopener" class="text-link" style="display:inline-flex;margin-top:6px;">
            <i data-lucide="external-link" style="width:14px;height:14px;"></i>Voir l'oeuvre diffusee
        </a>
        @endif
    </div>
    @empty
    <p style="color:var(--muted);padding:8px 0;">Aucun projet pour le moment.</p>
    @endforelse
</div>
