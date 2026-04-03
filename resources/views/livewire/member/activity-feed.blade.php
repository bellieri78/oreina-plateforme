<div>
    @if($feedItems->isEmpty())
        <p class="text-center py-6" style="color:var(--muted)">Aucune activité récente</p>
    @else
        <div class="space-y-1">
            @foreach($feedItems as $item)
            <div class="activity-item flex items-start gap-3 p-3 rounded-xl">
                {{-- Icon --}}
                <div class="bullet w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                    @if($item['color'] === 'green') style="background:var(--surface-sage)"
                    @elseif($item['color'] === 'blue') style="background:var(--surface-blue)"
                    @elseif($item['color'] === 'amber') style="background:var(--surface-amber)"
                    @elseif($item['color'] === 'purple') style="background:var(--surface-purple)"
                    @else style="background:var(--surface-sage)"
                    @endif
                >
                    @if($item['icon'] === 'heart')
                        <i data-lucide="heart" style="width:16px;height:16px;color:var(--amber)"></i>
                    @elseif($item['icon'] === 'id-card')
                        <i data-lucide="id-card" style="width:16px;height:16px;color:var(--sage)"></i>
                    @elseif($item['icon'] === 'book')
                        <i data-lucide="book-open" style="width:16px;height:16px;color:var(--info)"></i>
                    @elseif($item['icon'] === 'calendar')
                        <i data-lucide="calendar-days" style="width:16px;height:16px;color:var(--purple)"></i>
                    @endif
                </div>
                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-sm" style="color:var(--forest)">{{ $item['title'] }}</div>
                    <div class="text-xs truncate" style="color:var(--muted)">{{ $item['description'] }}</div>
                </div>
                {{-- Date --}}
                <div class="time text-xs flex-shrink-0" style="color:var(--muted)">
                    {{ $item['date']->diffForHumans() }}
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
