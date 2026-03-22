@extends('layouts.admin')
@section('title', 'Nouvelle adhesion')
@section('breadcrumb')
    <a href="{{ route('admin.memberships.index') }}">Adhesions</a>
    <span>/</span>
    <span>Nouvelle</span>
@endsection

@section('content')
    <div class="card" style="max-width: 800px;">
        <div class="card-header">
            <h3 class="card-title">Nouvelle adhesion</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.memberships.store') }}" method="POST">
                @csrf
                @include('admin.memberships._form')
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Creer l'adhesion</button>
                    <a href="{{ route('admin.memberships.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
