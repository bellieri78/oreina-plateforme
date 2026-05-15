@extends('layouts.admin')
@section('title', 'Espèce du mois')
@section('breadcrumb')<span>Espèce du mois</span>@endsection

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <p style="color: #6b7280; margin: 0;">
            Carousel affiché dans le hero du dashboard membre. Sans entrée active, la photo par défaut est utilisée.
        </p>
        <a href="{{ route('admin.espece-du-mois.create') }}" class="btn btn-primary">
            <i data-lucide="plus"></i> Ajouter une espèce
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body" style="padding: 0;">
            @if($entries->count() === 0)
                <div style="padding: 1rem; color: #6b7280;">
                    Aucune entrée. Le hero du dashboard membre retombera sur la photo par défaut.
                </div>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 90px;">Photo</th>
                            <th>Espèce</th>
                            <th>Photographe</th>
                            <th style="width: 90px;">Ordre</th>
                            <th style="width: 110px;">Statut</th>
                            <th style="width: 180px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($entries as $entry)
                        <tr>
                            <td>
                                <img src="{{ $entry->photoUrl() }}" alt=""
                                     style="width: 60px; height: 45px; object-fit: cover; border-radius: 8px;">
                            </td>
                            <td><em>{{ $entry->scientific_name }}</em></td>
                            <td style="color: #6b7280;">{{ $entry->photographer ?? '—' }}</td>
                            <td>{{ $entry->display_order }}</td>
                            <td>
                                @if($entry->is_active)
                                    <span class="badge badge-success">Actif</span>
                                @else
                                    <span class="badge badge-secondary">Inactif</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.espece-du-mois.edit', $entry) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8125rem;">
                                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i> Éditer
                                </a>
                                <form method="POST" action="{{ route('admin.espece-du-mois.destroy', $entry) }}" style="display: inline;" onsubmit="return confirm('Supprimer cette entrée ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8125rem; color: #dc2626;">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $entries->links() }}
    </div>
@endsection
