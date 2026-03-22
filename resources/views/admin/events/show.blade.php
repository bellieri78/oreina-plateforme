@extends('layouts.admin')
@section('title', $event->title)
@section('breadcrumb')
    <a href="{{ route('admin.events.index') }}">Evenements</a>
    <span>/</span>
    <span>{{ Str::limit($event->title, 30) }}</span>
@endsection

@section('content')
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $event->title }}</h3>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-secondary">Modifier</a>
                </div>
            </div>
            <div class="card-body">
                @if($event->description)
                    <div style="background-color: #f9fafb; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; font-style: italic; color: #6b7280;">
                        {{ $event->description }}
                    </div>
                @endif

                @if($event->content)
                    <div style="line-height: 1.7; color: #374151;">
                        {!! nl2br(e($event->content)) !!}
                    </div>
                @else
                    <p style="color: #9ca3af;">Aucun contenu detaille</p>
                @endif
            </div>
        </div>

        <div>
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Informations</h3>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 1rem;">
                        @if($event->status === 'published')
                            <span class="badge badge-success" style="font-size: 1rem; padding: 0.5rem 1rem;">Publie</span>
                        @else
                            <span class="badge badge-secondary" style="font-size: 1rem; padding: 0.5rem 1rem;">Brouillon</span>
                        @endif
                        @if($event->start_date > now())
                            <span class="badge badge-info" style="font-size: 1rem; padding: 0.5rem 1rem; margin-left: 0.5rem;">A venir</span>
                        @elseif($event->isPast())
                            <span class="badge badge-warning" style="font-size: 1rem; padding: 0.5rem 1rem; margin-left: 0.5rem;">Passe</span>
                        @endif
                    </div>

                    @if($event->event_type)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Type :</span>
                            <span style="font-weight: 500;">{{ $event->event_type }}</span>
                        </div>
                    @endif

                    <div style="margin-bottom: 0.75rem;">
                        <span style="color: #6b7280;">Date :</span>
                        <span style="font-weight: 500;">{{ $event->start_date->format('d/m/Y H:i') }}</span>
                        @if($event->end_date)
                            <br><span style="color: #6b7280;">au</span>
                            <span>{{ $event->end_date->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>

                    @if($event->organizer)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Organisateur :</span>
                            <span>{{ $event->organizer->name }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if($event->location_name || $event->location_city)
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-header">
                        <h3 class="card-title">Lieu</h3>
                    </div>
                    <div class="card-body">
                        @if($event->location_name)
                            <div style="font-weight: 600; margin-bottom: 0.5rem;">{{ $event->location_name }}</div>
                        @endif
                        @if($event->location_address)
                            <div style="color: #6b7280;">{{ $event->location_address }}</div>
                        @endif
                        @if($event->location_city)
                            <div style="color: #6b7280;">{{ $event->location_city }}</div>
                        @endif
                    </div>
                </div>
            @endif

            @if($event->registration_required || $event->max_participants || $event->price)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Inscription</h3>
                    </div>
                    <div class="card-body">
                        @if($event->registration_required)
                            <div style="margin-bottom: 0.75rem;">
                                <span class="badge badge-info">Inscription obligatoire</span>
                            </div>
                        @endif

                        @if($event->max_participants)
                            <div style="margin-bottom: 0.75rem;">
                                <span style="color: #6b7280;">Places :</span>
                                <span style="font-weight: 500;">{{ $event->max_participants }}</span>
                            </div>
                        @endif

                        @if($event->price)
                            <div style="margin-bottom: 0.75rem;">
                                <span style="color: #6b7280;">Prix :</span>
                                <span style="font-weight: 500;">{{ number_format($event->price, 2, ',', ' ') }} EUR</span>
                            </div>
                        @endif

                        @if($event->registration_url)
                            <div style="margin-top: 1rem;">
                                <a href="{{ $event->registration_url }}" target="_blank" class="btn btn-primary" style="width: 100%;">
                                    Lien d'inscription
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
