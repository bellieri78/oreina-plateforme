@extends('layouts.admin')
@section('title', 'Nouveau produit')
@section('breadcrumb')
    <a href="{{ route('admin.products.index') }}">Produits</a>
    <span>/</span>
    <span>Nouveau</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Nouveau produit</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 1.5rem;">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                @include('admin.products._form', ['product' => null])

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Creer le produit</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-ghost">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
