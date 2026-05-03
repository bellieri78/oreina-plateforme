@extends('layouts.admin')
@section('title', 'Modifier ' . $menuItem->label)
@section('breadcrumb')
    <a href="{{ route('admin.menus.index') }}">Menus</a>
    <span>/</span>
    <span>Modifier "{{ $menuItem->label }}"</span>
@endsection

@section('content')
    <div style="max-width: 800px;">
        <form action="{{ route('admin.menus.update', $menuItem) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.menus._form', ['menuItem' => $menuItem, 'availableParents' => $availableParents, 'defaultLocation' => $menuItem->location])

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
