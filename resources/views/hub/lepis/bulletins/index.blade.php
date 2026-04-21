@extends('layouts.hub')

@section('title', 'Bulletins Lepis — Archives')

@section('content')
<div class="max-w-6xl mx-auto py-12 px-4">
    <div class="mb-8">
        <a href="{{ route('hub.lepis') }}" class="text-sm text-oreina-green hover:underline">← Retour à Lepis</a>
        <h1 class="text-3xl font-bold text-oreina-dark mt-2">Les numéros de Lepis</h1>
        <p class="mt-2 text-gray-600">Bulletin trimestriel de l'association OREINA, réservé aux adhérents à jour pour son numéro le plus récent, puis accessible à tous après quelques mois.</p>
    </div>

    @if ($bulletins->isEmpty())
        <p class="text-gray-500">Aucun numéro disponible pour l'instant.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($bulletins as $bulletin)
                @include('hub.lepis.bulletins._card', ['bulletin' => $bulletin])
            @endforeach
        </div>

        <div class="mt-8">{{ $bulletins->links() }}</div>
    @endif
</div>
@endsection
