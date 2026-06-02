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
            @php
                $eventHref = ($event->visibility === \App\Models\Event::VIS_GROUP && $event->workGroup)
                    ? route('member.work-groups.show', $event->workGroup)
                    : route('hub.events.show', $event);
            @endphp
            <a href="{{ $eventHref }}" class="agenda-item" style="grid-template-columns:56px 1fr auto;text-decoration:none;color:inherit;">
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
                @php
                    $aud = $event->audience_roles ?? [];
                    if ($event->visibility === \App\Models\Event::VIS_GROUP) {
                        $repere = $event->workGroup?->name ?? 'Groupe';
                    } elseif ($event->meeting_url) {
                        $repere = 'Visio';
                    } elseif ($event->visibility === \App\Models\Event::VIS_RESTRICTED && $aud) {
                        $repere = implode(' · ', array_map(fn ($r) => \App\Models\Member::ADHERENT_ROLES[$r] ?? $r, $aud));
                    } elseif ($event->visibility === \App\Models\Event::VIS_MEMBERS) {
                        $repere = 'Adhérents';
                    } else {
                        $repere = 'À venir';
                    }
                @endphp
                <span class="space-row-chip gold">{{ $repere }}</span>
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
