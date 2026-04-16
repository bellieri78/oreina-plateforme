@extends('layouts.journal')

@section('title', 'Invitation de relecture')

@section('content')
<div class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-oreina-beige/50 p-8">
            <h1 class="text-2xl font-bold text-oreina-dark mb-2">Invitation de relecture</h1>
            <p class="text-slate-600 mb-6">Vous êtes invité(e) à évaluer le manuscrit suivant pour la revue {{ config('journal.name') }}.</p>

            <div class="bg-gray-50 rounded-xl p-5 mb-6">
                <h2 class="text-lg font-bold text-oreina-dark mb-2">{{ $review->submission->title }}</h2>

                <div class="text-sm text-slate-600 space-y-1 mb-3">
                    <p><strong>Auteur(s) :</strong> {{ $review->submission->author?->name ?? 'Non spécifié' }}</p>
                    @if($review->assignedBy)
                        <p><strong>Invité par :</strong> {{ $review->assignedBy->name }}</p>
                    @endif
                    @if($review->due_date)
                        <p><strong>Date limite de relecture :</strong> {{ $review->due_date->format('d/m/Y') }}</p>
                    @else
                        <p><strong>Délai :</strong> 3 semaines après acceptation</p>
                    @endif
                </div>

                @if($review->submission->abstract)
                    <div class="border-t border-gray-200 pt-3 mt-3">
                        <p class="text-xs font-semibold text-slate-500 uppercase mb-1">Résumé</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $review->submission->abstract }}</p>
                    </div>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <form method="POST" action="{{ URL::signedRoute('journal.review.accept', ['review' => $review->id]) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3 bg-oreina-green text-white font-semibold rounded-lg hover:bg-oreina-dark transition">
                        Accepter l'invitation
                    </button>
                </form>

                <form method="POST" action="{{ URL::signedRoute('journal.review.decline', ['review' => $review->id]) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3 bg-white border-2 border-red-300 text-red-700 font-semibold rounded-lg hover:bg-red-50 transition"
                            onclick="return confirm('Êtes-vous sûr(e) de vouloir décliner ?')">
                        Décliner
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
