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
                                        <form method="POST" action="{{ route('admin.journal.queue.assign', $s) }}"
                                              style="margin:0;display:flex;align-items:center;gap:6px;"
                                              x-data="{ override: false, reason: '', showModal: false }"
                                              @submit="if (override && !showModal) { $event.preventDefault(); showModal = true; }">
                                            @csrf
                                            <select name="user_id" class="form-input" style="font-size:0.8rem;padding:4px 8px;min-width:150px;">
                                                <option value="">— Assigner à —</option>
                                                @foreach($eligibleEditors as $ed)
                                                    <option value="{{ $ed->id }}">
                                                        {{ $ed->name }}
                                                        @if(in_array($ed->id, $reviewerIds)) (déjà relecteur) @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label style="font-size:0.75rem;display:flex;align-items:center;gap:4px;white-space:nowrap;">
                                                <input type="checkbox" name="override" value="1" x-model="override"> forcer
                                            </label>
                                            <input type="hidden" name="override_reason" :value="reason">
                                            <button type="submit" class="btn btn-dark btn-sm">Assigner</button>

                                            <template x-teleport="body">
                                                <div x-show="showModal" x-cloak x-transition
                                                     @keydown.escape.window="showModal = false"
                                                     style="position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; display:flex; align-items:center; justify-content:center; padding:1rem;">
                                                    <div @click.outside="showModal = false"
                                                         style="background:#fff; border-radius:8px; max-width:32rem; width:100%; padding:1.75rem; box-shadow:0 25px 50px rgba(0,0,0,0.25);">
                                                        <h3 style="font-size:1.1rem; font-weight:700; color:#92400e; margin:0 0 0.5rem 0;">
                                                            Séparation des rôles — confirmer l'override
                                                        </h3>
                                                        <p style="font-size:0.9rem; color:#6b7280; margin:0 0 1rem 0;">
                                                            Cette personne est <strong>déjà relecteur</strong> sur cet article. L'assigner comme éditeur crée un conflit d'intérêt.
                                                        </p>
                                                        <p style="font-size:0.875rem; color:#374151; margin:0 0 0.5rem 0;"><strong>Motif de l'override</strong> <span style="color:#dc2626;">*</span></p>
                                                        <textarea x-model="reason" rows="4" minlength="3" maxlength="500"
                                                                  placeholder="ex : aucun autre éditeur disponible dans ce domaine, override validé par le comité."
                                                                  style="width:100%; min-height:110px; box-sizing:border-box; border:1px solid #d1d5db; border-radius:6px; padding:10px 12px; font-size:0.875rem; font-family:inherit; resize:vertical; line-height:1.5;"></textarea>
                                                        <div style="display:flex; justify-content:flex-end; gap:0.5rem; margin-top:1rem;">
                                                            <button type="button" @click="showModal = false"
                                                                    style="padding:0.5rem 1rem; border:1px solid #d1d5db; border-radius:6px; font-size:0.875rem; background:#fff; cursor:pointer; color:#374151;">
                                                                Annuler
                                                            </button>
                                                            <button type="button"
                                                                    @click="if (reason.trim().length >= 3) { showModal = false; $root.submit(); }"
                                                                    style="padding:0.5rem 1.25rem; background:#d97706; color:#fff; border:none; border-radius:6px; font-size:0.875rem; font-weight:600; cursor:pointer;">
                                                                Confirmer
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
