@props(['compact' => false])

@php
    $memberCounts = \Illuminate\Support\Facades\Cache::remember('member_map_counts', 86400, function () {
        return \App\Models\Member::where('is_active', true)
            ->whereNotNull('postal_code')
            ->get()
            ->groupBy(fn ($m) => substr($m->postal_code, 0, 2))
            ->map->count()
            ->toArray();
    });

    $totalMembers = array_sum($memberCounts);
    $maxCount = max($memberCounts ?: [1]);

    // Simplified department centers (x, y) on a 600x600 viewBox
    $departments = [
        '01' => ['name' => 'Ain', 'x' => 410, 'y' => 310],
        '02' => ['name' => 'Aisne', 'x' => 340, 'y' => 115],
        '03' => ['name' => 'Allier', 'x' => 330, 'y' => 320],
        '04' => ['name' => 'Alpes-Hte-Provence', 'x' => 460, 'y' => 410],
        '05' => ['name' => 'Hautes-Alpes', 'x' => 450, 'y' => 380],
        '06' => ['name' => 'Alpes-Maritimes', 'x' => 500, 'y' => 420],
        '07' => ['name' => 'Ardèche', 'x' => 390, 'y' => 385],
        '08' => ['name' => 'Ardennes', 'x' => 370, 'y' => 95],
        '09' => ['name' => 'Ariège', 'x' => 250, 'y' => 500],
        '10' => ['name' => 'Aube', 'x' => 350, 'y' => 180],
        '11' => ['name' => 'Aude', 'x' => 295, 'y' => 480],
        '12' => ['name' => 'Aveyron', 'x' => 310, 'y' => 420],
        '13' => ['name' => 'Bouches-du-Rhône', 'x' => 430, 'y' => 450],
        '14' => ['name' => 'Calvados', 'x' => 195, 'y' => 130],
        '15' => ['name' => 'Cantal', 'x' => 310, 'y' => 370],
        '16' => ['name' => 'Charente', 'x' => 205, 'y' => 335],
        '17' => ['name' => 'Charente-Maritime', 'x' => 165, 'y' => 330],
        '18' => ['name' => 'Cher', 'x' => 300, 'y' => 260],
        '19' => ['name' => 'Corrèze', 'x' => 270, 'y' => 365],
        '21' => ['name' => 'Côte-d\'Or', 'x' => 385, 'y' => 240],
        '22' => ['name' => 'Côtes-d\'Armor', 'x' => 100, 'y' => 140],
        '23' => ['name' => 'Creuse', 'x' => 280, 'y' => 330],
        '24' => ['name' => 'Dordogne', 'x' => 230, 'y' => 380],
        '25' => ['name' => 'Doubs', 'x' => 445, 'y' => 240],
        '26' => ['name' => 'Drôme', 'x' => 420, 'y' => 390],
        '27' => ['name' => 'Eure', 'x' => 250, 'y' => 130],
        '28' => ['name' => 'Eure-et-Loir', 'x' => 260, 'y' => 175],
        '29' => ['name' => 'Finistère', 'x' => 50, 'y' => 140],
        '30' => ['name' => 'Gard', 'x' => 380, 'y' => 430],
        '31' => ['name' => 'Haute-Garonne', 'x' => 250, 'y' => 470],
        '32' => ['name' => 'Gers', 'x' => 215, 'y' => 465],
        '33' => ['name' => 'Gironde', 'x' => 175, 'y' => 390],
        '34' => ['name' => 'Hérault', 'x' => 345, 'y' => 460],
        '35' => ['name' => 'Ille-et-Vilaine', 'x' => 130, 'y' => 160],
        '36' => ['name' => 'Indre', 'x' => 270, 'y' => 280],
        '37' => ['name' => 'Indre-et-Loire', 'x' => 240, 'y' => 250],
        '38' => ['name' => 'Isère', 'x' => 430, 'y' => 350],
        '39' => ['name' => 'Jura', 'x' => 430, 'y' => 270],
        '40' => ['name' => 'Landes', 'x' => 165, 'y' => 440],
        '41' => ['name' => 'Loir-et-Cher', 'x' => 260, 'y' => 225],
        '42' => ['name' => 'Loire', 'x' => 380, 'y' => 340],
        '43' => ['name' => 'Haute-Loire', 'x' => 350, 'y' => 370],
        '44' => ['name' => 'Loire-Atlantique', 'x' => 130, 'y' => 240],
        '45' => ['name' => 'Loiret', 'x' => 290, 'y' => 210],
        '46' => ['name' => 'Lot', 'x' => 265, 'y' => 410],
        '47' => ['name' => 'Lot-et-Garonne', 'x' => 215, 'y' => 420],
        '48' => ['name' => 'Lozère', 'x' => 345, 'y' => 410],
        '49' => ['name' => 'Maine-et-Loire', 'x' => 185, 'y' => 235],
        '50' => ['name' => 'Manche', 'x' => 155, 'y' => 110],
        '51' => ['name' => 'Marne', 'x' => 365, 'y' => 145],
        '52' => ['name' => 'Haute-Marne', 'x' => 400, 'y' => 190],
        '53' => ['name' => 'Mayenne', 'x' => 175, 'y' => 190],
        '54' => ['name' => 'Meurthe-et-Moselle', 'x' => 455, 'y' => 135],
        '55' => ['name' => 'Meuse', 'x' => 425, 'y' => 130],
        '56' => ['name' => 'Morbihan', 'x' => 90, 'y' => 185],
        '57' => ['name' => 'Moselle', 'x' => 475, 'y' => 110],
        '58' => ['name' => 'Nièvre', 'x' => 340, 'y' => 265],
        '59' => ['name' => 'Nord', 'x' => 335, 'y' => 55],
        '60' => ['name' => 'Oise', 'x' => 305, 'y' => 120],
        '61' => ['name' => 'Orne', 'x' => 200, 'y' => 165],
        '62' => ['name' => 'Pas-de-Calais', 'x' => 305, 'y' => 60],
        '63' => ['name' => 'Puy-de-Dôme', 'x' => 330, 'y' => 340],
        '64' => ['name' => 'Pyrénées-Atlantiques', 'x' => 170, 'y' => 490],
        '65' => ['name' => 'Hautes-Pyrénées', 'x' => 210, 'y' => 500],
        '66' => ['name' => 'Pyrénées-Orientales', 'x' => 310, 'y' => 510],
        '67' => ['name' => 'Bas-Rhin', 'x' => 510, 'y' => 120],
        '68' => ['name' => 'Haut-Rhin', 'x' => 500, 'y' => 165],
        '69' => ['name' => 'Rhône', 'x' => 400, 'y' => 330],
        '70' => ['name' => 'Haute-Saône', 'x' => 445, 'y' => 210],
        '71' => ['name' => 'Saône-et-Loire', 'x' => 385, 'y' => 285],
        '72' => ['name' => 'Sarthe', 'x' => 215, 'y' => 210],
        '73' => ['name' => 'Savoie', 'x' => 460, 'y' => 340],
        '74' => ['name' => 'Haute-Savoie', 'x' => 460, 'y' => 310],
        '75' => ['name' => 'Paris', 'x' => 295, 'y' => 148],
        '76' => ['name' => 'Seine-Maritime', 'x' => 260, 'y' => 100],
        '77' => ['name' => 'Seine-et-Marne', 'x' => 320, 'y' => 165],
        '78' => ['name' => 'Yvelines', 'x' => 275, 'y' => 150],
        '79' => ['name' => 'Deux-Sèvres', 'x' => 190, 'y' => 290],
        '80' => ['name' => 'Somme', 'x' => 295, 'y' => 85],
        '81' => ['name' => 'Tarn', 'x' => 290, 'y' => 450],
        '82' => ['name' => 'Tarn-et-Garonne', 'x' => 255, 'y' => 440],
        '83' => ['name' => 'Var', 'x' => 470, 'y' => 440],
        '84' => ['name' => 'Vaucluse', 'x' => 420, 'y' => 420],
        '85' => ['name' => 'Vendée', 'x' => 140, 'y' => 280],
        '86' => ['name' => 'Vienne', 'x' => 225, 'y' => 300],
        '87' => ['name' => 'Haute-Vienne', 'x' => 255, 'y' => 340],
        '88' => ['name' => 'Vosges', 'x' => 470, 'y' => 170],
        '89' => ['name' => 'Yonne', 'x' => 340, 'y' => 215],
        '90' => ['name' => 'Territoire de Belfort', 'x' => 480, 'y' => 195],
        '91' => ['name' => 'Essonne', 'x' => 290, 'y' => 165],
        '92' => ['name' => 'Hauts-de-Seine', 'x' => 285, 'y' => 150],
        '93' => ['name' => 'Seine-Saint-Denis', 'x' => 300, 'y' => 148],
        '94' => ['name' => 'Val-de-Marne', 'x' => 298, 'y' => 155],
        '95' => ['name' => 'Val-d\'Oise', 'x' => 290, 'y' => 135],
        '2A' => ['name' => 'Corse-du-Sud', 'x' => 545, 'y' => 475],
        '2B' => ['name' => 'Haute-Corse', 'x' => 555, 'y' => 440],
    ];

    // Color scale: light green (few) to dark green (many)
    function getDeptColor(int $count, int $max): string {
        if ($count === 0) return '#f3f4f6';
        $ratio = min($count / max($max, 1), 1);
        $lightness = 90 - (50 * $ratio); // 90% (light) to 40% (dark)
        return "hsl(150, 40%, {$lightness}%)";
    }
@endphp

<div class="{{ $compact ? '' : 'member-card' }}">
    @unless($compact)
    <div class="member-card-header">
        <span class="dot" style="background: #2C5F2D;"></span>
        Carte des membres — {{ $totalMembers }} membres actifs
    </div>
    @endunless

    <div class="relative" x-data="{ tooltip: '', tooltipX: 0, tooltipY: 0 }">
        {{-- Tooltip --}}
        <div
            x-show="tooltip"
            x-transition
            class="absolute pointer-events-none bg-oreina-dark text-white text-[10px] px-2 py-1 rounded-lg shadow-lg z-10 whitespace-nowrap"
            :style="'left: ' + tooltipX + 'px; top: ' + tooltipY + 'px; transform: translate(-50%, -130%)'"
            x-text="tooltip"
        ></div>

        <svg viewBox="0 0 600 560" class="w-full h-auto">
            {{-- Draw department dots --}}
            @foreach($departments as $code => $dept)
                @php
                    $count = $memberCounts[$code] ?? 0;
                    $color = getDeptColor($count, $maxCount);
                    $radius = $compact ? ($count > 0 ? max(4, min(10, 4 + ($count / $maxCount) * 6)) : 3) : ($count > 0 ? max(6, min(14, 6 + ($count / $maxCount) * 8)) : 4);
                @endphp
                <circle
                    cx="{{ $dept['x'] }}"
                    cy="{{ $dept['y'] }}"
                    r="{{ $radius }}"
                    fill="{{ $color }}"
                    stroke="{{ $count > 0 ? '#2C5F2D' : '#d1d5db' }}"
                    stroke-width="{{ $count > 0 ? 1.5 : 0.5 }}"
                    class="cursor-pointer transition-all duration-200 hover:opacity-80"
                    @mouseenter="tooltip = '{{ $dept['name'] }} ({{ str_pad($code, 2, '0', STR_PAD_LEFT) }}) — {{ $count }} membre{{ $count > 1 ? 's' : '' }}'; tooltipX = $event.offsetX; tooltipY = $event.offsetY"
                    @mouseleave="tooltip = ''"
                />
            @endforeach
        </svg>

        @if($compact)
        <div class="text-center mt-1">
            <span class="text-[10px] text-gray-400">{{ $totalMembers }} membres actifs</span>
        </div>
        @else
        {{-- Legend --}}
        <div class="flex items-center justify-center gap-4 mt-3 text-[10px] text-gray-400">
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded-full" style="background: #f3f4f6; border: 1px solid #d1d5db;"></div>
                0
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded-full" style="background: hsl(150, 40%, 80%); border: 1px solid #2C5F2D;"></div>
                Peu
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded-full" style="background: hsl(150, 40%, 55%); border: 1px solid #2C5F2D;"></div>
                Moyen
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded-full" style="background: hsl(150, 40%, 40%); border: 1px solid #2C5F2D;"></div>
                Beaucoup
            </div>
        </div>
        @endif
    </div>
</div>
