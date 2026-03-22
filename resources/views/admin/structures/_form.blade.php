<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    {{-- Left column --}}
    <div>
        <div class="form-group">
            <label for="name" class="form-label">Nom *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $structure->name ?? '') }}"
                   class="form-input @error('name') is-invalid @enderror" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="code" class="form-label">Code *</label>
            <input type="text" name="code" id="code" value="{{ old('code', $structure->code ?? '') }}"
                   class="form-input @error('code') is-invalid @enderror" required
                   placeholder="Ex: REG-ARA, DEP-69">
            @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small style="color: #6b7280;">Code unique pour identifier la structure (majuscules, chiffres, tirets)</small>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="type" class="form-label">Type *</label>
                <select name="type" id="type" class="form-input @error('type') is-invalid @enderror" required>
                    @foreach(\App\Models\Structure::getTypes() as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $structure->type ?? '') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="parent_id" class="form-label">Structure parente</label>
                <select name="parent_id" id="parent_id" class="form-input @error('parent_id') is-invalid @enderror">
                    <option value="">-- Aucune (structure racine) --</option>
                    @foreach($parentStructures as $id => $name)
                        <option value="{{ $id }}" {{ old('parent_id', $structure->parent_id ?? '') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" rows="3"
                      class="form-input @error('description') is-invalid @enderror">{{ old('description', $structure->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="departement_code" class="form-label">Code departement</label>
                <input type="text" name="departement_code" id="departement_code"
                       value="{{ old('departement_code', $structure->departement_code ?? '') }}"
                       class="form-input @error('departement_code') is-invalid @enderror"
                       maxlength="3" placeholder="Ex: 69, 2A">
                @error('departement_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="region" class="form-label">Region</label>
                <input type="text" name="region" id="region"
                       value="{{ old('region', $structure->region ?? '') }}"
                       class="form-input @error('region') is-invalid @enderror"
                       placeholder="Ex: Auvergne-Rhone-Alpes">
                @error('region')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Right column --}}
    <div>
        <div class="form-group">
            <label for="responsable_id" class="form-label">Responsable</label>
            <select name="responsable_id" id="responsable_id" class="form-input @error('responsable_id') is-invalid @enderror">
                <option value="">-- Selectionner --</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ old('responsable_id', $structure->responsable_id ?? '') == $member->id ? 'selected' : '' }}>
                        {{ $member->full_name }}
                    </option>
                @endforeach
            </select>
            @error('responsable_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email"
                   value="{{ old('email', $structure->email ?? '') }}"
                   class="form-input @error('email') is-invalid @enderror">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone" class="form-label">Telephone</label>
            <input type="text" name="phone" id="phone"
                   value="{{ old('phone', $structure->phone ?? '') }}"
                   class="form-input @error('phone') is-invalid @enderror">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="address" class="form-label">Adresse</label>
            <textarea name="address" id="address" rows="2"
                      class="form-input @error('address') is-invalid @enderror">{{ old('address', $structure->address ?? '') }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
            <div class="form-group">
                <label for="postal_code" class="form-label">Code postal</label>
                <input type="text" name="postal_code" id="postal_code"
                       value="{{ old('postal_code', $structure->postal_code ?? '') }}"
                       class="form-input @error('postal_code') is-invalid @enderror">
                @error('postal_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="city" class="form-label">Ville</label>
                <input type="text" name="city" id="city"
                       value="{{ old('city', $structure->city ?? '') }}"
                       class="form-input @error('city') is-invalid @enderror">
                @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="sort_order" class="form-label">Ordre de tri</label>
                <input type="number" name="sort_order" id="sort_order"
                       value="{{ old('sort_order', $structure->sort_order ?? 0) }}"
                       class="form-input @error('sort_order') is-invalid @enderror">
                @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">&nbsp;</label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $structure->is_active ?? true) ? 'checked' : '' }}>
                    <span>Structure active</span>
                </label>
            </div>
        </div>
    </div>
</div>
