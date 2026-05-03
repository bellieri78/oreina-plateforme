@extends('layouts.member')

@section('title', 'Mon profil')
@section('page-title', 'Mon profil')
@section('page-subtitle', 'Gérer vos informations personnelles')

@section('content')
<div>
    <form action="{{ route('member.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Photo and basic info --}}
        <div class="card panel mb-6">
            <div class="panel-head">
                <div>
                    <h2>Informations personnelles</h2>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-6 mb-6">
                {{-- Photo --}}
                <div class="flex-shrink-0">
                    <div class="w-24 h-24 rounded-full overflow-hidden mb-2" style="background:var(--surface-sage)">
                        @if($member?->photo_path)
                            <img src="{{ Storage::url($member->photo_path) }}" alt="Photo" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center" style="color:var(--muted)">
                                <i data-lucide="user-round" style="width:48px;height:48px"></i>
                            </div>
                        @endif
                    </div>
                    <label class="block">
                        <span class="text-sm cursor-pointer hover:underline text-link">Changer la photo</span>
                        <input type="file" name="photo" accept="image/*" class="hidden">
                    </label>
                </div>

                {{-- Name fields --}}
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:var(--forest)">Civilité</label>
                        <select name="civilite" class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
                            <option value="">--</option>
                            @foreach(['M.', 'Mme', 'Dr', 'Pr'] as $civ)
                                <option value="{{ $civ }}" {{ old('civilite', $member?->civilite) === $civ ? 'selected' : '' }}>{{ $civ }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:var(--forest)">Prénom</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $member?->first_name) }}" class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:var(--forest)">Nom <span style="color:var(--coral)">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $member?->last_name) }}" required class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
                        @error('last_name')
                            <p class="mt-1 text-sm" style="color:var(--coral)">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Email and phone --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:var(--forest)">Email <span style="color:var(--coral)">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $member?->email ?? $user->email) }}" required class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
                    @error('email')
                        <p class="mt-1 text-sm" style="color:var(--coral)">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:var(--forest)">Téléphone fixe</label>
                    <input type="tel" name="telephone_fixe" value="{{ old('telephone_fixe', $member?->telephone_fixe) }}" class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:var(--forest)">Mobile</label>
                    <input type="tel" name="mobile" value="{{ old('mobile', $member?->mobile) }}" class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
                </div>
            </div>

            {{-- Profession --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color:var(--forest)">Profession</label>
                <input type="text" name="profession" value="{{ old('profession', $member?->profession) }}" class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
            </div>
        </div>

        {{-- Address --}}
        <div class="card panel mb-6">
            <div class="panel-head">
                <div>
                    <h2>Adresse</h2>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:var(--forest)">Adresse</label>
                    <input type="text" name="address" value="{{ old('address', $member?->address) }}" class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:var(--forest)">Code postal</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $member?->postal_code) }}" class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium mb-1" style="color:var(--forest)">Ville</label>
                        <input type="text" name="city" value="{{ old('city', $member?->city) }}" class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:var(--forest)">Pays</label>
                        <input type="text" name="country" value="{{ old('country', $member?->country ?? 'France') }}" class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
                    </div>
                </div>
            </div>
        </div>

        {{-- Interests --}}
        <div class="card panel mb-6">
            <div class="panel-head">
                <div>
                    <h2>Centres d'intérêt</h2>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" style="color:var(--forest)">Vos centres d'intérêt en lépidoptérologie</label>
                <textarea name="interests" rows="3" class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)" placeholder="Ex: Rhopalocères, Hétérocères nocturnes, macrophotographie...">{{ old('interests', $member?->interests) }}</textarea>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="check-circle"></i>
                Enregistrer
            </button>
        </div>
    </form>

    {{-- Format Lepis (lecture seule) --}}
    @if($lepisFormat)
    <div class="card panel mb-6">
        <div class="panel-head">
            <div>
                <h2>Bulletin Lepis</h2>
            </div>
        </div>
        <div>
            <p class="text-sm font-semibold mb-1" style="color:var(--forest)">Format de reception du bulletin Lepis :</p>
            <p class="font-bold" style="color:var(--forest)">
                {{ $lepisFormat === 'digital' ? 'Numerique' : 'Papier' }}
            </p>
            <p class="mt-2 text-sm" style="color:var(--muted)">
                Pour modifier ce choix, contactez le secretariat a
                <a href="mailto:secretariat@oreina.org" class="text-link hover:underline">secretariat@oreina.org</a>.
            </p>
        </div>
    </div>
    @endif

    {{-- Preferences link --}}
    <div class="mt-8 pt-8" style="border-top:1px solid var(--border)">
        <a href="{{ route('member.profile.preferences') }}" class="inline-flex items-center gap-2 font-medium hover:underline text-link">
            <i data-lucide="settings"></i>
            Gérer mes préférences de communication (RGPD)
        </a>
    </div>
</div>
@endsection
