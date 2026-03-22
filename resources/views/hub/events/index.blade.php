@extends('layouts.hub')

@section('title', 'Événements')
@section('meta_description', 'Découvrez les événements OREINA : sorties terrain, conférences, ateliers et réunions autour des Lépidoptères.')

@section('content')
    {{-- Header --}}
    <section class="pt-28 pb-12 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="icon-box bg-oreina-green text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <line x1="16" x2="16" y1="2" y2="6"/>
                        <line x1="8" x2="8" y1="2" y2="6"/>
                        <line x1="3" x2="21" y1="10" y2="10"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-oreina-dark">Événements</h1>
                    <p class="text-slate-500 mt-1">Sorties terrain, conférences, ateliers et réunions</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Upcoming Events --}}
    <section class="py-12 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-oreina-dark mb-8">Prochains événements</h2>

            @if($upcomingEvents->count() > 0)
                <div class="grid md:grid-cols-2 gap-8">
                    @foreach($upcomingEvents as $event)
                    <article class="card group p-0 overflow-hidden">
                        <div class="flex">
                            {{-- Date Block --}}
                            <div class="flex-shrink-0 w-24 bg-gradient-to-b from-oreina-green to-oreina-teal text-white p-4 flex flex-col items-center justify-center">
                                <span class="text-sm uppercase font-medium">{{ $event->start_date->translatedFormat('M') }}</span>
                                <span class="text-4xl font-bold">{{ $event->start_date->format('d') }}</span>
                                <span class="text-sm opacity-75">{{ $event->start_date->format('Y') }}</span>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 p-6">
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span class="px-3 py-1 text-xs font-bold rounded-lg bg-oreina-turquoise/10 text-oreina-turquoise">
                                        {{ $eventTypes[$event->event_type] ?? ucfirst($event->event_type) }}
                                    </span>
                                    @if($event->registration_required)
                                    <span class="px-3 py-1 text-xs font-bold rounded-lg bg-oreina-yellow/20 text-oreina-dark">
                                        Inscription requise
                                    </span>
                                    @endif
                                </div>

                                <h3 class="text-lg font-bold text-oreina-dark group-hover:text-oreina-green transition">
                                    <a href="{{ route('hub.events.show', $event) }}">
                                        {{ $event->title }}
                                    </a>
                                </h3>

                                <p class="text-slate-500 text-sm mt-2 line-clamp-2">
                                    {{ $event->description }}
                                </p>

                                <div class="flex flex-wrap items-center gap-4 mt-4 text-sm text-slate-500">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                            <circle cx="12" cy="10" r="3"/>
                                        </svg>
                                        {{ $event->location_city }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        {{ $event->start_date->format('H:i') }}
                                    </span>
                                    @if($event->price > 0)
                                    <span class="font-semibold text-oreina-green">
                                        {{ number_format($event->price, 0, ',', ' ') }} €
                                    </span>
                                    @else
                                    <span class="font-semibold text-oreina-green">Gratuit</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12">
                    {{ $upcomingEvents->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-3xl border-2 border-oreina-beige/30">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-slate-900">Aucun événement à venir</h3>
                    <p class="text-slate-500 mt-2">De nouveaux événements seront bientôt annoncés.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Past Events --}}
    @if($pastEvents->count() > 0)
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-oreina-dark mb-8">Événements passés</h2>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pastEvents as $event)
                <article class="card card-alt p-6 opacity-70 hover:opacity-100">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="px-3 py-1 text-xs font-bold rounded-lg bg-slate-100 text-slate-600">
                            {{ $eventTypes[$event->event_type] ?? ucfirst($event->event_type) }}
                        </span>
                        <span class="text-sm text-slate-500">
                            {{ $event->start_date->format('d/m/Y') }}
                        </span>
                    </div>
                    <h3 class="font-bold text-slate-700">
                        {{ $event->title }}
                    </h3>
                    <p class="text-sm text-slate-500 mt-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        {{ $event->location_city }}
                    </p>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- CTA --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="stats-banner text-center">
                <h2 class="text-2xl font-bold mb-4">Vous souhaitez participer ?</h2>
                <p class="text-white/90 mb-8 max-w-2xl mx-auto">
                    Devenez membre d'OREINA pour accéder à tous nos événements et bénéficier de tarifs préférentiels.
                </p>
                <a href="{{ route('hub.membership') }}" class="inline-flex items-center gap-2 bg-white text-oreina-teal px-8 py-4 rounded-2xl font-bold hover:shadow-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    Devenir membre
                </a>
            </div>
        </div>
    </section>
@endsection
