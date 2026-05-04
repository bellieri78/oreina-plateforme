@props(['events' => collect()])

<aside class="member-sidebar-right">
    {{-- Agenda header --}}
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg font-bold text-oreina-dark">Agenda</h2>
        <div class="relative">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
    </div>

    {{-- Events grouped by month --}}
    @if($events->isEmpty())
        <p class="text-sm text-gray-400 text-center py-4">Aucun événement à venir</p>
    @else
        @foreach($events->groupBy(fn ($e) => $e->start_date->translatedFormat('F Y')) as $month => $monthEvents)
        <div class="mb-5">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 pb-2 border-b border-gray-100">
                {{ $month }}
            </div>
            @foreach($monthEvents as $event)
            <div class="flex gap-3 mb-3">
                <div class="text-xs font-bold text-oreina-dark w-10 flex-shrink-0 leading-tight">
                    {{ $event->start_date->format('d') }}<br>
                    <span class="font-normal text-gray-400">{{ $event->start_date->translatedFormat('M') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[10px] text-oreina-green font-medium">{{ ucfirst($event->event_type ?? 'Événement') }}</div>
                    <div class="text-xs font-semibold text-oreina-dark truncate">{{ $event->title }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    @endif

    {{-- Map widget --}}
    <div class="mt-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xs font-bold text-oreina-dark">Carte des membres</h3>
            <a href="{{ route('member.directory.index') }}" class="text-[10px] text-oreina-green hover:underline">Agrandir →</a>
        </div>
        <x-member.map-france :compact="true" />
    </div>

    {{-- Chat widget --}}
    <div class="mt-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xs font-bold text-oreina-dark">💬 Discussion</h3>
            <a href="{{ route('member.chat') }}" class="text-[10px] text-oreina-green hover:underline">Ouvrir →</a>
        </div>
        @php
            $chatMember = $authMember ?? (\Auth::check() ? \App\Models\Member::where('user_id', \Auth::id())->first() : null);
        @endphp
        @if($chatMember)
            @livewire('member.chat', ['memberId' => $chatMember->id, 'expanded' => false])
        @else
            <p class="text-[10px] text-gray-400 text-center py-2">Connectez-vous pour discuter</p>
        @endif
    </div>
</aside>
