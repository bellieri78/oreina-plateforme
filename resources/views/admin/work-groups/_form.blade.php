<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div>
        <div class="form-group">
            <label class="form-label" for="name">Nom *</label>
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $workGroup->name ?? '') }}" required>
            @error('name')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea name="description" id="description" class="form-input" rows="5">{{ old('description', $workGroup->description ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="website_url">Site web</label>
            <input type="url" name="website_url" id="website_url" class="form-input" value="{{ old('website_url', $workGroup->website_url ?? '') }}" placeholder="https://...">
            @error('website_url')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <div class="form-group">
            <label class="form-label" for="color">Couleur *</label>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="color" name="color" id="color" value="{{ old('color', $workGroup->color ?? '#2C5F2D') }}" style="width: 50px; height: 38px; padding: 2px; border: 1px solid #d1d5db; border-radius: 0.375rem; cursor: pointer;">
                <input type="text" id="color_text" class="form-input" value="{{ old('color', $workGroup->color ?? '#2C5F2D') }}" style="flex: 1;" readonly>
            </div>
            @error('color')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="icon">Icone</label>
            <input type="text" name="icon" id="icon" class="form-input" value="{{ old('icon', $workGroup->icon ?? '') }}" placeholder="ex: butterfly, leaf...">
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $workGroup->is_active ?? true) ? 'checked' : '' }} style="width: auto;">
                <span>Groupe actif</span>
            </label>
        </div>

        <div class="form-group">
            <label class="form-label" for="join_policy">Adhésion *</label>
            <select name="join_policy" id="join_policy" class="form-input" required>
                <option value="open" {{ old('join_policy', $workGroup->join_policy ?? 'open') === 'open' ? 'selected' : '' }}>Ouverte (auto-inscription)</option>
                <option value="request" {{ old('join_policy', $workGroup->join_policy ?? '') === 'request' ? 'selected' : '' }}>Sur demande (validation coordinateur)</option>
            </select>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="has_resources" value="1" {{ old('has_resources', $workGroup->has_resources ?? true) ? 'checked' : '' }} style="width: auto;">
                <span>Espace ressources activé</span>
            </label>
        </div>

        <div class="form-group" x-data="{ collab: {{ old('has_collaborative_space', $workGroup->has_collaborative_space ?? false) ? 'true' : 'false' }} }">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="has_collaborative_space" value="1" x-model="collab" {{ old('has_collaborative_space', $workGroup->has_collaborative_space ?? false) ? 'checked' : '' }} style="width: auto;">
                <span>Espace de travail collaboratif (lien externe)</span>
            </label>
            <input type="url" name="collaborative_space_url" class="form-input" x-show="collab" style="margin-top: 0.5rem;"
                   value="{{ old('collaborative_space_url', $workGroup->collaborative_space_url ?? '') }}"
                   placeholder="https://framadrive.org/... ou Nextcloud, Drive...">
            @error('collaborative_space_url')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: not-allowed; opacity: 0.6;">
                <input type="checkbox" disabled style="width: auto;">
                <span>Forum / discussions <em>(Phase 2 — bientôt)</em></span>
            </label>
        </div>
    </div>
</div>

<script>
document.getElementById('color').addEventListener('input', function() {
    document.getElementById('color_text').value = this.value;
});
</script>
