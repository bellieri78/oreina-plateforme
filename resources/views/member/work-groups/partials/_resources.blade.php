<div class="card panel">
    <div class="panel-head">
        <div><h2>Ressources</h2></div>
    </div>

    @if($canManage)
    <details style="margin-bottom:18px;">
        <summary style="cursor:pointer;font-weight:800;color:var(--blue);">+ Ajouter une ressource</summary>
        <form method="POST" action="{{ route('member.work-groups.resources.store', $workGroup) }}" enctype="multipart/form-data" style="display:grid;gap:12px;margin-top:14px;max-width:560px;">
            @csrf
            <select name="category" required class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
                @foreach(config('work_group_resources.categories') as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <input type="text" name="title" placeholder="Titre" required class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
            <textarea name="description" placeholder="Description (optionnel)" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;"></textarea>
            <input type="file" name="file">
            <input type="url" name="external_url" placeholder="… ou un lien externe (https://)" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
            @error('file')<small style="color:#dc2626;">{{ $message }}</small>@enderror
            @error('external_url')<small style="color:#dc2626;">{{ $message }}</small>@enderror
            <button class="btn btn-primary" style="justify-self:start;"><i data-lucide="plus"></i>Ajouter</button>
        </form>
    </details>
    @endif

    @foreach(config('work_group_resources.categories') as $catKey => $catLabel)
        @php($items = $resources[$catKey] ?? collect())
        @if($items->count() > 0)
        <h3 style="font-size:15px;margin:18px 0 8px;">{{ $catLabel }}</h3>
        <div class="resource-list">
            @foreach($items as $r)
            <div class="resource-item">
                <div class="resource-item-icon" style="background:rgba(53,107,138,0.10);color:var(--blue);">
                    <i data-lucide="{{ $r->isFile() ? 'file-text' : 'external-link' }}"></i>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                    <div>
                        <strong>{{ $r->title }}</strong>
                        @if($r->description)<small style="display:block;color:var(--muted);">{{ $r->description }}</small>@endif
                        <small style="color:var(--muted);">Ajouté par {{ $r->addedBy?->first_name ?? 'OREINA' }} le {{ $r->created_at->format('d/m/Y') }}</small>
                    </div>
                    <span style="display:flex;gap:8px;white-space:nowrap;">
                        <a href="{{ $r->url() }}" target="_blank" rel="noopener" class="text-link"><i data-lucide="{{ $r->isFile() ? 'download' : 'arrow-up-right' }}"></i>{{ $r->isFile() ? 'Télécharger' : 'Ouvrir' }}</a>
                        @if($canManage)
                        <form method="POST" action="{{ route('member.work-groups.resources.destroy', [$workGroup, $r]) }}" onsubmit="return confirm('Supprimer cette ressource ?');">@csrf @method('DELETE')
                            <button class="text-link" style="background:none;border:none;cursor:pointer;color:#dc2626;"><i data-lucide="trash-2"></i></button>
                        </form>
                        @endif
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    @endforeach

    @if($resources->flatten()->count() === 0)
        <p style="color:var(--muted);padding:16px 0;">Aucune ressource pour le moment.</p>
    @endif
</div>
