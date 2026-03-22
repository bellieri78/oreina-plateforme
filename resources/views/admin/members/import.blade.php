@extends('layouts.admin')

@section('title', 'Importer des contacts')
@section('breadcrumb')
    <a href="{{ route('admin.members.index') }}">Contacts</a>
    <span>/</span>
    <span>Importer</span>
@endsection

@section('content')
    <div class="card" style="max-width: 700px;">
        <div class="card-header">
            <h3 class="card-title">Importer des contacts</h3>
        </div>

        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.members.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="import-info">
                    <h4>Format du fichier CSV</h4>
                    <p>Le fichier doit contenir une ligne d'en-tete avec les noms de colonnes.</p>

                    <table class="format-table">
                        <thead>
                            <tr>
                                <th>Colonne</th>
                                <th>Noms acceptes</th>
                                <th>Requis</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nom</td>
                                <td><code>nom</code>, <code>last_name</code>, <code>lastname</code></td>
                                <td><span class="badge badge-danger">Oui</span></td>
                            </tr>
                            <tr>
                                <td>Prenom</td>
                                <td><code>prenom</code>, <code>first_name</code>, <code>firstname</code></td>
                                <td><span class="badge badge-danger">Oui</span></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><code>email</code>, <code>e-mail</code>, <code>mail</code></td>
                                <td><span class="badge badge-danger">Oui</span></td>
                            </tr>
                            <tr>
                                <td>Telephone</td>
                                <td><code>telephone</code>, <code>phone</code>, <code>tel</code>, <code>mobile</code></td>
                                <td><span class="badge badge-secondary">Non</span></td>
                            </tr>
                            <tr>
                                <td>Adresse</td>
                                <td><code>adresse</code>, <code>address</code>, <code>rue</code></td>
                                <td><span class="badge badge-secondary">Non</span></td>
                            </tr>
                            <tr>
                                <td>Code postal</td>
                                <td><code>code postal</code>, <code>cp</code>, <code>postal_code</code>, <code>zip</code></td>
                                <td><span class="badge badge-secondary">Non</span></td>
                            </tr>
                            <tr>
                                <td>Ville</td>
                                <td><code>ville</code>, <code>city</code>, <code>commune</code></td>
                                <td><span class="badge badge-secondary">Non</span></td>
                            </tr>
                            <tr>
                                <td>Pays</td>
                                <td><code>pays</code>, <code>country</code></td>
                                <td><span class="badge badge-secondary">Non</span></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="import-note">
                        <strong>Notes :</strong>
                        <ul>
                            <li>Separateur accepte : point-virgule (;) ou virgule (,)</li>
                            <li>Encodage : UTF-8 recommande</li>
                            <li>Si un email existe deja, le contact sera mis a jour</li>
                            <li>Taille maximale : 2 Mo</li>
                        </ul>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label class="form-label">Fichier CSV</label>
                    <input type="file" name="file" accept=".csv,.txt" class="form-input" required>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                        Importer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
    .import-info {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1.25rem;
    }
    .import-info h4 {
        margin: 0 0 0.5rem;
        font-size: 1rem;
        color: #374151;
    }
    .import-info p {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
    .format-table {
        width: 100%;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
    .format-table th {
        text-align: left;
        padding: 0.5rem;
        background: #e5e7eb;
        font-weight: 500;
    }
    .format-table td {
        padding: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .format-table code {
        background: white;
        padding: 0.1rem 0.3rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        color: #e11d48;
    }
    .import-note {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 0.375rem;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }
    .import-note ul {
        margin: 0.5rem 0 0 1.25rem;
        padding: 0;
    }
    .import-note li {
        color: #92400e;
    }
    .form-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }
    </style>
@endsection
