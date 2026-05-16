<div class="card panel">
    <div class="panel-head"><div><h2>Documents</h2></div></div>

    @if($canManage)
    <details style="margin-bottom:18px;">
        <summary style="cursor:pointer;font-weight:800;color:var(--blue);">+ Ajouter un document</summary>
        <form method="POST" action="{{ route('member.work-groups.resources.store', $workGroup) }}" enctype="multipart/form-data" style="display:grid;gap:12px;margin-top:14px;max-width:560px;">
            @csrf
            <select name="category" required class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
                @foreach(config('work_group_resources.categories') as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <input type="text" name="title" placeholder="Titre" required class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">
            <textarea name="description" placeholder="Description (optionnel)" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;"></textarea>
            <input type="file" name="file" required>
            @error('file')<small style="color:#dc2626;">{{ $message }}</small>@enderror
            <button class="btn btn-primary" style="justify-self:start;"><i data-lucide="plus"></i>Ajouter</button>
        </form>
    </details>
    @endif

    @foreach(config('work_group_resources.categories') as $catKey => $catLabel)
        @php($items = $documents[$catKey] ?? collect())
        @if($items->count() > 0)
        <h3 style="font-size:15px;margin:18px 0 8px;">{{ $catLabel }}</h3>
        <div class="resource-list">
            @foreach($items as $r)
            @php($ext = strtoupper(pathinfo($r->file_path ?? '', PATHINFO_EXTENSION) ?: 'FICHIER'))
            @php($exists = $r->file_path && \Storage::disk('public')->exists($r->file_path))
            @php($size = $exists ? \Storage::disk('public')->size($r->file_path) : null)
            @php($sizeTxt = $size === null ? '—' : ($size >= 1048576 ? round($size/1048576,1).' Mo' : round($size/1024).' Ko'))
            <div class="resource-item">
                <div class="resource-item-icon" style="background:rgba(53,107,138,0.10);color:var(--blue);"><i data-lucide="file-text"></i></div>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                    <div>
                        <strong>{{ $r->title }}</strong>
                        @if($r->description)<small style="display:block;color:var(--muted);">{{ $r->description }}</small>@endif
                        <small style="color:var(--muted);">{{ $ext }} · {{ $sizeTxt }} · Modifié {{ $r->updated_at->diffForHumans() }}</small>
                    </div>
                    <span style="display:flex;gap:8px;white-space:nowrap;">
                        <a href="{{ $r->url() }}" target="_blank" rel="noopener" class="text-link"><i data-lucide="download"></i>Télécharger</a>
                        @if($canManage)
                        <form method="POST" action="{{ route('member.work-groups.resources.destroy', [$workGroup, $r]) }}" onsubmit="return confirm('Supprimer ce document ?');">@csrf @method('DELETE')
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

    @if($documents->flatten()->count() === 0)
        <p style="color:var(--muted);padding:16px 0;">Aucun document pour le moment.</p>
    @endif
</div>
