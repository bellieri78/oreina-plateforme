@extends('layouts.journal')

@section('title', 'Merci')

@section('content')
<div class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50 min-h-screen">
    <div class="max-w-xl mx-auto text-center">
        <div class="bg-white rounded-2xl border border-oreina-beige/50 p-8">
            @if($accepted === true)
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            @elseif($accepted === false)
                <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            @else
                <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            @endif

            <h1 class="text-xl font-bold text-oreina-dark mb-3">{{ $message }}</h1>

            @if($accepted === true)
                <p class="text-slate-600 mb-6">
                    Vous avez jusqu'au <strong>{{ $review->due_date?->format('d/m/Y') }}</strong> pour soumettre votre évaluation.
                    Connectez-vous à votre compte pour accéder au formulaire de relecture.
                </p>
                @if(Route::has('review.form'))
                    <a href="{{ route('review.form', $review) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-oreina-turquoise text-white font-semibold rounded-lg hover:bg-oreina-dark transition">
                        Accéder au formulaire de relecture
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
