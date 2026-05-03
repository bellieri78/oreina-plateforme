{{-- Carte 1 — Identité --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Identité</h3></div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 120px 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="civilite">Civilité</label>
                <select name="civilite" id="civilite" class="form-input">
                    <option value="">--</option>
                    @foreach(\App\Models\Member::CIVILITES as $c)
                        <option value="{{ $c }}" {{ old('civilite', $member?->civilite ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
                @error('civilite')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="first_name">Prénom *</label>
                <input type="text" name="first_name" id="first_name" class="form-input" value="{{ old('first_name', $member?->first_name ?? '') }}" required>
                @error('first_name')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="last_name">Nom *</label>
                <input type="text" name="last_name" id="last_name" class="form-input" value="{{ old('last_name', $member?->last_name ?? '') }}" required>
                @error('last_name')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="birth_date">Date de naissance</label>
                <input type="date" name="birth_date" id="birth_date" class="form-input" value="{{ old('birth_date', $member?->birth_date?->format('Y-m-d') ?? '') }}">
                @error('birth_date')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="profession">Profession</label>
                <input type="text" name="profession" id="profession" class="form-input" value="{{ old('profession', $member?->profession ?? '') }}">
                @error('profession')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</div>

{{-- Carte 2 — Contact --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Contact</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="email">Email *</label>
            <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $member?->email ?? '') }}" required>
            @error('email')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="mobile">Mobile</label>
                <input type="tel" name="mobile" id="mobile" class="form-input" placeholder="06 XX XX XX XX" value="{{ old('mobile', $member?->mobile ?? '') }}">
                @error('mobile')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="telephone_fixe">Téléphone fixe</label>
                <input type="tel" name="telephone_fixe" id="telephone_fixe" class="form-input" placeholder="01 XX XX XX XX" value="{{ old('telephone_fixe', $member?->telephone_fixe ?? '') }}">
                @error('telephone_fixe')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</div>

{{-- Carte 3 — Adresse --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Adresse</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="address">Adresse</label>
            <input type="text" name="address" id="address" class="form-input" value="{{ old('address', $member?->address ?? '') }}">
            @error('address')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
        <div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="postal_code">Code postal</label>
                <input type="text" name="postal_code" id="postal_code" class="form-input" value="{{ old('postal_code', $member?->postal_code ?? '') }}">
                @error('postal_code')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="city">Ville</label>
                <input type="text" name="city" id="city" class="form-input" value="{{ old('city', $member?->city ?? '') }}">
                @error('city')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="country">Pays</label>
                <input type="text" name="country" id="country" class="form-input" value="{{ old('country', $member?->country ?? 'France') }}">
                @error('country')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</div>

{{-- Carte 4 — Préférences --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Préférences</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="contact_type">Type de contact *</label>
            <select name="contact_type" id="contact_type" class="form-input" required>
                <option value="individuel" {{ old('contact_type', $member?->contact_type ?? 'individuel') === 'individuel' ? 'selected' : '' }}>Individuel</option>
                <option value="organisation" {{ old('contact_type', $member?->contact_type ?? '') === 'organisation' ? 'selected' : '' }}>Organisation</option>
            </select>
            @error('contact_type')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="newsletter_subscribed" value="1" {{ old('newsletter_subscribed', $member?->newsletter_subscribed ?? true) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                <span class="form-label" style="margin-bottom: 0;">Inscrit à la newsletter</span>
            </label>
        </div>
        <div class="form-group">
            <label class="form-label" for="interests">Intérêts</label>
            <textarea name="interests" id="interests" rows="3" class="form-input">{{ old('interests', $member?->interests ?? '') }}</textarea>
            @error('interests')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- Carte 5 — Statut & RGPD --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Statut &amp; RGPD</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $member?->is_active ?? true) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                <span class="form-label" style="margin-bottom: 0;">Contact actif</span>
            </label>
        </div>
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="consent_communication" value="1" {{ old('consent_communication', $member?->consent_communication ?? false) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                <span class="form-label" style="margin-bottom: 0;">Autorise les communications associatives</span>
            </label>
        </div>
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="consent_image" value="1" {{ old('consent_image', $member?->consent_image ?? false) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                <span class="form-label" style="margin-bottom: 0;">Autorise l'utilisation de son image</span>
            </label>
        </div>
    </div>
</div>
