@extends('layouts.admin')
@section('title', $event->title)
@section('breadcrumb')
    <a href="{{ route('admin.events.index') }}">Événements</a>
    <span>/</span>
    <span>{{ Str::limit($event->title, 30) }}</span>
@endsection

@push('styles')
<style>
    .article-content { line-height: 1.7; color: #1C2B27; font-size: 1rem; }
    .article-content p { margin: 0 0 1em; }
    .article-content h2 { font-size: 1.5rem; font-weight: 700; margin: 1.5em 0 0.5em; color: #16302B; }
    .article-content h3 { font-size: 1.25rem; font-weight: 700; margin: 1.2em 0 0.4em; color: #16302B; }
    .article-content ul, .article-content ol { margin: 0 0 1em 1.5em; padding: 0; }
    .article-content li { margin-bottom: 0.25em; }
    .article-content blockquote { border-left: 3px solid #85B79D; padding: 0.25em 0 0.25em 1em; color: #68756F; margin: 1em 0; font-style: italic; }
    .article-content a { color: #356B8A; text-decoration: underline; }
    .article-content strong { font-weight: 700; }
    .article-content em { font-style: italic; }
</style>
@endpush

@section('content')
    {{-- Header actions --}}
    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-secondary">Modifier</a>
        @if($event->status === 'published' && Route::has('hub.events.show'))
            <a href="{{ route('hub.events.show', $event) }}" target="_blank" class="btn btn-primary">Voir côté public ↗</a>
        @endif
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        {{-- SIDEBAR MÉTA --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">Informations</h3></div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    @if($event->status === 'published')
                        <span class="badge badge-success" style="font-size: 1rem; padding: 0.5rem 1rem;">Publié</span>
                    @else
                        <span class="badge badge-secondary" style="font-size: 1rem; padding: 0.5rem 1rem;">Brouillon</span>
                    @endif
                    @if($event->isUpcoming())
                        <span class="badge badge-info" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; margin-left: 0.5rem;">À venir</span>
                    @elseif($event->isPast())
                        <span class="badge badge-warning" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; margin-left: 0.5rem;">Passé</span>
                    @else
                        <span class="badge badge-warning" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; margin-left: 0.5rem;">En cours</span>
                    @endif
                </div>

                @if($event->event_type)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Type</div>
                        <div>{{ $event->event_type }}</div>
                    </div>
                @endif

                <div style="margin-bottom: 0.75rem;">
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Date début</div>
                    <div>{{ $event->start_date->format('d/m/Y H:i') }}</div>
                </div>

                @if($event->end_date)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Date fin</div>
                        <div>{{ $event->end_date->format('d/m/Y H:i') }}</div>
                    </div>
                @endif

                @if($event->location_name)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Lieu</div>
                        <div>{{ $event->location_name }}</div>
                        @if($event->location_city)
                            <div style="color: #6b7280; font-size: 0.875rem;">{{ $event->location_city }}</div>
                        @endif
                    </div>
                @endif

                @if($event->organizer)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Organisateur</div>
                        <div>{{ $event->organizer->name }}</div>
                    </div>
                @endif

                @if($event->price !== null)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Tarif</div>
                        <div>
                            @if((float) $event->price > 0)
                                {{ number_format($event->price, 2, ',', ' ') }} €
                            @else
                                Gratuit
                            @endif
                        </div>
                    </div>
                @endif

                @if($event->published_at)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Publié le</div>
                        <div>{{ $event->published_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- BODY --}}
        <div class="card">
            <div class="card-body">
                @if($event->featured_image)
                    <img src="{{ Storage::url($event->featured_image) }}" alt="" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                @endif

                <h1 style="font-size: 1.875rem; font-weight: 700; color: #16302B; margin: 0 0 1rem;">{{ $event->title }}</h1>

                {{-- Date+lieu en bandeau --}}
                <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; padding: 0.75rem 1rem; background: #f9fafb; border-radius: 0.5rem; font-size: 0.95rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i data-lucide="calendar" style="width: 18px; height: 18px; color: #356B8A;"></i>
                        @php
                            $start = $event->start_date;
                            $end = $event->end_date;
                            $sameDay = $end && $start->isSameDay($end);
                        @endphp
                        @if($end && !$sameDay)
                            <span>{{ $start->locale('fr')->isoFormat('LL') }} → {{ $end->locale('fr')->isoFormat('LL') }}</span>
                        @elseif($end && $sameDay)
                            <span>{{ $start->locale('fr')->isoFormat('LL') }} · {{ $start->format('H\hi') }} → {{ $end->format('H\hi') }}</span>
                        @else
                            <span>{{ $start->locale('fr')->isoFormat('LL') }} · {{ $start->format('H\hi') }}</span>
                        @endif
                    </div>
                    @if($event->location_name || $event->location_address || $event->location_city)
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i data-lucide="map-pin" style="width: 18px; height: 18px; color: #356B8A;"></i>
                            @php
                                $addr = trim(implode(', ', array_filter([$event->location_name, $event->location_address, $event->location_city])));
                                $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($addr);
                            @endphp
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" style="color: #356B8A; text-decoration: underline;">{{ $addr }}</a>
                        </div>
                    @endif
                </div>

                @if($event->description)
                    <p style="font-size: 1.05rem; font-style: italic; color: #6b7280; margin-bottom: 1.5rem;">{{ $event->description }}</p>
                @endif

                @if($event->content)
                    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 1.5rem 0;">
                    <div class="article-content">
                        {!! $event->content !!}
                    </div>
                @endif

                @if($event->registration_required || $event->registration_url || $event->max_participants)
                    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 2rem 0;">
                    <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                        <h3 style="margin: 0 0 0.75rem; font-size: 1.125rem; font-weight: 600;">Inscription</h3>
                        @if($event->max_participants)
                            <p style="margin: 0 0 0.5rem; color: #4b5563;">Maximum {{ $event->max_participants }} participants</p>
                        @endif
                        @if($event->registration_required && ! $event->registration_url)
                            <p style="margin: 0; color: #4b5563;">Inscription obligatoire (procédure à préciser).</p>
                        @endif
                        @if($event->registration_url)
                            <a href="{{ $event->registration_url }}" target="_blank" rel="noopener" class="btn btn-primary" style="margin-top: 0.5rem;">S'inscrire ↗</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
