<?php

namespace Database\Seeders;

use App\Models\MembershipType;
use Illuminate\Database\Seeder;

class MembershipTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Adhésion Individuelle',
                'slug' => 'individuelle',
                'description' => 'Adhésion standard pour une personne.',
                'price' => 35.00,
                'duration_months' => 12,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Adhésion Couple/Famille',
                'slug' => 'couple-famille',
                'description' => 'Adhésion pour un couple ou une famille à la même adresse.',
                'price' => 50.00,
                'duration_months' => 12,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Adhésion Étudiant',
                'slug' => 'etudiant',
                'description' => 'Tarif réduit pour les étudiants (sur justificatif).',
                'price' => 20.00,
                'duration_months' => 12,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Adhésion Bienfaiteur',
                'slug' => 'bienfaiteur',
                'description' => 'Adhésion de soutien pour les généreux donateurs.',
                'price' => 100.00,
                'duration_months' => 12,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Adhésion Institutionnelle',
                'slug' => 'institutionnelle',
                'description' => 'Pour les associations, bibliothèques et institutions.',
                'price' => 80.00,
                'duration_months' => 12,
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($types as $type) {
            MembershipType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
