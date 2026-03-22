@extends('layouts.admin')

@section('title', 'Modifier ' . $activity->title)

@section('breadcrumb')
    <a href="{{ route('admin.volunteer.index') }}">Benevolat</a>
    <span>/</span>
    <a href="{{ route('admin.volunteer.show', $activity) }}">{{ $activity->title }}</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('admin.volunteer.update', $activity) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-header">
                <h3 class="card-title">Modifier l'activite</h3>
            </div>
            <div class="card-body">
                @include('admin.volunteer._form', ['activity' => $activity])
            </div>
            <div class="card-footer" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.volunteer.show', $activity) }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>

    {{-- Delete form --}}
    <div class="card" style="margin-top: 1.5rem; border-color: #ef4444;">
        <div class="card-header" style="background: #fef2f2;">
            <h3 class="card-title" style="color: #dc2626;">Zone de danger</h3>
        </div>
        <div class="card-body">
            <p style="margin-bottom: 1rem;">La suppression de cette activite est irreversible. Toutes les participations associees seront egalement supprimees.</p>
            <form action="{{ route('admin.volunteer.destroy', $activity) }}" method="POST"
                  onsubmit="return confirm('Etes-vous sur de vouloir supprimer cette activite ? Cette action est irreversible.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Supprimer cette activite</button>
            </form>
        </div>
    </div>
@endsection
