@extends('layouts.admin')

@section('title', 'RGPD - Parametres')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Parametres RGPD</h1>
            <p class="text-gray-600">Configuration des durees de retention des donnees</p>
        </div>
        <a href="{{ route('admin.rgpd.index') }}"
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('admin.rgpd.settings.update') }}" method="POST">
    @csrf

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Durees de retention</h3>
        <p class="text-sm text-gray-500 mb-6">
            Definissez les durees (en mois) apres lesquelles une alerte sera declenchee pour chaque type de situation.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($settings as $setting)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        @switch($setting->key)
                            @case('retention_no_interaction')
                                Delai sans interaction
                                @break
                            @case('retention_not_updated')
                                Delai sans mise a jour
                                @break
                            @case('retention_expired_membership')
                                Delai apres expiration adhesion
                                @break
                            @case('retention_inactive_donor')
                                Delai d'inactivite donateur
                                @break
                            @default
                                {{ $setting->key }}
                        @endswitch
                    </label>
                    <div class="flex items-center">
                        <input type="number"
                               name="{{ $setting->key }}"
                               value="{{ old($setting->key, $setting->value) }}"
                               min="6"
                               max="120"
                               class="w-24 rounded-lg border-gray-300 focus:ring-oreina-green focus:border-oreina-green"
                               required>
                        <span class="ml-3 text-gray-500">mois</span>
                    </div>
                    @if($setting->description)
                        <p class="mt-1 text-xs text-gray-400">{{ $setting->description }}</p>
                    @endif
                    @error($setting->key)
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations RGPD</h3>

        <div class="prose prose-sm max-w-none text-gray-600">
            <h4 class="text-gray-900">Obligations legales</h4>
            <p>
                Le Reglement General sur la Protection des Donnees (RGPD) impose aux organisations de :
            </p>
            <ul>
                <li>Ne conserver les donnees personnelles que le temps necessaire a leur finalite</li>
                <li>Informer les personnes de la duree de conservation de leurs donnees</li>
                <li>Permettre aux personnes d'exercer leur droit a l'effacement (droit a l'oubli)</li>
                <li>Tenir un registre des traitements de donnees</li>
            </ul>

            <h4 class="text-gray-900 mt-4">Recommandations</h4>
            <ul>
                <li><strong>Sans interaction</strong> : 36 mois est une duree raisonnable pour un contact sans activite</li>
                <li><strong>Sans mise a jour</strong> : 60 mois (5 ans) permet de garder l'historique tout en restant conforme</li>
                <li><strong>Adhesion expiree</strong> : 24 mois apres expiration pour permettre un renouvellement</li>
                <li><strong>Donateur inactif</strong> : 48 mois pour tenir compte des obligations fiscales</li>
            </ul>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="px-6 py-3 bg-oreina-green text-white rounded-lg hover:bg-oreina-dark">
            Enregistrer les parametres
        </button>
    </div>
</form>
@endsection
