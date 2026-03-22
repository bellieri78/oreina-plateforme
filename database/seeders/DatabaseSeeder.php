<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer l'admin s'il n'existe pas
        User::firstOrCreate(
            ['email' => 'admin@oreina.org'],
            [
                'name' => 'Admin OREINA',
                'password' => Hash::make('admin123'),
            ]
        );

        // Créer quelques utilisateurs de test
        User::firstOrCreate(
            ['email' => 'redacteur@oreina.org'],
            [
                'name' => 'Rédacteur OREINA',
                'password' => Hash::make('redacteur123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'editeur@oreina.org'],
            [
                'name' => 'Éditeur Revue',
                'password' => Hash::make('editeur123'),
            ]
        );

        // Exécuter les seeders dans l'ordre
        $this->call([
            MembershipTypeSeeder::class,
            MemberSeeder::class,
            ArticleSeeder::class,
            EventSeeder::class,
            JournalIssueSeeder::class,
            SubmissionSeeder::class,
        ]);
    }
}
