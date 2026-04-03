@extends('layouts.admin')
@section('title', 'Suggestion Lepis')
@section('breadcrumb')
    <a href="{{ route('admin.lepis.index') }}">Lepis</a>
    <span>/</span>
    <a href="{{ route('admin.lepis-suggestions.index') }}">Suggestions</a>
    <span>/</span>
    <span>Detail</span>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">{{ $suggestion->title }}</h1>
            <p class="page-subtitle">
                Soumise par {{ $suggestion->member ? $suggestion->member->last_name . ' ' . $suggestion->member->first_name : 'Membre supprime' }}
                @if($suggestion->submitted_at)
                    le {{ $suggestion->submitted_at->format('d/m/Y') }}
                @endif
            </p>
        </div>
        <div class="page-header-actions">
            @if($suggestion->status === 'pending')
                <form action="{{ route('admin.lepis-suggestions.noted', $suggestion) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Marquer comme notee
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.lepis-suggestions.index') }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Status -->
            <div style="margin-bottom: 1.5rem;">
                <span class="form-label">Statut :</span>
                @if($suggestion->status === 'pending')
                    <span class="badge badge-warning">En attente</span>
                @else
                    <span class="badge badge-success">Notee</span>
                @endif
            </div>

            <!-- Content -->
            <div style="margin-bottom: 1.5rem;">
                <span class="form-label">Contenu :</span>
                <div style="margin-top: 0.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem; white-space: pre-wrap;">{{ $suggestion->content ?: 'Aucun contenu.' }}</div>
            </div>

            <!-- Attachment -->
            @if($suggestion->attachment_path)
                <div style="margin-bottom: 1.5rem;">
                    <span class="form-label">Piece jointe :</span>
                    <div style="margin-top: 0.5rem;">
                        <a href="{{ Storage::url($suggestion->attachment_path) }}" target="_blank" class="btn btn-outline">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Telecharger le fichier
                        </a>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <form action="{{ route('admin.lepis-suggestions.destroy', $suggestion) }}" method="POST" onsubmit="return confirm('Supprimer cette suggestion ?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
@endsection
