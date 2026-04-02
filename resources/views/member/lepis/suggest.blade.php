@extends('layouts.member')

@section('title', 'Suggerer un article - Lepis')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('member.lepis') }}" class="text-gray-400 hover:text-oreina-dark transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-oreina-dark">Suggerer un article</h1>
            <p class="text-sm text-gray-400 mt-0.5">Proposez un sujet pour un prochain numero de Lepis</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="member-card">
        <form action="{{ route('member.lepis.suggest.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-semibold text-oreina-dark mb-1">Titre de la suggestion</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-oreina-green/30 focus:border-oreina-green"
                    placeholder="Ex: Les Zygaena du sud de la France" required>
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content --}}
            <div>
                <label for="content" class="block text-sm font-semibold text-oreina-dark mb-1">Description / contenu</label>
                <textarea id="content" name="content" rows="8"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-oreina-green/30 focus:border-oreina-green resize-y"
                    placeholder="Decrivez votre suggestion, les points que vous souhaitez aborder..." required>{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Attachment --}}
            <div>
                <label for="attachment" class="block text-sm font-semibold text-oreina-dark mb-1">Piece jointe (optionnel)</label>
                <input type="file" id="attachment" name="attachment"
                    class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-oreina-green/10 file:text-oreina-green hover:file:bg-oreina-green/20">
                <p class="text-[11px] text-gray-400 mt-1">Formats acceptes : PDF, images. Taille max : 10 Mo</p>
                @error('attachment')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-member">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Envoyer la suggestion
                </button>
                <a href="{{ route('member.lepis') }}" class="text-sm text-gray-400 hover:text-gray-600">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
