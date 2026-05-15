@extends('layouts.admin')
@section('title', 'Nouveau groupe de travail')
@section('breadcrumb')
    <a href="{{ route('admin.work-groups.index') }}">Groupes de travail</a>
    <span>/</span>
    <span>Nouveau</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Nouveau groupe de travail</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.work-groups.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.work-groups._form')
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Creer le groupe</button>
                    <a href="{{ route('admin.work-groups.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
