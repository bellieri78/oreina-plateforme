<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div>
        <div class="form-group">
            <label class="form-label" for="title">Titre *</label>
            <input type="text" name="title" id="title" class="form-input" value="{{ old('title', $bulletin->title ?? '') }}" required>
            @error('title')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="issue_number">Numéro *</label>
                <input type="number" name="issue_number" id="issue_number" class="form-input" value="{{ old('issue_number', $bulletin->issue_number ?? '') }}" min="1" required>
                @error('issue_number')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="quarter">Trimestre *</label>
                <select name="quarter" id="quarter" class="form-input" required>
                    <option value="Q1" {{ old('quarter', $bulletin->quarter ?? '') === 'Q1' ? 'selected' : '' }}>Q1 - Printemps</option>
                    <option value="Q2" {{ old('quarter', $bulletin->quarter ?? '') === 'Q2' ? 'selected' : '' }}>Q2 - Été</option>
                    <option value="Q3" {{ old('quarter', $bulletin->quarter ?? '') === 'Q3' ? 'selected' : '' }}>Q3 - Automne</option>
                    <option value="Q4" {{ old('quarter', $bulletin->quarter ?? '') === 'Q4' ? 'selected' : '' }}>Q4 - Hiver</option>
                </select>
                @error('quarter')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="year">Année *</label>
                <input type="number" name="year" id="year" class="form-input" value="{{ old('year', $bulletin->year ?? date('Y')) }}" min="1900" max="2100" required>
                @error('year')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="summary">Sommaire</label>
            <textarea name="summary" id="summary" class="form-input" rows="4">{{ old('summary', $bulletin->summary ?? '') }}</textarea>
            <p style="font-size: 0.8rem; color: #6b7280; margin-top: 0.25rem;">Markdown supporté, affiché sur le site hub.</p>
            @error('summary')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <div class="form-group">
            <label class="form-label" for="pdf">Fichier PDF {{ isset($bulletin) ? '' : '*' }}</label>
            <input type="file" name="pdf" id="pdf" class="form-input" accept=".pdf" {{ isset($bulletin) ? '' : 'required' }}>
            @error('pdf')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            @if(isset($bulletin) && $bulletin->pdf_path)
                <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
                    PDF actuel : <a href="{{ Storage::url($bulletin->pdf_path) }}" target="_blank" style="color: #2C5F2D;">Voir le fichier</a>
                </p>
            @endif
        </div>

        <div class="form-group">
            <label class="form-label" for="cover">Image de couverture</label>
            @if(isset($bulletin) && $bulletin->cover_image)
                <div style="margin-bottom:0.5rem;">
                    <img src="{{ Storage::url($bulletin->cover_image) }}" alt="Couverture"
                        style="height:80px;object-fit:cover;border-radius:4px;border:1px solid #e5e7eb;">
                </div>
            @endif
            <input type="file" name="cover" id="cover" class="form-input" accept="image/*">
            @error('cover')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
