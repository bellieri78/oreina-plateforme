<div class="card panel">
    <div class="panel-head">
        <div><h2>Membres ({{ $workGroup->active_members_count }})</h2></div>
        <button type="button" @click="membersOpen=true" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;">Voir tous</button>
    </div>

    @if($coordinators->count())
    <h3 style="font-size:13px;margin:0 0 10px;color:var(--muted);text-transform:uppercase;letter-spacing:.03em;">Coordinateurs</h3>
    @foreach($coordinators as $c)
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
        <div class="reseau-avatar" style="margin:0;">@if($c->photo_path)<img src="{{ \Storage::url($c->photo_path) }}" alt="">@else{{ strtoupper(substr($c->first_name ?? '?',0,1)) }}@endif</div>
        <strong style="font-size:14px;">{{ $c->full_name ?? $c->first_name }}</strong>
    </div>
    @endforeach
    @endif

    <h3 style="font-size:13px;margin:16px 0 10px;color:var(--muted);text-transform:uppercase;letter-spacing:.03em;">Membres actifs</h3>
    <div class="gt-avatar-row">
        @foreach($members->take(6) as $m)
        <div class="reseau-avatar" style="margin:0;" title="{{ $m->full_name ?? $m->first_name }}">@if($m->photo_path)<img src="{{ \Storage::url($m->photo_path) }}" alt="">@else{{ strtoupper(substr($m->first_name ?? '?',0,1)) }}@endif</div>
        @endforeach
        @if($members->count() > 6)
        <div class="gt-avatar-more">+{{ $members->count() - 6 }}</div>
        @endif
    </div>

    @if($latestMembers->count())
    <h3 style="font-size:13px;margin:16px 0 10px;color:var(--muted);text-transform:uppercase;letter-spacing:.03em;">Derniers membres</h3>
    @foreach($latestMembers as $m)
    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:6px 0;border-bottom:1px solid var(--border);">
        <span style="display:flex;align-items:center;gap:10px;">
            <div class="reseau-avatar" style="margin:0;width:32px;height:32px;">@if($m->photo_path)<img src="{{ \Storage::url($m->photo_path) }}" alt="">@else{{ strtoupper(substr($m->first_name ?? '?',0,1)) }}@endif</div>
            <strong style="font-size:14px;">{{ $m->full_name ?? $m->first_name }}</strong>
        </span>
        <small style="color:var(--muted);white-space:nowrap;">{{ \Illuminate\Support\Carbon::parse($m->pivot->joined_at)->diffForHumans() }}</small>
    </div>
    @endforeach
    @endif

    <button type="button" @click="membersOpen=true" class="btn btn-secondary" style="margin-top:14px;"><i data-lucide="users"></i>Voir tous les membres</button>
</div>
