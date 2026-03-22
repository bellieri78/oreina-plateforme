<x-filament-widgets::widget>
    <div class="flex items-center justify-between mb-2">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Tableau de bord</h1>
            <nav class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                <span>Accueil</span>
                <span>/</span>
                <span class="text-gray-700">Vue d'ensemble</span>
            </nav>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
        </div>
    </div>
</x-filament-widgets::widget>
