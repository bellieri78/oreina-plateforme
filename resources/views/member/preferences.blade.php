@extends('layouts.member')

@section('title', 'Préférences')
@section('page-title', 'Préférences')
@section('page-subtitle', 'Gestion de vos consentements et communications')

@push('styles')
<style>[x-cloak] { display: none !important; }</style>
@endpush

@section('content')
<div>
    <form action="{{ route('member.profile.preferences.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card panel mb-6">
            <div class="panel-head">
                <div>
                    <h2>Consentements RGPD</h2>
                </div>
            </div>

            <p class="text-sm mb-6" style="color:var(--muted)">
                Conformément au Règlement Général sur la Protection des Données, vous pouvez gérer vos préférences de communication ci-dessous.
            </p>

            <div class="space-y-6">
                {{-- Newsletter --}}
                <label class="flex items-start gap-4 p-4 border rounded-lg cursor-pointer" style="border-color:var(--border)">
                    <input type="checkbox" name="newsletter_subscribed" value="1" {{ $member?->newsletter_subscribed ? 'checked' : '' }} class="mt-1 w-5 h-5 rounded">
                    <div class="flex-1">
                        <div class="font-medium" style="color:var(--forest)">Newsletter</div>
                        <p class="text-sm mt-1" style="color:var(--muted)">
                            Recevoir les actualités de l'association, les annonces d'événements et les informations sur la revue OREINA.
                        </p>
                    </div>
                </label>

                {{-- Communication --}}
                <label class="flex items-start gap-4 p-4 border rounded-lg cursor-pointer" style="border-color:var(--border)">
                    <input type="checkbox" name="consent_communication" value="1" {{ $member?->consent_communication ? 'checked' : '' }} class="mt-1 w-5 h-5 rounded">
                    <div class="flex-1">
                        <div class="font-medium" style="color:var(--forest)">Communications personnalisées</div>
                        <p class="text-sm mt-1" style="color:var(--muted)">
                            Recevoir des informations ciblées selon vos centres d'intérêt (sorties terrain dans votre région, groupes de travail, etc.).
                        </p>
                    </div>
                </label>

                {{-- Image rights --}}
                <label class="flex items-start gap-4 p-4 border rounded-lg cursor-pointer" style="border-color:var(--border)">
                    <input type="checkbox" name="consent_image" value="1" {{ $member?->consent_image ? 'checked' : '' }} class="mt-1 w-5 h-5 rounded">
                    <div class="flex-1">
                        <div class="font-medium" style="color:var(--forest)">Droit à l'image</div>
                        <p class="text-sm mt-1" style="color:var(--muted)">
                            Autoriser l'utilisation de photos où vous apparaissez dans les publications de l'association (site web, réseaux sociaux, revue).
                        </p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Annuaire des adhérents --}}
        <div class="card panel mb-6" x-data="{ optIn: {{ $member?->directory_opt_in ? 'true' : 'false' }} }">
            <div class="panel-head">
                <div>
                    <h2>Annuaire des adhérents</h2>
                    @if($member?->directory_opt_in)
                        <span class="badge badge-success">Publié</span>
                    @endif
                </div>
            </div>

            @if(!$member?->directory_opt_in)
                <p class="text-sm mb-4" style="color:var(--muted)">
                    Vous ne figurez pas dans l'annuaire des adhérents. Cochez ci-dessous pour
                    apparaître dans l'annuaire partagé entre adhérents à jour de cotisation.
                    Vos nom, prénom, département et email seront visibles.
                </p>
            @else
                <p class="text-sm mb-4" style="color:var(--muted)">
                    Vous figurez dans l'annuaire depuis le {{ $member->directory_opt_in_at?->format('d/m/Y') }}
                    (source&nbsp;: {{ $member->directory_opt_in_source }}).
                    Pour modifier nom/prénom/email/département, rendez-vous sur
                    <a href="{{ route('member.profile') }}" class="underline">votre profil</a>.
                </p>
            @endif

            {{-- Toggle opt-in --}}
            <label class="flex items-start gap-4 p-4 border rounded-lg cursor-pointer mb-4" style="border-color:var(--border)">
                <input type="checkbox" name="directory_opt_in" value="1" x-model="optIn"
                       {{ $member?->directory_opt_in ? 'checked' : '' }}
                       class="mt-1 w-5 h-5 rounded">
                <div class="flex-1">
                    <div class="font-medium" style="color:var(--forest)">J'accepte d'apparaître dans l'annuaire</div>
                    <p class="text-sm mt-1" style="color:var(--muted)">
                        Mes coordonnées seront visibles par les autres adhérents à jour.
                    </p>

                    {{-- Sous-formulaire conditionnel --}}
                    <div x-show="optIn" x-transition class="mt-4 space-y-4" x-cloak>
                        {{-- Téléphone visible --}}
                        @if($member?->mobile)
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="directory_phone_visible" value="1"
                                       {{ $member?->directory_phone_visible ? 'checked' : '' }}
                                       class="w-4 h-4 rounded">
                                <span class="text-sm">Exposer mon téléphone&nbsp;: <strong>{{ $member->mobile }}</strong></span>
                            </label>
                        @else
                            <p class="text-sm" style="color:var(--muted)">
                                Aucun téléphone renseigné. <a href="{{ route('member.profile') }}" class="underline">Ajoutez votre mobile</a> pour pouvoir l'exposer.
                            </p>
                        @endif

                        {{-- Groupes --}}
                        <div>
                            <div class="font-medium mb-2" style="color:var(--forest)">Mes groupes de prédilection</div>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach(\App\Models\Member::DIRECTORY_GROUPS as $key => $label)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="directory_groups[]" value="{{ $key }}"
                                               {{ in_array($key, $member?->directory_groups ?? [], true) ? 'checked' : '' }}
                                               class="w-4 h-4 rounded">
                                        <span class="text-sm">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('directory_groups')<p class="text-sm" style="color:#dc2626">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </label>
        </div>

        {{-- Info box --}}
        <div class="rounded-lg p-4 mb-6" style="background:var(--surface-blue); border:1px solid var(--border)">
            <div class="flex gap-3">
                <i data-lucide="info" class="flex-shrink-0 mt-0.5" style="color:var(--info)"></i>
                <div class="text-sm" style="color:var(--forest)">
                    <p class="font-medium mb-1">Vos droits</p>
                    <p>Vous pouvez à tout moment modifier ces préférences ou demander la suppression de vos données en contactant notre délégué à la protection des données à <a href="mailto:rgpd@oreina.org" class="underline">rgpd@oreina.org</a>.</p>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-between items-center">
            <a href="{{ route('member.profile') }}" class="text-link hover:underline">
                <i data-lucide="arrow-left" style="width:16px;height:16px;display:inline-block;vertical-align:middle"></i>
                Retour au profil
            </a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="check-circle"></i>
                Enregistrer mes préférences
            </button>
        </div>
    </form>
</div>
@endsection
