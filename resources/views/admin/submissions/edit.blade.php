@extends('layouts.admin')
@section('title', 'Modifier soumission')
@section('breadcrumb')
    <a href="{{ route('admin.submissions.index') }}">Soumissions</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Modifier la soumission</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.submissions.update', $submission) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.submissions._form', ['submission' => $submission])
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
