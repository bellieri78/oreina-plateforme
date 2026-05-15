<article class="card panel">
    <div class="panel-head">
        <div>
            <h2>Agenda</h2>
        </div>
        <a href="{{ route('hub.events.index') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir tout l'agenda</a>
    </div>

    @if($upcomingEvents->count() === 0)
        <p style="color:var(--muted);padding:16px 0;">Aucun événement à venir pour le moment.</p>
    @else
        <div class="agenda-list">
            @foreach($upcomingEvents->take(4) as $event)
            <a href="{{ route('hub.events.show', $event) }}" class="agenda-item" style="text-decoration:none;color:inherit;">
                <div class="agenda-date">
                    <small>{{ $event->start_date->translatedFormat('M') }}</small>
                    <strong>{{ $event->start_date->format('d') }}</strong>
                </div>
                <div class="agenda-item-body">
                    <strong>{{ $event->title }}</strong>
                    <small>
                        @if($event->location_city){{ $event->location_city }}@else{{ $event->start_date->format('H\hi') }}@endif
                    </small>
                </div>
            </a>
            @endforeach
        </div>
    @endif

    <div style="margin-top:16px;">
        <a href="mailto:contact@oreina.org?subject=Proposer un événement" class="btn btn-secondary" style="width:100%;">
            <i data-lucide="plus"></i>Proposer un événement
        </a>
    </div>
</article>
