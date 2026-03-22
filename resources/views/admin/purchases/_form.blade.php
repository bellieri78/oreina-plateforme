<div class="form-group">
    <label class="form-label" for="member_id">Membre *</label>
    <select name="member_id" id="member_id" class="form-input" required>
        <option value="">-- Selectionner --</option>
        @foreach($members as $member)
            <option value="{{ $member->id }}" {{ old('member_id', $purchase->member_id ?? '') == $member->id ? 'selected' : '' }}>
                {{ $member->last_name }} {{ $member->first_name }} ({{ $member->email }})
            </option>
        @endforeach
    </select>
    @error('member_id')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
</div>

<div class="form-group">
    <label class="form-label" for="product_id">Produit *</label>
    <select name="product_id" id="product_id" class="form-input" required onchange="updatePrice()">
        <option value="">-- Selectionner --</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ old('product_id', $purchase->product_id ?? '') == $product->id ? 'selected' : '' }}>
                {{ $product->name }} ({{ number_format($product->price, 2, ',', ' ') }} EUR)
            </option>
        @endforeach
    </select>
    @error('product_id')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
    <div class="form-group">
        <label class="form-label" for="quantity">Quantite *</label>
        <input type="number" name="quantity" id="quantity" class="form-input" value="{{ old('quantity', $purchase->quantity ?? 1) }}" min="1" required onchange="updateTotal()">
        @error('quantity')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="unit_price">Prix unitaire (EUR) *</label>
        <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-input" value="{{ old('unit_price', $purchase->unit_price ?? '0') }}" required onchange="updateTotal()">
        @error('unit_price')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Total</label>
        <input type="text" id="total_display" class="form-input" readonly style="background: #f3f4f6;">
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
    <div class="form-group">
        <label class="form-label" for="purchase_date">Date d'achat *</label>
        <input type="date" name="purchase_date" id="purchase_date" class="form-input" value="{{ old('purchase_date', isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
    </div>
    <div class="form-group">
        <label class="form-label" for="payment_method">Moyen de paiement</label>
        <select name="payment_method" id="payment_method" class="form-input">
            <option value="">-- Selectionner --</option>
            <option value="helloasso" {{ old('payment_method', $purchase->payment_method ?? '') === 'helloasso' ? 'selected' : '' }}>HelloAsso</option>
            <option value="virement" {{ old('payment_method', $purchase->payment_method ?? '') === 'virement' ? 'selected' : '' }}>Virement</option>
            <option value="cheque" {{ old('payment_method', $purchase->payment_method ?? '') === 'cheque' ? 'selected' : '' }}>Cheque</option>
            <option value="especes" {{ old('payment_method', $purchase->payment_method ?? '') === 'especes' ? 'selected' : '' }}>Especes</option>
            <option value="carte" {{ old('payment_method', $purchase->payment_method ?? '') === 'carte' ? 'selected' : '' }}>Carte bancaire</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="payment_reference">Reference</label>
        <input type="text" name="payment_reference" id="payment_reference" class="form-input" value="{{ old('payment_reference', $purchase->payment_reference ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="notes">Notes</label>
    <textarea name="notes" id="notes" class="form-input" rows="2">{{ old('notes', $purchase->notes ?? '') }}</textarea>
</div>

<script>
function updatePrice() {
    const select = document.getElementById('product_id');
    const option = select.options[select.selectedIndex];
    if (option && option.dataset.price) {
        document.getElementById('unit_price').value = option.dataset.price;
        updateTotal();
    }
}

function updateTotal() {
    const qty = parseFloat(document.getElementById('quantity').value) || 0;
    const price = parseFloat(document.getElementById('unit_price').value) || 0;
    const total = qty * price;
    document.getElementById('total_display').value = total.toFixed(2) + ' EUR';
}

document.addEventListener('DOMContentLoaded', updateTotal);
</script>
