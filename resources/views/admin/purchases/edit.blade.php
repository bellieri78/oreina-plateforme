@extends('layouts.admin')
@section('title', 'Modifier achat')
@section('breadcrumb')
    <a href="{{ route('admin.purchases.index') }}">Achats</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Modifier l'achat</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 1.5rem;">
            <form action="{{ route('admin.purchases.update', $purchase) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.purchases._form', ['purchase' => $purchase])

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-ghost">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
