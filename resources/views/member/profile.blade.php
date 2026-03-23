@extends('layouts.member')

@section('title', 'Mon profil')
@section('subtitle', 'Gérez vos informations personnelles')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('member.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Photo and basic info --}}
        <div class="member-card mb-6">
            <div class="member-card-header">
                <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h3 class="member-card-title">Informations personnelles</h3>
            </div>

            <div class="flex flex-col sm:flex-row gap-6 mb-6">
                {{-- Photo --}}
                <div class="flex-shrink-0">
                    <div class="w-24 h-24 rounded-full bg-gray-100 overflow-hidden mb-2">
                        @if($member?->photo_path)
                            <img src="{{ Storage::url($member->photo_path) }}" alt="Photo" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <label class="block">
                        <span class="text-sm text-oreina-green cursor-pointer hover:underline">Changer la photo</span>
                        <input type="file" name="photo" accept="image/*" class="hidden">
                    </label>
                </div>

                {{-- Name fields --}}
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Civilité</label>
                        <select name="civilite" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-oreina-green focus:border-transparent">
                            <option value="">--</option>
                            @foreach(['M.', 'Mme', 'Dr', 'Pr'] as $civ)
                                <option value="{{ $civ }}" {{ old('civilite', $member?->civilite) === $civ ? 'selected' : '' }}>{{ $civ }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $member?->first_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-oreina-green focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $member?->last_name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-oreina-green focus:border-transparent">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Email and phone --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $member?->email ?? $user->email) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-oreina-green focus:border-transparent">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone fixe</label>
                    <input type="tel" name="phone" value="{{ old('phone', $member?->phone) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-oreina-green focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
                    <input type="tel" name="mobile" value="{{ old('mobile', $member?->mobile) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-oreina-green focus:border-transparent">
                </div>
            </div>

            {{-- Profession --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Profession</label>
                <input type="text" name="profession" value="{{ old('profession', $member?->profession) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-oreina-green focus:border-transparent">
            </div>
        </div>

        {{-- Address --}}
        <div class="member-card mb-6">
            <div class="member-card-header">
                <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h3 class="member-card-title">Adresse</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <input type="text" name="address" value="{{ old('address', $member?->address) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-oreina-green focus:border-transparent">
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $member?->postal_code) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-oreina-green focus:border-transparent">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                        <input type="text" name="city" value="{{ old('city', $member?->city) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-oreina-green focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                        <input type="text" name="country" value="{{ old('country', $member?->country ?? 'France') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-oreina-green focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        {{-- Interests --}}
        <div class="member-card mb-6">
            <div class="member-card-header">
                <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <h3 class="member-card-title">Centres d'intérêt</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vos centres d'intérêt en lépidoptérologie</label>
                <textarea name="interests" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-oreina-green focus:border-transparent" placeholder="Ex: Rhopalocères, Hétérocères nocturnes, macrophotographie...">{{ old('interests', $member?->interests) }}</textarea>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('member.dashboard') }}" class="btn-member-outline">Annuler</a>
            <button type="submit" class="btn-member">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer
            </button>
        </div>
    </form>

    {{-- Preferences link --}}
    <div class="mt-8 pt-8 border-t border-gray-200">
        <a href="{{ route('member.profile.preferences') }}" class="inline-flex items-center gap-2 text-oreina-green font-medium hover:underline">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Gérer mes préférences de communication (RGPD)
        </a>
    </div>
</div>
@endsection
