@extends('layouts.admin')

@section('title', 'Maquettage - ' . Str::limit($submission->title, 30))

@section('breadcrumb')
    <a href="{{ route('admin.submissions.index') }}">Soumissions</a>
    <svg class="breadcrumb-separator" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <a href="{{ route('admin.submissions.show', $submission) }}">{{ Str::limit($submission->title, 20) }}</a>
    <svg class="breadcrumb-separator" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span>Maquettage</span>
@endsection

@section('content')
<style>
    .layout-page {
        max-width: 1400px;
        margin: 0 auto;
    }
    .layout-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .layout-header-left {
        flex: 1;
    }
    .layout-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0d9488;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    .layout-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.8rem;
        color: #6b7280;
    }
    .layout-meta-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .layout-actions {
        display: flex;
        gap: 0.5rem;
        flex-shrink: 0;
    }
    .layout-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.5rem;
    }
    @media (max-width: 1024px) {
        .layout-grid {
            grid-template-columns: 1fr;
        }
    }
    .layout-main {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .layout-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .sidebar-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 1rem;
    }
    .sidebar-card-title {
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .sidebar-field {
        margin-bottom: 1rem;
    }
    .sidebar-field:last-child {
        margin-bottom: 0;
    }
    .sidebar-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.375rem;
    }
    .sidebar-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.85rem;
    }
    .sidebar-textarea {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.8rem;
        line-height: 1.5;
        resize: vertical;
        min-height: 100px;
    }
    .sidebar-hint {
        font-size: 0.7rem;
        color: #9ca3af;
        margin-top: 0.25rem;
    }
    .preview-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .preview-btn:hover:not(.preview-btn-disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
    }
    .preview-btn-disabled {
        opacity: 0.45;
        cursor: not-allowed;
        pointer-events: none;
    }
    .preview-warning {
        font-size: 0.7rem;
        color: #d97706;
        margin-top: 0.5rem;
        text-align: center;
    }
    .save-indicator {
        display: none;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #f0fdf4;
        border: 1px solid #86efac;
        border-radius: 0.5rem;
        font-size: 0.8rem;
        color: #15803d;
    }
    .save-indicator.visible {
        display: flex;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-accepted {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .status-published {
        background: #dcfce7;
        color: #15803d;
    }
    .content-stats {
        display: flex;
        gap: 1rem;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        color: #6b7280;
    }
    .content-stat {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .content-stat strong {
        color: #374151;
    }
</style>

<div class="layout-page" x-data="{ unsaved: false, blockCount: {{ count($submission->content_blocks ?? []) }} }"
     @blocks-changed.window="unsaved = true; blockCount = $event.detail.count">
    <form action="{{ route('admin.submissions.layout.update', $submission) }}" method="POST" id="layoutForm">
        @csrf
        @method('PUT')

        {{-- Header --}}
        <div class="layout-header">
            <div class="layout-header-left">
                <h1 class="layout-title">{{ $submission->title }}</h1>
                <div class="layout-meta">
                    <div class="layout-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        {{ $submission->author?->name ?? 'Auteur inconnu' }}
                    </div>
                    @if($submission->journalIssue)
                    <div class="layout-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        Vol. {{ $submission->journalIssue->volume_number }} N°{{ $submission->journalIssue->issue_number }}
                    </div>
                    @endif
                    <div class="layout-meta-item">
                        <span class="status-badge {{ $submission->status === \App\Enums\SubmissionStatus::Published ? 'status-published' : 'status-accepted' }}">
                            {{ $submission->status->label() }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="layout-actions">
                <div class="save-indicator" id="saveIndicator">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Sauvegardé
                </div>
                <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>
                    Retour
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" />
                    </svg>
                    Sauvegarder
                </button>
            </div>
        </div>

        {{-- Main content --}}
        <div class="layout-grid">
            {{-- Editor --}}
            <div class="layout-main">
                <div id="detected-title-banner" style="display:none; background:#eff6ff; border:1px solid #93c5fd; border-radius:0.5rem; padding:0.75rem 1rem; margin-bottom:1rem; font-size:0.85rem; color:#1e40af; align-items:center; flex-wrap:wrap; gap:0.5rem;">
                    <strong>Titre détecté :</strong> <span id="detected-title-text" style="font-style:italic; flex:1;"></span>
                    <button type="button" id="update-title-btn" style="margin-left:0.75rem; background:#3b82f6; color:white; border:none; padding:0.25rem 0.75rem; border-radius:0.25rem; cursor:pointer; font-size:0.8rem;">
                        Mettre à jour le titre
                    </button>
                    <button type="button" onclick="document.getElementById('detected-title-banner').style.display='none'" style="margin-left:0.25rem; background:none; border:none; cursor:pointer; color:#6b7280; font-size:1rem;">&times;</button>
                </div>
                @include('admin.submissions._block-editor')
            </div>

            {{-- Sidebar --}}
            <div class="layout-sidebar">
                {{-- Preview --}}
                <div class="sidebar-card">
                    <a href="{{ route('admin.submissions.preview-pdf', $submission) }}"
                       target="_blank"
                       class="preview-btn"
                       :class="{ 'preview-btn-disabled': unsaved }"
                       x-bind:tabindex="unsaved ? -1 : 0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        Aperçu PDF
                    </a>
                    <div x-show="unsaved" class="preview-warning">
                        Sauvegardez d'abord pour prévisualiser le PDF
                    </div>
                </div>

                {{-- Stats --}}
                <div class="sidebar-card">
                    <div class="sidebar-card-title">Contenu</div>
                    <div class="content-stats">
                        <div class="content-stat">
                            <strong x-text="blockCount">0</strong> blocs
                        </div>
                        <div class="content-stat">
                            <strong>{{ count($submission->references ?? []) }}</strong> réf.
                        </div>
                    </div>
                </div>

                {{-- Display Authors --}}
                <div class="sidebar-card">
                    <div class="sidebar-card-title">Auteurs (affichage)</div>
                    <div class="sidebar-field">
                        <input type="text" name="display_authors" id="sidebar-display-authors" class="sidebar-input"
                               value="{{ old('display_authors', $submission->display_authors ?? '') }}"
                               placeholder="Prénom NOM, Prénom NOM & Prénom NOM">
                        <div class="sidebar-hint">Noms tels qu'affichés sur la page publique</div>
                    </div>
                </div>

                {{-- Affiliations --}}
                <div class="sidebar-card">
                    <div class="sidebar-card-title">Affiliations auteurs</div>
                    <div class="sidebar-field">
                        <textarea name="author_affiliations" id="sidebar-affiliations" class="sidebar-textarea" rows="4" placeholder="1. Université de Paris, France&#10;2. CNRS, Paris">{{ old('author_affiliations', is_array($submission->author_affiliations) ? implode("\n", $submission->author_affiliations) : $submission->author_affiliations) }}</textarea>
                        <div class="sidebar-hint">Une affiliation par ligne (1., 2., etc.)</div>
                    </div>
                </div>

                {{-- References --}}
                <div class="sidebar-card">
                    <div class="sidebar-card-title">Références bibliographiques</div>
                    <div class="sidebar-field">
                        <textarea name="references" id="sidebar-references" class="sidebar-textarea" rows="8" placeholder="Dupont J. & Martin P., 2023. Titre...&#10;Smith A., 2022. Autre référence...">{{ old('references', is_array($submission->references) ? implode("\n", $submission->references) : $submission->references) }}</textarea>
                        <div class="sidebar-hint">Une référence par ligne</div>
                    </div>
                </div>

                {{-- Acknowledgements --}}
                <div class="sidebar-card">
                    <div class="sidebar-card-title">Remerciements</div>
                    <div class="sidebar-field">
                        <textarea name="acknowledgements" id="sidebar-acknowledgements" class="sidebar-textarea" rows="3" placeholder="Les auteurs remercient...">{{ old('acknowledgements', $submission->acknowledgements ?? '') }}</textarea>
                    </div>
                </div>

                {{-- Dates --}}
                <div class="sidebar-card">
                    <div class="sidebar-card-title">Dates</div>
                    <div class="sidebar-field">
                        <label class="sidebar-label">Date de réception</label>
                        <input type="date" name="received_at" class="sidebar-input"
                               value="{{ old('received_at', $submission->received_at?->format('Y-m-d') ?? $submission->submitted_at?->format('Y-m-d')) }}">
                    </div>
                    <div class="sidebar-field">
                        <label class="sidebar-label">Date d'acceptation</label>
                        <input type="date" name="accepted_at" class="sidebar-input"
                               value="{{ old('accepted_at', $submission->accepted_at?->format('Y-m-d') ?? $submission->decision_at?->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
