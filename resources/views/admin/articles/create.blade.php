@extends('layouts.admin')
@section('title', 'Nouvel article')
@section('breadcrumb')
    <a href="{{ route('admin.articles.index') }}">Articles</a>
    <span>/</span>
    <span>Nouveau</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Nouvel article</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.articles._form')
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Creer l'article</button>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
