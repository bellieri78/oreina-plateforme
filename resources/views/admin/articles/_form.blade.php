<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div>
        <div class="form-group">
            <label class="form-label" for="title">Titre *</label>
            <input type="text" name="title" id="title" class="form-input" value="{{ old('title', $article->title ?? '') }}" required>
            @error('title')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="slug">Slug (URL)</label>
            <input type="text" name="slug" id="slug" class="form-input" value="{{ old('slug', $article->slug ?? '') }}" placeholder="Genere automatiquement si vide">
            @error('slug')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="summary">Resume</label>
            <textarea name="summary" id="summary" class="form-input" rows="3">{{ old('summary', $article->summary ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="content">Contenu *</label>
            <textarea name="content" id="content" class="form-input" rows="15" required>{{ old('content', $article->content ?? '') }}</textarea>
            @error('content')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <div class="form-group">
            <label class="form-label" for="status">Statut *</label>
            <select name="status" id="status" class="form-input" required>
                <option value="draft" {{ old('status', $article->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                <option value="submitted" {{ old('status', $article->status ?? '') === 'submitted' ? 'selected' : '' }}>Soumis</option>
                <option value="validated" {{ old('status', $article->status ?? '') === 'validated' ? 'selected' : '' }}>Valide</option>
                <option value="published" {{ old('status', $article->status ?? '') === 'published' ? 'selected' : '' }}>Publie</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="category">Categorie</label>
            <input type="text" name="category" id="category" class="form-input" value="{{ old('category', $article->category ?? '') }}" placeholder="ex: Actualites, Recherche...">
        </div>

        <div class="form-group">
            <label class="form-label" for="author_id">Auteur</label>
            <select name="author_id" id="author_id" class="form-input">
                <option value="">-- Utilisateur courant --</option>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}" {{ old('author_id', $article->author_id ?? '') == $author->id ? 'selected' : '' }}>
                        {{ $author->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="published_at">Date de publication</label>
            <input type="datetime-local" name="published_at" id="published_at" class="form-input" value="{{ old('published_at', isset($article) && $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}">
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $article->is_featured ?? false) ? 'checked' : '' }} style="width: auto;">
                <span>Article en vedette</span>
            </label>
        </div>

        @if(isset($article))
            <div class="form-group">
                <label class="form-label" for="validation_notes">Notes de validation</label>
                <textarea name="validation_notes" id="validation_notes" class="form-input" rows="3">{{ old('validation_notes', $article->validation_notes ?? '') }}</textarea>
            </div>
        @endif
    </div>
</div>
