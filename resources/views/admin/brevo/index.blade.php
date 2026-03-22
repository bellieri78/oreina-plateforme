@extends('layouts.admin')

@section('title', 'Synchronisation Brevo')

@section('breadcrumb')
    <span>Brevo</span>
@endsection

@section('content')
    @if(!$isConfigured)
        <div class="card" style="border-color: #fbbf24;">
            <div class="card-body" style="background: #fef3c7;">
                <h3 style="color: #92400e; margin-bottom: 0.5rem;">Configuration requise</h3>
                <p style="color: #92400e; margin-bottom: 1rem;">
                    L'integration Brevo n'est pas configuree. Ajoutez votre cle API dans le fichier <code>.env</code> :
                </p>
                <pre style="background: white; padding: 1rem; border-radius: 4px; color: #92400e;">BREVO_API_KEY=votre_cle_api</pre>
                <p style="color: #92400e; margin-top: 1rem; margin-bottom: 0;">
                    Vous pouvez obtenir une cle API dans votre compte Brevo : <br>
                    <a href="https://app.brevo.com/settings/keys/api" target="_blank" style="color: #92400e;">https://app.brevo.com/settings/keys/api</a>
                </p>
            </div>
        </div>
    @else
        @if($error)
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <strong>Erreur de connexion :</strong> {{ $error }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="stats-grid" style="margin-bottom: 1.5rem;">
            <div class="stat-card">
                <div class="stat-value">{{ $stats['total_members'] }}</div>
                <div class="stat-label">Contacts total</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['with_email'] }}</div>
                <div class="stat-label">Avec email</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['newsletter_subscribers'] }}</div>
                <div class="stat-label">Abonnes newsletter</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['active_members'] }}</div>
                <div class="stat-label">Adherents actifs</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
            {{-- Sync to existing list --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Exporter vers Brevo</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.brevo.sync') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="list_id" class="form-label">Liste Brevo *</label>
                            <select name="list_id" id="list_id" class="form-input" required>
                                <option value="">-- Selectionner --</option>
                                @foreach($lists as $list)
                                    <option value="{{ $list['id'] }}">
                                        {{ $list['name'] }} ({{ $list['uniqueSubscribers'] ?? 0 }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sync_type" class="form-label">Contacts a exporter *</label>
                            <select name="sync_type" id="sync_type" class="form-input" required>
                                <option value="all">Tous avec email ({{ $stats['with_email'] }})</option>
                                <option value="newsletter">Newsletter ({{ $stats['newsletter_subscribers'] }})</option>
                                <option value="active">Adherents actifs ({{ $stats['active_members'] }})</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Exporter</button>
                    </form>
                </div>
            </div>

            {{-- Import from Brevo --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Importer depuis Brevo</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.brevo.import') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="import_list_id" class="form-label">Liste Brevo *</label>
                            <select name="list_id" id="import_list_id" class="form-input" required>
                                <option value="">-- Selectionner --</option>
                                @foreach($lists as $list)
                                    <option value="{{ $list['id'] }}">
                                        {{ $list['name'] }} ({{ $list['uniqueSubscribers'] ?? 0 }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="import_mode" class="form-label">Mode d'import *</label>
                            <select name="import_mode" id="import_mode" class="form-input" required>
                                <option value="create_only">Creer uniquement (ignorer existants)</option>
                                <option value="update_only">MAJ uniquement (ignorer nouveaux)</option>
                                <option value="create_update">Creer et mettre a jour</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary">Importer</button>
                    </form>
                </div>
            </div>

            {{-- Create new list and sync --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nouvelle liste Brevo</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.brevo.export-new') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="list_name" class="form-label">Nom de la liste *</label>
                            <input type="text" name="list_name" id="list_name" class="form-input"
                                   placeholder="Ex: Newsletter 2026" required>
                        </div>
                        <div class="form-group">
                            <label for="sync_type_new" class="form-label">Contacts a ajouter *</label>
                            <select name="sync_type" id="sync_type_new" class="form-input" required>
                                <option value="all">Tous avec email</option>
                                <option value="newsletter">Newsletter</option>
                                <option value="active">Adherents actifs</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Creer et exporter</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Existing lists --}}
        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title">Listes Brevo ({{ count($lists) }})</h3>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Contacts</th>
                            <th>Dossier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lists as $list)
                            <tr>
                                <td>{{ $list['id'] }}</td>
                                <td>{{ $list['name'] }}</td>
                                <td>{{ $list['uniqueSubscribers'] ?? 0 }}</td>
                                <td>{{ $list['folderId'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: #6b7280;">Aucune liste trouvee.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sync history --}}
        @if(!empty($syncHistory))
            <div class="card" style="margin-top: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Historique des synchronisations</h3>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Contacts</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($syncHistory as $sync)
                                <tr>
                                    <td>{{ $sync['date'] }}</td>
                                    <td>{{ $sync['type'] }}</td>
                                    <td>{{ $sync['count'] }}</td>
                                    <td>
                                        @if($sync['success'])
                                            <span class="badge badge-success">Succes</span>
                                            @if(!empty($sync['details']))
                                                <span style="font-size: 0.75rem; color: #6b7280; margin-left: 0.5rem;">{{ $sync['details'] }}</span>
                                            @endif
                                        @else
                                            <span class="badge badge-danger">Echec</span>
                                            @if($sync['error'])
                                                <span style="font-size: 0.75rem; color: #dc2626;">{{ $sync['error'] }}</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Info --}}
        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title">Informations</h3>
            </div>
            <div class="card-body">
                <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Attributs synchronises</h4>
                <p style="color: #6b7280; font-size: 0.875rem;">Les attributs suivants sont envoyes vers Brevo :</p>
                <ul style="color: #6b7280; font-size: 0.875rem; margin: 0.5rem 0 1rem 1.5rem;">
                    <li><strong>PRENOM</strong> : Prenom du contact</li>
                    <li><strong>NOM</strong> : Nom de famille</li>
                    <li><strong>MEMBRE_NUMERO</strong> : Numero d'adherent</li>
                    <li><strong>VILLE</strong> : Ville</li>
                    <li><strong>CODE_POSTAL</strong> : Code postal</li>
                    <li><strong>DATE_ADHESION</strong> : Date de premiere adhesion</li>
                    <li><strong>DATE_EXPIRATION</strong> : Date d'expiration de l'adhesion</li>
                    <li><strong>EST_ADHERENT</strong> : Adhesion active (true/false)</li>
                    <li><strong>EST_DONATEUR</strong> : A fait un don (true/false)</li>
                    <li><strong>NEWSLETTER</strong> : Abonne newsletter (true/false)</li>
                </ul>

                <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Webhooks</h4>
                <p style="color: #6b7280; font-size: 0.875rem;">
                    Pour synchroniser les desabonnements, configurez un webhook dans Brevo vers :
                </p>
                <pre style="background: #f3f4f6; padding: 0.75rem; border-radius: 4px; font-size: 0.875rem; overflow-x: auto;">{{ url('/api/webhooks/brevo') }}</pre>
            </div>
        </div>
    @endif
@endsection
