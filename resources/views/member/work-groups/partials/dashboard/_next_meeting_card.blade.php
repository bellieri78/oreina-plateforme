@if($nextEvent)
<div class="card panel">
    <div class="panel-head"><div><h2>Prochaine réunion</h2></div></div>
    <div class="gt-next">
        <div class="gt-next-date">
            <strong>{{ $nextEvent->start_date->format('d') }}</strong>
            <small>{{ $nextEvent->start_date->translatedFormat('M') }}</small>
        </div>
        <div class="gt-next-info">
            <strong style="display:block;font-size:14px;">{{ $nextEvent->title }}</strong>
            <small style="display:block;color:var(--muted);margin-top:6px;">
                <i data-lucide="clock" style="width:13px;height:13px;vertical-align:-2px;"></i>
                {{ $nextEvent->start_date->format('H\hi') }}@if($nextEvent->end_date) - {{ $nextEvent->end_date->format('H\hi') }}@endif
            </small>
            <small style="display:block;color:var(--muted);margin-top:3px;">
                @if($nextEvent->meeting_url)
                    <i data-lucide="video" style="width:13px;height:13px;vertical-align:-2px;"></i> En visioconférence
                @elseif($nextEvent->location_city || $nextEvent->location_name)
                    <i data-lucide="map-pin" style="width:13px;height:13px;vertical-align:-2px;"></i> {{ $nextEvent->location_name ?: $nextEvent->location_city }}
                @endif
            </small>
        </div>
    </div>
    <button type="button" @click="tab='evenements'" class="gt-fullbtn">Voir tous les événements</button>
</div>
@endif
