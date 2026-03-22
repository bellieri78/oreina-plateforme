@extends('layouts.admin')
@section('title', 'Nouvel achat')
@section('breadcrumb')
    <a href="{{ route('admin.purchases.index') }}">Achats</a>
    <span>/</span>
    <span>Nouveau</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Nouvel achat</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 1.5rem;">
            <form action="{{ route('admin.purchases.store') }}" method="POST">
                @csrf
                @include('admin.purchases._form', ['purchase' => null])

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer l'achat</button>
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-ghost">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
