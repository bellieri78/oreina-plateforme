<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between w-full">
                <span>Activite recente</span>
                <a href="#" class="text-sm font-medium text-gray-500 hover:text-gray-700">Voir tout</a>
            </div>
        </x-slot>

        <div class="space-y-1">
            @forelse($activities as $activity)
                @php
                    $colors = [
                        'success' => ['bg' => 'rgba(45, 206, 137, 0.15)', 'text' => '#2dce89'],
                        'info' => ['bg' => 'rgba(53, 107, 138, 0.15)', 'text' => '#356B8A'],
                        'warning' => ['bg' => 'rgba(251, 99, 64, 0.15)', 'text' => '#fb6340'],
                        'danger' => ['bg' => 'rgba(245, 54, 92, 0.15)', 'text' => '#f5365c'],
                    ];
                    $color = $colors[$activity['color']] ?? $colors['info'];
                @endphp
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-all">
                    <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center"
                         style="background-color: {{ $color['bg'] }}">
                        <x-dynamic-component :component="$activity['icon']"
                            class="w-4 h-4"
                            style="color: {{ $color['text'] }}" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800">{{ $activity['title'] }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $activity['description'] }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="text-xs text-gray-400">{{ $activity['time']->locale('fr')->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-gray-500">
                    <x-heroicon-o-inbox class="w-10 h-10 mx-auto text-gray-300 mb-2" />
                    <p class="text-sm">Aucune activite recente</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
