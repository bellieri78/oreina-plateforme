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
                      style="margin-top: 0.5rem; display: none;">
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
                            <input type="checkbox" name="override" value="1"> Forcer
                        </label>
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
                <form method="POST" action="{{ route('admin.journal.submissions.invite-reviewer', $submission) }}">
                    @csrf
                    <select name="reviewer_id" class="form-input" style="width: 100%; margin-bottom: 0.5rem;" required>
                        <option value="">— Sélectionner un relecteur —</option>
                        @foreach($availableReviewers as $r)
                            <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->email }})</option>
                        @endforeach
                    </select>

                    @if($editorIsReviewer)
                        <label style="display: flex; align-items: center; gap: 4px; font-size: 0.75rem; margin-bottom: 0.5rem;">
                            <input type="checkbox" name="override" value="1"> Forcer (override séparation des rôles)
                        </label>
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
