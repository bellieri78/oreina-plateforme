@extends('layouts.admin')
@section('title', 'Modifier produit')
@section('breadcrumb')
    <a href="{{ route('admin.products.index') }}">Produits</a>
    <span>/</span>
    <span>{{ $product->name }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Modifier le produit</h1>
            <p class="page-subtitle">{{ $product->name }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 1.5rem;">
            <form action="{{ route('admin.products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.products._form', ['product' => $product])

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-ghost">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
