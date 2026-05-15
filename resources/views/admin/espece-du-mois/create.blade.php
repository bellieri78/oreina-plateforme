@extends('layouts.admin')
@section('title', 'Nouvelle espèce du mois')
@section('breadcrumb')
    <a href="{{ route('admin.espece-du-mois.index') }}">Espèce du mois</a>
    <span style="margin: 0 0.5rem;">/</span>
    <span>Nouvelle entrée</span>
@endsection

@section('content')
    <form action="{{ route('admin.espece-du-mois.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.espece-du-mois._form')

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="check"></i> Créer l'entrée
            </button>
            <a href="{{ route('admin.espece-du-mois.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
@endsection
