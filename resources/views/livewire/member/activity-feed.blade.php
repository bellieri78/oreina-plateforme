<div>
    @if($feedItems->isEmpty())
        <p class="text-gray-500 text-center py-6">Aucune activité récente</p>
    @else
        <div class="space-y-1">
            @foreach($feedItems as $item)
            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                {{-- Icon --}}
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                    @if($item['color'] === 'green') bg-green-100
                    @elseif($item['color'] === 'blue') bg-blue-100
                    @elseif($item['color'] === 'amber') bg-amber-100
                    @elseif($item['color'] === 'purple') bg-purple-100
                    @else bg-gray-100
                    @endif
                ">
                    @if($item['icon'] === 'heart')
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    @elseif($item['icon'] === 'id-card')
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                    @elseif($item['icon'] === 'book')
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    @elseif($item['icon'] === 'calendar')
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    @endif
                </div>
                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-sm text-oreina-dark">{{ $item['title'] }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ $item['description'] }}</div>
                </div>
                {{-- Date --}}
                <div class="text-xs text-gray-400 flex-shrink-0">
                    {{ $item['date']->diffForHumans() }}
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
