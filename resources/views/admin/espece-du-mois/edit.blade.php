@extends('layouts.admin')
@section('title', 'Modifier l\'espèce')
@section('breadcrumb')
    <a href="{{ route('admin.espece-du-mois.index') }}">Espèce du mois</a>
    <span style="margin: 0 0.5rem;">/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <form action="{{ route('admin.espece-du-mois.update', $entry) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.espece-du-mois._form')

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="check"></i> Enregistrer
            </button>
            <a href="{{ route('admin.espece-du-mois.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>

    <form action="{{ route('admin.espece-du-mois.destroy', $entry) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette entrée ?');" style="margin-top: 1.5rem;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-secondary" style="color: #dc2626;">
            <i data-lucide="trash-2"></i> Supprimer cette entrée
        </button>
    </form>
@endsection
