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

    <details style="margin-top:18px;" x-data="{ q:'', results:[], picked:null, async search(){ if(this.q.length<2){this.results=[];return;} let r=await fetch('{{ route('member.work-groups.members.search', $workGroup) }}?q='+encodeURIComponent(this.q)); this.results=await r.json(); }, choose(m){ this.picked=m; this.results=[]; this.q=m.label; } }">
        <summary style="cursor:pointer;font-weight:800;color:var(--blue);">+ Ajouter un membre</summary>
        <form method="POST" action="{{ route('member.work-groups.members.add', $workGroup) }}" style="margin-top:12px;">
            @csrf
            <div style="position:relative;max-width:360px;">
                <input type="text" x-model="q" @input.debounce.300ms="search()" placeholder="Rechercher un adhérent (nom, email)…" autocomplete="off" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;width:100%;">
                <input type="hidden" name="member_id" :value="picked ? picked.id : ''" required>
                <div x-show="results.length" x-cloak style="position:absolute;z-index:10;background:white;border:1px solid var(--border);border-radius:10px;width:100%;margin-top:4px;box-shadow:0 8px 20px rgba(0,0,0,0.08);max-height:240px;overflow:auto;">
                    <template x-for="m in results" :key="m.id">
                        <div @click="choose(m)" style="padding:10px;cursor:pointer;font-size:14px;" x-text="m.label"></div>
                    </template>
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
                <select name="role" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
                    <option value="member">Membre</option>
                    <option value="coordinator">Coordinateur</option>
                </select>
                <button class="btn btn-primary" :disabled="!picked"><i data-lucide="user-plus"></i>Ajouter</button>
            </div>
        </form>
    </details>

    <h3 style="font-size:15px;margin:22px 0 8px;">Projets ({{ $projects->count() }})</h3>
    <div class="resource-list">
        @forelse($projects as $project)
        <div class="resource-item" style="grid-template-columns:1fr;" x-data="{ editing:false }">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                <span><strong>{{ $project->title }}</strong> <span class="badge" style="margin-left:6px;">{{ $project->statusLabel() }}</span></span>
                <span style="display:flex;gap:8px;">
                    <button type="button" @click="editing=!editing" class="text-link" style="background:none;border:none;cursor:pointer;font-size:12px;"><i data-lucide="pencil"></i></button>
                    <form method="POST" action="{{ route('member.work-groups.projects.destroy', [$workGroup, $project]) }}" onsubmit="return confirm('Supprimer ce projet ?');">@csrf @method('DELETE')
                        <button class="text-link" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:12px;"><i data-lucide="trash-2"></i></button>
                    </form>
                </span>
            </div>
            <form method="POST" action="{{ route('member.work-groups.projects.update', [$workGroup, $project]) }}" x-show="editing" x-cloak style="display:grid;gap:8px;margin-top:10px;">
                @csrf @method('PUT')
                <input type="text" name="title" value="{{ $project->title }}" required class="form-input" style="padding:8px;border:1px solid var(--border);border-radius:8px;">
                <textarea name="description" class="form-input" style="padding:8px;border:1px solid var(--border);border-radius:8px;">{{ $project->description }}</textarea>
                <select name="status" class="form-input" style="padding:8px;border:1px solid var(--border);border-radius:8px;">
                    @foreach(config('work_group_projects.statuses') as $key => $label)
                        <option value="{{ $key }}" {{ $project->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="url" name="deliverable_url" value="{{ $project->deliverable_url }}" placeholder="Lien vers l'œuvre diffusée (optionnel)" class="form-input" style="padding:8px;border:1px solid var(--border);border-radius:8px;">
                <button class="btn btn-secondary" style="height:30px;padding:0 10px;font-size:12px;justify-self:start;">Enregistrer</button>
            </form>
        </div>
        @empty
        <p style="color:var(--muted);font-size:14px;padding:8px 0;">Aucun projet.</p>
        @endforelse
    </div>

    <details style="margin-top:14px;">
        <summary style="cursor:pointer;font-weight:800;color:var(--blue);">+ Nouveau projet</summary>
        <form method="POST" action="{{ route('member.work-groups.projects.store', $workGroup) }}" style="display:grid;gap:10px;margin-top:12px;max-width:560px;">
            @csrf
            <input type="text" name="title" placeholder="Titre du projet" required class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
            <textarea name="description" placeholder="Description (optionnel)" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;"></textarea>
            <select name="status" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
                @foreach(config('work_group_projects.statuses') as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <input type="url" name="deliverable_url" placeholder="Lien vers l'œuvre diffusée (optionnel)" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
            <button class="btn btn-primary" style="justify-self:start;"><i data-lucide="plus"></i>Créer le projet</button>
        </form>
    </details>
</div>
