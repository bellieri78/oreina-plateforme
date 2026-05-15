<?php

namespace Database\Seeders;

use App\Models\LepidopteraOfMonth;
use Illuminate\Database\Seeder;

class LepidopteraOfMonthSeeder extends Seeder
{
    public function run(): void
    {
        if (LepidopteraOfMonth::count() > 0) {
            return;
        }

        LepidopteraOfMonth::create([
            'scientific_name' => 'Zygaena lavandulae',
            'photographer' => 'David Demerges',
            'photo_path' => 'images/espace-membre/papillon-hero.jpg',
            'display_order' => 1,
            'is_active' => true,
        ]);
    }
}
