@php
    $canManage = $workGroup->isCoordinator($member);
@endphp
<div class="card panel" style="margin-top:18px;">
    <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center;">
        <div><h2>Prochaines réunions</h2></div>
        @if($canManage)
        <button type="button" class="btn btn-secondary"
            onclick="var f=document.getElementById('wg-event-form');f.style.display=f.style.display==='block'?'none':'block';">
            <i data-lucide="calendar-plus"></i>Planifier
        </button>
        @endif
    </div>

    @if($canManage)
    <form id="wg-event-form" method="POST" action="{{ route('member.work-groups.events.store', $workGroup) }}"
          style="display:none; margin:12px 0; padding:14px; border:1px solid var(--border); border-radius:14px;"
          x-data="{ mode: 'online' }">
        @csrf
        <div class="form-group">
            <label>Titre</label>
            <input type="text" name="title" required class="form-input" value="{{ old('title') }}">
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div class="form-group">
                <label>Début</label>
                <input type="datetime-local" name="start_date" required class="form-input" value="{{ old('start_date') }}">
            </div>
            <div class="form-group">
                <label>Fin (optionnel)</label>
                <input type="datetime-local" name="end_date" class="form-input" value="{{ old('end_date') }}">
            </div>
        </div>
        <div class="form-group">
            <label>Mode</label>
            <select name="mode" x-model="mode" class="form-input">
                <option value="online">Visio</option>
                <option value="onsite">Présentiel</option>
            </select>
        </div>
        <div class="form-group" x-show="mode === 'online'">
            <label>Lien visio</label>
            <input type="url" name="meeting_url" class="form-input" placeholder="https://..." value="{{ old('meeting_url') }}">
        </div>
        <template x-if="mode === 'onsite'">
            <div>
                <div class="form-group"><label>Lieu</label><input type="text" name="location_name" class="form-input" value="{{ old('location_name') }}"></div>
                <div class="form-group"><label>Ville</label><input type="text" name="location_city" class="form-input" value="{{ old('location_city') }}"></div>
            </div>
        </template>
        <div class="form-group">
            <label>Description (optionnel)</label>
            <textarea name="description" rows="2" class="form-input">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary"><i data-lucide="check"></i>Enregistrer</button>
    </form>
    @endif

    @forelse($upcomingGroupEvents as $ev)
        <div class="agenda-item" style="grid-template-columns:56px 1fr auto; padding:10px 0; border-bottom:1px solid var(--border);">
            <div class="agenda-date">
                <small>{{ $ev->start_date->translatedFormat('M') }}</small>
                <strong>{{ $ev->start_date->format('d') }}</strong>
            </div>
            <div class="agenda-item-body">
                <strong>{{ $ev->title }}</strong>
                <small>
                    {{ $ev->start_date->format('H\hi') }}
                    @if($ev->meeting_url) · <a href="{{ $ev->meeting_url }}" target="_blank" rel="noopener">Lien visio</a>
                    @elseif($ev->location_city) · {{ $ev->location_city }}@endif
                </small>
            </div>
            @if($canManage)
            <form method="POST" action="{{ route('member.work-groups.events.destroy', [$workGroup, $ev]) }}"
                  onsubmit="return confirm('Supprimer cette réunion ?');">
                @csrf @method('DELETE')
                <button type="submit"
                    style="background:none;border:1px solid var(--border);border-radius:8px;padding:6px 8px;cursor:pointer;color:var(--muted);display:inline-flex;align-items:center;"
                    title="Supprimer"><i data-lucide="trash-2"></i></button>
            </form>
            @endif
        </div>
    @empty
        <p style="color:var(--muted); padding:10px 0;">Aucune réunion programmée.</p>
    @endforelse
</div>
