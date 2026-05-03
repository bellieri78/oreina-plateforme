<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MenuRenderer
{
    public function forLocation(string $location): Collection
    {
        return Cache::remember(
            "menu.{$location}",
            3600,
            fn () => MenuItem::query()
                ->forLocation($location)
                ->active()
                ->whereNull('parent_id')
                ->with(['children' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
        );
    }
}
