@extends('layouts.admin')
@section('title', 'Modifier numero')
@section('breadcrumb')
    <a href="{{ route('admin.journal-issues.index') }}">Numeros</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div class="card" style="max-width: 900px;">
        <div class="card-header">
            <h3 class="card-title">Modifier Vol. {{ $journalIssue->volume_number }} N°{{ $journalIssue->issue_number }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.journal-issues.update', $journalIssue) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.journal-issues._form', ['journalIssue' => $journalIssue])
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.journal-issues.show', $journalIssue) }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
