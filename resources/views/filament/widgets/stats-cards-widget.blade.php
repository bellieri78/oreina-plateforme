<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($stats as $stat)
            <div class="relative overflow-hidden rounded-xl p-6 transition-all duration-200 hover:shadow-lg"
                 style="background: {{ $stat['gradient'] }};">
                {{-- Decorative circles --}}
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full opacity-20"
                     style="background: rgba(255,255,255,0.3);"></div>
                <div class="absolute -right-2 -bottom-6 h-20 w-20 rounded-full opacity-10"
                     style="background: rgba(255,255,255,0.3);"></div>

                <div class="relative">
                    <div class="text-4xl font-bold text-white">
                        {{ $stat['value'] }}
                    </div>
                    <div class="mt-1 text-sm font-medium text-white/80">
                        {{ $stat['label'] }}
                    </div>
                    @if(isset($stat['description']))
                        <div class="mt-3 flex items-center gap-2">
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium"
                                  style="background: rgba(255,255,255,0.2); color: white;">
                                {{ $stat['description'] }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
