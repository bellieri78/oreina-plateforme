@extends('layouts.admin')

@section('title', 'Import / Export')

@section('breadcrumb')
    <span>Import / Export</span>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="stats-grid" style="margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_imports'] }}</div>
            <div class="stat-label">Imports realises</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['successful_imports'] }}</div>
            <div class="stat-label">Imports reussis</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_exports'] }}</div>
            <div class="stat-label">Exports realises</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['import_templates'] + $stats['export_templates'] }}</div>
            <div class="stat-label">Modeles</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        {{-- Import templates --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Modeles d'import</h3>
                <a href="{{ route('admin.import-export.import-template.create') }}" class="btn btn-primary btn-sm">Nouveau</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($importTemplates as $template)
                            <tr>
                                <td>
                                    {{ $template->name }}
                                    @if($template->is_default)
                                        <span class="badge badge-primary" style="font-size: 0.65rem;">Defaut</span>
                                    @endif
                                </td>
                                <td>{{ \App\Models\ImportTemplate::getTypes()[$template->type] ?? $template->type }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('admin.import-export.import-template.edit', $template) }}" class="btn btn-sm btn-secondary">Modifier</a>
                                        <form action="{{ route('admin.import-export.import-template.destroy', $template) }}" method="POST" style="display: inline;"
                                              onsubmit="return confirm('Supprimer ce modele ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Suppr.</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #6b7280;">Aucun modele d'import.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Export templates --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Modeles d'export</h3>
                <a href="{{ route('admin.import-export.export-template.create') }}" class="btn btn-primary btn-sm">Nouveau</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exportTemplates as $template)
                            <tr>
                                <td>
                                    {{ $template->name }}
                                    @if($template->is_default)
                                        <span class="badge badge-primary" style="font-size: 0.65rem;">Defaut</span>
                                    @endif
                                </td>
                                <td>{{ \App\Models\ExportTemplate::getTypes()[$template->type] ?? $template->type }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('admin.import-export.export', $template) }}" class="btn btn-sm btn-primary">Exporter</a>
                                        <a href="{{ route('admin.import-export.export-template.edit', $template) }}" class="btn btn-sm btn-secondary">Modifier</a>
                                        <form action="{{ route('admin.import-export.export-template.destroy', $template) }}" method="POST" style="display: inline;"
                                              onsubmit="return confirm('Supprimer ce modele ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Suppr.</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #6b7280;">Aucun modele d'export.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent imports --}}
    <div class="card" style="margin-top: 1.5rem;">
        <div class="card-header">
            <h3 class="card-title">Historique des imports</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Fichier</th>
                        <th>Type</th>
                        <th>Lignes</th>
                        <th>Resultats</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentImports as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->filename }}</td>
                            <td>{{ \App\Models\ImportTemplate::getTypes()[$log->type] ?? $log->type }}</td>
                            <td>{{ $log->total_rows }}</td>
                            <td>
                                <span style="color: #059669;">{{ $log->imported_rows }} crees</span>,
                                <span style="color: #2563eb;">{{ $log->updated_rows }} MAJ</span>,
                                <span style="color: #dc2626;">{{ $log->error_rows }} erreurs</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $log->status_color }}">{{ $log->status_label }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.import-export.import-log', $log) }}" class="btn btn-sm btn-secondary">Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #6b7280;">Aucun import enregistre.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent exports --}}
    <div class="card" style="margin-top: 1.5rem;">
        <div class="card-header">
            <h3 class="card-title">Historique des exports</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Lignes</th>
                        <th>Format</th>
                        <th>Utilisateur</th>
                        <th>Modele</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentExports as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->type_label }}</td>
                            <td>{{ $log->total_rows }}</td>
                            <td>{{ strtoupper($log->format) }}</td>
                            <td>{{ $log->user?->name ?? '-' }}</td>
                            <td>{{ $log->template?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #6b7280;">Aucun export enregistre.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="card" style="margin-top: 1.5rem;">
        <div class="card-header">
            <h3 class="card-title">Import rapide</h3>
        </div>
        <div class="card-body">
            <p style="color: #6b7280; margin-bottom: 1rem;">Pour importer des donnees, utilisez les imports depuis les modules concernes :</p>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="{{ route('admin.members.import') }}" class="btn btn-secondary">Importer des contacts</a>
            </div>
        </div>
    </div>
@endsection
