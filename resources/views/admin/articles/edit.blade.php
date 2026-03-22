@extends('layouts.admin')
@section('title', 'Modifier article')
@section('breadcrumb')
    <a href="{{ route('admin.articles.index') }}">Articles</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Modifier l'article</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.articles.update', $article) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.articles._form', ['article' => $article])
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
