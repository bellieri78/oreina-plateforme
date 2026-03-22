@extends('layouts.admin')
@section('title', 'Nouvel evenement')
@section('breadcrumb')
    <a href="{{ route('admin.events.index') }}">Evenements</a>
    <span>/</span>
    <span>Nouveau</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Nouvel evenement</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.events.store') }}" method="POST">
                @csrf
                @include('admin.events._form')
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Creer l'evenement</button>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
