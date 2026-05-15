<div class="card panel">
    <div class="panel-head"><div><h2>Gérer le groupe</h2></div></div>

    @if($workGroup->join_policy === 'request' && $pending->count() > 0)
    <h3 style="font-size:15px;margin:8px 0;">Demandes en attente ({{ $pending->count() }})</h3>
    <div class="resource-list" style="margin-bottom:22px;">
        @foreach($pending as $p)
        <div class="resource-item" style="grid-template-columns:1fr;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <span>{{ $p->full_name ?? $p->first_name }} <small style="color:var(--muted);">— {{ optional($p->pivot->requested_at)->format('d/m/Y') }}</small></span>
                <span style="display:flex;gap:8px;">
                    <form method="POST" action="{{ route('member.work-groups.requests.approve', [$workGroup, $p]) }}">@csrf
                        <button class="btn btn-primary" style="height:32px;padding:0 12px;font-size:12px;">Accepter</button>
                    </form>
                    <form method="POST" action="{{ route('member.work-groups.requests.reject', [$workGroup, $p]) }}">@csrf @method('DELETE')
                        <button class="btn btn-secondary" style="height:32px;padding:0 12px;font-size:12px;color:#dc2626;">Refuser</button>
                    </form>
                </span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <h3 style="font-size:15px;margin:8px 0;">Membres ({{ $activeMembers->count() }})</h3>
    <div class="resource-list">
        @foreach($activeMembers as $m)
        <div class="resource-item" style="grid-template-columns:1fr;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <span>{{ $m->full_name ?? $m->first_name }}
                    @if(($m->pivot->role ?? '') === 'coordinator')<span class="badge sage" style="margin-left:6px;">Coordinateur</span>@endif
                </span>
                <form method="POST" action="{{ route('member.work-groups.members.remove', [$workGroup, $m]) }}" onsubmit="return confirm('Retirer ce membre ?');">@csrf @method('DELETE')
                    <button class="text-link" style="background:none;border:none;cursor:pointer;color:#dc2626;"><i data-lucide="user-minus"></i>Retirer</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <details style="margin-top:18px;">
        <summary style="cursor:pointer;font-weight:800;color:var(--blue);">+ Ajouter un membre</summary>
        <form method="POST" action="{{ route('member.work-groups.members.add', $workGroup) }}" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
            @csrf
            <input type="number" name="member_id" placeholder="ID adhérent" required class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;max-width:160px;">
            <select name="role" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
                <option value="member">Membre</option>
                <option value="coordinator">Coordinateur</option>
            </select>
            <button class="btn btn-primary"><i data-lucide="user-plus"></i>Ajouter</button>
        </form>
        <small style="color:var(--muted);display:block;margin-top:6px;">Astuce : l'ID adhérent est visible dans l'annuaire / l'extranet.</small>
    </details>
</div>
