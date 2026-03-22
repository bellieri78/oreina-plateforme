<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'jean.dupont@example.com',
                'phone' => '06 12 34 56 78',
                'address' => '12 rue des Papillons',
                'postal_code' => '75001',
                'city' => 'Paris',
                'country' => 'France',
                'profession' => 'Entomologiste amateur',
                'interests' => 'Lépidoptères diurnes, Photographie naturaliste',
                'newsletter_subscribed' => true,
                'is_active' => true,
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Martin',
                'email' => 'marie.martin@example.com',
                'phone' => '06 98 76 54 32',
                'address' => '45 avenue des Fleurs',
                'postal_code' => '69001',
                'city' => 'Lyon',
                'country' => 'France',
                'profession' => 'Biologiste',
                'interests' => 'Hétérocères, Conservation',
                'newsletter_subscribed' => true,
                'is_active' => true,
            ],
            [
                'first_name' => 'Pierre',
                'last_name' => 'Bernard',
                'email' => 'pierre.bernard@example.com',
                'phone' => '07 11 22 33 44',
                'address' => '8 chemin du Naturaliste',
                'postal_code' => '31000',
                'city' => 'Toulouse',
                'country' => 'France',
                'profession' => 'Enseignant',
                'interests' => 'Zygènes, Élevage',
                'newsletter_subscribed' => false,
                'is_active' => true,
            ],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Leroy',
                'email' => 'sophie.leroy@example.com',
                'phone' => '06 55 44 33 22',
                'address' => '23 rue de la Nature',
                'postal_code' => '33000',
                'city' => 'Bordeaux',
                'country' => 'France',
                'profession' => 'Photographe',
                'interests' => 'Macrophotographie, Rhopalocères',
                'newsletter_subscribed' => true,
                'is_active' => true,
            ],
        ];

        $individualType = MembershipType::where('slug', 'individuelle')->first();

        foreach ($members as $memberData) {
            $member = Member::updateOrCreate(
                ['email' => $memberData['email']],
                array_merge($memberData, [
                    'member_number' => Member::generateMemberNumber(),
                    'joined_at' => now()->subMonths(rand(1, 24)),
                ])
            );

            // Créer une adhésion active pour chaque membre
            if ($individualType && !$member->memberships()->exists()) {
                Membership::create([
                    'member_id' => $member->id,
                    'membership_type_id' => $individualType->id,
                    'start_date' => now()->subMonths(rand(1, 6)),
                    'end_date' => now()->addMonths(rand(6, 12)),
                    'amount_paid' => $individualType->price,
                    'payment_method' => ['helloasso', 'virement', 'cheque'][rand(0, 2)],
                    'status' => 'active',
                ]);
            }
        }
    }
}
