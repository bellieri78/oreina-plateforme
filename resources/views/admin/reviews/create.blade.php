@extends('layouts.admin')
@section('title', 'Nouvelle review')
@section('breadcrumb')
    <a href="{{ route('admin.reviews.index') }}">Reviews</a>
    <span>/</span>
    <span>Nouvelle</span>
@endsection

@section('content')
    <div class="card" style="max-width: 800px;">
        <div class="card-header">
            <h3 class="card-title">Assigner une nouvelle review</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reviews.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.reviews._form')
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Assigner la review</button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
