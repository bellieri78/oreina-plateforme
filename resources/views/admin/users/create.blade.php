@extends('layouts.admin')
@section('title', 'Nouvel utilisateur')
@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}">Utilisateurs</a>
    <span>/</span>
    <span>Nouveau</span>
@endsection

@section('content')
    <div class="card" style="max-width: 900px;">
        <div class="card-header">
            <h3 class="card-title">Nouvel utilisateur</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                @include('admin.users._form')
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Creer l'utilisateur</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
