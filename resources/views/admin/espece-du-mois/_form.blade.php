<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Espèce</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="scientific_name">Nom scientifique *</label>
            <input type="text" name="scientific_name" id="scientific_name" class="form-input" required maxlength="255"
                   value="{{ old('scientific_name', $entry->scientific_name ?? '') }}"
                   placeholder="Ex. Vanessa atalanta">
            @error('scientific_name')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="photographer">Photographe</label>
            <input type="text" name="photographer" id="photographer" class="form-input" maxlength="255"
                   value="{{ old('photographer', $entry->photographer ?? '') }}"
                   placeholder="Ex. Jean Dupont">
            @error('photographer')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="photo">
                Photo {{ isset($entry) ? '(laisser vide pour conserver l\'actuelle)' : '*' }}
            </label>
            <input type="file" name="photo" id="photo" class="form-input" accept="image/*" @if(!isset($entry)) required @endif>
            <small style="color: #6b7280; font-size: 0.8125rem;">JPEG / PNG / WebP, 8 Mo maximum.</small>
            @if(isset($entry) && $entry->photo_path)
                <div style="margin-top: 0.75rem;">
                    <img src="{{ $entry->photoUrl() }}" alt="" style="max-width: 240px; border-radius: 10px;">
                </div>
            @endif
            @error('photo')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Affichage</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="display_order">Ordre d'affichage</label>
            <input type="number" name="display_order" id="display_order" class="form-input" min="0"
                   style="max-width: 120px;"
                   value="{{ old('display_order', $entry->display_order ?? 0) }}">
            <small style="color: #6b7280; font-size: 0.8125rem;">Les entrées sont triées du plus petit au plus grand.</small>
            @error('display_order')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $entry->is_active ?? true) ? 'checked' : '' }}
                       style="width: 1rem; height: 1rem;">
                <span>Actif (affiché dans le carousel)</span>
            </label>
        </div>
    </div>
</div>
