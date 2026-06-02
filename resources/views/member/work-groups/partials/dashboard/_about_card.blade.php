<div class="card panel">
    <div class="panel-head">
        <div><h2>À propos de ce groupe</h2></div>
        <button type="button" @click="tab='apropos'" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;">Voir tous</button>
    </div>
    <p style="color:var(--muted);line-height:1.6;margin:0 0 16px;">{{ \Illuminate\Support\Str::limit($workGroup->description ?: "Aucune description pour le moment.", 180) }}</p>

    <h3 style="font-size:14px;margin:0 0 10px;color:var(--text);">Coordinateurs</h3>
    @forelse($coordinators as $c)
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
        <div class="reseau-avatar" style="margin:0;">
            @if($c->photo_path)<img src="{{ \Storage::url($c->photo_path) }}" alt="">@else{{ strtoupper(substr($c->first_name ?? '?',0,1)) }}@endif
        </div>
        <span><strong style="display:block;font-size:14px;">{{ $c->full_name ?? $c->first_name }}</strong><small style="color:var(--muted);">Coordinateur</small></span>
    </div>
    @empty
    <p style="color:var(--muted);font-size:14px;">Aucun coordinateur désigné.</p>
    @endforelse
</div>
