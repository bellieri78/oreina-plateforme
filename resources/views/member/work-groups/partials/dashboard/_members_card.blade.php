<div class="card panel">
    <div class="panel-head">
        <div><h2>Membres ({{ $workGroup->active_members_count }})</h2></div>
        <button type="button" @click="membersOpen=true" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;"><i data-lucide="arrow-right"></i>Voir tous</button>
    </div>

    @if($coordinators->count())
    <h3 style="font-size:13px;margin:0 0 10px;color:var(--muted);">Coordinateurs</h3>
    <div class="gt-coord-grid">
        @foreach($coordinators as $c)
        <div style="display:flex;align-items:center;gap:9px;min-width:0;">
            <div class="reseau-avatar" style="margin:0;width:34px;height:34px;flex-shrink:0;">@if($c->photo_path)<img src="{{ \Storage::url($c->photo_path) }}" alt="">@else{{ strtoupper(substr($c->first_name ?? '?',0,1)) }}@endif</div>
            <strong style="font-size:13px;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $c->full_name ?? $c->first_name }}</strong>
        </div>
        @endforeach
    </div>
    @endif

    <h3 style="font-size:13px;margin:16px 0 10px;color:var(--muted);">Membres actifs</h3>
    <div class="gt-avatar-row">
        @foreach($members->take(5) as $m)
        <div class="reseau-avatar" style="margin:0;" title="{{ $m->full_name ?? $m->first_name }}">@if($m->photo_path)<img src="{{ \Storage::url($m->photo_path) }}" alt="">@else{{ strtoupper(substr($m->first_name ?? '?',0,1)) }}@endif</div>
        @endforeach
        @if($members->count() > 5)
        <div class="gt-avatar-more">+{{ $members->count() - 5 }}</div>
        @endif
    </div>

    @if($latestMembers->count())
    <h3 style="font-size:13px;margin:16px 0 10px;color:var(--muted);">Derniers membres</h3>
    @foreach($latestMembers as $m)
    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:6px 0;">
        <span style="display:flex;align-items:center;gap:10px;min-width:0;">
            <div class="reseau-avatar" style="margin:0;width:30px;height:30px;flex-shrink:0;">@if($m->photo_path)<img src="{{ \Storage::url($m->photo_path) }}" alt="">@else{{ strtoupper(substr($m->first_name ?? '?',0,1)) }}@endif</div>
            <strong style="font-size:13.5px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $m->full_name ?? $m->first_name }}</strong>
        </span>
        <small style="color:var(--muted);white-space:nowrap;">{{ \Illuminate\Support\Carbon::parse($m->pivot->joined_at)->diffForHumans() }}</small>
    </div>
    @endforeach
    @endif

    <button type="button" @click="membersOpen=true" class="gt-fullbtn">Voir tous les membres</button>
</div>
