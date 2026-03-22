<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div>
        <div class="form-group">
            <label class="form-label" for="submission_id">Soumission *</label>
            <select name="submission_id" id="submission_id" class="form-input" required>
                <option value="">-- Selectionner --</option>
                @foreach($submissions as $submission)
                    <option value="{{ $submission->id }}" {{ old('submission_id', $review->submission_id ?? $selectedSubmission ?? '') == $submission->id ? 'selected' : '' }}>
                        {{ Str::limit($submission->title, 60) }}
                    </option>
                @endforeach
            </select>
            @error('submission_id')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="reviewer_id">Reviewer *</label>
            <select name="reviewer_id" id="reviewer_id" class="form-input" required>
                <option value="">-- Selectionner --</option>
                @foreach($reviewers as $reviewer)
                    <option value="{{ $reviewer->id }}" {{ old('reviewer_id', $review->reviewer_id ?? '') == $reviewer->id ? 'selected' : '' }}>
                        {{ $reviewer->name }}
                    </option>
                @endforeach
            </select>
            @error('reviewer_id')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="status">Statut *</label>
            <select name="status" id="status" class="form-input" required>
                @foreach(\App\Models\Review::getStatuses() as $key => $label)
                    <option value="{{ $key }}" {{ old('status', $review->status ?? 'invited') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="due_date">Date limite</label>
            <input type="date" name="due_date" id="due_date" class="form-input" value="{{ old('due_date', isset($review) && $review->due_date ? $review->due_date->format('Y-m-d') : '') }}">
        </div>
    </div>

    <div>
        @if(isset($review))
            <div class="form-group">
                <label class="form-label" for="recommendation">Recommandation</label>
                <select name="recommendation" id="recommendation" class="form-input">
                    <option value="">-- Pas de recommandation --</option>
                    @foreach(\App\Models\Review::getRecommendations() as $key => $label)
                        <option value="{{ $key }}" {{ old('recommendation', $review->recommendation ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">Scores (1-5)</h4>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="score_originality">Originalite</label>
                    <input type="number" name="score_originality" id="score_originality" class="form-input" value="{{ old('score_originality', $review->score_originality ?? '') }}" min="1" max="5">
                </div>
                <div class="form-group">
                    <label class="form-label" for="score_methodology">Methodologie</label>
                    <input type="number" name="score_methodology" id="score_methodology" class="form-input" value="{{ old('score_methodology', $review->score_methodology ?? '') }}" min="1" max="5">
                </div>
                <div class="form-group">
                    <label class="form-label" for="score_clarity">Clarte</label>
                    <input type="number" name="score_clarity" id="score_clarity" class="form-input" value="{{ old('score_clarity', $review->score_clarity ?? '') }}" min="1" max="5">
                </div>
                <div class="form-group">
                    <label class="form-label" for="score_significance">Significance</label>
                    <input type="number" name="score_significance" id="score_significance" class="form-input" value="{{ old('score_significance', $review->score_significance ?? '') }}" min="1" max="5">
                </div>
                <div class="form-group">
                    <label class="form-label" for="score_references">References</label>
                    <input type="number" name="score_references" id="score_references" class="form-input" value="{{ old('score_references', $review->score_references ?? '') }}" min="1" max="5">
                </div>
            </div>

            <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">Commentaires</h4>

            <div class="form-group">
                <label class="form-label" for="comments_to_editor">Commentaires a l'editeur</label>
                <textarea name="comments_to_editor" id="comments_to_editor" class="form-input" rows="4">{{ old('comments_to_editor', $review->comments_to_editor ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="comments_to_author">Commentaires a l'auteur</label>
                <textarea name="comments_to_author" id="comments_to_author" class="form-input" rows="4">{{ old('comments_to_author', $review->comments_to_author ?? '') }}</textarea>
            </div>

            <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">Fichier</h4>

            <div class="form-group">
                <label class="form-label" for="review_file">Document de review (PDF)</label>
                <input type="file" name="review_file" id="review_file" class="form-input" accept=".pdf,.doc,.docx">
                @if($review->review_file)
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: #f0fdf4; border: 1px solid #86efac; border-radius: 0.375rem; margin-top: 0.5rem; font-size: 0.875rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16" style="color: #15803d;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <a href="{{ route('admin.reviews.download', $review) }}" style="color: #15803d;">{{ basename($review->review_file) }}</a>
                        <label style="margin-left: auto; display: flex; align-items: center; gap: 0.25rem; cursor: pointer;">
                            <input type="checkbox" name="remove_review_file" value="1">
                            <span style="font-size: 0.75rem;">Supprimer</span>
                        </label>
                    </div>
                @endif
                @error('review_file')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        @endif
    </div>
</div>
