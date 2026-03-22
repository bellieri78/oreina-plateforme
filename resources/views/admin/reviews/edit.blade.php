@extends('layouts.admin')
@section('title', 'Modifier review')
@section('breadcrumb')
    <a href="{{ route('admin.reviews.index') }}">Reviews</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Modifier la review</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reviews.update', $review) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.reviews._form', ['review' => $review])
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
