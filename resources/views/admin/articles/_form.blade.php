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

        {{-- Image à la une --}}
        <div class="form-group">
            <label class="form-label" for="featured_image">Image à la une</label>
            @if(isset($article) && $article->featured_image)
                <div style="margin-bottom: 0.5rem;">
                    <img src="{{ Storage::url($article->featured_image) }}" alt="Image actuelle" style="max-height: 120px; border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
            @endif
            <input type="file" name="featured_image" id="featured_image" class="form-input" accept="image/*">
            <p style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">JPG, PNG ou WebP. Max 5 Mo.</p>
            @error('featured_image')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        {{-- Document à télécharger --}}
        <div class="form-group">
            <label class="form-label" for="document">Document joint</label>
            @if(isset($article) && $article->document_path)
                <div style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: #f3f4f6; border-radius: 8px; font-size: 0.875rem;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>{{ $article->document_name ?? 'Document joint' }}</span>
                    <a href="{{ Storage::url($article->document_path) }}" target="_blank" style="color: #356B8A; font-weight: 600; margin-left: auto;">Voir</a>
                </div>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.875rem; color: #dc2626; margin-bottom: 0.5rem;">
                    <input type="checkbox" name="remove_document" value="1" style="width: auto;">
                    <span>Supprimer le document</span>
                </label>
            @endif
            <input type="file" name="document" id="document" class="form-input">
            <p style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">PDF, Word, etc. Max 20 Mo.</p>
            @error('document')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        @if(isset($article))
            <div class="form-group">
                <label class="form-label" for="validation_notes">Notes de validation</label>
                <textarea name="validation_notes" id="validation_notes" class="form-input" rows="3">{{ old('validation_notes', $article->validation_notes ?? '') }}</textarea>
            </div>
        @endif
    </div>
</div>
