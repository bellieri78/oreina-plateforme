<div class="form-group">
    <label class="form-label" for="name">Nom du produit *</label>
    <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $product->name ?? '') }}" required>
    @error('name')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    <div class="form-group">
        <label class="form-label" for="slug">Slug</label>
        <input type="text" name="slug" id="slug" class="form-input" value="{{ old('slug', $product->slug ?? '') }}" placeholder="Genere automatiquement si vide">
        @error('slug')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="sku">Code article (SKU)</label>
        <input type="text" name="sku" id="sku" class="form-input" value="{{ old('sku', $product->sku ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="product_type">Type de produit *</label>
    <select name="product_type" id="product_type" class="form-input" required onchange="toggleProductFields()">
        <option value="">-- Selectionner --</option>
        @foreach(\App\Models\Product::getTypeOptions() as $value => $label)
            <option value="{{ $value }}" {{ old('product_type', $product->product_type ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @error('product_type')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
</div>

<div class="form-group">
    <label class="form-label" for="description">Description</label>
    <textarea name="description" id="description" class="form-input" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
    <div class="form-group">
        <label class="form-label" for="price">Prix (EUR) *</label>
        <input type="number" step="0.01" name="price" id="price" class="form-input" value="{{ old('price', $product->price ?? '0') }}" required>
        @error('price')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="year">Annee</label>
        <input type="number" name="year" id="year" class="form-input" value="{{ old('year', $product->year ?? '') }}" min="2000" max="2100">
    </div>
    <div class="form-group">
        <label class="form-label" for="stock_quantity">Stock</label>
        <input type="number" name="stock_quantity" id="stock_quantity" class="form-input" value="{{ old('stock_quantity', $product->stock_quantity ?? '') }}" min="0">
    </div>
</div>

<!-- Magazine/Hors-serie fields -->
<div id="magazineFields" style="display: none;">
    <div class="form-group">
        <label class="form-label" for="issue_number">Numero</label>
        <input type="number" name="issue_number" id="issue_number" class="form-input" value="{{ old('issue_number', $product->issue_number ?? '') }}">
    </div>
</div>

<!-- Rencontre fields -->
<div id="rencontreFields" style="display: none;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
            <label class="form-label" for="event_date">Date de l'evenement</label>
            <input type="date" name="event_date" id="event_date" class="form-input" value="{{ old('event_date', isset($product->event_date) ? $product->event_date->format('Y-m-d') : '') }}">
        </div>
        <div class="form-group">
            <label class="form-label" for="event_location">Lieu</label>
            <input type="text" name="event_location" id="event_location" class="form-input" value="{{ old('event_location', $product->event_location ?? '') }}">
        </div>
    </div>
</div>

<div class="form-group">
    <label class="form-label">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
        Produit actif
    </label>
</div>

<script>
function toggleProductFields() {
    const type = document.getElementById('product_type').value;
    document.getElementById('magazineFields').style.display = (type === 'magazine' || type === 'hors_serie') ? 'block' : 'none';
    document.getElementById('rencontreFields').style.display = (type === 'rencontre') ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', toggleProductFields);
</script>
