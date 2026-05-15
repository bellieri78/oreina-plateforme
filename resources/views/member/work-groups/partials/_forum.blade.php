<div class="card panel">
    <div class="panel-head"><div><h2>Discussions</h2></div></div>

    @if($canManage)
    <details style="margin-bottom:18px;">
        <summary style="cursor:pointer;font-weight:800;color:var(--blue);">+ Nouvelle catégorie</summary>
        <form method="POST" action="{{ route('member.work-groups.forum.categories.store', $workGroup) }}" style="display:grid;gap:10px;margin-top:12px;max-width:480px;">
            @csrf
            <input type="text" name="name" placeholder="Nom de la catégorie" required class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
            <input type="text" name="description" placeholder="Description (optionnel)" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
            <input type="number" name="position" placeholder="Ordre (0)" min="0" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;max-width:140px;">
            <button class="btn btn-primary" style="justify-self:start;"><i data-lucide="plus"></i>Créer</button>
        </form>
    </details>
    @endif

    @if(! $canParticipate && $status !== 'active')
    <div style="background:var(--surface-soft,#f5f5f5);border-radius:12px;padding:14px;margin-bottom:18px;font-size:14px;">
        <strong>Mode aperçu.</strong> Rejoignez ce groupe pour participer aux discussions.
        <form method="POST" action="{{ route('member.work-groups.join', $workGroup) }}" style="display:inline;margin-left:8px;">@csrf
            <button class="btn btn-primary" style="height:32px;padding:0 12px;font-size:12px;">
                <i data-lucide="{{ $workGroup->join_policy === 'open' ? 'plus' : 'send' }}"></i>{{ $workGroup->join_policy === 'open' ? 'Rejoindre' : 'Demander à rejoindre' }}
            </button>
        </form>
    </div>
    @endif

    @forelse($forumCategories as $cat)
    <div style="margin-bottom:22px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
            <div>
                <h3 style="font-size:16px;margin:0;">{{ $cat->name }}</h3>
                @if($cat->description)<small style="color:var(--muted);">{{ $cat->description }}</small>@endif
            </div>
            @if($canManage)
            <span style="display:flex;gap:8px;">
                <details>
                    <summary style="cursor:pointer;color:var(--blue);font-size:12px;font-weight:800;">Éditer</summary>
                    <form method="POST" action="{{ route('member.work-groups.forum.categories.update', [$workGroup, $cat]) }}" style="display:grid;gap:8px;margin-top:8px;">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $cat->name }}" required class="form-input" style="padding:8px;border:1px solid var(--border);border-radius:8px;">
                        <input type="text" name="description" value="{{ $cat->description }}" class="form-input" style="padding:8px;border:1px solid var(--border);border-radius:8px;">
                        <input type="number" name="position" value="{{ $cat->position }}" min="0" class="form-input" style="padding:8px;border:1px solid var(--border);border-radius:8px;max-width:120px;">
                        <button class="btn btn-secondary" style="height:30px;padding:0 10px;font-size:12px;justify-self:start;">Enregistrer</button>
                    </form>
                </details>
                <form method="POST" action="{{ route('member.work-groups.forum.categories.destroy', [$workGroup, $cat]) }}" onsubmit="return confirm('Supprimer cette catégorie et tous ses fils ?');">@csrf @method('DELETE')
                    <button class="text-link" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:12px;"><i data-lucide="trash-2"></i></button>
                </form>
            </span>
            @endif
        </div>

        <div class="resource-list" style="margin-top:10px;">
            @forelse($cat->threads as $thread)
            <div class="resource-item" style="grid-template-columns:1fr;">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <div>
                        <a href="{{ route('member.work-groups.forum.threads.show', [$workGroup, $thread]) }}" class="text-link">
                            @if($thread->is_pinned)<i data-lucide="pin" style="width:14px;height:14px;"></i>@endif
                            <strong>{{ $thread->title }}</strong>
                        </a>
                        @if($thread->is_locked)<span class="badge gold" style="margin-left:6px;">Verrouillé</span>@endif
                        <small style="display:block;color:var(--muted);">
                            par {{ $thread->author?->full_name ?? $thread->author?->first_name ?? 'Membre supprimé' }}
                            · {{ $thread->posts_count }} message(s)
                            @if($thread->last_posted_at)· dernier {{ $thread->last_posted_at->diffForHumans() }}@endif
                        </small>
                    </div>
                </div>
            </div>
            @empty
            <p style="color:var(--muted);font-size:14px;padding:8px 0;">Aucun fil dans cette catégorie.</p>
            @endforelse
        </div>
    </div>
    @empty
    <p style="color:var(--muted);padding:12px 0;">
        Le forum n'a pas encore de catégorie.@if($canManage) Créez-en une ci-dessus.@endif
    </p>
    @endforelse

    @if($canParticipate && $forumCategories->count() > 0)
    <details style="margin-top:12px;">
        <summary style="cursor:pointer;font-weight:800;color:var(--blue);">+ Nouveau fil</summary>
        <form method="POST" action="{{ route('member.work-groups.forum.threads.store', $workGroup) }}" style="display:grid;gap:10px;margin-top:12px;max-width:560px;">
            @csrf
            <select name="work_group_forum_category_id" required class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
                @foreach($forumCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <input type="text" name="title" placeholder="Titre du fil" required class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
            <textarea name="content" placeholder="Votre message…" required rows="4" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;"></textarea>
            <button class="btn btn-primary" style="justify-self:start;"><i data-lucide="message-square-plus"></i>Publier le fil</button>
        </form>
    </details>
    @endif
</div>
