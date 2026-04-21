@extends('layouts.admin')
@section('title', 'Nouveau bulletin Lepis')
@section('breadcrumb')
    <a href="{{ route('admin.lepis.index') }}">Lepis</a>
    <span>/</span>
    <span>Nouveau</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Nouveau bulletin Lepis</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.lepis.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.lepis._form')

                <details style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid #e5e7eb;">
                    <summary style="cursor:pointer;font-weight:600;color:#374151;padding:0.25rem 0;list-style:none;display:flex;align-items:center;gap:0.5rem;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Template annonce adhérents (optionnel)
                    </summary>
                    <div style="margin-top:1rem;">
                        <p style="font-size:0.875rem;color:#6b7280;margin-bottom:1rem;">
                            Pré-rempli depuis le bulletin précédent. Vous pouvez modifier ce template après création.
                        </p>
                        <div class="form-group">
                            <label class="form-label" for="announcement_subject">Objet de l'email</label>
                            <input type="text" name="announcement_subject" id="announcement_subject" class="form-input"
                                value="{{ old('announcement_subject', $defaults['announcement_subject'] ?? '') }}">
                            @error('announcement_subject')<p style="color:#dc2626;font-size:0.875rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="announcement_body">Corps de l'annonce</label>
                            <textarea name="announcement_body" id="announcement_body" class="form-input" rows="6"
                                style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:0.875rem;">{{ old('announcement_body', $defaults['announcement_body'] ?? '') }}</textarea>
                            <p style="font-size:0.8rem;color:#6b7280;margin-top:0.25rem;">
                                Markdown supporté. Utilisez le token <code>{{'{{'}}lien_bulletin{{'}}'}}</code> pour insérer le lien.
                            </p>
                            @error('announcement_body')<p style="color:#dc2626;font-size:0.875rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </details>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Créer le bulletin</button>
                    <a href="{{ route('admin.lepis.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
