<div class="gt-banner-about">
    <h2 style="font-size:16px;margin:0 0 10px;color:var(--text);">À propos de ce groupe</h2>
    <p style="color:var(--muted);line-height:1.55;margin:0 0 16px;font-size:13.5px;">{{ \Illuminate\Support\Str::limit($workGroup->description ?: "Aucune description pour le moment.", 150) }}</p>

    <div style="display:flex;align-items:center;justify-content:space-between;margin:0 0 12px;">
        <h3 style="font-size:14px;margin:0;color:var(--text);">Coordinateurs</h3>
        <button type="button" @click="tab='apropos'" class="text-link" style="background:none;border:none;cursor:pointer;font-size:12px;">Voir tous</button>
    </div>
    @forelse($coordinators as $c)
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
        <div class="reseau-avatar" style="margin:0;width:34px;height:34px;">
            @if($c->photo_path)<img src="{{ \Storage::url($c->photo_path) }}" alt="">@else{{ strtoupper(substr($c->first_name ?? '?',0,1)) }}@endif
        </div>
        <strong style="flex:1;min-width:0;font-size:13.5px;">{{ $c->full_name ?? $c->first_name }}</strong>
        <span class="gt-coord-pill">Coordinateur</span>
    </div>
    @empty
    <p style="color:var(--muted);font-size:13.5px;">Aucun coordinateur désigné.</p>
    @endforelse
</div>
