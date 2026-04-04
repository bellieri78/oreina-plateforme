@extends('layouts.hub')

@section('title', 'Créer un compte')

@section('content')
@push('styles')
<style>
    .register-section {
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 16px;
    }
    .register-card {
        width: 100%;
        max-width: 480px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow);
        padding: 40px;
    }
    .register-header {
        text-align: center;
        margin-bottom: 32px;
    }
    .register-header .register-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: var(--surface-sage);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        color: var(--forest);
    }
    .register-header h1 {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.04em;
        margin: 0;
    }
    .register-header p {
        margin: 8px 0 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.6;
    }
    .register-form {
        display: grid;
        gap: 18px;
    }
    .register-field label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 6px;
    }
    .register-field input {
        width: 100%;
        height: 46px;
        padding: 0 14px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--surface);
        font-size: 15px;
        font-family: inherit;
        color: var(--text);
        transition: 0.2s ease;
    }
    .register-field input:focus {
        outline: none;
        border-color: var(--sage);
        box-shadow: 0 0 0 3px rgba(133,183,157,0.15);
    }
    .register-field .error {
        color: #dc2626;
        font-size: 13px;
        margin-top: 6px;
    }
    .register-field .hint {
        color: var(--muted);
        font-size: 12px;
        margin-top: 4px;
    }
    .register-submit {
        width: 100%;
    }
    .register-footer {
        text-align: center;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
    }
    .register-footer p {
        color: var(--muted);
        font-size: 14px;
    }
    .register-footer a {
        color: var(--blue);
        font-weight: 700;
    }
    .register-info {
        margin-top: 20px;
        padding: 14px;
        border-radius: 12px;
        background: var(--surface-sage);
        border: 1px solid rgba(133,183,157,0.2);
        font-size: 13px;
        color: #2f694e;
        line-height: 1.5;
    }
    .register-info strong {
        display: block;
        margin-bottom: 4px;
    }
</style>
@endpush

<section class="register-section">
    <div class="register-card">
        <div class="register-header">
            <div class="register-icon">
                <i data-lucide="user-round-plus" style="width:28px;height:28px;"></i>
            </div>
            <h1>Créer un compte</h1>
            <p>Rejoignez la plateforme OREINA pour accéder à votre espace personnel et soumettre des articles à Chersotis.</p>
        </div>

        <form method="POST" action="{{ route('hub.register.submit') }}" class="register-form">
            @csrf

            <div class="register-field">
                <label for="name">Nom complet</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Prénom Nom">
                @error('name')<p class="error">{{ $message }}</p>@enderror
            </div>

            <div class="register-field">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="votre@email.fr">
                <p class="hint">Si vous êtes déjà adhérent, utilisez l'email associé à votre adhésion pour un rattachement automatique.</p>
                @error('email')<p class="error">{{ $message }}</p>@enderror
            </div>

            <div class="register-field">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="8 caractères minimum">
                @error('password')<p class="error">{{ $message }}</p>@enderror
            </div>

            <div class="register-field">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary register-submit">
                <i data-lucide="user-round-plus"></i>
                Créer mon compte
            </button>
        </form>

        <div class="register-info">
            <strong>Pas besoin d'être adhérent</strong>
            Un compte gratuit suffit pour soumettre un article à Chersotis et accéder aux fonctionnalités de base. L'adhésion vous donne accès en plus au bulletin Lepis, au chat, aux groupes de travail et à la revue complète.
        </div>

        <div class="register-footer">
            <p>Déjà un compte ? <a href="{{ route('hub.login') }}">Se connecter</a></p>
        </div>
    </div>
</section>
@endsection
