@extends('layouts.admin')
@section('title', 'Modifier utilisateur')
@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}">Utilisateurs</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div class="card" style="max-width: 900px;">
        <div class="card-header">
            <h3 class="card-title">Modifier {{ $user->name }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.users._form', ['user' => $user])
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
