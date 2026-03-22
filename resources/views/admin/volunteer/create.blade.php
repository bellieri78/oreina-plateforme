@extends('layouts.admin')

@section('title', 'Nouvelle activite')

@section('breadcrumb')
    <a href="{{ route('admin.volunteer.index') }}">Benevolat</a>
    <span>/</span>
    <span>Nouvelle activite</span>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('admin.volunteer.store') }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 class="card-title">Nouvelle activite benevole</h3>
            </div>
            <div class="card-body">
                @include('admin.volunteer._form', ['activity' => null])
            </div>
            <div class="card-footer" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.volunteer.activities') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Creer l'activite</button>
            </div>
        </form>
    </div>
@endsection
