@extends('layouts.member')

@section('title', 'Préférences')
@section('subtitle', 'Gérez vos préférences de communication')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('member.profile.preferences.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="member-card mb-6">
            <div class="member-card-header">
                <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <h3 class="member-card-title">Consentements RGPD</h3>
            </div>

            <p class="text-sm text-gray-600 mb-6">
                Conformément au Règlement Général sur la Protection des Données, vous pouvez gérer vos préférences de communication ci-dessous.
            </p>

            <div class="space-y-6">
                {{-- Newsletter --}}
                <label class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg hover:border-oreina-green transition cursor-pointer">
                    <input type="checkbox" name="newsletter_subscribed" value="1" {{ $member?->newsletter_subscribed ? 'checked' : '' }} class="mt-1 w-5 h-5 rounded border-gray-300 text-oreina-green focus:ring-oreina-green">
                    <div class="flex-1">
                        <div class="font-medium text-oreina-dark">Newsletter</div>
                        <p class="text-sm text-gray-500 mt-1">
                            Recevoir les actualités de l'association, les annonces d'événements et les informations sur la revue OREINA.
                        </p>
                    </div>
                </label>

                {{-- Communication --}}
                <label class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg hover:border-oreina-green transition cursor-pointer">
                    <input type="checkbox" name="consent_communication" value="1" {{ $member?->consent_communication ? 'checked' : '' }} class="mt-1 w-5 h-5 rounded border-gray-300 text-oreina-green focus:ring-oreina-green">
                    <div class="flex-1">
                        <div class="font-medium text-oreina-dark">Communications personnalisées</div>
                        <p class="text-sm text-gray-500 mt-1">
                            Recevoir des informations ciblées selon vos centres d'intérêt (sorties terrain dans votre région, groupes de travail, etc.).
                        </p>
                    </div>
                </label>

                {{-- Image rights --}}
                <label class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg hover:border-oreina-green transition cursor-pointer">
                    <input type="checkbox" name="consent_image" value="1" {{ $member?->consent_image ? 'checked' : '' }} class="mt-1 w-5 h-5 rounded border-gray-300 text-oreina-green focus:ring-oreina-green">
                    <div class="flex-1">
                        <div class="font-medium text-oreina-dark">Droit à l'image</div>
                        <p class="text-sm text-gray-500 mt-1">
                            Autoriser l'utilisation de photos où vous apparaissez dans les publications de l'association (site web, réseaux sociaux, revue).
                        </p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Info box --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-700">
                    <p class="font-medium mb-1">Vos droits</p>
                    <p>Vous pouvez à tout moment modifier ces préférences ou demander la suppression de vos données en contactant notre délégué à la protection des données à <a href="mailto:rgpd@oreina.org" class="underline">rgpd@oreina.org</a>.</p>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-between items-center">
            <a href="{{ route('member.profile') }}" class="text-gray-500 hover:text-gray-700">
                ← Retour au profil
            </a>
            <button type="submit" class="btn-member">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer mes préférences
            </button>
        </div>
    </form>
</div>
@endsection
