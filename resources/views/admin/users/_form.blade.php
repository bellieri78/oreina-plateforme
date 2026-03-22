<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div>
        <div class="form-group">
            <label class="form-label" for="name">Nom complet *</label>
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name ?? '') }}" required>
            @error('name')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email *</label>
            <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email ?? '') }}" required>
            @error('email')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="phone">Telephone</label>
            <input type="text" name="phone" id="phone" class="form-input" value="{{ old('phone', $user->phone ?? '') }}">
        </div>
    </div>

    <div>
        <div class="form-group">
            <label class="form-label" for="role">Role *</label>
            <select name="role" id="role" class="form-input" required>
                @foreach(\App\Models\User::getRoles() as $key => $label)
                    <option value="{{ $key }}" {{ old('role', $user->role ?? 'user') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
                Admin: acces complet | Editeur: gestion revue | Reviewer: evaluation | Auteur: soumission
            </p>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">
                Mot de passe {{ isset($user) ? '(laisser vide pour conserver)' : '*' }}
            </label>
            <input type="password" name="password" id="password" class="form-input" {{ isset($user) ? '' : 'required' }}>
            @error('password')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-input">
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                <span>Compte actif</span>
            </label>
            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
                Un compte inactif ne peut pas se connecter.
            </p>
        </div>
    </div>
</div>
