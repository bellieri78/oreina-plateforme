@extends('layouts.admin')
@section('title', 'Modifier bulletin Lepis')
@section('breadcrumb')
    <a href="{{ route('admin.lepis.index') }}">Lepis</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Modifier le bulletin Lepis</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.lepis.update', $bulletin) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.lepis._form', ['bulletin' => $bulletin])
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.lepis.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
