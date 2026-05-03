<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemsSeeder extends Seeder
{
    public function run(): void
    {
        if (MenuItem::count() > 0) {
            $this->command?->info('MenuItems déjà présents, seeder ignoré (idempotent).');
            return;
        }

        // Header (5 items)
        MenuItem::create(['location' => 'header', 'label' => 'Association', 'url' => '/a-propos', 'sort_order' => 10, 'is_active' => true]);
        MenuItem::create(['location' => 'header', 'label' => 'Projets', 'url' => '#projets', 'sort_order' => 20, 'is_active' => false]);
        MenuItem::create(['location' => 'header', 'label' => 'Actualités', 'url' => '/actualites', 'sort_order' => 30, 'is_active' => true]);
        MenuItem::create(['location' => 'header', 'label' => 'Réseau', 'url' => '#reseau', 'sort_order' => 40, 'is_active' => false]);
        MenuItem::create(['location' => 'header', 'label' => 'Chersotis', 'url' => '/revue', 'sort_order' => 50, 'is_active' => true]);

        // Footer (7 items)
        MenuItem::create(['location' => 'footer', 'label' => 'Association', 'url' => '/a-propos', 'sort_order' => 10, 'is_active' => true]);
        MenuItem::create(['location' => 'footer', 'label' => 'Portail', 'url' => '/', 'sort_order' => 20, 'is_active' => true]);
        MenuItem::create(['location' => 'footer', 'label' => 'Projets', 'url' => '#', 'sort_order' => 30, 'is_active' => false]);
        MenuItem::create(['location' => 'footer', 'label' => 'Actualités', 'url' => '/actualites', 'sort_order' => 40, 'is_active' => true]);
        MenuItem::create(['location' => 'footer', 'label' => 'Réseau', 'url' => '/contact', 'sort_order' => 50, 'is_active' => true]);
        MenuItem::create(['location' => 'footer', 'label' => 'Mentions légales', 'url' => '#', 'sort_order' => 60, 'is_active' => false]);
        MenuItem::create(['location' => 'footer', 'label' => 'Politique de données', 'url' => '#', 'sort_order' => 70, 'is_active' => false]);
    }
}
