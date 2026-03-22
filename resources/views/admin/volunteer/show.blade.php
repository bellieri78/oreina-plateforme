@extends('layouts.admin')

@section('title', $activity->title)

@section('breadcrumb')
    <a href="{{ route('admin.volunteer.index') }}">Benevolat</a>
    <span>/</span>
    <a href="{{ route('admin.volunteer.activities') }}">Activites</a>
    <span>/</span>
    <span>{{ $activity->title }}</span>
@endsection

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        {{-- Activity details --}}
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Details de l'activite</h3>
                    <a href="{{ route('admin.volunteer.edit', $activity) }}" class="btn btn-secondary btn-sm">Modifier</a>
                </div>
                <div class="card-body">
                    <dl style="display: grid; gap: 0.75rem;">
                        <div>
                            <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Titre</dt>
                            <dd style="font-weight: 500;">{{ $activity->title }}</dd>
                        </div>
                        <div>
                            <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Type</dt>
                            <dd>
                                <span style="display: inline-flex; align-items: center; gap: 4px;">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $activity->activityType?->color ?? '#ccc' }};"></span>
                                    {{ $activity->activityType?->name ?? '-' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Date</dt>
                            <dd>
                                {{ $activity->activity_date->format('d/m/Y') }}
                                @if($activity->start_time)
                                    de {{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }}
                                @endif
                                @if($activity->end_time)
                                    a {{ \Carbon\Carbon::parse($activity->end_time)->format('H:i') }}
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Statut</dt>
                            <dd>
                                <span class="badge badge-{{ $activity->status_color }}">{{ $activity->status_label }}</span>
                            </dd>
                        </div>
                        @if($activity->location || $activity->city)
                            <div>
                                <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Lieu</dt>
                                <dd>
                                    {{ $activity->location }}
                                    @if($activity->city)
                                        <span style="color: #6b7280;">({{ $activity->city }})</span>
                                    @endif
                                </dd>
                            </div>
                        @endif
                        @if($activity->structure)
                            <div>
                                <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Structure</dt>
                                <dd>
                                    <a href="{{ route('admin.structures.show', $activity->structure) }}">{{ $activity->structure->name }}</a>
                                </dd>
                            </div>
                        @endif
                        @if($activity->organizer)
                            <div>
                                <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Organisateur</dt>
                                <dd>
                                    <a href="{{ route('admin.members.show', $activity->organizer) }}">{{ $activity->organizer->full_name }}</a>
                                </dd>
                            </div>
                        @endif
                        @if($activity->description)
                            <div>
                                <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Description</dt>
                                <dd>{{ $activity->description }}</dd>
                            </div>
                        @endif
                        @if($activity->notes)
                            <div>
                                <dt style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Notes internes</dt>
                                <dd style="font-style: italic; color: #6b7280;">{{ $activity->notes }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Stats --}}
            <div class="card" style="margin-top: 1rem;">
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: center;">
                        <div>
                            <div style="font-size: 1.5rem; font-weight: 600; color: #2C5F2D;">{{ $activity->participants_count }}</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Inscrits</div>
                        </div>
                        <div>
                            <div style="font-size: 1.5rem; font-weight: 600; color: #2C5F2D;">{{ $activity->attended_participants_count }}</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Presents</div>
                        </div>
                        @if($activity->max_participants)
                            <div>
                                <div style="font-size: 1.5rem; font-weight: 600;">{{ $activity->max_participants }}</div>
                                <div style="font-size: 0.75rem; color: #6b7280;">Places max</div>
                            </div>
                            <div>
                                <div style="font-size: 1.5rem; font-weight: 600;">{{ max(0, $activity->max_participants - $activity->participants_count) }}</div>
                                <div style="font-size: 0.75rem; color: #6b7280;">Places dispo</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Participants --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Participants ({{ $activity->participants_count }})</h3>
                <div style="display: flex; gap: 0.5rem;">
                    @if($activity->status !== 'cancelled')
                        <form action="{{ route('admin.volunteer.mark-attended', $activity) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">Marquer tous presents</button>
                        </form>
                        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('add-participant-modal').showModal()">
                            Ajouter un participant
                        </button>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Benevole</th>
                            <th>Statut</th>
                            <th>Heures</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activity->participations as $participation)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.members.show', $participation->member) }}">{{ $participation->member->full_name }}</a>
                                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $participation->member->email }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ ['registered' => 'info', 'confirmed' => 'primary', 'attended' => 'success', 'absent' => 'danger', 'cancelled' => 'secondary'][$participation->status] ?? 'secondary' }}">
                                        {{ ['registered' => 'Inscrit', 'confirmed' => 'Confirme', 'attended' => 'Present', 'absent' => 'Absent', 'cancelled' => 'Annule'][$participation->status] ?? $participation->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($participation->hours_worked)
                                        {{ number_format($participation->hours_worked, 1) }}h
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size: 0.875rem; color: #6b7280;">{{ $participation->notes ?? '-' }}</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button type="button" class="btn btn-sm btn-secondary"
                                                onclick="openEditModal({{ json_encode($participation) }})">
                                            Modifier
                                        </button>
                                        <form action="{{ route('admin.volunteer.participants.remove', [$activity, $participation->member]) }}" method="POST"
                                              onsubmit="return confirm('Retirer ce participant ?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Retirer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: #6b7280;">Aucun participant inscrit.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add participant modal --}}
    <dialog id="add-participant-modal" style="padding: 0; border: none; border-radius: 8px; max-width: 500px; width: 100%;">
        <div class="card" style="margin: 0;">
            <form action="{{ route('admin.volunteer.participants.add', $activity) }}" method="POST">
                @csrf
                <div class="card-header">
                    <h3 class="card-title">Ajouter un participant</h3>
                    <button type="button" onclick="this.closest('dialog').close()" style="background: none; border: none; cursor: pointer; font-size: 1.25rem;">&times;</button>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="member_id" class="form-label">Membre *</label>
                        <select name="member_id" id="member_id" class="form-input" required>
                            <option value="">-- Selectionner --</option>
                            @foreach($availableMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add_status" class="form-label">Statut</label>
                        <select name="status" id="add_status" class="form-input">
                            <option value="registered">Inscrit</option>
                            <option value="confirmed">Confirme</option>
                            <option value="attended">Present</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add_hours_worked" class="form-label">Heures</label>
                        <input type="number" name="hours_worked" id="add_hours_worked" step="0.5" min="0" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="add_notes" class="form-label">Notes</label>
                        <textarea name="notes" id="add_notes" rows="2" class="form-input"></textarea>
                    </div>
                </div>
                <div class="card-footer" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <button type="button" onclick="this.closest('dialog').close()" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- Edit participant modal --}}
    <dialog id="edit-participant-modal" style="padding: 0; border: none; border-radius: 8px; max-width: 500px; width: 100%;">
        <div class="card" style="margin: 0;">
            <form id="edit-participant-form" method="POST">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <h3 class="card-title">Modifier la participation</h3>
                    <button type="button" onclick="this.closest('dialog').close()" style="background: none; border: none; cursor: pointer; font-size: 1.25rem;">&times;</button>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="edit_status" class="form-label">Statut</label>
                        <select name="status" id="edit_status" class="form-input">
                            <option value="registered">Inscrit</option>
                            <option value="confirmed">Confirme</option>
                            <option value="attended">Present</option>
                            <option value="absent">Absent</option>
                            <option value="cancelled">Annule</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_hours_worked" class="form-label">Heures</label>
                        <input type="number" name="hours_worked" id="edit_hours_worked" step="0.5" min="0" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea name="notes" id="edit_notes" rows="2" class="form-input"></textarea>
                    </div>
                </div>
                <div class="card-footer" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <button type="button" onclick="this.closest('dialog').close()" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        function openEditModal(participation) {
            const form = document.getElementById('edit-participant-form');
            form.action = '{{ route('admin.volunteer.participants.update', [$activity, '']) }}/' + participation.member_id;
            document.getElementById('edit_status').value = participation.status;
            document.getElementById('edit_hours_worked').value = participation.hours_worked || '';
            document.getElementById('edit_notes').value = participation.notes || '';
            document.getElementById('edit-participant-modal').showModal();
        }
    </script>
@endsection
