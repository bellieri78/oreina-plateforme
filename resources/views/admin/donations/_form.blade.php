<div class="form-group">
    <label class="form-label" for="member_id">Adherent lie (optionnel)</label>
    <select name="member_id" id="member_id" class="form-input">
        <option value="">-- Selectionner --</option>
        @foreach($members as $member)
            <option value="{{ $member->id }}" {{ old('member_id', $donation->member_id ?? '') == $member->id ? 'selected' : '' }}>
                {{ $member->last_name }} {{ $member->first_name }} ({{ $member->email }})
            </option>
        @endforeach
    </select>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    <div class="form-group">
        <label class="form-label" for="donor_name">Nom du donateur *</label>
        <input type="text" name="donor_name" id="donor_name" class="form-input" value="{{ old('donor_name', $donation->donor_name ?? '') }}" required>
        @error('donor_name')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="donor_email">Email *</label>
        <input type="email" name="donor_email" id="donor_email" class="form-input" value="{{ old('donor_email', $donation->donor_email ?? '') }}" required>
        @error('donor_email')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="donor_address">Adresse</label>
    <input type="text" name="donor_address" id="donor_address" class="form-input" value="{{ old('donor_address', $donation->donor_address ?? '') }}">
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
    <div class="form-group">
        <label class="form-label" for="donor_postal_code">Code postal</label>
        <input type="text" name="donor_postal_code" id="donor_postal_code" class="form-input" value="{{ old('donor_postal_code', $donation->donor_postal_code ?? '') }}">
    </div>
    <div class="form-group">
        <label class="form-label" for="donor_city">Ville</label>
        <input type="text" name="donor_city" id="donor_city" class="form-input" value="{{ old('donor_city', $donation->donor_city ?? '') }}">
    </div>
</div>

<hr style="margin: 1.5rem 0; border: none; border-top: 1px solid #e5e7eb;">

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    <div class="form-group">
        <label class="form-label" for="amount">Montant (EUR) *</label>
        <input type="number" step="0.01" name="amount" id="amount" class="form-input" value="{{ old('amount', $donation->amount ?? '') }}" required>
        @error('amount')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="donation_date">Date du don *</label>
        <input type="date" name="donation_date" id="donation_date" class="form-input" value="{{ old('donation_date', isset($donation) ? $donation->donation_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
        @error('donation_date')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    <div class="form-group">
        <label class="form-label" for="payment_method">Moyen de paiement</label>
        <select name="payment_method" id="payment_method" class="form-input">
            <option value="">-- Selectionner --</option>
            <option value="helloasso" {{ old('payment_method', $donation->payment_method ?? '') === 'helloasso' ? 'selected' : '' }}>HelloAsso</option>
            <option value="virement" {{ old('payment_method', $donation->payment_method ?? '') === 'virement' ? 'selected' : '' }}>Virement</option>
            <option value="cheque" {{ old('payment_method', $donation->payment_method ?? '') === 'cheque' ? 'selected' : '' }}>Cheque</option>
            <option value="especes" {{ old('payment_method', $donation->payment_method ?? '') === 'especes' ? 'selected' : '' }}>Especes</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="payment_reference">Reference paiement</label>
        <input type="text" name="payment_reference" id="payment_reference" class="form-input" value="{{ old('payment_reference', $donation->payment_reference ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="campaign">Campagne</label>
    <input type="text" name="campaign" id="campaign" class="form-input" value="{{ old('campaign', $donation->campaign ?? '') }}">
</div>

<div class="form-group">
    <label class="form-label" for="notes">Notes</label>
    <textarea name="notes" id="notes" class="form-input" rows="3">{{ old('notes', $donation->notes ?? '') }}</textarea>
</div>

@if(isset($donation) && $donation->exists)
<div class="form-group">
    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="tax_receipt_sent" value="1" {{ old('tax_receipt_sent', $donation->tax_receipt_sent ?? false) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
        <span class="form-label" style="margin-bottom: 0;">Recu fiscal envoye</span>
    </label>
</div>
@endif
