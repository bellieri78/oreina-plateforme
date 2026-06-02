@php
    $canManage = $workGroup->isCoordinator($member);
@endphp
<div class="card panel" style="margin-top:18px;">
    <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center;">
        <div><h2>Prochaines réunions</h2></div>
        @if($canManage)
        <button type="button" class="btn btn-secondary" @click="planMeeting = !planMeeting">
            <i data-lucide="calendar-plus"></i><span x-text="planMeeting ? 'Fermer' : 'Planifier'"></span>
        </button>
        @endif
    </div>

    @if($canManage)
    <div x-show="planMeeting" x-cloak x-transition style="margin:6px 0 18px; padding:18px; border:1px solid var(--border); border-radius:14px; background:var(--surface-soft);">
        <form method="POST" action="{{ route('member.work-groups.events.store', $workGroup) }}" x-data="{ mode: 'online' }" style="display:grid; gap:14px; max-width:620px;">
            @csrf
            <div>
                <label class="wg-field-label">Titre de la réunion</label>
                <input type="text" name="title" required value="{{ old('title') }}" class="wg-field" placeholder="Ex : Point d'étape Atlas Grand Est">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div>
                    <label class="wg-field-label">Début</label>
                    <input type="datetime-local" name="start_date" required value="{{ old('start_date') }}" class="wg-field">
                </div>
                <div>
                    <label class="wg-field-label">Fin (optionnel)</label>
                    <input type="datetime-local" name="end_date" value="{{ old('end_date') }}" class="wg-field">
                </div>
            </div>
            <div>
                <label class="wg-field-label">Format</label>
                <select name="mode" x-model="mode" class="wg-field">
                    <option value="online">Visioconférence</option>
                    <option value="onsite">Présentiel</option>
                </select>
            </div>
            <div x-show="mode === 'online'">
                <label class="wg-field-label">Lien visio</label>
                <input type="url" name="meeting_url" class="wg-field" placeholder="https://..." value="{{ old('meeting_url') }}">
            </div>
            <template x-if="mode === 'onsite'">
                <div style="display:grid; gap:14px;">
                    <div>
                        <label class="wg-field-label">Lieu</label>
                        <input type="text" name="location_name" class="wg-field" placeholder="Nom du lieu" value="{{ old('location_name') }}">
                    </div>
                    <div>
                        <label class="wg-field-label">Ville</label>
                        <input type="text" name="location_city" class="wg-field" placeholder="Ville" value="{{ old('location_city') }}">
                    </div>
                </div>
            </template>
            <div>
                <label class="wg-field-label">Description (optionnel)</label>
                <textarea name="description" rows="2" class="wg-field">{{ old('description') }}</textarea>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary"><i data-lucide="check"></i>Enregistrer la réunion</button>
                <button type="button" class="btn btn-secondary" @click="planMeeting = false">Annuler</button>
            </div>
        </form>
    </div>
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
