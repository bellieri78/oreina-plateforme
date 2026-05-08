@extends('layouts.admin')
@section('title', 'Nouvelle question FAQ')
@section('breadcrumb')
    <a href="{{ route('admin.faq.index') }}">FAQ</a>
    <span style="margin: 0 0.5rem;">/</span>
    <span>Nouvelle question</span>
@endsection

@section('content')
    <form action="{{ route('admin.faq.store') }}" method="POST">
        @csrf
        @include('admin.faq._form')

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="check"></i> Créer la question
            </button>
            <a href="{{ route('admin.faq.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
@endsection
