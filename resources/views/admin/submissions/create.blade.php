@extends('layouts.admin')
@section('title', 'Nouvelle soumission')
@section('breadcrumb')
    <a href="{{ route('admin.submissions.index') }}">Soumissions</a>
    <span>/</span>
    <span>Nouvelle</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Nouvelle soumission</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.submissions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.submissions._form')
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Creer la soumission</button>
                    <a href="{{ route('admin.submissions.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
