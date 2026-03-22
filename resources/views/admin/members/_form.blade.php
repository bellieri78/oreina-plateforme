<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    <div class="form-group">
        <label class="form-label" for="first_name">Prenom *</label>
        <input type="text" name="first_name" id="first_name" class="form-input" value="{{ old('first_name', $member->first_name ?? '') }}" required>
        @error('first_name')
            <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="last_name">Nom *</label>
        <input type="text" name="last_name" id="last_name" class="form-input" value="{{ old('last_name', $member->last_name ?? '') }}" required>
        @error('last_name')
            <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="email">Email *</label>
    <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $member->email ?? '') }}" required>
    @error('email')
        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label class="form-label" for="phone">Telephone</label>
    <input type="tel" name="phone" id="phone" class="form-input" value="{{ old('phone', $member->phone ?? '') }}">
    @error('phone')
        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label class="form-label" for="address">Adresse</label>
    <input type="text" name="address" id="address" class="form-input" value="{{ old('address', $member->address ?? '') }}">
    @error('address')
        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
    @enderror
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 1rem;">
    <div class="form-group">
        <label class="form-label" for="postal_code">Code postal</label>
        <input type="text" name="postal_code" id="postal_code" class="form-input" value="{{ old('postal_code', $member->postal_code ?? '') }}">
        @error('postal_code')
            <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="city">Ville</label>
        <input type="text" name="city" id="city" class="form-input" value="{{ old('city', $member->city ?? '') }}">
        @error('city')
            <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="country">Pays</label>
        <input type="text" name="country" id="country" class="form-input" value="{{ old('country', $member->country ?? 'France') }}">
        @error('country')
            <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="form-group">
    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $member->is_active ?? true) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
        <span class="form-label" style="margin-bottom: 0;">Contact actif</span>
    </label>
</div>
