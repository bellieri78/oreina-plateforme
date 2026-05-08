@extends('layouts.admin')
@section('title', 'Modifier la question FAQ')
@section('breadcrumb')
    <a href="{{ route('admin.faq.index') }}">FAQ</a>
    <span style="margin: 0 0.5rem;">/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <form action="{{ route('admin.faq.update', $faq) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.faq._form')

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="check"></i> Enregistrer
            </button>
            <a href="{{ route('admin.faq.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>

    <form action="{{ route('admin.faq.destroy', $faq) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette question ?');" style="margin-top: 1.5rem;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-secondary" style="color: #dc2626;">
            <i data-lucide="trash-2"></i> Supprimer cette question
        </button>
    </form>
@endsection
