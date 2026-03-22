<?php

namespace Database\Seeders;

use App\Models\MembershipType;
use Illuminate\Database\Seeder;

class MembershipType2026Seeder extends Seeder
{
    public function run(): void
    {
        // Marquer les types existants comme legacy
        MembershipType::query()->update([
            'is_legacy' => true,
            'valid_until' => '2025-12-31',
        ]);

        // Type legacy pour l'adhésion historique à 5€
        MembershipType::updateOrCreate(
            ['slug' => 'legacy-adhesion'],
            [
                'name' => 'Adhésion historique',
                'description' => "Adhésion de base à 5€ (système avant 2026). Le reste du montant payé correspondait à l'achat du magazine.",
                'price' => 5.00,
                'duration_months' => 12,
                'is_active' => false,
                'is_legacy' => true,
                'valid_from' => '2008-01-01',
                'valid_until' => '2025-12-31',
                'sort_order' => 100,
            ]
        );

        // Nouveaux types 2026
        $types2026 = [
            [
                'name' => 'Adhésion individuelle standard (France)',
                'slug' => 'individuelle-france',
                'description' => 'Adhésion standard pour une personne résidant en France métropolitaine.',
                'price' => 20.00,
                'sort_order' => 1,
            ],
            [
                'name' => 'Adhésion famille',
                'slug' => 'famille',
                'description' => 'Adhésion pour un couple avec ou sans enfants.',
                'price' => 25.00,
                'sort_order' => 2,
            ],
            [
                'name' => 'Membre bienfaiteur',
                'slug' => 'bienfaiteur-2026',
                'description' => 'Adhésion de soutien pour les généreux donateurs.',
                'price' => 50.00,
                'sort_order' => 3,
            ],
            [
                'name' => 'Adhésion personne morale',
                'slug' => 'personne-morale',
                'description' => 'Pour les associations, collectivités, entreprises et institutions.',
                'price' => 50.00,
                'for_organization' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Adhésion hors France métropolitaine',
                'slug' => 'hors-france',
                'description' => 'Adhésion pour les résidents hors France métropolitaine. Transmission du bulletin des adhérents en version numérique par mail.',
                'price' => 20.00,
                'for_foreign' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Adhésion petit budget',
                'slug' => 'petit-budget',
                'description' => 'Tarif réduit pour étudiant majeur, apprenti, demandeur d\'emploi, situation de précarité. Transmission du bulletin des adhérents en version numérique par courriel.',
                'price' => 12.00,
                'sort_order' => 6,
            ],
        ];

        foreach ($types2026 as $type) {
            MembershipType::updateOrCreate(
                ['slug' => $type['slug']],
                array_merge($type, [
                    'duration_months' => 12,
                    'is_active' => true,
                    'is_legacy' => false,
                    'valid_from' => '2026-01-01',
                    'for_foreign' => $type['for_foreign'] ?? false,
                    'for_organization' => $type['for_organization'] ?? false,
                ])
            );
        }
    }
}
