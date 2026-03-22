@extends('layouts.admin')

@section('title', 'Nouveau modele d\'export')

@section('breadcrumb')
    <a href="{{ route('admin.import-export.index') }}">Import / Export</a>
    <span>/</span>
    <span>Nouveau modele d'export</span>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('admin.import-export.export-template.store') }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 class="card-title">Creer un modele d'export</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <div class="form-group">
                            <label for="name" class="form-label">Nom du modele *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="form-input @error('name') is-invalid @enderror"
                                   placeholder="Ex: Export contacts complet" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="type" class="form-label">Type de donnees *</label>
                            <select name="type" id="type" class="form-input @error('type') is-invalid @enderror" required>
                                <option value="">-- Selectionner --</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-input">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                Definir comme modele par defaut
                            </label>
                        </div>
                    </div>

                    <div>
                        <div class="form-group">
                            <label class="form-label">Colonnes a exporter *</label>
                            <div id="columns-container" style="border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; max-height: 400px; overflow-y: auto;">
                                <p style="color: #6b7280; font-size: 0.875rem;">Selectionnez d'abord un type de donnees.</p>
                            </div>
                            @error('columns')<div class="invalid-feedback" style="display: block;">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.import-export.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Creer le modele</button>
            </div>
        </form>
    </div>

    <script>
        const columnsData = {
            members: {
                id: 'ID',
                member_number: 'Numero adherent',
                title: 'Civilite',
                first_name: 'Prenom',
                last_name: 'Nom',
                email: 'Email',
                phone: 'Telephone',
                address: 'Adresse',
                postal_code: 'Code postal',
                city: 'Ville',
                country: 'Pays',
                status: 'Statut',
                newsletter: 'Newsletter',
                created_at: 'Date creation'
            },
            memberships: {
                id: 'ID',
                member_name: 'Membre',
                member_email: 'Email',
                type: 'Type',
                amount: 'Montant',
                start_date: 'Date debut',
                end_date: 'Date fin',
                payment_method: 'Mode paiement',
                status: 'Statut',
                created_at: 'Date creation'
            },
            donations: {
                id: 'ID',
                member_name: 'Donateur',
                member_email: 'Email',
                amount: 'Montant',
                donation_date: 'Date don',
                payment_method: 'Mode paiement',
                campaign: 'Campagne',
                receipt_sent: 'Recu envoye',
                created_at: 'Date creation'
            },
            volunteer: {
                id: 'ID',
                title: 'Titre',
                type: 'Type activite',
                date: 'Date',
                location: 'Lieu',
                participants: 'Participants',
                hours: 'Heures',
                status: 'Statut'
            }
        };

        document.getElementById('type').addEventListener('change', function() {
            const type = this.value;
            const container = document.getElementById('columns-container');

            if (!type || !columnsData[type]) {
                container.innerHTML = '<p style="color: #6b7280; font-size: 0.875rem;">Selectionnez d\'abord un type de donnees.</p>';
                return;
            }

            const columns = columnsData[type];
            let html = '<div style="display: flex; flex-direction: column; gap: 0.5rem;">';

            for (const [key, label] of Object.entries(columns)) {
                html += `
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="columns[]" value="${key}" checked>
                        <span>${label}</span>
                    </label>
                `;
            }

            html += '</div>';
            container.innerHTML = html;
        });
    </script>
@endsection
