@extends('layouts.admin')

@section('title', 'Nouvelle structure')

@section('breadcrumb')
    <a href="{{ route('admin.structures.index') }}">Structures</a>
    <span>/</span>
    <span>Nouvelle</span>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('admin.structures.store') }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 class="card-title">Nouvelle structure</h3>
            </div>

            <div class="card-body">
                @include('admin.structures._form', ['structure' => null])
            </div>

            <div class="card-footer" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.structures.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Creer la structure</button>
            </div>
        </form>
    </div>
@endsection
