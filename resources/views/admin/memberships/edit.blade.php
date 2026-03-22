@extends('layouts.admin')
@section('title', 'Modifier adhesion')
@section('breadcrumb')
    <a href="{{ route('admin.memberships.index') }}">Adhesions</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div class="card" style="max-width: 800px;">
        <div class="card-header">
            <h3 class="card-title">Modifier l'adhesion</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.memberships.update', $membership) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.memberships._form', ['membership' => $membership])
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.memberships.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
