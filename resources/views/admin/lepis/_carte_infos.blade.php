<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Infos éditoriales</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.lepis.update', $bulletin) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="title">Titre *</label>
                <input type="text" name="title" id="title" class="form-input"
                    value="{{ old('title', $bulletin->title) }}" required>
                @error('title')<p style="color:#dc2626;font-size:0.875rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label" for="issue_number">Numéro *</label>
                    <input type="number" name="issue_number" id="issue_number" class="form-input"
                        value="{{ old('issue_number', $bulletin->issue_number) }}" min="1" required>
                    @error('issue_number')<p style="color:#dc2626;font-size:0.875rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="quarter">Trimestre *</label>
                    <select name="quarter" id="quarter" class="form-input" required>
                        <option value="Q1" {{ old('quarter', $bulletin->quarter) === 'Q1' ? 'selected' : '' }}>Q1 - Printemps</option>
                        <option value="Q2" {{ old('quarter', $bulletin->quarter) === 'Q2' ? 'selected' : '' }}>Q2 - Été</option>
                        <option value="Q3" {{ old('quarter', $bulletin->quarter) === 'Q3' ? 'selected' : '' }}>Q3 - Automne</option>
                        <option value="Q4" {{ old('quarter', $bulletin->quarter) === 'Q4' ? 'selected' : '' }}>Q4 - Hiver</option>
                    </select>
                    @error('quarter')<p style="color:#dc2626;font-size:0.875rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="year">Année *</label>
                    <input type="number" name="year" id="year" class="form-input"
                        value="{{ old('year', $bulletin->year) }}" min="1900" max="2100" required>
                    @error('year')<p style="color:#dc2626;font-size:0.875rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="summary">Sommaire</label>
                <textarea name="summary" id="summary" class="form-input" rows="5">{{ old('summary', $bulletin->summary) }}</textarea>
                <p style="font-size:0.8rem;color:#6b7280;margin-top:0.25rem;">Markdown supporté, affiché sur le site hub.</p>
                @error('summary')<p style="color:#dc2626;font-size:0.875rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Image de couverture</label>
                @if ($bulletin->cover_image)
                    <div style="margin-bottom:0.5rem;">
                        <img src="{{ Storage::url($bulletin->cover_image) }}" alt="Couverture actuelle"
                            style="height:120px;object-fit:cover;border-radius:4px;border:1px solid #e5e7eb;">
                    </div>
                    <label class="form-label" style="font-weight:normal;font-size:0.875rem;" for="cover">Remplacer</label>
                @else
                    <label class="form-label" style="font-weight:normal;font-size:0.875rem;" for="cover">Choisir un fichier image</label>
                @endif
                <input type="file" name="cover" id="cover" class="form-input" accept="image/*">
                @error('cover')<p style="color:#dc2626;font-size:0.875rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
