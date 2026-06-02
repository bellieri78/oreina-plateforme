@if($nextEvent)
<div class="card panel">
    <div class="panel-head"><div><h2>Prochaine réunion</h2></div></div>
    <div class="gt-next">
        <div class="gt-next-date">
            <small>{{ strtoupper($nextEvent->start_date->translatedFormat('M')) }}</small>
            <strong>{{ $nextEvent->start_date->format('d') }}</strong>
        </div>
        <div class="gt-next-info">
            <strong style="display:block;">{{ $nextEvent->title }}</strong>
            <small style="display:block;color:var(--muted);margin-top:4px;">
                <i data-lucide="clock" style="width:13px;height:13px;vertical-align:-2px;"></i>
                {{ $nextEvent->start_date->format('H\hi') }}@if($nextEvent->end_date) - {{ $nextEvent->end_date->format('H\hi') }}@endif
            </small>
            <small style="display:block;color:var(--muted);margin-top:2px;">
                @if($nextEvent->meeting_url)
                    <i data-lucide="video" style="width:13px;height:13px;vertical-align:-2px;"></i> en visioconférence
                @elseif($nextEvent->location_city || $nextEvent->location_name)
                    <i data-lucide="map-pin" style="width:13px;height:13px;vertical-align:-2px;"></i> {{ $nextEvent->location_name ?: $nextEvent->location_city }}
                @endif
            </small>
        </div>
    </div>
    <button type="button" @click="tab='evenements'" class="text-link" style="background:none;border:none;cursor:pointer;margin-top:12px;display:inline-flex;"><i data-lucide="arrow-right"></i>Voir tous les événements</button>
</div>
@endif
