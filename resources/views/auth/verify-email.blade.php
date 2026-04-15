@extends('layouts.hub')

@section('content')
<div class="max-w-xl mx-auto px-4 py-16">
    <h1 class="text-2xl font-bold text-oreina-dark mb-4">Vérifiez votre adresse email</h1>

    <p class="text-gray-700 mb-4">
        Un lien de vérification a été envoyé à <strong>{{ auth()->user()->email }}</strong>.
        Cliquez sur le lien reçu pour activer pleinement votre compte.
    </p>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <p class="text-sm text-gray-600 mb-6">
        Le mail n'est pas arrivé ? Vérifiez vos spams, ou demandez un nouvel envoi :
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="bg-oreina-green text-white px-4 py-2 rounded hover:bg-oreina-dark">
            Renvoyer le lien
        </button>
    </form>
</div>
@endsection
