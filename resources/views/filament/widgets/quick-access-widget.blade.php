<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <span>Actions rapides</span>
            </div>
        </x-slot>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($links as $link)
                <a href="{{ $link['url'] }}"
                   class="group flex items-center gap-4 p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                         style="background-color: {{ $link['color'] === 'primary' ? 'rgba(133, 183, 157, 0.2)' : ($link['color'] === 'info' ? 'rgba(53, 107, 138, 0.2)' : ($link['color'] === 'success' ? 'rgba(45, 206, 137, 0.2)' : ($link['color'] === 'warning' ? 'rgba(251, 99, 64, 0.2)' : 'rgba(232, 93, 117, 0.2)'))) }}">
                        <x-dynamic-component :component="$link['icon']"
                            class="w-5 h-5"
                            style="color: {{ $link['color'] === 'primary' ? '#85B79D' : ($link['color'] === 'info' ? '#356B8A' : ($link['color'] === 'success' ? '#2dce89' : ($link['color'] === 'warning' ? '#fb6340' : '#E85D75'))) }}" />
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ $link['label'] }}</span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
