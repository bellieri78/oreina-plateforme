@extends('layouts.admin')

@section('title', 'Modifier ' . $structure->name)

@section('breadcrumb')
    <a href="{{ route('admin.structures.index') }}">Structures</a>
    <span>/</span>
    <a href="{{ route('admin.structures.show', $structure) }}">{{ $structure->name }}</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('admin.structures.update', $structure) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-header">
                <h3 class="card-title">Modifier {{ $structure->name }}</h3>
            </div>

            <div class="card-body">
                @include('admin.structures._form', ['structure' => $structure])
            </div>

            <div class="card-footer" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.structures.show', $structure) }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
@endsection
