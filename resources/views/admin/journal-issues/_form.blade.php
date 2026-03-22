<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="volume_number">Volume *</label>
                <input type="number" name="volume_number" id="volume_number" class="form-input" value="{{ old('volume_number', $journalIssue->volume_number ?? $suggestedVolume ?? 1) }}" min="1" required>
                @error('volume_number')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="issue_number">Numero *</label>
                <input type="number" name="issue_number" id="issue_number" class="form-input" value="{{ old('issue_number', $journalIssue->issue_number ?? $suggestedIssue ?? 1) }}" min="1" required>
                @error('issue_number')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="title">Titre (optionnel)</label>
            <input type="text" name="title" id="title" class="form-input" value="{{ old('title', $journalIssue->title ?? '') }}" placeholder="ex: Special Biodiversite">
        </div>

        <div class="form-group">
            <label class="form-label" for="slug">Slug (URL)</label>
            <input type="text" name="slug" id="slug" class="form-input" value="{{ old('slug', $journalIssue->slug ?? '') }}" placeholder="Genere automatiquement si vide">
        </div>

        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea name="description" id="description" class="form-input" rows="5">{{ old('description', $journalIssue->description ?? '') }}</textarea>
        </div>
    </div>

    <div>
        <div class="form-group">
            <label class="form-label" for="status">Statut *</label>
            <select name="status" id="status" class="form-input" required>
                <option value="draft" {{ old('status', $journalIssue->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                <option value="published" {{ old('status', $journalIssue->status ?? '') === 'published' ? 'selected' : '' }}>Publie</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="publication_date">Date de publication</label>
            <input type="date" name="publication_date" id="publication_date" class="form-input" value="{{ old('publication_date', isset($journalIssue) && $journalIssue->publication_date ? $journalIssue->publication_date->format('Y-m-d') : '') }}">
        </div>

        <div class="form-group">
            <label class="form-label" for="doi">DOI</label>
            <input type="text" name="doi" id="doi" class="form-input" value="{{ old('doi', $journalIssue->doi ?? '') }}" placeholder="ex: 10.1234/oreina.2026.1">
        </div>

        <div class="form-group">
            <label class="form-label" for="page_count">Nombre de pages</label>
            <input type="number" name="page_count" id="page_count" class="form-input" value="{{ old('page_count', $journalIssue->page_count ?? '') }}" min="1">
        </div>
    </div>
</div>
