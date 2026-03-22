@extends('layouts.admin')
@section('title', 'Modifier don')
@section('breadcrumb')
    <a href="{{ route('admin.donations.index') }}">Dons</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div class="card" style="max-width: 800px;">
        <div class="card-header">
            <h3 class="card-title">Modifier le don</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.donations.update', $donation) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.donations._form', ['donation' => $donation])
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.donations.show', $donation) }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
