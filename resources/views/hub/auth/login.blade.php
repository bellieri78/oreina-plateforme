@extends('layouts.hub')

@section('title', 'Connexion')

@section('content')
@push('styles')
<style>
    .login-section {
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 16px;
    }
    .login-card {
        width: 100%;
        max-width: 440px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow);
        padding: 40px;
    }
    .login-header {
        text-align: center;
        margin-bottom: 32px;
    }
    .login-header .login-icon {
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
    .login-header h1 {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.04em;
        margin: 0;
    }
    .login-header p {
        margin: 8px 0 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.6;
    }
    .login-form {
        display: grid;
        gap: 20px;
    }
    .login-field label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 6px;
    }
    .login-field input {
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
    .login-field input:focus {
        outline: none;
        border-color: var(--sage);
        box-shadow: 0 0 0 3px rgba(133,183,157,0.15);
    }
    .login-field .error {
        color: #dc2626;
        font-size: 13px;
        margin-top: 6px;
    }
    .login-remember {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: var(--muted);
    }
    .login-remember input {
        width: 16px;
        height: 16px;
        accent-color: var(--sage);
    }
    .login-submit {
        width: 100%;
    }
    .login-footer {
        text-align: center;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
    }
    .login-footer p {
        color: var(--muted);
        font-size: 14px;
    }
    .login-footer a {
        color: var(--blue);
        font-weight: 700;
    }
</style>
@endpush

<section class="login-section">
    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i data-lucide="butterfly" style="width:28px;height:28px;"></i>
            </div>
            <h1>Espace membre</h1>
            <p>Connectez-vous pour accéder à votre espace personnel, vos contributions et les ressources du réseau.</p>
        </div>

        <form method="POST" action="{{ route('hub.login.submit') }}" class="login-form">
            @csrf

            <div class="login-field">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="votre@email.fr">
                @error('email')<p class="error">{{ $message }}</p>@enderror
            </div>

            <div class="login-field">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
                @error('password')<p class="error">{{ $message }}</p>@enderror
            </div>

            <label class="login-remember">
                <input type="checkbox" name="remember">
                Se souvenir de moi
            </label>

            <button type="submit" class="btn btn-primary login-submit">
                <i data-lucide="log-in"></i>
                Se connecter
            </button>
        </form>

        <div class="login-footer">
            <p>Pas encore de compte ? <a href="{{ route('hub.register') }}">Créer un compte</a></p>
        </div>
    </div>
</section>
@endsection
