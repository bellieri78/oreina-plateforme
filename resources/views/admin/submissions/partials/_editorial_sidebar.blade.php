@php
    use App\Enums\SubmissionStatus;
    use App\Models\EditorialCapability;
    use App\Policies\SubmissionPolicy;

    $policy = app(SubmissionPolicy::class);
    $authUser = auth()->user();
    $reviewerIds = $submission->reviews->pluck('reviewer_id')->all();

    // Le maquettiste n'a de sens qu'à partir du moment où l'article est accepté
    $statusValue = $submission->status instanceof SubmissionStatus ? $submission->status->value : $submission->status;
    $showLayoutEditor = in_array($statusValue, ['accepted', 'in_production', 'awaiting_author_approval', 'published']);
@endphp

{{-- Équipe éditoriale --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">Équipe éditoriale</h3>
    </div>
    <div class="card-body">
        {{-- Éditeur --}}
        <div style="margin-bottom: 1rem;">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Éditeur</div>
            @if($submission->editor)
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-weight: 500;">{{ $submission->editor->name }}</span>
                    @if($policy->assignEditor($authUser, $submission))
                        <button type="button" onclick="var f=this.closest('.card-body').querySelector('#reassign-editor-form-{{ $submission->id }}');f.style.display=f.style.display==='none'?'block':'none'"
                                style="font-size: 0.75rem; color: #356B8A; cursor: pointer; background: none; border: none; text-decoration: underline;">
                            Changer
                        </button>
                    @endif
                </div>
            @else
                <span style="color: #9ca3af;">Non assigné</span>
            @endif

            @if($policy->assignEditor($authUser, $submission))
                <form id="reassign-editor-form-{{ $submission->id }}" method="POST"
                      action="{{ route('admin.journal.submissions.assign-editor', $submission) }}"
                      style="margin-top: 0.5rem; display: none;"
                      x-data="{ override: false, reason: '', showModal: false }"
                      @submit="if (override && !showModal) { $event.preventDefault(); showModal = true; }">
                    @csrf
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <select name="user_id" class="form-input" style="font-size: 0.8rem; padding: 4px 8px; flex: 1;" required>
                            <option value="">— Sélectionner —</option>
                            @foreach($eligibleEditors as $ed)
                                @if($ed->id !== ($submission->editor_id ?? 0))
                                    <option value="{{ $ed->id }}"
                                        @if(in_array($ed->id, $reviewerIds)) disabled @endif>
                                        {{ $ed->name }}
                                        @if(in_array($ed->id, $reviewerIds)) (relecteur) @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">OK</button>
                    </div>
                    @if(collect($eligibleEditors)->contains(fn($e) => in_array($e->id, $reviewerIds)))
                        <label style="display: flex; align-items: center; gap: 4px; font-size: 0.75rem; margin-top: 0.25rem;">
                            <input type="checkbox" name="override" value="1" x-model="override"> Forcer (déjà relecteur)
                        </label>
                        <input type="hidden" name="override_reason" :value="reason">

                        {{-- Modale de confirmation override --}}
                        <template x-teleport="body">
                            <div x-show="showModal" x-cloak x-transition
                                 @keydown.escape.window="showModal = false"
                                 style="position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; display:flex; align-items:center; justify-content:center; padding:1rem;">
                                <div @click.outside="showModal = false"
                                     style="background:#fff; border-radius:8px; max-width:32rem; width:100%; padding:1.75rem; box-shadow:0 25px 50px rgba(0,0,0,0.25);">
                                    <h3 style="font-size:1.1rem; font-weight:700; color:#92400e; margin:0 0 0.5rem 0; display:flex; align-items:center; gap:0.5rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="22" height="22" style="color:#d97706;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                        </svg>
                                        Séparation des rôles — confirmer l'override
                                    </h3>
                                    <p style="font-size:0.9rem; color:#6b7280; margin:0 0 1rem 0;">
                                        Cette personne est <strong>déjà relecteur</strong> sur cet article. Assigner comme éditeur crée un conflit d'intérêt (l'éditeur synthétise et décide à partir de relectures, dont la sienne).
                                    </p>
                                    <p style="font-size:0.875rem; color:#374151; margin:0 0 0.5rem 0;"><strong>Motif de l'override</strong> <span style="color:#dc2626;">*</span></p>
                                    <textarea x-model="reason" rows="4" required minlength="3" maxlength="500"
                                              placeholder="ex : aucun autre relecteur spécialisé disponible dans ce domaine, override validé par le comité le DD/MM."
                                              style="width:100%; min-height:110px; box-sizing:border-box; border:1px solid #d1d5db; border-radius:6px; padding:10px 12px; font-size:0.875rem; font-family:inherit; resize:vertical; line-height:1.5;"></textarea>
                                    <p style="font-size:0.75rem; color:#6b7280; margin-top:0.25rem;">Le motif est enregistré dans le journal éditorial (visible des admins).</p>
                                    <div style="display:flex; justify-content:flex-end; gap:0.5rem; margin-top:1rem;">
                                        <button type="button" @click="showModal = false"
                                                style="padding:0.5rem 1rem; border:1px solid #d1d5db; border-radius:6px; font-size:0.875rem; background:#fff; cursor:pointer; color:#374151;">
                                            Annuler
                                        </button>
                                        <button type="button"
                                                @click="if (reason.trim().length >= 3) { showModal = false; $root.submit(); }"
                                                style="padding:0.5rem 1.25rem; background:#d97706; color:#fff; border:none; border-radius:6px; font-size:0.875rem; font-weight:600; cursor:pointer;">
                                            Confirmer l'override
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    @endif
                </form>
            @endif
        </div>

        {{-- Maquettiste — visible seulement à partir du stade "accepté" --}}
        @if($showLayoutEditor)
        <div style="padding-top: 1rem; border-top: 1px solid #e5e7eb;">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Maquettiste</div>
            @if($submission->layoutEditor)
                <span style="font-weight: 500;">{{ $submission->layoutEditor->name }}</span>
            @else
                <span style="color: #9ca3af;">Non assigné</span>
            @endif

            @if($policy->assignLayoutEditor($authUser, $submission))
                <form method="POST" action="{{ route('admin.journal.submissions.assign-layout-editor', $submission) }}"
                      style="margin-top: 0.5rem;">
                    @csrf
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <select name="user_id" class="form-input" style="font-size: 0.8rem; padding: 4px 8px; flex: 1;" required>
                            <option value="">— Sélectionner —</option>
                            @foreach($eligibleLayoutEditors as $le)
                                @if($le->id !== ($submission->layout_editor_id ?? 0))
                                    <option value="{{ $le->id }}">{{ $le->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">OK</button>
                    </div>
                </form>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- Inviter un relecteur --}}
@if($policy->assignReviewer($authUser, $submission))
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header">
            <h3 class="card-title">Inviter un relecteur</h3>
        </div>
        <div class="card-body">
            @php
                $availableReviewers = $eligibleReviewers->filter(fn($r) =>
                    !in_array($r->id, $reviewerIds) && $r->id !== $submission->editor_id
                );
                $editorIsReviewer = $submission->editor_id && $eligibleReviewers->contains(fn($r) => $r->id === $submission->editor_id);
            @endphp

            @if($availableReviewers->isEmpty() && !$editorIsReviewer)
                <p style="color: #9ca3af; font-size: 0.875rem;">Aucun relecteur éligible disponible.</p>
            @else
                <form method="POST" action="{{ route('admin.journal.submissions.invite-reviewer', $submission) }}"
                      x-data="{ override: false, reason: '', showModal: false }"
                      @submit="if (override && !showModal) { $event.preventDefault(); showModal = true; }">
                    @csrf
                    <select name="reviewer_id" class="form-input" style="width: 100%; margin-bottom: 0.5rem;" required>
                        <option value="">— Sélectionner un relecteur —</option>
                        @foreach($availableReviewers as $r)
                            <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->email }})</option>
                        @endforeach
                        @if($editorIsReviewer)
                            <option value="{{ $submission->editor_id }}">{{ $submission->editor?->name }} (éditeur — override requis)</option>
                        @endif
                    </select>

                    @if($editorIsReviewer)
                        <label style="display: flex; align-items: center; gap: 4px; font-size: 0.75rem; margin-bottom: 0.5rem;">
                            <input type="checkbox" name="override" value="1" x-model="override"> Forcer (override séparation des rôles)
                        </label>
                        <input type="hidden" name="override_reason" :value="reason">

                        <template x-teleport="body">
                            <div x-show="showModal" x-cloak x-transition
                                 @keydown.escape.window="showModal = false"
                                 style="position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; display:flex; align-items:center; justify-content:center; padding:1rem;">
                                <div @click.outside="showModal = false"
                                     style="background:#fff; border-radius:8px; max-width:32rem; width:100%; padding:1.75rem; box-shadow:0 25px 50px rgba(0,0,0,0.25);">
                                    <h3 style="font-size:1.1rem; font-weight:700; color:#92400e; margin:0 0 0.5rem 0; display:flex; align-items:center; gap:0.5rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="22" height="22" style="color:#d97706;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                        </svg>
                                        Séparation des rôles — confirmer l'override
                                    </h3>
                                    <p style="font-size:0.9rem; color:#6b7280; margin:0 0 1rem 0;">
                                        Inviter l'<strong>éditeur</strong> de l'article comme relecteur crée un conflit d'intérêt (la même personne synthétise sa propre relecture).
                                    </p>
                                    <p style="font-size:0.875rem; color:#374151; margin:0 0 0.5rem 0;"><strong>Motif de l'override</strong> <span style="color:#dc2626;">*</span></p>
                                    <textarea x-model="reason" rows="4" required minlength="3" maxlength="500"
                                              placeholder="ex : aucun autre relecteur spécialisé disponible dans ce domaine, override validé par le comité le DD/MM."
                                              style="width:100%; min-height:110px; box-sizing:border-box; border:1px solid #d1d5db; border-radius:6px; padding:10px 12px; font-size:0.875rem; font-family:inherit; resize:vertical; line-height:1.5;"></textarea>
                                    <p style="font-size:0.75rem; color:#6b7280; margin-top:0.25rem;">Le motif est enregistré dans le journal éditorial (visible des admins).</p>
                                    <div style="display:flex; justify-content:flex-end; gap:0.5rem; margin-top:1rem;">
                                        <button type="button" @click="showModal = false"
                                                style="padding:0.5rem 1rem; border:1px solid #d1d5db; border-radius:6px; font-size:0.875rem; background:#fff; cursor:pointer; color:#374151;">
                                            Annuler
                                        </button>
                                        <button type="button"
                                                @click="if (reason.trim().length >= 3) { showModal = false; $root.submit(); }"
                                                style="padding:0.5rem 1.25rem; background:#d97706; color:#fff; border:none; border-radius:6px; font-size:0.875rem; font-weight:600; cursor:pointer;">
                                            Confirmer l'override
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    @endif

                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                        Inviter
                    </button>
                </form>
            @endif

            @if($submission->reviews->isNotEmpty())
                <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #e5e7eb;">
                    <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.5rem;">Relecteurs actuels :</div>
                    @foreach($submission->reviews as $review)
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; margin-bottom: 0.25rem;">
                            <span>{{ $review->reviewer?->name ?? '—' }}</span>
                            <span class="badge badge-{{ match($review->status) {
                                'invited' => 'info',
                                'accepted' => 'primary',
                                'completed' => 'success',
                                'declined' => 'danger',
                                'expired' => 'secondary',
                                default => 'secondary',
                            } }}" style="font-size: 0.7rem;">
                                {{ \App\Models\Review::getStatuses()[$review->status] ?? $review->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
