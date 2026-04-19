@extends('layouts.admin')

@section('title', 'File d\'attente éditoriale')

@section('breadcrumb')<span>Revue</span><span>/</span><span>File d'attente</span>@endsection

@section('content')
    <div class="page-header">
        <h1>File d'attente éditoriale</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom:1rem;">{{ session('error') }}</div>
    @endif

    @if($submissions->isEmpty())
        <div class="card">
            <div class="card-body" style="text-align:center;padding:2rem;color:#6b7280;">
                Aucun article en attente d'éditeur.
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Soumis le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($submissions as $s)
                        @php
                            $reviewerIds = $s->reviews->pluck('reviewer_id')->all();
                        @endphp
                        <tr>
                            <td title="{{ Str::limit($s->abstract, 300) }}">
                                <strong>{{ $s->title }}</strong>
                            </td>
                            <td>{{ $s->author?->name ?? '—' }}</td>
                            <td>{{ optional($s->submitted_at)->format('d/m/Y') }}</td>
                            <td>
                                <div style="display:flex;flex-direction:column;gap:6px;">
                                    @can('takeEditor', $s)
                                        <form method="POST" action="{{ route('admin.journal.queue.take', $s) }}" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                Prendre en charge
                                            </button>
                                        </form>
                                    @endcan

                                    @can('assignEditor', $s)
                                        @php
                                            $queueConflictIds = collect($eligibleEditors)
                                                ->filter(fn($e) => in_array($e->id, $reviewerIds))
                                                ->pluck('id')->values()->all();
                                        @endphp
                                        <form method="POST" action="{{ route('admin.journal.queue.assign', $s) }}"
                                              style="margin:0;display:flex;align-items:center;gap:6px;"
                                              x-data="{
                                                  conflictIds: @js($queueConflictIds),
                                                  reason: '',
                                                  showModal: false,
                                                  selectedName: '',
                                              }"
                                              @submit="
                                                  const sel = $event.target.querySelector('select[name=user_id]');
                                                  const selectedId = parseInt(sel.value);
                                                  if (conflictIds.includes(selectedId) && !showModal) {
                                                      $event.preventDefault();
                                                      selectedName = sel.options[sel.selectedIndex].dataset.name;
                                                      showModal = true;
                                                  }
                                              ">
                                            @csrf
                                            <select name="user_id" class="form-input" style="font-size:0.8rem;padding:4px 8px;min-width:150px;">
                                                <option value="">— Assigner à —</option>
                                                @foreach($eligibleEditors as $ed)
                                                    <option value="{{ $ed->id }}" data-name="{{ $ed->name }}">
                                                        {{ $ed->name }}
                                                        @if(in_array($ed->id, $reviewerIds)) ⚠ déjà relecteur @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="override" :value="showModal ? '1' : ''">
                                            <input type="hidden" name="override_reason" :value="reason">
                                            <button type="submit" class="btn btn-dark btn-sm">Assigner</button>

                                            <template x-teleport="body">
                                                <div x-show="showModal" x-cloak x-transition
                                                     @keydown.escape.window="showModal = false"
                                                     style="position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; display:flex; align-items:center; justify-content:center; padding:1rem;">
                                                    <div @click.outside="showModal = false"
                                                         style="background:#fff; border-radius:8px; max-width:32rem; width:100%; padding:1.75rem; box-shadow:0 25px 50px rgba(0,0,0,0.25); border-top:4px solid #d97706;">
                                                        <h3 style="font-size:1.1rem; font-weight:700; color:#92400e; margin:0 0 0.5rem 0; display:flex; align-items:center; gap:0.5rem;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="22" height="22" style="color:#d97706;">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                                            </svg>
                                                            Conflit d'intérêt détecté
                                                        </h3>
                                                        <p style="font-size:0.9rem; color:#6b7280; margin:0 0 1rem 0;">
                                                            <strong x-text="selectedName"></strong> est déjà relecteur sur cet article. L'assigner comme éditeur crée un conflit d'intérêt.
                                                        </p>
                                                        <p style="font-size:0.875rem; color:#374151; margin:0 0 0.5rem 0;"><strong>Motif pour outrepasser</strong> <span style="color:#dc2626;">*</span></p>
                                                        <textarea x-model="reason" rows="4" minlength="3" maxlength="500"
                                                                  placeholder="ex : aucun autre éditeur disponible dans ce domaine, override validé par le comité."
                                                                  style="width:100%; min-height:110px; box-sizing:border-box; border:1px solid #d1d5db; border-radius:6px; padding:10px 12px; font-size:0.875rem; font-family:inherit; resize:vertical; line-height:1.5;"></textarea>
                                                        <p style="font-size:0.75rem; color:#6b7280; margin-top:0.25rem;">Le motif est enregistré dans le journal éditorial.</p>
                                                        <div style="display:flex; justify-content:flex-end; gap:0.5rem; margin-top:1rem;">
                                                            <button type="button" @click="showModal = false; reason = ''"
                                                                    style="padding:0.5rem 1rem; border:1px solid #d1d5db; border-radius:6px; font-size:0.875rem; background:#fff; cursor:pointer; color:#374151;">
                                                                Annuler
                                                            </button>
                                                            <button type="button"
                                                                    @click="if (reason.trim().length >= 3) { $root.submit(); }"
                                                                    x-bind:disabled="reason.trim().length < 3"
                                                                    x-bind:style="reason.trim().length < 3 ? 'opacity:0.5;cursor:not-allowed;' : 'cursor:pointer;'"
                                                                    style="padding:0.5rem 1.25rem; background:#d97706; color:#fff; border:none; border-radius:6px; font-size:0.875rem; font-weight:600;">
                                                                Confirmer l'override
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
