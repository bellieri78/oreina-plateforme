@extends('layouts.admin')

@section('title', 'Nouveau contact')
@section('breadcrumb')
    <a href="{{ route('admin.members.index') }}">Contacts</a>
    <span>/</span>
    <span>Nouveau</span>
@endsection

@section('content')
    <div style="max-width: 800px;">
        <form action="{{ route('admin.members.store') }}" method="POST">
            @csrf
            @include('admin.members._form', ['member' => null])

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">Créer le contact</button>
                <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
