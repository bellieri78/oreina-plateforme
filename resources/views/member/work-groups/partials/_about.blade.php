<div class="card panel">
    <div class="panel-head"><div><h2>À propos de ce groupe</h2></div></div>
    <div style="white-space:pre-line;line-height:1.7;color:var(--text);">{{ $workGroup->description ?: 'Aucune description pour le moment.' }}</div>

    @if(count($workGroup->aboutPointsList()))
    <div style="display:flex;flex-direction:column;gap:8px;margin-top:16px;">
        @foreach($workGroup->aboutPointsList() as $point)
        <div style="display:flex;align-items:flex-start;gap:8px;">
            <i data-lucide="check-circle-2" style="width:16px;height:16px;color:#2f694e;flex-shrink:0;margin-top:2px;"></i>
            <span style="font-size:14px;color:var(--text);">{{ $point }}</span>
        </div>
        @endforeach
    </div>
    @endif

    <div class="panel-head" style="margin:22px 0 10px;"><div><h3 style="margin:0;font-size:16px;">Coordinateurs</h3></div>
        @if($canManage)<button type="button" @click="tab='manage'" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;">Gérer les rôles</button>@endif
    </div>
    @if($coordinators->count() === 0)
        <p style="color:var(--muted);font-size:14px;">Aucun coordinateur désigné.</p>
    @else
        <div style="display:flex;flex-direction:column;gap:12px;">
            @foreach($coordinators as $c)
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="reseau-avatar" style="margin:0;">
                    @if($c->photo_path)<img src="{{ \Storage::url($c->photo_path) }}" alt="">@else{{ strtoupper(substr($c->first_name ?? '?',0,1)) }}@endif
                </div>
                <span><strong style="display:block;font-size:14px;">{{ $c->full_name ?? $c->first_name }}</strong><small style="color:var(--muted);">Coordinateur</small></span>
            </div>
            @endforeach
        </div>
    @endif
</div>
