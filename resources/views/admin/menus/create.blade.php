@extends('layouts.admin')
@section('title', 'Nouvel item de menu')
@section('breadcrumb')
    <a href="{{ route('admin.menus.index') }}">Menus</a>
    <span>/</span>
    <span>Nouveau</span>
@endsection

@section('content')
    <div style="max-width: 800px;">
        <form action="{{ route('admin.menus.store') }}" method="POST">
            @csrf
            @include('admin.menus._form', ['menuItem' => null, 'availableParents' => $availableParents, 'defaultLocation' => $defaultLocation])

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">Créer l'item</button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
