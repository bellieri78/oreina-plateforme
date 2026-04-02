@extends('layouts.admin')
@section('title', 'Nouveau bulletin Lepis')
@section('breadcrumb')
    <a href="{{ route('admin.lepis.index') }}">Lepis</a>
    <span>/</span>
    <span>Nouveau</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Nouveau bulletin Lepis</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.lepis.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.lepis._form')
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Creer le bulletin</button>
                    <a href="{{ route('admin.lepis.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
