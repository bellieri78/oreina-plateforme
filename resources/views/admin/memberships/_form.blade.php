<div class="form-group">
    <label class="form-label" for="member_id">Membre *</label>
    <select name="member_id" id="member_id" class="form-input" required>
        <option value="">-- Selectionner --</option>
        @foreach($members as $member)
            <option value="{{ $member->id }}" {{ old('member_id', $membership->member_id ?? '') == $member->id ? 'selected' : '' }}>
                {{ $member->last_name }} {{ $member->first_name }} ({{ $member->email }})
            </option>
        @endforeach
    </select>
    @error('member_id')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
</div>

<div class="form-group">
    <label class="form-label" for="membership_type_id">Type d'adhesion</label>
    <select name="membership_type_id" id="membership_type_id" class="form-input">
        <option value="">-- Selectionner --</option>
        @foreach($membershipTypes as $type)
            <option value="{{ $type->id }}" {{ old('membership_type_id', $membership->membership_type_id ?? '') == $type->id ? 'selected' : '' }}>
                {{ $type->name }} ({{ number_format($type->price, 0, ',', ' ') }} EUR)
            </option>
        @endforeach
    </select>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
    <div class="form-group">
        <label class="form-label" for="amount_paid">Montant (EUR) *</label>
        <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-input" value="{{ old('amount_paid', $membership->amount_paid ?? '30') }}" required>
        @error('amount_paid')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="start_date">Date debut *</label>
        <input type="date" name="start_date" id="start_date" class="form-input" value="{{ old('start_date', isset($membership) ? $membership->start_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
    </div>
    <div class="form-group">
        <label class="form-label" for="end_date">Date fin *</label>
        <input type="date" name="end_date" id="end_date" class="form-input" value="{{ old('end_date', isset($membership) ? $membership->end_date->format('Y-m-d') : now()->addYear()->format('Y-m-d')) }}" required>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    <div class="form-group">
        <label class="form-label" for="payment_method">Moyen de paiement</label>
        <select name="payment_method" id="payment_method" class="form-input">
            <option value="">-- Selectionner --</option>
            <option value="helloasso" {{ old('payment_method', $membership->payment_method ?? '') === 'helloasso' ? 'selected' : '' }}>HelloAsso</option>
            <option value="virement" {{ old('payment_method', $membership->payment_method ?? '') === 'virement' ? 'selected' : '' }}>Virement</option>
            <option value="cheque" {{ old('payment_method', $membership->payment_method ?? '') === 'cheque' ? 'selected' : '' }}>Cheque</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="payment_reference">Reference</label>
        <input type="text" name="payment_reference" id="payment_reference" class="form-input" value="{{ old('payment_reference', $membership->payment_reference ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="notes">Notes</label>
    <textarea name="notes" id="notes" class="form-input" rows="2">{{ old('notes', $membership->notes ?? '') }}</textarea>
</div>

<div class="form-group">
    <label class="form-label" for="lepis_format">Format Lepis *</label>
    <select name="lepis_format" id="lepis_format" class="form-input" required>
        <option value="">-- Selectionner --</option>
        <option value="paper" {{ old('lepis_format', $membership->lepis_format ?? '') === 'paper' ? 'selected' : '' }}>Papier</option>
        <option value="digital" {{ old('lepis_format', $membership->lepis_format ?? '') === 'digital' ? 'selected' : '' }}>Numerique</option>
    </select>
    @error('lepis_format')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
    <small style="color: #6b7280; font-size: 0.875rem;">Choix fige pour la duree de l'adhesion. Redecide au renouvellement.</small>
</div>
