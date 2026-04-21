@extends('layouts.member')

@section('title', 'Suggerer un article - Lepis')
@section('page-title', 'Suggérer un article')
@section('page-subtitle', 'Proposez une idée pour le prochain Lepis')

@section('content')
<div class="space-y-6">
    {{-- Back link --}}
    <div>
        <a href="{{ route('hub.lepis.bulletins.index') }}" class="inline-flex items-center gap-2 text-link hover:underline text-sm">
            <i data-lucide="arrow-left" style="width:16px;height:16px"></i>
            Retour aux bulletins
        </a>
    </div>

    {{-- Form --}}
    <div class="card panel">
        <form action="{{ route('member.lepis.suggest.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-semibold mb-1" style="color:var(--forest)">Titre de la suggestion</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}"
                    class="w-full px-3 py-2 text-sm border rounded-lg" style="border-color:var(--border)"
                    placeholder="Ex: Les Zygaena du sud de la France" required>
                @error('title')
                    <p class="text-xs mt-1" style="color:var(--coral)">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content --}}
            <div>
                <label for="content" class="block text-sm font-semibold mb-1" style="color:var(--forest)">Description / contenu</label>
                <textarea id="content" name="content" rows="8"
                    class="w-full px-3 py-2 text-sm border rounded-lg resize-y" style="border-color:var(--border)"
                    placeholder="Decrivez votre suggestion, les points que vous souhaitez aborder..." required>{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-xs mt-1" style="color:var(--coral)">{{ $message }}</p>
                @enderror
            </div>

            {{-- Attachment --}}
            <div>
                <label for="attachment" class="block text-sm font-semibold mb-1" style="color:var(--forest)">Piece jointe (optionnel)</label>
                <input type="file" id="attachment" name="attachment"
                    class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold" style="color:var(--muted)">
                <p class="text-xs mt-1" style="color:var(--muted)">Formats acceptes : PDF, images. Taille max : 10 Mo</p>
                @error('attachment')
                    <p class="text-xs mt-1" style="color:var(--coral)">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="send"></i>
                    Envoyer la suggestion
                </button>
                <a href="{{ route('hub.lepis.bulletins.index') }}" class="text-sm text-link hover:underline">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
