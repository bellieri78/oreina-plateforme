@php
    $hubRoutes = [
        '/' => 'Accueil',
        '/a-propos' => 'Association',
        '/actualites' => 'Actualités',
        '/evenements' => 'Événements',
        '/lepis' => 'Lepis',
        '/lepis/bulletins' => 'Lepis — bulletins',
        '/revue' => 'Chersotis (revue)',
        '/adhesion' => 'Adhésion',
        '/contact' => 'Contact',
        '/connexion' => 'Connexion',
        '/inscription' => 'Inscription',
    ];
    $alreadyHasChildren = $menuItem && $menuItem->exists && $menuItem->children()->exists();
@endphp

{{-- Carte 1 — Identité --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Identité</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="label">Libellé *</label>
            <input type="text" name="label" id="label" class="form-input" value="{{ old('label', $menuItem?->label ?? '') }}" required>
            @error('label')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="location">Localisation *</label>
            <select name="location" id="location" class="form-input" required>
                <option value="header" {{ old('location', $menuItem?->location ?? $defaultLocation) === 'header' ? 'selected' : '' }}>Header</option>
                <option value="footer" {{ old('location', $menuItem?->location ?? $defaultLocation) === 'footer' ? 'selected' : '' }}>Footer</option>
            </select>
            @error('location')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        @if(! $alreadyHasChildren)
            <div class="form-group" id="parent-group" style="{{ old('location', $menuItem?->location ?? $defaultLocation) === 'footer' ? 'display: none;' : '' }}">
                <label class="form-label" for="parent_id">Parent (optionnel — uniquement pour le header)</label>
                <select name="parent_id" id="parent_id" class="form-input">
                    <option value="">— Aucun (item racine) —</option>
                    @foreach($availableParents as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $menuItem?->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->label }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        @endif
    </div>
</div>

{{-- Carte 2 — Cible --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Cible</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="hub_route_helper">Page hub (optionnel — pré-remplit l'URL)</label>
            <select id="hub_route_helper" class="form-input">
                <option value="">— Choisir une page... —</option>
                @foreach($hubRoutes as $path => $label)
                    <option value="{{ $path }}">{{ $label }} — {{ $path }}</option>
                @endforeach
            </select>
            <small style="color: #6b7280; font-size: 0.8125rem;">Sélectionne une page pour pré-remplir le champ URL ci-dessous. Tu peux ensuite l'éditer librement.</small>
        </div>

        <div class="form-group">
            <label class="form-label" for="url">URL *</label>
            <input type="text" name="url" id="url" class="form-input" value="{{ old('url', $menuItem?->url ?? '') }}" required maxlength="500">
            <small style="color: #6b7280; font-size: 0.8125rem;">Path interne (<code>/actualites</code>), ancre (<code>#section</code>), ou URL externe (<code>https://...</code>).</small>
            @error('url')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="open_in_new_tab" value="1" {{ old('open_in_new_tab', $menuItem?->open_in_new_tab ?? false) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                <span class="form-label" style="margin-bottom: 0;">Ouvrir dans un nouvel onglet</span>
            </label>
        </div>
    </div>
</div>

{{-- Carte 3 — Affichage --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Affichage</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $menuItem?->is_active ?? true) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                <span class="form-label" style="margin-bottom: 0;">Item actif (visible côté hub)</span>
            </label>
        </div>

        <div class="form-group">
            <label class="form-label" for="sort_order">Ordre de tri</label>
            <input type="number" name="sort_order" id="sort_order" class="form-input" value="{{ old('sort_order', $menuItem?->sort_order ?? 0) }}" min="0" style="max-width: 120px;">
            <small style="color: #6b7280; font-size: 0.8125rem;">Entier croissant. Les items sont affichés du plus petit au plus grand. Les boutons ↑↓ de l'index permettent de modifier en swap.</small>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hubHelper = document.getElementById('hub_route_helper');
        const urlInput = document.getElementById('url');
        const locationSelect = document.getElementById('location');
        const parentGroup = document.getElementById('parent-group');

        if (hubHelper && urlInput) {
            hubHelper.addEventListener('change', function () {
                if (this.value) {
                    urlInput.value = this.value;
                }
            });
            urlInput.addEventListener('input', function () {
                if (hubHelper.value && urlInput.value !== hubHelper.value) {
                    hubHelper.value = '';
                }
            });
        }

        if (locationSelect && parentGroup) {
            locationSelect.addEventListener('change', function () {
                parentGroup.style.display = this.value === 'footer' ? 'none' : '';
            });
        }
    });
</script>
@endpush
