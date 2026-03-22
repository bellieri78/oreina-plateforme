@extends('layouts.hub')

@section('title', $event->title)
@section('meta_description', $event->description)

@section('content')
    {{-- Breadcrumb --}}
    <section class="pt-24 pb-4 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center text-sm text-slate-500">
                <a href="{{ route('hub.home') }}" class="hover:text-oreina-green transition">Accueil</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
                <a href="{{ route('hub.events.index') }}" class="hover:text-oreina-green transition">Événements</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
                <span class="text-oreina-dark font-medium truncate max-w-xs">{{ $event->title }}</span>
            </nav>
        </div>
    </section>

    {{-- Event Content --}}
    <article class="py-8 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">
                {{-- Main Content --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-3xl border-2 border-oreina-beige/30 overflow-hidden">
                        {{-- Featured Image --}}
                        @if($event->featured_image)
                        <figure class="aspect-video">
                            <img src="{{ Storage::url($event->featured_image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                        </figure>
                        @endif

                        <div class="p-8 lg:p-12">
                            {{-- Header --}}
                            <header class="mb-8">
                                <div class="flex flex-wrap items-center gap-3 mb-6">
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-lg bg-oreina-turquoise/10 text-oreina-turquoise">
                                        {{ ucfirst($event->event_type) }}
                                    </span>
                                    @if($event->start_date->isFuture())
                                        <span class="px-3 py-1.5 text-xs font-bold rounded-lg bg-oreina-green/10 text-oreina-green">
                                            À venir
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-600">
                                            Terminé
                                        </span>
                                    @endif
                                </div>
                                <h1 class="text-3xl lg:text-4xl font-bold text-oreina-dark mb-6 leading-tight">
                                    {{ $event->title }}
                                </h1>
                                <p class="text-xl text-slate-600 leading-relaxed">
                                    {{ $event->description }}
                                </p>
                            </header>

                            {{-- Content --}}
                            <div class="prose prose-lg max-w-none prose-headings:text-oreina-dark prose-headings:font-bold prose-a:text-oreina-green prose-a:no-underline hover:prose-a:underline prose-img:rounded-2xl">
                                {!! $event->content !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-3xl border-2 border-oreina-beige/30 p-8 sticky top-24">
                        {{-- Date & Time --}}
                        <div class="mb-8">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-4">Date et heure</h3>
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-gradient-to-b from-oreina-green to-oreina-teal text-white rounded-2xl flex flex-col items-center justify-center flex-shrink-0">
                                    <span class="text-xs uppercase font-medium">{{ $event->start_date->translatedFormat('M') }}</span>
                                    <span class="text-xl font-bold leading-none">{{ $event->start_date->format('d') }}</span>
                                </div>
                                <div>
                                    <p class="font-bold text-oreina-dark">
                                        {{ $event->start_date->translatedFormat('l d F Y') }}
                                    </p>
                                    <p class="text-slate-600">
                                        {{ $event->start_date->format('H:i') }}
                                        @if($event->end_date)
                                            - {{ $event->end_date->format('H:i') }}
                                        @endif
                                    </p>
                                    @if($event->end_date && !$event->start_date->isSameDay($event->end_date))
                                    <p class="text-sm text-slate-500 mt-1">
                                        Jusqu'au {{ $event->end_date->translatedFormat('d F Y') }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Location --}}
                        <div class="mb-8">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-4">Lieu</h3>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-oreina-green/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-oreina-green" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                </div>
                                <div>
                                    @if($event->location_name)
                                    <p class="font-bold text-oreina-dark">{{ $event->location_name }}</p>
                                    @endif
                                    @if($event->location_address)
                                    <p class="text-slate-600">{{ $event->location_address }}</p>
                                    @endif
                                    <p class="text-slate-600">{{ $event->location_city }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Price --}}
                        <div class="mb-8">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-4">Tarif</h3>
                            @if($event->price > 0)
                                <p class="text-3xl font-bold text-oreina-green">{{ number_format($event->price, 0, ',', ' ') }} €</p>
                            @else
                                <p class="text-3xl font-bold text-oreina-green">Gratuit</p>
                            @endif
                        </div>

                        {{-- Registration --}}
                        @if($event->registration_required)
                        <div class="mb-8">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-4">Inscription</h3>
                            @if($event->max_participants)
                            <p class="text-slate-600 mb-3">
                                Places limitées à {{ $event->max_participants }} participants
                            </p>
                            @endif
                            <span class="inline-flex items-center gap-2 text-sm font-bold px-4 py-2 bg-oreina-yellow/20 text-oreina-dark rounded-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Inscription obligatoire
                            </span>
                        </div>
                        @endif

                        {{-- CTA --}}
                        @if($event->start_date->isFuture())
                        <div class="pt-6 border-t border-oreina-beige/30">
                            <a href="mailto:contact@oreina.org?subject=Inscription : {{ $event->title }}" class="btn-primary w-full justify-center py-4">
                                S'inscrire à l'événement
                            </a>
                            <p class="text-xs text-slate-500 text-center mt-3">
                                Contactez-nous pour vous inscrire
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </article>

    {{-- Related Events --}}
    @if($relatedEvents->count() > 0)
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-oreina-dark mb-8">Événements similaires</h2>
            <div class="grid sm:grid-cols-2 gap-8">
                @foreach($relatedEvents as $related)
                <article class="card group p-0 overflow-hidden">
                    <div class="flex">
                        <div class="flex-shrink-0 w-20 bg-gradient-to-b from-oreina-green to-oreina-teal text-white p-4 flex flex-col items-center justify-center">
                            <span class="text-xs uppercase font-medium">{{ $related->start_date->translatedFormat('M') }}</span>
                            <span class="text-2xl font-bold">{{ $related->start_date->format('d') }}</span>
                        </div>
                        <div class="flex-1 p-6">
                            <h3 class="font-bold text-oreina-dark group-hover:text-oreina-green transition">
                                <a href="{{ route('hub.events.show', $related) }}">
                                    {{ $related->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-slate-500 mt-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                {{ $related->location_city }}
                            </p>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Back link --}}
    <section class="py-8 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('hub.events.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-oreina-green transition font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
                Retour aux événements
            </a>
        </div>
    </section>
@endsection
