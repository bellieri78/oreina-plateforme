@extends('layouts.journal')

@section('title', 'Activer mon compte — Chersotis')

@section('content')
<div style="min-height: 70vh; display: flex; align-items: center; justify-content: center; padding: 3rem 1rem;">
    <div style="max-width: 28rem; width: 100%; background: white; border-radius: 0.75rem; box-shadow: 0 10px 25px rgba(0,0,0,0.08); padding: 2rem;">
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #16302B; margin-bottom: 0.5rem;">Activer mon compte</h1>
        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 1.5rem;">
            Bienvenue {{ $user->name }} ! Définissez votre mot de passe pour accéder à votre espace auteur Chersotis.
        </p>

        <div style="margin-bottom: 1.5rem; padding: 0.75rem; background: #f9fafb; border-radius: 0.375rem; border: 1px solid #e5e7eb; font-size: 0.875rem;">
            <div><strong>Nom :</strong> {{ $user->name }}</div>
            <div><strong>Email :</strong> {{ $user->email }}</div>
        </div>

        <form method="POST" action="{{ url()->current() }}">
            @csrf

            <div style="margin-bottom: 1rem;">
                <label for="password" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Mot de passe</label>
                <input type="password" name="password" id="password" required
                       autocomplete="new-password" minlength="8"
                       style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">8 caractères minimum.</p>
                @error('password')
                    <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="password_confirmation" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       autocomplete="new-password" minlength="8"
                       style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            </div>

            <button type="submit"
                    style="width: 100%; background: #2C5F2D; color: white; padding: 0.625rem 1rem; border-radius: 0.375rem; border: none; font-weight: 500; cursor: pointer;">
                Activer mon compte
            </button>
        </form>

        <p style="font-size: 0.75rem; color: #6b7280; margin-top: 1.5rem; text-align: center;">
            En activant votre compte, vous acceptez les conditions d'utilisation et la politique de confidentialité.
        </p>
    </div>
</div>
@endsection
